<?php

namespace App\Repositories;

use Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Interfaces\WebKioskConfigurationRepositoryInterface;
use App\Models\WebkioskConfiguration;
use App\Models\WebkioskLayout2Configuration;
use App\Models\WebkioskLayout;
use App\Models\WebkioskLayout3Configuration;
use App\Models\WebkioskLayout4Configuration;

class WebKioskConfigurationRepository implements WebKioskConfigurationRepositoryInterface
{
    public function GetAllLayout() {
        return WebkioskLayout::get();
    }

    public function GetOneConfigurationByBranchID($branchID) {
        return WebkioskConfiguration::where('branch_id', $branchID)
            ->with('layoutConfiguration2')
            ->with('layoutConfiguration3')
            ->with('layoutConfiguration4')
            ->with('layout')
            ->first();
    }

    public function Upsert($branchID, Request $request) {
        return DB::transaction(function () use ($branchID, $request){
            $STORAGE_FOLDER = 'webkiosk-backgrond-images';
            $DEFAULT_IMAGE = 'img-placeholder.jpg';

            $newWebkioskConfiguration = [
                'branch_id' => $branchID,
                'layout_id' => $request->layout,
            ];

            $webkiosConfiguration = WebkioskConfiguration::firstOrNew(['branch_id' => $branchID]);
            $webkiosConfiguration->fill($newWebkioskConfiguration);
            $webkiosConfiguration->save();

            switch ($request->layout) {
                case 2:
                        $newWebkioskLayout2Configuration = [
                            'webkios_configuration_id' => $webkiosConfiguration->id,
                            'primary_background_type' => $request->primary_background_type,
                            'primary_background_color' => $request->primary_background_color,
                            'secondary_background_type' => $request->secondary_background_type,
                            'secondary_background_color' => $request->secondary_background_color,
                            'button_background_color' => $request->button_background_color,
                            'botton_border_color' => $request->botton_border_color,
                            'font_color' => $request->font_color,
                        ];

                        $webkioskLayout2Configuration = WebkioskLayout2Configuration::firstOrNew(['webkios_configuration_id' => $webkiosConfiguration->id]);

                        if($request->primary_background_type == 'image' && $request->primary_background_image) {
                            $newWebkioskLayout2Configuration["primary_background_image"] = Storage::disk('public')->put($STORAGE_FOLDER, $request->primary_background_image);
                            Storage::disk('public')->delete($webkioskLayout2Configuration->primary_background_image);
                        }

                        if($request->secondary_background_type == 'image' && $request->secondary_background_image) {
                            $newWebkioskLayout2Configuration["secondary_background_image"] = Storage::disk('public')->put($STORAGE_FOLDER, $request->secondary_background_image);
                            Storage::disk('public')->delete($webkioskLayout2Configuration->secondary_background_image);
                        }

                        $webkioskLayout2Configuration->fill($newWebkioskLayout2Configuration);
                        $webkioskLayout2Configuration->save();

                        return $webkiosConfiguration;
                    break;
                case 3:
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
                            $newWebkioskLayout3Configuration["primary_background_image"] = Storage::disk('public')->put($STORAGE_FOLDER, $request->primary_background_image);
                            Storage::disk('public')->delete($webkioskLayout3Configuration->primary_background_image);
                        }

                        if($request->secondary_background_type == 'image' && $request->secondary_background_image) {
                            $newWebkioskLayout3Configuration["secondary_background_image"] = Storage::disk('public')->put($STORAGE_FOLDER, $request->secondary_background_image);
                            Storage::disk('public')->delete($webkioskLayout3Configuration->secondary_background_image);
                        }

                        $webkioskLayout3Configuration->fill($newWebkioskLayout3Configuration);
                        $webkioskLayout3Configuration->save();

                        return $webkiosConfiguration;
                    break;
                case 4:
                        $newWebkioskLayout4Configuration = [
                            'webkios_configuration_id' => $webkiosConfiguration->id,
                            'primary_background_type' => $request->primary_background_type,
                            'primary_background_color' => $request->primary_background_color,
                            'button_background_color' => $request->button_background_color,
                            'botton_border_color' => $request->botton_border_color,
                            'font_color' => $request->font_color,
                            'button_checkin_background_color' => $request->button_checkin_background_color,
                            'button_checkin_border_color' => $request->button_checkin_border_color,
                            'font_checkin_color' => $request->font_checkin_color,
                        ];

                        $webkioskLayout4Configuration = WebkioskLayout4Configuration::firstOrNew(['webkios_configuration_id' => $webkiosConfiguration->id]);

                        if($request->primary_background_type == 'image' && $request->primary_background_image) {
                            $newWebkioskLayout4Configuration["primary_background_image"] = Storage::disk('public')->put($STORAGE_FOLDER, $request->primary_background_image);
                            Storage::disk('public')->delete($webkioskLayout4Configuration->primary_background_image);
                        }

                        if($request->logo) {
                            $newWebkioskLayout4Configuration["logo"] = Storage::disk('public')->put($STORAGE_FOLDER, $request->logo);
                            Storage::disk('public')->delete($webkioskLayout4Configuration->logo);
                        }

                        if($request->ticket_logo) {
                            $newWebkioskLayout4Configuration["ticket_logo"] = Storage::disk('public')->put($STORAGE_FOLDER, $request->ticket_logo);
                            Storage::disk('public')->delete($webkioskLayout4Configuration->ticket_logo);
                        }

                        $webkioskLayout4Configuration->fill($newWebkioskLayout4Configuration);
                        $webkioskLayout4Configuration->save();

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
