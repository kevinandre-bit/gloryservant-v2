<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminDevotionController extends Controller
{
    /**
     * This method retrieves all the devotions from the 'tbl_people_devotion' table,
     * orders them by the 'created_at' timestamp in descending order, 
     * and passes the resulting collection to the view for display.
     *
     * @return \Illuminate\View\View The view displaying the devotions
     */
    public function index()
    {
        // Retrieve all devotions from the 'tbl_people_devotion' table, ordered by 'created_at' in descending order
        $devotions = DB::table('tbl_people_devotion')->orderBy('created_at', 'desc')->get();

        // Return the view 'admin.devotion' and pass the devotions data to it
        return view('admin.devotion', compact('devotions'));
    }
    
    
}
