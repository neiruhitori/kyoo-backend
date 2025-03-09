<?php

use App\Branch;
use App\BranchConfiguration;

Route::get('/customer/{path?}', function ($path) {
    preg_match('/^\d+/', $path, $matches);
    $branch_id = intval($matches[0]);
    $branchConfiguration = null;
    $country = Branch::where('id',$branch_id)->select('country')->first();
    if (preg_match('/^\d+\/onsite\/services(\/two-layer)?$/', $path)) {
        $branchConfiguration =BranchConfiguration::where('branch_id', $branch_id)->first();
    }

    return view('customer.index', compact('branchConfiguration','country'));
})->where([
    'branch_id' => '[0-9]+',
    'path' => '.*',
])->middleware('cookie.clientid');