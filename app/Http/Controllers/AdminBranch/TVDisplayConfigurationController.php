<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Branch;
use App\Models\TVConfiguration;
use App\Models\TVLayout;
use APP\BranchConfiguration;
use Storage;
use Auth;

class TVDisplayConfigurationController extends Controller
{
    public function index()
    {
        $DEFAULT_IMAGE = 'img/img-placeholder.jpg';

        $image_1 = $DEFAULT_IMAGE;
        $image_2 = $DEFAULT_IMAGE;
        $image_3 = $DEFAULT_IMAGE;

        $tv_configuration = TVConfiguration::where('branch_id', Auth::user()->branch_id)->first() ?? null;

        if ($tv_configuration) {
            if ($tv_configuration->image_1) $image_1 = 'storage/' . $tv_configuration->image_1;
            if ($tv_configuration->image_2) $image_2 = 'storage/' . $tv_configuration->image_2;
            if ($tv_configuration->image_3) $image_3 = 'storage/' . $tv_configuration->image_3;
        }


        $defaultImageLayout  = "img/tv-display/layout-1.png";
        if (Auth::user()->Branch->BranchConfiguration->template_signage == 'custom-layout-1'){
            $defaultImageLayout = "img/tv-display/custom-layout-1.jpg";
        }

        return view('adminBranch.tvDisplayConfiguration', [
            'tv_configuration' => $tv_configuration,
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
}
