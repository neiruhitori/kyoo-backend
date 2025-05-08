<?php

use App\Branch;
use App\BranchType;
use App\BranchConfiguration;
use Illuminate\Support\Facades\Cookie;

Route::get('/customer/{path?}', function ($path) {
    preg_match('/^\d+/', $path, $matches);
    $branch_id = intval($matches[0]);
    $branchConfiguration = null;
    $webStyle = null;
    $ticketStyle = null;
    $branch = Branch::with('BranchType:id,is_premium')
    ->where('id', $branch_id)
    ->select('branch_type_id', 'country')
    ->first();
    if ($branch->is_premium) {
        $d = BranchConfiguration::where('branch_id', $branch_id)->first();
        $webStyle = $d->web_style ?? 'web-style-1';
        $ticketStyle = $d->ticket_style ?? 'ticket-style-1';
    }
    $country = $branch->country;
    if (preg_match('/^\d+\/onsite\/services(\/two-layer)?$/', $path)) {
        $branchConfiguration =BranchConfiguration::where('branch_id', $branch_id)->first();
    }

    return view('customer.index', compact('branchConfiguration','country','webStyle','ticketStyle'));
})->where([
    'branch_id' => '[0-9]+',
    'path' => '.*',
])->middleware('cookie.clientid');