<?php

use Illuminate\Support\Facades\Route;
use App\Notification;
use App\Appointment;
use App\FcmToken;
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
Route::get('fcm', function(){
    $recipients = FcmToken::get()->pluck('token')->toArray();
    fcm()
        ->to($recipients) // $recipients must an array
        ->priority('high')
        ->timeToLive(0)
        ->notification([
            'title' => 'KYOO',
            'body' => $text
        ])
        ->send();
    return $recipients;
});
Route::get('test', function(){
$date = date('Y-m-d');
$hourStart = date('H:00:00', strtotime('1 hour'));
$hourEnd = date('H:00:00', strtotime('2 hour'));

$appointments = Appointment::whereHas('Slot', function($query) use ($hourStart, $hourEnd){
    $query->whereBetween('start_time', [$hourStart, $hourEnd]);
})->where('date', $date)->where('status', 'book')->get();

foreach ($appointments as $appointment) {
    $text = "Please be prepare for your appointment in {$appointment->Slot->Service->Branch->name} at {$appointment->Slot->start_time} - {$appointment->Slot->end_time}";
    Notification::create([
        'user_id' => $appointment->user_id,
        'text' => $text
    ]);
    $recipients = FcmToken::whereUserId($appointment->user_id)->pluck('token')->toArray();
    fcm()
        ->to($recipients) // $recipients must an array
        ->priority('high')
        ->timeToLive(0)
        ->notification([
            'title' => 'KYOO',
            'body' => $text
        ])
        ->send();
}
});

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

        // Schedule routes
        Route::get('/schedule/list', 'ScheduleController@templateIndex')->name('schedule.template.index');
        Route::put('/schedule/list', 'ScheduleController@templateUpdate')->name('schedule.template.update');
        Route::resource('schedule', 'ScheduleController')->except('show');

        // Service routes
        Route::resource('service', 'ServiceController');

        // Slot routes
        Route::resource('service.slot', 'SlotController')->shallow();

        // Counter routes
        Route::put('user/restore', 'UserController@restore')->name('user.restore');
        Route::resource('user', 'UserController');
    });
});

Route::namespace('CS')->prefix('cs')->middleware('auth', 'checkCS')->name('cs.')->group(function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::put('appointment/{appointment}', 'HomeController@updateAppointment')->name('appointment.update');
});