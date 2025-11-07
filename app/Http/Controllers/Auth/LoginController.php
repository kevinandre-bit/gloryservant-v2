<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers { 
        sendFailedLoginResponse as traitSendFailedLoginResponse; 
        authenticated as traitAuthenticated;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'portal';

    /**
     * Override to clear any “intended” URL so everyone always
     * lands on $redirectTo.
     */
    protected function sendLoginResponse(Request $request)
    {
        // Clear any previously-intended URL
        $request->session()->forget('url.intended');

        // Regenerate session to prevent fixation
        $request->session()->regenerate();

        // Clear login throttling
        $this->clearLoginAttempts($request);

        // Redirect to either your authenticated hook or the fixed path
        return $this->authenticated($request, $this->guard()->user())
                ?: redirect($this->redirectPath());
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Adjust login throttling (attempts per decay window).
     */
    protected function maxAttempts()
    {
        return 5; // attempts
    }

    protected function decayMinutes()
    {
        return 1; // minutes
    }

    /**
     * Log failed login attempts with a correlation id.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $rid = (string) Str::uuid();
        try {
            Log::warning('auth.login_failed', [
                'rid'   => $rid,
                'ip'    => $request->ip(),
                'email' => $request->input($this->username()),
            ]);
        } catch (\Throwable $e) {
            // ignore logging failures
        }
        return $this->traitSendFailedLoginResponse($request);
    }

    /**
     * Log successful logins with a correlation id.
     */
    protected function authenticated(Request $request, $user)
    {
        $rid = (string) Str::uuid();
        try {
            Log::info('auth.login_success', [
                'rid'     => $rid,
                'ip'      => $request->ip(),
                'user_id' => $user->id,
            ]);
        } catch (\Throwable $e) {
            // ignore logging failures
        }
        return $this->traitAuthenticated($request, $user);
    }
}
