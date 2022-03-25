<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\GetSlots;
use App\Slot;
use App\Service;
use App\Branch;
use App\Schedule;
use App\ScheduleTemplateDetail;
use App\Appointment;

class SlotController extends Controller
{
    public function index(GetSlots $request)
    {
        // additional validations
        $current_date = date('Y-m-d');
        $current_time = date('H:i:s');
        $selected_day = strtolower(date("l", strtotime($request->date)));
        $branch = Branch::whereHas('Service', function($query) use ($request){
            $query->where('id', $request->service_id);
        })->get()->first();

        // validation by schedule template
        if ($branch->schedule_template_id) {
            $schedule_template_details = ScheduleTemplateDetail::where('schedule_template_id', $branch->schedule_template_id)->where('date', $request->date)->count();
            if ($schedule_template_details > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Available Time Slot',
                    'data' => []
                ]);
            }
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
        $slots = Slot::with('Service')->where('service_id', $request->service_id)->where('day', $selected_day);
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
