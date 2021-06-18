<?php

namespace App\Http\Controllers\CS;

use App\DirectQueue;
use App\Service;
use App\WorkstationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CS\StoreDirectQueue;
use Auth;
use App\Events\VCTDirectQueue as VCTDirectQueueEvent;
use App\Events\DirectQueue as DirectQueueEvent;

class DirectQueueController extends Controller
{
    
    private function InitQuery()
    {
        return DirectQueue::query()->join('workstation_services', 'workstation_services.id', '=', 'direct_queues.workstation_service_id')
                    ->with(['WorkstationService.Service'])
                    ->where(function($query){
                        $query->where('vct_id', Auth::id())->orWhere('vct_id', null);
                    })
                    ->whereDate('direct_queues.created_at', Date('Y-m-d'))
                    ->whereNotIn('status', ['end served', 'no show'])
                    ->orderBy('workstation_services.priority', 'DESC')
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

        return response()->json([
            'success' => true,
            'message' => 'get all direct queues by today',
            'data' => $directQueues->get()
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
        $workstationService = WorkstationService::find($request->workstation_service_id);
        $serviceOrderNumber = Service::where('branch_id', $workstationService->Service->branch_id)->where('id', '<=', $workstationService->service_id)->count();
        $lastDirectQueue = DirectQueue::where('vct_id', Auth::id())->where('workstation_service_id', $workstationService->id)->whereDate('created_at', Date('Y-m-d'))->count();

        if ($lastDirectQueue > 0) {
            $lastDirectQueue = DirectQueue::where('vct_id', Auth::id())->where('workstation_service_id', $request->workstation_service_id)->whereDate('created_at', Date('Y-m-d'))->orderBy('queue_no', 'desc')->first();
            $queueNo = (int) $lastDirectQueue->queue_no + 1;
        }else{
            $queueNo = $serviceOrderNumber . sprintf('%03s', ++$lastDirectQueue);
        }

        $input = $request->all();
        $input['queue_no'] = $queueNo;
        $directQueue = DirectQueue::create($input);

        // send event to update Direct Queue Monitor
        event(new VCTDirectQueueEvent($directQueue));
        event(new DirectQueueEvent($directQueue));

        $request->session()->flash('success', "Direct Queue Has Been Created, Queue no: {$directQueue->queue_no}");
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

    private function checkPreviousQueue_old($queueNo, $isSkip = false)
    {
        $queues = $this->InitQuery()->get()->toArray();
        $arrayIndex = array_search($queueNo, array_column($queues, 'queue_no'));
        if ($arrayIndex > 0) {
            if (!$isSkip) {
                if ($queues[$arrayIndex - 1]['status'] != 'end served' && $queues[$arrayIndex - 1]['status'] != 'no show') {
                    return false;
                }
            } else {
                if ($queues[$arrayIndex - 1]['status'] == 'served') {
                    return false;
                }
            }
            
        }
        return true;
    }

    private function checkPreviousQueue($directQueue, $isSkip = false)
    {
        $query = DirectQueue::query()
                                ->where('vct_id', Auth::id())
                                ->where('workstation_service_id', $directQueue->workstation_service_id)
                                ->whereDate('created_at', Date('Y-m-d'));

        if ($isSkip) {
            $queues = $query->whereStatus('served')->where('id', '!=', $directQueue->id);
        } else {
            $queues = $query->whereNotIn('status', ['end served', 'no show'])->where('id', '<', $directQueue->id);
        }
        
        return $queues->exists();
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
            $directQueue->done_at = Date('Y-m-d H:m:s');
            $directQueue->save();
            return response()->json([
                'success' => false,
                'message' => 'Queue recall has on limited',
                'data' => null
            ], 400);
        }
        $directQueue->status = 'served';
        $directQueue->recall_count = $directQueue->recall_count > 0 ? $directQueue->recall_count + 1 : 0;
        $directQueue->called_at = Date('Y-m-d H:m:s');
        $directQueue->save();

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
            $directQueue->done_at = Date('Y-m-d H:m:s');
            $directQueue->save();
            return response()->json([
                'success' => false,
                'message' => 'Queue recall has on limited',
                'data' => $directQueue
            ], 400);
        }

        $directQueue->status = $directQueue->recall_count + 1 >= Auth::user()->Branch->BranchConfiguration->maximum_recall ? 'no show' : 'served';
        $directQueue->recall_count = $directQueue->recall_count + 1;
        $directQueue->called_at = Date('Y-m-d H:m:s');
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
        $directQueue->done_at = Date('Y-m-d H:m:s');
        $directQueue->save();

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Served',
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
        $directQueue->done_at = Date('Y-m-d H:m:s');
        $directQueue->save();

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Served',
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

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Transfer',
            'data' => $directQueue
        ]);
    }
}
