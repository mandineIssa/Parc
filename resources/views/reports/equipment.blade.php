@extends('layouts.app')

@section('title', 'Rapport des Équipements')
@section('header', '📋 Rapport Détail des Équipements')

@section('content')
<div class="mb-6">
    <!-- Filtres -->
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">🔍 Filtres de Recherche</h2>
        <form method="GET" action="{{ route('reports.equipment') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" id="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
                    <option value="">Tous les types</option>
                    @foreach(['Réseau', 'Informatique', 'Électronique', 'Logiciel'] as $typeOption)
                        <option value="{{ $typeOption }}" {{ request('type') == $typeOption ? 'selected' : '' }}>{{ $typeOption }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="etat" class="block text-sm font-medium text-gray-700 mb-1">État</label>
                <select name="etat" id="etat" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
                    <option value="">Tous les états</option>
                    @foreach(['neuf', 'bon', 'moyen', 'mauvais'] as $etatOption)
                        <option value="{{ $etatOption }}" {{ request('etat') == $etatOption ? 'selected' : '' }}>{{ ucfirst($etatOption) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" id="statut" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
                    <option value="">Tous les statuts</option>
                    @foreach(['stock', 'parc', 'maintenance', 'hors_service', 'perdu'] as $statutOption)
                        <option value="{{ $statutOption }}" {{ request('statut') == $statutOption ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $statutOption)) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-cofina-primary flex-1">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filtrer
                </button>
                <a href="{{ route('reports.equipment') }}" class="btn-cofina-secondary px-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </a>
            </div>
        </form>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-600">Total</h3>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total'], 0, ',', ' ') }}</p>
            <p class="text-sm text-gray-500">équipements</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-600">Valeur Totale</h3>
            <p class="text-3xl font-bold text-green-600">{{ number_format($stats['total_value'], 0, ',', ' ') }} FCFA</p>
            <p class="text-sm text-gray-500">investis</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <h3 class="text-lg font-semibold text-gray-600">Prix Moyen</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['avg_price'], 0, ',', ' ') }} FCFA</p>
            <p class="text-sm text-gray-500">par équipement</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <h3 class="text-lg font-semibold text-gray-600">Prix Min</h3>
            <p class="text-3xl font-bold text-red-600">{{ number_format($stats['min_price'], 0, ',', ' ') }} FCFA</p>
            <p class="text-sm text-gray-500">le moins cher</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <h3 class="text-lg font-semibold text-gray-600">Prix Max</h3>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['max_price'], 0, ',', ' ') }} FCFA</p>
            <p class="text-sm text-gray-500">le plus cher</p>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="mb-6 flex space-x-4">
        <a href="{{ route('reports.export.equipment') }}" class="btn-cofina-primary inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Exporter les Résultats
        </a>
    </div>

    <!-- Tableau des équipements -->
    <div class="card-cofina">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📋 Liste des Équipements</h2>
        
        <div class="mb-4">
            <p class="text-sm text-gray-600">
                Affichage de {{ $equipments->firstItem() }} à {{ $equipments->lastItem() }} sur {{ $equipments->total() }} équipements
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro Série</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marque/Modèle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">État</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Livraison</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($equipments as $equipment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                {{ $equipment->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $equipment->numero_serie }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold">{{ $equipment->marque }}</div>
                            <div class="text-sm text-gray-500">{{ $equipment->modele }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $equipment->agence ? $equipment->agence->nom : 'Non attribué' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $etatColors = [
                                    'neuf' => 'bg-green-100 text-green-800',
                                    'bon' => 'bg-blue-100 text-blue-800',
                                    'moyen' => 'bg-yellow-100 text-yellow-800',
                                    'mauvais' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $etatColors[$equipment->etat] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($equipment->etat) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statutColors = [
                                    'stock' => 'bg-gray-100 text-gray-800',
                                    'parc' => 'bg-green-100 text-green-800',
                                    'maintenance' => 'bg-yellow-100 text-yellow-800',
                                    'hors_service' => 'bg-red-100 text-red-800',
                                    'perdu' => 'bg-purple-100 text-purple-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statutColors[$equipment->statut] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $equipment->statut)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold text-green-600">
                            {{ number_format($equipment->prix, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $equipment->date_livraison ? $equipment->date_livraison->format('d/m/Y') : 'N/A' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $equipments->links() }}
        </div>
    </div>

    <!-- Résumé des filtres -->
    @if(request()->hasAny(['type', 'etat', 'statut']))
    <div class="card-cofina mt-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📌 Filtres Actifs</h2>
        <div class="flex flex-wrap gap-2">
            @if(request('type'))
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                    Type: {{ request('type') }}
                    <a href="{{ route('reports.equipment', array_merge(request()->except('type'), ['type' => ''])) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                </span>
            @endif
            
            @if(request('etat'))
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                    État: {{ ucfirst(request('etat')) }}
                    <a href="{{ route('reports.equipment', array_merge(request()->except('etat'), ['etat' => ''])) }}" class="ml-1 text-green-600 hover:text-green-800">×</a>
                </span>
            @endif
            
            @if(request('statut'))
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">
                    Statut: {{ ucfirst(str_replace('_', ' ', request('statut'))) }}
                    <a href="{{ route('reports.equipment', array_merge(request()->except('statut'), ['statut' => ''])) }}" class="ml-1 text-yellow-600 hover:text-yellow-800">×</a>
                </span>
            @endif
            
            @if(request()->hasAny(['type', 'etat', 'statut']))
                <a href="{{ route('reports.equipment') }}" class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm hover:bg-gray-200">
                    Effacer tous les filtres
                </a>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection