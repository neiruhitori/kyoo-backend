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
            $tv_images = [];

            if ($tv_config->image_1) {
                array_push($tv_images, [
                    'name' => 'Image 1',
                    'url' => "/storage/$tv_config->image_1"
                ]);
            }

            if ($tv_config->image_2) {
                array_push($tv_images, [
                    'name' => 'Image 2',
                    'url' => "/storage/$tv_config->image_2"
                ]);
            }

            if ($tv_config->image_3) {
                array_push($tv_images, [
                    'name' => 'Image 3',
                    'url' => "/storage/$tv_config->image_3"
                ]);
            }

            if (
                $branch->BranchConfiguration->template_signage === 'custom-layout-2' ||
                $branch->BranchConfiguration->template_signage === 'custom-layout-3'
               ) {
                    if ($tv_config->image_4) {
                        array_push($tv_images, [
                            'name' => 'Image 4',
                            'url' => "/storage/$tv_config->image_4"
                        ]);
                    }

                    if ($tv_config->image_5) {
                        array_push($tv_images, [
                            'name' => 'Image 5',
                            'url' => "/storage/$tv_config->image_5"
                        ]);
                    }

                    if ($tv_config->image_6) {
                        array_push($tv_images, [
                            'name' => 'Image 6',
                            'url' => "/storage/$tv_config->image_6"
                        ]);
                    }
                }

            return response()->json($tv_images);
        }

        return [];
    }
}
