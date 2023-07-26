<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DirectQueue;
use App\Branch;
use Crypt;
use App\Models\FeatureSubscription;

class DirectQueueController extends Controller
{

    public function initQuery()
    {
        return DirectQueue::query()->join('workstation_services', 'workstation_services.id', '=', 'direct_queues.workstation_service_id')
            ->with(['WorkstationService.Service', 'WorkstationService.Workstation'])
            ->whereDate('direct_queues.created_at', Date('Y-m-d'))
            ->whereNotIn('status', ['end served', 'no show'])
            ->orderBy('workstation_services.priority', 'DESC')->orderBy('direct_queues.queue_no', 'ASC');
    }

    public function monitor(Request $request, $branch_id)
    {
        $branch = Branch::with(['BranchConfiguration'])->findOrFail(Crypt::decrypt($branch_id));
        $features = FeatureSubscription::with('AdditionalFeature')->where('branch_id', Crypt::decrypt($branch_id))->get();

        return view('directQueue.monitor', [
            'branch' => $branch,
            'branchIdEncrypted' => $branch_id,
            'features' => $features
        ]);
    }

    public function branchList($branch_id)
    {
        $branch_id = Crypt::decrypt($branch_id);

        // OLD CODE
        // $queues = $this->initQuery()->whereHas('WorkstationService.Service', function($query) use ($branch_id){
        //     return $query->whereBranchId($branch_id);
        // });

        $queues = DirectQueue::whereHas('Service', function ($query) use ($branch_id) {
            return $query->where('branch_id', $branch_id);
        })
            ->whereDate('created_at', date('Y-m-d'))
            ->whereNotIn('status', ['end served', 'no show'])
            ->orderBy('queue_no')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'get queues by branch id',
            'data' => $queues
        ]);
    }
}
