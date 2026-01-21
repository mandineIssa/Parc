<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Vérifie si c'est un super admin
        // Accepte plusieurs formats de rôle
        $role = strtolower(trim($user->role ?? ''));
        $isSuperAdmin = in_array($role, ['super_admin', 'superadmin', 'admin']) 
            || $user->email === 'superadmin@cofina.sn';
        
        if (!$isSuperAdmin) {
            abort(403, "Accès réservé aux super administrateurs. Votre rôle: '{$user->role}'");
        }
        
        return $next($request);
    }
}