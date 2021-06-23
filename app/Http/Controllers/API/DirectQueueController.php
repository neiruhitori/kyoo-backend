<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DirectQueue;
use App\Branch;
use App\WorkstationService;
use App\Service;
use App\Schedule;
use App\ScheduleTemplateDetail;
use App\Http\Requests\API\DirectQueue\Store as DirectQueueStore;
use App\Http\Requests\API\DirectQueue\Feedback as DirectQueueFeedback;
use App\Http\Resources\DirectQueue\All as DirectQueueAll;
use App\Http\Resources\DirectQueue\Detail as DirectQueueDetail;
use Auth;
use App\Events\VCTDirectQueue as VCTDirectQueueEvent;
use App\Events\DirectQueue as DirectQueueEvent;

class DirectQueueController extends Controller
{

    private function InitQuery()
    {
        return DirectQueue::query()
                    ->addSelect([
                        'vct_priority' => WorkstationService::query()
                                                                ->select('priority')
                                                                ->whereColumn('service_id', 'direct_queues.service_id')
                                                                ->where('workstation_id', Auth::user()->WorkstationVct->workstation_id)
                                                                ->limit(1)
                    ])
                    ->with('Service')
                    ->whereDate('direct_queues.created_at', Date('Y-m-d'))
                    ->whereNotIn('status', ['end served', 'no show'])
                    ->orderBy('vct_priority', 'ASC')
                    ->orderBy('direct_queues.requeue_count', 'ASC')
                    ->orderBy('direct_queues.queue_no', 'ASC');
    }

    public function index(Branch $branch)
    {
        $workstationServices = WorkstationService::query()
                                                    ->with('Service')
                                                    ->whereHas('Service', function($query) use ($branch){
                                                        $query->whereBranchId($branch->id);
                                                    })
                                                    ->get();

        return response()->json([
            'success' => true,
            'message' => 'get all direct queue list by branch_id',
            'data' => DirectQueueAll::collection($workstationServices)
        ]);
    }

    public function show(DirectQueue $directQueue)
    {
        return response()->json([
            'success' => true,
            'message' => 'get detail direct queues',
            'data' => new DirectQueueDetail($directQueue)
        ]);
    }

    public function store(DirectQueueStore $request)
    {

        /**
         * additional validations:
         * - user cant create direct queue on closed day with schedule template
         * - user cant create direct queue on closed day
         */
        
        $current_date = date('Y-m-d');
        $current_time = date('H:i');
        $workstationService = WorkstationService::find($request->workstation_service_id);

        // cant create direct queue on closed day by schedule template
        if($workstationService->Service->Branch->schedule_template_id){
            $schedule_template_details = ScheduleTemplateDetail::query()
                                                                    ->where('schedule_template_id', $workstationService->Service->Branch->schedule_template_id)
                                                                    ->where('date', $current_date)
                                                                    ->first();
            if($schedule_template_details){
                return response()->json([
                    'success' => false,
                    'message' => 'Service Provider Already Closed',
                    'data' => []
                ]);    
            }
        }

        // cant create direct queue on closed day
        $selectedSchedule = Schedule::query()
                                ->where('branch_id', $workstationService->Service->branch_id)
                                ->where('day', strtolower(date('l', strtotime($current_date))))
                                ->get(['day', 'status', 'start_time', 'end_time'])
                                ->first();

        if (!$selectedSchedule || $selectedSchedule->status == 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Service Provider Already Closed',
                'data' => []
            ]);
        }

        // cant create direct queue before open time and after closed time
        if ($current_time < $selectedSchedule->start_time || $current_time > $selectedSchedule->end_time) {
            return response()->json([
                'success' => false,
                'message' => 'Service Provider Already Closed',
                'data' => []
            ]);
        }

        $serviceOrderNumber = Service::where('branch_id', $workstationService->Service->branch_id)->where('id', '<=', $workstationService->service_id)->count();
        $lastDirectQueue = DirectQueue::where('service_id', $workstationService->service_id)->whereDate('created_at', Date('Y-m-d'))->count();

        if ($lastDirectQueue > 0) {
            $lastDirectQueue = DirectQueue::where('workstation_service_id', $request->workstation_service_id)->whereDate('created_at', Date('Y-m-d'))->orderBy('queue_no', 'desc')->first();
            $queueNo = (int) $lastDirectQueue->queue_no + 1;
        }else{
            $queueNo = $serviceOrderNumber . sprintf('%03s', ++$lastDirectQueue);
        }

        $input = $request->all();
        $input['queue_no'] = $queueNo;
        $input['service_id'] = $workstationService->service_id;
        $input['workstation_id'] = $workstationService->workstation_id;
        $input['user_id'] = Auth::id();
        $input['direct_queue_channel'] = 'Mobile Apps';
        $directQueue = DirectQueue::create($input);

        // send event to update Direct Queue Monitor
        event(new VCTDirectQueueEvent($directQueue));
        event(new DirectQueueEvent($directQueue));

        $workstation['total_waiting'] = DirectQueue::whereServiceId($directQueue->service_id)->whereStatus('waiting')->whereDate('created_at', date('Y-m-d'))->count();

        return response()->json([
            'success' => true,
            'message' => 'direct queue created',
            'data' => $workstation
        ]);
    }

    public function upcoming()
    {
        $directQueues = DirectQueue::whereUserId(Auth::id())->whereStatus('waiting')->whereDate('created_at', '>=', date('Y-m-d'))->get();
        return response()->json([
            'success' => true,
            'message' => 'get upcoming direct queues',
            'data' => DirectQueueDetail::collection($directQueues)
        ]);
    }

    public function feedback(DirectQueue $directQueue, DirectQueueFeedback $request)
    {
        $directQueue->update([
            'rating' => $request->rating,
            'is_liked' => $request->is_liked,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'success give feedback direct queue',
            'data' => $directQueue
        ]);
    }
}
