<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdditionalFeature;
use App\BranchType;
use App\Branch;
use App\Models\FeatureSubscription;
use App\User;
use Illuminate\Support\Carbon;

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
            'max_counter' => $request->max_counter,
            'license_expiration_date' => Carbon::parse($request->license_expiration_date)->format('Y-m-d H:i:s'),
        ]);

        FeatureSubscription::where('branch_id', $id)->delete();
        FeatureSubscription::insert(collect($request->feature_name)->map(function ($feature_id) use ($id) {
            return [
                'branch_id' => $id,
                'feature_id' => $feature_id,
                'created_at' => date('Y-m-d H:i:s')
            ];
        })->toArray());

        // 1 is WST and 6 is WKK
        // disable all account for device if WST and WKK is disable
        if (
            !empty($request->feature_name)
            && !in_array('1', $request->feature_name)
            && !in_array(6, $request->feature_name)
        ) {
            User::where([ 'branch_id' => $id, 'role' => 'device'])->delete();
        }

        $request->session()->flash('success', 'Lisensi diperbarui');

        return redirect()->back();
    }
}
