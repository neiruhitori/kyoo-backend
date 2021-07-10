<?php

namespace App\Http\Controllers\API\External;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DirectQueue;
use App\WorkstationService;
use App\Service;
use App\Schedule;
use App\ScheduleTemplateDetail;
use App\Http\Requests\API\External\DirectQueue\Store;
use App\Events\VCTDirectQueue as VCTDirectQueueEvent;
use App\Events\DirectQueue as DirectQueueEvent;

class DirectQueueController extends Controller
{
    public function store(Store $request)
    {
        /**
         * additional validations:
         * - user cant create direct queue on closed day with schedule template
         * - user cant create direct queue on closed day
         */
        
        $current_date = date('Y-m-d');
        $current_time = date('H:i');
        $workstationService = WorkstationService::whereServiceId($request->service_id)->orderBy('priority', 'ASC')->first();

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
        $lastDirectQueue = DirectQueue::where('service_id', $workstationService->service_id)->whereDate('created_at', Date('Y-m-d'))->orderBy('queue_no', 'desc')->get();

        if (count($lastDirectQueue) > 0) {
            $queueNo = (int) $lastDirectQueue[0]->queue_no + 1;
        }else{
            $queueNo = $serviceOrderNumber . sprintf('%03s', ((int) count($lastDirectQueue) + 1));
        }

        $input = $request->all();
        $input['queue_no'] = $queueNo;
        $input['workstation_service_id'] = $workstationService->id;
        $input['service_id'] = $workstationService->service_id;
        $input['workstation_id'] = $workstationService->workstation_id;
        $input['direct_queue_channel'] = 'API';
        $directQueue = DirectQueue::create($input);

        // send event to update Direct Queue Monitor
        event(new VCTDirectQueueEvent($directQueue, $workstationService->Service->branch_id));
        event(new DirectQueueEvent($directQueue, $workstationService->Service->branch_id));

        $workstation['total_waiting'] = DirectQueue::whereServiceId($directQueue->service_id)->whereStatus('waiting')->whereDate('created_at', date('Y-m-d'))->count();

        return response()->json([
            'success' => true,
            'message' => 'direct queue created',
            'data' => $workstation
        ]);
    }
}
