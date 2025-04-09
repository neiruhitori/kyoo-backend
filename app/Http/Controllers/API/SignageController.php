<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Branch;
use Carbon\Carbon;
use App\Models\TVToken;
use App\Http\Controllers\Controller;
use App\Helpers\TVImageHelper;

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

        $TVConfiguration = $branch->TVConfiguration;
       
        if(!$TVConfiguration){
            return response()->json([
                'success' => false,
                'message' => 'TV Configuration not found'
            ], 404);
        }

        if (!$TVConfiguration->customLayoutConfiguration2) {
            return response()->json([
                'success' => false,
                'message' => 'Custom Layout Configuration not found'
            ], 404);
        }
        

        return response()->json([
            'success' => true,
            'data' => [
                'layout' => $branch->BranchConfiguration->template_signage,
                'display_duration' => (int) $TVConfiguration->display_duration * 1000,
                'custom_layout_config' => $TVConfiguration->customLayoutConfiguration2,
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
}
