<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalMenuController extends Controller
{
    public function edit()
    {
        $branchConfiguration = Auth::user()->Branch->BranchConfiguration;

        return view('adminBranch.branchConfiguration.portalMenu.index', compact('branchConfiguration'));
    }

    public function update(Request $request)
    {
        $data['layer'] = $request->layer;
        $data['time_interval'] = $request->time_interval;
        $data['max_slots'] = $request->max_slots;

        $branchConfiguration = Auth::user()->Branch->BranchConfiguration;

        $branchConfiguration->update($data);

        $request->session()->flash('warning', __('Portal Menu has been updated'));
        return back();
    }
}
