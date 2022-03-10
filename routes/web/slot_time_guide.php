<?php

Route::namespace('AdminBranch')
    ->middleware('auth', 'checkAdminBranch', 'checkAdminBranchPassword')
    ->prefix('adminBranch')
    ->name('adminBranch.')
    ->group(function () {
        Route::get('slotTimeGuide', SlotTimeGuideController::class)
            ->name('slotTimeGuide');
    });