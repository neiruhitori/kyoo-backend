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
Route::get('regency/city/{id}', 'API\RegionController@regencyById');

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
Route::get('branch/type/{branch}', 'API\BranchController@getBranchType');

Route::get('branch/{branch}', 'API\BranchController@show');
Route::get('branch/{branch}/schedules', 'API\BranchController@getSchedules');
Route::get('branch/{branch}/term-condition', 'API\BranchController@getTermsConditions');
Route::get('branch/{branch}/promotions', 'API\BranchController@getPromotions');
Route::get('branch/{branch_id}/holiday', 'API\HolidayController@getHolidayByBranchId');

// service categories routes
Route::get('service-category/branch/{branch_id}', 'API\ServiceCategoryController@getAllByBranchId');

// service routes
Route::get('service/branch/{branch_id}', 'API\ServiceController@getAllByBranchId');
Route::get('service/{service_id}', 'API\ServiceController@getById');
Route::get('service/department/{department_id}', 'API\ServiceController@getByDepartmentId');

// workstation routes
Route::get('workstation/department/{department_id}', 'API\WorkstationController@getByDepartmentId');

// vct routes
Route::get('vct/department/{department_id}', 'API\VctController@getByDepartmentId');

// slot routes
Route::post('slot', 'API\SlotController@index');

Route::post('appointment', 'API\AppointmentController@store')->middleware('throttle:appointment');
Route::get('appointment/{appointment}', 'API\AppointmentController@show');
Route::post('appointment/{appointment}/feedback', 'API\AppointmentController@feedback');
Route::patch('appointment/{id}/cancel', 'API\AppointmentController@cancel');

Route::post('exhibition', 'API\ExhibitionController@store');
Route::get('exhibition/{exhibition}', 'API\ExhibitionController@show');

Route::post('direct-queue', 'API\DirectQueueController@store')->middleware('cookie.clientid');
Route::get('direct-queue/{directQueue}', 'API\DirectQueueController@show');
Route::post('direct-queue/{direct_queue}/feedback', 'API\DirectQueueController@feedback');

Route::post('appointment-onsite', 'API\AppointmentOnsiteController@store')->middleware('cookie.clientid');
Route::get('appointment-onsite/{appointmentOnsite}', 'API\AppointmentOnsiteController@show');
Route::get('appointment-onsite/{branch}/slots', 'API\AppointmentOnsiteController@slots');
Route::get('appointment-onsite/direct-queue-by-branch/{branch}', 'API\AppointmentOnsiteController@index');

Route::middleware(['auth:api'])->group(function () {
    // user routes
    Route::get('user', 'API\UserController@detail');
    Route::put('user/update-profile', 'API\UserController@update');
    Route::put('user/update-password', 'API\UserController@updatePassword');
    Route::put('user/update-avatar', 'API\UserController@updateAvatar');
    Route::get('upcoming', 'API\AppointmentController@upcomingCombine');
    Route::patch('user/{id}/personal-access-token', 'API\UserController@updatePersonalAccessToken');

    // appointment routes
    Route::get('appointment', 'API\AppointmentController@index');
    Route::get('appointment-history', 'API\AppointmentController@history');
    Route::get('appointment-upcoming', 'API\AppointmentController@upcoming');

    // direct queue routes
    Route::get('direct-queue-upcoming', 'API\DirectQueueController@upcoming');

    // favorite routes
    Route::get('favorite', 'API\FavoriteController@index');
    Route::post('favorite', 'API\FavoriteController@store');
    Route::post('favorite-delete', 'API\FavoriteController@destroy');

    // notification routes
    Route::get('notification', 'API\NotificationController@index');

    // corporate routes
    Route::get('corporates/{id}/branches', 'API\CorporateController@getCorporateBranches');
});

// guest can be get the data
Route::get('direct-queue-by-branch/{branch}', 'API\DirectQueueController@index');

// Search queue
Route::get('search', 'API\SearchQueueController');

/**
 * External API Routes
 */
Route::namespace('API\External')->prefix('external')->middleware('external.checkBranchToken')->group(function () {
    Route::get('service', 'ServiceController@index');
    Route::get('service/{service}/slot', 'ServiceController@slot');

    Route::post('direct-queue', 'DirectQueueController@store');

    Route::post('appointment', 'AppointmentController@store');
});

// Verify token
Route::get('introspect', function () {
    return response()->noContent();
})->middleware('auth:api');
