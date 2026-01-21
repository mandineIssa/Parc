<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }
        
        // Vérifie le rôle
        $user = Auth::user();
        
        // Debug (à enlever après)
        Log::info('SuperAdminMiddleware: User role = ' . $user->role);
        Log::info('SuperAdminMiddleware: User email = ' . $user->email);
        
        if ($user->role === 'super_admin') {
            return $next($request);
        }
        
        // Si ce n'est pas un super admin
        return redirect('/dashboard')->with('error', 'Accès réservé aux super administrateurs.');
    }
}