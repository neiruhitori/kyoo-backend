<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exhibition;
use App\Slot;
use App\Schedule;
use App\ScheduleTemplateDetail;
use App\Http\Resources\Exhibition as ExhibitionCollection;
use App\Http\Requests\API\StoreExhibition;
use Mail;
use App\Mail\CS\StoreExhibitionMail;

class ExhibitionController extends Controller
{
    private $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
 
    private function generate_booking_code($input, $strength = 5) {
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
    
        return $random_string;
    }

    public function store(StoreExhibition $request)
    {
        $branch = Slot::find($request->slot_id)->Service->Branch;

        $total_current_booking = Exhibition::where('date', $request->date ?? date('Y-m-d'))
            ->whereHas('Slot.Service', function($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            })
            ->count();
        
        if (!$branch->BranchType->is_premium && $total_current_booking >= 200) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah antrian melebihi batas maksimal harian untuk cabang berlisensi gratis',
                'data' => []
            ]);
        }

        // cant create booking on same time slot
        $same_booking = Exhibition::where(function ($query) use  ($request) {
                $query->where('email', $request->email)
                    ->orWhere('phone', $request->phone);
            })
            ->where('slot_id', $request->slot_id) 
            ->where('date', $request->date)
            ->first(); 

        if ($same_booking) {
            return response()->json([
                'success' => false,
                'message' => 'Only 1 booking request allowed in the same time slot',
                'data' => []
            ]);
        }

        $current_date = date('Y-m-d');
        $current_time = date('H:i');
        $selected_day = strtolower(date('l', strtotime($request->date)));
        $slot = Slot::find($request->slot_id);

        // cant create booking on closed day by schedule template
        if($slot->Service->Branch->schedule_template_id){
            $schedule_template_details = ScheduleTemplateDetail::where('schedule_template_id', $slot->Service->Branch->schedule_template_id)->where('date', $request->date)->first();
            if($schedule_template_details){
                return response()->json([
                    'success' => false,
                    'message' => 'Service Provider Already Closed',
                    'data' => []
                ]);    
            }
        }

        // cant create booking on closed day
        $slot_day = Schedule::where('branch_id', $slot->Service->branch_id)->where('day', $selected_day)->get(['day', 'status'])->first();
        if ($slot_day && $slot_day->status == 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Service Provider Already Closed',
                'data' => []
            ]);
        }

        // cant create booking with past time slot
        if ($request->date == $current_date && $slot->end_time < $current_time) {
            return response()->json([
                'success' => false,
                'message' => 'Service Provider Already Closed',
                'data' => []
            ]);
        }

        $input = $request->all();
        $input['booking_code'] = $this->generate_booking_code($this->permitted_chars, 5);
        $input['queue_order'] = Exhibition::whereDateAndSlotId($request->date, $request->slot_id)->get()->count() + 1;
        $booking = Exhibition::create($input);

        // send email to customer
        Mail::to($request->email)
            ->send(new StoreExhibitionMail($booking));

        return response()->json([
            'success' => true,
            'message' => 'create exhibition',
            'data' => $booking
        ]);
    }

    public function show(Exhibition $exhibition)
    {
        return response()->json([
            'success' => true,
            'message' => 'get detail exhibition by id',
            'data' => new ExhibitionCollection($exhibition)
        ]);
    }
}
