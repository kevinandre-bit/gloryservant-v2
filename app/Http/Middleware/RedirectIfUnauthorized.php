<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfUnauthorized
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
{
    $user = auth()->user();

    if ($user) {
        // Trying to access admin but user is not admin
        if ($request->is('dashboard') && $user->acc_type != 2) {
            return redirect('/personal/dashboard');
        }

        // Trying to access user dashboard but user is admin
        if ($request->is('personal/dashboard') && $user->acc_type == 2) {
            return redirect('/dashboard');
        }
    }

    return $next($request);
}

}
