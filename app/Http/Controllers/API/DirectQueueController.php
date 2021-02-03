<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DirectQueue;
use App\Branch;
use App\WorkstationService;

use App\Http\Requests\API\DirectQueue\Store as DirectQueueStore;
use App\Http\Resources\DirectQueue\All as DirectQueueAll;
use App\Http\Resources\DirectQueue\Detail as DirectQueueDetail;
use Auth;

class DirectQueueController extends Controller
{
    public function index(Branch $branch)
    {
        $workstationServices = WorkstationService::with('Service')->whereHas('Service', function($query) use ($branch){
            $query->whereBranchId($branch->id);
        })->get();

        return response()->json([
            'success' => true,
            'message' => 'get all direct queue list by branch_id',
            'data' => DirectQueueAll::collection($workstationServices)
        ]);
    }

    public function store(DirectQueueStore $request)
    {
        $input = $request->all();

        $workstationService = WorkstationService::find($request->workstation_service_id);
        $lastDirectQueue = DirectQueue::whereWorkstationServiceId($workstationService->id)->whereDate('created_at', Date('Y-m-d'))->count();
        $input['queue_no'] = $workstationService->service_id . sprintf('%04s', $lastDirectQueue + 1);
        $input['user_id'] = Auth::id();
        $input['direct_queue_channel'] = 'Mobile Apps';

        $workstation = DirectQueue::create($input);
        $workstation['total_waiting'] = DirectQueue::whereWorkstationServiceId($workstation->workstation_service_id)->whereStatus('waiting')->where('queue_no', '<' , $workstation->queue_no)->count();

        return response()->json([
            'success' => true,
            'message' => 'direct queue created',
            'data' => $workstation
        ]);
    }

    public function upcomming()
    {
        $directQueues = DirectQueue::whereUserId(Auth::id())->whereStatus('waiting')->get();
        return response()->json([
            'success' => true,
            'message' => 'get upcomming direct queues',
            'data' => DirectQueueDetail::collection($directQueues)
        ]);
    }
}
