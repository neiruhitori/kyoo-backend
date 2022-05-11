<?php

Route::view('/customer/{path?}', 'customer.index')->where([
    'branch_id' => '[0-9]+',
    'path' => '.*',
]);