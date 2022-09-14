<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Appointment;
use Illuminate\Support\Carbon;
use App\Events\AppointmentServed;
use App\Events\AppointmentEndServed;

class AppointmentMonitorController extends Controller
{
    public function index()
    {
        return view('cs.appointment.monitor');
    }

    public function getAll()
    {
        $workstationVct = Auth::user()->WorkstationVct;
        $serviceIds = $workstationVct
            ->Workstation
            ->WorkstationService
            ->map(function ($item) {
                return $item->service_id;
            })
            ->toArray();

        $data = Appointment::with(['Service', 'Slot'])
            ->where('date', date('Y-m-d'))
            ->whereIn('service_id', $serviceIds)
            ->where(function ($query) use ($workstationVct) {
                $query->whereNull('workstation_id')
                    ->orWhere('workstation_id', $workstationVct->workstation_id);
            })
            ->orderBy('number')
            ->get();

        return response()->json($data, 200);
    }

    public function checkIn($id)
    {
        $appointment = Appointment::find($id);

        $appointment->status = 'check in';
        $appointment->checkin_time = date('Y-m-d H:i:s');

        $appointment->save();

        return response()->json($appointment, 200);
    }

    public function noShow($id)
    {
        $appointment = Appointment::find($id);

        $appointment->status = 'no show';
        $appointment->checkin_time = date('Y-m-d H:i:s');

        $appointment->save();

        return response()->json($appointment, 200);
    }

    public function served($id)
    {
        $workstation_id = Auth::user()->WorkstationVct->workstation_id;
        
        $appointment = Appointment::find($id);

        $waiting_duration = Carbon::now()->diffInseconds(Carbon::parse($appointment->checkin_time));
        
        $appointment->status = 'served';
        $appointment->vct_id = Auth::id();
        $appointment->workstation_id = $workstation_id;
        $appointment->served_time = date('Y-m-d H:i:s');
        $appointment->waiting_duration = $waiting_duration;
        
        $appointment->save();

        $appointment->branch_id = Auth::user()->branch_id;

        // Send event
        AppointmentServed::dispatch($appointment);

        return response()->json($appointment, 200);
    }

    public function endServed($id)
    {
        $appointment = Appointment::find($id);

        $serving_duration = Carbon::now()->diffInseconds(Carbon::parse($appointment->served_time));
        
        $appointment->status = 'end served';
        $appointment->end_served_time = date('Y-m-d H:i:s');
        $appointment->serving_duration = $serving_duration;
        
        $appointment->save();

        $appointment->branch_id = Auth::user()->branch_id;

        // Send event
        AppointmentEndServed::dispatch($appointment);

        return response()->json($appointment, 200);
    }
}
