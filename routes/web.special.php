<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controllers referenced here
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserTrackingController;
use App\Http\Controllers\MeetingLinkController as PublicMeetingLinkController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\Admin\MeetingAttendanceController;

/*
|--------------------------------------------------------------------------
| SPECIAL ROUTES
|--------------------------------------------------------------------------
| Session ping, triggers, wellness modules, notifications, tracking,
| requests (user), meeting attendance QR, auth scaffolding, language,
| permission-denied, wellness (user+admin), small admin followups.
| (Anything that doesn't fit cleanly in Public/Personal/Admin/Radio/MR)
|--------------------------------------------------------------------------
*/

// Session ping (AJAX only)
Route::get('/session/ping', function () {
    // Only respond to AJAX requests to prevent accidental navigation
    if (!request()->ajax() && !request()->wantsJson()) {
        return redirect('/')->with('error', 'Invalid request');
    }
    
    return auth()->check()
        ? response()->json(['ok' => true, 'timestamp' => now()->toISOString()])
        : response()->json(['error' => 'unauthenticated'], 401);
})->name('session.ping');

// Trigger flow (throttled)
Route::get('/trigger/flow', [\App\Http\Controllers\RadioDashboard\DashboardController::class, 'triggerFlow'])
    ->name('trigger.flow')
    ->middleware(['throttle:10,1']); // rate-limit: 10/min

/*
|-------------------------------------------------------
| WELLNESS (user + admin) — kept exactly as provided
|-------------------------------------------------------
*/
use App\Http\Controllers\Wellness\WellnessController;
use App\Http\Controllers\Wellness\AdminWellnessController;
use App\Http\Controllers\Wellness\FollowupController;
use App\Http\Controllers\Wellness\FollowupsAdminController;
use App\Http\Controllers\Wellness\FollowupAttachmentController;

Route::middleware(['auth'])->group(function () {

    // Leader
    Route::get('/wellness/followups', [FollowupController::class, 'index'])->name('wellness.followups.index');
    Route::post('/wellness/followups', [FollowupController::class, 'store'])->name('wellness.followups.store');
    Route::get('/wellness/followups/{id}', [FollowupController::class, 'show'])->name('wellness.followups.show');

    Route::post('/wellness/followups/{id}/due', [FollowupController::class, 'updateDueDate'])->name('wellness.followups.updateDue');
    Route::post('/wellness/followups/{id}/assign/me', [FollowupController::class, 'selfAssign'])->name('wellness.followups.selfAssign');

    // Private attachment
    Route::get('/wellness/followups/{id}/attachments/download', [FollowupAttachmentController::class, 'download'])->name('wellness.followups.attachments.download');

    // Admin (add your own admin middleware/gate)
    Route::middleware(['can:assign-followup,followup_id'])->group(function() {
        // If you prefer a global 'admin' middleware, replace with it.
    });

    Route::get('/admin/followups', [FollowupsAdminController::class, 'index'])->name('admin.followups.index')->middleware('auth'); // add your admin middleware
    Route::post('/admin/followups/{id}/assign', [FollowupsAdminController::class, 'assign'])->name('admin.followups.assign')->middleware('auth'); // add admin middleware
});

Route::middleware(['auth'])
    ->prefix('wellness/admin')
    ->name('wellness.admin.')
    ->group(function () {
        Route::get('/dashboard',            [AdminWellnessController::class, 'index'])->name('dashboard');
        Route::post('/cases/{id}/assign',   [AdminWellnessController::class, 'assign'])->name('cases.assign');
    });

Route::prefix('wellness')->name('wellness.')->middleware(['auth'])->group(function () {
    Route::get('/',                 [WellnessController::class, 'dashboard'])->name('dashboard');

    // Check-ins
    Route::post('/checkins',        [WellnessController::class, 'storeCheckin'])->name('checkins.store');

    // Cases
    Route::get('/cases',            [WellnessController::class, 'casesIndex'])->name('cases.index');
    Route::get('/cases/{id}',       [WellnessController::class, 'caseShow'])->name('cases.show');

    // Case actions / transitions
    Route::post('/cases/{id}/assign',        [WellnessController::class, 'assign'])->name('cases.assign');
    Route::post('/cases/{id}/transition',    [WellnessController::class, 'transition'])->name('cases.transition');
    Route::post('/cases/{id}/propose-close', [WellnessController::class, 'proposeClose'])->name('cases.proposeClose');
    Route::post('/cases/{id}/approve-close', [WellnessController::class, 'approveClose'])->name('cases.approveClose'); // overseer only

    Route::get('/members.json', [\App\Http\Controllers\Wellness\WellnessController::class, 'membersJson'])
             ->name('members.json');
});

/*
|-------------------------------------------------------
| MEETING ATTENDANCE QR (non-admin view route)
|-------------------------------------------------------
*/
// Public QR Scan Flow (auth + throttle)
Route::middleware(['throttle:10,1'])
    ->match(['get','post'], 'meeting-attendance/{token}', [MeetingAttendanceController::class, 'resolve'])
    ->where('token', '[A-Za-z0-9\-_]+')
    ->name('meeting.attendance.resolve');

    // routes/web.php
Route::get('/api/attendance/suggest', [\App\Http\Controllers\Admin\MeetingAttendanceController::class, 'suggest'])
  ->middleware(['auth','admin'])
  ->name('attendance.suggest');

/*
|-------------------------------------------------------
| REQUESTS (user-facing) — kept as-is
|-------------------------------------------------------
*/
use App\Http\Controllers\RequestController;

Route::middleware(['auth'])->group(function () {
    Route::get('my-requests',           [RequestController::class, 'index']);
    Route::get('request/create',        [RequestController::class, 'create']);
    Route::post('request/store',        [RequestController::class, 'store']);
});

// Alias (personal requests view)
Route::middleware(['auth'])->get('personal/requests/view', [RequestController::class, 'index']);

/*
|-------------------------------------------------------
| NOTIFICATIONS (dropdown/json/pages) — kept as-is
|-------------------------------------------------------
*/
// Dropdown (page fragment)
Route::get('/notifications/dropdown', [NotificationController::class, 'dropdown'])
    ->name('notifications.dropdown')
    ->middleware('auth');

// JSON endpoints (list, count, read, read-all)
Route::middleware('auth')->group(function () {
    Route::get('notifications',                 [NotificationController::class, 'list'])->name('notifications.list');
    Route::get('notifications/count',           [NotificationController::class, 'count'])->name('notifications.count');
    Route::post('notifications/{id}/read',      [NotificationController::class, 'read'])->whereNumber('id')->name('notifications.read');
    Route::post('notifications/read-all',       [NotificationController::class, 'readAll'])->name('notifications.readAll');
});

// Notifications pages (views)
Route::get('notifications-text', function(){ return view('admin.notifications-text'); })
    ->name('notifications.page')->middleware('auth');

// Send Mail / Notifications (duplicates deduped; keep one set exactly as given)
Route::get('sendMail', [\App\Http\Controllers\SendMailController::class, 'index'])->name('send.mail');
Route::post('sendMail', [\App\Http\Controllers\SendMailController::class, 'sendNotification'])->name('send.notification');
Route::get('sent-notifications', [\App\Http\Controllers\SendMailController::class, 'listNotifications'])->name('sent.notifications');
Route::delete('delete-notification/{notification_code}', [\App\Http\Controllers\SendMailController::class, 'deleteNotification'])->name('delete.notification');
Route::get('duplicate-notification/{notification_code}', [\App\Http\Controllers\SendMailController::class, 'duplicateNotification'])->name('duplicate.notification');

Route::middleware(['auth'])->group(function () {
    Route::get('notifications/fetch',        [\App\Http\Controllers\SendMailController::class, 'fetchNotifications'])->name('notifications.fetch');
    Route::post('notifications/mark-all-read', [\App\Http\Controllers\SendMailController::class, 'markAllNotificationsRead'])->name('notifications.markAllAsRead');
});

/*
|-------------------------------------------------------
| TRACKING (user activity)
|-------------------------------------------------------
*/
Route::post('track-action', [UserTrackingController::class, 'trackAction'])->middleware('throttle:60,1');
Route::get('report/user-activity', [UserTrackingController::class, 'showReport'])
    ->middleware(['auth','admin'])
    ->name('report.user-activity');

/*
|-------------------------------------------------------
| AUTH / LANGUAGE / REDIRECTS
|-------------------------------------------------------
*/
Route::middleware('auth')->get('/home', 'Personal\\PersonalDashboardController@index')
    ->name('home');

// Auth routes (replaces Auth::routes() to remove laravel/ui dependency)
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;

// Login / Logout
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->middleware('throttle:10,1');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Registration
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Password Reset
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware('throttle:10,1')
    ->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])
    ->middleware('throttle:10,1')
    ->name('password.update');

// Email Verification
Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('email/resend', [VerificationController::class, 'resend'])
    ->middleware('throttle:6,1')
    ->name('verification.resend');

// Language + logout + permission pages
Route::get('lang/{locale}', 'LanguageController@lang');
// Use POST logout; avoid GET logout route
Route::view('account-disabled', 'errors.account-disabled')->name('disabled');
Route::view('account-not-found', 'errors.account-not-found')->name('notfound');

Route::get('permission-denied', function (Request $request) {
    $msg = $request->query('m', 'You don’t have permission to access this page.');
    session()->flash('denied', $msg);

    // Try to go back; if not possible, go home
    $prev = url()->previous();
    return $prev && $prev !== $request->fullUrl()
        ? redirect($prev)
        : redirect('/');
})->name('denied');
