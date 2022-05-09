<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use ShortURL;

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
        return view('adminBranch.customerGuide', [
            'short_url' => url(Auth::user()->branch_id),
        ]);
    }
}
