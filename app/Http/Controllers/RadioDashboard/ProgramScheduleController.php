<?php

namespace App\Http\Controllers\RadioDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class ProgramScheduleController extends Controller
{
    // /program/schedules  (Batches)
    public function index()
    {
        // Fake batches list
        $batches = collect([
            [
                'id' => 1,
                'period_label' => 'Week 34 / 2025',
                'starts_on' => Carbon::parse('2025-08-18'),
                'ends_on'   => Carbon::parse('2025-08-24'),
                'original_filename' => 'program_grid_2025-W34.xlsx',
                'status' => 'EVALUATED', // PENDING|IMPORTED|EVALUATED|ERROR
                'uploader' => 'Admin User',
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'id' => 2,
                'period_label' => 'Week 35 / 2025',
                'starts_on' => Carbon::parse('2025-08-25'),
                'ends_on'   => Carbon::parse('2025-08-31'),
                'original_filename' => 'program_grid_2025-W35.xlsx',
                'status' => 'IMPORTED',
                'uploader' => 'Ops Manager',
                'created_at' => Carbon::now()->subHours(6),
            ],
        ]);

        return view('radio_dashboard.program.schedules.index', compact('batches'));
    }

    // /program/schedules/upload (Upload Grid)
    public function uploadForm()
    {
        return view('radio_dashboard.program.schedules.upload');
    }

    // /program/schedules/template (Download Template helper page)
    public function template()
    {
        // UI-only: show the expected columns and a download button (link to same route for now).
        return view('radio_dashboard.program.schedules.template');
    }

    // Optional: /program/schedules/{batch}
    public function show($batchId)
    {
        // Simple placeholder (can wire later)
        return redirect()->route('program.schedules.index');
    }
}
