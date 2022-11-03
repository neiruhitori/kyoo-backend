<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Branch;
use App\Models\Corporate;

class CorporateBranchController extends Controller
{
    public function index($corporateId)
    {
        $corporate = Corporate::find($corporateId);
        $branches = Branch::where('corporate_id', $corporateId)->get();

        return view('admin.corporate.branch', [
            'corporate' => $corporate,
            'branches' => $branches,
        ]);
    }
}
