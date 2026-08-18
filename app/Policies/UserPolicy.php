<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir n'importe quel modèle.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin()
            || strtolower(trim((string) ($user->role ?? ''))) === 'agent_it';
    }

    /**
     * Déterminer si l'utilisateur peut créer un modèle.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin()
            || strtolower(trim((string) ($user->role ?? ''))) === 'agent_it';
    }

    /**
     * Déterminer si l'utilisateur peut mettre à jour le modèle.
     */
    public function update(User $user, User $model): bool
    {
        // Un utilisateur peut toujours se modifier lui-même
        if ($user->id === $model->id) {
            return true;
        }
        
        // Les super_admin peuvent tout modifier
        if ($user->isSuperAdmin()) {
            return true;
        }
        
        // Les agent_it peuvent modifier tous les utilisateurs sauf les super_admin
        if (strtolower(trim((string) ($user->role ?? ''))) === 'agent_it') {
            return $model->role !== 'super_admin' && strtolower(trim((string) ($model->role ?? ''))) !== 'superadmin';
        }
        
        // Les users normaux ne peuvent modifier que leur propre profil
        return false;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer le modèle.
     */
    public function delete(User $user, User $model): bool
    {
        // Empêcher de se supprimer soi-même
        if ($user->id === $model->id) {
            return false;
        }
        
        // Seuls les super_admin peuvent supprimer
        if ($user->isSuperAdmin()) {
            // Empêcher de supprimer le dernier super_admin
            if ($model->isSuperAdmin() &&
                User::whereIn('role', ['super_admin', 'superadmin'])->count() <= 1) {
                return false;
            }
            return true;
        }
        
        // Les autres rôles ne peuvent pas supprimer
        return false;
    }

    /**
     * Déterminer si l'utilisateur peut voir le modèle.
     */
    public function view(User $user, User $model): bool
    {
        // Par défaut, autoriser à voir si on peut voir la liste
        return $this->viewAny($user);
    }
}