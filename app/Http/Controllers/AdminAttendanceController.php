<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','admin']);
    }
    /**
     * This method is responsible for handling the display of the admin attendance page.
     * Currently, it returns a placeholder message indicating that the page is under construction.
     * 
     * @return string A message indicating the status of the page
     */
    public function index()
    {
        return 'Admin attendance page under construction.';
    }
}
