<?php

Route::namespace('CS')
    ->middleware('auth', 'checkCS')
    ->prefix('cs')
    ->name('cs.')
    ->group(function () {
        Route::get('branchQrCode', BranchQrCodeController::class)
            ->name('branchQrCode');
    });