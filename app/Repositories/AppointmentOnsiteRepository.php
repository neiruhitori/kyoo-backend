<?php

namespace App\Repositories;

use App\Events\AppointmentOnsiteCreated;
use App\Interfaces\AppointmentOnsiteRepositoryInterface;
use App\Listeners\SendAppointmentOnsiteCreatedNotification;
use App\Models\BranchScheduleTemplateDetail;
use App\Service;
use App\Schedule;
use App\Models\AppointmentOnsite;
use Illuminate\Support\Facades\Cache;

class AppointmentOnsiteRepository implements AppointmentOnsiteRepositoryInterface
{
    public function store($data)
    {
        return Cache::lock('onsites', 10)->block(5, function () use ($data) {
            $service = Service::find($data['service_id']);
            $branch = $service->Branch;

            // user cant create same direct queue 3x at same date
            $total_same_user_queue = 0;
            if (isset($data['email'])) {
                $total_queue = AppointmentOnsite::where('email', $data['email'])
                    ->whereDate('date', date('Y-m-d', strtotime($data['date'])))
                    ->count();

                if ($total_queue > $total_same_user_queue) {
                    $total_same_user_queue = $total_queue;
                }
            }
            if (isset($data['phone'])) {
                $total_queue = AppointmentOnsite::where('phone', $data['phone'])
                    ->whereDate('date', date('Y-m-d', strtotime($data['date'])))
                    ->count();

                if ($total_queue > $total_same_user_queue) {
                    $total_same_user_queue = $total_queue;
                }
            }
            if (isset($data['client_id'])) {
                $total_queue = AppointmentOnsite::where('client_id', $data['client_id'])
                    ->whereDate('date', date('Y-m-d', strtotime($data['date'])))
                ->count();

                if ($total_queue > $total_same_user_queue) {
                    $total_same_user_queue = $total_queue;
                }
            }
            if ($total_same_user_queue >= 3) {
                throw new \Exception('Batas antrian maksimal harian untuk pengantri telah terlampaui');
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

            AppointmentOnsiteCreated::dispatch($appointmentOnsite);

            return $appointmentOnsite;
        });
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
