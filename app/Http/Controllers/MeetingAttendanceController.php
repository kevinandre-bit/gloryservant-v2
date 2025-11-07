<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Attendance; // legacy model in App\
use App\Models\Meeting; // Eloquent model under App\Models

// This controller is responsible for displaying meeting attendance and handling user check-ins.
class MeetingAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a list of all meetings.
     * This method retrieves all meetings from the database
     * and passes them to the 'meetings.index' view.
     */
    public function index() {
        // Fetch all meeting records from the 'meetings' table.
        $meetings = Meeting::all();

        // Pass the meetings data to the 'meetings.index' Blade view.
        return view('meetings.index', compact('meetings'));
    }

    /**
     * Handle user check-in for a specific meeting.
     * This method ensures the user is recorded as checked in for the selected meeting.
     *
     * @param int $id - The ID of the meeting the user is checking into.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkIn($id) {
        // Retrieve the meeting by its ID or fail with a 404 error if not found.
        $meeting = Meeting::findOrFail($id);

        // Create an attendance record if it doesn't already exist for the current user and meeting.
        // If the record exists, it won't be duplicated due to 'firstOrCreate'.
        Attendance::firstOrCreate([
            'meeting_id' => $meeting->id,    // ID of the meeting
            'user_id' => auth()->id(),       // ID of the currently authenticated user
        ], [
            'checked_in_at' => now()         // Timestamp of the check-in
        ]);

        // Redirect back to the previous page with a success message.
        return redirect()->back()->with('success', 'You have checked in successfully!');
    }
}
