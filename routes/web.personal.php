<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequestController;

/*
|--------------------------------------------------------------------------
| PERSONAL ROUTES
|--------------------------------------------------------------------------
| Employee-facing (dashboard, profile, attendance, schedules, leaves,
| settings, requests, devotions, notifications views).
|--------------------------------------------------------------------------
*/

// Employee (non-admin) area — web + auth + employee
Route::middleware(['web', 'auth', 'employee'])->group(function () {
    Route::get('personal/dashboard', 'Personal\\PersonalDashboardController@index');

    // profile
    Route::get('personal/profile/view', 'Personal\\PersonalProfileController@index')->name('myProfile');
    Route::get('personal/profile/edit', 'Personal\\PersonalProfileController@profileEdit');
    Route::post('personal/profile/update', 'Personal\\PersonalProfileController@profileUpdate');

    // attendance
    Route::get('personal/attendance/view', 'Personal\\PersonalAttendanceController@index');
    Route::get('get/personal/attendance', 'Personal\\PersonalAttendanceController@getPA');

    // schedules
    Route::get('personal/schedules/view', 'Personal\\PersonalSchedulesController@index');
    Route::get('get/personal/schedules', 'Personal\\PersonalSchedulesController@getPS');

    // leaves
    Route::get('personal/leaves/view', 'Personal\\PersonalLeavesController@index')->name('viewPersonalLeave');
    Route::get('personal/leaves/edit/{id}', 'Personal\\PersonalLeavesController@edit')->whereNumber('id');
    Route::post('personal/leaves/update', 'Personal\\PersonalLeavesController@update');
    Route::post('personal/leaves/request', 'Personal\\PersonalLeavesController@requestL');
    // Use DELETE instead of GET for destructive action
    Route::delete('personal/leaves/{id}', 'Personal\\PersonalLeavesController@delete')->whereNumber('id');
    Route::get('get/personal/leaves', 'Personal\\PersonalLeavesController@getPL');
    Route::get('view/personal/leave', 'Personal\\PersonalLeavesController@viewPL');

    // settings + user
    Route::get('personal/settings', 'Personal\\PersonalSettingsController@index');
    Route::get('personal/update-user', 'Personal\\PersonalAccountController@viewUser')->name('changeUser');
    Route::get('personal/update-password', 'Personal\\PersonalAccountController@viewPassword')->name('changePass');
    Route::post('personal/update/user', 'Personal\\PersonalAccountController@updateUser');
    Route::post('personal/update/password', 'Personal\\PersonalAccountController@updatePassword');
});

// Personal requests alias
Route::middleware(['auth'])->get('personal/requests/view', [RequestController::class, 'index']);

// Personal Devotion
Route::post('personal/devotion/post', [App\Http\Controllers\DevotionController::class, 'store']);
Route::get('personal/devotion/view', [App\Http\Controllers\DevotionController::class, 'viewDevotions']);
Route::get('get/personal/devotion', [App\Http\Controllers\DevotionController::class, 'getPersonalDevotions']);

// Personal notifications page (view)
Route::get('personal/notifications', function(){ return view('personal.personal-notifications-view'); })
    ->name('personal.notifications')->middleware('auth');

// Site Dashboard (non-admin overview)
use App\Http\Controllers\DashboardController as SiteDashboardController;
Route::get('dashboard-overview', [SiteDashboardController::class, 'showAttendanceOverview'])->name('dashboard.overview');

// Creative Volunteer Dashboard
Route::get('personal/creative/dashboard', [App\Http\Controllers\Personal\CreativeController::class, 'dashboard'])->name('personal.creative.dashboard')->middleware('auth');
Route::get('personal/creative/tasks/{id}', [App\Http\Controllers\Personal\CreativeController::class, 'show'])->name('personal.creative.task.show')->middleware('auth');
Route::post('personal/creative/tasks/{id}/status', [App\Http\Controllers\Personal\CreativeController::class, 'updateTaskStatus'])->name('personal.creative.task.updateStatus')->middleware('auth');
