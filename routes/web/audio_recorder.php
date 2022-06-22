<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth', 'checkCS', 'access:Voice Recording')
    ->namespace('CS')
    ->prefix('/cs/record-sound')
    ->group(function ()  {
        Route::get('/', 'RecordSoundController@index')->name('cs.record-sound.index');
        Route::post('/', 'RecordSoundController@store')->name('cs.record-sound.store');
    });

Route::middleware('auth', 'checkAdminBranch', 'access:Voice Recording')
    ->namespace('AdminBranch')
    ->prefix('/admin-branch/service-quality')
    ->name('admin-branch.service-quality.')
    ->group(function () {
        Route::get('/recordings', 'RecordingController@index')->name('recordings.index');
        Route::get('/recordings/all', 'RecordingController@getRecordings')->name('recordings.get-all');
    });