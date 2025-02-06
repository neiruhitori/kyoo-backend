<?php

namespace App\Repositories;

use App\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Interfaces\TVConfigurationRepositoryInterface;
use App\Models\TVConfiguration;
use App\Models\TVCustomLayout2Configuration;
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
            $branch = Branch::find($branchID);
            $STORAGE_FOLDER = 'tv_background_images';
            $DEFAULT_IMAGE = 'img-placeholder.jpg';

            $tvConfiguration = TVConfiguration::where('branch_id', $branchID)->first();

            if($branch->BranchConfiguration->template_signage === 'custom-layout-2') {
                $newTVCustomLayoutConfiguration = [
                    'tv_configuration_id' => $tvConfiguration->id,
                    'background_type' => $request->background_type,
                    'background_color' => $request->background_color,
                    'datetime_color' => $request->datetime_color,
                    'sidebar_subtitle_color' => $request->sidebar_subtitle_color,
                    'waiting_list_card_color' => $request->waiting_list_card_color,
                    'waiting_list_font_color' => $request->waiting_list_font_color,
                    'calling_card_header_color' => $request->calling_card_header_color,
                    'calling_card_body_color' => $request->calling_card_body_color,
                    'calling_card_font_header_color' => $request->calling_card_font_header_color,
                    'font_queue_first_letter_color' => $request->font_queue_first_letter_color,
                    'font_queue_color' => $request->font_queue_color,
                    'running_text' => $request->running_text,
                    'running_text_color' => $request->running_text_color,
                    'running_text_speed' => $request->running_text_speed,
                    'running_text_size' => $request->running_text_size,
                    'logo_size' => $request->logo_size,
                    'text_time_size' => $request->text_time_size,
                    'youtube_volume' => $request->youtube_volume,
                ];
            } else {
                $newTVCustomLayoutConfiguration = [
                    'tv_configuration_id' => $tvConfiguration->id,
                    'background_type' => $request->background_type,
                    'background_color' => $request->background_color,
                    'datetime_color' => $request->datetime_color,
                    'sidebar_subtitle_color' => $request->sidebar_subtitle_color,
                    'calling_card_header_color' => $request->calling_card_header_color,
                    'calling_card_body_color' => $request->calling_card_body_color,
                    'waiting_list_card_color' => $request->waiting_list_card_color,
                    'waiting_list_font_color' => $request->waiting_list_font_color,
                    'font_queue_first_letter_color' => $request->font_queue_first_letter_color,
                    'font_queue_color' => $request->font_queue_color,
                    'calling_card_font_header_color' => $request->calling_card_font_header_color,
                    'running_text' => $request->running_text,
                    'running_text_color' => $request->running_text_color,
                    'running_text_speed' => $request->running_text_speed,
                ];
            }

            $tvCustomLayout2Configuration = TVCustomLayout2Configuration::firstOrNew(['tv_configuration_id' => $tvConfiguration->id]);

            if($request->background_type == 'image' && $request->background_image) {
                $newTVCustomLayoutConfiguration["background_image"] = Storage::disk('public')->put($STORAGE_FOLDER, $request->background_image);
                Storage::disk('public')->delete($tvCustomLayout2Configuration->background_image);
            }

            $tvCustomLayout2Configuration->fill($newTVCustomLayoutConfiguration);
            $tvCustomLayout2Configuration->save();

            return $tvCustomLayout2Configuration;
        });
    }

    public function GetOneLayout2ConfigurationByConfigurationID($configurationID) {
        return WebkioskLayout2Configuration::where('webkios_configuration_id', $configurationID)->first();
    }

}
