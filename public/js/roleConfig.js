/**
 * Configuration des rôles et permissions - Version améliorée
 */

const RoleConfig = {
    // ==================== RÔLES DISPONIBLES ====================
    ROLES: {
        SUPER_ADMIN: 'super_admin',
        AGENT_IT: 'agent_it',
        USER: 'user'
    },
    
    // ==================== DESCRIPTION DES RÔLES ====================
    ROLE_DESCRIPTIONS: {
        super_admin: {
            name: 'Super Administrateur',
            description: 'Accès complet à toutes les fonctionnalités',
            color: 'danger',
            icon: 'fa-crown'
        },
        agent_it: {
            name: 'Agent IT',
            description: 'Gestion des équipements et maintenance',
            color: 'warning',
            icon: 'fa-laptop-code'
        },
        user: {
            name: 'Utilisateur',
            description: 'Accès en lecture seule à ses équipements',
            color: 'primary',
            icon: 'fa-user'
        }
    },

    // ==================== MODES D'ACCÈS ====================
    ACCESS_MODES: {
        super_admin: 'full',       // CRUD complet sur tout
        agent_it: 'mixed',         // CRUD sur équipements, lecture sur le reste
        user: 'readonly'          // Lecture seulement
    },

    // ==================== PERMISSIONS PAR RÔLE ====================
    PERMISSIONS: {
        super_admin: {
            // === CRUD GÉNÉRAL ===
            canCreate: true,
            canRead: true,
            canUpdate: true,
            canDelete: true,
            
            // === ADMINISTRATION ===
            canManageUsers: true,
            canDeleteUsers: true,
            canEditUsers: true,
            
            // === ÉQUIPEMENTS ===
            canManageEquipment: true,
            canManageEquipmentCRUD: true, // CRUD complet
            canDeleteEquipment: true,
            canAssignEquipment: true,
            
            // === APPROBATIONS ===
            canApproveRequests: true,
            canRejectRequests: true,
            canViewAllRequests: true,
            
            // === RAPPORTS ===
            canViewReports: true,
            canExportReports: true,
            
            // === CONFIGURATION ===
            canEditSettings: true,
            canManageDepartments: true,
            
            // === IMPORT/EXPORT ===
            canImport: true,
            canExport: true,
            
            // === ACTIONS SPÉCIALES ===
            canBypassApproval: true,
            canOverrideRestrictions: true
        },

        agent_it: {
            // === CRUD GÉNÉRAL ===
            canCreate: true,      // Seulement sur équipements
            canRead: true,
            canUpdate: true,      // Seulement sur équipements
            canDelete: true,      // Seulement sur équipements
            
            // === ADMINISTRATION ===
            canManageUsers: false,
            canDeleteUsers: false,
            canEditUsers: false,
            
            // === ÉQUIPEMENTS ===
            canManageEquipment: true,
            canManageEquipmentCRUD: true, // CRUD sur équipements seulement
            canDeleteEquipment: true,
            canAssignEquipment: true,
            
            // === APPROBATIONS ===
            canApproveRequests: false,
            canRejectRequests: false,
            canViewAllRequests: true,     // Lecture seulement
            
            // === RAPPORTS ===
            canViewReports: true,
            canExportReports: true,
            
            // === CONFIGURATION ===
            canEditSettings: false,
            canManageDepartments: false,
            
            // === IMPORT/EXPORT ===
            canImport: true,      // Seulement équipements
            canExport: true,      // Seulement équipements
            
            // === ACTIONS SPÉCIALES ===
            canBypassApproval: false,
            canOverrideRestrictions: false
        },

        user: {
            // === CRUD GÉNÉRAL ===
            canCreate: false,     // PAS DE CRÉATION
            canRead: true,        // LECTURE SEULEMENT
            canUpdate: false,     // PAS DE MODIFICATION
            canDelete: false,     // PAS DE SUPPRESSION
            
            // === ADMINISTRATION ===
            canManageUsers: false,
            canDeleteUsers: false,
            canEditUsers: false,
            
            // === ÉQUIPEMENTS ===
            canManageEquipment: false,
            canManageEquipmentCRUD: false, // Pas de CRUD
            canDeleteEquipment: false,
            canAssignEquipment: false,
            
            // === APPROBATIONS ===
            canApproveRequests: false,
            canRejectRequests: false,
            canViewAllRequests: false,
            
            // === RAPPORTS ===
            canViewReports: false,
            canExportReports: false,
            
            // === CONFIGURATION ===
            canEditSettings: false,
            canManageDepartments: false,
            
            // === IMPORT/EXPORT ===
            canImport: false,
            canExport: false,
            
            // === ACTIONS SPÉCIALES ===
            canBypassApproval: false,
            canOverrideRestrictions: false
        }
    },

    // ==================== ROUTES AUTORISÉES ====================
    ALLOWED_ROUTES: {
        super_admin: [
            '/admin', '/admin/*',
            '/it', '/it/*',
            '/dashboard',
            '/profile',
            '/reports', '/reports/*',
            '/settings', '/settings/*',
            '/equipment', '/equipment/*',
            '/approvals', '/approvals/*',
            '/users', '/users/*',
            '/agencies', '/agencies/*',
            '/categories', '/categories/*',
            '/suppliers', '/suppliers/*',
            '/maintenance', '/maintenance/*',
            '/parc', '/parc/*'
        ],
        
        agent_it: [
            '/it', '/it/*',
            '/dashboard',
            '/profile',
            '/equipment', '/equipment/*',
            '/approvals', '/approvals/*',
            '/maintenance', '/maintenance/*',
            '/parc', '/parc/*'
        ],
        
        user: [
            '/dashboard',
            '/profile',
            '/equipment/my-equipment',
            '/requests/my-requests'
        ]
    },

    // ==================== ACTIONS AUTORISÉES PAR ENTITÉ ====================
    ENTITY_ACTIONS: {
        super_admin: {
            user: ['create', 'read', 'update', 'delete'],
            equipment: ['create', 'read', 'update', 'delete', 'import', 'export'],
            agency: ['create', 'read', 'update', 'delete'],
            category: ['create', 'read', 'update', 'delete'],
            supplier: ['create', 'read', 'update', 'delete'],
            maintenance: ['create', 'read', 'update', 'delete'],
            report: ['create', 'read', 'update', 'delete', 'export'],
            settings: ['create', 'read', 'update', 'delete']
        },
        
        agent_it: {
            equipment: ['create', 'read', 'update', 'delete', 'import', 'export'],
            maintenance: ['create', 'read', 'update', 'delete'],
            report: ['read', 'export'],
            user: ['read']  // Peut voir les utilisateurs mais pas modifier
        },
        
        user: {
            equipment: ['read'],      // Lecture seulement de SES équipements
            profile: ['read', 'update'],  // Peut lire et modifier SON profil
            request: ['create', 'read']    // Peut créer et lire SES demandes
        }
    },

    // ==================== MESSAGES ====================
    MESSAGES: {
        // Messages d'erreur généraux
        ACCESS_DENIED: 'Accès non autorisé. Vous n\'avez pas les permissions nécessaires.',
        LOGIN_REQUIRED: 'Veuillez vous connecter pour accéder à cette page.',
        ROLE_REQUIRED: 'Rôle requis: ',
        PERMISSION_REQUIRED: 'Permission requise: ',
        
        // Messages spécifiques aux actions
        NO_CREATE_PERMISSION: 'Vous n\'avez pas la permission de créer cet élément.',
        NO_READ_PERMISSION: 'Vous n\'avez pas la permission de visualiser cet élément.',
        NO_UPDATE_PERMISSION: 'Vous n\'avez pas la permission de modifier cet élément.',
        NO_DELETE_PERMISSION: 'Vous n\'avez pas la permission de supprimer cet élément.',
        NO_IMPORT_PERMISSION: 'Vous n\'avez pas la permission d\'importer des données.',
        NO_EXPORT_PERMISSION: 'Vous n\'avez pas la permission d\'exporter des données.',
        
        // Messages spécifiques aux rôles
        READONLY_MODE: 'Vous êtes en mode lecture seule. Contactez un administrateur pour effectuer des modifications.',
        LIMITED_ACCESS: 'Votre accès est limité. Certaines fonctionnalités ne sont pas disponibles.',
        FULL_ACCESS: 'Vous avez un accès complet à toutes les fonctionnalités.'
    },
    
    // ==================== CONFIGURATION DE L'INTERFACE ====================
    UI_CONFIG: {
        // Éléments à masquer par rôle
        HIDDEN_ELEMENTS: {
            super_admin: [], // Rien à masquer
            agent_it: [
                '.admin-only',
                '.user-management',
                '.system-settings',
                '[data-entity="user"][data-action="create"]',
                '[data-entity="user"][data-action="delete"]',
                '[data-entity="settings"]'
            ],
            user: [
                '.admin-only',
                '.it-only',
                '.crud-actions',
                '.create-button',
                '.edit-button',
                '.delete-button',
                '.import-button',
                '.export-button',
                '[data-action="create"]',
                '[data-action="edit"]',
                '[data-action="delete"]',
                '[data-action="import"]',
                '[data-action="export"]'
            ]
        },
        
        // Classes CSS à ajouter selon le rôle
        ROLE_CLASSES: {
            super_admin: 'role-super-admin access-full',
            agent_it: 'role-agent-it access-mixed',
            user: 'role-user access-readonly'
        }
    }
};

// Validation de la configuration
RoleConfig.validate = function() {
    const errors = [];
    
    // Vérifier que tous les rôles ont des permissions
    Object.keys(this.ROLES).forEach(roleKey => {
        const role = this.ROLES[roleKey];
        if (!this.PERMISSIONS[role]) {
            errors.push(`Permissions manquantes pour le rôle: ${role}`);
        }
        if (!this.ALLOWED_ROUTES[role]) {
            errors.push(`Routes manquantes pour le rôle: ${role}`);
        }
    });
    
    if (errors.length > 0) {
        console.error('Erreurs dans RoleConfig:', errors);
        return false;
    }
    
    return true;
};

// Initialiser la validation
RoleConfig.validate();

window.RoleConfig = RoleConfig;