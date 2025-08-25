<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Integrate the application's custom permission system with Laravel Gate
        // so calls to $user->can('permission.code') will use User::hasPermission/isAdmin.
        Gate::before(function (?User $user, $ability) {
            if (!$user) {
                return null;
            }

            // If user is admin, allow everything
            if ($user->isAdmin()) {
                return true;
            }

            // Normalize ability name if needed and check against hasPermission
            if (is_string($ability) && $user->hasPermission($ability)) {
                return true;
            }

            return null; // continue to other checks / policies
        });
    }
}
