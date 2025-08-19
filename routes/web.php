<?php

use App\Http\Controllers\Admin\WhatsappConfigurationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect(route('dashboard'));
});

//to differ visitor
Route::group(['middleware' => ['localization','setlocaleIP']], function(){

Auth::routes();

});

Route::get('/change-locale/{locale}','LocalizationController@setLang')->name('change.locale');

//where method prevent conflict from page customer
Route::middleware('localization')
->where(['locale' => 'id|en'])
->group(function(){


Route::get('/unauthorized', function () {
    return 'Unauthorized';
})->name('unauthorized');

Route::get('/dashboard', 'HomeController@index')->name('dashboard');

Route::middleware('signed')->group(function () {
    Route::get('/direct-queues/signage/{branch_id}', 'DirectQueueController@monitor')
        ->name('directQueues.signage');
});

Route::get('/branches/{branch_id}/signage/appointments', 'AppointmentSignageController@index')
    ->name('appointments.signage');
Route::get('/branches/{branch_id}/appointments', 'AppointmentSignageController@getAppointments')
    ->name('branches.appointments');

Route::get('/appointments/{appointment_id}/call',  'AppointmentCallController@call');

Route::namespace('AdminBranch')
    ->prefix('admin-branch')
    ->middleware('auth', 'checkAdminBranch', 'setTimeZone')
    ->name('admin-branch.')
    ->group(function () {
        // User Profile
        Route::get('profile', 'HomeController@edit')->name('profile');
        Route::put('profile', 'HomeController@update')->name('profile.update');

        // Dashboard
        Route::get('/dashboard', 'HomeController@index')->name('dashboard');
        Route::get('/getDataChart', 'HomeController@getDataChart');

        // List Appointment Onsite
        Route::get('/appointment-onsites', 'AppointmentOnsiteController@index')->name('appointment-onsites');
        Route::get('/appointment-onsites/slots', 'AppointmentOnsiteController@getSlots')->name('appointment-onsites.slot');
        Route::get('/appointment-onsites/slot/{appointmentOnsite}', 'AppointmentOnsiteController@editSlot')->name('appointment-onsites.slot.edit');
        Route::put('/appointment-onsites/{appointmentOnsite}', 'AppointmentOnsiteController@update')->name('appointment-onsites.update');

        // QR Code Poster
        Route::get('/branch-qr-code', 'BranchQrCodeController')->name('branch-qr-code');

        // Queue Monitor
        Route::get('/queue-monitor', 'HomeController@queueMonitor')
            ->name('queue-monitor')
            ->middleware('access:Web Signage TV');

        // Export
        Route::prefix('/export')->name('export.')->group(function () {
            Route::get('/appointment', 'HomeController@exportExcel')
                ->name('appointment')
                ->middleware('checkAppointmentQueue');
            Route::get('/exhibition', 'HomeController@exportExcelExhibition')
                ->name('exhibition')
                ->middleware('exhibitionPermissionIsValid');
        });

        // Product Guide
        Route::prefix('/product-guide')->name('product-guide.')->group(function () {
            Route::get('/queue-configuration', 'BranchConfigGuideController')
                ->name('queue-configuration');
            Route::get('/customer', 'CustomerGuideController')
                ->name('customer');
            Route::get('/slot-time', 'SlotTimeGuideController')
                ->name('slot-time');
        });

        // Branch Information
        Route::prefix('/branch-information')->name('branch-information.')->group(function () {
            Route::get('/profile', 'BranchController@profile')->name('profile');
            Route::get('/location', 'BranchController@location')->name('location');
            Route::put('/', 'BranchController@update')->name('update');
        });

        // Branch Configuration
        Route::prefix('/branch-configuration')->name('branch-configuration.')->group(function () {
            Route::resource('department', 'DepartmentController');

            Route::resource('service-category', 'ServiceCategoryController');

            Route::resource('service', 'ServiceController');
            Route::resource('service.slot', 'SlotController')->shallow()->middleware('checkAppointmentQueue');
            
            Route::get('service/{id}/enable', 'SubServiceController@enableDisable')->name('service.enableDisable');
            Route::get('service/{id}/assign', 'SubServiceController@assign')->name('service.assign');
            Route::get('service/{id}/add/sub-service', 'SubServiceController@add')->name('service.add.sub-service');
            Route::get('service/{id}/edit/sub-service', 'SubServiceController@editSubService')->name('service.edit.sub-service');
            Route::put('service/{id}/edit/sub-service', 'SubServiceController@syncSubService');
            Route::post('service/{id}/add/sub-service', 'SubServiceController@submitAdd');
            Route::delete('service/{id}/remove-sub', 'SubServiceController@removeSubService')->name('service.remove.sub-service');
            
            Route::get('sub-service/create', 'SubServiceController@create')->name('sub-service.create');
            Route::get('sub-service/{id}/edit', 'SubServiceController@edit')->name('sub-service.edit');
            Route::post('sub-service/store', 'SubServiceController@store')->name('sub-service.store');
            Route::put('sub-service/{id}/update', 'SubServiceController@update')->name('sub-service.update');
            Route::delete('sub-service/{id}/kill', 'SubServiceController@destroy')->name('sub-service.destroy');

            Route::get('holiday/template', 'HolidayController@template')->name('holiday.template.create');
            Route::post('holiday/template', 'HolidayController@storeAll')->name('holiday.template.store');
            Route::get('holiday/create', 'HolidayController@create')->name('holiday.create');
            Route::post('holiday', 'HolidayController@store')->name('holiday.store');
            Route::delete('holiday/{holiday_id}', 'HolidayController@destroy')->name('holiday.destroy');

            Route::resource('schedule', 'ScheduleController')->except('show');

            Route::resource('workstation', 'WorkstationController');
            Route::resource('workstation.workstation-service', 'WorkstationServiceController');

            Route::put('/user/restore', 'UserController@restore')->name('user.restore');
            Route::post('/user/reset-password/{user}', 'UserController@resetPassword')->name('user.reset-password');
            Route::resource('user', 'UserController');
            Route::get('user/edit-workstation/{user}', 'UserController@editWorkstation')->name('user.edit-workstation');
            Route::put('user/update-workstation/{user}', 'UserController@updateWorkstation')->name('user.update-workstation');

            Route::get('/menu-portal', 'PortalMenuController@edit')->name('menu-portal');
            Route::put('/menu-portal', 'PortalMenuController@update')->name('menu-portal.update');
            Route::put('/menu-portal/service', 'PortalMenuController@formServiceUpdate')->name('menu-portal.service.update');

            Route::put('/device-account/restore', 'DeviceAccountController@restore')->name('device-account.restore');
            Route::post('/device-account/reset-password/{user}', 'DeviceAccountController@resetPassword')->name('device-account.reset-password');
            Route::resource('device-account', 'DeviceAccountController');

            Route::get('feature', 'FeatureController@index')->name('feature');
            Route::post('feature/checkInConfig','BranchConfigurationController@checkInConfig')->name('feature.checkIn');
            Route::put('feature', 'BranchConfigurationController@update')->name('feature.update');

            Route::get('queue-monitor', 'TVDisplayConfigurationController@index')
                ->name('queue-monitor')
                ->middleware('access:Web Signage TV');
            Route::put('queue-monitor/{branch}', 'TVDisplayConfigurationController@update')
                ->name('queue-monitor.update')
                ->middleware('access:Web Signage TV');
            Route::put('queue-monitor/update-layout/{branch}','TVDisplayConfigurationController@updateLayout')
                ->name('queue-monitor.update-layout')
                ->middleware('access:Web Signage TV');
            Route::put('/queue-monitor/update-token/{branch}', 'TVDisplayConfigurationController@updateToken')
                ->name('queue-monitor.update-token')
                ->middleware('access:Web Signage TV');
            Route::put('/queue-monitor/update-custom-layout/{branch}', 'TVDisplayConfigurationController@updateCustomLayout')
                ->name('queue-monitor.update-custom-layout')
                ->middleware('access:Web Signage TV');

            Route::get('webkiosk', 'WebkioskConfigurationController@index')
                ->name('webkiosk')
                ->middleware('access:Webkiosk');
            Route::put('webkiosk/{branch}', 'WebkioskConfigurationController@update')
                ->name('webkiosk.update')
                ->middleware('access:Webkiosk');
            Route::put('/webkiosk/update-token/{branch}', 'WebkioskConfigurationController@updateToken')
            ->name('webkiosk.update-token')
            ->middleware('access:Webkiosk');;
            Route::put('webkiosk/active_menus/{branch}', 'WebkioskConfigurationController@updateActiveMenus')
                ->name('webkiosk.active-menus.update')
                ->middleware('access:Webkiosk');

            Route::get('terms-conditions', 'TermsConditionsController@index')->name('terms-conditions.index');
            Route::get('terms-conditions/get', 'TermsConditionsController@get')->name('terms-conditions.get');
            Route::put('terms-conditions', 'TermsConditionsController@update')->name('terms-conditions.store');
         
            Route::prefix('/promotions')
                ->name('promotions.')
                ->middleware('access:Promosi')
                ->group(function () {
                Route::get('/', 'PromotionsController@index')->name('index');
                Route::get('/image/create', 'PromotionsController@createImage')->name('image.create');
                Route::post('/image', 'PromotionsController@storeImage')->name('image.store');
                Route::delete('/image/{id}', 'PromotionsController@destroyImage')->name('image.destroy');
                Route::get('/text/create', 'PromotionsController@createText')->name('text.create');
                Route::post('/text', 'PromotionsController@storeText')->name('text.store');
                Route::delete('/text/{id}', 'PromotionsController@destroyText')->name('text.destroy');
            });
        });

         Route::get('/customer-feedback', 'CustomerFeedbackController@index')->name('feedback.index');
         Route::get('/customer-feedback/create', 'CustomerFeedbackController@create')->name('feedback.create');
         Route::get('/customer-feedback/edit/{id}', 'CustomerFeedbackController@edit')->name('feedback.edit');
         Route::post('/customer-feedback', 'CustomerFeedbackController@save')->name('feedback.save');
         Route::post('/customer-feedback/create', 'CustomerFeedbackController@addQuestion')->name('feedback.store');
         Route::put('/customer-feedback/edit/{id}', 'CustomerFeedbackController@update')->name('feedback.update');
         Route::delete('/customer-feedback/delete/{id}', 'CustomerFeedbackController@delete')->name('feedback.delete');
        // Service Quality
        Route::middleware('access:Voice Recording')
            ->prefix('/service-quality')
            ->name('service-quality.')
            ->group(function () {
                Route::get('/audio-recording', 'AudioRecordingController@index')->name('audio-recording.index');
                Route::get('/audio-recording/all', 'AudioRecordingController@getAll')->name('audio-recording.get-all');
            });

        // Monitoritng
        Route::prefix('/monitoring')->name('monitoring.')->group(function () {
            Route::get('/department', 'DepartmentMonitoringController@index')->name('department');
            Route::get('/department/{id}', 'DepartmentMonitoringController@getData')->name('department.show');
            Route::get('/department-detail/{id}/max-wait', 'DepartmentMonitoringController@maxWait')->name('department.maxwait');
            Route::get('/department-detail/{id}/max-service', 'DepartmentMonitoringController@maxService')->name('department.maxservice');

            Route::get('/department/{id}/service', 'ServiceMonitoringController@getServiceByDepartment')->name('department.service');
            Route::get('/service', 'ServiceMonitoringController@index')->name('service');
            Route::get('/service/{id}', 'ServiceMonitoringController@show')->name('service.show');
        });

        // Report
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
            Route::get('/appointment-onsites', 'ReportController@appointmentOnsite')
                ->name('appointment-onsites')
                ->middleware('checkDirectQueue');

            Route::get('/daily/exhibition', 'ReportController@exhibitionDaily')
                ->name('daily.exhibition')
                ->middleware('exhibitionPermissionIsValid');
            Route::get('/monthly/exhibition', 'ReportController@exhibitionMonthly')
                ->name('monthly.exhibition')
                ->middleware('exhibitionPermissionIsValid');

            Route::get('/customer-satisfaction', 'ReportController@customerSatisfaction')
                ->name('customer-satisfaction');

            Route::get('/department', 'ReportingDepartmentController@index')->name('department');
            Route::get('/department/all', 'ReportingDepartmentController@getAll')->name('department.all');
            Route::get('/department/pdf', 'ReportingDepartmentController@exportToPdf')->name('department.pdf');
            Route::get('/department/excel', 'ReportingDepartmentController@exportToExcel')->name('department.excel');
            Route::get('/department/chart', 'ChartDepartmentController@index')->name('department.chart');
            Route::get('/department/chart/all', 'ChartDepartmentController@getAll')->name('department.chart.all');

            Route::get('/service', 'ReportingServiceController@index')->name('service.index');
            Route::get('/service/all', 'ReportingServiceController@getAll')->name('service.all');
            Route::get('/service/pdf', 'ReportingServiceController@exportToPdf')->name('service.pdf');
            Route::get('/service/excel', 'ReportingServiceController@exportToExcel')->name('service.excel');
            Route::get('/service/chart', 'ChartServiceController@index')->name('service.chart');
            Route::get('/service/chart/all', 'ChartServiceController@getAll')->name('service.chart.all');

            Route::get('/service-distribution', 'ReportingServiceDistributionController@index')->name('service-distribution');
            Route::get('/service-distribution/all', 'ReportingServiceDistributionController@getAll')->name('service-distribution.all');
            Route::get('/service-distribution/pdf', 'ReportingServiceDistributionController@exportToPdf')->name('service-distribution.pdf');
            Route::get('/service-distribution/excel', 'ReportingServiceDistributionController@exportToExcel')->name('service-distribution.excel');

            Route::get('/workstation', 'ReportingWorkstationController@index')->name('workstation');
            Route::get('/workstation/all', 'ReportingWorkstationController@getAll')->name('workstation.all');
            Route::get('/workstation/pdf', 'ReportingWorkstationController@exportToPdf')->name('workstation.pdf');
            Route::get('/workstation/excel', 'ReportingWorkstationController@exportToExcel')->name('workstation.excel');
            Route::get('/workstation/chart', 'ChartWorkstationController@index')->name('workstation.chart');
            Route::get('/workstation/chart/all', 'ChartWorkstationController@getAll')->name('workstation.chart.all');

            Route::get('/vct', 'ReportingVctController@index')->name('vct');
            Route::get('/vct/all', 'ReportingVctController@getAll')->name('vct.all');
            Route::get('/vct/pdf', 'ReportingVctController@exportToPdf')->name('vct.pdf');
            Route::get('/vct/excel', 'ReportingVctController@exportToExcel')->name('vct.excel');
            Route::get('/vct/chart', 'ChartVctController@index')->name('vct.chart');
            Route::get('/vct/chart/all', 'ChartVctController@getAll')->name('vct.chart.all');
        });
        
    
        
        Route::get('/billing','BillingController@index')->name('billing');
        Route::get('/billing/{id}/print','BillingController@print')->name('billing.print');
        Route::post('/subscription','BillingController@storeInvoice');
        Route::get('/subscription','BillingController@invoiceForm')->name('subscription');
       //hanya untuk front-end
        Route::get('/get_Billing_Prices','BillingController@getBilling');
        //hanya untuk front-end

        Route::prefix('/cs')->name('cs.')->group(function () {
            Route::resource('access', 'CSAccessController');
        });
    });

Route::resource('registrationBranch', 'RegistrationBranchController')->only(['store', 'edit']);
Route::get('/register/success', 'RegistrationBranchController@afterRegister')->name('registrationBranch.afterRegister');
Route::get('/register/verified', 'RegistrationBranchController@afterVerified')->name('registrationBranch.afterVerified');

Route::get('/vct/reset-password/{user_id}', 'AdminBranch\UserController@reset')->name('adminBranch.user.reset');
Route::put('/vct/reset-password/{user_id}', 'AdminBranch\UserController@updatePassword')->name('adminBranch.user.password.update');

Route::get('/device-account/reset-password/{user_id}', 'AdminBranch\DeviceAccountController@reset')->name('adminBranch.device-account.reset');
Route::put('/device-account/reset-password/{user_id}', 'AdminBranch\DeviceAccountController@updatePassword')->name('adminBranch.device-account.password.update');

// Auth::routes();

// Appointment Status
Route::get('/appointment/status/{id}', 'AppointmentController@status')->name('appointment.status');

Route::get('/queue-caller/{directQueue}', 'QueueCallerController@call')->name('queueCaller');

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

    // Branch Configuration
    // Route::resource('/branchConfiguration', 'WhatsappConfigurationController')->only(['index', 'show', 'update']);
    Route::get('/branchConfiguration', [WhatsappConfigurationController::class, 'index'])->name('branchConfiguration.index');
    Route::get('/branchConfiguration/whatsapp/{id}', [WhatsappConfigurationController::class, 'show'])->name('whatsappConfiguration.show');
    Route::get('/branchConfiguration/gtm/{id}', [WhatsappConfigurationController::class, 'showGTM'])->name('gtmConfiguration.show');
    Route::put('/branchConfiguration/whatsapp/{id}', [WhatsappConfigurationController::class, 'update'])->name('whatsappConfiguration.update');
    Route::put('/branchConfiguration/gtm/{id}', [WhatsappConfigurationController::class, 'updateGTM'])->name('gtmConfiguration.update');
    
    Route::get('/branch/{id}/license', 'BranchLicenseController@index')->name('branch.license');
    Route::put('/branch/{id}/license', 'BranchLicenseController@update')->name('branch.license.update');
    
    Route::get('/branch/{id}/generateSecretToken', 'BranchLicenseController@generateToken')->name('branch.license.generateToken');
    Route::post('/branch/{id}/store/webhook-url', 'BranchLicenseController@storeWebhookUrl')->name('branch.license.webhook');
    
    //Billing SA
    Route::get('billing', 'BillingController@index')->name('billing.index');
    Route::get('billing/{id}/print', 'BillingController@print')->name('billing.print');
    Route::get('/branch/{id}/billing', 'BillingController@show')->name('branch.billing');
    Route::get('/billing-configuration', 'BillingController@list')->name('billing.config');
    Route::get('/billing-configuration/items', 'BillingController@itemList')->name('billing.item');
    Route::get('/billing-configuration/items/{id}/edit', 'BillingController@itemEdit')->name('billing.item.edit');
    Route::put('/billing-configuration/items/{id}/edit', 'BillingController@itemUpdate');
    Route::get('/billing-configuration/create', 'BillingController@create')->name('billing-prices.create');
    Route::post('/billing-configuration/create', 'BillingController@priceStore');
    Route::get('/billing-configuration/{id}', 'BillingController@priceEdit')->name('billing-prices.update');
    Route::put('/billing-configuration/{id}', 'BillingController@priceUpdate');
    
    // Branch Type
    Route::resource('branchType', 'BranchTypeController');

    // Schedule Template
    Route::resource('scheduleTemplate', 'ScheduleTemplateController');

    // Corporate
    Route::get('corporate', 'CorporateController@index')->name('corporate.index');
    Route::get('corporate/create', 'CorporateController@create')->name('corporate.create');
    Route::get('corporate/options', 'CorporateController@createOptions')->name('corporate.options');
    Route::get('corporate/copy', 'CorporateController@copyFromBranch')->name('corporate.copy');
    Route::post('corporate/copy', 'CorporateController@storeCopiedBranch');
    Route::get('corporate/branch', 'CorporateController@findBranchByName')->name('corporate.branch');
    Route::get('corporate/branch/{id}', 'CorporateController@findBranchById')->name('corporate.branch.show');
    Route::get('corporate/edit/{id}', 'CorporateController@edit')->name('corporate.edit');
    Route::get('corporate/{id}', 'CorporateController@show')->name('corporate.show');
    Route::post('corporate', 'CorporateController@store')->name('corporate.store');
    Route::patch('corporate/{id}', 'CorporateController@update')->name('corporate.update');

    // Corporate Branch
    Route::get('corporate/{corporateId}/branch', 'CorporateBranchController@index')->name('corporate.branch.index');
    Route::get('corporate/{corporateId}/branch/create', 'CorporateBranchController@create')->name('corporate.branch.create');
    Route::get('corporate/{corporateId}/branch/get', 'CorporateBranchController@findByName')->name('corporate.branch.get');
    Route::post('corporate/{corporateId}/branch', 'CorporateBranchController@store')->name('corporate.branch.store');
    Route::delete('corporate/{corporateId}/branch/{branchId}', 'CorporateBranchController@destroy')->name('corporate.branch.destroy');
    Route::get('corporate/{corporateId}/branch/options', 'CorporateBranchController@createOptions')->name('corporate.branch.options');


    Route::get('waSession', 'WaSessionController@index')->name('waSession.index');
    Route::get('waSession/qr', 'WaSessionController@getQr')->name('waSession.qr');
    Route::get('waSession/me', 'WaSessionController@getProfile')->name('waSession.me');
});

Route::namespace('AdminBranch')->prefix('adminBranch')->middleware('auth', 'checkAdminBranch', 'setTimeZone', 'checkAdminBranchPassword')->name('adminBranch.')->group(function () {
    Route::middleware('checkAdminBranchPassword')->group(function () {
        Route::get('directQueue/monitor', 'HomeController@directQueueMonitor')->name('directQueue.monitor')->middleware(['checkDirectQueue', 'access:Web Signage TV']);
        Route::get('qr', 'HomeController@qr')->name('qr');

        // Branch Configuration routes
        Route::get('branchConfiguration', 'BranchConfigurationController@edit')->name('branchConfiguration.edit')->middleware('checkDirectQueue');
        Route::put('branchConfiguration', 'BranchConfigurationController@update')->name('branchConfiguration.update')->middleware('checkDirectQueue');
    });
});

Route::namespace('AdminCorporate')
    ->prefix('admin-corporate')
    ->middleware('auth')
    ->name('adminCorporate.')
    ->group(function () {
        Route::get('/', 'HomeController@index')->name('home');

        Route::get('/monitoring', 'MonitoringController@index')->name('monitoring');
        Route::get('/monitoring/branches', 'MonitoringController@monitorBranches')->name('monitoring.branches.index');
        Route::get('/monitoring/branches/{id}/services', 'MonitoringController@monitorServices')->name('monitoring.branches.services');

        Route::get('/report/customer-satisfaction', 'CustomerSatisfactionReportController@index')->name('report.customerSatisfaction.index');
        Route::get('/report/branch', 'BranchReportController@index')->name('report.branch.index');
        Route::get('/report/service', 'ServiceReportController@index')->name('report.service.index');
        Route::get('/report/service/all', 'ServiceReportController@getReports')->name('report.service.all');
        Route::get('/report/service/pdf', 'ServiceReportController@exportToPdf')->name('report.service.pdf');
        Route::get('/report/service/excel', 'ServiceReportController@exportToExcel')->name('report.service.excel');
        Route::get('/report/service/chart', 'ServiceChartReportController@index')->name('report.service.chart');
        Route::get('/report/workstation', 'WorkstationReportController@index')->name('report.workstation.index');
        Route::get('/report/workstation/all', 'WorkstationReportController@getReports')->name('report.workstation.all');
        Route::get('/report/workstation/pdf', 'WorkstationReportController@exportToPdf')->name('report.workstation.pdf');
        Route::get('/report/workstation/excel', 'WorkstationReportController@exportToExcel')->name('report.workstation.excel');
    });

Route::namespace('CS')->prefix('cs')->middleware('auth', 'checkCS', 'setTimeZone')->name('cs.')->group(function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::put('appointment/{appointment}', 'HomeController@updateAppointment')->name('appointment.update')->middleware('checkAppointmentQueue');
    Route::get('mini-report', 'HomeController@miniReport')->name('miniReport');
    Route::get('qr', 'HomeController@qr')->name('qr');
    Route::get('workstation', 'HomeController@workstation')->name('workstation');
    Route::put('update-workstation/{user}', 'HomeController@updateWorkstation')->name('updateWorkstation');
    // Appointment Monitor
    Route::middleware('checkAppointmentQueue')->group(function () {
        Route::get('appointments/monitor', 'AppointmentMonitorController@index')->name('appointments.monitor');
        Route::get('appointments', 'AppointmentMonitorController@getAll')->name('appointments.getAll');
        Route::get('appointments/create', 'HomeController@createAppointment')->name('appointments.create');
        Route::post('appointments/create', 'HomeController@storeAppointment')->name('appointments.store');
        Route::patch('appointments/{id}/checkin', 'AppointmentMonitorController@checkIn')->name('appointments.checkin');
        Route::patch('appointments/{id}/no-show', 'AppointmentMonitorController@noShow')->name('appointments.noShow');
        Route::patch('appointments/{id}/served', 'AppointmentMonitorController@served')->name('appointments.served');
        Route::patch('appointments/{id}/end-served', 'AppointmentMonitorController@endServed')->name('appointments.endServed');
        Route::patch('appointments/{id}/cancel', 'AppointmentMonitorController@cancel')->name('appointments.cancel');
    });

    // Direct Queue
    Route::get('directQueue/monitor', 'DirectQueueController@monitor')->name('directQueue.monitor')->middleware('checkDirectQueue');
    Route::get('directQueue/workstationServices', 'DirectQueueController@workstationServices')->middleware('checkDirectQueue');
    Route::get('directQueue/allWorkstationServices', 'DirectQueueController@getAllWorkstationServiceByBranch')->middleware('checkDirectQueue');
    Route::resource('directQueue', 'DirectQueueController')->middleware('checkDirectQueue');
    Route::post('directQueue/onCall', 'DirectQueueController@onCall')->middleware('checkDirectQueue');
    Route::post('directQueue/onServed', 'DirectQueueController@onServed')->middleware('checkDirectQueue');
    Route::post('directQueue/onRecall', 'DirectQueueController@onRecall')->middleware('checkDirectQueue');
    Route::post('directQueue/onRequeue', 'DirectQueueController@onRequeue')->middleware('checkDirectQueue');
    Route::post('directQueue/onEndServed', 'DirectQueueController@onEndServed')->middleware('checkDirectQueue');
    Route::post('directQueue/onNoShow', 'DirectQueueController@onNoShow')->middleware('checkDirectQueue');
    Route::post('directQueue/onTransfer', 'DirectQueueController@onTransfer')->middleware('checkDirectQueue');
    Route::get('directQueue/getQRCode/{queue_id}', 'DirectQueueController@getQrCode')->middleware('checkDirectQueue');

    // Report routes
    Route::get('report/daily', 'ReportController@daily')->name('report.daily');
    Route::get('report/directQueue/daily', 'ReportController@directQueueDaily')->name('report.directQueue.daily')->middleware('checkDirectQueue');
    Route::get('report/directQueue/monthly', 'ReportController@directQueueMonthly')->name('report.directQueue.monthly')->middleware('checkDirectQueue');
    Route::get('report/directQueue/getMonthly', 'ReportController@getDirectQueueMonthly')->name('report.directQueue.getMonthly')->middleware('checkDirectQueue');
    Route::get('report/appointment-onsite', 'ReportController@appointmentOnsite')->name('report.directQueue.appointmentOnsite')->middleware('checkDirectQueue');

    // Voice Recorder
    Route::get('voice-recorder', 'VoiceRecorderController@index')->name('voiceRecorder.index');
    Route::post('voice-recorder', 'VoiceRecorderController@store')->name('voiceRecorder.store');

    // Future Appointments
    Route::get('appointment/future', 'FutureAppointmentController@index')->name('appointment.future.index');
    Route::get('appointment/future/get', 'FutureAppointmentController@getFutureAppointments')->name('appointment.future.get');
    Route::get('appointment/slots', 'FutureAppointmentController@getAppointmentSlots')->name('appointment.slots.index');
    Route::get('appointment/slots/{id}', 'FutureAppointmentController@showAppointmentSlot')->name('appointment.slots.get');

    // Holidays
    Route::get('holidays', 'HolidayController@getHolidaysByDate')->name('holidays');
});

Route::namespace('Device')->prefix('device')->middleware('auth', 'checkDevice', 'setTimeZone')->name('device.')->group(function () {
    Route::get('/', 'HomeController@index')->name('home');
});

Route::namespace('Device')->prefix('device')->name('device.')->group(function () {
    Route::get('directQueue/allWorkstationServices/{branch_id}', 'HomeController@getAllWorkstationServiceByBranch');

    Route::post('directQueue/store', 'HomeController@store');
    Route::post('directQueueByBookingCode/store', 'HomeController@storeByBookingCode');

    Route::get('web-kiosk-ui', 'HomeController@webKioskUI')->name('web-kiosk-ui');
    Route::get('web-monitor', 'HomeController@webMonitor')->name('web-monitor');

    Route::get('branch/{branch}/workstations', 'HomeController@workstationList')->name('branch.workstations');

    Route::get('branch/{branch_id}/queue', 'HomeController@directQueueList')->name('branch.queue');
    Route::get('branch/{branch_id}/queue/served', 'HomeController@directQueueServed')->name('branch.queue.served');
});

Route::group([], __DIR__ . '/web/exhibition.php');
Route::group([], __DIR__ . '/web/branch_qr_code.php');
Route::group([], __DIR__ . '/web/customer.php');
Route::group([], __DIR__ . '/web/cs_menus.php');

Route::get('search', 'SearchQueueController@index')->name('search.index');
Route::post('search', 'SearchQueueController@search')->name('search.search');

Route::get('scan', 'QRScannerController@index')->name('scan.index');

// Route::get('AddCategoryService/onlyAdMin', function(){
//        $branchOnsite = App\Branch::onsite()->where('license_expiration_date', '>', Illuminate\Support\Carbon::now())
//                         ->get();

//         foreach ($branchOnsite as $branch) {
//             $hasCategory = App\Models\ServiceCategory::where('branch_id', $branch->id)->exists();

//             if(!$hasCategory){
//                 App\Models\ServiceCategory::create([
//                     'name' => 'Service Category 1',
//                     'branch_id' => $branch->id
//                 ]);
//             }

//         }
// });
// Route::get('AddServiceToCategory/onlyAdMin', function(){
//             $branches = App\Branch::onsite()
//                 ->where('license_expiration_date', '>', Illuminate\Support\Carbon::now())
//                 ->with('Service')
//                 ->get();

//             foreach ($branches as $branch) {
//                 $firstCategory = App\Models\ServiceCategory::where('branch_id', $branch->id)->first();

//                 if (!$firstCategory) {
//                     continue;
//                 }

//                 foreach ($branch->Service as $service) {
//                     if (is_null($service->service_category_id)) {
//                         $service->service_category_id = $firstCategory->id;
//                         $service->save();
//                     }
//                 }
//             }
// });

// Route::get('testing', function(){
    
// });


}); //end of locale prefix
Route::get('feedback/{branchId}/{queueType}/{queueId}', 'FeedbackController@index')->name('feedback.mail')->middleware('signed');
Route::get('{branch}', 'ShortURLController@customerWebUrl')->name('shortUrl.customerWebUrl');