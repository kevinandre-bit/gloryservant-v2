<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Attendance;

// This controller handles displaying meeting links and viewing attendance for meetings.
class MeetingLinkController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','admin']);
    }
    /**
     * Display all meeting links.
     * This method fetches all records from the 'meeting_link' table
     * and passes them to the 'admin.meeting_links' view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Retrieve all meeting links from the 'meeting_link' table.
        // Ensure the table name 'meeting_link' is correctly spelled in your database.
        $meetings = DB::table('meeting_link')->get();

        // Return the 'admin.meeting_links' view with the retrieved meeting data.
        return view('admin.meeting_links', compact('meetings'));
    }

    /**
     * Display a list of all meeting attendance records.
     * This method retrieves the latest attendance records
     * from the 'attendances' table (using the Attendance model)
     * and passes them to the 'admin.meeting_attendance' view.
     *
     * @return \Illuminate\View\View
     */
    public function attendanceView()
    {
        // Fetch attendance records ordered by the most recent entries.
        $attendances = Attendance::latest()->get();

        // Pass the attendance data to the 'admin.meeting_attendance' Blade view.
        return view('admin.meeting_attendance', compact('attendances'));
    }
}
