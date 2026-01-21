<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerPolicies();

        /*
        |--------------------------------------------------------------------------
        | HELPER FUNCTION - Normaliser le rôle
        |--------------------------------------------------------------------------
        */
        $normalizeRole = function (User $user) {
            return strtolower(trim((string) ($user->role ?? '')));
        };

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN - Accès complet
        |--------------------------------------------------------------------------
        */
        Gate::define('super-admin-only', function (User $user) use ($normalizeRole) {
            $role = $normalizeRole($user);
            
            return $role === 'super_admin' 
                || $user->email === 'superadmin@cofina.sn';
        });

        /*
        |--------------------------------------------------------------------------
        | MANAGE TRANSITIONS - Approuver/Rejeter les transitions
        | ✅ CORRECTION: Utiliser exactement le même nom que dans les routes
        |--------------------------------------------------------------------------
        */
        Gate::define('manage-transitions', function (User $user) use ($normalizeRole) {
            $role = $normalizeRole($user);
            
            return in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
                || $user->email === 'superadmin@cofina.sn';
        });

        /*
        |--------------------------------------------------------------------------
        | VIEW APPROVAL - Voir une approbation
        | ✅ CORRECTION: Utiliser exactement le même nom que dans les routes
        |--------------------------------------------------------------------------
        */
        Gate::define('view-approval', function (User $user) use ($normalizeRole) {
            $role = $normalizeRole($user);
            
            return in_array($role, ['super_admin', 'responsable_approbation', 'agent_it', 'admin'])
                || $user->email === 'superadmin@cofina.sn';
        });

        /*
        |--------------------------------------------------------------------------
        | SUBMIT TRANSITION - Soumettre une transition
        |--------------------------------------------------------------------------
        */
        Gate::define('submit-transition', function (User $user) use ($normalizeRole) {
            $role = $normalizeRole($user);

            return in_array($role, ['agent_it', 'super_admin', 'admin'])
                || $user->email === 'superadmin@cofina.sn';
        });
        
        /*
        |--------------------------------------------------------------------------
        | BONUS: Gate before - Super Admin a TOUS les droits
        |--------------------------------------------------------------------------
        */
        Gate::before(function (User $user, string $ability) use ($normalizeRole) {
            $role = $normalizeRole($user);
            
            // Super admin bypass TOUTES les gates
            if ($role === 'super_admin' || $user->email === 'superadmin@cofina.sn') {
                return true;
            }
        });
    }
}