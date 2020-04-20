<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(route('home'));
});

Route::get('/unauthorized', function () {
    return 'unauthorized';
});

Route::get('/register/branch', 'BranchController@register')->name('branch.register');
Route::post('/register/branch', 'BranchController@store')->name('branch.store');
Route::get('/register/branch/success', 'BranchController@afterRegister')->name('branch.afterRegister');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::namespace('Admin')->prefix('admin')->middleware('auth', 'checkAdmin')->name('admin.')->group(function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::get('profile', 'HomeController@edit')->name('profile.edit');
    Route::put('profile', 'HomeController@update')->name('profile.update');

    // Industry Category
    Route::resource('industryCategory', 'IndustryCategoryController');

    // Branch
    Route::get('branch/verify', 'BranchController@verifyList')->name('branch.verify.index');
    Route::put('branch/verify/{branch}', 'BranchController@doVerify')->name('branch.verify.update');
    Route::resource('branch', 'BranchController');

    // Schedule Template
    Route::resource('scheduleTemplate', 'ScheduleTemplateController');
});