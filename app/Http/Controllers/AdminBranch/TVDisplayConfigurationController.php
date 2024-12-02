<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Branch;
use App\Models\TVConfiguration;
use App\Models\TVLayout;
use App\BranchConfiguration;
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
        $is_appointment = Auth::user()->Branch->BranchType->is_appointment;

        $image_1 = $DEFAULT_IMAGE;
        $image_2 = $DEFAULT_IMAGE;
        $image_3 = $DEFAULT_IMAGE;
        $image_4 = $DEFAULT_IMAGE;
        $image_5 = $DEFAULT_IMAGE;
        $image_6 = $DEFAULT_IMAGE;

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
        $switchLink = 'file';
        $link_1 = '';
        $link_2 = '';
        $link_3 = '';

        if ($tv_configuration) {
            $link_1 =$tv_configuration->image_1 && filter_var($tv_configuration->image_1, FILTER_VALIDATE_URL)
            ? $tv_configuration->image_1 // Jika image_1 adalah URL
            : null ;

            $link_2 =$tv_configuration->image_2 && filter_var($tv_configuration->image_2, FILTER_VALIDATE_URL)
            ? $tv_configuration->image_2 // Jika image_1 adalah URL
            : null ;
            
            $link_3 =$tv_configuration->image_3 && filter_var($tv_configuration->image_3, FILTER_VALIDATE_URL)
            ? $tv_configuration->image_3 // Jika image_1 adalah URL
            : null ;

            

            // Cek jika salah satu link adalah null atau kosong
            if (!empty($link_1) || !empty($link_2) || !empty($link_3)) {
                $switchLink = 'youtube';
            }else{
                $switchLink = 'file';
            }

            
           // Cek gambar untuk image 1, 2, dan 3
            $image_1 = ($tv_configuration->image_1 && !filter_var($tv_configuration->image_1, FILTER_VALIDATE_URL))
            ? 'storage/' . $tv_configuration->image_1
            : '';

            $image_2 = ($tv_configuration->image_2 && !filter_var($tv_configuration->image_2, FILTER_VALIDATE_URL))
            ? 'storage/' . $tv_configuration->image_2
            : '';

            $image_3 = ($tv_configuration->image_3 && !filter_var($tv_configuration->image_3, FILTER_VALIDATE_URL))
            ? 'storage/' . $tv_configuration->image_3
            : '';
            
            if ($tv_configuration->image_4) $image_4 = 'storage/' . $tv_configuration->image_4;
            if ($tv_configuration->image_5) $image_5 = 'storage/' . $tv_configuration->image_5;
            if ($tv_configuration->image_6) $image_6 = 'storage/' . $tv_configuration->image_6;
        }

        if($branch_configuration && $tv_configuration && $tv_configuration->customLayoutConfiguration2) {
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
        if (!$is_appointment && Auth::user()->Branch->BranchConfiguration->template_signage == 'custom-layout-1'){
            $defaultImageLayout = "img/tv-display/custom-layout-1.jpg";
        } elseif(!$is_appointment && Auth::user()->Branch->BranchConfiguration->template_signage == 'custom-layout-2') {
            $defaultImageLayout = "img/tv-display/custom-layout-2.jpg";
        } elseif(!$is_appointment && Auth::user()->Branch->BranchConfiguration->template_signage == 'custom-layout-3') {
            $defaultImageLayout = "img/tv-display/custom-layout-3.png";
        }

        return view('adminBranch.tvDisplayConfiguration', [
            'tv_configuration' => $tv_configuration,
            'layout_configuration' => $tvConfigurationFormValue,
            'switchLink' => $switchLink,
            'link_1' => $link_1,
            'link_2' => $link_2,
            'link_3' => $link_3,
            'image_1' => $image_1,
            'image_2' => $image_2,
            'image_3' => $image_3,
            'image_4' => $image_4,
            'image_5' => $image_5,
            'image_6' => $image_6,
            'template_signage' => Auth::user()->Branch->BranchConfiguration->template_signage,
            'is_appointment' => $is_appointment,
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
                    'key' => 'custom-layout-3',
                    'image' => 'img/tv-display/custom-layout-3.png'
                ],
                [
                    'key' => 'standard-ui',
                    'image' => 'img/tv-display/layout-1.png'
                ],
            ],
            'default_image_layout' => $defaultImageLayout,
            'defaultImage' => self::DEFAULT_BACKGROUND_IMAGE_PREVIEW
        ]);
    }

    public function update(Branch $branch, Request $request)
{
    $request->validate([
        'image_1' => 'nullable|file|max:10000|mimes:jpeg,png,jpg,gif,svg,mp4',
        'image_2' => 'nullable|file|max:10000|mimes:jpeg,png,jpg,gif,svg,mp4',
        'image_3' => 'nullable|file|max:10000|mimes:jpeg,png,jpg,gif,svg,mp4',
        'url_1' => 'nullable|url',
        'url_2' => 'nullable|url',
        'url_3' => 'nullable|url',
    ]);

    $tv_layout = TVLayout::first();
    $tv_configuration = TVConfiguration::where('branch_id', $branch->id)->first();
    
    $data = [
        'branch_id' => $branch->id,
        'tv_layout_id' => $request->tv_layout_id ?? $tv_layout->id
    ];

    for ($i = 1; $i <= 3; $i++) {
        if ($request->file("image_$i")) {
            // Hapus file lama jika ada
            if ($tv_configuration && $tv_configuration->{"image_$i"}) {
                Storage::disk('public')->delete($tv_configuration->{"image_$i"});
            }

            $file = $request->file("image_$i");
            $extension = $file->getClientOriginalExtension();
            $folder = $extension === 'mp4' ? 'tv_videos' : 'tv_images';
            $data["image_$i"] = Storage::disk('public')->put($folder, $file);
        } elseif ($request->input("url_$i")) {
            $data["image_$i"] = $request->input("url_$i"); // Simpan URL jika ada
        }
    }

    if ($tv_configuration) {
        TVConfiguration::where('id', $tv_configuration->id)->update($data);
    } else {
        $tv_config = TVConfiguration::create($data);
        TVToken::create([
            'tv_configuration_id' => $tv_config->id,
            'token' => Str::random(12)
        ]);
    }

    return redirect()->route('admin-branch.branch-configuration.queue-monitor')
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

    public function updateCustomLayout(Branch $branch, Request $request) {
        if($branch->BranchConfiguration->template_signage === 'custom-layout-2') {
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
                'running_text' => 'required|string|max:100',
                'running_text_color' => 'required|string',
                'running_text_speed' => 'required|string',
            ]);
        } else {
            $request->validate([
                'background_type' => 'required|string|in:color,image',
                'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
                'background_color' => 'required|string',
                'datetime_color' => 'required|string',
                'sidebar_subtitle_color' => 'required|string',
                'waiting_list_card_color' => 'required|string',
                'waiting_list_font_color' => 'required|string',
                'font_queue_color' => 'required|string',
                'running_text' => 'required|string|max:100',
                'running_text_color' => 'required|string',
                'running_text_speed' => 'required|string',
            ]);
        }

        $configuration = $this->tvConfigurationRepository->Upsert($branch->id, $request);

        return redirect()
            ->route('admin-branch.branch-configuration.queue-monitor')
            ->with('success', 'Layout Display TV berhasil diperbarui');
    }
}
