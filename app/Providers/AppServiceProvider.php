<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Définir les gates pour les permissions
        Gate::define('manage-users', function (User $user) {
            return $user->isAgentIT();
        });

        Gate::define('manage-all', function (User $user) {
            return $user->isSuperAdmin();
        });
    }
}