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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\CS\StoreAppointment as StoreAppointmentMail;
use App\Events\AppointmentCreated;

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
        $branch = Slot::find($request->slot_id)->Service->Branch;

        $total_current_booking = Appointment::where('date', $request->date ?? date('Y-m-d'))
            ->whereHas('Slot.Service', function($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            })
            ->count();
        
        if (!$branch->BranchType->is_premium && $total_current_booking >= 200) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah appointment melebihi batas maksimal harian untuk cabang berlisensi gratis',
                'data' => []
            ]);
        }

        /**
         * additional validations:
         * - user cant create appointment on same time slot
         * - user cant create appointment on closed day with schedule template
         * - user cant create appointment on closed day
         * - user cant create appointment with past time slot for today
         */
        
        // cant create appointment on same time slot
        $sameAppointment = Appointment::where(function ($query) use  ($request) {
            $query->where('email', $request->email)
                ->orWhere('phone', $request->phone);
        })
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
        $input['number'] = Appointment::whereHas('Service', function ($query) use ($branch) {
            $query->where('branch_id', $branch->id);
        })
            ->where('date', $request->date)
            ->get()
            ->count() + 1;
        $input['service_id'] = $slot->service_id;
        $appointment = Appointment::create($input);

        $appointment->branch_id = $branch->id;

        Mail::to($request->email)
            ->send(new StoreAppointmentMail($appointment));

        // Send event
        AppointmentCreated::dispatch($appointment);

        return response()->json([
            'success' => true,
            'message' => 'create appointment',
            'data' => $appointment
        ]);
    }

    public function index()
    {
        $appointments = Appointment::where('user_id', Auth::id())
            ->whereIn('status', ['book', 'check in', 'served'])
            ->orderBy('date', 'desc')
            ->get();
        
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
        $appointments = Appointment::where('user_id', Auth::id())->whereIn('status', ['no show', 'end served'])->orderBy('date', 'desc')->get()->toArray();
        foreach ($appointments as $key => $appointment) {
            $appointments[$key]['is_direct_queue'] = false;
            $appointments[$key]['sorting_date'] = $appointment['date'];
        }

        $directQueues = DirectQueue::whereUserId(Auth::id())->whereNotIn('status', ['waiting', 'served'])->orderBy('created_at', 'desc')->get()->toArray();
        foreach ($directQueues as $key => $directQueue) {
            $directQueues[$key]['is_direct_queue'] = true;
            $directQueues[$key]['sorting_date'] = date('Y-m-d', strtotime($directQueue['created_at']));
        }

        // merging appointments and direct queues and sorting by date desc
        $histories = array_merge($directQueues, $appointments);
        usort($histories, function($a, $b) {return strcmp($a['sorting_date'], $b['sorting_date']);});
        $histories = collect($histories)->sortByDesc('sorting_date')->toArray();

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
        $appointments = Appointment::where('user_id', Auth::id())->where('date', '>=', $dateNow)->whereIn('status', ['book', 'check in', 'served'])->orderBy('date', 'desc')->get()->toArray();

        return response()->json([
            'success' => true,
            'message' => 'get upcoming appointment',
            'data' => $appointments
        ]);
    }

    public function upcomingCombine()
    {
        $dateNow = date('Y-m-d');
        $appointments = Appointment::where('user_id', Auth::id())->where('date', '>=', $dateNow)->whereIn('status', ['book', 'check in', 'served'])->orderBy('date', 'asc')->get()->toArray();
        foreach ($appointments as $key => $appointment) {
            $appointments[$key]['is_direct_queue'] = false;
            $appointments[$key]['sorting_date'] = $appointment['date'];
        }

        $directQueues = DirectQueue::whereUserId(Auth::id())->whereIn('status', ['waiting', 'served', 'requeue'])->whereDate('created_at', '>=', date('Y-m-d'))->orderBy('created_at', 'asc')->get()->toArray();
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
