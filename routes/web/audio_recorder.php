<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth', 'checkCS')
    ->namespace('CS')
    ->prefix('/cs/conversations')
    ->name('cs.conversations.')
    ->group(function ()  {
        Route::get('/', function () {
            return view('cs.conversations.index');
        })->name('index');
        
        Route::get('/record', function () {
            return view('cs.conversations.record');
        })->name('record');

        Route::get('/messages', 'AudioRecordingController@index');
        Route::post('/messages', 'AudioRecordingController@store');
    });