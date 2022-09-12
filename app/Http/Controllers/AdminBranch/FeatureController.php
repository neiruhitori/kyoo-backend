<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FeatureController extends Controller
{
    public function index()
    {
        $branchConfig = Auth::user()->Branch->BranchConfiguration;

        if (Auth::user()->Branch->queue_type == 'onsite') {
            return view('adminBranch.feature', ['branch_config' => $branchConfig]);
        }

        if (Auth::user()->Branch->queue_type == 'appointment') {
            return view('adminBranch.appointment.feature', ['branch_config' => $branchConfig]);
        }

        if (Auth::user()->Branch->queue_type == 'exhibition') {
            return redirect()->back();
        }
    }
}
