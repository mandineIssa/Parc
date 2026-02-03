<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory,Notifiable;
   
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

    public function isUser()
{
    return $this->role === 'user';
}

        /**
     * Vérifier si l'utilisateur peut gérer les utilisateurs
     */
    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin();
    }

    
    

    // ... vos attributs existants ...

    /**
     * Vérifier si l'utilisateur peut approuver une approbation
     */
    public function canApprove($approval = null)
    {
        $role = strtolower(trim((string) ($this->role ?? '')));
        
        // Vérifier les rôles autorisés
        $isAuthorized = in_array($role, ['super_admin', 'responsable_approbation', 'admin'])
            || $this->email === 'superadmin@cofina.sn';
        
        // Si une approbation est fournie, vérifier des conditions supplémentaires
        if ($approval) {
            // Un utilisateur ne peut pas approuver sa propre demande (sauf si super admin)
            if ($this->id === $approval->submitted_by && $role !== 'super_admin') {
                return false;
            }
            
            // Vérifier que l'approbation est en attente
            if ($approval->status !== 'pending') {
                return false;
            }
        }
        
        return $isAuthorized;
    }

    /**
     * Vérifier si l'utilisateur peut rejeter une approbation
     */
    public function canReject($approval = null)
    {
        // Mêmes permissions que pour l'approbation
        return $this->canApprove($approval);
    }

    /**
     * Vérifier si l'utilisateur peut voir une approbation
     */
    public function canViewApproval($approval)
    {
        // Les administrateurs peuvent voir toutes les approbations
        if ($this->canApprove()) {
            return true;
        }
        
        // L'utilisateur qui a soumis la demande peut la voir
        if ($this->id === $approval->submitted_by) {
            return true;
        }
        
        // Les agents IT peuvent voir les approbations
        $role = strtolower(trim((string) ($this->role ?? '')));
        if (in_array($role, ['agent_it', 'admin', 'super_admin'])) {
            return true;
        }
        
        return false;
    }

}