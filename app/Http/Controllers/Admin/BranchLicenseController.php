<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdditionalFeature;
use App\BranchType;
use App\Branch;
use App\Models\FeatureSubscription;

class BranchLicenseController extends Controller
{
    public function index($id)
    {
        $branch = Branch::find($id);

        $data = [
            'branch' => $branch,
            'branch_license' => $branch->BranchType,
            'branch_types' => BranchType::all(),
            'features' => AdditionalFeature::all(),
            'selected_features' => FeatureSubscription::where('branch_id', $branch->id)->get()
        ];

        return view('admin.branch.license', $data);
    }

    public function update(Request $request, $id)
    {
        Branch::where('id', $id)->update([
            'branch_type_id' => $request->branch_type_id,
            'max_counter' => $request->max_counter
        ]);

        FeatureSubscription::where('branch_id', $id)->delete();
        FeatureSubscription::insert(collect($request->feature_name)->map(function ($feature_id) use ($id) {
            return [
                'branch_id' => $id,
                'feature_id' => $feature_id,
                'created_at' => date('Y-m-d H:i:s')
            ];
        })->toArray());

        $request->session()->flash('success', 'Lisensi diperbarui');

        return redirect()->back();
    }
}
