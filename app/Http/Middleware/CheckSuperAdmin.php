<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CheckSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        if (Auth::user()->role !== 'super_admin') {
            return redirect()->route('dashboard')
                ->with('error', 'Accès non autorisé. Super admin requis.');
        }
        
        return $next($request);
    }
}