<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Services\CapabilityService;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Models\Person::class => \App\Policies\PersonPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        /*$this->registerPolicies();
        Gate::define('admin', function ($user) {
            return $user->role_id !== 2;
        });*/
        //
         $this->registerPolicies();
    // Radio playlist management — delegate to capability service (role-based permission by key)
    Gate::define('manage-playlists', function ($user) {
        return app(CapabilityService::class)->roleHasPermissionKey((int) $user->role_id, 'radio-programming');
    });
    Gate::define('view-followup', [\App\Policies\FollowupPolicy::class, 'view']);
    Gate::define('update-followup', [\App\Policies\FollowupPolicy::class, 'update']);
    Gate::define('assign-followup', [\App\Policies\FollowupPolicy::class, 'assign']);
    Gate::define('download-followup', [\App\Policies\FollowupPolicy::class, 'download']);
    }
}
