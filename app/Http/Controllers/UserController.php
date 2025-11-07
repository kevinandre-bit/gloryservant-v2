<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Return the authenticated user object from the API request.
     * Useful for retrieving the currently logged-in user from a token.
     */
    public function AuthRouteAPI(Request $request) 
    {   
        return $request->user(); // Returns authenticated user data from the API token
    }
    
    /**
     * Updates the work type of a user based on the provided reference ID.
     * Expects 'id' (reference) and 'work_type' to be passed in the request.
     */
    
    public function index()
    {
        $users = \App\Models\User::with('role')->get();
        return view('admin.users', compact('users'));
    }
}
