@extends('layouts.app')

@section('title', 'Approbations Hors Service')

@section('header', 'Demandes de mise hors service')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="card-cofina">
        <!-- En-tête -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-cofina-red mb-2">📋 Demandes de mise hors service</h1>
            <p class="text-gray-600">Liste des équipements demandés pour mise hors service en attente de validation</p>
        </div>

        <!-- Filtres -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
            <form method="GET" action="{{ route('admin.hors-service-approvals.list') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Statut</label>
                    <select name="status" class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Origine</label>
                    <select name="type" class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                        <option value="">Toutes les origines</option>
                        <option value="stock_to_hors_service" {{ request('type') == 'stock_to_hors_service' ? 'selected' : '' }}>Stock</option>
                        <option value="parc_to_hors_service" {{ request('type') == 'parc_to_hors_service' ? 'selected' : '' }}>Parc</option>
                        <option value="maintenance_to_hors_service" {{ request('type') == 'maintenance_to_hors_service' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="N° série, nom équipement..."
                           class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-cofina px-6 py-2">
                        🔍 Filtrer
                    </button>
                    <a href="{{ route('admin.hors-service-approvals.list') }}" class="btn-cofina-outline px-6 py-2">
                        ↺ Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <!-- Tableau des approbations -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Équipement
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            N° Série
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Origine
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Raison
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Demandeur
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Date demande
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($approvals as $approval)
                    @php
                        $data = json_decode($approval->data, true);
                        $equipment = $approval->equipment;
                        
                        // Définir les labels et couleurs par type
                        $typeConfig = [
                            'stock_to_hors_service' => [
                                'label' => '📦 Stock',
                                'color' => 'blue',
                                'icon' => '📦'
                            ],
                            'parc_to_hors_service' => [
                                'label' => '👨‍💼 Parc',
                                'color' => 'green',
                                'icon' => '👨‍💼'
                            ],
                            'maintenance_to_hors_service' => [
                                'label' => '🔧 Maintenance',
                                'color' => 'yellow',
                                'icon' => '🔧'
                            ]
                        ];
                        
                        $config = $typeConfig[$approval->type] ?? [
                            'label' => $approval->type,
                            'color' => 'gray',
                            'icon' => '❓'
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $equipment->nom ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $equipment->modele ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm">{{ $equipment->numero_serie ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                      bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-800">
                                {{ $config['icon'] }} {{ $config['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <span class="font-semibold">{{ $data['raison'] ?? 'N/A' }}</span>
                                @if(isset($data['destinataire']))
                                <div class="text-xs text-gray-500">Destinataire: {{ $data['destinataire'] }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                {{ $approval->submitter->name ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $data['agent_fonction'] ?? '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                {{ $approval->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $approval->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($approval->status)
                                @case('pending')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        ⏳ En attente
                                    </span>
                                    @break
                                @case('approved')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        ✅ Approuvé
                                    </span>
                                    @if($approval->approved_at)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $approval->approved_at->format('d/m/Y') }}
                                    </div>
                                    @endif
                                    @break
                                @case('rejected')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        ❌ Rejeté
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        
                           
    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
    @php
        // Déterminer la route en fonction du type
        $viewRoutes = [
            'stock_to_hors_service' => 'admin.hors-service-approval',
            'parc_to_hors_service' => 'admin.parc-hors-service-approval',
            'maintenance_to_hors_service' => 'admin.maintenance-hors-service-approval'
        ];
        
        $viewRoute = $viewRoutes[$approval->type] ?? 'transitions.approval.show';
    @endphp
    
    <a href="{{ route($viewRoute, $approval) }}" 
       class="text-cofina-blue hover:text-cofina-red mr-3">
        👁️ Voir
    </a>
    
    @if($approval->status == 'pending' && auth()->user()->canApprove($approval))
    @php
        // Déterminer la route d'approbation en fonction du type
        $approveRoutes = [
            'stock_to_hors_service' => 'transitions.approve-hors-service',
            'parc_to_hors_service' => 'transitions.approve-parc-hors-service',
            'maintenance_to_hors_service' => 'transitions.approve-maintenance-hors-service'
        ];
        
        $approveRoute = $approveRoutes[$approval->type] ?? 'transitions.approval.show';
    @endphp
    
    <!-- Remplacer le lien par un formulaire pour méthode POST -->
    <form action="{{ route($approveRoute, $approval) }}" method="POST" class="inline-block">
        @csrf
        <button type="submit" 
                onclick="return confirm('Êtes-vous sûr de vouloir approuver cette demande de mise hors service ?')"
                class="text-green-600 hover:text-green-900 bg-transparent border-none p-0 cursor-pointer">
            ✅ Valider
        </button>
    </form>
    @endif
</td>

                            
                           
                        
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <div class="text-lg mb-2">📭 Aucune demande trouvée</div>
                            <p class="text-sm">Il n'y a pas de demande de mise hors service pour le moment.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($approvals->hasPages())
        <div class="mt-6">
            {{ $approvals->withQueryString()->links() }}
        </div>
        @endif

        <!-- Statistiques -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="text-sm font-semibold text-blue-800">Total demandes</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $approvals->total() }}</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="text-sm font-semibold text-yellow-800">En attente</div>
                    <div class="text-2xl font-bold text-yellow-600">
                        {{ $approvals->where('status', 'pending')->count() }}
                    </div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="text-sm font-semibold text-green-800">Approuvées</div>
                    <div class="text-2xl font-bold text-green-600">
                        {{ $approvals->where('status', 'approved')->count() }}
                    </div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="text-sm font-semibold text-red-800">Rejetées</div>
                    <div class="text-2xl font-bold text-red-600">
                        {{ $approvals->where('status', 'rejected')->count() }}
                    </div>
                </div>
            </div>
            
            <!-- Statistiques par origine -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="text-sm font-semibold text-blue-800">Depuis Stock</div>
                    <div class="text-2xl font-bold text-blue-600">
                        {{ $approvals->where('type', 'stock_to_hors_service')->count() }}
                    </div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="text-sm font-semibold text-green-800">Depuis Parc</div>
                    <div class="text-2xl font-bold text-green-600">
                        {{ $approvals->where('type', 'parc_to_hors_service')->count() }}
                    </div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="text-sm font-semibold text-yellow-800">Depuis Maintenance</div>
                    <div class="text-2xl font-bold text-yellow-600">
                        {{ $approvals->where('type', 'maintenance_to_hors_service')->count() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Bouton retour -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.dashboard') }}" class="btn-cofina-outline inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour au tableau de bord
            </a>
        </div>
    </div>
</div>
@endsection