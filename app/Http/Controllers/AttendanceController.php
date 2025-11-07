<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Classes\table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Marks attendance for the currently logged-in user based on a meeting code.
     * Prevents duplicate entries for the same day and meeting.
     *
     * @param  string  $meeting_code
     * @return \Illuminate\View\View
     */
    public function markAttendance($meeting_code)
    {
        // Get the currently authenticated user
        $user = Auth::user();
        $id = $user->reference;
        $idno = $user->idno;
    
        // ✅ Retrieve meeting data from the 'meetings' table using the unique meeting code
        $meeting = DB::table('meetings')->where('meeting_code', $meeting_code)->first();
    
        // If meeting is not found, return 404 error
        if (! $meeting) {
            abort(404, 'Invalid meeting link.');
        }
    
        // Extract the meeting slug and type (use slug capitalized if type is not available)
        $meetingName = $meeting->slug;
        $meetingType = $meeting->type ?? ucfirst($meetingName);
        $todayDate = Carbon::today()->toDateString(); // Get today's date in Y-m-d format
    
        // ✅ Check if user has already marked attendance today for the same meeting
        $alreadyChecked = Attendance::where('user_id', $user->id)
            ->whereDate('meeting_date', $todayDate)
            ->where('meeting', $meetingName)
            ->exists();
    
        // ✅ Fetch employee's full name from the 'tbl_people' table
        $q = table::people()->where('id', $id)->select('firstname', 'mi', 'lastname')->first();
        $employee = $q ? $q->lastname . ', ' . $q->firstname : 'Unknown';
    
        // If attendance is not already recorded for this user today, create a new record
        if (! $alreadyChecked) {
            Attendance::create([
                'user_id' => $user->id,
                'idno' => $idno,
                'employee' => $employee,
                'meeting' => $meetingName,
                'meeting_code' => $meeting_code,
                'meeting_date' => $todayDate,
                'meeting_type' => $meetingType,
            ]);
        }
    
        // Return the success view with the meeting name and whether the user was already checked in
        return view('attendance.success', [
            'meeting' => $meetingName,
            'alreadyChecked' => $alreadyChecked,
        ]);
    }
}
