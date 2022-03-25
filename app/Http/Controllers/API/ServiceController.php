<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service;
use App\Appointment;
use App\Slot;

class ServiceController extends Controller
{
    public function getAllByBranchId(Request $requets, $branch_id)
    {
        $dateNow = $request->date ?? date('Y-m-d');
        $dayNow =  strtolower(date("l", strtotime($dateNow)));
        $services = Service::where('branch_id', $branch_id)->get();

        foreach ($services as $service) {
            // get filled slot
            $filledSlot = Appointment::whereHas('Slot', function($query) use ($service) {
                $query->where('service_id', $service->id);
            })->where('date', $dateNow)->whereIn('status', ['book', 'check in'])->get();

            // get total slot
            $slots = Slot::where('day', $dayNow)
                ->whereServiceId($service->id);

            $service->slots = $slots->get();
            $service->filledSlot = count($filledSlot);
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
            $slot->filled_slot = Appointment::whereHas('Slot', function ($query) use ($service) {
                $query->where('service_id', $service->id);
            })
                ->where('date', $date)
                ->whereIn('status', ['book', 'check in'])
                ->count();
        }

        return response()->json([
            'success' => true,
            'message' => 'get service by id',
            'data' => $service
        ]);
    }
}
