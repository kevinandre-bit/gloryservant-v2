<?php

namespace App\Http\Controllers\RadioDashboard\Tech;

use App\Http\Controllers\Controller;

class TechScheduleController extends Controller
{
    public function index()
    {
        // Views currently include mock data, so no payload required yet.
        return view('radio_dashboard.tech.schedule.index');
    }
}
