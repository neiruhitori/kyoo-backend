<?php

namespace App\Http\Controllers\CS;

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
        $branchLicense = Auth::user()->Branch->BranchType;
        $queueType = 'exhibition';

        if ($branchLicense->is_appointment) {
            $queueType = 'appointment';
        }

        if ($branchLicense->is_direct_queue) {
            $queueType = 'onsite';
        }

        $data = [
            'qr' => QrCode::size(180)->generate(
                url('kyooTicket/' . $queueType  . '/' . Auth::user()->branch_id . '/services')
            )
        ];

        return view('adminBranch.branchQrCode', $data);
    }
}
