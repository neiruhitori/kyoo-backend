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

Route::get('/vct/reset-password/{user_id}', 'AdminBranch\UserController@reset')->name('adminBranch.user.reset');
Route::put('/vct/reset-password/{user_id}', 'AdminBranch\UserController@updatePassword')->name('adminBranch.user.password.update');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Appointment Status
Route::get('/appointment/status/{id}', 'AppointmentController@status')->name('appointment.status');

// Direct Queue Monitor
Route::get('/direct-queue/monitor/{branch_id}', 'DirectQueueController@monitor')
    ->name('directQueue.monitor')
    ->middleware('signed');

Route::get('/queue-caller', 'QueueCallerController@call')
    ->name('queueCaller');

Route::get('/direct-queue/branch/{branch_id}/list', 'DirectQueueController@branchList')->name('directQueue.branch.list');

// success state from API
Route::get('/changeEmail/{id}', 'API\UserController@changeEmail')->name('user.changeEmail');
Route::get('/userRegister/{id}', 'API\UserController@userRegister')->name('user.userRegister');

Route::get('/display-images/{branch}', 'DisplayImageController@show')->name('display-images');

Route::namespace('Admin')->prefix('admin')->middleware('auth', 'checkAdmin')->name('admin.')->group(function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::get('profile', 'HomeController@edit')->name('profile.edit');
    Route::put('profile', 'HomeController@update')->name('profile.update');
    Route::get('export', 'HomeController@exportExcel')->name('export');

    // Industry Category
    Route::resource('industryCategory', 'IndustryCategoryController');

    // Branch
    Route::post('branchToken', 'BranchTokenController@store')->name('branchToken.store');
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

    Route::get('tvDisplayConfiguration', 'TVDisplayConfigurationController@index')->name('tvDisplayConfiguration.index');
    Route::put('tvDisplayConfiguration/{branch}', 'TVDisplayConfigurationController@update')->name('tvDisplayConfiguration.update');

    Route::middleware('checkAdminBranchPassword')->group(function () {
        Route::get('export', 'HomeController@exportExcel')->name('export')->middleware('checkAppointmentQueue');
        Route::get('directQueue/monitor', 'HomeController@directQueueMonitor')->name('directQueue.monitor')->middleware('checkDirectQueue');
        Route::get('qr', 'HomeController@qr')->name('qr');

        // Branch routes
        Route::get('branch/profile', 'BranchController@profile')->name('branch.profile');
        Route::get('branch/location', 'BranchController@location')->name('branch.location');
        Route::put('branch', 'BranchController@update')->name('branch.update');

        // Branch Configuration routes
        Route::get('branchConfiguration', 'BranchConfigurationController@edit')->name('branchConfiguration.edit')->middleware('checkDirectQueue');
        Route::put('branchConfiguration', 'BranchConfigurationController@update')->name('branchConfiguration.update')->middleware('checkDirectQueue');

        // Department routes
        Route::resource('department', 'DepartmentController');

        // Schedule routes
        Route::get('/schedule/list', 'ScheduleController@templateIndex')->name('schedule.template.index');
        Route::put('/schedule/list', 'ScheduleController@templateUpdate')->name('schedule.template.update');
        Route::resource('schedule', 'ScheduleController')->except('show');

        // Service routes
        Route::resource('service', 'ServiceController');

        // Slot routes
        Route::resource('service.slot', 'SlotController')->shallow()->middleware('checkAppointmentQueue');

        // Workstation routes
        Route::resource('workstation', 'WorkstationController');
        Route::resource('workstation.workstationService', 'WorkstationServiceController');

        // Counter routes
        Route::put('user/restore', 'UserController@restore')->name('user.restore');
        Route::post('user/resetPassword/{user}', 'UserController@resetPassword')->name('user.resetPassword');
        Route::resource('user', 'UserController');

        // Report routes
        Route::get('report/daily', 'ReportController@daily')->name('report.daily')->middleware('checkAppointmentQueue');
        Route::get('report/appointment/monthly', 'ReportController@appointmentMonthly')->name('report.appointment.monthly')->middleware('checkAppointmentQueue');
        Route::get('report/directQueue/daily', 'ReportController@directQueueDaily')->name('report.directQueue.daily')->middleware('checkDirectQueue');
        Route::get('report/directQueue/monthly', 'ReportController@directQueueMonthly')->name('report.directQueue.monthly')->middleware('checkDirectQueue');
        Route::get('report/customerSatisfaction', 'ReportController@customerSatisfaction')->name('report.customerSatisfaction');
    });
});

Route::namespace('CS')->prefix('cs')->middleware('auth', 'checkCS')->name('cs.')->group(function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::put('appointment/{appointment}', 'HomeController@updateAppointment')->name('appointment.update')->middleware('checkAppointmentQueue');
    Route::get('mini-report', 'HomeController@miniReport')->name('miniReport');
    Route::get('qr', 'HomeController@qr')->name('qr');

    // Appointment
    Route::get('appointment/create', 'HomeController@createAppointment')->name('appointment.create')->middleware('checkAppointmentQueue');
    Route::post('appointment/create', 'HomeController@storeAppointment')->name('appointment.store')->middleware('checkAppointmentQueue');

    // Direct Queue
    Route::get('directQueue/monitor', 'DirectQueueController@monitor')->name('directQueue.monitor')->middleware('checkDirectQueue');
    Route::resource('directQueue', 'DirectQueueController')->middleware('checkDirectQueue');
    Route::post('directQueue/onServed', 'DirectQueueController@onServed')->middleware('checkDirectQueue');
    Route::post('directQueue/onRecall', 'DirectQueueController@onRecall')->middleware('checkDirectQueue');
    Route::post('directQueue/onRequeue', 'DirectQueueController@onRequeue')->middleware('checkDirectQueue');
    Route::post('directQueue/onEndServed', 'DirectQueueController@onEndServed')->middleware('checkDirectQueue');
    Route::post('directQueue/onNoShow', 'DirectQueueController@onNoShow')->middleware('checkDirectQueue');
    Route::post('directQueue/onTransfer', 'DirectQueueController@onTransfer')->middleware('checkDirectQueue');
    Route::get('directQueue/workstationServices', 'DirectQueueController@workstationServices')->middleware('checkDirectQueue');

    // Report routes
    Route::get('report/daily', 'ReportController@daily')->name('report.daily');
    Route::get('report/directQueue/daily', 'ReportController@directQueueDaily')->name('report.directQueue.daily')->middleware('checkDirectQueue');
});

Route::group([], __DIR__ . '/web/exhibition.php');
Route::group([], __DIR__ . '/web/feature.php');
Route::group([], __DIR__ . '/web/branch_config_guide.php');
Route::group([], __DIR__ . '/web/customer_guide.php');
Route::group([], __DIR__ . '/web/slot_time_guide.php');
Route::group([], __DIR__ . '/web/branch_qr_code.php');
Route::group([], __DIR__ . '/web/customer.php');

Route::get('search', 'SearchQueueController@index')->name('search.index');
Route::post('search', 'SearchQueueController@search')->name('search.search');

Route::get('scan', 'QRScannerController@index')->name('scan.index');

Route::get('{branch}', 'ShortURLController@customerWebUrl')->name('shortUrl.customerWebUrl');