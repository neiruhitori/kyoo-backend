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
            'url' => 'customer/' . $branch->id . '/' . $branch->queue_type . '/services'
        ]);
    }
}
