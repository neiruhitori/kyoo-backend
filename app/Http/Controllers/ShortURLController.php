<?php

namespace App\Http\Controllers;

use App\Branch;

class ShortURLController extends Controller
{
    public function customerWebUrl(Branch $branch)
    {
        return redirect('customer/' . $branch->id . '/' . $branch->queue_type . '/services');
    }
}
