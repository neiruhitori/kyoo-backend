<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DirectQueue;
use App\Branch;
use App\WorkstationService;

use App\Http\Resources\DirectQueue\All as DirectQueueAll;

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
}
