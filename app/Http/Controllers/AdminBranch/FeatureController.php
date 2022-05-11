<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Auth;

class FeatureController extends Controller
{
    public function index()
    {
        return view('adminBranch.feature', [
            'branch_config' => Auth::user()->Branch->BranchConfiguration
        ]);
    }
}
