<?php

namespace App\Http\Controllers\RadioDashboard\Tech;

use App\Http\Controllers\Controller;

class TechAssignmentsController extends Controller
{
    public function index()
    {
        return view('radio_dashboard.tech.assignments.index');
    }

    public function create()
    {
        // If you later add a dedicated “new assignment” page
        return view('radio_dashboard.tech.assignments.create');
    }
}
