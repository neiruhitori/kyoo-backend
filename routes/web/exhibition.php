<?php

use Illuminate\Support\Facades\Route;

Route::get('/exhibition/status/{id}', 'ExhibitionController@status')->name('exhibition.status');

Route::middleware('auth', 'checkCS')
    ->middleware('exhibitionPermissionIsValid')
    ->namespace('CS')
    ->prefix('cs/exhibition')
    ->name('cs.exhibition.')
    ->group(function () {
        Route::get('/create', 'HomeController@createExhibition')
            ->name('create');

        Route::post('/create', 'HomeController@storeExhibition')
            ->name('store');

        Route::post('/create', 'HomeController@storeExhibition')
            ->name('store');
        
        Route::put('/{exhibition}', 'HomeController@updateExhibition')
            ->name('update');
        
        Route::get('/report/daily', 'ReportController@exhibitionDaily')
            ->name('report.daily');
        
        Route::get('/report/monthly', 'ReportController@exhibitionMonthly')
            ->name('report.monthly');
    });