<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, $permId)
    {
        $user = Auth::user();

        // TODO: replace this with your real permission check:
        // e.g. app(User)->hasPerm($permId) or helper function
        $has = $user && method_exists($user, 'hasPerm')
            ? $user->hasPerm($permId)
            : false;

        if ($has) return $next($request);

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Permission denied'], 403);
        }

        // Flash once, then redirect back (or to a safe page)
        return redirect()->back()->with('denied', 'You don’t have permission to access this page.');
    }
} 