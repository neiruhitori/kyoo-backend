<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;

class ShortURLController extends Controller
{
    public function customerWebUrl(Branch $branch)
    {
        $branch_license = $branch->BranchType;
        $queue_type = 'appointment';

        if ($branch_license->is_exhibition) {
            $queue_type = 'exhibition';
        } else if ($branch_license->is_direct_queue) {
            $queue_type = 'onsite';
        }

        return redirect('/kyooTicket/' . $queue_type . '/' . $branch->id . '/services');
    }
}
