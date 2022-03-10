<?php

// use BranchConfigGuideController;

Route::namespace('AdminBranch')
    ->middleware('auth', 'checkAdminBranch', 'checkAdminBranchPassword')
    ->prefix('adminBranch')
    ->name('adminBranch.')
    ->group(function () {
        Route::get('branchConfigGuide', BranchConfigGuideController::class)
            ->name('branchConfigGuide');
    });