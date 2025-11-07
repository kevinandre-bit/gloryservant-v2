<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware (runs on every request).
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class, // for older Laravel
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\SecurityHeaders::class,
        // ❌ Do NOT put named aliases here (e.g. 'force.user.campus' => ...)
    ];

    /**
     * Route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            // Laravel defaults
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // ✅ Your custom web-only middleware
            \App\Http\Middleware\Localization::class,
            \App\Http\Middleware\SetDbActor::class,
            \App\Http\Middleware\NoStoreForHtml::class,
            
        ],

        'api' => [
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // ❌ Do NOT add ForceUserCampusSubdomain here
        ],
    ];

    /**
     * Route middleware (aliases you can attach to routes).
     */
    protected $routeMiddleware = [
        'auth'                      => \App\Http\Middleware\Authenticate::class,
        'auth.basic'                => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'                  => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers'             => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'                       => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'                     => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed'                    => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'                  => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'                  => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // custom aliases
        'checkstatus'               => \App\Http\Middleware\CheckStatus::class,
        'admin'                     => \App\Http\Middleware\Admin::class,
        'employee'                  => \App\Http\Middleware\Employee::class,
        'redirect.if.unauthorized'  => \App\Http\Middleware\RedirectIfUnauthorized::class,
        'perm'                      => \App\Http\Middleware\EnsurePermission::class,
        // API token verification for clock-in endpoints
        'clockin'                   => \App\Http\Middleware\VerifyClockinToken::class,
    ];

    /**
     * Priority order for non-global middleware.
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
