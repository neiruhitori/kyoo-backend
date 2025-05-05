<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Branch;
use Carbon\Carbon;
use App\Appointment;
use App\DirectQueue;
use App\Workstation;
use App\Models\TVToken;
use App\Helpers\TVImageHelper;
use App\Models\FeatureSubscription;
use App\Http\Controllers\Controller;

class SignageController extends Controller
{
    public function layout()
    {
        $user = auth()->user();
        $branch = $user->branch()->with([
            'BranchConfiguration',
            'BranchType',
            'Departments',
            'TVConfiguration.customLayoutConfiguration2'
        ])->firstOrFail();

        $isAppt = $branch->BranchType->is_appointment;
        $custom_layout_config = null;

        $branchConfig = collect($branch->BranchConfiguration)->only([
            'signage_vo_format',
            'vo_call_style',
            'queue_voice'
        ]);

        $features = FeatureSubscription::with('AdditionalFeature')
        ->where('branch_id', $branch->id)
        ->get();

        $workstations = Workstation::whereIn('department_id',
                         $branch->Departments->pluck('id')->toArray(),)
        ->take(6)
        ->orderBy('label')
        ->get();

        $TVConfiguration = $branch->TVConfiguration;
       
        if(!$TVConfiguration){
            return response()->json([
                'success' => false,
                'message' => 'TV Configuration not found'
            ], 404);
        }

        if($isAppt){
            return response()->json([
                'success' => true,
                'data' => [
                    'layout' => $branch->BranchConfiguration->template_signage,
                    'logo' => $branch->logo ? "/storage/$branch->logo" : null,
                    'display_duration' => (int) $TVConfiguration->display_duration * 1000,
                    'custom_layout_config' => $custom_layout_config,
                    'features' => $features,
                    'branch_configuration' => $branchConfig,
                    'workstation' => $workstations,
                    'is_direct_queue' => $branch->BranchType->is_direct_queue,
                    'is_appointment' => $branch->BranchType->is_appointment,
                ]
            ]);
        }

        if (!$TVConfiguration->customLayoutConfiguration2) {
            return response()->json([
                'success' => false,
                'message' => 'Custom Layout Configuration not found'
            ], 404);
        }

        $custom_layout_config = $TVConfiguration->customLayoutConfiguration2;
        $custom_layout_config->background_image = "/storage/$custom_layout_config->background_image";
        

        return response()->json([
            'success' => true,
            'data' => [
                'layout' => $branch->BranchConfiguration->template_signage,
                'logo' => $branch->logo ? "/storage/$branch->logo" : null,
                'display_duration' => (int) $TVConfiguration->display_duration * 1000,
                'custom_layout_config' => $custom_layout_config,
                'features' => $features,
                'branch_configuration' => $branchConfig,
                'workstation' => $workstations,
                'is_direct_queue' => $branch->BranchType->is_direct_queue,
                'is_appointment' => $branch->BranchType->is_appointment,
            ]
        ]);
    }

    public function getMedia()
    {
        $branch = auth()->user()->branch;
        $tv_config = $branch->TVConfiguration;

        if(!$branch){
            return response()->json([
                'success' => false,
                'message' => 'Branch not found'
            ], 404);
        }

        $tv_images = TVImageHelper::fetchImages($branch,$tv_config);
        return response()->json([
            'success' => true,
            'data' => $tv_images
        ]);
    }
    public function getQueues(){
        $branch = auth()->user()->branch;
        $branch_id = $branch->id;

        if($branch->BranchType->is_appointment){
            $data = Appointment::with(['Service', 'Workstation'])
                ->withoutCanceled()
                ->whereHas('Service', function ($query) use ($branch_id) {
                    $query->where('branch_id', $branch_id);
                })
                ->whereHas('Service.WorkstationService')
                ->where('date', date('Y-m-d'))
                ->whereNotIn('status', ['end served', 'no show'])
                ->orderBy('created_at')
                ->get();
        }else{
            $data = DirectQueue::whereHas('Service', function ($query) use ($branch_id) {
                return $query->where('branch_id', $branch_id);
            })
                ->whereDate('created_at', date('Y-m-d'))
                ->whereNotIn('status', ['end served', 'no show'])
                ->orderBy('queue_no')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' =>  $data
        ]);
    }
}
