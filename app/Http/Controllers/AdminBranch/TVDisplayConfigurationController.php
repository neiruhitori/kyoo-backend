<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Branch;
use App\Models\TVConfiguration;
use App\Models\TVLayout;
use App\BranchConfiguration;
use App\Http\Requests\AdminBranch\UpdateTVCustomLayout2Configuration;
use App\Interfaces\TVConfigurationRepositoryInterface;
use App\Models\TVToken;
use Storage;
use Auth;
use Illuminate\Support\Str;

class TVDisplayConfigurationController extends Controller
{
    const DEFAULT_BACKGROUND_TYPE = 'color';
    const DEFAULT_BACKGROUND_IMAGE_PREVIEW = 'img/img-placeholder.jpg';
    const DEFAULT_BACKGROUND_COLOR = '#192db3';
    const DEFAULT_DATETIME_COLOR = '#ffffff';
    const DEFAULT_SIDEBAR_SUBTITLE_COLOR = '#ffffff';
    const DEFAULT_WAITING_LIST_CARD_COLOR = '#ffffff';
    const DEFAULT_WAITING_LIST_FONT_COLOR = '#000000';
    const DEFAULT_CALLING_CARD_HEADER_COLOR = '#ffffff';
    const DEFAULT_CALLING_CARD_BODY_COLOR = '#233c8c';
    const DEFAULT_CALLING_CARD_FONT_HEADER_COLOR = '#233c8c';
    const DEFAULT_FONT_QUEUE_FIRST_LETTER_COLOR = '#000000';
    const DEFAULT_FONT_QUEUE_COLOR = '#ffffff';
    const DEFAULT_RUNNING_TEXT = null;
    const DEFAULT_RUNNING_TEXT_COLOR = '#ffffff';
    const DEFAULT_RUNNING_TEXT_SPEED = 10;

    private TVConfigurationRepositoryInterface $tvConfigurationRepository;

    public function __construct(
        TVConfigurationRepositoryInterface $tvConfigurationRepository
    )
    {
        $this->tvConfigurationRepository = $tvConfigurationRepository;
    }

    public function index()
    {
        $DEFAULT_IMAGE = 'img/img-placeholder.jpg';

        $image_1 = $DEFAULT_IMAGE;
        $image_2 = $DEFAULT_IMAGE;
        $image_3 = $DEFAULT_IMAGE;

        $tvConfigurationFormValue = (object) array(
            'background_type' => self::DEFAULT_BACKGROUND_TYPE,
            'background_image' => self::DEFAULT_BACKGROUND_IMAGE_PREVIEW,
            'background_color' => self::DEFAULT_BACKGROUND_COLOR,
            'datetime_color' => self::DEFAULT_DATETIME_COLOR,
            'sidebar_subtitle_color' => self::DEFAULT_SIDEBAR_SUBTITLE_COLOR,
            'waiting_list_card_color' => self::DEFAULT_WAITING_LIST_CARD_COLOR,
            'waiting_list_font_color' => self::DEFAULT_WAITING_LIST_FONT_COLOR,
            'calling_card_header_color' => self::DEFAULT_CALLING_CARD_HEADER_COLOR,
            'calling_card_body_color' => self::DEFAULT_CALLING_CARD_BODY_COLOR,
            'calling_card_font_header_color' => self::DEFAULT_CALLING_CARD_FONT_HEADER_COLOR,
            'font_queue_first_letter_color' => self::DEFAULT_FONT_QUEUE_FIRST_LETTER_COLOR,
            'font_queue_color' => self::DEFAULT_FONT_QUEUE_COLOR,
            'running_text' => self::DEFAULT_RUNNING_TEXT,
            'running_text_color' => self::DEFAULT_RUNNING_TEXT_COLOR,
            'running_text_speed' => self::DEFAULT_RUNNING_TEXT_SPEED
        );

        $tv_configuration = $this->tvConfigurationRepository->GetOneConfigurationByBranchID(Auth::user()->branch_id);
        $branch_configuration = BranchConfiguration::where('branch_id', Auth::user()->branch_id)->first();

        if ($tv_configuration) {
            if ($tv_configuration->image_1) $image_1 = 'storage/' . $tv_configuration->image_1;
            if ($tv_configuration->image_2) $image_2 = 'storage/' . $tv_configuration->image_2;
            if ($tv_configuration->image_3) $image_3 = 'storage/' . $tv_configuration->image_3;
        }

        if($branch_configuration) {
            $customLayoutConfiguration = $tv_configuration->customLayoutConfiguration2;

            if($customLayoutConfiguration) {
                $tvConfigurationFormValue = (object) array(
                    'background_type' => $customLayoutConfiguration->background_type,
                    'background_image' => $customLayoutConfiguration->background_image ? 'storage/' . $customLayoutConfiguration->background_image : self::DEFAULT_BACKGROUND_IMAGE_PREVIEW,
                    'background_color' => $customLayoutConfiguration->background_color,
                    'datetime_color' => $customLayoutConfiguration->datetime_color,
                    'sidebar_subtitle_color' => $customLayoutConfiguration->sidebar_subtitle_color,
                    'waiting_list_card_color' => $customLayoutConfiguration->waiting_list_card_color,
                    'waiting_list_font_color' => $customLayoutConfiguration->waiting_list_font_color,
                    'calling_card_header_color' => $customLayoutConfiguration->calling_card_header_color,
                    'calling_card_body_color' => $customLayoutConfiguration->calling_card_body_color,
                    'calling_card_font_header_color' => $customLayoutConfiguration->calling_card_font_header_color,
                    'font_queue_first_letter_color' => $customLayoutConfiguration->font_queue_first_letter_color,
                    'font_queue_color' => $customLayoutConfiguration->font_queue_color,
                    'running_text' => $customLayoutConfiguration->running_text,
                    'running_text_color' => $customLayoutConfiguration->running_text_color,
                    'running_text_speed' => $customLayoutConfiguration->running_text_speed
                );
            }
        }

        $defaultImageLayout  = "img/tv-display/layout-1.png";
        if (Auth::user()->Branch->BranchConfiguration->template_signage == 'custom-layout-1'){
            $defaultImageLayout = "img/tv-display/custom-layout-1.jpg";
        } elseif(Auth::user()->Branch->BranchConfiguration->template_signage == 'custom-layout-2') {
            $defaultImageLayout = "img/tv-display/custom-layout-2.jpg";
        }

        return view('adminBranch.tvDisplayConfiguration', [
            'tv_configuration' => $tv_configuration,
            'layout_configuration' => $tvConfigurationFormValue,
            'image_1' => $image_1,
            'image_2' => $image_2,
            'image_3' => $image_3,
            'template_signage' => Auth::user()->Branch->BranchConfiguration->template_signage,
            'is_appointment' => Auth::user()->Branch->BranchType->is_appointment,
            'image_layouts' => [
                [
                    'key' => 'custom-layout-1',
                    'image' => 'img/tv-display/custom-layout-1.jpg'
                ],
                [
                    'key' => 'custom-layout-2',
                    'image' => 'img/tv-display/custom-layout-2.jpg'
                ],
                [
                    'key' => 'standard-ui',
                    'image' => 'img/tv-display/layout-1.png'
                ],
            ],
            'default_image_layout' => $defaultImageLayout
        ]);
    }

    public function update(Branch $branch, Request $request)
    {
        $STORAGE_FOLDER = 'tv_images';

        $request->validate([
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1000',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1000',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1000'
        ]);

        $tv_layout = TVLayout::first();
        $tv_configuration = TVConfiguration::where('branch_id', $branch->id)->first();

        $data = [
            'branch_id' => $branch->id,
            'tv_layout_id' => $request->tv_layout_id ?? $tv_layout->id
        ];

        for ($i = 1; $i <= 3; $i++) {
            if ($request->file("image_$i")) {
                $data["image_$i"] = Storage::disk('public')->put($STORAGE_FOLDER, $request->file("image_$i"));
            }
        }

        if ($tv_configuration) {
            for ($i = 1; $i <= 3; $i++) {
                if ($request->file("image_$i") && $tv_configuration->{"image_$i"}) {
                    Storage::disk('public')->delete($tv_configuration->{"image_$i"});
                }
            }

            TVConfiguration::where('id', $tv_configuration->id)->update($data);
        } else {
            TVConfiguration::create($data);
        }

        return redirect()
            ->route('admin-branch.branch-configuration.queue-monitor')
            ->with('success', 'Konfigurasi display TV berhasil diperbarui.');
    }

    public function updateLayout(Branch $branch, Request $request) {
        $branch->BranchConfiguration->template_signage = $request->template_signage;
        $branch->BranchConfiguration->save();
        return redirect()
            ->route('admin-branch.branch-configuration.queue-monitor')
            ->with('success', 'Manajemen display TV berhasil diperbarui.');
    }

    public function updateToken(Branch $branch) {
        $tv_configuration_id = $branch->TVConfiguration->id;
        $tv_token = TVToken::where('tv_configuration_id', $tv_configuration_id)->first();

        if($tv_token) {
            $tv_token->update([
                'token' => Str::random(12)
            ]);
        } else {
            TVToken::create([
                'tv_configuration_id' => $tv_configuration_id,
                'token' => Str::random(12)
            ]);
        }

        return redirect()
            ->route('admin-branch.branch-configuration.queue-monitor')
            ->with('success', 'Token Web Monitor TV berhasil diperbarui');
    }

    public function updateCustomLayout(Branch $branch, UpdateTVCustomLayout2Configuration $request) {
        $request->validate([
            'background_type' => 'required|string|in:color,image',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
            'background_color' => 'required|string',
            'datetime_color' => 'required|string',
            'sidebar_subtitle_color' => 'required|string',
            'waiting_list_card_color' => 'required|string',
            'waiting_list_font_color' => 'required|string',
            'calling_card_header_color' => 'required|string',
            'calling_card_body_color' => 'required|string',
            'calling_card_font_header_color' => 'required|string',
            'font_queue_first_letter_color' => 'required|string',
            'font_queue_color' => 'required|string',
            'running_text' => 'required|string',
            'running_text_color' => 'required|string',
            'running_text_speed' => 'required|string',
        ]);

        $configuration = $this->tvConfigurationRepository->Upsert($branch->id, $request);

        return redirect()
            ->route('admin-branch.branch-configuration.queue-monitor')
            ->with('success', 'Layout Display TV berhasil diperbarui');
    }
}
