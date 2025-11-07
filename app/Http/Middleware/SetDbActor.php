<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetDbActor
{
    public function handle($request, Closure $next)
    {
        $id = optional(Auth::user())->id;
        // Pass NULL safely if no user is logged in
        DB::statement('SET @actor_user_id = ' . ($id ? (int)$id : 'NULL'));
        return $next($request);
    }
}