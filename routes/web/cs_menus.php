<?php

use Illuminate\Support\Facades\Route;

Route::namespace('CS\FeatureMenus')
    ->prefix('cs')
    ->middleware(['auth', 'checkCS'])
    ->name('cs.feature-menus.')
    ->group(function () {
        // This writes for menu features for cs
        Route::prefix('/monitoring')->name('monitoring.')->group(function () {
            Route::get('/department', 'MonitoringController@department')->name('department');
            Route::get('/department/{id}', 'MonitoringController@getDataDepartement')->name('department.getDataDepartement');
            Route::get('/department/{id}/service', 'MonitoringController@getServiceByDepartment')->name('department.service');

            Route::get('/service', 'MonitoringController@service')->name('service');
            Route::get('/service/{id}', 'MonitoringController@getDataService')->name('service.getDataService');
        });

        Route::prefix('/workstation-service')->name('workstation-service.')->group(function () {
            Route::get('/', 'WorkstationServiceController@index')->name('index');
            Route::post('/', 'WorkstationServiceController@store')->name('store');
            Route::put('/{workstation_service}', 'WorkstationServiceController@update')->name('update');
            Route::get('/create', 'WorkstationServiceController@create')->name('create');
            Route::get('{workstation_service}/edit', 'WorkstationServiceController@edit')->name('edit');
            Route::delete('{workstation_service}/destroy', 'WorkstationServiceController@destroy')->name('destroy');
        });

    });



