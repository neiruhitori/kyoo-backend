<?php

namespace App\Http\Controllers\CS;

use App\Http\Controllers\Controller;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use App\Schedule;
use App\Slot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class FutureAppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function index()
    {
        $schedules = Schedule::where('branch_id', Auth::user()->branch_id)->get();
        $slots = Slot::with('Service')->whereIn(
            'service_id',
            Auth::user()->WorkstationVct->Workstation->WorkstationService->map(function ($value) {
                return $value->service_id;
            })
        )->get();

        return view('cs.appointment.futureAppointment', [
            'schedules' => $schedules,
            'slots' => $slots,
        ]);
    }

    public function getFutureAppointments(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $appointments = $this->appointmentService->getFutureAppointmentsByDate($request->date);

        return response()->json($appointments);
    }

    public function getAppointmentSlots(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date'
        ]);

        $appointments = $this->appointmentService->getAppointmentSlotsByDateRange($request->from, $request->to);

        return response()->json($appointments);
    }

    public function showAppointmentSlot($id, Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);
        
        $service = $this->appointmentService->getAppointmentSlotsByServiceId($id, $request->date);

        return response()->json($service);
    }
}
