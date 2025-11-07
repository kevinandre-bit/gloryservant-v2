<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\VolunteersController;
use App\Http\Controllers\VolunteersActivityController;
use App\Http\Controllers\DevotionPublicController;
use App\Http\Controllers\ClockController;
/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
| - No authentication required (unless explicitly set on a route).
| - Landing pages, static pages, volunteer forms, public devotion submit,
|   clock-in utilities, redirects, and small-group endpoints (as defined).
| - Definitions are unchanged; only relocated.
|--------------------------------------------------------------------------
*/

// Landing / welcome
Route::get('/', function () {
    return redirect('/login'); // keep original redirect
});

// Volunteers (protected)
Route::middleware(['auth'])->group(function () {
    Route::get('volunteers',              [VolunteersController::class, 'index'])->name('volunteers.index');
    Route::get('volunteers/{id}/edit',    [VolunteersController::class, 'edit'])->whereNumber('id')->name('volunteers.edit');
    Route::put('volunteers/{id}',         [VolunteersController::class, 'update'])->whereNumber('id')->name('volunteers.update');
    Route::get('volunteers/{id}/activity',[VolunteersActivityController::class, 'index'])->whereNumber('id')->name('volunteers.activity');
});

// Public devotion submit (throttled)
Route::middleware('throttle:10,1')->group(function () {
    Route::get('devotion/submit', [DevotionPublicController::class, 'create'])->name('devotion.public.create');
    Route::post('devotion/submit', [DevotionPublicController::class, 'store'])->name('devotion.public.store');
    Route::get('/communication_pap_devotions', [DevotionPublicController::class, 'index'])->name('devotion.public.index');
});

// (This block appears twice in the original; keep duplicate for parity)
Route::middleware('throttle:10,1')->group(function () {
    Route::get('devotion/submit', [DevotionPublicController::class, 'create'])->name('devotion.public.create');
    Route::post('devotion/submit', [DevotionPublicController::class, 'store'])->name('devotion.public.store');
    Route::get('/communication_pap_devotions', [DevotionPublicController::class, 'index'])->name('devotion.public.index');
});

// Static pages
Route::get('monthly-digital-gift', 'MonthlyDigitalGiftController')->name('monthly-digital-gift.show');
Route::view('faq', 'faq');
Route::view('knowledge-base', 'knowledge-base');
Route::view('team-attendance', 'team-attendance');
Route::view('make-a-request', 'public.make-request')->name('public.make-request');

// Small Groups (as-is, no extra middleware added)
use App\Http\Controllers\SmallGroup\SmallGroupController;
Route::prefix('small-groups')->name('small.')->middleware(['auth'])->group(function () {
    Route::get('/',                          [SmallGroupController::class, 'index'])->name('index');
    Route::post('/',                         [SmallGroupController::class, 'storeGroup'])->name('store');
    Route::post('{group}/leader',            [SmallGroupController::class, 'updateLeader'])->whereNumber('group')->name('leader');
    Route::post('{group}/members',           [SmallGroupController::class, 'syncMembers'])->whereNumber('group')->name('sync');
    Route::delete('{group}/member/{person}', [SmallGroupController::class, 'removeMember'])->whereNumber('group')->whereNumber('person')->name('remove');
});

// Clock-in utilities
Route::get('clock', [ClockController::class, 'clock'])->name('clockin.show');

//Route::get('clockin', function () { return view('clockin.qr'); })->name('clockin.show');
Route::get('scan', 'ClockController@scanQr')->middleware('auth');

Route::get('check-clockin', function(Request $request) {
    $idno = $request->query('idno');
    if (! $idno) {
        return response()->json(['clocked_in' => false]);
    }

    $today = today()->toDateString();
    $exists = DB::table('tbl_people_attendance')
        ->where('idno', $idno)
        ->whereDate('date', $today)
        ->exists();

    return response()->json(['clocked_in' => (bool) $exists]);
})->middleware('throttle:30,1');

// Protect write endpoint (as-is, requires auth)
Route::post('attendance/addWebApp', 'ClockController@add')->middleware('auth');

// Redirect old /attendance/{token} → new /meeting-attendance/{token}
Route::get('attendance/{token}', function ($token) {
    return redirect()->route('meeting.attendance.auto', ['token' => $token], 301);
})->where('token', '[A-Za-z0-9\-_]+');

// Basic utilities / static files
Route::get('test-migration', function () { return 'Migration is working!'; });

Route::get('gloryservant-v2', function () {
    $file = public_path('gloryservant-v2/index.html');
    if (! file_exists($file)) {
        abort(404, "New UI not found at: {$file}");
    }
    return response()->file($file);
});

// Team attendance display page
use App\Http\Controllers\DisplayAttendanceController;
Route::get('display-attendance', [DisplayAttendanceController::class, 'index'])->middleware('auth')->name('team-attendance');
