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

Route::get('/google', 'RegistrationBranchController@redirectToProvider');
Route::get('/google/callback', 'RegistrationBranchController@handleProviderCallback');

Route::get('/', function () {
    return redirect(route('home'));
});

Route::get('/unauthorized', function () {
    return 'unauthorized';
})->name('unauthorized');

Route::resource('registrationBranch', 'RegistrationBranchController')->only(['store', 'edit']);
Route::get('/register/success', 'RegistrationBranchController@afterRegister')->name('registrationBranch.afterRegister');
Route::get('/register/verified', 'RegistrationBranchController@afterVerified')->name('registrationBranch.afterVerified');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Appointment Status
Route::get('/appointment/status/{id}', 'AppointmentController@status')->name('appointment.status');

// success state from API
Route::get('/changeEmail/{id}', 'API\UserController@changeEmail')->name('user.changeEmail');
Route::get('/userRegister/{id}', 'API\UserController@userRegister')->name('user.userRegister');

Route::namespace('Admin')->prefix('admin')->middleware('auth', 'checkAdmin')->name('admin.')->group(function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::get('profile', 'HomeController@edit')->name('profile.edit');
    Route::put('profile', 'HomeController@update')->name('profile.update');
    Route::get('export', 'HomeController@exportExcel')->name('export');

    // Industry Category
    Route::resource('industryCategory', 'IndustryCategoryController');

    // Branch
    Route::resource('branch', 'BranchController');
    Route::resource('registrationBranch', 'RegistrationBranchController')->only(['index', 'show', 'update', 'destroy']);

    // Branch Type
    Route::resource('branchType', 'BranchTypeController');

    // Schedule Template
    Route::resource('scheduleTemplate', 'ScheduleTemplateController');
});

Route::namespace('AdminBranch')->prefix('adminBranch')->middleware('auth', 'checkAdminBranch')->name('adminBranch.')->group(function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::get('profile', 'HomeController@edit')->name('profile.edit');
    Route::put('profile', 'HomeController@update')->name('profile.update');

    Route::middleware('auth', 'checkAdminBranchPassword')->group(function () {
        Route::get('export', 'HomeController@exportExcel')->name('export');
        Route::get('qr', 'HomeController@qr')->name('qr');

        // Branch routes
        Route::get('branch', 'BranchController@edit')->name('branch.edit');
        Route::put('branch', 'BranchController@update')->name('branch.update');

        // Branch Configuration routes
        Route::get('branchConfiguration', 'BranchConfigurationController@edit')->name('branchConfiguration.edit');
        Route::put('branchConfiguration', 'BranchConfigurationController@update')->name('branchConfiguration.update');

        // Department routes
        Route::resource('department', 'DepartmentController');

        // Schedule routes
        Route::get('/schedule/list', 'ScheduleController@templateIndex')->name('schedule.template.index');
        Route::put('/schedule/list', 'ScheduleController@templateUpdate')->name('schedule.template.update');
        Route::resource('schedule', 'ScheduleController')->except('show');

        // Service routes
        Route::resource('service', 'ServiceController');

        // Slot routes
        Route::resource('service.slot', 'SlotController')->shallow();

        // Workstation routes
        Route::resource('workstation', 'WorkstationController');
        Route::resource('workstation.workstationService', 'WorkstationServiceController');

        // Counter routes
        Route::put('user/restore', 'UserController@restore')->name('user.restore');
        Route::resource('user', 'UserController');

        // Report routes
        Route::get('report/daily', 'ReportController@daily')->name('report.daily');
    });
});

Route::namespace('CS')->prefix('cs')->middleware('auth', 'checkCS')->name('cs.')->group(function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::put('appointment/{appointment}', 'HomeController@updateAppointment')->name('appointment.update');
    Route::get('mini-report', 'HomeController@miniReport')->name('miniReport');

    // Appointment
    Route::get('appointment/create', 'HomeController@createAppointment')->name('appointment.create');
    Route::post('appointment/create', 'HomeController@storeAppointment')->name('appointment.store');

    // Direct Queue
    Route::get('directQueue/monitor', function(){
        return view('cs.directQueue.monitor');
    });
    Route::resource('directQueue', 'DirectQueueController');
    Route::post('directQueue/onCall', 'DirectQueueController@onCall');
    Route::post('directQueue/onRecall', 'DirectQueueController@onRecall');

    // Report routes
    Route::get('report/daily', 'ReportController@daily')->name('report.daily');
});