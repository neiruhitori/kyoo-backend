<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BranchConfigGuideController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $branchConfig = Auth::user()->Branch->BranchConfiguration;
        if (Auth::user()->Branch->queue_type == 'onsite' && $branchConfig->queue_layout_configuration == "modern-ui") {
            return view('adminBranch.branchConfigGuide.modernUI');
        }

        return view('adminBranch.branchConfigGuide.standardUI');
    }
}
