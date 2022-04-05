<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class CustomerGuideController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $branch_license = Auth::user()->Branch->BranchType;
        $queue_type = 'appointment';

        if ($branch_license->is_exhibition) {
            $queue_type = 'exhibition';
        } else if ($branch_license->is_direct_queue) {
            $queue_type = 'onsite';
        }

        return view('adminBranch.customerGuide', [
            'queue_type' => $queue_type
        ]);
    }
}
