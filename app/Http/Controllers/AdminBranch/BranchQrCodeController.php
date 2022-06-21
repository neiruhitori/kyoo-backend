<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BranchQrCodeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $data = [
            'qr' => QrCode::size(180)->generate(
                url('customer/' . Auth::user()->branch_id . '/' . Auth::user()->Branch->queue_type . '/services')
            )
        ];

        return view('adminBranch.branchQrCode', $data);
    }
}
