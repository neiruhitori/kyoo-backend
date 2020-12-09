<?php

namespace App\Http\Controllers\CS;

use App\DirectQueue;
use App\Service;
use App\WorkstationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CS\StoreDirectQueue;
use Auth;

class DirectQueueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
}
