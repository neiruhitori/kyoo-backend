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
        $services = Service::where('branch_id', $branch->id)
            ->get();

        foreach ($services as $service) {
            $service->total_queue = DirectQueue::where('service_id', $service->id)
                ->whereDate('created_at', date('Y-m-d'))
                ->count();
        }

        return response()->json([
            'success' => true,
            'message' => 'get direct queue services by branch id',
            'data' => $services
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
        $branch = Service::find($request->service_id)->Branch;

        $total_current_booking = DirectQueue::whereHas('Service', function ($query) use ($branch) {
            return $query->where('branch_id', $branch->id);
        })
            ->whereDate('created_at', date('Y-m-d'))
            ->count();
        
        if (!$branch->BranchType->is_premium && $total_current_booking >= 200) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah antrian melebihi batas maksimal harian untuk cabang berlisensi gratis'
            ]);
        }

        $service = Service::with('Branch')->where('id', $request->service_id)->first(); 

        // cant create direct queue on closed day by schedule template
        $holiday = ScheduleTemplateDetail::where('schedule_template_id', $service->Branch->schedule_template_id)
            ->where('date', date('Y-m-d'))
            ->first();
        
        if ($holiday) {
            return response()->json([
                'success' => false,
                'message' => 'Cabang sedang tutup hari ini'
            ]);
        }

        // cant create direct queue on closed day
        $schedule = Schedule::where('branch_id', $service->branch_id)
            ->where('day', strtolower(date('l')))
            ->first();
        
        if ($schedule->status == 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Cabang sedang tutup hari ini'
            ]);
        }

        // cant create direct queue before open time and after closed time
        if (date('H:i:s') < $schedule->start_time || date('H:i:s') > $schedule->end_time) {
            return response()->json([
                'success' => false,
                'message' => 'Cabang sedang tutup hari ini'
            ]);
        }
        
        $service_order_no = Service::where('branch_id', $service->branch_id)
            ->where('id', '<=', $request->service_id)
            ->count();
        $last_onsite_queue = DirectQueue::where('service_id', $request->service_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->orderBy('queue_no', 'desc')
            ->first();

        if ($last_onsite_queue) {
            $queue_no = (int) $last_onsite_queue->queue_no + 1;
        } else {
            $queue_no = $service_order_no . sprintf('%03s', 1);
        }

        $input = $request->all();
        $input['queue_no'] = $queue_no;
        $input['service_id'] = $request->service_id;
        $input['direct_queue_channel'] = 'Mobile Apps';
        $workstation = DirectQueue::create($input);

        // send event to update Direct Queue Monitor
        event(new VCTDirectQueueEvent($workstation));
        event(new DirectQueueEvent($workstation, $service->branch_id));

        return response()->json([
            'success' => true,
            'message' => 'direct queue created',
            'data' => $workstation
        ]);
    }

    public function upcoming()
    {
        $directQueues = DirectQueue::whereUserId(Auth::id())->whereIn('status', ['waiting', 'served', 'requeue'])->whereDate('created_at', '>=', date('Y-m-d'))->orderBy('created_at', 'asc')->get();
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
