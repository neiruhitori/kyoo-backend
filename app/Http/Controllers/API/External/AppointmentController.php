<?php

namespace App\Http\Controllers\API\External;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\External\Appointment\Store;
use App\Slot;
use App\Services\AppointmentService;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appoinmentService)
    {
        $this->appointmentService = $appoinmentService;
    }

    public function store(Store $request)
    {
        $slot = Slot::find($request->slot_id);

        $data = $request->all();

        $data['branch_id'] = $slot->Service->Department->branch_id;
        $data['service_id'] = $slot->service_id;

        try {
            $appointment = $this->appointmentService->create($data);
    
            return response()->json([
                'success' => true,
                'message' => 'create appointment',
                'data' => $appointment
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'sucess' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }
}
