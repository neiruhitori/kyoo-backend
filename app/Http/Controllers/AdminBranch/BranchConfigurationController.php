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
        $data = $request->all();
        if (!isset($request->queue_voice)) {
            $data['queue_voice'] = 'off';
        }
        
        $branchConfiguration = Auth::user()->Branch->BranchConfiguration;

        if ($branchConfiguration) {
            $branchConfiguration->update($data);
        } else {
            $data['branch_id'] = Auth::user()->branch_id;
            BranchConfiguration::create($data);
        }

        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Branch Configuration'
        ]);

        $request->session()->flash('warning', __('Branch Configuration has been updated'));
        return back();
    }
}
