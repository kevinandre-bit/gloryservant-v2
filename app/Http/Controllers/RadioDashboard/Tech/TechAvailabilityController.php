<?php

namespace App\Http\Controllers\RadioDashboard\Tech;

use App\Http\Controllers\Controller;

class TechAvailabilityController extends Controller
{
    public function index()
    {
        return view('radio_dashboard.tech.availability.index');
    }
}
