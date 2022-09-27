<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Appointment;
use Illuminate\Support\Carbon;
use App\Events\AppointmentServed;
use App\Events\AppointmentEndServed;
use App\Services\AppointmentService;

class AppointmentMonitorController extends Controller
{
    private $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

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
        try {
            $this->appointmentService->serve($id);

            return response()->json([
                'success' => true,
                'message' => 'Appointment dilayani'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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

    public function cancel($appointmentId)
    {
        try {
            $this->appointmentService->cancel($appointmentId);

            return response()->json([
                'success' => true,
                'message' => 'Appointment dibatalkan'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
