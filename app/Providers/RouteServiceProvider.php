<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */

    public const HOME = '/home';
    public const SYSTEM = '/dashboard';
    public const RADIO = '/radio/dashboard';
    public const PORTAL = '/PORTAL';
    protected $namespace = 'App\Http\Controllers';

    
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        // Call parent for legacy bindings then register routes via closure (Laravel 8+ compatible)
        parent::boot();

        $this->routes(function () {
            // Keep legacy string-based controller routes working by applying the
            // app namespace to both API and Web route groups.
            Route::middleware('api')
                ->prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            // load admin_v2 routes (not loaded in web.php)
            if (file_exists(base_path('routes/web.admin_v2.php'))) {
                Route::middleware('web')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/web.admin_v2.php'));
            }
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
