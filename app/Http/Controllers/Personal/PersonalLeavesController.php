<?php
/*
* Workday - A time clock application for employees
* Email: official.codefactor@gmail.com
* Version: 1.1
* Author: Brian Luna
* Copyright 2020 Codefactor
*/

namespace App\Http\Controllers\Personal;

use DB;
use App\Classes\table; // Custom helper for accessing database tables
use App\Classes\permission; // (Possibly unused in this file)
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt; // Used to encrypt/decrypt sensitive IDs
use App\Http\Controllers\Controller;

class PersonalLeavesController extends Controller
{
    // Display all leave records for the logged-in user
    public function index() 
    {
        // Get the current user's reference key
        $ref = \Auth::user()->reference;

        // Fetch all leaves by reference
        $l = table::leaves()->where('reference', $ref)->get();

        // Get the user's leave privilege group ID
        $lp = table::campusdata()->where('reference', $ref)->value('leaveprivilege');

        // Get the string of privileges for that leave group
        $r = table::leavegroup()->where('id', $lp)->value('leaveprivileges');

        // Convert comma-separated string into array
        $rights = explode(",", $r);
        
        // Fetch all leave types and groups
        $lt = table::leavetypes()->get();
        $lg = table::leavegroup()->get();
        
        // Pass the collected data to the view
        return view('personal.personal-leaves-view', compact('l', 'lt', 'lg', 'lp', 'rights'));
    }

    // Store a new leave request
    public function requestL(Request $request) 
    {
        // Validate request input
        $v = $request->validate([
            'type' => 'required|max:100',
            'typeid' => 'required|digits_between:0,999|max:100',
            'leavefrom' => 'required|date|max:15',
            'leaveto' => 'required|date|max:15',
            'returndate' => 'required|date|max:15',
            'reason' => 'required|max:255',
        ]);

        // Format inputs properly
        $typeid = $request->typeid;
        $type = mb_strtoupper($request->type); // Uppercase formatting
        $reason = mb_strtoupper($request->reason);
        $leavefrom = date("Y-m-d", strtotime($request->leavefrom));
        $leaveto = date("Y-m-d", strtotime($request->leaveto));
        $returndate = date("Y-m-d", strtotime($request->returndate));

        // Get user reference and ID number
        $id = \Auth::user()->reference;
        $idno = \Auth::user()->idno;

        // Get employee's full name
        $q = table::people()->where('id', $id)->select('firstname', 'mi', 'lastname')->first();
        
        // Insert leave record into the database
        table::leaves()->insert([
            'reference' => $id,
            'idno' => $idno,
            'employee' => $q->lastname.', '.$q->firstname, // Format: Lastname, Firstname
            'type' => $type,
            'typeid' => $typeid,
            'leavefrom' => $leavefrom,
            'leaveto' => $leaveto,
            'returndate' => $returndate,
            'reason' => $reason,
            'status' => 'Pending', // Default status
        ]);

        // Redirect back with a success message
        return redirect('personal/leaves/view')->with('success', trans("Leave request sent!"));
    }

    // Fetch leave requests within a date range (AJAX)
    public function getPL(Request $request) 
    {
        $id = \Auth::user()->reference;

        // Format dates
        $datefrom = date("Y-m-d", strtotime($request->datefrom));
        $dateto = date("Y-m-d", strtotime($request->dateto));

        // If no dates provided, return all leaves
        if($datefrom == null || $dateto == null ) {
            $data = table::leaves()->where('reference', $id)->get();
            return response()->json($data);
        } 
        
        // If both dates are set, filter between them
        if ($datefrom !== null AND $dateto !== null) {
            $data = table::leaves()
                        ->where('reference', $id)
                        ->whereDate('leavefrom', '<=', $dateto)
                        ->whereDate('leavefrom', '>=', $datefrom)
                        ->get();

            return response()->json($data);
        }
    }

    // View details of a specific leave request (AJAX)
    public function viewPL(Request $request) 
    {
        $id = $request->id;

        // Find leave entry by ID
        $view = table::leaves()->where('id', $id)->first();

        // Format the dates for readability
        $view->leavefrom = date('M d, Y', strtotime($view->leavefrom));
        $view->leaveto = date('M d, Y', strtotime($view->leaveto));
        $view->returndate = date('M d, Y', strtotime($view->returndate));

        return response()->json($view);
    }

    // Show the edit form for a leave request
    public function edit($id, Request $request) 
    {
        // Fetch leave record by ID
        $l = table::leaves()->where('id', $id)->first();

        // Get all leave types for the dropdown
        $lt = table::leavetypes()->get();

        // Store current type
        $type = $l->type;

        // Encrypt the ID for use in the form (to avoid exposing raw IDs)
        $e_id = ($l->id == null) ? 0 : Crypt::encryptString($l->id);

        // Render the edit form view
        return view('personal.edits.personal-leaves-edit', compact('l', 'lt', 'type', 'e_id'));
    }

    // Update a leave request in the database
    public function update(Request $request)
    {
        // Validate form data
        $v = $request->validate([
            'id' => 'required|max:200',
            'type' => 'required|max:100',
            'leavefrom' => 'required|date|max:15',
            'leaveto' => 'required|date|max:15',
            'returndate' => 'required|date|max:15',
            'reason' => 'required|max:255',
        ]);

        // Decrypt the encrypted ID
        $id = Crypt::decryptString($request->id);

        // Get updated values
        $type = mb_strtoupper($request->type);
        $leavefrom = $request->leavefrom;
        $leaveto = $request->leaveto;
        $returndate = $request->returndate;
        $reason = mb_strtoupper($request->reason);

        // Perform the update
        table::leaves()
            ->where('id', $id)
            ->update([
                'type' => $type,
                'leavefrom' => $leavefrom,
                'leaveto' => $leaveto,
                'reason' => $reason
            ]);

        return redirect('personal/leaves/view')->with('success', trans("Leave is up to date!"));
    }

    // Delete a leave request
    public function delete($id, Request $request)
    {
        // Delete record from leaves table by ID
        table::leaves()->where('id', $id)->delete();

        return redirect('personal/leaves/view')->with('success', trans("Leave has been deleted!"));
    }

}
