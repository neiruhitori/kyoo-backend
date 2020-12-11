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

class DirectQueueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $directQueues = DirectQueue::query()->with('Service')->whereDate('created_at', Date('Y-m-d'));
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDirectQueue $request)
    {
        $lastDirectQueue = DirectQueue::whereDate('created_at', Date('Y-m-d'))->latest('created_at')->first();
        $workstationService = WorkstationService::find($request->workstation_service_id);
        $input = $request->all();
        $input['queue_no'] = $lastDirectQueue ? $lastDirectQueue->queue_no + 1 : 1;
        $input['workstation_id'] = $workstationService->workstation_id;
        $input['service_id'] = $workstationService->service_id;
        $directQueue = DirectQueue::create($input);
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

        $directQueue = DirectQueue::where('queue_no' ,$request->queue_no)->whereDate('created_at', Date('Y-m-d'))->first();
        if (!$directQueue) {
            return response()->json([
                'success' => false,
                'message' => 'Queue not found',
                'data' => $validation->errors()
            ], 404);
        }
        $directQueue->status = 'call';
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
        $directQueue->status = 'call';
        $directQueue->called_at = Date('Y-m-d H:m:s');
        $directQueue->recall_count = $directQueue->recall_count + 1;
        $directQueue->save();

        return response()->json([
            'success' => true,
            'message' => 'Direct Queue on Call',
            'data' => $directQueue
        ]);
    }
}
