<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(route('dashboard'));
});

Route::get('/dashboard', 'HomeController@index')->name('dashboard');

Route::get('/queue-monitor/{branch_id}', 'DirectQueueController@monitor')
    ->name('queue-monitor')
    ->middleware('signed');

Route::get('/unauthorized', function () {
    return 'Unauthorized';
})->name('unauthorized');

Route::namespace('AdminBranch')
    ->prefix('admin-branch')
    ->middleware('auth', 'checkAdminBranch')
    ->name('admin-branch.')
    ->group(function () {
        Route::get('profile', 'HomeController@edit')->name('profile');
        Route::put('profile', 'HomeController@update')->name('profile.update');

        Route::get('/dashboard', 'HomeController@index')->name('dashboard');

        Route::get('/branch-qr-code', 'BranchQrCodeController')->name('branch-qr-code');
        
        Route::get('/queue-monitor', 'HomeController@directQueueMonitor')
            ->name('queue-monitor')
            ->middleware('checkDirectQueue');

        Route::prefix('/export')->name('export.')->group(function () {
            Route::get('/appointment', 'HomeController@exportExcel')
                ->name('appointment')
                ->middleware('checkAppointmentQueue');
            Route::get('/exhibition', 'HomeController@exportExcelExhibition')
                ->name('exhibition')
                ->middleware('exhibitionPermissionIsValid');
        });
        
        Route::prefix('/product-guide')->name('product-guide.')->group(function () {
            Route::get('/queue-configuration', 'BranchConfigGuideController')
                ->name('queue-configuration');
            Route::get('/customer', 'CustomerGuideController')
                ->name('customer');
            Route::get('/slot-time', 'SlotTimeGuideController')
                ->name('slot-time');
        });

        Route::prefix('/branch-information')->name('branch-information.')->group(function () {
            Route::get('/profile', 'BranchController@profile')->name('profile');
            Route::get('/location', 'BranchController@location')->name('location');
            Route::put('/', 'BranchController@update')->name('update');
        });

        Route::prefix('/branch-configuration')->name('branch-configuration.')->group(function () {
            Route::resource('department', 'DepartmentController');
            Route::resource('service', 'ServiceController');
            Route::resource('service.slot', 'SlotController')->shallow()->middleware('checkAppointmentQueue');

            Route::resource('schedule', 'ScheduleController')->except('show');
            Route::get('/schedule/list', 'ScheduleController@templateIndex')->name('schedule.template.index');
            Route::put('/schedule/list', 'ScheduleController@templateUpdate')->name('schedule.template.update');

            Route::resource('workstation', 'WorkstationController');
            Route::resource('workstation.workstation-service', 'WorkstationServiceController');

            Route::put('/user/restore', 'UserController@restore')->name('user.restore');
            Route::post('/user/reset-password/{user}', 'UserController@resetPassword')->name('user.reset-password');
            Route::resource('user', 'UserController');

            Route::get('feature', 'FeatureController@index')
                ->name('feature')
                ->middleware('checkDirectQueue');
            Route::put('feature', 'BranchConfigurationController@update')
                ->name('feature.update')
                ->middleware('checkDirectQueue');

            Route::get('queue-monitor', 'TVDisplayConfigurationController@index')->name('queue-monitor');
            Route::put('queue-monitor/{branch}', 'TVDisplayConfigurationController@update')->name('queue-monitor.update');
        });

        Route::prefix('/report')->name('report.')->group(function () {
            Route::get('/daily/appointment', 'ReportController@daily')
                ->name('daily.appointment')
                ->middleware('checkAppointmentQueue');
            Route::get('/monthly/appointment', 'ReportController@appointmentMonthly')
                ->name('monthly.appointment')
                ->middleware('checkAppointmentQueue');

            Route::get('/daily/onsite', 'ReportController@directQueueDaily')
                ->name('daily.onsite')
                ->middleware('checkDirectQueue');
            Route::get('/monthly/onsite', 'ReportController@directQueueMonthly')
                ->name('monthly.onsite')
                ->middleware('checkDirectQueue');
            
            Route::get('/daily/exhibition', 'ReportController@exhibitionDaily')
                ->name('daily.exhibition')
                ->middleware('exhibitionPermissionIsValid');
            Route::get('/monthly/exhibition', 'ReportController@exhibitionMonthly')
                ->name('monthly.exhibition')
                ->middleware('exhibitionPermissionIsValid');

            Route::get('/customer-satisfaction', 'ReportController@customerSatisfaction')
                ->name('customer-satisfaction');
        });
    });

Route::resource('registrationBranch', 'RegistrationBranchController')->only(['store', 'edit']);
Route::get('/register/success', 'RegistrationBranchController@afterRegister')->name('registrationBranch.afterRegister');
Route::get('/register/verified', 'RegistrationBranchController@afterVerified')->name('registrationBranch.afterVerified');

Route::get('/vct/reset-password/{user_id}', 'AdminBranch\UserController@reset')->name('adminBranch.user.reset');
Route::put('/vct/reset-password/{user_id}', 'AdminBranch\UserController@updatePassword')->name('adminBranch.user.password.update');

Auth::routes();

// Appointment Status
Route::get('/appointment/status/{id}', 'AppointmentController@status')->name('appointment.status');
Route::get('/queue-caller/{directQueue}', 'QueueCallerController@call')
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
    Route::middleware('checkAdminBranchPassword')->group(function () {
        Route::get('directQueue/monitor', 'HomeController@directQueueMonitor')->name('directQueue.monitor')->middleware('checkDirectQueue');
        Route::get('qr', 'HomeController@qr')->name('qr');

        // Branch Configuration routes
        Route::get('branchConfiguration', 'BranchConfigurationController@edit')->name('branchConfiguration.edit')->middleware('checkDirectQueue');
        Route::put('branchConfiguration', 'BranchConfigurationController@update')->name('branchConfiguration.update')->middleware('checkDirectQueue');
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
    Route::get('directQueue/workstationServices', 'DirectQueueController@workstationServices')->middleware('checkDirectQueue');
    Route::resource('directQueue', 'DirectQueueController')->middleware('checkDirectQueue');
    Route::post('directQueue/onServed', 'DirectQueueController@onServed')->middleware('checkDirectQueue');
    Route::post('directQueue/onRecall', 'DirectQueueController@onRecall')->middleware('checkDirectQueue');
    Route::post('directQueue/onRequeue', 'DirectQueueController@onRequeue')->middleware('checkDirectQueue');
    Route::post('directQueue/onEndServed', 'DirectQueueController@onEndServed')->middleware('checkDirectQueue');
    Route::post('directQueue/onNoShow', 'DirectQueueController@onNoShow')->middleware('checkDirectQueue');
    Route::post('directQueue/onTransfer', 'DirectQueueController@onTransfer')->middleware('checkDirectQueue');

    // Report routes
    Route::get('report/daily', 'ReportController@daily')->name('report.daily');
    Route::get('report/directQueue/daily', 'ReportController@directQueueDaily')->name('report.directQueue.daily')->middleware('checkDirectQueue');
});

Route::group([], __DIR__ . '/web/exhibition.php');
Route::group([], __DIR__ . '/web/branch_qr_code.php');
Route::group([], __DIR__ . '/web/customer.php');

Route::get('search', 'SearchQueueController@index')->name('search.index');
Route::post('search', 'SearchQueueController@search')->name('search.search');

Route::get('scan', 'QRScannerController@index')->name('scan.index');

Route::get('{branch}', 'ShortURLController@customerWebUrl')->name('shortUrl.customerWebUrl');