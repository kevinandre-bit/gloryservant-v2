<?php

namespace App\Http\Controllers;

use App\Classes\table;
use App\Request as UserRequest; // ✅ Your custom model for handling user requests
use Illuminate\Http\Request; // ✅ Laravel's request class
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Displays a list of requests submitted by the currently authenticated user.
     */
    public function index()
    {
        // Retrieve requests made by the currently authenticated user (based on reference ID)
        $requests = \App\Request::where('reference', auth()->id())
                    ->orderBy('created_at', 'desc') // Order by most recent first
                    ->get();

        // Pass the retrieved requests to the view
        return view('personal.personal-request-view', compact('requests'));
    }

    /**
     * Stores a new request submitted by the user.
     */
    public function store(Request $request)
    {
        // Validate the request input fields
        $request->validate([
            'type' => 'required|string|max:255',    // Type of request (e.g., IT, HR, General)
            'subject' => 'required|string|max:255', // Short title/subject of the request
            'message' => 'required|string',         // Detailed message of the request
        ]);

        // Get the user's internal reference ID and ID number from the authenticated user
        $id = \Auth::user()->reference;
        $idno = \Auth::user()->idno;

        // Log the reference and ID number for tracking/debugging purposes
        \Log::info([
            'reference' => $id,
            'idno' => $idno,
        ]);

        // Get employee's full name in "Lastname, Firstname" format using their reference ID
        $q = table::people()->where('id', $id)->select('firstname', 'mi', 'lastname')->first();
        $employee = $q ? $q->lastname . ', ' . $q->firstname : 'Unknown';

        // Store the request in the database using the UserRequest model
        UserRequest::create([
            'reference'     => $id,
            'idno'          => $idno,
            'employee'      => $employee,
            'type'          => $request->type,
            'message'       => "**" . $request->subject . "**\n\n" . $request->message, // Format message with markdown-like subject bold
            'status'        => 'pending', // Default status when a request is created
        ]);

        // Redirect back with a success message
        return redirect('personal.personal-request-view')->with('success', 'Your request has been submitted successfully.');
    }
}
