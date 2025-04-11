<?php
namespace App\Helpers;
use App\Branch;
use App\Models\TVConfiguration;

class TVImageHelper{
    public static function fetchImages(Branch $branch, TVConfiguration $tv_config){
        $tv_images = [];
        $images = ['image_1','image_2','image_3'];

        foreach ($images as $key => $image) {
            $value = $tv_config->$image;

            if($value){
                $tv_images[] = [
                    'name' => (filter_var($value,FILTER_VALIDATE_URL) ? 'Link' : 'Image') . ($key + 1),
                    'url' => filter_var($value, FILTER_VALIDATE_URL) ? $value : "/storage/$value"
                ];
            }
        }

        if (in_array($branch->BranchConfiguration->template_signage, ['custom-layout-2', 'custom-layout-3'])) {
            for ($i = 4; $i <= 6; $i++) {
                $img = $tv_config->{'image_' . $i};
                if ($img) {
                    $tv_images[] = [
                        'name' => "Image $i",
                        'url' => "/storage/$img"
                    ];
                }
            }
        }

        return $tv_images;

    }
}