<?php

namespace App\Http\Controllers\CS;

use App\DirectQueue;
use App\Service;
use App\WorkstationService;
use App\WorkstationVct;
use App\Schedule;
use App\ScheduleTemplateDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CS\StoreDirectQueue;
use Auth;
use App\Events\VCTDirectQueue as VCTDirectQueueEvent;
use App\Events\DirectQueue as DirectQueueEvent;
use App\Events\QueueStatusUpdated;

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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $directQueues = $this->InitQuery();

        $directQueues->when($request->keyword, function($query) use ($request){
            return $query->where(function($query) use ($request){
                return $query->where('name', 'ilike', '%'.$request->keyword.'%')->orWhere('queue_no', (int) $request->keyword);
            });
        });

        $data = $directQueues->get()->filter(function($directQueue){
            return $directQueue->vct_priority;
        });

        return response()->json([
            'success' => true,
            'message' => 'get all direct queues by today',
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $workstationServices = WorkstationService::whereWorkstationId(Auth::user()->WorkstationVct->workstation_id)->get();
        return view('cs.directQueue.create')->withServices($workstationServices);
    }

    public function workstationServices()
    {
        $workstationServices = WorkstationService::whereHas('Workstation.WorkstationVct', function($query){
            return $query->whereVctId(Auth::id());
        })->with('Service')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'get all workstation service',
            'data' => $workstationServices
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDirectQueue $request)
    {
        $branch = WorkstationService::find($request->workstation_service_id)->Service->Branch;

        $total_current_booking = DirectQueue::whereHas('Service', function ($query) use ($branch) {
            return $query->where('branch_id', $branch->id);
        })
            ->whereDate('created_at', date('Y-m-d'))
            ->count();
        
        if (!$branch->BranchType->is_premium && $total_current_booking >= 200) {
            $request->session()->flash('error', "Jumlah antrian melebihi batas maksimal harian untuk cabang berlisensi gratis");
            return redirect(route('cs.directQueue.create'));
        }

        /**
         * additional validations:
         * - user cant create same direct queue 3x at same date
         * - user cant create direct queue on closed day with schedule template
         * - user cant create direct queue on closed day
         */
        
        $current_date = date('Y-m-d');
        $current_time = date('H:i');
        $workstationService = WorkstationService::find($request->workstation_service_id);

        // user cant create same direct queue 3x at same date
        if ($request->name || $request->phone) {
            $sameUserQueueCount = DirectQueue::query()
                                            ->whereHas('WorkstationService.Service', function($query) use($workstationService){
                                                return $query->where('branch_id', $workstationService->Service->branch_id);
                                            })
                                            ->where('name', $request->name)
                                            ->where('phone', $request->phone)
                                            ->whereDate('created_at', $current_date)
                                            ->count();

            if ($sameUserQueueCount > 3) {
                $request->session()->flash('error', "Only max 3 direct queue number request at the same date");
                return redirect(route('cs.directQueue.create'));
            }
        }

        // cant create direct queue on closed day by schedule template
        if($workstationService->Service->Branch->schedule_template_id){
            $schedule_template_details = ScheduleTemplateDetail::query()
                                                                    ->where('schedule_template_id', $workstationService->Service->Branch->schedule_template_id)
                                                                    ->where('date', $current_date)
                                                                    ->first();
            if($schedule_template_details){
                $request->session()->flash('error', "Service Provider Already Closed");
                return redirect(route('cs.directQueue.create'));
            }
        }

        // cant create direct queue on closed day
        $selectedSchedule = Schedule::query()
                                ->where('branch_id', $workstationService->Service->branch_id)
                                ->where('day', strtolower(date('l', strtotime($current_date))))
                                ->get(['day', 'status', 'start_time', 'end_time'])
                                ->first();

        if (!$selectedSchedule || $selectedSchedule->status == 'closed') {
            $request->session()->flash('error', "Service Provider Already Closed");
            return redirect(route('cs.directQueue.create'));
        }

        // cant create direct queue before open time and after closed time
        if ($current_time < $selectedSchedule->start_time || $current_time > $selectedSchedule->end_time) {
            $request->session()->flash('error', "Service Provider Already Closed");
            return redirect(route('cs.directQueue.create'));
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
        $input['service_id'] = $workstationService->service_id;
        $input['workstation_id'] = $workstationService->workstation_id;
        $input['user_id'] = Auth::id();
        $input['direct_queue_channel'] = 'Web';
        $directQueue = DirectQueue::create($input);

        // send event to update Direct Queue Monitor
        event(new VCTDirectQueueEvent($directQueue));
        event(new DirectQueueEvent($directQueue));

        $request->session()->flash('success', __('Direct Queue Has Been Created, Queue no: :no', ['no' => $directQueue->queue_no]));
        return redirect(route('cs.directQueue.create'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DirectQueue  $directQueue
     * @return \Illuminate\Http\Response
     */
    public function show(DirectQueue $directQueue)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DirectQueue  $directQueue
     * @return \Illuminate\Http\Response
     */
    public function edit(DirectQueue $directQueue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DirectQueue  $directQueue
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DirectQueue $directQueue)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DirectQueue  $directQueue
     * @return \Illuminate\Http\Response
     */
    public function destroy(DirectQueue $directQueue)
    {
        //
    }

    public function monitor()
    {
        return view('cs.directQueue.monitor');
    }

    private function checkPreviousQueue($directQueue, $isSkip = false)
    {
        $query = $this->InitQuery();

        if ($directQueue->status == 'requeue' && !$isSkip) {
            $queues = $query->whereIn('status', ['served', 'waiting'])->where('id', '!=', $directQueue->id)->exists();
        }else if ($isSkip) {
            $queues = $query->whereStatus('served')->where('id', '!=', $directQueue->id)->exists();
        } else {
            $query->whereNotIn('status', ['end served', 'no show', 'requeue']);
            $queues = $query->get()->filter(function($item){
                return $item->vct_priority;
            });
            $queues = $queues[0] ? $queues[0]->queue_no != $directQueue->queue_no : false;
        }
        
        return $queues;
    }

    public function onServed(Request $request)
    {
        $rules = [
            'queue_no' => 'required|integer|min:1|exists:direct_queues',
            'is_skip' => 'nullable|boolean'
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validation',
                'data' => $validation->errors()
            ], 400);
        }

        // check the queue no with created date is today
        $directQueue = DirectQueue::where('queue_no' ,$request->queue_no)->whereNotIn('status', ['no show', 'end served'])->whereDate('created_at', Date('Y-m-d'))->first();
        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => null
            ], 404);
        }

        // check queue can called if previous queue end served
        if ($this->checkPreviousQueue($directQueue, $request->is_skip)) {
            return response()->json([
                'success' => false,
                'message' => 'Previous queue not finished',
                'data' => null
            ], 400);
        }

        // check if queue recall_count on limit
        if ($directQueue->recall_count >= Auth::user()->Branch->BranchConfiguration->maximum_recall) {
            $directQueue->vct_id = Auth::id();
            $directQueue->status = 'no show';
            $directQueue->done_at = Date('Y-m-d H:i:s');
            $directQueue->save();

            return response()->json([
                'success' => false,
                'message' => 'Queue recall has on limited',
                'data' => null
            ], 400);
        }

        $workstation_service = Auth::user()
            ->WorkstationVct
            ->Workstation
            ->WorkstationService()
            ->where('service_id', $directQueue->service_id)
            ->first();

        $directQueue->workstation_service_id = $workstation_service->id;
        $directQueue->workstation_id = Auth::user()->WorkstationVct->workstation_id;
        $directQueue->status = 'served';
        $directQueue->recall_count = $directQueue->recall_count > 0 ? $directQueue->recall_count + 1 : 0;
        $directQueue->called_at = Date('Y-m-d H:i:s');
        $directQueue->save();

        event(new QueueStatusUpdated([
            'queue_no' => $directQueue->queue_no,
            'status' => 'served'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Served',
            'data' => $directQueue
        ]);
    }

    public function onRecall(Request $request)
    {
        $rules = [
            'queue_no' => 'required|integer|min:1|exists:direct_queues'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validation',
                'data' => $validation->errors()
            ], 400);
        }

        $directQueue = DirectQueue::where('queue_no' ,$request->queue_no)->whereDate('created_at', Date('Y-m-d'))->first();
        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => $validation->errors()
            ], 404);
        }
        // check if queue recall_count on limit
        if ($directQueue->recall_count >= Auth::user()->Branch->BranchConfiguration->maximum_recall) {
            $directQueue->status = 'no show';
            $directQueue->done_at = Date('Y-m-d H:i:s');
            $directQueue->save();
            return response()->json([
                'success' => false,
                'message' => 'Queue recall has on limited',
                'data' => $directQueue
            ], 400);
        }

        $directQueue->status = $directQueue->recall_count + 1 >= Auth::user()->Branch->BranchConfiguration->maximum_recall ? 'no show' : 'served';
        $directQueue->recall_count = $directQueue->recall_count + 1;
        $directQueue->called_at = Date('Y-m-d H:i:s');
        $directQueue->save();

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Served',
            'data' => $directQueue
        ]);
    }

    public function onRequeue(Request $request)
    {
        $rules = [
            'queue_no' => 'required|integer|min:1|exists:direct_queues,queue_no'
        ];
        
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validation',
                'data' => $validation->errors()
            ], 400);
        }

        $directQueue = DirectQueue::where('queue_no' ,$request->queue_no)->whereDate('created_at', Date('Y-m-d'))->first();
        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => $validation->errors()
            ], 404);
        }

        // check if queue requeue_count on limit
        if ($directQueue->requeue_count >= Auth::user()->Branch->BranchConfiguration->maximum_requeue_count) {
            return response()->json([
                'success' => false,
                'message' => 'Queue requeue has on limited',
                'data' => $directQueue
            ], 400);
        }

        $directQueue->status = 'requeue';
        $directQueue->requeue_count = $directQueue->requeue_count + 1;
        $directQueue->recall_count = 0;
        $directQueue->called_at = Date('Y-m-d H:m:i');

        /**
         * disabled this flow
         */
        // $lastQueue = DirectQueue::where('vct_id', Auth::id())->where('workstation_service_id', $directQueue->workstation_service_id)->whereDate('created_at', Date('Y-m-d'))->orderBy('queue_no', 'desc')->first();
        // $directQueue['queue_no'] = (int) $lastQueue->queue_no + 1;

        $directQueue->save();

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Served',
            'data' => $directQueue
        ]);
    }

    public function onEndServed(Request $request)
    {
        $rules = [
            'queue_no' => 'required|integer|min:1|exists:direct_queues'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validation',
                'data' => $validation->errors()
            ], 400);
        }

        $directQueue = DirectQueue::where('queue_no' ,$request->queue_no)->whereDate('created_at', Date('Y-m-d'))->first();
        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => $validation->errors()
            ], 404);
        }
        $directQueue->status = 'end served';
        $directQueue->done_at = Date('Y-m-d H:i:s');
        $directQueue->save();

        event(new QueueStatusUpdated([
            'queue_no' => $directQueue->queue_no,
            'status' => 'end served'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on End Served',
            'data' => $directQueue
        ]);
    }

    public function onNoShow(Request $request)
    {
        $rules = [
            'queue_no' => 'required|integer|min:1|exists:direct_queues'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validation',
                'data' => $validation->errors()
            ], 400);
        }

        $directQueue = DirectQueue::where('queue_no' ,$request->queue_no)->whereDate('created_at', Date('Y-m-d'))->first();
        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => $validation->errors()
            ], 404);
        }
        $directQueue->status = 'no show';
        $directQueue->done_at = Date('Y-m-d H:i:s');
        $directQueue->save();

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on No Show',
            'data' => $directQueue
        ]);
    }

    public function onTransfer(Request $request)
    {
        $rules = [
            'queue_no' => 'required|integer|min:1|exists:direct_queues',
            'workstation_service_id' => 'required|integer|min:1|exists:workstation_services,id'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validation',
                'data' => $validation->errors()
            ], 400);
        }

        $directQueue = DirectQueue::where('queue_no' ,$request->queue_no)->whereDate('created_at', Date('Y-m-d'))->first();
        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => $validation->errors()
            ], 404);
                }

        $lastDirectQueue = DirectQueue::whereWorkstationServiceId($request->workstation_service_id)->whereDate('created_at', Date('Y-m-d'))->count();
        $workstationService = WorkstationService::find($request->workstation_service_id);
        $queue_no = $workstationService->service_id . sprintf('%03s', $lastDirectQueue + 1);
        $directQueue->queue_no = $queue_no;
        $directQueue->workstation_service_id = $request->workstation_service_id;
        $directQueue->status = 'waiting';
        $directQueue->save();

        event(new QueueStatusUpdated([
            'queue_no' => $directQueue->queue_no,
            'status' => 'waiting'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Transfer',
            'data' => $directQueue
        ]);
    }
}
