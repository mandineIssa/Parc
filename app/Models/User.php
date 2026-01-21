<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    
    protected $fillable = [
        'name',
        'prenom',           // Ajouté
        'email',
        'password',
        'role',             // Ajouté: 'user', 'agent_it', 'super_admin'
        'departement',      // Ajouté
        'fonction',
        'email_verified_at',         // Ajouté
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    

        /**
     * Relation: Équipements associés à l'utilisateur
     */
    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'user_id');
    }
    // Méthodes helper pour vérifier les rôles
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }
    
    public function isAgentIT()
    {
        return $this->role === 'agent_it' || $this->isSuperAdmin();
    }

        /**
     * Vérifier si l'utilisateur peut gérer les utilisateurs
     */
    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin();
    }
}