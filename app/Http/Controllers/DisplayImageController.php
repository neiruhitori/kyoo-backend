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

            //jika link
            if($tv_config->image_1 && filter_var($tv_config->image_1, FILTER_VALIDATE_URL)){
                if ($tv_config->image_1) {
                    array_push($tv_images, [
                        'name' => 'Link 1',
                        'url' => $tv_config->image_1
                    ]);
                }
    
                if ($tv_config->image_2) {
                    array_push($tv_images, [
                        'name' => 'Link 2',
                        'url' => $tv_config->image_2
                    ]);
                }
    
                if ($tv_config->image_3) {
                    array_push($tv_images, [
                        'name' => 'Link 3',
                        'url' => $tv_config->image_3
                    ]);
                }
            }else{
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
