<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Appointment;
use App\Log;
use Auth;
class HomeController extends Controller
{
    public function index()
    {
        $dateNow = date('Y-m-d');
        
        $appointments = Appointment::whereHas('Slot.Service', function($query) {
            $query->where('branch_id', Auth::user()->branch_id);
        })->where('date', $dateNow)->whereIn('status', ['book', 'check in', 'served'])->get();

        $historyAppointments = Appointment::whereHas('Slot.Service', function($query) {
            $query->where('branch_id', Auth::user()->branch_id);
        })->where('date', $dateNow)->whereIn('status', ['no show', 'end served'])->get();
        
        return view('cs.home', [
            'appointments' => $appointments,
            'historyAppointments' => $historyAppointments
        ]);
    }
    
    public function updateAppointment(Request $request, Appointment $appointment)
    {
        switch ($request->status) {
            case 'check in':
                $appointment->update([
                    'vct_id' => Auth::id(),
                    'status' => $request->status,
                    'checkin_time' => date(now())
                ]);
                Log::create([
                    'user_id' => Auth::id(),
                    'description' => 'Update Appointment to Check in'
                ]);       
                break;
            case 'served':
                $onServed = Appointment::where('slot_id', $appointment->slot_id)->where('date', $appointment->date)->where('status', 'served')->first();
                if (isset($onServed)) {
                    $request->session()->flash('error', 'Cant move to serve because other appointment in progress on '.$appointment->Slot->Service->name);
                    return redirect(route('cs.home'));
                }
                $appointment->update([
                    'vct_id' => Auth::id(),
                    'status' => $request->status,
                    'served_time' => date(now())
                ]);
                Log::create([
                    'user_id' => Auth::id(),
                    'description' => 'Update Appointment to Served'
                ]);    
                break;
            case 'end served':
                $appointment->update([
                    'vct_id' => Auth::id(),
                    'status' => $request->status,
                    'end_served_time' => date(now())
                ]);
                Log::create([
                    'user_id' => Auth::id(),
                    'description' => 'Update Appointment to End Served'
                ]);    
                break;
            case 'no show':
                $appointment->update([
                    'vct_id' => Auth::id(),
                    'status' => $request->status,
                    'served_time' => date(now())
                ]);
                Log::create([
                    'user_id' => Auth::id(),
                    'description' => 'Update Appointment to No Show'
                ]);    
                break;
        }
        $request->session()->flash('success', 'Appointment #'.$appointment->id.' status has been changed!');
        return redirect(route('cs.home'));
    }
}
