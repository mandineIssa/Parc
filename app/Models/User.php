<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
   
    protected $fillable = [
        'name',
        'prenom',
        'email',
        'password',
        'role',              // Rôle principal (user, agent_it, super_admin)
        'role_change',        // NOUVEAU: Rôle Change Management (N1, N2, N3, null)
        'departement',
        'fonction',
        'email_verified_at',
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

    // ==================== RÔLES PRINCIPAUX ====================
    
    /**
     * Vérifier si l'utilisateur est Super Admin
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }
    
    /**
     * Vérifier si l'utilisateur est Agent IT
     */
    public function isAgentIT()
    {
        return $this->role === 'agent_it' || $this->isSuperAdmin();
    }

    /**
     * Vérifier si l'utilisateur est un utilisateur standard
     */
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

    // ==================== RÔLES CHANGE MANAGEMENT ====================
    
    /**
     * Vérifier si l'utilisateur a un rôle Change Management
     */
    public function hasChangeRole()
    {
        return !is_null($this->role_change);
    }

    /**
     * Vérifier si l'utilisateur est N+1
     */
    public function isN1()
    {
        return $this->role_change === 'N1';
    }

    /**
     * Vérifier si l'utilisateur est N+2
     */
    public function isN2()
    {
        return $this->role_change === 'N2';
    }

    /**
     * Vérifier si l'utilisateur est N+3
     */
    public function isN3()
    {
        return $this->role_change === 'N3';
    }

    /**
     * Obtenir le libellé du rôle Change Management
     */
    public function getChangeRoleLabelAttribute()
    {
        return match($this->role_change) {
            'N1' => 'N+1 - Demandeur',
            'N2' => 'N+2 - Technicien',
            'N3' => 'N+3 - Validateur',
            default => 'Aucun rôle Change Management'
        };
    }

    /**
     * Obtenir la couleur du badge pour le rôle Change Management
     */
    public function getChangeRoleColorAttribute()
    {
        return match($this->role_change) {
            'N1' => 'blue',
            'N2' => 'green',
            'N3' => 'purple',
            default => 'gray'
        };
    }

    // ==================== PERMISSIONS EXISTANTES ====================

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

    // ==================== VALIDATION DES TÂCHES DE CONTRÔLE ====================
    
    /**
     * Vérifier si l'utilisateur peut valider une tâche de contrôle
     * 
     * @param \App\Models\ControlTask $task
     * @return bool
     */
    public function canValidateTask($task): bool
    {
        // Hiérarchie des rôles Change Management
        $roleHierarchy = [
            'N1' => 1,  // Contrôleur (exécutant)
            'N2' => 2,  // Superviseur (validation)
            'N3' => 3   // Direction (validation finale)
        ];
        
        // Niveau du rôle requis pour la tâche
        $taskRoleLevel = $roleHierarchy[$task->control->responsible_role] ?? 0;
        
        // Niveau du rôle de l'utilisateur
        $userRoleLevel = $roleHierarchy[$this->role_change] ?? 0;
        
        // L'utilisateur peut valider si son niveau est supérieur au niveau requis
        // Par exemple: N2 peut valider les tâches N1, N3 peut valider les tâches N1 et N2
        return $userRoleLevel > $taskRoleLevel;
    }
}