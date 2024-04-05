<?php

namespace App\Repositories;

use App\Interfaces\AppointmentOnsiteRepositoryInterface;
use App\Mail\CS\AppointmentOnsiteCreatedMail;
use App\Models\BranchScheduleTemplateDetail;
use App\Service;
use App\Schedule;
use App\Models\AppointmentOnsite;
use App\Notifications\AppointmentOnsiteCreatedNotification;
use App\Slot;
use App\Workstation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class AppointmentOnsiteRepository implements AppointmentOnsiteRepositoryInterface
{
    public function store($data)
    {
        return Cache::lock('onsites', 10)->block(5, function () use ($data) {
            $service = Service::find($data['service_id']);
            $branch = $service->Branch;

            if($branch) {
                if($branch->timezone == 'WITA') {
                    config(['app.timezone' => 'Asia/Makassar']);
                } else if ($branch->timezone == 'WIT') {
                    config(['app.timezone' => 'Asia/Jayapura']);
                } else {
                    config(['app.timezone' => 'Asia/Jakarta']);
                }

                date_default_timezone_set(config('app.timezone'));
            }

            // Prevent double appointments
            if ($this->isAppoinmentDuplicate($data)) {
                throw new \Exception('Appointment telah terdaftar');
            }

            // Prevent appointment on full slot
            if ($this->isAppointmentSlotFull($data['slot_id'], $data['date'])) {
                throw new \Exception('Sesi appointment tidak tersedia');
            }

            // Prevent appointment on closed days
            if ($this->isClosed($data['branch_id'], $data['date'])) {
                throw new \Exception('Sesi appointment tidak tersedia');
            }

            if ($this->isAppointmentSessionFinish($data['slot_id'], $data['date'])) {
                throw new \Exception('Sesi appointment sudah berakhir');
            }

            // Prevent empty workstation service
            if ($this->isWorkstationServiceEmpty($data['service_id'])) {
                throw new \Exception('Tidak ada layanan');
            }

            if ($this->isAppointmentOverlap($data['slot_id'], $data['end_time'])) {
                throw new \Exception('Sesi waktu telah dibooking oleh layanan lain');
            }

            // free license branch cannot create more than 100 queue
            $total_current_booking = AppointmentOnsite::where('service_id', $data['service_id'])
                ->whereDate('date', date('Y-m-d', strtotime($data['date'])))
                ->count();
            if (!$branch->BranchType->is_premium && $total_current_booking >= $branch->max_queue) {
                throw new \Exception('Batas maksimal harian untuk cabang berlisensi gratis telah terlampaui');
            }
            if ($branch->BranchType->is_premium && $total_current_booking >= $branch->max_queue) {
                throw new \Exception('Batas maksimal harian untuk cabang telah terlampaui');
            }

            // cant create direct queue on closed day by schedule template
            $holiday = BranchScheduleTemplateDetail::where([
                'branch_id' => $branch->id,
                'date' => date('Y-m-d', strtotime($data['date']))
            ])->first();

            if ($holiday) {
                throw new \Exception('Cabang sedang tutup hari ini');
            }

            // cant create direct queue on closed day
            $schedule = Schedule::where('branch_id', $service->branch_id)
                ->where('day', strtolower(date('l', strtotime($data['date']))))
                ->first();
            if ($schedule && $schedule->status == 'closed') {
                throw new \Exception('Cabang sedang tutup hari ini');
            }

            // cant create direct queue before open time and after closed time
            if ($schedule && (date('H:i:s') < $schedule->start_time || date('H:i:s') > $schedule->end_time)) {
                throw new \Exception('Cabang sedang tutup hari ini');
            }

            $data['booking_code'] = $this->generate_booking_code();

            $appointmentOnsite = AppointmentOnsite::create($data);

            if (
                $appointmentOnsite->phone &&
                $branch &&
                $branch->is_premium &&
                $branch->BranchConfiguration->wa_notification != false &&
                $branch->BranchConfiguration->whatsapp_type == 'official_wa_branch'
            ) {
                $appointmentOnsite->sendAppointmentOnsiteCreatedNotification($appointmentOnsite);
            }

            try {
                Mail::to($appointmentOnsite->email)->send(new AppointmentOnsiteCreatedMail($appointmentOnsite));
            } catch (\Exception $e) {
                throw new \Exception('ERR-0002: Pengiriman email gagal, mohon cek koneksi internet Anda.', 10002);
            }

            return $appointmentOnsite;
        });
    }

    public function isAppoinmentDuplicate($data)
    {
        $formattedDate = date('Y-m-d', strtotime($data['date']));
        $email = $phone = '';

        if (isset($data['email'])) $email = $data['email'];
        if (isset($data['phone'])) $phone = $data['phone'];

        $sameAppointment = AppointmentOnsite::where([
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

        $totalTodayAppointmentsBySlot = AppointmentOnsite::where([
            'slot_id' => $slotId,
            'date' => $formattedDate
        ])->count();

        return $totalTodayAppointmentsBySlot >= $slot->max_slots;
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

    private function generate_booking_code() {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $code_exists = true;

        do {
            $random_string = '';
            for ($i = 0; $i < 6; $i++) {
                $random_character = $permitted_chars[mt_rand(0, strlen($permitted_chars) - 1)];
                $random_string .= $random_character;
            }

            $existing_code = AppointmentOnsite::where('booking_code', $random_string)->exists();

            if (!$existing_code) {
                $code_exists = false;
            }
        } while ($code_exists);

        return $random_string;
    }
}
