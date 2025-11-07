<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Volunteer;

class VolunteerController extends Controller
{
    public function create()
    {
        return view('volunteers.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:volunteers,email',
            'phone' => 'nullable|string|max:20',
        ]);

        $volunteer = Volunteer::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'registered_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Registration successful!');
    }
}
