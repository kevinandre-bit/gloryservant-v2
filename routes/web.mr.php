<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MR\MeetingReportController;
use App\Http\Controllers\MR\Admin\MeetingAdminController;

/*
|--------------------------------------------------------------------------
| MR ROUTES
|--------------------------------------------------------------------------
| Meeting Reports system (/mr and /mr/admin).
|--------------------------------------------------------------------------
*/

Route::prefix('mr')->name('mr.')->group(function () {
    // Public: open a meeting by magic token
    Route::get('/{token}', [MeetingReportController::class, 'show'])
        ->where('token', '[A-Za-z0-9\-_]+')
        ->name('show');

    // Notes: autosave + asset upload
    Route::post('/{meeting}/notes', [MeetingReportController::class, 'saveNotes'])
        ->whereNumber('meeting')
        ->middleware(['auth','throttle:20,1'])
        ->name('notes.save');
    Route::post('/{meeting}/notes/upload', [MeetingReportController::class, 'uploadNoteAsset'])
        ->whereNumber('meeting')
        ->middleware(['auth','throttle:20,1'])
        ->name('notes.upload');

    // Stubs / actions
    Route::post('/{meeting}/publish', [MeetingReportController::class, 'publish'])
        ->whereNumber('meeting')
        ->middleware(['auth','throttle:20,1'])
        ->name('publish');

    Route::post('/{meeting}/agenda/{item}/start',  [MeetingReportController::class, 'agendaStart'])
        ->whereNumber('meeting')->whereNumber('item')
        ->middleware(['auth','throttle:20,1'])
        ->name('agenda.start');
    Route::post('/{meeting}/agenda/{item}/stop',   [MeetingReportController::class, 'agendaStop'])
        ->whereNumber('meeting')->whereNumber('item')
        ->middleware(['auth','throttle:20,1'])
        ->name('agenda.stop');
    Route::post('/{meeting}/agenda/{item}/toggle', [MeetingReportController::class, 'agendaToggle'])
        ->whereNumber('meeting')->whereNumber('item')
        ->middleware(['auth','throttle:20,1'])
        ->name('agenda.toggle');

    Route::post('/{meeting}/attendees/add',                [MeetingReportController::class, 'attendeeAdd'])
        ->whereNumber('meeting')
        ->middleware(['auth','throttle:20,1'])
        ->name('attendee.add');
    Route::post('/{meeting}/attendees/{attendee}/status',  [MeetingReportController::class, 'attendeeStatus'])
        ->whereNumber('meeting')->whereNumber('attendee')
        ->middleware(['auth','throttle:20,1'])
        ->name('attendee.status');

    Route::post('/{meeting}/tasks/create',   [MeetingReportController::class, 'taskCreate'])
        ->whereNumber('meeting')
        ->middleware(['auth','throttle:20,1'])
        ->name('tasks.create');
    Route::post('/{meeting}/tasks/link',     [MeetingReportController::class, 'taskLink'])
        ->whereNumber('meeting')
        ->middleware(['auth','throttle:20,1'])
        ->name('tasks.link');
    Route::post('/{meeting}/tasks/{task}/complete', [MeetingReportController::class, 'taskComplete'])
        ->whereNumber('meeting')->whereNumber('task')
        ->middleware(['auth','throttle:20,1'])
        ->name('tasks.complete');
});

// MR Admin
Route::prefix('mr/admin')->name('mr.admin.')->group(function () {
    Route::get('/meetings/create', [MeetingAdminController::class, 'create'])->name('meetings.create');
    Route::post('/meetings',       [MeetingAdminController::class, 'store'])->name('meetings.store');
});
