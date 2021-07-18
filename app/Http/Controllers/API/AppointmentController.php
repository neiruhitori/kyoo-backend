<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\StoreAppointment;
use App\Http\Requests\API\FeedbackAppointment;
use App\Appointment;
use App\Slot;
use App\Schedule;
use App\ScheduleTemplateDetail;
use App\DirectQueue;
use App\Http\Resources\Appointment as AppointmentCollection;
use App\Http\Resources\Upcomming as UpcommingCollection;
use Auth;
use Collection;

class AppointmentController extends Controller
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

    public function store(StoreAppointment $request)
    {
        /**
         * additional validations:
         * - user cant create appointment on same time slot
         * - user cant create appointment on closed day with schedule template
         * - user cant create appointment on closed day
         * - user cant create appointment with past time slot for today
         */
        
        // cant create appointment on same time slot
        $sameAppointment = Appointment::where(['user_id' => $request->user_id])
                                            ->where(['slot_id' => $request->slot_id]) 
                                            ->where(['date' => $request->date])
                                            ->first(); 
        if ($sameAppointment) {
            return response()->json([
                'success' => false,
                'message' => 'Only 1 appointment request in the same time slot',
                'data' => []
            ]);
        }

        $current_date = date('Y-m-d');
        $current_time = date('H:i');
        $selected_day = strtolower(date('l', strtotime($request->date)));
        $slot = Slot::find($request->slot_id);

        // cant create appointment on closed day by schedule template
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

        // cant create appointment on closed day
        $slot_day = Schedule::where('branch_id', $slot->Service->branch_id)->where('day', $selected_day)->get(['day', 'status'])->first();
        if ($slot_day && $slot_day->status == 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Service Provider Already Closed',
                'data' => []
            ]);
        }

        // cant create appointment with past time slot
        if ($request->date == $current_date && $slot->end_time < $current_time) {
            return response()->json([
                'success' => false,
                'message' => 'Service Provider Already Closed',
                'data' => []
            ]);
        }

        $input = $request->all();
        $input['booking_code'] = $this->generate_booking_code($this->permitted_chars, 5);
        $input['number'] = Appointment::whereDateAndSlotId($request->date, $request->slot_id)->get()->count() + 1;
        $appointment = Appointment::create($input);

        return response()->json([
            'success' => true,
            'message' => 'create appointment',
            'data' => $appointment
        ]);
    }

    public function index()
    {
        $appointments = Appointment::where('user_id', Auth::id())->whereIn('status', ['book', 'check in', 'served'])->orderBy('date', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'get all appointment',
            'data' => AppointmentCollection::collection($appointments)
        ]);
    }

    public function show(Appointment $appointment)
    {
        return response()->json([
            'success' => true,
            'message' => 'get detail appointment by id',
            'data' => new AppointmentCollection($appointment)
        ]);
    }
    
    public function history()
    {
        $appointments = Appointment::where('user_id', Auth::id())->whereIn('status', ['no show', 'end served'])->orderBy('date', 'asc')->get()->toArray();
        foreach ($appointments as $key => $appointment) {
            $appointments[$key]['is_direct_queue'] = false;
            $appointments[$key]['sorting_date'] = $appointment['date'];
        }

        $directQueues = DirectQueue::whereUserId(Auth::id())->whereNotIn('status', ['waiting', 'served'])->orderBy('created_at', 'asc')->get()->toArray();
        foreach ($directQueues as $key => $directQueue) {
            $directQueues[$key]['is_direct_queue'] = true;
            $directQueues[$key]['sorting_date'] = date('Y-m-d', strtotime($directQueue['created_at']));
        }
        $histories = array_merge($directQueues, $appointments);
        usort($histories, function($a, $b) {return strcmp($a['sorting_date'], $b['sorting_date']);});
        return response()->json([
            'success' => true,
            'message' => 'get all history appointment',
            'data' => UpcommingCollection::collection($histories)
        ]);
    }

    public function feedback(FeedbackAppointment $request, Appointment $appointment)
    {
        $appointment->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'success give feedback appointment',
            'data' => $appointment
        ]);
    }

    public function upcoming()
    {
        $dateNow = date('Y-m-d');
        $appointments = Appointment::where('user_id', Auth::id())->where('date', '>=', $dateNow)->where('status', 'book')->orderBy('date', 'desc')->get()->toArray();

        return response()->json([
            'success' => true,
            'message' => 'get upcoming appointment',
            'data' => $appointments
        ]);
    }

    public function upcomingCombine()
    {
        $dateNow = date('Y-m-d');
        $appointments = Appointment::where('user_id', Auth::id())->where('date', '>=', $dateNow)->where('status', 'book')->orderBy('date', 'asc')->get()->toArray();
        foreach ($appointments as $key => $appointment) {
            $appointments[$key]['is_direct_queue'] = false;
            $appointments[$key]['sorting_date'] = $appointment['date'];
        }

        $directQueues = DirectQueue::whereUserId(Auth::id())->where('status', 'waiting')->whereDate('created_at', '>=', date('Y-m-d'))->orderBy('created_at', 'asc')->get()->toArray();
        foreach ($directQueues as $key => $directQueue) {
            $directQueues[$key]['is_direct_queue'] = true;
            $directQueues[$key]['sorting_date'] = date('Y-m-d', strtotime($directQueue['created_at']));
        }

        $histories = array_merge($directQueues, $appointments);
        usort($histories, function($a, $b) {return strcmp($a['sorting_date'], $b['sorting_date']);});

        return response()->json([
            'success' => true,
            'message' => 'get upcoming appointment and direct queue',
            'data' => UpcommingCollection::collection($histories)
        ]);
    }
}
