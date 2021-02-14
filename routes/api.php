<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// region routes
Route::get('allProvince', 'API\RegionController@allProvince');
Route::get('allRegency', 'API\RegionController@allRegency');
Route::get('regency/{province}', 'API\RegionController@regencyByProvince');

// user routes
Route::post('user/register', 'API\UserController@register');
Route::post('user/login', 'API\UserController@login');
Route::post('user/socialMedia', 'API\UserController@socialMedia');
Route::post('user/logout', 'API\UserController@logout');

// industry category routes
Route::get('industry-category', 'API\IndustryCategoryController@index');

// branch routes
Route::get('branch/city/{regency_id}', 'API\BranchController@getAllByCityId');
Route::get('branch/keyword/{keyword}', 'API\BranchController@getAllByKeyword');
Route::get('branch/industry-category/{industry_category_id}', 'API\BranchController@getAllByIndustryCategory');
Route::get('branch/{branch}', 'API\BranchController@show');

// service routes
Route::get('service/branch/{branch_id}', 'API\ServiceController@getAllByBranchId');

// slot routes
Route::post('slot', 'API\SlotController@index');

Route::middleware(['auth:api'])->group(function () {
    // user routes
    Route::get('user', 'API\UserController@detail');
    Route::put('user/update-profile', 'API\UserController@update');
    Route::put('user/update-password', 'API\UserController@updatePassword');
    Route::put('user/update-avatar', 'API\UserController@updateAvatar');
    Route::get('upcoming', 'API\AppointmentController@upcomingCombine');

    // appointment routes
    Route::post('appointment', 'API\AppointmentController@store');
    Route::get('appointment/{appointment}', 'API\AppointmentController@show');
    Route::get('appointment', 'API\AppointmentController@index');
    Route::get('appointment-history', 'API\AppointmentController@history');
    Route::post('appointment/{appointment}/feedback', 'API\AppointmentController@feedback');
    Route::get('appointment-upcoming', 'API\AppointmentController@upcoming');

    // direct queue routes
    Route::get('direct-queue/{directQueue}', 'API\DirectQueueController@show');
    Route::get('direct-queue-upcoming', 'API\DirectQueueController@upcoming');
    Route::post('direct-queue/{direct_queue}/feedback', 'API\DirectQueueController@feedback');
    Route::post('direct-queue', 'API\DirectQueueController@store');

    // favorite routes
    Route::get('favorite', 'API\FavoriteController@index');
    Route::post('favorite', 'API\FavoriteController@store');
    Route::post('favorite-delete', 'API\FavoriteController@destroy');

    // notification routes
    Route::get('notification', 'API\NotificationController@index');
});

// guest can be get the data
Route::get('direct-queue-by-branch/{branch}', 'API\DirectQueueController@index');