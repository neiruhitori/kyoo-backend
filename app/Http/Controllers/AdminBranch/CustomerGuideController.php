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
        $branch_license = Auth::user()->Branch->BranchType;
        $queue_type = 'appointment';

        if ($branch_license->is_exhibition) {
            $queue_type = 'exhibition';
        } else if ($branch_license->is_direct_queue) {
            $queue_type = 'onsite';
        }

        $original_url = url('kyooTicket/' . $queue_type . '/' . Auth::user()->branch_id. '/services');
        
        try {
            ShortURL::destinationUrl($original_url)->urlKey(Auth::user()->branch_id)->secure()->make();
        } catch (\AshAllenDesign\ShortURL\Exceptions\ShortURLException $e) {
            if ($e->getMessage() != 'A short URL with this key already exists.') {
                throw new \AshAllenDesign\ShortURL\Exceptions\ShortURLException($e->getMessage());
            }
        }

        return view('adminBranch.customerGuide', [
            'queue_type' => $queue_type,
            'short_url' => url(Auth::user()->branch_id),
        ]);
    }
}
