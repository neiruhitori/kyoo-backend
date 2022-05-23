<?php

Route::get('/customer/{path?}', function () {
    return view('customer.index');
})->where([
    'branch_id' => '[0-9]+',
    'path' => '.*',
])->middleware('cookie.clientid');