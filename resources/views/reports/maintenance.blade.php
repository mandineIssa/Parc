@extends('layouts.app')

@section('title', 'Rapport de Maintenance')
@section('header', '🔧 Rapport de Maintenance')

@section('content')
<div class="mb-6">
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-600">Total</h3>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total'], 0, ',', ' ') }}</p>
            <p class="text-sm text-gray-500">interventions</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-600">Terminées</h3>
            <p class="text-3xl font-bold text-green-600">{{ number_format($stats['termines'], 0, ',', ' ') }}</p>
            <p class="text-sm text-gray-500">interventions</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <h3 class="text-lg font-semibold text-gray-600">En Cours</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['en_cours'], 0, ',', ' ') }}</p>
            <p class="text-sm text-gray-500">interventions</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <h3 class="text-lg font-semibold text-gray-600">Coût Total</h3>
            <p class="text-3xl font-bold text-red-600">{{ number_format($stats['cout_total'], 0, ',', ' ') }} FCFA</p>
            <p class="text-sm text-gray-500">dépensés</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <h3 class="text-lg font-semibold text-gray-600">Coût Moyen</h3>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['cout_moyen'], 0, ',', ' ') }} FCFA</p>
            <p class="text-sm text-gray-500">par intervention</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">🔍 Filtres de Recherche</h2>
        <form method="GET" action="{{ route('reports.maintenance') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" id="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
                    <option value="">Tous les types</option>
                    @foreach(['Préventive', 'Curative', 'Corrective', 'Prédictive'] as $typeOption)
                        <option value="{{ $typeOption }}" {{ request('type') == $typeOption ? 'selected' : '' }}>{{ $typeOption }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" id="statut" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
                    <option value="">Tous les statuts</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En Cours</option>
                    <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                    <option value="planifie" {{ request('statut') == 'planifie' ? 'selected' : '' }}>Planifié</option>
                    <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date Début</label>
                <input type="date" name="date_from" id="date_from" 
                       value="{{ request('date_from') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
            </div>
            
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date Fin</label>
                <input type="date" name="date_to" id="date_to" 
                       value="{{ request('date_to') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
            </div>
            
            <div class="md:col-span-4 flex items-end space-x-2">
                <button type="submit" class="btn-cofina-primary flex-1">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filtrer
                </button>
                <a href="{{ route('reports.maintenance') }}" class="btn-cofina-secondary px-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau des maintenances -->
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📋 Liste des Interventions de Maintenance</h2>
        
        <div class="mb-4">
            <p class="text-sm text-gray-600">
                Affichage de {{ $maintenances->firstItem() }} à {{ $maintenances->lastItem() }} sur {{ $maintenances->total() }} interventions
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technicien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coût</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($maintenances as $maintenance)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($maintenance->equipment)
                                <div class="font-semibold">{{ $maintenance->equipment->type }}</div>
                                <div class="text-sm text-gray-500">{{ $maintenance->equipment->numero_serie }}</div>
                            @else
                                <span class="text-gray-400">Équipement supprimé</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                {{ $maintenance->type ?? 'Non spécifié' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">{{ $maintenance->date_intervention ? $maintenance->date_intervention->format('d/m/Y') : 'N/A' }}</div>
                            @if($maintenance->date_fin_prevue)
                                <div class="text-xs text-gray-500">Fin: {{ $maintenance->date_fin_prevue->format('d/m/Y') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($maintenance->technician)
                                {{ $maintenance->technician->name }}
                            @else
                                <span class="text-gray-400">Non assigné</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statutColors = [
                                    'en_cours' => 'bg-yellow-100 text-yellow-800',
                                    'termine' => 'bg-green-100 text-green-800',
                                    'planifie' => 'bg-blue-100 text-blue-800',
                                    'annule' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statutColors[$maintenance->statut] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $maintenance->statut)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold {{ $maintenance->cout > 0 ? 'text-red-600' : 'text-gray-500' }}">
                            {{ number_format($maintenance->cout, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs truncate" title="{{ $maintenance->description }}">
                                {{ Str::limit($maintenance->description, 50) }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $maintenances->links() }}
        </div>
    </div>

    <!-- Équipements les plus défaillants -->
    @if($mostFailing->count() > 0)
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">⚠️ Top 10 Équipements les Plus Défaillants</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre d'Interventions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux de Défaillance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière Intervention</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($mostFailing as $index => $item)
                    @php
                        $rate = $stats['total'] > 0 ? round(($item['count'] / $stats['total']) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-sm rounded-full {{ $index < 3 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                #{{ $index + 1 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">
                            {{ $item['equipment'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-sm rounded-full {{ $item['count'] > 5 ? 'bg-red-100 text-red-800' : ($item['count'] > 2 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ $item['count'] }} intervention(s)
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                    <div class="bg-red-500 h-2.5 rounded-full" style="width: {{ min($rate, 100) }}%"></div>
                                </div>
                                <span>{{ $rate }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @php
                                $lastMaintenance = \App\Models\Maintenance::where('equipment_id', 
                                    \App\Models\Equipment::where('numero_serie', explode(' - ', $item['equipment'])[1] ?? '')->first()?->id ?? 0)
                                    ->latest('date_intervention')
                                    ->first();
                            @endphp
                            {{ $lastMaintenance && $lastMaintenance->date_intervention ? $lastMaintenance->date_intervention->format('d/m/Y') : 'N/A' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Graphique des coûts par mois -->
    <div class="card-cofina">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📈 Évolution des Coûts de Maintenance</h2>
        <div class="text-center py-8 text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <p>Les données pour le graphique d'évolution des coûts ne sont pas encore disponibles.</p>
            <p class="text-sm mt-2">Cette fonctionnalité sera bientôt implémentée.</p>
        </div>
    </div>

    <!-- Résumé des filtres -->
    @if(request()->hasAny(['type', 'statut', 'date_from', 'date_to']))
    <div class="card-cofina mt-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📌 Filtres Actifs</h2>
        <div class="flex flex-wrap gap-2">
            @if(request('type'))
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                    Type: {{ request('type') }}
                    <a href="{{ route('reports.maintenance', array_merge(request()->except('type'), ['type' => ''])) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                </span>
            @endif
            
            @if(request('statut'))
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                    Statut: {{ ucfirst(str_replace('_', ' ', request('statut'))) }}
                    <a href="{{ route('reports.maintenance', array_merge(request()->except('statut'), ['statut' => ''])) }}" class="ml-1 text-green-600 hover:text-green-800">×</a>
                </span>
            @endif
            
            @if(request('date_from'))
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">
                    Début: {{ \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') }}
                    <a href="{{ route('reports.maintenance', array_merge(request()->except('date_from'), ['date_from' => ''])) }}" class="ml-1 text-yellow-600 hover:text-yellow-800">×</a>
                </span>
            @endif
            
            @if(request('date_to'))
                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                    Fin: {{ \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') }}
                    <a href="{{ route('reports.maintenance', array_merge(request()->except('date_to'), ['date_to' => ''])) }}" class="ml-1 text-purple-600 hover:text-purple-800">×</a>
                </span>
            @endif
            
            @if(request()->hasAny(['type', 'statut', 'date_from', 'date_to']))
                <a href="{{ route('reports.maintenance') }}" class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm hover:bg-gray-200">
                    Effacer tous les filtres
                </a>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- Inclure Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Vous pouvez ajouter du JavaScript pour les graphiques ici
        // lorsqu'ils seront implémentés dans le contrôleur
    });
</script>
@endsection