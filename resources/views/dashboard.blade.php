@extends('layouts.app')

@section('title', 'Dashboard')

@section('header', 'Tableau de bord')

@section('content')
<div class="p-4 sm:p-6">
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">
            Bienvenue, {{ auth()->user()->name }}!
        </h2>
        <p class="text-gray-600 mt-1">
            Vous êtes connecté en tant que 
            <span class="font-medium text-{{ 
                auth()->user()->role == 'super_admin' ? 'red' : 
                (auth()->user()->role == 'agent_it' ? 'yellow' : 'blue')
            }}-600">
                {{ auth()->user()->role == 'super_admin' ? 'Super Administrateur' : 
                   (auth()->user()->role == 'agent_it' ? 'Agent IT' : 'Utilisateur') }}
            </span>
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Carte pour Super Admin -->
        <div class="card admin-panel" data-role="super_admin">
            <div class="card-body">
                <h5 class="card-title text-red-600 mb-3">
                    <i class="fas fa-crown mr-2"></i> Administration
                </h5>
                <p class="card-text text-gray-600 mb-4">
                    Gestion complète du système et des utilisateurs
                </p>
                <div class="space-y-2">
                    <a href="{{ route('users.index') }}" 
                       class="btn btn-outline-danger btn-sm d-block text-start"
                       data-requires="permission:canManageUsers">
                        <i class="fas fa-users mr-2"></i> Gérer les utilisateurs
                    </a>
                    <a href="{{ route('reports.index') }}" 
                       class="btn btn-outline-danger btn-sm d-block text-start"
                       data-requires="permission:canViewReports">
                        <i class="fas fa-chart-bar mr-2"></i> Voir les rapports
                    </a>
                    <a href="{{ route('settings.index') }}" 
                       class="btn btn-outline-danger btn-sm d-block text-start"
                       data-requires="permission:canEditSettings">
                        <i class="fas fa-cog mr-2"></i> Paramètres système
                    </a>
                </div>
            </div>
        </div>

        <!-- Carte pour Agent IT -->
        <div class="card it-panel" data-role="super_admin|agent_it">
            <div class="card-body">
                <h5 class="card-title text-warning mb-3">
                    <i class="fas fa-laptop-code mr-2"></i> Service IT
                </h5>
                <p class="card-text text-gray-600 mb-4">
                    Gestion du parc informatique et des équipements
                </p>
                <div class="space-y-2">
                    <a href="{{ route('equipment.index') }}" 
                       class="btn btn-outline-warning btn-sm d-block text-start"
                       data-requires="permission:canManageEquipment">
                        <i class="fas fa-desktop mr-2"></i> Gérer l'équipement
                    </a>
                    <a href="{{ route('approvals.index') }}" 
                       class="btn btn-outline-warning btn-sm d-block text-start"
                       data-requires="permission:canViewAllRequests">
                        <i class="fas fa-check-circle mr-2"></i> Approbations en attente
                    </a>
                    <a href="{{ route('it.dashboard') }}" 
                       class="btn btn-outline-warning btn-sm d-block text-start">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard IT
                    </a>
                </div>
            </div>
        </div>

        <!-- Carte pour tous les utilisateurs -->
        <div class="card user-panel">
            <div class="card-body">
                <h5 class="card-title text-primary mb-3">
                    <i class="fas fa-user mr-2"></i> Mon espace
                </h5>
                <p class="card-text text-gray-600 mb-4">
                    Informations et actions personnelles
                </p>
                <div class="space-y-2">
                    <a href="{{ route('profile.edit') }}" 
                       class="btn btn-outline-primary btn-sm d-block text-start">
                        <i class="fas fa-user-edit mr-2"></i> Modifier mon profil
                    </a>
                    <a href="{{ route('equipment.my-equipment') }}" 
                       class="btn btn-outline-primary btn-sm d-block text-start">
                        <i class="fas fa-laptop mr-2"></i> Mes équipements
                    </a>
                    <a href="{{ route('requests.create') }}" 
                       class="btn btn-outline-primary btn-sm d-block text-start"
                       data-permission="canAssignEquipment">
                        <i class="fas fa-plus-circle mr-2"></i> Nouvelle demande
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques (visible seulement pour certains rôles) -->
    <div class="mt-8" data-role="super_admin|agent_it">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistiques rapides</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-sm text-gray-500">Utilisateurs actifs</div>
                <div class="text-2xl font-bold text-gray-800">{{ \App\Models\User::count() }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-sm text-gray-500">Équipements</div>
                <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Equipment::count() }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg" data-role="super_admin">
                <div class="text-sm text-gray-500">Approbations en attente</div>
                <div class="text-2xl font-bold text-gray-800">{{ \App\Models\TransitionApproval::where('status', 'pending')->count() }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg" data-role="super_admin|agent_it">
                <div class="text-sm text-gray-500">Équipements à réparer</div>
                <div class="text-2xl font-bold text-gray-8">0</div>
            </div>
        </div>
    </div>

    <!-- Actions rapides selon le rôle -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions rapides</h3>
        <div class="flex flex-wrap gap-3">
            @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('users.create') }}" 
                   class="btn btn-danger"
                   data-protected-click="role:super_admin">
                    <i class="fas fa-user-plus mr-2"></i> Ajouter un utilisateur
                </a>
                <a href="{{ route('equipment.create') }}" 
                   class="btn btn-danger"
                   data-protected-click="permission:canManageEquipment">
                    <i class="fas fa-laptop-medical mr-2"></i> Ajouter un équipement
                </a>
            @endif
            
            @if(in_array(auth()->user()->role, ['super_admin', 'agent_it']))
                <a href="{{ route('equipment.create') }}" 
                   class="btn btn-warning"
                   data-protected-click="permission:canManageEquipment">
                    <i class="fas fa-plus-circle mr-2"></i> Nouvel équipement
                </a>
                <a href="{{ route('maintenance.create') }}" 
                   class="btn btn-warning"
                   data-protected-click="role:super_admin|agent_it">
                    <i class="fas fa-tools mr-2"></i> Déclarer une maintenance
                </a>
            @endif
            
            <a href="{{ route('requests.create') }}" 
               class="btn btn-primary"
               data-permission="canAssignEquipment">
                <i class="fas fa-file-alt mr-2"></i> Nouvelle demande
            </a>
        </div>
    </div>
</div>

<script>
// Script spécifique au dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Exemple: Ajuster l'interface selon le rôle
    const userRole = '{{ auth()->user()->role }}';
    
    if (userRole === 'user') {
        // Pour les utilisateurs réguliers, simplifier l'interface
        document.querySelectorAll('.advanced-feature').forEach(el => {
            el.style.display = 'none';
        });
    }
    
    // Gérer les clics sur les boutons protégés
    document.querySelectorAll('[data-protected-click]').forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.disabled) {
                e.preventDefault();
                e.stopPropagation();
                Swal.fire({
                    icon: 'warning',
                    title: 'Action non autorisée',
                    text: 'Vous n\'avez pas les permissions nécessaires pour cette action.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
</script>
@endsection