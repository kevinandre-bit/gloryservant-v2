<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Attendance;
use App\Classes\table;
use App\Classes\permission;
use App\Http\Controllers\Controller;

// This controller handles displaying meeting links and viewing attendance for meetings.
class DisplayAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
    $meetings = DB::table('meeting_link')->get();
    $campuses = DB::table('tbl_campus_data')->get();
    $users = table::people()->get();
    $companies = table::company()->get();
    $departments = table::department()->get();
    $today = date('M, d Y');
    $empAtten = table::attendance()->get();
    $employee = table::people()
        ->join('tbl_company_data', 'tbl_people.id', '=', 'tbl_company_data.reference')
        ->where('tbl_people.employmentstatus', 'Active')->get();
    table::reportviews()->where('report_id', 2)->update(['last_viewed' => $today]);
    $tf = table::settings()->value("time_format");

    return view('team-attendance', compact('meetings', 'users', 'campuses', 'empAtten', 'employee', 'tf'));
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
