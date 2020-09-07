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

        return view('appointmentStatus', [
            'appointment' => $appointment,
            'total_waiting' => $total_waiting,
            'currently_attending' => isset($currently_attending) ? intval($currently_attending->number) : 0
        ]);
    }
}
