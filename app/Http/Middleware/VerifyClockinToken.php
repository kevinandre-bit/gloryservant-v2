<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyClockinToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?: $request->header('X-Clock-Token');
        if (! $token || ! hash_equals((string) config('services.clockin.token'), (string) $token)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $next($request);
    }
}