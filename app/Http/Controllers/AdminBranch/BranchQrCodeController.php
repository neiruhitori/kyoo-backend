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
        $qrData = json_encode([
            'type' => 'show_branch_action',
            'branch' => [
                'id' => Auth::user()->branch_id
            ]
        ]);
        $qrDataToken = base64_encode($qrData);

        $data = [
            'qr' => QrCode::size(180)->generate($qrDataToken)
        ];

        return view('adminBranch.branchQrCode', $data);
    }
}
