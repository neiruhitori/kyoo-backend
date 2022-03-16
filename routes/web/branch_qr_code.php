<?php

Route::namespace('AdminBranch')
    ->middleware('auth', 'checkAdminBranch', 'checkAdminBranchPassword')
    ->prefix('adminBranch')
    ->name('adminBranch.')
    ->group(function () {
        Route::get('branchQrCode', BranchQrCodeController::class)
            ->name('branchQrCode');
    });


Route::namespace('CS')
    ->middleware('auth', 'checkCS')
    ->prefix('cs')
    ->name('cs.')
    ->group(function () {
        Route::get('branchQrCode', BranchQrCodeController::class)
            ->name('branchQrCode');
    });