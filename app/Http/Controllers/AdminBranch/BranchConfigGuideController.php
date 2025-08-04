<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BranchConfigGuideController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {   $data = [
            'qr' => QrCode::size(180)->generate(
                url('customer/' . Auth::user()->branch_id . '/' . Auth::user()->Branch->queue_type . '/services')
            ),
            'short_url' => url(Auth::user()->branch_id),
        ];
        // $branchConfig = Auth::user()->Branch->BranchConfiguration;
        // if (Auth::user()->Branch->queue_type == 'onsite' && $branchConfig->queue_layout_configuration == "modern-ui") {
        //     return view('adminBranch.branchConfigGuide.modernUI');
        // }

        return view('adminBranch.branchConfigGuide.standardUI', $data);
    }
}
