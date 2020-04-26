<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service;
use App\Appointment;
use App\Slot;

class ServiceController extends Controller
{
    public function getAllByBranchId($branch_id)
    {
        $dateNow = date('Y-m-d');
        $services = Service::where('branch_id', $branch_id)->get();

        foreach ($services as $service) {
            // get filled slot
            $filledSlot = Appointment::whereHas('Slot', function($query) use ($service) {
                $query->where('service_id', $service->id);
            })->where('date', $dateNow)->whereIn('status', ['book', 'check in'])->get();

            // get total slot
            $totalSlot = Slot::whereServiceId($service->id)->sum('max_slots');

            $service->filledSlot = count($filledSlot);
            $service->totalSlot = (int) $totalSlot;
        }

        return response()->json([
            'success' => true,
            'message' => 'get all services by branch id',
            'data' => $services
        ]);
    }
}
