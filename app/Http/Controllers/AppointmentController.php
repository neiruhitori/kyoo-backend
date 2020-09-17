<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Appointment;
use App\Http\Resources\Appointment as AppointmentCollection;
use Crypt;
class AppointmentController extends Controller
{
    public function status($id)
    {
        $id = Crypt::decrypt($id);
        $appointment = Appointment::find($id);
        $currently_attending = Appointment::select('number')->where('slot_id', $appointment->slot_id)->where('date', $appointment->date)->where('status', 'served')->first();
        $total_waiting = Appointment::where('slot_id', $appointment->slot_id)->where('date', $appointment->date)->where('number', '<', $appointment->number)->whereIn('status', ['book', 'check in'])->get()->count();

        // user can not see the appointment status after +1 day of appointment
        $expired_date = date('Y-m-d', strtotime('+1 day', strtotime($appointment->date)));
        $date_now = date('Y-m-d');
        if ($date_now > $expired_date) {
            return redirect('https://www.kyoo.id/cloud/');
        } 
        
        return view('appointmentStatus', [
            'appointment' => $appointment,
            'total_waiting' => $total_waiting,
            'currently_attending' => isset($currently_attending) ? intval($currently_attending->number) : 0
        ]);
    }
}
