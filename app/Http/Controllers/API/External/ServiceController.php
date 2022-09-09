<?php

namespace App\Http\Controllers\API\External;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service;
use App\Appointment;
use App\Slot;
use App\Branch;
use App\ScheduleTemplateDetail;
use App\Schedule;
use App\Http\Requests\API\External\Service\Slot as GetSlot;
use App\Models\BranchScheduleTemplateDetail;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $branch = Branch::whereHas('BranchToken', function($query) use ($request){
            $query->whereToken($request->branch_token);
        })->first();

        $limit = $request->limit ?: 10;
        $type = $request->type ?: 'direct queue';

        $services = Service::query()->whereBranchId($branch->id);
        $services->when($request->name, function($query) use($request){
            $query->where('name', 'like', "%{$request->name}%");
        });

        if ($type == 'direct queue') {
            $services->whereHas('WorkstationService');
        }

        $services = $services->paginate($limit);

        if ($type == 'appointment') {
            $dateNow = date('Y-m-d');
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
        }

        return response()->json([
            'success' => true,
            'message' => 'get all services by branch_id',
            'data' => $services
        ]);
    }

    public function slot(GetSlot $request, Service $service)
    {
        // additional validations
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');
        $selected_day = strtolower(date("l", strtotime($request->date)));
        $branch = Branch::whereHas('Service', function($query) use ($service){
            $query->where('id', $service->id);
        })->get()->first();

        // validation by holiday
        $holiday = BranchScheduleTemplateDetail::where([
            'branch_id' => $branch->id,
            'date' => $request->date
        ])->first();

        if ($holiday) {
            return response()->json([
                'success' => false,
                'message' => 'No Available Time Slot',
                'data' => []
            ]);
        }
        
        // validation by day
        $schedule = Schedule::where('branch_id', $branch->id)->where('day', $selected_day)->get('status')->first();

        if ($schedule->status == 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'No Available Time Slot',
                'data' => []
            ]);
        }

        // validation by today and time
        $slots = Slot::where('service_id', $service->id)->where('day', $selected_day);
        if ($current_date == $request->date) {
            $slots->where('end_time', '>', $current_time);
        }
        $slots = $slots->get();


        foreach ($slots as $slot) {
            $filledSlot = Appointment::whereHas('Slot', function($query) use ($slot) {
                $query->where('slot_id', $slot->id);
            })->where('date', $request->date)->get();

            $slot->filledSlot = count($filledSlot);
        }

        return response()->json([
            'success' => true,
            'message' => 'get all slot by service id',
            'data' => $slots
        ]);
    }
}
