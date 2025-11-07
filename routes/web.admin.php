<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MeetingLinkController as AdminMeetingLinkController;
use App\Http\Controllers\MeetingLinkController as PublicMeetingLinkController;
use App\Http\Controllers\Admin\MeetingEventController;
use App\Http\Controllers\Admin\AdminDevotionController;
use App\Http\Controllers\Admin\AdminAsanaController;
use App\Http\Controllers\Admin\AdminAsanaPortfolioController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\Admin\MeetingAttendanceController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ComputerController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\DevotionReportController;
use App\Http\Controllers\AttendanceReportsController;
use App\Http\Controllers\AdminRequestController;
use App\Http\Controllers\Admin\ReportSetupController;
use App\Http\Controllers\Admin\ReportEntryController;
use App\Http\Controllers\Admin\ReportDashboardController;
use App\Http\Controllers\Admin\MemberReportController;

use App\Http\Controllers\Admin\WorkspaceController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\TaskController;

// --------------------------------------
// WORKSPACE MANAGEMENT MODULE
// --------------------------------------
Route::prefix('admin')->name('admin.')->group(function () {

    // ==== Workspaces ====
    Route::get('/workspaces', [WorkspaceController::class, 'index'])
        ->name('workspaces.index');
    Route::post('/workspaces', [WorkspaceController::class, 'store'])
        ->name('workspaces.store');
    Route::delete('/workspaces/{workspace}', [WorkspaceController::class, 'destroy'])
        ->name('workspaces.destroy');
    
    // ==== Workspace Sharing ====
    Route::post('/workspaces/{workspace}/share', [WorkspaceController::class, 'shareStore'])
        ->name('workspaces.share.store');
    Route::delete('/workspaces/{workspace}/share/{user}', [WorkspaceController::class, 'shareDestroy'])
        ->name('workspaces.share.destroy');

    // ==== Projects ====
    Route::post('/projects', [ProjectController::class, 'store'])
        ->name('projects.store');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])
        ->name('projects.destroy');

    // ==== Tasks ====
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('tasks/{task}/focus', [TaskController::class, 'addToFocus'])->name('tasks.focus.add');
    Route::delete('tasks/{task}/focus/{date}', [TaskController::class, 'removeFromFocus'])->name('tasks.focus.remove');

    // ==== Workload JSON feed (for workload view) ====
    Route::get('/workloads/{workspace}', [WorkspaceController::class, 'workload'])
        ->name('workspaces.workload');
});
/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (non-Radio)
|--------------------------------------------------------------------------
| Classic Admin area (dashboards, employees, attendance, schedules, leaves,
| users, roles, reports, settings, exports, imports, inventory, devotions,
| asana, requests, meeting links/attendance views).
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\RoleController; // adjust namespace if different

Route::middleware(['web','auth']) // add other middleware you use (e.g. 'can:roles-edit')
    ->group(function () {
        Route::post('/roles/assign-permission', [RoleController::class, 'assignPermissionToUser'])
            ->name('roles.assignPermissionToUser');
    });
Route::get('/newpage', function () {
    return view('newpage');
});
     
 Route::get('/admin/reports/entry/events/day', [ReportEntryController::class,'dayEvents'])
     ->name('admin.reports.entry.events.day');

// web.volunteer.php
Route::middleware(['auth'])->group(function () {
    Route::get('/volunteer', [VolunteerController::class, 'index'])->name('volunteer.dashboard');
});

// web.admin.php
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('admin/monthly-digital-gift', 'Admin\MonthlyDigitalGiftController@edit')->name('admin.monthly-digital-gift.edit');
    Route::post('admin/monthly-digital-gift', 'Admin\MonthlyDigitalGiftController@storeOrUpdate')->name('admin.monthly-digital-gift.store');
});

// web.radio.php
Route::middleware(['auth'])->group(function () {
    Route::get('/radio', [RadioController::class, 'index'])->name('radio.dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/portal', function () {
        return view('volunteers.portal');
    })->name('portal');
});
// routes/web.php

Route::prefix('admin/reports')->group(function () {
    // person profile (GET /admin/reports/people/{id})
    Route::get('/people/{id}', [MemberReportController::class, 'show'])
        ->whereNumber('id')
        ->name('admin.reports.people.show');

    // optional: if someone lands on /admin/reports/people (no id), redirect back
    Route::get('/people', function () {
        return redirect()->route('admin.reports.dashboard');
    });
});

Route::get('/admin/reports/people/{id}', [MemberReportController::class, 'show'])
    ->name('admin.reports.people.show');

Route::prefix('admin/reports')->middleware(['auth','admin'])->group(function () {
    Route::get('/dashboard', [ReportDashboardController::class, 'index'])
        ->name('admin.reports.dashboard');

    Route::get('/dashboard/export', [ReportDashboardController::class, 'export'])
        ->name('admin.reports.dashboard.export');

    Route::match(['GET','POST'], '/dashboard/save', [ReportDashboardController::class, 'save'])
        ->name('admin.reports.dashboard.save');
});


// Admin portal entry (auth)
use App\Http\Controllers\Admin\AdminPortalController;
Route::middleware('auth')->get('/admin-portal', [AdminPortalController::class, 'enter'])->name('admin.portal');

// Admin: Meeting links & analytics (auth)
Route::middleware(['auth'])->group(function () {
    Route::get('meeting-links',            [AdminMeetingLinkController::class, 'index'])->name('meetings.index');
    // Meeting links CRUD
    Route::post('meeting-links',            [AdminMeetingLinkController::class, 'store'])->name('meetings.store');
    Route::put('meeting-links/{id}',        [AdminMeetingLinkController::class, 'update'])->name('meetings.update');
    Route::delete('meeting-links/{id}',     [AdminMeetingLinkController::class, 'destroy'])->name('meetings.destroy');
    Route::post('meeting-categories',       [AdminMeetingLinkController::class, 'storeCategory'])->name('meetings.categories.store');
    Route::post('meeting-events',           [MeetingEventController::class, 'store'])->name('meeting-events.store');
    Route::put('meeting-events/{event}',    [MeetingEventController::class, 'update'])->name('meeting-events.update');
    Route::delete('meeting-events/{event}', [MeetingEventController::class, 'destroy'])->name('meeting-events.destroy');
    Route::delete('meeting-events/bulk-destroy', [MeetingEventController::class, 'bulkDestroy'])->name('meeting-events.bulk-destroy');
    Route::get('meeting-events/{event}/json', [MeetingEventController::class, 'show'])->name('meeting-events.show');
    // Admin pages for meeting sessions
    Route::get('meeting-sessions',           [MeetingEventController::class, 'calendar'])->name('meeting-sessions.index');
    Route::get('meeting-sessions/list',       [MeetingEventController::class, 'listView'])->name('meeting-sessions.list');
    Route::get('meeting-sessions/calendar',  [MeetingEventController::class, 'calendar'])->name('meeting-sessions.calendar');
    Route::get('meeting-session/calendar',   [MeetingEventController::class, 'calendar'])->name('meeting-session.calendar');
    Route::get('meeting-sessions/events',    [MeetingEventController::class, 'eventsJson'])->name('meeting-sessions.events');
    
    // Improved meeting system routes
    Route::get('meeting-links-improved',     [AdminMeetingLinkController::class, 'indexImproved'])->name('meetings.improved');
    Route::get('meeting-sessions-improved',  [MeetingEventController::class, 'indexImproved'])->name('meeting-sessions.improved');

    // Attendance analytics
    Route::get('meeting-attendance',        [AdminMeetingLinkController::class, 'attendanceView'])->name('meetings.attendance');
    Route::get('meeting-attendance/data',   [AdminMeetingLinkController::class, 'getData'])->name('meetings.attendance.data');

    // Options API for meeting link modal
    Route::get('admin/meeting-link/options', function () {
        return response()->json([
            'campuses'    => DB::table('tbl_form_campus')->distinct()->pluck('campus')->filter()->values(),
            'ministries'  => DB::table('tbl_campus_data')->distinct()->pluck('ministry')->filter()->values(),
            'departments' => DB::table('tbl_campus_data')->distinct()->pluck('department')->filter()->values(),
        ]);
    })->name('meetings.options');
});

Route::middleware(['web', 'auth', 'admin']) // remove 'admin' here if you don't use it
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::prefix('reports')->name('reports.')->group(function () {

        // ===== Setup (catalog) =====
        Route::get('/setup', [ReportSetupController::class, 'index'])
            ->name('setup');

        // Categories
        Route::post  ('/setup/categories',           [ReportSetupController::class, 'storeCategory'])->name('categories.store');
        Route::get   ('/setup/categories/{id}/edit', [ReportSetupController::class, 'editCategory'])->name('categories.edit');
        Route::put   ('/setup/categories/{id}',      [ReportSetupController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/setup/categories/{id}',      [ReportSetupController::class, 'destroyCategory'])->name('categories.destroy');

        // Metrics
        Route::post  ('/setup/metrics',           [ReportSetupController::class, 'storeMetric'])->name('metrics.store');
        Route::get   ('/setup/metrics/{id}/edit', [ReportSetupController::class, 'editMetric'])->name('metrics.edit');
        Route::put   ('/setup/metrics/{id}',      [ReportSetupController::class, 'updateMetric'])->name('metrics.update');
        Route::delete('/setup/metrics/{id}',      [ReportSetupController::class, 'destroyMetric'])->name('metrics.destroy');

        // Teams
        Route::post  ('/setup/teams',           [ReportSetupController::class, 'storeTeam'])->name('teams.store');
        Route::get   ('/setup/teams/{id}/edit', [ReportSetupController::class, 'editTeam'])->name('teams.edit');
        Route::put   ('/setup/teams/{id}',      [ReportSetupController::class, 'updateTeam'])->name('teams.update');
        Route::delete('/setup/teams/{id}',      [ReportSetupController::class, 'destroyTeam'])->name('teams.destroy');

        // People
        Route::post  ('/people',           [ReportSetupController::class, 'storePerson'])->name('people.store');
        Route::get   ('/people/{id}/edit', [ReportSetupController::class, 'editPerson'])->name('people.edit');
        Route::put   ('/people/{id}',      [ReportSetupController::class, 'updatePerson'])->name('people.update');
        Route::delete('/people/{id}',      [ReportSetupController::class, 'destroyPerson'])->name('people.destroy');

        // Assignments
        Route::post  ('/assignments',           [ReportSetupController::class, 'storeAssignment'])->name('assignments.store');
        Route::post  ('/assignments/bulk',      [ReportSetupController::class, 'storeAssignmentsBulk'])->name('assignments.bulk_store');
        Route::get   ('/assignments/{id}/edit', [ReportSetupController::class, 'editAssignment'])->name('assignments.edit');
        Route::put   ('/assignments/{id}',      [ReportSetupController::class, 'updateAssignment'])->name('assignments.update');
        Route::delete('/assignments/{id}',      [ReportSetupController::class, 'destroyAssignment'])->name('assignments.destroy');

        // ===== Entry (posting events) =====
        Route::get ('/entry',                 [ReportEntryController::class, 'index'])->name('entry');
        // IMPORTANT: team page points to teamEntry
        Route::get ('/entry/team/{teamId}',   [ReportEntryController::class, 'teamEntry'])
              ->whereNumber('teamId')
              ->name('entry.team');
        Route::post('/entry/events',          [ReportEntryController::class, 'storeEvent'])->name('entry.events.store');

        // ===== Dashboard (reporting) =====
        Route::get ('/dashboard',          [ReportDashboardController::class, 'index'])->name('dashboard');
        Route::get ('/dashboard/export',   [ReportDashboardController::class, 'export'])->name('dashboard.export');
        Route::match(['GET','POST'], '/dashboard/save', [ReportDashboardController::class, 'save'])->name('dashboard.save');

        // Members (kept in the same controller per your request)
        Route::get ('/members/create', [ReportDashboardController::class, 'memberCreate'])->name('members.create');
        Route::post('/members',        [ReportDashboardController::class, 'memberStore'])->name('members.store');
        Route::get ('/members/{id}',   [ReportDashboardController::class, 'memberShow'])
              ->whereNumber('id')
              ->name('members.show');

              // Alerts (create/store) — same controller
            Route::get('/alerts/create', [ReportDashboardController::class, 'alertCreate'])
                ->name('alerts.create');
            Route::post('/alerts', [ReportDashboardController::class, 'alertStore'])
                ->name('alerts.store');

               

                Route::get ('/reports/setup',               [\App\Http\Controllers\Admin\ReportSetupController::class, 'index'])->name('admin.reports.setup');

    // Categories
    Route::post('/reports/setup/categories',    [\App\Http\Controllers\Admin\ReportSetupController::class, 'storeCategory'])->name('admin.reports.categories.store');

    // Status Sets
    Route::post('/reports/setup/status-sets',   [\App\Http\Controllers\Admin\ReportSetupController::class, 'storeStatusSet'])->name('admin.reports.status_sets.store');
    Route::post('/reports/setup/status-options',[\App\Http\Controllers\Admin\ReportSetupController::class, 'storeStatusOption'])->name('admin.reports.status_options.store');

    // Metrics
    Route::post('/reports/setup/metrics',       [\App\Http\Controllers\Admin\ReportSetupController::class, 'storeMetric'])->name('admin.reports.metrics.store');

    // Status sets & options
Route::post('/admin/reports/status-sets',        [ReportSetupController::class, 'storeStatusSet'])->name('admin.reports.status_sets.store');
Route::delete('/admin/reports/status-sets/{id}', [ReportSetupController::class, 'destroyStatusSet'])->name('admin.reports.status_sets.destroy');

Route::post('/admin/reports/status-options',        [ReportSetupController::class, 'storeStatusOption'])->name('admin.reports.status_options.store');
Route::delete('/admin/reports/status-options/{id}', [ReportSetupController::class, 'destroyStatusOption'])->name('admin.reports.status_options.destroy');

// Metrics (store/update now expect value_mode + optional status_set_id)
Route::post('/admin/reports/metrics',       [ReportSetupController::class, 'storeMetric'])->name('admin.reports.metrics.store');
Route::get ('/admin/reports/metrics/{id}/edit', [ReportSetupController::class, 'editMetric'])->name('admin.reports.metrics.edit');
Route::put ('/admin/reports/metrics/{id}',      [ReportSetupController::class, 'updateMetric'])->name('admin.reports.metrics.update');
    });
});
// Meeting Attendance (view pages under admin namespace)
Route::prefix('meeting-attendance-view')->name('admin.meeting_attendance.')
    ->middleware(['auth','admin'])
    ->group(function () {
    Route::get('/',                [MeetingAttendanceController::class, 'index'])->name('index');
    Route::get('/expected-missing',[MeetingAttendanceController::class, 'expectedMissing'])->name('expected_missing');
});

// Attendance (admin links + utilities)
Route::middleware(['auth','admin'])->get('admin/attendance-links', [AdminAttendanceController::class, 'showLinks'])->name('admin.attendance.links');
Route::middleware(['auth','admin'])->get('admin/attendance', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');

// Admin aliases with auth (non-admin here by name only; keep as-is)
Route::middleware(['auth'])->get('meeting_links', [PublicMeetingLinkController::class, 'index'])->name('admin.meeting_links');
Route::middleware(['auth'])->get('sendMail',      [SendMailController::class, 'index'])->name('admin.sendMail');
use App\Http\Controllers\Admin\TimeTrackingDashboardController;

Route::prefix('admin')->name('admin.')
    ->middleware(['auth','admin'])
    ->group(function () {
    Route::get('/time-tracking/dashboard', [TimeTrackingDashboardController::class, 'index'])
        ->name('time_tracking.dashboard');

    Route::get('/time-tracking/individual', [TimeTrackingDashboardController::class, 'individual'])
        ->name('time_tracking.individual');

    // (optional) CSV export endpoint
    Route::get('/time-tracking/dashboard/export', [TimeTrackingDashboardController::class, 'export'])
        ->name('time_tracking.dashboard.export');

    // Force clock-out for anyone still clocked in
    Route::post('/time-tracking/dashboard/clockout-open', [TimeTrackingDashboardController::class, 'forceClockOutOpen'])
        ->middleware(['auth'])
        ->name('time_tracking.clockout_open');

    // (optional) team drilldown
    Route::get('/time-tracking/team', [TimeTrackingDashboardController::class, 'team'])
        ->name('time_tracking.team');
});
// ADMIN area (guarded by web + auth + checkstatus + admin)
Route::middleware(['web', 'auth', 'checkstatus', 'admin'])->group(function () {
    // Dashboards
    Route::get('/', [AdminDashboardController::class, 'index']);
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Employees
    Route::get('employees', 'Admin\\EmployeesController@index')->name('employees');
    Route::get('employees/new', 'Admin\\EmployeesController@new');
    Route::post('employee/add', 'Admin\\EmployeesController@add');

    // Employee Profile
    Route::get('profile-view-{id}', 'Admin\\ProfileController@view')->whereNumber('id');
    Route::get('profile/view/{id}', 'Admin\\ProfileController@view')->whereNumber('id');
    Route::get('profile/delete/{id}', 'Admin\\ProfileController@delete')->whereNumber('id');
    
    Route::post('/profile/delete/employee', [ProfileController::class, 'clear'])
    ->name('profile.clear');
    Route::get('profile/archive/{id}', 'Admin\\ProfileController@archive')->whereNumber('id');

    // Profile Info
    Route::get('profile/edit/{id}', 'Admin\\ProfileController@editPerson')->whereNumber('id');
    Route::post('profile-view-{id}', [ProfileController::class, 'updatePerson'])->whereNumber('id');

    // Attendance 
    Route::get('attendance', 'Admin\\AttendanceController@index')->name('attendance');
    Route::get('attendance/edit/{id}', 'Admin\\AttendanceController@edit')->whereNumber('id');
    Route::get('attendance/delete/{id}', 'Admin\\AttendanceController@delete')->whereNumber('id');
    Route::post('attendance/update', 'Admin\\AttendanceController@update');
    Route::post('attendance/add-entry', 'Admin\\AttendanceController@addEntry');
    Route::get('attendance/filter', 'Admin\\AttendanceController@getFilter');

  // Schedules
    Route::get('schedules', 'Admin\\SchedulesController@index')->name('schedule');
    Route::post('schedules/add', 'Admin\\SchedulesController@add')->name('schedules.add');
    Route::get('schedules/edit/{id}', 'Admin\\SchedulesController@edit')->whereNumber('id');
    Route::post('schedules/update', 'Admin\\SchedulesController@update')->name('schedules.update');
    Route::get('schedules/delete/{id}', 'Admin\\SchedulesController@delete')->whereNumber('id');
    Route::get('schedules/archive/{id}', 'Admin\\SchedulesController@archive')->whereNumber('id');
    Route::post('schedules/check', 'Admin\\SchedulesController@checkConflict')->name('schedules.check');

    // Leaves
    Route::get('leaves', 'Admin\\LeavesController@index')->name('leave');
    Route::get('leaves/edit/{id}', 'Admin\\LeavesController@edit')->whereNumber('id');
    Route::get('leaves/delete/{id}', 'Admin\\LeavesController@delete')->whereNumber('id');
    Route::post('leaves/update', 'Admin\\LeavesController@update');

    // Users
    Route::get('users', 'Admin\\UsersController@index')->name('users');
    Route::get('users/enable/{id}', 'Admin\\UsersController@enable')->whereNumber('id');
    Route::get('users/disable/{id}', 'Admin\\UsersController@disable')->whereNumber('id');
    Route::get('users/edit/{id}', 'Admin\\UsersController@edit')->whereNumber('id');
    Route::get('users/delete/{id}', 'Admin\\UsersController@delete')->whereNumber('id');
    Route::post('users/register', 'Admin\\UsersController@register');
    Route::post('users/update/user', 'Admin\\UsersController@update');
    Route::post('users/update-worktype', 'Admin\\UsersController@updateWorkType');
    Route::get('users/{user}/permissions/json', 'Admin\\UsersController@permissionsJson')
        ->whereNumber('user')
        ->name('users.permissions.json');
    Route::post('users/{user}/permissions', 'Admin\\UsersController@updatePermissions')
        ->whereNumber('user')
        ->name('users.permissions.update');

    // Roles
    Route::get('roles', 'Admin\\RolesController@index')->name('roles');
    Route::post('users/roles/add', 'Admin\\RolesController@add');
    Route::get('user/roles/get', 'Admin\\RolesController@get');
    Route::post('users/roles/update', 'Admin\\RolesController@update');
    Route::get('users/roles/delete/{id}', 'Admin\\RolesController@delete')->whereNumber('id');
    Route::get('users/roles/permissions/edit/{id}', 'Admin\\RolesController@editperm')->whereNumber('id');
    Route::post('users/roles/permissions/update', 'Admin\\RolesController@updateperm');

    // Reports (admin UI)
    Route::get('reports', 'Admin\\ReportsController@index')->name('reports');
    Route::get('employee-list', 'Admin\\ReportsController@empList');
    Route::get('reports/employee-list', 'Admin\\ReportsController@empList');
    Route::get('reports/employee-attendance', 'Admin\\ReportsController@empAtten');
    Route::get('reports/individual-attendance', 'Admin\\ReportsController@indiAtten');
    Route::get('reports/employee-leaves', 'Admin\\ReportsController@empLeaves');
    Route::get('reports/individual-leaves', 'Admin\\ReportsController@indiLeaves');
    Route::get('employee-schedule', 'Admin\\ReportsController@empSched');
    Route::get('reports/organization-profile', 'Admin\\ReportsController@orgProfile');
    Route::get('volunteer-birthday', 'Admin\\ReportsController@empBday');
    Route::get('reports/user-accounts', 'Admin\\ReportsController@userAccs');
    Route::get('get/employee-attendance', 'Admin\\ReportsController@getEmpAtten');
    Route::get('get/employee-leaves', 'Admin\\ReportsController@getEmpLeav');
    Route::get('get/employee-schedules', 'Admin\\ReportsController@getEmpSched');

    // Settings
    Route::get('settings', 'Admin\\SettingsController@index')->name('settings');
    Route::post('settings/update', 'Admin\\SettingsController@update');
    Route::post('settings/reverse/activation', 'Admin\\SettingsController@reverse');
    Route::get('settings/get/app/info', 'Admin\\SettingsController@appInfo');

    // Fields
    Route::get('campus', 'Admin\\FieldsController@campus')->name('campus');
    Route::post('fields/campus/add', 'Admin\\FieldsController@addcampus');
    Route::get('fields/campus/delete/{id}', 'Admin\\FieldsController@deletecampus')->whereNumber('id');

    Route::get('ministry', 'Admin\\FieldsController@ministry')->name('ministry');
    Route::post('fields/ministry/add', 'Admin\\FieldsController@addministry');
    Route::get('fields/ministry/delete/{id}', 'Admin\\FieldsController@deleteministry')->whereNumber('id');

    Route::get('fields/jobtitle', 'Admin\\FieldsController@jobtitle')->name('jobtitle');
    Route::post('fields/jobtitle/add', 'Admin\\FieldsController@addJobtitle');
    Route::get('fields/jobtitle/delete/{id}', 'Admin\\FieldsController@deleteJobtitle')->whereNumber('id');

    Route::get('fields/leavetype', 'Admin\\FieldsController@leavetype')->name('leavetype');
    Route::post('fields/leavetype/add', 'Admin\\FieldsController@addLeavetype');
    Route::get('fields/leavetype/delete/{id}', 'Admin\\FieldsController@deleteLeavetype')->whereNumber('id');
    Route::get('fields/leavetype/leave-groups', 'Admin\\FieldsController@leaveGroups')->name('leavegroup');
    Route::post('fields/leavetype/leave-groups/add', 'Admin\\FieldsController@addLeaveGroups');
    Route::get('fields/leavetype/leave-groups/edit/{id}', 'Admin\\FieldsController@editLeaveGroups')->whereNumber('id');
    Route::post('fields/leavetype/leave-groups/update', 'Admin\\FieldsController@updateLeaveGroups');
    Route::get('fields/leavetype/leave-groups/delete/{id}', 'Admin\\FieldsController@deleteLeaveGroups')->whereNumber('id');

    // Exports / Imports (admin)
    Route::get('export/fields/campus', 'Admin\\ExportsController@campus');
    Route::get('export/fields/ministry', 'Admin\\ExportsController@ministry');
    Route::get('export/fields/jobtitle', 'Admin\\ExportsController@jobtitle');
    Route::get('export/fields/leavetypes', 'Admin\\ExportsController@leavetypes');

    Route::post('import/fields/campus', 'Admin\\ImportsController@importcampus');
    Route::post('import/fields/ministry', 'Admin\\ImportsController@importministry');
    Route::post('import/fields/jobtitle', 'Admin\\ImportsController@importJobtitle');
    Route::post('import/fields/leavetypes', 'Admin\\ImportsController@importLeavetypes');
    Route::post('import/options', 'Admin\\ImportsController@opt');

    Route::get('export/report/employees', 'Admin\\ExportsController@employeeList');
    Route::post('export/report/attendance', 'Admin\\ExportsController@attendanceReport');
    Route::post('export/report/leaves', 'Admin\\ExportsController@leavesReport');
    Route::get('export/report/birthdays', 'Admin\\ExportsController@birthdaysReport');
    Route::get('export/report/accounts', 'Admin\\ExportsController@accountReport');
    Route::post('export/report/schedule', 'Admin\\ExportsController@scheduleReport');
});

// Inventory & Computers (admin UI pieces without global admin middleware)
Route::get('computers', [App\Http\Controllers\Admin\ComputerController::class, 'create'])
    ->middleware(['auth','admin'])
    ->name('admin.computers');
Route::post('computers', [App\Http\Controllers\Admin\ComputerController::class, 'store'])
    ->middleware(['auth','admin'])
    ->name('admin.computers.store');

Route::get('inventory', [App\Http\Controllers\Admin\InventoryController::class, 'create'])
    ->middleware(['auth','admin'])
    ->name('admin.inventory-equipment');

Route::prefix('admin')->name('admin.')
    ->middleware(['auth','admin'])
    ->group(function (){
    Route::get('inventory-equipment/create', [App\Http\Controllers\Admin\InventoryController::class, 'create'])->name('inventory-equipment.create');
    Route::post('inventory-equipment', [App\Http\Controllers\Admin\InventoryController::class, 'store'])->name('inventory-equipment.store');
    Route::get('inventory-equipment/{id}/edit', [App\Http\Controllers\Admin\InventoryController::class, 'edit'])->whereNumber('id')->name('inventory-equipment.edit');
    Route::match(['put','patch'], 'inventory-equipment/{id}', [App\Http\Controllers\Admin\InventoryController::class, 'update'])->whereNumber('id')->name('inventory-equipment.update');
    Route::delete('inventory-equipment/{id}', [App\Http\Controllers\Admin\InventoryController::class, 'destroy'])->whereNumber('id')->name('inventory-equipment.destroy');
});

// Devotion Reports (Admin)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('devotions-reports', [DevotionReportController::class, 'index'])->name('admin.reports.devotions');
    Route::get('admin/reports/devotions', [DevotionReportController::class, 'index'])->name('admin.reports.devotions');
    Route::get('admin/reports/global-devotions', [DevotionReportController::class, 'GlobalDevotionReport'])->name('admin.reports.global-devotions');
    Route::get('admin/reports/devotions/data', [DevotionReportController::class, 'getData'])->name('admin.reports.devotions.data');
});

// Employee Attendance Reports (Admin)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/reports/employee-attendance', [AttendanceReportsController::class, 'index'])->name('admin.reports.employee-attendance');
    Route::get('admin/reports/attendance/data', [AttendanceReportsController::class, 'getData'])->name('admin.reports.attendance.data');
});

// Admin Devotions / Asana
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('devotion', [AdminDevotionController::class, 'index'])->name('admin.devotion.index');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('asana', [AdminAsanaController::class, 'index'])->name('admin.asana.index');
    Route::get('asana-portfolio', [AdminAsanaPortfolioController::class, 'index'])->name('admin.asana-portfolio.index');
});

// Admin Requests area
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('admin/requests', [AdminRequestController::class, 'index']);
    Route::get('admin/request/{id}', [AdminRequestController::class, 'show'])->whereNumber('id');
    Route::post('admin/request/{id}/respond', [AdminRequestController::class, 'respond'])->whereNumber('id');
});

// Admin send alert
Route::get('admin/send-alert', [App\Http\Controllers\Admin\AlertController::class, 'create'])->name('admin.sendAlert');

// Creative Workload Management
Route::prefix('admin/creative')->name('admin.creative.')
    ->middleware(['auth','admin'])
    ->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\CreativeAnalyticsController::class, 'index'])->name('index');
    Route::get('/requests', [App\Http\Controllers\Admin\CreativeRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [App\Http\Controllers\Admin\CreativeRequestController::class, 'create'])->name('requests.create');
    Route::get('/requests/create-graphic', function() { return view('admin.creative.requests.create-graphic'); })->name('requests.create-graphic');
    Route::get('/requests/create-video', function() { return view('admin.creative.requests.create-video'); })->name('requests.create-video');
    Route::post('/requests', [App\Http\Controllers\Admin\CreativeRequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{id}', [App\Http\Controllers\Admin\CreativeRequestController::class, 'show'])->name('requests.show');
    Route::put('/requests/{id}', [App\Http\Controllers\Admin\CreativeRequestController::class, 'update'])->name('requests.update');
    Route::post('/requests/{id}/approve', [App\Http\Controllers\Admin\CreativeRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/requests/{id}/reject', [App\Http\Controllers\Admin\CreativeRequestController::class, 'reject'])->name('requests.reject');
    Route::post('/requests/{id}/status', [App\Http\Controllers\Admin\CreativeRequestController::class, 'updateStatus'])->name('requests.updateStatus');
    Route::get('/tasks/create', [App\Http\Controllers\Admin\CreativeTaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [App\Http\Controllers\Admin\CreativeTaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{id}', [App\Http\Controllers\Admin\CreativeTaskController::class, 'update'])->name('tasks.update');
    Route::post('/tasks/{id}/status', [App\Http\Controllers\Admin\CreativeTaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::delete('/tasks/{id}', [App\Http\Controllers\Admin\CreativeTaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/reports', [App\Http\Controllers\Admin\CreativeReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/tasks', [App\Http\Controllers\Admin\CreativeReportsController::class, 'exportTasks'])->name('reports.tasks');
    Route::get('/reports/contributions', [App\Http\Controllers\Admin\CreativeReportsController::class, 'exportContributions'])->name('reports.contributions');
    Route::get('/insights', [App\Http\Controllers\Admin\CreativeInsightsController::class, 'index'])->name('insights.index');
    Route::get('/insights/api', [App\Http\Controllers\Admin\CreativeInsightsController::class, 'api'])->name('insights.api');
    Route::get('/insights/realtime', [App\Http\Controllers\Admin\CreativeInsightsController::class, 'realtime'])->name('insights.realtime');
    Route::get('/test', function(\Illuminate\Http\Request $request) { 
        if ($request->get('action') === 'clear-cache') {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            return 'Cache cleared';
        }
        return view('admin.creative.test'); 
    })->name('test');
    Route::post('/test', function(\Illuminate\Http\Request $request) {
        if ($request->get('action') === 'sample-data') {
            // Create sample request
            $request = \App\Classes\table::creativeRequests()->create([
                'title' => 'Sample Creative Request',
                'description' => 'This is a test request for system validation',
                'request_type' => 'graphic',
                'priority' => 'normal',
                'status' => 'pending',
                'requester_people_id' => 1,
                'desired_due_at' => now()->addDays(7)
            ]);
            
            // Create sample task
            $task = \App\Classes\table::creativeTasks()->create([
                'request_id' => $request->id,
                'title' => 'Sample Task',
                'description' => 'Test task for validation',
                'status' => 'completed',
                'priority' => 'normal',
                'estimated_minutes' => 120,
                'completed_at' => now()
            ]);
            
            return 'Sample data created';
        }
        return 'Invalid action';
    })->name('test.post');
});
