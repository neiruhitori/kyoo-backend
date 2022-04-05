<?php

Route::namespace('AdminBranch')
    ->middleware('auth', 'checkAdminBranch', 'checkAdminBranchPassword')
    ->prefix('adminBranch')
    ->name('adminBranch.')
    ->group(function () {
        Route::get('customer-guide', CustomerGuideController::class)
            ->name('customerGuide');
    });