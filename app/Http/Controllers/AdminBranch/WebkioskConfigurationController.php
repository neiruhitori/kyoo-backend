<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Branch;
use App\Interfaces\WebKioskConfigurationRepositoryInterface;
use Auth;

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
        );

        $webkiosConfiguration = $this->webKioskConfigurationRepository->GetOneConfigurationByBranchID(Auth::user()->branch_id);
        $layouts = $this->webKioskConfigurationRepository->GetAllLayout();

        if($webkiosConfiguration) {
            $webkiosConfigurationFormValue->layout = $webkiosConfiguration->layout_id;

            if ($webkiosConfiguration->layoutConfiguration) {
                $webkiosConfigurationFormValue = (object) array(
                    'layout' => $webkiosConfiguration->layout_id,
                    'primary_background_type' => $webkiosConfiguration->layoutConfiguration->primary_background_type,
                    'primary_background_image' => $webkiosConfiguration->layoutConfiguration->primary_background_image ? 'storage/' . $webkiosConfiguration->layoutConfiguration->primary_background_image : self::DEFAULT_IMAGE,
                    'primary_background_color' => $webkiosConfiguration->layoutConfiguration->primary_background_color,
                    'secondary_background_type' => $webkiosConfiguration->layoutConfiguration->secondary_background_type,
                    'secondary_background_image' => $webkiosConfiguration->layoutConfiguration->secondary_background_image ? 'storage/' . $webkiosConfiguration->layoutConfiguration->secondary_background_image : self::DEFAULT_IMAGE,
                    'secondary_background_color' => $webkiosConfiguration->layoutConfiguration->secondary_background_color,
                    'button_background_color' => $webkiosConfiguration->layoutConfiguration->button_background_color,
                    'botton_border_color' => $webkiosConfiguration->layoutConfiguration->botton_border_color,
                    'font_color' => $webkiosConfiguration->layoutConfiguration->font_color,
                );
            }
        }

        return view('adminBranch.webkioskConfiguration', [
            'layouts' => $layouts,
            'webkiosConfiguration' => $webkiosConfigurationFormValue,
            'defaultImage' => self::DEFAULT_IMAGE,
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
}
