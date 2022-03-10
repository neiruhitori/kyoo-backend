<?php

Route::namespace('AdminBranch')
    ->middleware('auth', 'checkAdminBranch', 'checkAdminBranchPassword')
    ->prefix('adminBranch')
    ->name('adminBranch.')
    ->group(function () {
        Route::get('feature', 'FeatureController@index')
            ->name('feature');
    });
