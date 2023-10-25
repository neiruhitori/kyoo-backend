<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalMenuController extends Controller
{
    public function edit()
    {
        $layer = Auth::user()->Branch->BranchConfiguration->layer;

        return view('adminBranch.branchConfiguration.portalMenu.index', compact('layer'));
    }

    public function update(Request $request)
    {
        $data = ['layer' => $request->layer];

        $branchConfiguration = Auth::user()->Branch->BranchConfiguration;

        $branchConfiguration->update($data);

        $request->session()->flash('warning', __('Portal Menu has been updated'));
        return back();
    }
}
