<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

/***
 -------------------------------------
| Only user with admin role would use associated method
-------------------------------------
***/
        Gate::define('admin-gate', function ($user) {
            return $user->role === 'admin';
        });

/***
 -------------------------------------
| Only user with admin & teacher role would use associated method
-------------------------------------
***/
        Gate::define('admin-teacher-gate', function ($user) {
            return in_array($user->role, ['admin', 'teacher']);
        });
    
    }
}
