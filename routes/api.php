<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\UserTrackingController;
use App\Http\Controllers\ClockController;
use App\Http\Controllers\RadioDashboard\DashboardController;
use App\Http\Controllers\Api\TrackIngestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are prefixed with /api and use the "api" middleware group.
| Make sure your document root points to /public so /api/* hits Laravel.
|--------------------------------------------------------------------------
*/

// --- Health / Ping (for curl & Apps Script sanity checks)
Route::get('/health', fn () => response()->json([
    'ok'   => true,
    'time' => now()->toIso8601String(),
]));
Route::get('/ping', fn () => response()->json(['ok' => true]));

// --- Public endpoints you actually use
Route::post('/track', [UserTrackingController::class, 'trackAction']);
Route::post('/attendance', [ClockController::class, 'add'])->middleware('clockin');

Route::post('/integrations/zoho/checkins', [DashboardController::class, 'storeOutage'])
    ->name('zoho.checkins.store');

// --- Google Sheets ingest (Apps Script posts here)
Route::post('/tracks/ingest', [TrackIngestController::class, 'ingest'])
    ->name('api.tracks.ingest');

// --- (Optional) Authenticated example (only if you really need it)
// If you’re using Sanctum/Passport, uncomment and update the controller:
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
