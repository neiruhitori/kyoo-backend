<?php

namespace App\Http\Controllers\CS;

use App\Service;
use App\Appointment;
use App\WorkstationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Events\AppointmentServed;
use App\Events\AppointmentEndServed;
use App\Http\Controllers\Controller;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\Auth;

class AppointmentMonitorController extends Controller
{
    private $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function index()
    {
        $workstationId = Auth::user()->WorkstationVct->workstation_id;
        $serviceIds = WorkstationService::where('workstation_id', $workstationId)
            ->get()
            ->map(function ($ws) {
                return $ws->service_id;
            });

        $services = Service::whereIn('id', $serviceIds)->get();

        return view('cs.appointment.monitor', ['services'=>$services]);
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

            $queueCounts = [
                        'check_in'     => $data->where('status', 'check in')->count(),
                        'no_show'     => $data->where('status', 'no show')->count(),
                        'end_served'  => $data->where('status', 'end served')->count(),
                    ];
            return response()->json([
                        'success' => true,
                        'message' => 'get all appointment by today',
                        'data' => $data,
                        'count' => $queueCounts ]);
        // return response()->json($data, 200);
    }

    public function checkIn($id)
    {
        try {
            $this->appointmentService->checkIn($id);

            return response()->json([
                'success' => true,
                'message' => 'Appointment berhasil diperbarui'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function noShow($id)
    {
        try {
            $this->appointmentService->noShow($id);

            return response()->json([
                'success' => true,
                'message' => 'Appointment diperbarui'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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
        try {
            $this->appointmentService->endServe($id);

            return response()->json([
                'success' => true,
                'message' => 'Appointment selesai'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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
