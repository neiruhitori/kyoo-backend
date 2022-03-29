<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service;
use App\Appointment;
use App\Models\Exhibition;
use App\Slot;

class ServiceController extends Controller
{
    public function getAllByBranchId(Request $request, $branch_id)
    {
        $dateNow = $request->date ?? date('Y-m-d');
        $dayNow =  strtolower(date("l", strtotime($dateNow)));
        $services = Service::where('branch_id', $branch_id)->get();

        foreach ($services as $service) {
            // get filled slot
            $filledSlot = $this->getFilledSlot($request->queue_type, [
                'service_id' => $service->id,
                'date' => $dateNow
            ]);

            // get total slot
            $slots = Slot::where('day', $dayNow)
                ->whereServiceId($service->id);

            $service->slots = $slots->get();
            $service->filledSlot = $filledSlot;
            $service->totalSlot = $slots->sum('max_slots');
        }

        return response()->json([
            'success' => true,
            'message' => 'get all services by branch id',
            'data' => $services
        ]);
    }

    public function getById(Request $request, $id)
    {
        $service = Service::with('Slot')->where('id', $id)->first();
        $date = $request->date ?? date('Y-m-d');

        foreach ($service->slot as $slot) {
            $slot->filled_slot = $this->getFilledSlot($request->queue_type, [
                'service_id' => $service->id,
                'date' => $date
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'get service by id',
            'data' => $service
        ]);
    }

    public function getFilledSlot($queue_type, $params)
    {
        if ($queue_type == 'appointment') {
            return Appointment::whereHas('Slot', function ($query) use ($service) {
                $query->where('service_id', $params['service_id']);
            })
                ->where('date', $params['date'])
                ->whereIn('status', ['book', 'check in'])
                ->count();
        }

        if ($queue_type === 'exhibition') {
            return Exhibition::whereHas('Slot', function ($query) use ($params) {
                $query->where('service_id', $params['service_id']);
            })
                ->where('date', $params['date'])
                ->where('status', 'book')
                ->count();
        }
    }
}
