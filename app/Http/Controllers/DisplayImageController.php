<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use App\Models\TVConfiguration;

class DisplayImageController extends Controller
{
    public function show(Branch $branch, Request $request)
    {
        $tv_config = TVConfiguration::where('branch_id', $branch->id)->first();

        if ($tv_config) {
            return response()->json([
                [
                    'name' => 'Image 1',
                    'url' => "/storage/$tv_config->image_1"
                ],
                [
                    'name' => 'Image 2',
                    'url' => "/storage/$tv_config->image_2"
                ],
                [
                    'name' => 'Image 3',
                    'url' => "/storage/$tv_config->image_3"
                ],
            ]);
        }

        return [];
    }
}
