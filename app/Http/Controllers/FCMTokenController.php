<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FCMTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:2048',
        ]);

        // Assuming your `users` table has a column `fcm_token`
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user->fcm_token = $request->token;
        $user->save();

        return response()->json(['success' => true]);
    }
}
