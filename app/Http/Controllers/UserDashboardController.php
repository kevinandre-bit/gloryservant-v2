<?php

use Illuminate\Http\Request;

namespace App\Http\Controllers;

class UserDashboardController extends Controller
{
    /**
     * Displays the user's dashboard view.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Return the dashboard view for the user
        // Ensure the correct path to the 'personal.dashboard' view is provided
        return view('personal.dashboard'); // adjust view path if needed
    }
}
