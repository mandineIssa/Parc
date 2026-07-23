<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'role',              // user, agent_it, super_admin, eod_n3, eod_controller
        'role_change',        // Rôle Change / EOD (N1, N2, N3, CONTROLLER, null)
        'eod_signature_only_ui',
        'departement',
        'fonction',
        'signature_path',
        'signature_updated_at',
        'email_verified_at',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'eod_signature_only_ui' => 'boolean',
        'signature_updated_at' => 'datetime',
    ];

    public function hasStoredSignature(): bool
    {
        return ! empty($this->signature_path);
    }

    public function signaturePublicUrl(): ?string
    {
        if (! $this->signature_path) {
            return null;
        }

        return asset('storage/' . $this->signature_path);
    }

    /**
     * Accès aux écrans EOD N+3 (/eod/n3) : rôle principal eod_n3 ou ancien role_change N3.
     */
    public function canAccessEodAsN3(): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        return $this->role === 'eod_n3' || $this->role_change === 'N3';
    }

    /**
     * Accès aux écrans Controller EOD (/eod/controller) : rôle principal eod_controller ou role_change CONTROLLER.
     */
    public function canAccessEodAsController(): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        return $this->role === 'eod_controller' || $this->role_change === 'CONTROLLER';
    }

    /**
     * Peut soumettre la signature « Controller » : rôle principal eod_controller ou désignation CONTROLLER uniquement.
     * Exclut les comptes N+3 et un Super Admin qui n’a pas l’une de ces deux affectations Controller.
     */
    public function canSignEodControllerSlot(): bool
    {
        if ($this->role === 'eod_n3') {
            return false;
        }

        return $this->role === 'eod_controller' || $this->role_change === 'CONTROLLER';
    }

    /**
     * Menu latéral réduit : compte dédié EOD (rôle principal) ou case à cocher + role_change N3/CONTROLLER.
     */
    public function usesEodSignatureOnlySidebar(): bool
    {
        if (in_array($this->role, ['eod_n3', 'eod_controller'], true)) {
            return true;
        }

        return (bool) $this->eod_signature_only_ui
            && in_array($this->role_change, ['N3', 'CONTROLLER'], true);
    }

    /**
     * Liens N+3 dans le menu EOD réduit (exclut les comptes Controller dédiés).
     */
    public function eodSidebarShowsN3Section(): bool
    {
        if ($this->role === 'eod_controller') {
            return false;
        }
        if ($this->role === 'eod_n3') {
            return true;
        }
        if ($this->role === 'super_admin') {
            return $this->canAccessEodAsN3();
        }
        if ($this->usesEodSignatureOnlySidebar()
            && $this->role_change === 'N3'
            && $this->role !== 'eod_controller') {
            return true;
        }

        return $this->canAccessEodAsN3() && $this->role !== 'eod_controller';
    }

    /**
     * Liens Controller dans le menu EOD réduit (exclut les comptes N+3 dédiés).
     */
    public function eodSidebarShowsControllerSection(): bool
    {
        return $this->canSignEodControllerSlot();
    }

    /**
     * Compte EOD « signature seule » : pas d’accès au tableau de bord principal, redirection après login.
     */
    public function shouldBypassMainDashboard(): bool
    {
        return $this->usesEodSignatureOnlySidebar();
    }

    /**
     * Route Laravel à ouvrir après authentification (profils EOD restreints).
     */
    public function eodPostLoginRoute(): ?string
    {
        if (! $this->usesEodSignatureOnlySidebar()) {
            return null;
        }

        return 'eod.n3.pending';
    }

    /**
     * Accès Controller EOD sans accès N+3 (liste d’attente différente sur /eod/n3/fiches-en-attente).
     */
    public function isEodControllerOnly(): bool
    {
        return $this->canAccessEodAsController() && ! $this->canAccessEodAsN3();
    }

    /**
     * Libellé affichage du rôle principal (dont profils EOD dédiés).
     */
    public function getPrincipalRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'agent_it' => 'Agent IT',
            'user' => 'Utilisateur',
            'eod_n3' => 'Signataire EOD N+3',
            'eod_controller' => 'Contrôleur EOD (batch)',
            default => (string) $this->role,
        };
    }
    
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
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin' || $this->hasBootstrapSuperAccess();
    }

    public function hasBootstrapSuperAccess(): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        $email = strtolower(trim((string) $this->email));

        return $email !== '' && in_array($email, config('cofina.super_admin_emails', []), true);
    }

    public function canApproveTransitions(): bool
    {
        $role = strtolower(trim((string) ($this->role ?? '')));

        return in_array($role, ['super_admin', 'responsable_approbation', 'admin'], true)
            || $this->hasBootstrapSuperAccess();
    }

    public function isDashboardAdmin(): bool
    {
        $role = strtolower(trim((string) ($this->role ?? '')));

        return in_array($role, ['super_admin', 'admin', 'responsable_approbation'], true)
            || $this->hasBootstrapSuperAccess();
    }

    public function gpiNotifications(): HasMany
    {
        return $this->hasMany(GpiUserNotification::class);
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
     * Vérifier si l'utilisateur est Controller EOD
     */
    public function isController()
    {
        return $this->role_change === 'CONTROLLER';
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
            'CONTROLLER' => 'Controller - Validation Batch EOD',
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
            'CONTROLLER' => 'indigo',
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
        $isAuthorized = $this->canApproveTransitions();
        
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