<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth', 'checkCS')
    ->namespace('CS')
    ->prefix('/cs/record-sound')
    ->group(function ()  {
        Route::view('/record-sound', 'cs.record-sound')->name('cs.record-sound');
    });

Route::middleware('auth', 'checkAdminBranch')
    ->namespace('AdminBranch')
    ->prefix('/admin-branch/service-quality')
    ->name('admin-branch.service-quality.')
    ->group(function () {
        Route::view('/recordings', 'adminBranch.service-quality.recordings')->name('recordings');
    });