<?php

use Illuminate\Support\Facades\Route;

// Radio Dashboard controllers
use App\Http\Controllers\RadioDashboard\DashboardController;
use App\Http\Controllers\RadioDashboard\ProgramScheduleController;
use App\Http\Controllers\RadioDashboard\PlayoutLogController;
use App\Http\Controllers\RadioDashboard\Tech\TechScheduleController;
use App\Http\Controllers\RadioDashboard\Tech\TechAssignmentsController;
use App\Http\Controllers\RadioDashboard\Tech\TechRotaController;
use App\Http\Controllers\RadioDashboard\Tech\TechCheckinsController;
use App\Http\Controllers\RadioDashboard\Tech\TechAvailabilityController;
use App\Http\Controllers\RadioDashboard\MaintenanceController;
use App\Http\Controllers\RadioDashboard\MonitoringController;
use App\Http\Controllers\RadioDashboard\ReportsController;
use App\Http\Controllers\RadioDashboard\Admin\RadioRegistryController;
use App\Http\Controllers\RadioDashboard\Admin\TechnicianRegistryController;
use App\Http\Controllers\RadioDashboard\Admin\PocRegistryController;
use App\Http\Controllers\RadioDashboard\Admin\SitesDirectoryController;
use App\Http\Controllers\RadioDashboard\Inventory\RadioInventoryController;
use App\Http\Controllers\RadioDashboard\Inventory\InventoryVendorsController;
use App\Http\Controllers\RadioDashboard\Reports\ReportStudioController;
// Finance
use App\Http\Controllers\RadioDashboard\Finance\ExpenseController;
use App\Http\Controllers\RadioDashboard\Finance\RecurringController;
use App\Http\Controllers\RadioDashboard\Finance\VendorController;

// Controllers
use App\Http\Controllers\Radio\PlaylistController;
use App\Http\Controllers\Radio\PlaylistItemController;
use App\Http\Controllers\Radio\PlaylistExportController;
use App\Http\Controllers\Radio\TrackController;
use App\Http\Controllers\Radio\LibraryUploadController;
use App\Http\Controllers\Radio\LibraryApiController;
use App\Http\Controllers\Radio\PlaylistApiController;

/*
|--------------------------------------------------------------------------
| RADIO ROUTES (Glory Servant)
| Prefix: /radio/dashboard
| All UI behind auth; program-specific routes named "program.*"
|--------------------------------------------------------------------------
*/
// simple show route (protected)
Route::get('/stations/{station}', [StationController::class, 'show'])
     ->middleware(['auth'])
     ->name('stations.show');
Route::post('/radio/dashboard/program/playlists/{playlist}/reindex',
  [\App\Http\Controllers\Radio\PlaylistItemController::class, 'reindex']
)->middleware(['auth','can:manage-playlists'])
 ->name('program.playlists.items.reindex');
Route::middleware(['auth'])->prefix('radio/dashboard')->group(function () {

    /* ----------------------- Library (UI) ----------------------- */
Route::post(
    '/radio/dashboard/playlists/{playlist}/send/{format}',
    [PlaylistExportController::class, 'sendToXPlayout']
)->where(['format' => 'xlsx|csv'])
 ->name('program.playlists.send');

    // Library browse (left pane UI)
    Route::get('/library', [TrackController::class, 'index'])
        ->name('program.library.index');

    // Library upload & clear
    Route::get('/library/upload',  [LibraryUploadController::class, 'showForm'])
        ->name('program.library.upload');
    Route::post('/library/upload', [LibraryUploadController::class, 'upload'])
        ->name('program.library.upload.post');
    Route::delete('/library/clear', [LibraryUploadController::class, 'clear'])
        ->middleware('can:manage-playlists')
        ->name('program.library.clear');
        Route::put('/playlists/{playlist}', [PlaylistController::class, 'update'])
    ->name('program.playlists.update');
    // routes/web.php (inside program group)
Route::get('/playlists/{playlist}/export.xlsx', [PlaylistExportController::class, 'xlsx'])
    ->name('program.playlists.export.xlsx');   // distinct route
Route::get('/playlists/{playlist}/export.csv', [PlaylistExportController::class, 'csv'])
    ->name('program.playlists.export.csv');    // keep csv if you want
    Route::get('/playlists/{playlist}/export.txt',  [PlaylistExportController::class, 'txt'])
    ->name('program.playlists.export.txt');

Route::get('/playlists/{playlist}/export.xml',  [PlaylistExportController::class, 'xml'])
    ->name('program.playlists.export.xml');
    Route::delete('/playlists/{playlist}/clear', [\App\Http\Controllers\Radio\PlaylistItemController::class, 'clear'])
    ->name('program.playlists.items.clear');
    Route::post('/playlists/{playlist}/duplicate', [PlaylistController::class, 'duplicate'])
    ->name('program.playlists.duplicate');
    // routes/web.php
Route::get('/playlists/{playlist}', [\App\Http\Controllers\Radio\PlaylistController::class, 'show'])
    ->name('program.playlists.show');
    Route::delete('/radio/dashboard/program/library/clear', [\App\Http\Controllers\Radio\LibraryUploadController::class, 'clear'])
    ->middleware(['auth','can:manage-playlists'])
    ->name('program.library.clear');

    /* ----------------------- Program ----------------------- */
    Route::prefix('program')->name('program.')->group(function () {

        // Playlists (UI)
        Route::get('/playlists',                 [PlaylistController::class, 'index'])->name('playlists.index');
        Route::get('/playlists/create',          [PlaylistController::class, 'create'])->name('playlists.create');
        Route::post('/playlists',                [PlaylistController::class, 'store'])->name('playlists.store');
        Route::get('/playlists/{playlist}/edit', [PlaylistController::class, 'edit'])->name('playlists.edit');
        Route::put('/playlists/{playlist}',      [PlaylistController::class, 'update'])->name('playlists.update');
        Route::delete('/playlists/{playlist}',   [PlaylistController::class, 'destroy'])->name('playlists.destroy');

        // Playlist Items (UI actions)
        Route::post('/playlists/{playlist}/items',             [PlaylistItemController::class, 'store'])->name('playlists.items.store');
        Route::delete('/playlists/{playlist}/items/{item}',    [PlaylistItemController::class, 'destroy'])->name('playlists.items.destroy');
        Route::post('/playlists/{playlist}/items/{item}/up',   [PlaylistItemController::class, 'moveUp'])->name('playlists.items.up');
        Route::post('/playlists/{playlist}/items/{item}/down', [PlaylistItemController::class, 'moveDown'])->name('playlists.items.down');
        // (Optional) Drag-sort endpoint if you add it later:
        Route::put('/playlists/{playlist}/items/reorder',      [PlaylistItemController::class, 'reorder'])->name('playlists.items.reorder');

        // Export
        Route::post('/playlists/{playlist}/export', [PlaylistExportController::class, 'export'])->name('playlists.export');

        /* ------------- Optional JSON API (Progressive Enh.) ------------- */
        Route::get('/library.json',                                      [LibraryApiController::class,   'index'])->name('library.json'); // ?q=&page=
        Route::post('/playlists/{playlist}/items.json',                  [PlaylistApiController::class, 'store'])->name('playlists.items.store.json');
        Route::patch('/playlists/{playlist}/items/reorder.json',         [PlaylistApiController::class, 'reorder'])->name('playlists.items.reorder.json');
        Route::delete('/playlists/{playlist}/items/{item}.json',         [PlaylistApiController::class, 'destroy'])->name('playlists.items.destroy.json');


    });
});

// Radio Admin (registry, sites directory)
Route::prefix('radio/admin')->name('radio.admin.')->group(function () {
    // Stations
    Route::get('/stations',                    [RadioRegistryController::class, 'index'])->name('stations.index');
    Route::post('/stations/new',               [RadioRegistryController::class, 'store'])->name('stations.store');     // kept
    Route::get('/stations/{station}/edit',     [RadioRegistryController::class, 'edit'])->name('stations.edit');
    Route::put('/stations/{station}',          [RadioRegistryController::class, 'update'])->name('stations.update');
    Route::delete('/stations/{station}',       [RadioRegistryController::class, 'destroy'])->name('stations.destroy');
    Route::post('/stations/{station}/toggle',  [RadioRegistryController::class, 'toggle'])->name('stations.toggle');   // On-Air switch

    // Geo JSON (stations)
    Route::get('/stations/geo/arrondissements/{department}', [RadioRegistryController::class, 'arrondissements'])
        ->name('stations.geo.arrondissements');
    Route::get('/stations/geo/communes/{arrondissement}',    [RadioRegistryController::class, 'communes'])
        ->name('stations.geo.communes');

    // Technicians
    Route::get('/technicians',                     [TechnicianRegistryController::class, 'index'])->name('techs.index');
    Route::get('/technicians/new',                 [TechnicianRegistryController::class, 'create'])->name('techs.create'); // kept
    Route::post('/technicians/new',                [TechnicianRegistryController::class, 'store'])->name('techs.store');   // kept
    Route::get('/technicians/{technician}/edit',   [TechnicianRegistryController::class, 'edit'])->name('techs.edit');
    Route::put('/technicians/{technician}',        [TechnicianRegistryController::class, 'update'])->name('techs.update');
    Route::delete('/technicians/{technician}',     [TechnicianRegistryController::class, 'destroy'])->name('techs.destroy');

    // POCs
    Route::get('/pocs',                 [PocRegistryController::class, 'index'])->name('pocs.index');
    Route::get('/pocs/new',             [PocRegistryController::class, 'create'])->name('pocs.create');   // kept
    Route::post('/pocs/new',            [PocRegistryController::class, 'store'])->name('pocs.store');     // kept
    Route::get('/pocs/{poc}/edit',      [PocRegistryController::class, 'edit'])->name('pocs.edit');
    Route::put('/pocs/{poc}',           [PocRegistryController::class, 'update'])->name('pocs.update');
    Route::delete('/pocs/{poc}',        [PocRegistryController::class, 'destroy'])->name('pocs.destroy');

    // Sites directory (list + modal create)
    Route::get('/sites',  [SitesDirectoryController::class, 'index'])->name('sites.index');
    Route::post('/sites', [SitesDirectoryController::class, 'store'])->name('sites.store');

    // JSON endpoints for dependent selects (sites)
    Route::get('/geo/arrondissements/{department}', [SitesDirectoryController::class, 'arrondissements'])
        ->name('geo.arrondissements');
    Route::get('/geo/communes/{arrondissement}',    [SitesDirectoryController::class, 'communes'])
        ->name('geo.communes');
});

// Radio main (authenticated)
Route::middleware(['web', 'auth'])->group(function () {
    // Home → Dashboard
    Route::get('/radio/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Modal posts (shortcuts)
    Route::post('/radioops/checkins',    [DashboardController::class, 'storeOutage'])->name('radio.checkins.store');
    Route::post('/radioops/assignments', [DashboardController::class, 'storeAssignment'])->name('tech.assignments.store');
    Route::post('/radioops/maintenance', [DashboardController::class, 'storeMaintenance'])->name('maintenance.tasks.store');
    Route::post('/radioops/maintenance', [DashboardController::class, 'storeMaintenance'])->name('maintenance.tasks.store'); // duplicate kept

    Route::prefix('maintenance')->name('maintenance.')->group(function () {
    // List + filters
    Route::get('/tasks', [MaintenanceController::class, 'tasksIndex'])
        ->name('tasks.index');

    // Create (modal submit)
    Route::post('/tasks/store', [MaintenanceController::class, 'storeTask'])
        ->name('tasks.store');

    // Actions
    Route::post('/tasks/{id}/done', [MaintenanceController::class, 'markDone'])
        ->name('tasks.done');

    Route::post('/tasks/{id}/reschedule', [MaintenanceController::class, 'reschedule'])
        ->name('tasks.reschedule');

    // Optional calendar page (keep if you link to it)
    Route::get('/calendar', [MaintenanceController::class, 'calendar'])
        ->name('calendar.index');
});
    // Reports daily store (UI post target)
    Route::post('/reports/daily', [DashboardController::class, 'storeDailyReport'])->name('reports.daily.store');

    // Program Schedules (Excel → evaluation)
    Route::prefix('program/schedules')->name('program.schedules.')->group(function () {
        Route::get('/',                  [ProgramScheduleController::class, 'index'])->name('index');
        Route::get('/upload',            [ProgramScheduleController::class, 'uploadForm'])->name('upload');
        Route::post('/upload',           [ProgramScheduleController::class, 'uploadStore']);
        Route::get('/template',          [ProgramScheduleController::class, 'template'])->name('template');
        Route::get('/{batch}',           [ProgramScheduleController::class, 'show'])->name('show');
        Route::post('/{batch}/evaluate', [ProgramScheduleController::class, 'evaluate'])->name('evaluate');
    });

    // Announce schedule (single action)
    Route::post('/program/schedules/announce', [DashboardController::class, 'announceSchedule'])
        ->name('program.schedules.announce');

    // Playout (what actually aired)
    Route::prefix('playout')->name('playout.')->group(function () {
        Route::get('/logs',        [PlayoutLogController::class, 'index'])->name('logs.index');
        Route::get('/logs/new',    [PlayoutLogController::class, 'create'])->name('logs.new');
        Route::get('/logs/upload', [PlayoutLogController::class, 'uploadForm'])->name('logs.upload');
        Route::post('/logs/upload',[PlayoutLogController::class, 'uploadStore']);
        Route::get('/deviations',  [PlayoutLogController::class, 'deviations'])->name('deviations.index');
    });

    // Technician Schedule
    Route::prefix('tech')->name('tech.')->group(function () {
        Route::get('/schedule',                  [TechScheduleController::class, 'index'])->name('schedule.index');
        Route::get('/assignments',               [TechAssignmentsController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/new',           [TechAssignmentsController::class, 'create'])->name('assignments.create');
        Route::get('/rota',                      [TechRotaController::class, 'index'])->name('rota.index');
        Route::get('/checkins',                  [TechCheckinsController::class, 'index'])->name('checkins.index');
        Route::post('/checkins',                 [TechCheckinsController::class, 'store'])->name('checkins.store');
        Route::post('/checkins/stations/{station}/toggle', [TechCheckinsController::class, 'toggleStation'])->name('checkins.station.toggle');
        Route::get('/availability',              [TechAvailabilityController::class, 'index'])->name('availability.index');
        Route::prefix('checkins')->name('checkins.')->group(function () {
        Route::post('/station/{station}/quick',  [TechCheckinsController::class, 'quickUpdateStation'])->name('station.quick');
        Route::post('/station/{station}/toggle', [TechCheckinsController::class, 'toggleStation'])->name('station.toggle');
    });
    });

    // Maintenance
    Route::prefix('maintenance')->name('maintenance.')->group(function () {
        Route::get('/tasks',   [MaintenanceController::class, 'tasksIndex'])->name('tasks.index');
        Route::get('/tasks/new',[MaintenanceController::class, 'tasksCreate'])->name('tasks.create');
        Route::get('/calendar',[MaintenanceController::class, 'calendar'])->name('calendar.index');
    });

    // Monitoring
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/source',     [MonitoringController::class, 'source'])->name('source');
        Route::get('/hub',        [MonitoringController::class, 'hub'])->name('hub');
        Route::get('/sites',      [MonitoringController::class, 'sitesIndex'])->name('sites.index');
        Route::get('/sites/{site}',[MonitoringController::class, 'sitesShow'])->name('sites.show');
    });

    // Technician Daily Report (UI-only, fake data)
    Route::get('/tech/reports/daily/new', [\App\Http\Controllers\RadioDashboard\Tech\TechReportsController::class, 'create'])
        ->name('tech.reports.daily.create');

    // Optional preview submit target (UI-only)
    Route::post('/tech/reports/daily/preview', [\App\Http\Controllers\RadioDashboard\Tech\TechReportsController::class, 'preview'])
        ->name('tech.reports.daily.preview');

    // Duplicate create route via view (kept as in original)
    Route::get('/tech/reports/daily/new', fn() => view('radio_dashboard.tech.reports.daily_create'))
      ->name('tech.reports.daily.create');

    // Reports (views + controller)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily/technician', fn() => view('radio_dashboard.reports.daily_technician'))->name('daily.tech');
        Route::get('/daily/operator',   fn() => view('radio_dashboard.reports.daily_operator'))->name('daily.op');
        Route::get('/daily/admin',      fn() => view('radio_dashboard.reports.daily_admin'))->name('daily.admin');
        Route::get('/weekly',           fn() => view('radio_dashboard.reports.weekly_summary'))->name('weekly');
        Route::get('/daily',            [ReportsController::class, 'daily'])->name('daily');
        Route::get('/weekly',           [ReportsController::class, 'weekly'])->name('weekly'); // duplicate kept
    });

    // Inventory (radio)
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/inventory.index', [RadioInventoryController::class, 'index'])->name('index');
        Route::get('/movements',       [RadioInventoryController::class, 'movements'])->name('movements');
        Route::get('/items/new',       [RadioInventoryController::class, 'create'])->name('items.create'); // UI only

        // Vendors
        Route::get('/vendors',     [InventoryVendorsController::class, 'index'])->name('vendors.index');
        Route::get('/vendors/new', [InventoryVendorsController::class, 'create'])->name('vendors.create');
    });

    // Report Studio (UI only)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/studio',           [ReportStudioController::class, 'studio'])->name('studio');          // entry hub
        Route::get('/build',            [ReportStudioController::class, 'build'])->name('build');            // wizard
        Route::get('/preview/{type}',   [ReportStudioController::class, 'preview'])->name('preview');        // assembled preview
    });
});

// Finance (radio)
Route::prefix('finance')->name('finance.')->group(function () {
    // Expenses
    Route::get('/expenses',      [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/new',  [ExpenseController::class, 'create'])->name('expenses.create');

    // Recurring rules
    Route::get('/recurring',     [RecurringController::class, 'index'])->name('recurring.index');
    Route::get('/recurring/new', [RecurringController::class, 'create'])->name('recurring.create');

    // Vendors (optional directory)
    Route::get('/vendors',       [VendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/new',   [VendorController::class, 'create'])->name('vendors.create');
});
