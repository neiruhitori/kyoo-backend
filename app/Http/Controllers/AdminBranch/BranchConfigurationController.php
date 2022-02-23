<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BranchConfiguration;
use App\Log;
use App\Http\Requests\AdminBranch\UpdateBranchConfiguration;
use Auth;
class BranchConfigurationController extends Controller
{
    public function edit()
    {
        return view('adminBranch.branchConfiguration.edit');
    }

    public function update(UpdateBranchConfiguration $request)
    {
        $branchConfiguration = Auth::user()->Branch->BranchConfiguration;
        $branchConfiguration->update($request->all());
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Branch Configuration'
        ]);
        $request->session()->flash('warning', __('Branch Configuration has been updated'));
        return redirect(route('adminBranch.branchConfiguration.edit'));
    }
}
