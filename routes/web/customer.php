<?php

use App\BranchConfiguration;

Route::get('/customer/{path?}', function ($path) {
    preg_match('/^\d+/', $path, $matches);
    $branch_id = intval($matches[0]);
    $branchConfiguration = BranchConfiguration::where('branch_id', $branch_id)->first();

    return view('customer.index', compact('branchConfiguration'));
})->where([
    'branch_id' => '[0-9]+',
    'path' => '.*',
])->middleware('cookie.clientid');