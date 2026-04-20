<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Afficher les détails d'un utilisateur
     */
    public function show(User $user)
    {
        try {
            $user->load(['equipment' => function ($query) {
                $query->latest()->take(5);
            }]);
            $user->loadCount('equipment');
        } catch (\Exception $e) {
            $user->equipment_count = 0;
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Afficher la liste des utilisateurs
     */
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['user', 'agent_it', 'super_admin'])],
            'role_change' => ['nullable', Rule::in(['N1', 'N2', 'N3'])],
            'departement' => ['nullable', 'string', 'max:255'],
            'fonction' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur
     * CORRECTION IMPORTANTE ICI
     */


public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'prenom' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'role' => ['required', Rule::in(['user', 'agent_it', 'super_admin'])],
        'role_change' => ['nullable', Rule::in(['N1', 'N2', 'N3'])],
        'departement' => ['nullable', 'string', 'max:255'],
        'fonction' => ['nullable', 'string', 'max:255'],
    ]);

    // Mise à jour du mot de passe si fourni
    if ($request->filled('password')) {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $validated['password'] = Hash::make($request->password);
    }

    // SOLUTION 1: Mise à jour avec Query Builder (contourne les problèmes d'Elquent)
    DB::table('users')
        ->where('id', $user->id)
        ->update([
            'name' => $validated['name'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'role_change' => $validated['role_change'] ?? null, // Force la mise à jour
            'departement' => $validated['departement'] ?? null,
            'fonction' => $validated['fonction'] ?? null,
            'updated_at' => now(),
        ]);

    // Vérification immédiate
    $updated = DB::table('users')->where('id', $user->id)->first();
    Log::info('Vérification après mise à jour:', [
        'id' => $updated->id,
        'role_change' => $updated->role_change
    ]);

    return redirect()->route('users.index')
        ->with('success', 'Utilisateur mis à jour avec succès.');
}

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        // Empêcher la suppression du dernier super admin
        if ($user->isSuperAdmin() && User::where('role', 'super_admin')->count() === 1) {
            return redirect()->route('users.index')
                ->with('error', 'Impossible de supprimer le dernier super administrateur.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}