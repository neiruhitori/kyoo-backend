<?php

namespace App\Services;

use App\Slot;
use App\Branch;
use App\Appointment;
use App\Models\BranchScheduleTemplateDetail;
use App\Schedule;

use App\Events\AppointmentCanceledEvent;
use App\Events\AppointmentCreated;
use App\Events\AppointmentServed;
use App\Events\AppointmentEndServed;

use Illuminate\Support\Facades\Cache;
use App\Supports\BookingCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function create(array $data): Appointment
    {
        return Cache::lock('appointments', 10)->block(5, function () use ($data) {
            $date = date('Y-m-d', strtotime($data['date']));

            $branch = Branch::find($data['branch_id']);
            $slot = Slot::find($data['slot_id']);

            // Limit free appointments
            $todayAppointments = Appointment::where([
                'branch_id' => $data['branch_id'],
                'date' => $date
            ])
                ->get();

            if (
                !$branch->BranchType->is_premium &&
                count($todayAppointments) >= config('appointment.free_appointment_limit')
            ) {
                throw new \Exception('Batas appointment gratis hari ini terlampaui');
            }

            // Prevent double appointments
            $email = $phone = '';

            if (isset($data['email'])) $email = $data['email'];
            if (isset($data['phone'])) $phone = $data['phone'];

            $sameAppointment = Appointment::withoutCanceled()->where([
                    'slot_id' => $data['slot_id'],
                    'date' => $date
                ])
                ->where(function ($query) use ($email, $phone) {
                    $query->where('email', $email)
                        ->orWhere('phone', $phone);
                })
                ->first();

            if ($sameAppointment) {
                throw new \Exception('Appointment telah terdaftar');
            }

            // Prevent appointment on empty slots
            $todayAppointmentsBySlot = Appointment::withoutCanceled()->where([
                    'slot_id' => $data['slot_id'],
                    'date' => $date
                ])
                ->get();
            
            if (count($todayAppointmentsBySlot) >= $slot->max_slots) {
                throw new \Exception('Sesi appointment tidak tersedia');
            }

            // Prevent appointment on holidays
            $selectedHoliday = BranchScheduleTemplateDetail::where([
                'branch_id' => $data['branch_id'],
                'date' => $date
            ])->first();

            if ($selectedHoliday) {
                throw new \Exception('Sesi appointment tidak tersedia');
            }

            // Prevent appointment on closed days
            $day = strtolower(date('l', strtotime($data['date'])));

            $selectedSchedule = Schedule::where([
                'branch_id' => $data['branch_id'],
                'day' => $day
            ])->first();

            if ($selectedSchedule && $selectedSchedule->status == 'closed') {
                throw new \Exception('Sesi appointment tidak tersedia');
            }

            // Can't select past
            $appointmentEndDateTime = date('Y-m-d H:i:s', strtotime($data['date'] . ' ' . $slot->end_time . ':00'));
            $currentDateTime = date('Y-m-d H:i:s');

            if ($appointmentEndDateTime < $currentDateTime) {
                throw new \Exception('Sesi appointment sudah berakhir');
            }

            $lastAppointment = Appointment::withoutCanceled()->where([
                'branch_id' => $data['branch_id'],
                'date' => $date
            ])
                ->get()
                ->sortByDesc('number')
                ->first();

            $data['booking_code'] = BookingCode::generate();
            $data['number'] = $lastAppointment ? $lastAppointment->number + 1 : 1;
            
            // Store appointment to db
            $appointment = Appointment::create($data);

            // Dispatch created event
            AppointmentCreated::dispatch($appointment);

            return $appointment;
        });
    }

    public function checkIn(int $appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            throw new \Exception('Appointment tidak ditemukan');
        }

        if ($appointment->status === 'check in') {
            throw new \Exception('Customer sudah hadir');
        }

        if ($appointment->status === 'served') {
            throw new \Exception('Appointment sudah dilayani');
        }

        if (in_array($appointment->status, ['end served', 'no show', 'canceled'])) {
            throw new \Exception('Appointment sudah selesai');
        }

        Appointment::where('id', $appointment->id)->update([
            'status' => 'check in',
            'checkin_time' => date('Y-m-d H:i:s')
        ]);
    }

    public function serve(int $appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            throw new \Exception('Appointment tidak ditemukan');
        }

        if ($appointment->status === 'served') {
            throw new \Exception('Appointment sudah dilayani');
        }

        if (in_array($appointment->status, ['end served', 'no show', 'canceled'])) {
            throw new \Exception('Appointment sudah selesai');
        }

        if ($appointment->status !== 'check in') {
            throw new \Exception('Customer belum hadir');
        }

        Appointment::where('id', $appointment->id)->update([
            'status' => 'served',
            'served_time' => date('Y-m-d H:i:s'),
            'workstation_id' => Auth::user()->WorkstationVct->workstation_id,
            'vct_id' => Auth::id(),
            'waiting_duration' => Carbon::now()->diffInseconds(Carbon::parse($appointment->checkin_time))
        ]);

        AppointmentServed::dispatch($appointment);
    }

    public function cancel(int $appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            throw new \Exception('Appointment tidak ditemukan');
        }

        if ($appointment->status === 'canceled') {
            throw new \Exception('Appointment sudah dibatalkan');
        }

        if ($appointment->status === 'served') {
            throw new \Exception('Apointment sedang berlangsung');
        }

        if (in_array($appointment->status, ['end served', 'no show'])) {
            throw new \Exception('Appointment sudah selesai');
        }

        Appointment::where('id', $appointmentId)->update([
            'status' => 'canceled',
            'canceled_time' => date('Y-m-d H:i:s'),
        ]);

        AppointmentCanceledEvent::dispatch($appointment);
    }

    public function noShow(int $appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            throw new \Exception('Appointment tidak ditemukan');
        }

        if ($appointment->status === 'check in') {
            throw new \Exception('Customer sudah hadir');
        }

        if ($appointment->status === 'no show') {
            throw new \Exception('Appointment sudah diperbarui');
        }

        if ($appointment->status === 'served') {
            throw new \Exception('Appointment sedang dilayani');
        }

        if (in_array($appointment->status, ['end served', 'canceled'])) {
            throw new \Exception('Appointment sudah selesai');
        }

        Appointment::where('id', $appointmentId)->update(['status' => 'no show']);
    }

    public function endServe(int $appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            throw new \Exception('Appointment tidak ditemukan');
        }

        if (in_array($appointment->status, ['no show', 'canceled', 'end served'])) {
            throw new \Exception('Appointment sudah selesai');
        }

        if ($appointment->status != 'served') {
            throw new \Exception('Appointment belum dilayani');
        }

        Appointment::where('id', $appointmentId)->update([
            'status' => 'end served',
            'end_served_time' => date('Y-m-d H:i:s'),
            'serving_duration' => Carbon::now()->diffInseconds(Carbon::parse($appointment->served_time))
        ]);

        AppointmentEndServed::dispatch($appointment);
    }

    public function getFutureAppointmentsByDate($date)
    {
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            throw new \InvalidArgumentException('Appointment lama tidak tersedia');
        }

        return Appointment::with(['Service', 'Slot', 'Branch', 'Workstation'])
            ->where([
                'branch_id' => Auth::user()->branch_id,
                'date' => $date
            ])
            ->whereIn(
                'service_id',
                Auth::user()->WorkstationVct->Workstation->WorkstationService
                    ->map(function ($workstationService) {
                        return $workstationService->service_id;
                    })
            )
            ->where(function ($query) {
                $query->whereNull('workstation_id')
                    ->orWhere('workstation_id', Auth::user()->WorkstationVct->workstation_id);
            })
            ->orderBy('created_at')
            ->get();
    }

    public function getAppointmentSlotsByDateRange($from, $to)
    {
        if (strtotime($from) > strtotime($to)) {
            throw new \InvalidArgumentException('Tanggal awal tidak boleh lebih dari tanggal akhir');
        }

        return Appointment::with('Service.Slot')
            ->withoutCanceled()
            ->select(
                'date', 'service_id',
                DB::raw('COUNT(*) as filled_slots')
            )
            ->whereBetween('date', [$from, $to])
            ->groupBy(['date', 'service_id'])
            ->orderBy('date')
            ->get();
    }

    public function getAppointmentSlotsByServiceId($serviceId, $date)
    {
        return Appointment::with('Slot')
            ->withoutCanceled()
            ->select(
                'date', 'service_id', 'slot_id',
                DB::raw('COUNT(*) as filled_slots')
            )
            ->where('service_id', $serviceId)
            ->whereDate('date', $date)
            ->groupBy(['date', 'service_id', 'slot_id'])
            ->get();
    }
}