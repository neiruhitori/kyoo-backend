<?php

namespace App\Http\Controllers;

use App\Branch;

class ShortURLController extends Controller
{
    public function customerWebUrl(Branch $branch)
    {
        return redirect('customer/' . $branch->id . '/' . $branch->queue_type . '/services');
    }
    public function APICustomerUrl(Branch $branch)
    {
        return response()->json([
            'url' => 'customer/' . $branch->id . '/' . $branch->queue_type . '/services',
            'style' => $branch->BranchConfiguration->web_style ?? 'web-style-1'
        ]);
    }

    public function handleDeeplink($branch_id, $booking_id)
    {
        $branch = Branch::find($branch_id);

        $webUrl = url("customer/{$branch_id}/appointment-onsite/booking-status/{$booking_id}");
        if($branch->BranchType->is_appointment){
            $webUrl = url("customer/{$branch_id}/appointment/booking-status/{$booking_id}");
        }
        
        // dd($branch->BranchType->is_direct_queue);
        // Fallback
        return redirect()->away($webUrl);
    }
}
