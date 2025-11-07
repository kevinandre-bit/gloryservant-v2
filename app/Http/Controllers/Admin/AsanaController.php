<?php
namespace App\Http\Controllers;

use App\Classes\table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AsanaController extends Controller
{
    /**
     * Store a new devotion entry in the database.
     * This method validates the incoming data, retrieves the user's reference and full name,
     * and stores the devotion information into the 'tbl_people_devotion' table.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate incoming request data: devotion_date must be a valid date, and devotion_text must be a string
        $request->validate([
            'devotion_date' => 'required|date', // Ensures devotion_date is provided and is a valid date
            'devotion_text' => 'required|string', // Ensures devotion_text is provided and is a string
        ]);
        
        // Get the authenticated user's reference and ID number from the Auth system
        $id = \Auth::user()->reference;
        $idno = \Auth::user()->idno;

        // Retrieve the user's full name (first name, middle initial, last name)
        $q = table::people()->where('id', $id)->select('firstname', 'mi', 'lastname')->first();
        // Format the full name, if found, otherwise set it as 'Unknown'
        $employee = $q ? $q->lastname . ', ' . $q->firstname : 'Unknown';

        // Insert the new devotion record into the 'tbl_people_devotion' table
        DB::table('tbl_people_devotion')->insert([
            'devotion_date' => $request->devotion_date, // Store the devotion date from the request
            'devotion_text' => $request->devotion_text, // Store the devotion text from the request
            'reference'     => $id, // Store the reference (user's unique ID)
            'idno'          => $idno, // Store the user's ID number
            'employee'      => $employee, // Store the user's full name
            'status'        => 'Pending', // Default status is 'Pending'
            'comment'       => '', // No comment initially
            'archived'      => 0, // Devotion is not archived by default
            'created_at'    => now(), // Set current timestamp for creation
            'updated_at'    => now(), // Set current timestamp for update
        ]);

        // Redirect back with a success message after submission
        return redirect()->back()->with('success', 'Devotion submitted successfully!');
    }

    /**
     * View all devotions for the logged-in user.
     * This method fetches all devotions of the logged-in user from the 'tbl_people_devotion' table,
     * orders them by devotion date in descending order, and returns the view with the data.
     *
     * @return \Illuminate\View\View
     */
    public function viewDevotions()
    {
        // Get the reference of the logged-in user
        $id = \Auth::user()->reference;

        // Fetch all devotions for the user, ordered by 'devotion_date' in descending order
        $devotions = \DB::table('tbl_people_devotion')
            ->where('reference', $id) // Filter by the logged-in user's reference
            ->orderBy('devotion_date', 'desc') // Order by devotion date in descending order
            ->get(); // Execute the query and fetch the results

        // Return the 'personal-devotion-view' view with the devotions data
        return view('personal.personal-devotion-view', compact('devotions'));
    }

    /**
     * Get personal devotions for the logged-in user within a specified date range.
     * This method allows for filtering devotions by a 'datefrom' and 'dateto' range,
     * and returns the results in JSON format.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPersonalDevotions(Request $request)
    {
        // Get the reference of the logged-in user
        $id = \Auth::user()->reference;

        // Format 'datefrom' and 'dateto' from the request if provided, or set as null
        $datefrom = $request->datefrom ? date("Y-m-d", strtotime($request->datefrom)) : null;
        $dateto = $request->dateto ? date("Y-m-d", strtotime($request->dateto)) : null;

        // Start the query for devotions of the logged-in user
        $query = \DB::table('tbl_people_devotion')->where('reference', $id);

        // If both 'datefrom' and 'dateto' are provided, filter the devotions by the date range
        if ($datefrom && $dateto) {
            $query->whereBetween('devotion_date', [$datefrom, $dateto]);
        }

        // Execute the query and retrieve the devotions
        $data = $query->get();

        // Return the devotions as a JSON response
        return response()->json($data);
    }
}
