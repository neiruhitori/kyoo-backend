<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Branch;
use App\Interfaces\WebKioskConfigurationRepositoryInterface;
use Auth;
use App\Models\WebkioskConfiguration;
use App\Models\WebkioskToken;
use Illuminate\Support\Str;

class WebkioskConfigurationController extends Controller
{
    const DEFAULT_BACKGROUND_TYPE = 'color';
    const DEFAULT_IMAGE = 'img/img-placeholder.jpg';
    const DEFAULT_PRIMARY_COLOR = '#0c61a2';
    const DEFAULT_SECONDARY_COLOR = '#ffffff';
    const DEFAULT_BUTTON_COLOR = '#0c30a8';
    const DEFAULT_BUTTON_BORDER = '#ffffff';
    const DEFAULT_FONT_COLOR = '#ffffff';

    private WebKioskConfigurationRepositoryInterface $webKioskConfigurationRepository;

    public function __construct(
        WebKioskConfigurationRepositoryInterface $webKioskConfigurationRepository
    )
    {
        $this->webKioskConfigurationRepository = $webKioskConfigurationRepository;
    }

    public function index()
    {
        $webkiosConfigurationFormValue = (object) array(
            'layout' => "2",
            'primary_background_type' => self::DEFAULT_BACKGROUND_TYPE,
            'primary_background_image' => null,
            'primary_background_image_preview' => self::DEFAULT_IMAGE,
            'primary_background_color' => self::DEFAULT_PRIMARY_COLOR,
            'secondary_background_type' => self::DEFAULT_BACKGROUND_TYPE,
            'secondary_background_image' => null,
            'secondary_background_image_preview' => self::DEFAULT_IMAGE,
            'secondary_background_color' => self::DEFAULT_SECONDARY_COLOR,
            'button_background_color' => self::DEFAULT_BUTTON_COLOR,
            'botton_border_color' => self::DEFAULT_BUTTON_BORDER,
            'font_color' => self::DEFAULT_FONT_COLOR,
            'active_menus' => [],
        );

        $webkiosConfiguration = $this->webKioskConfigurationRepository->GetOneConfigurationByBranchID(Auth::user()->branch_id);
        $layouts = $this->webKioskConfigurationRepository->GetAllLayout();

        if($webkiosConfiguration) {
            $webkiosConfigurationFormValue->layout = $webkiosConfiguration->layout_id;
            $layoutConfiguration = $webkiosConfiguration->layout_id == 2 ? $webkiosConfiguration->layoutConfiguration2 : $webkiosConfiguration->layoutConfiguration3;

            if ($layoutConfiguration) {
                $webkiosConfigurationFormValue = (object) array(
                    'layout' => $webkiosConfiguration->layout_id,
                    'primary_background_type' => $layoutConfiguration->primary_background_type,
                    'primary_background_image' => $layoutConfiguration->primary_background_image ? 'storage/' . $layoutConfiguration->primary_background_image : self::DEFAULT_IMAGE,
                    'primary_background_color' => $layoutConfiguration->primary_background_color,
                    'secondary_background_type' => $layoutConfiguration->secondary_background_type,
                    'secondary_background_image' => $layoutConfiguration->secondary_background_image ? 'storage/' . $layoutConfiguration->secondary_background_image : self::DEFAULT_IMAGE,
                    'secondary_background_color' => $layoutConfiguration->secondary_background_color,
                    'button_background_color' => $layoutConfiguration->button_background_color,
                    'botton_border_color' => $layoutConfiguration->botton_border_color,
                    'font_color' => $layoutConfiguration->font_color,
                );
            }

            if ($webkiosConfiguration->active_menus) {
                $webkiosConfigurationFormValue->active_menus = json_decode($webkiosConfiguration->active_menus);
            }
        }

        return view('adminBranch.webkioskConfiguration', [
            'layouts' => $layouts,
            'webkiosConfiguration' => $webkiosConfigurationFormValue,
            'defaultImage' => self::DEFAULT_IMAGE,
            'menuOptions'=> WebkioskConfiguration::MENU_OPTIONS,
        ]);
    }

    public function update(Branch $branch, Request $request)
    {
        $request->validate([
            'layout' => 'required|integer',
            'primary_background_type' => 'required_if:layout,=,2|string|in:color,image',
            'primary_background_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1000',
            'primary_background_color' => 'required_if:layout,=,2|string',
            'secondary_background_type' => 'required_if:layout,=,2|string|in:color,image',
            'secondary_background_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1000',
            'secondary_background_color' => 'required_if:layout,=,2|string',
            'button_background_color' => 'required_if:layout,=,2|string',
            'botton_border_color' => 'required_if:layout,=,2|string',
            'font_color' => 'required_if:layout,=,2|string',
        ]);

        $configuration = $this->webKioskConfigurationRepository->Upsert($branch->id, $request);


        return redirect()
            ->route('admin-branch.branch-configuration.webkiosk')
            ->with('success', 'Konfigurasi webkiosk berhasil diperbarui.');
    }

    public function updateActiveMenus(Branch $branch, Request $request)
    {
        try {
            $request->validate([
                'input_active_menus.*' => 'in:wa,photo,print',
            ]);

            $webkioskConfig = WebkioskConfiguration::where('branch_id', $branch->id)->firstOrFail();
            $webkioskConfig->active_menus = json_encode($request->get('input_active_menus'));
            $webkioskConfig->save();

            return redirect()
                ->route('admin-branch.branch-configuration.webkiosk')
                ->with('success', 'Konfigurasi aktif menu berhasil diperbarui.');
        } catch (\Throwable $th) {
            return redirect()
                ->route('admin-branch.branch-configuration.webkiosk')
                ->with('error', 'Error konfigurasi aktif menu');
        }
    }

    public function updateToken(Branch $branch) {
        $webkioskConfig = WebkioskConfiguration::where('branch_id', $branch->id)->first();
        $webkioskToken = WebkioskToken::where('webkiosk_configuration_id', $webkioskConfig->id)->first();

        if($webkioskToken) {
            $webkioskToken->update([
                'token' => Str::random(12)
            ]);
        } else {
            WebkioskToken::create([
                'webkiosk_configuration_id' => $webkioskConfig->id,
                'token' => Str::random(12)
            ]);
        }

        return redirect()
            ->route('admin-branch.branch-configuration.webkiosk')
            ->with('success', 'Token Web Kiosk berhasil diperbarui');
    }
}
