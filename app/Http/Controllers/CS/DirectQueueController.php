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
                    ->where('vct_id', Auth::id())->whereDate('direct_queues.created_at', Date('Y-m-d'))
                    ->whereNotIn('status', ['done', 'unattend'])
                    ->orderBy('workstation_services.priority', 'DESC')->orderBy('direct_queues.queue_no', 'ASC');
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
        $lastDirectQueue = DirectQueue::whereWorkstationServiceId($workstationService->id)->whereDate('created_at', Date('Y-m-d'))->count();

        $input = $request->all();
        $input['queue_no'] = $workstationService->service_id . sprintf('%04s', $lastDirectQueue + 1);
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

    private function checkPreviousQueue($queueNo)
    {
        $queues = $this->InitQuery()->get()->toArray();
        $arrayIndex = array_search($queueNo, array_column($queues, 'queue_no'));
        if ($arrayIndex > 0) {
            if ($queues[$arrayIndex - 1]['status'] != 'done' && $queues[$arrayIndex - 1]['status'] != 'unattend') {
                return false;
            }
        }
        return true;
    }

    public function onCall(Request $request)
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

        // check the queue no with created date is today
        $directQueue = DirectQueue::where('queue_no' ,$request->queue_no)->whereNotIn('status', ['unattend', 'done'])->whereDate('created_at', Date('Y-m-d'))->first();
        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => null
            ], 404);
        }

        // check queue can called if previous queue done
        if (!$this->checkPreviousQueue($request->queue_no)) {
            return response()->json([
                'success' => false,
                'message' => 'Previous queue not finished',
                'data' => null
            ], 400);
        }

        // check if queue recall_count on limit
        if ($directQueue->recall_count >= Auth::user()->Branch->BranchConfiguration->maximum_recall) {
            $directQueue->status = 'unattend';
            $directQueue->done_at = Date('Y-m-d H:m:s');
            $directQueue->save();
            return response()->json([
                'success' => false,
                'message' => 'Queue recall has on limited',
                'data' => null
            ], 400);
        }
        $directQueue->status = 'call';
        $directQueue->recall_count = $directQueue->recall_count > 0 ? $directQueue->recall_count + 1 : 0;
        $directQueue->called_at = Date('Y-m-d H:m:s');
        $directQueue->save();

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Call',
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
            $directQueue->status = 'unattend';
            $directQueue->done_at = Date('Y-m-d H:m:s');
            $directQueue->save();
            return response()->json([
                'success' => false,
                'message' => 'Queue recall has on limited',
                'data' => $directQueue
            ], 400);
        }

        // check queue can called if previous queue done
        if (!$this->checkPreviousQueue($request->queue_no)) {
            return response()->json([
                'success' => false,
                'message' => 'Previous queue not finished',
                'data' => null
            ], 400);
        }

        $directQueue->status = $directQueue->recall_count + 1 >= Auth::user()->Branch->BranchConfiguration->maximum_recall ? 'unattend' : 'call';
        $directQueue->recall_count = $directQueue->recall_count + 1;
        $directQueue->called_at = Date('Y-m-d H:m:s');
        $directQueue->save();

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Call',
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
            $directQueue->status = 'unattend';
            $directQueue->done_at = Date('Y-m-d H:m:s');
            $directQueue->save();
            return response()->json([
                'success' => false,
                'message' => 'Queue requeue has on limited',
                'data' => $directQueue
            ], 400);
        }
        $directQueue->status = $directQueue->requeue_count + 1 >= Auth::user()->Branch->BranchConfiguration->maximum_requeue_count ? 'unattend' : 'requeue';
        $directQueue->requeue_count = $directQueue->requeue_count + 1;
        $directQueue->recall_count = 0;
        $directQueue->called_at = Date('Y-m-d H:m:s');
        $lastQueue = DirectQueue::where('vct_id', Auth::id())->where('workstation_service_id', $directQueue->workstation_service_id)->whereDate('created_at', Date('Y-m-d'))->orderBy('queue_no', 'desc')->first();
        $directQueue['queue_no'] = (int) $lastQueue->queue_no + 1;
        $directQueue->save();

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Call',
            'data' => $directQueue
        ]);
    }

    public function onDone(Request $request)
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
        $directQueue->status = 'done';
        $directQueue->done_at = Date('Y-m-d H:m:s');
        $directQueue->save();

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Call',
            'data' => $directQueue
        ]);
    }

    public function onUnattend(Request $request)
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
        $directQueue->status = 'unattend';
        $directQueue->done_at = Date('Y-m-d H:m:s');
        $directQueue->save();

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Call',
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
        $queue_no = $workstationService->service_id . sprintf('%04s', $lastDirectQueue + 1);
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
