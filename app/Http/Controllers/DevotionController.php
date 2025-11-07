<?php
namespace App\Http\Controllers;

use App\Classes\table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DevotionController extends Controller
{
    /**
     * Store a new devotion entry submitted by the user.
     * Validates input, fetches employee info, then inserts the devotion into the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the form input
        $request->validate([
            'devotion_date' => 'required|date',
            'devotion_text' => 'required|string',
        ]);
        
        // Get the authenticated user's reference ID and ID number
        $id = \Auth::user()->reference;
        $idno = \Auth::user()->idno;

        // Query the tbl_people table to retrieve the user's name
        $q = table::people()->where('id', $id)->select('firstname', 'mi', 'lastname')->first();

        // Format the employee's name or default to 'Unknown' if not found
        $employee = $q ? $q->lastname . ', ' . $q->firstname : 'Unknown';

        // Insert the devotion data into the tbl_people_devotion table
        DB::table('tbl_people_devotion')->insert([
            'devotion_date' => $request->devotion_date,
            'devotion_text' => $request->devotion_text,
            'reference'     => $id,
            'idno'          => $idno,
            'employee'      => $employee,
            'status'        => 'Pending',
            'comment'       => '',
            'archived'      => 0,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Devotion submitted successfully!');
    }
    
    /**
     * View all devotions submitted by the logged-in user.
     *
     * @return \Illuminate\View\View
     */
    public function viewDevotions()
    {
        // Get the logged-in user's reference
        $id = \Auth::user()->reference;

        // Retrieve all devotions linked to the current user, ordered by date
        $devotions = \DB::table('tbl_people_devotion')
            ->where('reference', $id)
            ->orderBy('devotion_date', 'desc')
            ->get();

        // Return view with the user's devotions
        return view('personal.personal-devotion-view', compact('devotions'));
    }

    /**
     * Fetch devotion records for the current user (used in AJAX/datatable views).
     * Can optionally filter by date range if provided.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPersonalDevotions(Request $request)
    {
        // Get the authenticated user's reference
        $id = \Auth::user()->reference;

        // Convert the input dates to Y-m-d format if provided
        $datefrom = $request->datefrom ? date("Y-m-d", strtotime($request->datefrom)) : null;
        $dateto = $request->dateto ? date("Y-m-d", strtotime($request->dateto)) : null;

        // Start the base query for the user's devotions
        $query = \DB::table('tbl_people_devotion')->where('reference', $id);

        // Apply date filter if both dates are set
        if ($datefrom && $dateto) {
            $query->whereBetween('devotion_date', [$datefrom, $dateto]);
        }

        // Return the filtered data as a JSON response
        $data = $query->get();
        return response()->json($data);
    }
    
   
}
