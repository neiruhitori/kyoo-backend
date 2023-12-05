<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Interfaces\TVConfigurationRepositoryInterface;
use App\Models\TVConfiguration;
use App\Models\WebkioskConfiguration;
use App\Models\WebkioskLayout2Configuration;
use App\Models\WebkioskLayout3Configuration;
use Illuminate\Support\Facades\Storage;

class TVConfigurationRepository implements TVConfigurationRepositoryInterface
{
    public function GetOneConfigurationByBranchID($branchID) {
        return TVConfiguration::where('branch_id', $branchID)
            ->with('customLayoutConfiguration2')
            ->first();
    }

    public function Upsert($branchID, Request $request) {
        return DB::transaction(function () use ($branchID, $request){
            $STORAGE_FOLDER = 'tv-backgrond-images';
            $DEFAULT_IMAGE = 'img-placeholder.jpg';

            $newWebkioskConfiguration = [
                'branch_id' => $branchID,
                'layout_id' => $request->layout,
            ];

            $webkiosConfiguration = WebkioskConfiguration::firstOrNew(['branch_id' => $branchID]);
            $webkiosConfiguration->fill($newWebkioskConfiguration);
            $webkiosConfiguration->save();

            switch ($request->layout) {
                case 'custom-layout-2':
                        $newWebkioskLayout3Configuration = [
                            'webkios_configuration_id' => $webkiosConfiguration->id,
                            'primary_background_type' => $request->primary_background_type,
                            'primary_background_color' => $request->primary_background_color,
                            'secondary_background_type' => $request->secondary_background_type,
                            'secondary_background_color' => $request->secondary_background_color,
                            'button_background_color' => $request->button_background_color,
                            'botton_border_color' => $request->botton_border_color,
                            'font_color' => $request->font_color,
                        ];

                        $webkioskLayout3Configuration = WebkioskLayout3Configuration::firstOrNew(['webkios_configuration_id' => $webkiosConfiguration->id]);

                        if($request->primary_background_type == 'image' && $request->primary_background_image) {
                            $newWebkioskLayout2Configuration["primary_background_image"] = Storage::disk('public')->put($STORAGE_FOLDER, $request->primary_background_image);
                            Storage::disk('public')->delete($webkioskLayout3Configuration->primary_background_image);
                        }

                        if($request->secondary_background_type == 'image' && $request->secondary_background_image) {
                            $newWebkioskLayout2Configuration["secondary_background_image"] = Storage::disk('public')->put($STORAGE_FOLDER, $request->secondary_background_image);
                            Storage::disk('public')->delete($webkioskLayout3Configuration->secondary_background_image);
                        }

                        $webkioskLayout3Configuration->fill($newWebkioskLayout3Configuration);
                        $webkioskLayout3Configuration->save();

                        return $webkiosConfiguration;
                    break;
                default:
                    return $webkiosConfiguration;
                    break;
            }
        });
    }

    public function GetOneLayout2ConfigurationByConfigurationID($configurationID) {
        return WebkioskLayout2Configuration::where('webkios_configuration_id', $configurationID)->first();
    }

}
