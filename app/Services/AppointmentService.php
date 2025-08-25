<?php

namespace App\Services;

use App\Slot;
use App\Branch;
use App\Service;
use App\Schedule;
use App\Appointment;
use App\Workstation;
use App\Supports\BookingCode;

use App\Jobs\SendFeedbackMail;
use Illuminate\Support\Carbon;
use App\Events\AppointmentServed;
use App\Events\AppointmentCreated;
use Illuminate\Support\Facades\DB;
use App\Events\AppointmentEndServed;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Events\QueueAppointmentStatus;
use App\Events\OwnerAppointmentCreated;
use App\Events\AppointmentCanceledEvent;
use App\Models\BranchScheduleTemplateDetail;
use App\Notifications\AppointmentCreatedNotification;

class AppointmentService
{
    public function create(array $data): Appointment
    {
        return Cache::lock('appointments', 10)->block(5, function () use ($data) {
            $service = Service::find($data['service_id']);
            $branch = $service->Branch;
            if ($branch->country != 'Indonesia') {
                app()->setLocale('en');
            }

            // Limit free appointments
            if ($this->isFreeAppointmentExceeded($data['branch_id'], $data['date'])) {
                throw new \Exception(__('The limit for free appointments today has been exceeded'));
            }

            // Prevent double appointments
            if ($this->isAppoinmentDuplicate($data)) {
                throw new \Exception(__('Appointment already registered'));
            }

            // Prevent appointment on full slot
            if ($this->isAppointmentSlotFull($data['slot_id'], $data['date'])) {
                throw new \Exception(__('The appointment session is not available'));
            }

            // Prevent appointment on holidays
            if ($this->isHoliday($data['branch_id'], $data['date'])) {
                throw new \Exception(__('The appointment session is not available'));
            }

            // Prevent appointment on closed days
            if ($this->isClosed($data['branch_id'], $data['date'])) {
                throw new \Exception(__('The appointment session is not available'));
            }

            // Can't select finished session
            if ($this->isAppointmentSessionFinish($data['slot_id'], $data['date'])) {
                throw new \Exception(__('The appointment session has ended'));
            }

            // Prevent empty workstation service
            if ($this->isWorkstationServiceEmpty($data['service_id'])) {
                throw new \Exception(__('No services available'));
            }

            //check if branch is expired
            if ($this->isBranchExpired($branch->license_expiration_date)) {
                throw new \Exception(__('The trial period or branch license has expired'));
            }

            $timeRange = $this->getFinalTimeSlot([
                'date' => $data['date'],
                'branch_id' => $data['branch_id'],
                'department_id' => $service->department_id,
                'service_id' => $data['service_id'],
                'slot_id' => $data['slot_id']
            ]);

            $data['start_time'] = $timeRange[0];
            $data['end_time'] = $timeRange[1];

            // Check overlap appointment
            if ($this->isAppointmentOverlap($data['slot_id'], $data['end_time'])) {
                throw new \Exception(__('Already booked by another service'));
            }

            $data['booking_code'] = BookingCode::generate();
            $data['number'] = $this->getCurrrentAppointmentNumber($data['branch_id'], $data['date']);

            // Store appointment to db
            $appointment = Appointment::create($data);
            if(
                $appointment->phone &&
                $branch &&
                $branch->is_premium &&
                $branch->BranchConfiguration->wa_notification != false &&
                $branch->BranchConfiguration->whatsapp_type == 'wa_kyoo'
            ){
                $appointment->sendNotificationWaBlast($appointment);
            }

            // Dispatch created event
            AppointmentCreated::dispatch($appointment);

            OwnerAppointmentCreated::dispatch($appointment);
            
            return $appointment;
        });
    }

    public function isFreeAppointmentExceeded($branchId, $date)
    {
        $formattedDate = date('Y-m-d', strtotime($date));
        $branch = Branch::find($branchId);

        $todayAppointments = Appointment::where([
            'branch_id' => $branchId,
            'date' => $formattedDate
        ])
            ->get();

        return (!$branch->BranchType->is_premium &&
            count($todayAppointments) >= config('appointment.free_appointment_limit'));
    }

    public function isAppoinmentDuplicate($data)
    {
        $formattedDate = date('Y-m-d', strtotime($data['date']));
        $email = $phone = '';

        if (isset($data['email'])) $email = $data['email'];
        if (isset($data['phone'])) $phone = $data['phone'];

        $sameAppointment = Appointment::withoutCanceled()->where([
            'slot_id' => $data['slot_id'],
            'date' => $formattedDate
        ])
            ->where(function ($query) use ($email, $phone) {
                $query->where('email', $email)
                    ->orWhere('phone', $phone);
            })
            ->first();

        return !!$sameAppointment;
    }

    public function isAppointmentSlotFull($slotId, $date)
    {
        $slot = Slot::find($slotId);
        $formattedDate = date('Y-m-d', strtotime($date));

        $totalTodayAppointmentsBySlot = Appointment::withoutCanceled()->where([
            'slot_id' => $slotId,
            'date' => $formattedDate
        ])->count();

        return $totalTodayAppointmentsBySlot >= $slot->max_slots;
    }

    public function isHoliday($branchId, $date)
    {
        $formattedDate = date('Y-m-d', strtotime($date));

        $selectedHoliday = BranchScheduleTemplateDetail::where([
            'branch_id' => $branchId,
            'date' => $formattedDate
        ])->first();

        return !!$selectedHoliday;
    }

    public function isClosed($branchId, $date)
    {
        $day = strtolower(date('l', strtotime($date)));

        $selectedSchedule = Schedule::where([
            'branch_id' => $branchId,
            'day' => $day
        ])->first();

        return $selectedSchedule && $selectedSchedule->status == 'closed';
    }

    public function isAppointmentSessionFinish($slotId, $date)
    {
        $slot = Slot::find($slotId);

        $appointmentEndDateTime = date('Y-m-d H:i:s', strtotime($date . ' ' . $slot->end_time . ':00'));
        $currentDateTime = date('Y-m-d H:i:s');

        return $appointmentEndDateTime < $currentDateTime;
    }

    private function isWorkstationServiceEmpty($serviceId)
    {
        $workstationService = Workstation::whereHas('WorkstationService', function ($query) use ($serviceId) {
            $query->where('service_id', $serviceId);
        })->first();

        return !$workstationService;
    }

    private function isAppointmentOverlap($slotId, $endTime)
    {
        $slot = Slot::find($slotId);

        return date('H:i', strtotime($endTime)) > $slot->end_time;
    }

    private function getFinalTimeSlot($data)
    {
        $date = date('Y-m-d', strtotime($data['date']));

        $departmentId = $data['department_id'];
        $serviceId = $data['service_id'];
        $slot = Slot::find($data['slot_id']);
        $workstationServices = Workstation::whereHas('WorkstationService', function ($query) use ($serviceId) {
            $query->where('service_id', $serviceId);
        })->get();

        $nextTimeslots = $this->getInitTimeSlot($slot, $date);

        $slot->next_start_time = $nextTimeslots[0];
        $slot->next_end_time = $nextTimeslots[1];

        $startTime = $nextTimeslots[0];
        $endTime = $nextTimeslots[1];

        $isOverlap = true;
        while ($isOverlap && date('H:i', strtotime($endTime)) <= $slot->end_time) {
            $overlapAppointments = Appointment::where([
                'date' => $date,
                'branch_id' => $data['branch_id'],
            ])
                ->whereHas('Service', function ($query) use ($departmentId) {
                    $query->where('department_id', $departmentId);
                })
                ->whereIn('status', ['book', 'waiting', 'served'])
                ->where([
                    ['start_time', '<', $endTime],
                    ['end_time', '>', $startTime]
                ])
                ->orderByDesc('start_time')
                ->get();

            if (count($overlapAppointments) >= count($workstationServices)) {
                $slotDurationInSeconds = strtotime($slot->end_time) - strtotime($slot->start_time);
                $secondsPerSlot = floor($slotDurationInSeconds / $slot->max_slots);

                $maxEndTime = $overlapAppointments->map(fn ($app) => strtotime($app->end_time))->max();

                $startTime = date('H:i', $maxEndTime);
                $endTime = date('H:i', $maxEndTime + $secondsPerSlot);
            } else {
                $isOverlap = false;
            }
        }
        return [$startTime, $endTime];
    }

    private function getInitTimeSlot($slot, $date)
    {
        $formattedDate = date('Y-m-d', strtotime($date));

        $currentAppointment = Appointment::withoutCanceled()->where([
            'date' => $formattedDate,
            'slot_id' => $slot->id
        ])
            ->orderByDesc('start_time')
            ->first();

        $slotDurationInSeconds = strtotime($slot->end_time) - strtotime($slot->start_time);
        $secondsPerSlot = floor($slotDurationInSeconds / $slot->max_slots);

        $nextStartTime = $slot->start_time;
        $nextEndTime = date('H:i', strtotime($slot->start_time) + $secondsPerSlot);

        if ($currentAppointment && $currentAppointment->start_time && $currentAppointment->end_time) {
            $nextStartTime = $currentAppointment->start_time;
            $nextEndTime = $currentAppointment->end_time;
        }

        return [$nextStartTime, $nextEndTime];
    }

    private function getCurrrentAppointmentNumber($branchId, $date)
    {
        $formattedDate = date('Y-m-d', strtotime($date));

        $lastAppointment = Appointment::withoutCanceled()->where([
            'branch_id' => $branchId,
            'date' => $formattedDate
        ])
            ->get()
            ->sortByDesc('number')
            ->first();

        return $lastAppointment ? $lastAppointment->number + 1 : 1;
    }

    public function checkIn(int $appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            throw new \Exception(__('Appointment not found'));
        }

        if ($appointment->status === 'check in') {
            throw new \Exception(__('Customer has arrived'));
        }

        if ($appointment->status === 'served') {
            throw new \Exception(__('Appointment has been served'));
        }

        if (in_array($appointment->status, ['end served', 'no show', 'canceled'])) {
            throw new \Exception(__('Appointment has been completed'));
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
            throw new \Exception(__('Appointment not found'));
        }

        if ($appointment->status === 'served') {
            throw new \Exception(__('Appointment has been served'));
        }

        if (in_array($appointment->status, ['end served', 'no show', 'canceled'])) {
            throw new \Exception(__('Appointment has been completed'));
        }

        if ($appointment->status !== 'check in') {
            throw new \Exception(__('Customer has not yet arrived'));
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
            throw new \Exception(__('Appointment not found'));
        }

        if ($appointment->status === 'canceled') {
            throw new \Exception(__('Appointment has been canceled'));
        }

        if ($appointment->status === 'served') {
            throw new \Exception(__('Appointment is in progress'));
        }

        if (in_array($appointment->status, ['end served', 'no show'])) {
            throw new \Exception(__('Appointment has been completed'));
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
            throw new \Exception(__('Appointment not found'));
        }

        if ($appointment->status === 'check in') {
            throw new \Exception(__('Customer has arrived'));
        }

        if ($appointment->status === 'no show') {
            throw new \Exception(__('Appointment has been updated'));
        }

        if ($appointment->status === 'served') {
            throw new \Exception(__('Appointment is being served'));
        }

        if (in_array($appointment->status, ['end served', 'canceled'])) {
            throw new \Exception(__('Appointment has been completed'));
        }

        Appointment::where('id', $appointmentId)->update(['status' => 'no show']);
    }

    public function endServe(int $appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            throw new \Exception(__('Appointment not found'));
        }

        if (in_array($appointment->status, ['no show', 'canceled', 'end served'])) {
            throw new \Exception(__('Appointment has been completed'));
        }

        if ($appointment->status != 'served') {
            throw new \Exception(__('Appointment has not yet been served'));
        }

        Appointment::where('id', $appointmentId)->update([
            'status' => 'end served',
            'end_served_time' => date('Y-m-d H:i:s'),
            'serving_duration' => Carbon::now()->diffInseconds(Carbon::parse($appointment->served_time))
        ]);

        if($appointment->email){
            SendFeedbackMail::dispatch('appointment',$appointment);
        }

        AppointmentEndServed::dispatch($appointment);
    }

    public function getFutureAppointmentsByDate($date)
    {
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            throw new \InvalidArgumentException(__('Appointment is no longer available'));
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
            throw new \InvalidArgumentException(__('Start date cannot be later than the end date'));
        }

        return Appointment::with('Service.Slot')
            ->withoutCanceled()
            ->select(
                'date',
                'service_id',
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
                'date',
                'service_id',
                'slot_id',
                DB::raw('COUNT(*) as filled_slots')
            )
            ->where('service_id', $serviceId)
            ->whereDate('date', $date)
            ->groupBy(['date', 'service_id', 'slot_id'])
            ->get();
    }

    public function isBranchExpired($license_expiry_date){
        $currentDateTime = date('Y-m-d H:i:s');
        $expiry_date = date('Y-m-d H:i:s', strtotime($license_expiry_date));

        return $license_expiry_date < $currentDateTime;
    }
}
