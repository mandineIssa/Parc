/**
 * RoleManager - Gestionnaire avancé des rôles avec contrôle CRUD
 */

class RoleManager {
    constructor() {
        this.userRole = null;
        this.userPermissions = {};
        this.userData = null;
        this.isInitialized = false;
        
        // Modes d'accès par rôle
        this.ACCESS_MODES = {
            'super_admin': 'full',      // CRUD complet
            'agent_it': 'mixed',        // CRUD sur équipements + affectations, lecture sur le reste
            'user': 'readonly'          // Lecture seulement
        };
        
        this.initializeFromDOM();
        this.checkCurrentRoute();
    }

    initializeFromDOM() {
        try {
            const roleMeta = document.querySelector('meta[name="user-role"]');
            const userMeta = document.querySelector('meta[name="user-data"]');
            
            if (roleMeta) {
                this.userRole = roleMeta.getAttribute('content');
                
                // Définir les permissions selon le mode d'accès
                this.definePermissionsByAccessMode();
                
                if (userMeta) {
                    try {
                        this.userData = JSON.parse(userMeta.getAttribute('content'));
                    } catch (e) {
                        console.warn('Impossible de parser les données utilisateur:', e);
                    }
                }
                
                this.isInitialized = true;
                console.log(`RoleManager initialisé: ${this.userRole} (mode: ${this.ACCESS_MODES[this.userRole]})`);
                
                this.applyUIRules();
                
                const event = new CustomEvent('roleManager:initialized', {
                    detail: { 
                        role: this.userRole,
                        accessMode: this.ACCESS_MODES[this.userRole]
                    }
                });
                document.dispatchEvent(event);
            }
        } catch (error) {
            console.error('Erreur lors de l\'initialisation de RoleManager:', error);
        }
    }

    definePermissionsByAccessMode() {
        const accessMode = this.ACCESS_MODES[this.userRole] || 'readonly';
        
        switch(accessMode) {
            case 'full': // Super Admin
                this.userPermissions = {
                    // CRUD complet
                    canCreate: true,
                    canRead: true,
                    canUpdate: true,
                    canDelete: true,
                    
                    // Spécifiques
                    canManageUsers: true,
                    canManageEquipment: true,
                    canManageEquipmentCRUD: true,
                    canAssignEquipment: true,
                    canManageAssignments: true,       // ← AJOUT
                    canManageAssignmentCRUD: true,    // ← AJOUT
                    canApproveRequests: true,
                    canRejectRequests: true,
                    canViewAllRequests: true,
                    canViewReports: true,
                    canExportReports: true,
                    canEditSettings: true,
                    canManageDepartments: true,
                    canBypassApproval: true,
                    canOverrideRestrictions: true
                };
                break;
                
            case 'mixed': // Agent IT
                this.userPermissions = {
                    // CRUD limité
                    canCreate: true,
                    canRead: true,
                    canUpdate: true,
                    canDelete: true,
                    
                    // Spécifiques
                    canManageUsers: false,
                    canManageEquipment: true,
                    canManageEquipmentCRUD: true,
                    canAssignEquipment: true,
                    canManageAssignments: true,       // ← AJOUT : peut gérer les affectations
                    canManageAssignmentCRUD: true,    // ← AJOUT : CRUD sur les affectations
                    canApproveRequests: false,
                    canRejectRequests: false,
                    canViewAllRequests: true,
                    canViewReports: true,
                    canExportReports: true,
                    canEditSettings: false,
                    canManageDepartments: false,
                    canBypassApproval: false,
                    canOverrideRestrictions: false
                };
                break;
                
            case 'readonly': // Utilisateur normal
            default:
                this.userPermissions = {
                    canCreate: false,
                    canRead: true,
                    canUpdate: false,
                    canDelete: false,
                    
                    canManageUsers: false,
                    canManageEquipment: false,
                    canManageEquipmentCRUD: false,
                    canAssignEquipment: false,
                    canManageAssignments: false,      // ← AJOUT
                    canManageAssignmentCRUD: false,   // ← AJOUT
                    canApproveRequests: false,
                    canRejectRequests: false,
                    canViewAllRequests: false,
                    canViewReports: false,
                    canExportReports: false,
                    canEditSettings: false,
                    canManageDepartments: false,
                    canBypassApproval: false,
                    canOverrideRestrictions: false
                };
                break;
        }
    }

    // ==================== VÉRIFICATIONS DE BASE ====================
    hasRole(role) {
        if (!this.isInitialized) return false;
        
        if (Array.isArray(role)) {
            return role.includes(this.userRole);
        }
        
        if (typeof role === 'string' && role.includes('|')) {
            return role.split('|').includes(this.userRole);
        }
        
        return this.userRole === role;
    }

    hasPermission(permission) {
        if (!this.isInitialized) return false;
        return this.userPermissions[permission] === true;
    }

    // ==================== VÉRIFICATIONS CRUD SPÉCIFIQUES ====================
    
    canCreate(entity = null) {
        if (!this.hasPermission('canCreate')) return false;
        
        if (entity === 'user' && !this.hasPermission('canManageUsers')) return false;
        if (entity === 'equipment' && !this.hasPermission('canManageEquipmentCRUD')) return false;
        // ↓ AJOUT : l'agent IT peut créer des affectations
        if (entity === 'assignment' && !this.hasPermission('canManageAssignmentCRUD')) return false;
        
        return true;
    }
    
    canRead(entity = null) {
        if (!this.hasPermission('canRead')) return false;
        
        if (this.hasRole('user')) {
            return entity !== 'admin' && entity !== 'settings';
        }
        
        return true;
    }
    
    canUpdate(entity = null, item = null) {
        if (!this.hasPermission('canUpdate')) return false;
        
        if (this.hasRole('agent_it')) {
            // ↓ MODIFIÉ : agent IT peut modifier équipements ET affectations
            if (entity === 'equipment') return this.hasPermission('canManageEquipmentCRUD');
            if (entity === 'assignment') return this.hasPermission('canManageAssignmentCRUD');
            return false;
        }
        
        if (this.hasRole('super_admin')) return true;
        
        if (this.hasRole('user')) {
            return entity === 'profile' && item && item.id === this.userData?.id;
        }
        
        return false;
    }
    
    canDelete(entity = null) {
        if (!this.hasPermission('canDelete')) return false;
        
        if (this.hasRole('agent_it')) {
            // ↓ MODIFIÉ : agent IT peut supprimer équipements ET affectations
            if (entity === 'equipment') return this.hasPermission('canManageEquipmentCRUD');
            if (entity === 'assignment') return this.hasPermission('canManageAssignmentCRUD');
            return false;
        }
        
        if (this.hasRole('super_admin')) return true;
        
        return false;
    }
    
    canImport(entity = null) {
        if (this.hasRole('super_admin')) return true;
        if (this.hasRole('agent_it') && entity === 'equipment') return true;
        return false;
    }
    
    canExport(entity = null) {
        if (this.hasRole('super_admin')) return true;
        if (this.hasRole('agent_it') && entity === 'equipment') return true;
        return false;
    }
    
    // ==================== VÉRIFICATIONS D'INTERFACE ====================
    
    shouldShowButton(action, entity = null) {
        switch(action) {
            case 'create':
            case 'add':
            case 'new':
                return this.canCreate(entity);
                
            case 'edit':
            case 'update':
            case 'modify':
                return this.canUpdate(entity);
                
            case 'delete':
            case 'remove':
            case 'destroy':
                return this.canDelete(entity);
                
            case 'import':
                return this.canImport(entity);
                
            case 'export':
                return this.canExport(entity);
                
            case 'view':
            case 'show':
            case 'read':
                return this.canRead(entity);
                
            default:
                return true;
        }
    }
    
    isLogoutElement(element) {
        if (!element) return false;
        
        const href = element.getAttribute?.('href') || element.href || '';
        const action = element.getAttribute?.('action') || element.action || '';
        const dataAction = element.getAttribute?.('data-action') || '';
        
        if (href.includes('/logout') || 
            action.includes('/logout') || 
            dataAction === 'logout' ||
            element.classList?.contains('logout-btn') ||
            element.classList?.contains('logout-link') ||
            element.classList?.contains('logout-form')) {
            return true;
        }
        
        const closestLogoutForm = element.closest?.('form[action*="/logout"]');
        const closestLogoutLink = element.closest?.('a[href*="/logout"]');
        const closestLogoutBtn = element.closest?.('.logout-btn, .logout-link, .logout-form');
        const closestDataAction = element.closest?.('[data-action="logout"]');
        
        return !!(closestLogoutForm || closestLogoutLink || closestLogoutBtn || closestDataAction);
    }
    
    // ==================== GESTION DE L'INTERFACE ====================
    
    checkCurrentRoute() {
        if (!this.isInitialized) return true;
        
        const currentPath = window.location.pathname;
        
        if (this.hasRole('user')) {
            const forbiddenPaths = [
                '/admin', '/admin/',
                '/users/create', '/users/store',
                '/users/edit/', '/users/update/',
                '/users/delete/', '/users/destroy/',
                '/settings', '/settings/',
                '/reports/create', '/reports/edit',
                '/agencies/create', '/agencies/edit',
                '/categories/create', '/categories/edit',
                '/suppliers/create', '/suppliers/edit',
                // ↓ AJOUT : utilisateur ne peut pas accéder aux routes d'affectation
                '/assignments/create', '/assignments/edit',
                '/assignments/delete', '/assignments/store',
                '/assignments/update', '/assignments/destroy',
                '/parc/create', '/parc/store',
                '/parc/edit/', '/parc/update/',
                '/parc/delete/', '/parc/destroy/'
            ];
            
            if (forbiddenPaths.some(path => currentPath.startsWith(path))) {
                this.showAccessDeniedAlert();
                setTimeout(() => this.redirectToAuthorizedPage(), 1500);
                return false;
            }
        }
        
        return true;
    }

    redirectToAuthorizedPage() {
        let redirectPath = '/dashboard';
        
        if (this.hasRole('super_admin')) {
            redirectPath = '/admin/dashboard';
        } else if (this.hasRole('agent_it')) {
            redirectPath = '/it/dashboard';
        } else if (this.hasRole('user')) {
            redirectPath = '/dashboard';
        }
        
        if (window.location.pathname !== redirectPath) {
            window.location.href = redirectPath;
        }
    }

    applyUIRules() {
        if (!this.isInitialized) return;
        
        this.hideUnauthorizedElements();
        this.disableUnauthorizedElements();
        this.protectFormsAndButtons();
        this.applyRoleStyles();
        this.setupProtectedClickHandlers();
        
        console.log('UI Rules appliquées pour:', this.userRole);
    }

    hideUnauthorizedElements() {
        const elementsToHide = {
            'super_admin': [],
            'agent_it': [
                '.admin-only',
                '[data-role="super_admin"]',
                '.user-management',
                '.system-settings'
                // ↓ NOTE : .assignment-actions N'EST PAS masqué pour agent_it
            ],
            'user': [
                '.admin-only',
                '.it-only',
                '[data-role="super_admin"]',
                '[data-role="agent_it"]',
                '.crud-actions',
                '.create-button',
                '.edit-button',
                '.delete-button',
                '.import-button',
                '.export-button',
                '.settings-section',
                '.reports-section',
                '.assignment-actions'  // ← AJOUT : cacher les actions d'affectation pour les users
            ]
        };
        
        const selectors = elementsToHide[this.userRole] || [];
        selectors.forEach(selector => {
            document.querySelectorAll(selector).forEach(element => {
                if (!this.isLogoutElement(element)) {
                    element.style.display = 'none';
                }
            });
        });
        
        document.querySelectorAll('[data-role]').forEach(element => {
            if (!this.isLogoutElement(element)) {
                const requiredRoles = element.getAttribute('data-role').split('|');
                if (!this.hasRole(requiredRoles)) {
                    element.style.display = 'none';
                }
            }
        });
        
        document.querySelectorAll('[data-permission]').forEach(element => {
            if (!this.isLogoutElement(element)) {
                const requiredPermission = element.getAttribute('data-permission');
                if (!this.hasPermission(requiredPermission)) {
                    element.style.display = 'none';
                }
            }
        });
        
        this.hideActionButtonsByRole();
        
        document.querySelectorAll('form[action*="/logout"], a[href*="/logout"], [data-action="logout"], .logout-btn, .logout-link, .logout-form').forEach(element => {
            element.style.display = '';
            element.style.visibility = 'visible';
            element.style.opacity = '1';
        });
    }

    hideActionButtonsByRole() {
        if (this.hasRole('user')) {
            document.querySelectorAll('.btn-create, .btn-edit, .btn-delete, .btn-import, .btn-export').forEach(btn => {
                if (!this.isLogoutElement(btn)) {
                    btn.style.display = 'none';
                }
            });
            
            document.querySelectorAll('a[href*="/create"], a[href*="/edit/"], a[href*="/delete/"]').forEach(link => {
                if (!this.isLogoutElement(link) && !link.href.includes('/profile')) {
                    link.style.display = 'none';
                }
            });
        }
        
        if (this.hasRole('agent_it')) {
            document.querySelectorAll('.user-action, .settings-action, .admin-action').forEach(btn => {
                if (!this.isLogoutElement(btn)) {
                    btn.style.display = 'none';
                }
            });
            // ↓ NOTE : .assignment-action N'EST PAS masqué — l'agent IT peut agir sur les affectations
        }
    }

    disableUnauthorizedElements() {
        if (this.hasRole('user')) {
            document.querySelectorAll('input, select, textarea').forEach(field => {
                if (!field.classList.contains('readonly-allowed') && !this.isLogoutElement(field)) {
                    field.disabled = true;
                    field.classList.add('readonly-field');
                }
            });
            
            document.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(btn => {
                if (this.isLogoutElement(btn)) {
                    btn.disabled = false;
                    btn.classList.remove('disabled-action');
                    return;
                }
                
                if (!btn.closest('form')?.classList.contains('profile-form')) {
                    btn.disabled = true;
                    btn.classList.add('disabled-action');
                }
            });
        }
        
        document.querySelectorAll('[data-readonly-if]').forEach(element => {
            if (!this.isLogoutElement(element)) {
                const condition = element.getAttribute('data-readonly-if');
                if (this.evaluateCondition(condition)) {
                    element.disabled = true;
                    element.classList.add('readonly-field');
                }
            }
        });
        
        document.querySelectorAll('button[data-requires], a[data-requires]').forEach(element => {
            if (this.isLogoutElement(element)) {
                element.disabled = false;
                element.classList.remove('disabled-action');
                return;
            }
            
            const requirement = element.getAttribute('data-requires');
            
            if (requirement.startsWith('role:')) {
                const requiredRole = requirement.replace('role:', '');
                if (!this.hasRole(requiredRole)) {
                    element.disabled = true;
                    element.classList.add('disabled-action');
                }
            } else if (requirement.startsWith('permission:')) {
                const requiredPermission = requirement.replace('permission:', '');
                if (!this.hasPermission(requiredPermission)) {
                    element.disabled = true;
                    element.classList.add('disabled-action');
                }
            } else if (requirement.startsWith('action:')) {
                const action = requirement.replace('action:', '');
                if (!this.shouldShowButton(action)) {
                    element.disabled = true;
                    element.classList.add('disabled-action');
                }
            }
        });
    }

    protectFormsAndButtons() {
        document.querySelectorAll('form').forEach(form => {
            if (this.isLogoutElement(form)) {
                return;
            }
            
            if (!this.isFormAuthorized(form)) {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.showAccessDeniedAlert();
                }, { capture: true });
                
                form.classList.add('readonly-form');
            }
        });
        
        document.querySelectorAll('a[href*="/edit/"], a[href*="/delete/"], a[href*="/create"]').forEach(link => {
            if (this.isLogoutElement(link)) {
                return;
            }
            
            if (!this.isLinkAuthorized(link)) {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.showAccessDeniedAlert();
                }, { capture: true });
                link.classList.add('disabled-link');
            }
        });
    }

    isFormAuthorized(form) {
        const formAction = form.getAttribute('action') || '';
        
        if (formAction.includes('/logout')) {
            return true;
        }
        
        if (this.hasRole('user')) {
            return formAction.includes('/profile') || form.classList.contains('profile-form');
        }
        
        if (this.hasRole('agent_it')) {
            return formAction.includes('/equipment') 
                || formAction.includes('/maintenance')
                || formAction.includes('/assignments')
                || formAction.includes('/assignment')
                || formAction.includes('/parc');  // ← AJOUT : route parc = affectation
        }
        
        return true;
    }

    isLinkAuthorized(link) {
        const href = link.getAttribute('href') || '';
        
        if (href.includes('/logout')) {
            return true;
        }
        
        if (href.includes('/create')) {
            return this.canCreate(this.getEntityFromUrl(href));
        }
        
        if (href.includes('/edit/')) {
            return this.canUpdate(this.getEntityFromUrl(href));
        }
        
        if (href.includes('/delete/')) {
            return this.canDelete(this.getEntityFromUrl(href));
        }
        
        return true;
    }

    getEntityFromUrl(url) {
        if (url.includes('/users/')) return 'user';
        if (url.includes('/equipment/')) return 'equipment';
        if (url.includes('/agencies/')) return 'agency';
        if (url.includes('/categories/')) return 'category';
        if (url.includes('/suppliers/')) return 'supplier';
        if (url.includes('/maintenance/')) return 'maintenance';
        if (url.includes('/assignments/')) return 'assignment';
        if (url.includes('/assignment/')) return 'assignment';
        if (url.includes('/parc/')) return 'assignment'; // ← AJOUT : route parc = entité assignment
        return null;
    }

    applyRoleStyles() {
        document.body.classList.remove('role-super_admin', 'role-agent_it', 'role-user');
        document.body.classList.remove('access-full', 'access-mixed', 'access-readonly');
        
        document.body.classList.add(`role-${this.userRole}`);
        document.body.classList.add(`access-${this.ACCESS_MODES[this.userRole]}`);
        
        if (this.hasRole('user')) {
            document.body.classList.add('readonly-mode');
        }
        
        this.addRoleBadge();
    }

    addRoleBadge() {
        const userNameElement = document.querySelector('.user-name');
        if (!userNameElement) return;

        const roleNames = {
            'super_admin': 'Super Admin',
            'admin': 'Administrateur',
            'agent_it': 'Agent IT',
            'user': 'Utilisateur'
        };

        const roleName = roleNames[this.userRole] || this.userRole;
        const roleColors = {
            'super_admin': 'bg-red-500',
            'admin': 'bg-blue-500',
            'agent_it': 'bg-green-500',
            'user': 'bg-gray-500'
        };
        
        const badgeContainer = document.getElementById('role-badge-container');
        if (badgeContainer) {
            badgeContainer.innerHTML = `
                <span class="badge bg-${this.getRoleColor()} role-badge">
                    <i class="fas ${this.getRoleIcon()} mr-1"></i>
                    ${roleNames[this.userRole] || this.userRole}
                </span>
            `;
        }
    }

    getRoleColor() {
        const colors = {
            'super_admin': 'danger',
            'agent_it': 'warning',
            'user': 'secondary'
        };
        return colors[this.userRole] || 'secondary';
    }

    getRoleIcon() {
        const icons = {
            'super_admin': 'fa-crown',
            'agent_it': 'fa-laptop-code',
            'user': 'fa-user'
        };
        return icons[this.userRole] || 'fa-user';
    }

    evaluateCondition(condition) {
        try {
            let evalCondition = condition
                .replace(/hasRole\('([^']+)'\)/g, (match, role) => this.hasRole(role))
                .replace(/hasPermission\('([^']+)'\)/g, (match, permission) => this.hasPermission(permission))
                .replace(/canCreate\('([^']*)'\)/g, (match, entity) => this.canCreate(entity))
                .replace(/canUpdate\('([^']*)'\)/g, (match, entity) => this.canUpdate(entity))
                .replace(/canDelete\('([^']*)'\)/g, (match, entity) => this.canDelete(entity))
                .replace(/canRead\('([^']*)'\)/g, (match, entity) => this.canRead(entity));
            
            return eval(evalCondition);
        } catch (e) {
            console.error('Erreur lors de l\'évaluation de la condition:', condition, e);
            return false;
        }
    }

    setupProtectedClickHandlers() {
        document.addEventListener('click', (e) => {
            if (this.isLogoutElement(e.target)) {
                return;
            }
            
            const protectedElement = e.target.closest('[data-protected-click]');
            
            if (protectedElement && !this.isLogoutElement(protectedElement)) {
                const requirement = protectedElement.getAttribute('data-protected-click');
                
                if (requirement.startsWith('role:')) {
                    const requiredRole = requirement.replace('role:', '');
                    if (!this.hasRole(requiredRole)) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.showAccessDeniedAlert();
                    }
                } else if (requirement.startsWith('permission:')) {
                    const requiredPermission = requirement.replace('permission:', '');
                    if (!this.hasPermission(requiredPermission)) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.showAccessDeniedAlert();
                    }
                } else if (requirement.startsWith('action:')) {
                    const action = requirement.replace('action:', '');
                    if (!this.shouldShowButton(action)) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.showAccessDeniedAlert();
                    }
                }
            }
            
            const disabledElement = e.target.closest('.disabled-action, .disabled-link');
            if (disabledElement && !this.isLogoutElement(disabledElement)) {
                e.preventDefault();
                e.stopPropagation();
                this.showAccessDeniedAlert();
            }
        }, { capture: true });
    }

    showAccessDeniedAlert(message = null) {
        const alertMessage = message || RoleConfig.MESSAGES.ACCESS_DENIED;
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Accès refusé',
                text: alertMessage,
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            alert(alertMessage);
        }
    }

    // ==================== HELPERS GLOBAUX ====================
    
    getUserInfo() {
        return {
            role: this.userRole,
            accessMode: this.ACCESS_MODES[this.userRole],
            permissions: this.userPermissions,
            data: this.userData,
            isSuperAdmin: this.hasRole('super_admin'),
            isAgentIT: this.hasRole('agent_it'),
            isUser: this.hasRole('user'),
            canCreate: this.canCreate(),
            canUpdate: this.canUpdate(),
            canDelete: this.canDelete(),
            canRead: this.canRead()
        };
    }
    
    canPerformAction(action, entity = null, item = null) {
        switch(action.toLowerCase()) {
            case 'create': return this.canCreate(entity);
            case 'read': return this.canRead(entity);
            case 'update': return this.canUpdate(entity, item);
            case 'delete': return this.canDelete(entity);
            case 'import': return this.canImport(entity);
            case 'export': return this.canExport(entity);
            default: return false;
        }
    }
}

// Créer une instance globale
window.RoleManager = new RoleManager();

// Exposer les méthodes principales globalement
window.hasRole = (role) => window.RoleManager.hasRole(role);
window.hasPermission = (permission) => window.RoleManager.hasPermission(permission);
window.canCreate = (entity) => window.RoleManager.canCreate(entity);
window.canRead = (entity) => window.RoleManager.canRead(entity);
window.canUpdate = (entity, item) => window.RoleManager.canUpdate(entity, item);
window.canDelete = (entity) => window.RoleManager.canDelete(entity);
window.canImport = (entity) => window.RoleManager.canImport(entity);
window.canExport = (entity) => window.RoleManager.canExport(entity);
window.canPerformAction = (action, entity, item) => window.RoleManager.canPerformAction(action, entity, item);