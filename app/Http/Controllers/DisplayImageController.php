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
            
            $images = ['image_1', 'image_2', 'image_3'];
    
            foreach ($images as $key => $image) {
                $value = $tv_config->$image; 
    
                if ($value) {
                    if (filter_var($value, FILTER_VALIDATE_URL)) {
                       
                        array_push($tv_images, [
                            'name' => 'Link ' . ($key + 1),
                            'url' => $value
                        ]);
                    } else {
                        
                        array_push($tv_images, [
                            'name' => 'Image ' . ($key + 1),
                            'url' => "/storage/$value"
                        ]);
                    }
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
