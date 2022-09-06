<?php

namespace App\Services;

use App\Slot;
use App\Branch;
use App\Appointment;
use App\Models\BranchScheduleTemplateDetail;
use App\Schedule;

class AppointmentService
{
    public function create($data): ?Appointment
    {
        $date = date('Y-m-d', strtotime($data['date']));

        // Limit free appointments
        $branch = Branch::find($data['branch_id']);

        $totalTodayAppointments = Appointment::where([
            'branch_id' => $data['branch_id'],
            'date' => $date
        ])->count();

        if (
            !$branch->BranchType->is_premium &&
            $totalTodayAppointments >= config('appointment.free_appointment_limit')
        ) {
            throw new \Exception('Batas appointment gratis hari ini terlampaui');
        }

        // Prevent double appointments
        $email = '';
        $phone = '';

        if (isset($data['email'])) $email = $data['email'];
        if (isset($data['phone'])) $phone = $data['phone'];

        $sameAppointment = Appointment::where('email', $email)
            ->orWhere('phone', $phone)
            ->where([
                'slot_id' => $data['slot_id'],
                'date' => $date
            ])
            ->first();

        if ($sameAppointment) {
            throw new \Exception('Appointment telah terdaftar');
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
        $slot = Slot::find($data['slot_id']);

        $appointmentEndDateTime = date('Y-m-d H:i:s', strtotime($data['date'] . ' ' . $slot->end_time . ':00'));
        $currentDateTime = date('Y-m-d H:i:s');

        if ($appointmentEndDateTime < $currentDateTime) {
            throw new \Exception('Sesi appointment sudah berakhir');
        }

        $totalLastAppointments = Appointment::where([
            'branch_id' => $data['branch_id'],
            'date' => $date
        ])->count();

        $data['booking_code'] = $this->generateBookingCode();
        $data['number'] = $totalLastAppointments + 1;
        
        // Store appointment to db
        $appointment = Appointment::create($data);

        return $appointment;
    }

    private function generateBookingCode()
    {
        $permittedChars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charsLen = strlen($permittedChars);
        $randomString = '';
        for($i = 0; $i < 5; $i++) {
            $randomString .= $permittedChars[mt_rand(0, $charsLen - 1)];
        }
    
        return $randomString;
    }
}