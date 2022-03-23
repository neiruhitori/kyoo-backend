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
            'qr' => QrCode::size(180)->generate(url('kyooTicket', [Auth::user()->branch_id]))
        ];

        return view('adminBranch.branchQrCode', $data);
    }
}
