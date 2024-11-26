<?php

namespace App\Http\Controllers\API;

use App\Slot;
use App\Branch;
use App\Service;
use App\Appointment;
use App\DirectQueue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\API\StoreAppointment;

use App\Http\Requests\API\FeedbackAppointment;
use App\Http\Resources\Upcomming as UpcommingCollection;
use App\Events\AppointmentQueue as AppointmentQueueEvents;
use App\Http\Resources\Appointment as AppointmentCollection;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function store(StoreAppointment $request)
    {
        $slot = Slot::find($request->slot_id);
       
        $data = $request->all();
        $data['branch_id'] = $slot->Service->Department->branch_id;
        $data['service_id'] = $slot->service_id;

        try {
            $appointment = $this->appointmentService->create($data);
            $branch = Branch::where('id', $appointment->branch_id)->first();
            if (
                $appointment->phone &&
                $branch &&
                $branch->getIsPremiumAttribute() &&
                $branch->BranchConfiguration->wa_notification != false &&
                $branch->BranchConfiguration->whatsapp_type == 'official_wa_branch'
            ) {
                $appointment->sendappointmentCreatedNotification($appointment);
            }elseif(
                $appointment->phone &&
                $branch &&
                $branch->getIsPremiumAttribute() &&
                $branch->BranchConfiguration->wa_notification != false &&
                $branch->BranchConfiguration->whatsapp_type == 'wa_kyoo'
            ){
                $appointment->sendNotificationWaBlast($appointment);
            }

            event(new AppointmentQueueEvents($appointment, $data['branch_id']));
    
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

    public function cancel($appointmentId)
    {
        try {
            $this->appointmentService->cancel($appointmentId);
    
            return response()->json([
                'success' => true,
                'message' => 'Appointment dibatalkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
