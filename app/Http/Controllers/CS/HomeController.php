<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Appointment;
use Auth;
class HomeController extends Controller
{
    public function index()
    {
        $dateNow = date('Y-m-d');
        
        $appointments = Appointment::whereHas('Slot.Service', function($query) {
            $query->where('branch_id', Auth::user()->branch_id);
        })->where('date', $dateNow)->whereIn('status', ['book', 'attend'])->get();

        $historyAppointments = Appointment::whereHas('Slot.Service', function($query) {
            $query->where('branch_id', Auth::user()->branch_id);
        })->where('date', $dateNow)->whereIn('status', ['not attend', 'served'])->get();
        
        return view('cs.home', [
            'appointments' => $appointments,
            'historyAppointments' => $historyAppointments
        ]);
    }
    
    public function updateAppointment(Request $request, Appointment $appointment)
    {
        $appointment->update($request->all());
        $request->session()->flash('success', 'Appointment #'.$appointment->id.' status has been changed!');
        return redirect(route('cs.home'));
    }
}
