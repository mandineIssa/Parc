<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

class LoginController
{
    public function authenticated(Request $request, $user)
    {
        if ($user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return redirect()->intended('/dashboard');
    }
}