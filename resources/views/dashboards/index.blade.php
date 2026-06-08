@extends('layouts.app')

@section('title', 'Dashboard - Gestion des Transitions')

@section('header', 'Dashboard des Soumissions')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- En-tête avec filtres -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <!-- Boutons d'action -->
            <div class="flex flex-wrap gap-3 mt-4 md:mt-0">
                <button onclick="refreshDashboard()" class="btn-cofina-outline">
                    🔄 Rafraîchir
                </button>
                <a href="{{ route('dashboard.export') }}?{{ http_build_query(request()->query()) }}" 
                   class="btn-cofina-outline">
                    📁 Exporter
                </a>
                @if(auth()->user()->canApprove())
                <a href="{{ route('admin.approvals') }}" class="btn-cofina">
                    ✅ Gérer approbations
                </a>
                @endif
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Filtres</h2>
            <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Type de transition -->
                <div>
                    <label class="block text-sm font-semibold mb-2">Type de transition</label>
                    <select name="type" class="w-full px-4 py-2 border-2 border-cofina-gray rounded">
                        <option value="all">Tous les types</option>
                        <option value="stock_to_parc" {{ request('type') == 'stock_to_parc' ? 'selected' : '' }}>Stock → Parc</option>
                        <option value="stock_to_hors_service" {{ request('type') == 'stock_to_hors_service' ? 'selected' : '' }}>Stock → Hors Service</option>
                        <option value="parc_to_maintenance" {{ request('type') == 'parc_to_maintenance' ? 'selected' : '' }}>Parc → Maintenance</option>
                        <option value="parc_to_hors_service" {{ request('type') == 'parc_to_hors_service' ? 'selected' : '' }}>Parc → Hors Service</option>
                        <option value="parc_to_perdu" {{ request('type') == 'parc_to_perdu' ? 'selected' : '' }}>Parc → Perdu</option>
                        <option value="maintenance_to_stock" {{ request('type') == 'maintenance_to_stock' ? 'selected' : '' }}>Maintenance → Stock</option>
                        <option value="maintenance_to_hors_service" {{ request('type') == 'maintenance_to_hors_service' ? 'selected' : '' }}>Maintenance → Hors Service</option>
                    </select>
                </div>

                <!-- Statut -->
                <div>
                    <label class="block text-sm font-semibold mb-2">Statut</label>
                    <select name="status" class="w-full px-4 py-2 border-2 border-cofina-gray rounded">
                        <option value="all">Tous les statuts</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                </div>

                <!-- Période -->
                <div>
                    <label class="block text-sm font-semibold mb-2">Période</label>
                    <input type="text" name="date_range" id="date_range" 
                           value="{{ request('date_range') ?? date('d/m/Y') . ' - ' . date('d/m/Y') }}"
                           class="w-full px-4 py-2 border-2 border-cofina-gray rounded">
                </div>

                <!-- Recherche -->
                <div>
                    <label class="block text-sm font-semibold mb-2">Recherche</label>
                    <div class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="N° série, nom équipement..."
                               class="flex-1 px-4 py-2 border-2 border-cofina-gray rounded">
                        <button type="submit" class="btn-cofina px-6">
                            🔍
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn-cofina-outline px-4">
                            ↺
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Total soumissions</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <span class="text-2xl">📊</span>
                </div>
            </div>
        </div>

        <!-- En attente -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">En attente</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['pending'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <span class="text-2xl">⏳</span>
                </div>
            </div>
        </div>

        <!-- Approuvées -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Approuvées</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['approved'] }}</p>
                    <p class="text-sm text-green-600">{{ $stats['approval_rate'] }}% taux d'approbation</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <span class="text-2xl">✅</span>
                </div>
            </div>
        </div>

        <!-- Rejetées -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Rejetées</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['rejected'] }}</p>
                    <p class="text-sm text-red-600">{{ $stats['rejection_rate'] }}% taux de rejet</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <span class="text-2xl">❌</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des soumissions -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-bold text-gray-800">Soumissions récentes</h2>
        <div class="text-sm text-gray-500">
            {{ $recentSubmissions->total() }} soumission(s)
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Équipement
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Statut
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Demandeur
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentSubmissions as $submission)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">
                            {{ $submission->equipment->nom ?? 'N/A' }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $submission->equipment->numero_serie ?? '' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $typeLabels = [
                                'stock_to_parc' => '<span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">Stock→Parc</span>',
                                'stock_to_hors_service' => '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Stock→HS</span>',
                                'parc_to_maintenance' => '<span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Parc→Maint</span>',
                                'parc_to_hors_service' => '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Parc→HS</span>',
                                'parc_to_perdu' => '<span class="px-2 py-1 text-xs rounded bg-orange-100 text-orange-800">Parc→Perdu</span>',
                                'maintenance_to_stock' => '<span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Maint→Stock</span>',
                                'maintenance_to_hors_service' => '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Maint→HS</span>',
                            ];
                        @endphp
                        {!! $typeLabels[$submission->type] ?? '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">'. $submission->type .'</span>' !!}
                        <div class="text-xs text-gray-500 mt-1">
                            {{ ucfirst($submission->from_status) }} → {{ ucfirst($submission->to_status) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @switch($submission->status)
                            @case('pending')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    ⏳ En attente
                                </span>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $submission->created_at->diffForHumans() }}
                                </div>
                                @break
                            @case('approved')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    ✅ Approuvé
                                </span>
                                @if($submission->approved_at)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $submission->approved_at->format('d/m/Y') }}
                                </div>
                                @endif
                                @break
                            @case('rejected')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    ❌ Rejeté
                                </span>
                                @if($submission->rejected_at)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $submission->rejected_at->format('d/m/Y') }}
                                </div>
                                @endif
                                @break
                        @endswitch
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm">
                            {{ $submission->submitter->name ?? 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $submission->created_at->format('H:i') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $submission->created_at->format('d/m/Y') }}
                    </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
    @php
        // Déterminer la route en fonction du type
        // Utilisez des routes qui existent réellement
        $viewRoutes = [
            'stock_to_hors_service' => 'admin.hors-service-approval',
            'stock_to_parc' => 'transitions.approval.show', // Route par défaut
            'parc_to_maintenance' => 'admin.maintenance-approval',
            'parc_to_hors_service' => 'admin.parc-hors-service-approval',
            'parc_to_perdu' => 'admin.perdu-approval',
            'maintenance_to_stock' => 'admin.maintenance-to-stock-approval',
            'maintenance_to_hors_service' => 'admin.maintenance-hors-service-approval',
        ];
        
        $viewRoute = $viewRoutes[$submission->type] ?? 'transitions.approval.show';
    @endphp
    
    <a href="{{ route($viewRoute, $submission) }}" 
       class="text-cofina-blue hover:text-cofina-red mr-3">
        👁️ Voir
    </a>
    
    @if($submission->status == 'pending' && auth()->user()->canApprove())
    <a href="{{ route('transitions.approval.show', $submission) }}?action=approve" 
        class="text-green-600 hover:text-green-900">
        ✅ Valider
    </a>
    @endif
</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <div class="text-lg mb-2">📭 Aucune soumission trouvée</div>
                        <p class="text-sm">Aucune soumission ne correspond aux critères sélectionnés.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($recentSubmissions->hasPages())
    <div class="mt-6">
        {{ $recentSubmissions->withQueryString()->links() }}
    </div>
    @endif
</div>

</div>
@endsection

@push('styles')
<!-- Date Range Picker CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
<!-- Date Range Picker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
// Initialiser le date picker
$(function() {
    $('#date_range').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Appliquer',
            cancelLabel: 'Annuler',
            daysOfWeek: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
            monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        }
    });
});

// Fonction pour rafraîchir le dashboard
function refreshDashboard() {
    // Rafraîchir les statistiques via AJAX
    fetch('{{ route("dashboard.stats") }}?{{ http_build_query(request()->query()) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour les cartes de statistiques
                document.querySelectorAll('[data-stat]').forEach(card => {
                    const stat = card.getAttribute('data-stat');
                    if (data.data[stat] !== undefined) {
                        card.querySelector('.text-3xl').textContent = data.data[stat];
                    }
                });
            }
        });

    // Rafraîchir le tableau
    window.location.reload();
}

// Auto-rafraîchissement toutes les 2 minutes (optionnel)
setTimeout(() => {
    refreshDashboard();
}, 120000); // 2 minutes
</script>
@endpush