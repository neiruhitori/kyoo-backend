<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Branch;
use App\BranchToken;

class BranchTokenController extends Controller
{
    public function store(Request $request)
    {
        $branch = Branch::findOrFail($request->branch_id);
        $token = bcrypt($branch->id . '-' . time());

        BranchToken::whereBranchId($branch->id)->delete();
        BranchToken::create([
            'branch_id' => $branch->id,
            'token' => $token
        ]);

        $request->session()->flash('success', __('module.generated', ['module' => __('Token'), 'name' => $branch->name]));
        return redirect(route('admin.branch.show', $branch->id));
    }
}
