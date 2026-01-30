@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Stock DECELER - Électronique</h1>
            <p class="text-gray-600 mt-2">Gestion des équipements électroniques retournés (DECELER)</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <a href="{{ route('dashboard.deceler-electronique.export') }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter CSV
            </a>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Total équipements</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total'] }}</p>
                    <p class="text-sm opacity-80 mt-1">DECELER Électronique</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Valeur résiduelle totale</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['valeur_totale'] * 0.25, 2, ',', ' ') }} €</p>
                    <p class="text-sm opacity-80 mt-1">(25% de la valeur totale)</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           id="searchInput"
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="Rechercher par N° série, marque, modèle, diagnostic..."
                           value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="w-full md:w-48">
                <select id="etatFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white">
                    <option value="">Tous les états</option>
                    <option value="neuf" {{ request('etat') == 'neuf' ? 'selected' : '' }}>Neuf</option>
                    <option value="bon" {{ request('etat') == 'bon' ? 'selected' : '' }}>Bon</option>
                    <option value="moyen" {{ request('etat') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                    <option value="mauvais" {{ request('etat') == 'mauvais' ? 'selected' : '' }}>Mauvais</option>
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <select id="categorieFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white">
                    <option value="">Toutes catégories</option>
                    @foreach($categoryStats as $category => $count)
                        @if($category)
                            <option value="{{ $category }}" {{ request('categorie') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            
            <button id="resetFilters" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Réinitialiser
            </button>
        </div>
        
        <!-- Filtres avancés -->
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <select id="origineFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white">
                        <option value="">Toutes origines</option>
                        <option value="parc" {{ request('origine') == 'parc' ? 'selected' : '' }}>Parc</option>
                        <option value="maintenance" {{ request('origine') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="hors_service" {{ request('origine') == 'hors_service' ? 'selected' : '' }}>Hors Service</option>
                        <option value="stock" {{ request('origine') == 'stock' ? 'selected' : '' }}>Stock</option>
                    </select>
                </div>
                
                <div>
                    <input type="date" 
                           id="dateFromFilter"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="Date début"
                           value="{{ request('date_from') }}">
                </div>
                
                <div>
                    <input type="date" 
                           id="dateToFilter"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="Date fin"
                           value="{{ request('date_to') }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Informations de recherche -->
    <div id="searchResultsInfo" class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-4 hidden">
        <div class="flex justify-between items-center">
            <div>
                <span id="resultsCount" class="text-indigo-800 font-medium">0 résultats</span>
                <span id="searchTerm" class="text-indigo-600 text-sm ml-4"></span>
            </div>
            <button id="clearAllFilters" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Tout effacer
            </button>
        </div>
    </div>

    <!-- Tableau des équipements DECELER Électronique -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Liste des Équipements DECELER Électronique</h2>
                    <p class="text-sm text-gray-600 mt-1" id="totalCount">{{ $stocks->total() }} équipement{{ $stocks->total() > 1 ? 's' : '' }} au total</p>
                </div>
                <div class="text-sm text-gray-500">
                    <span id="filteredCount"></span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° de Série</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">État retour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marque / Modèle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date retour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnostic</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur résiduelle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="stocksTableBody">
                    @forelse($stocks as $stock)
                    <tr class="stock-row hover:bg-gray-50 transition-colors"
                        data-id="{{ $stock->id }}"
                        data-numero="{{ strtolower($stock->numero_serie) }}"
                        data-marque="{{ strtolower($stock->equipment->marque ?? '') }}"
                        data-modele="{{ strtolower($stock->equipment->modele ?? '') }}"
                        data-categorie="{{ strtolower($stock->equipment->categorie ?? '') }}"
                        data-etat="{{ strtolower($stock->deceler->etat_retour ?? '') }}"
                        data-origine="{{ strtolower($stock->deceler->origine ?? '') }}"
                        data-diagnostic="{{ strtolower($stock->deceler->diagnostic ?? '') }}"
                        data-date="{{ $stock->deceler->date_retour ? $stock->deceler->date_retour->format('Y-m-d') : '' }}">
                        <!-- N° de Série -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900 stock-numero">{{ $stock->numero_serie }}</div>
                            <div class="text-sm text-gray-500">Qty: {{ $stock->quantite }}</div>
                        </td>
                        
                        <!-- Type -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->equipment)
                                <div class="font-medium text-gray-900">{{ $stock->equipment->type ?? 'Électronique' }}</div>
                                <div class="text-xs text-gray-500 stock-categorie">{{ $stock->equipment->categorie ?? '' }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- État retour -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->deceler && $stock->deceler->etat_retour)
                                @php
                                    $etatClasses = [
                                        'neuf' => 'bg-green-100 text-green-800 border border-green-200',
                                        'bon' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                        'moyen' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                        'mauvais' => 'bg-red-100 text-red-800 border border-red-200'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $etatClasses[$stock->deceler->etat_retour] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($stock->deceler->etat_retour) }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- Marque / Modèle -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->equipment)
                                <div class="font-medium text-gray-900 stock-marque">{{ $stock->equipment->marque ?? '' }}</div>
                                <div class="text-sm text-gray-600 stock-modele">{{ $stock->equipment->modele ?? '-' }}</div>
                            @else
                                <span class="text-red-500">Équipement non trouvé</span>
                            @endif
                        </td>
                        
                        <!-- Date retour -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->deceler && $stock->deceler->date_retour)
                                <div class="font-medium text-gray-900">{{ $stock->deceler->date_retour->format('d/m/Y') }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- Origine -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->deceler && $stock->deceler->origine)
                                @php
                                    $origineClasses = [
                                        'parc' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                        'maintenance' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                        'hors_service' => 'bg-red-100 text-red-800 border border-red-200',
                                        'stock' => 'bg-gray-100 text-gray-800 border border-gray-200'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $origineClasses[$stock->deceler->origine] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($stock->deceler->origine) }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- Diagnostic -->
                        <td class="px-6 py-4">
                            @if($stock->deceler && $stock->deceler->diagnostic)
                                <div class="text-sm text-gray-700 truncate max-w-xs stock-diagnostic">{{ $stock->deceler->diagnostic }}</div>
                            @elseif($stock->deceler && $stock->deceler->raison_retour)
                                <div class="text-sm text-gray-600 truncate max-w-xs">{{ $stock->deceler->raison_retour }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- Valeur résiduelle -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->deceler && $stock->deceler->valeur_residuelle)
                                <div class="font-bold text-gray-900">{{ number_format($stock->deceler->valeur_residuelle, 0, ',', ' ') }} €</div>
                                @if($stock->equipment && $stock->equipment->prix)
                                    @php
                                        $pourcentage = $stock->deceler->valeur_residuelle > 0 ? 
                                            round(($stock->deceler->valeur_residuelle / $stock->equipment->prix) * 100, 0) : 0;
                                    @endphp
                                    <div class="text-xs text-gray-500">{{ $pourcentage }}% de la valeur</div>
                                @endif
                            @elseif($stock->equipment && $stock->equipment->prix)
                                <div class="text-gray-600">{{ number_format($stock->equipment->prix * 0.25, 0, ',', ' ') }} €</div>
                                <div class="text-xs text-gray-400">(estimation 25%)</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex justify-center space-x-2">
                                @if($stock->equipment)
                                    <a href="{{ route('equipment.show', $stock->equipment->id) }}" 
                                       class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition"
                                       title="Voir détails">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                @endif
                                @if($stock->deceler)
                                    <button type="button" 
                                            onclick="showDecelerDetails({{ $stock->deceler->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition"
                                            title="Voir fiche retour">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement électronique DECELER trouvé</h3>
                                <p class="text-gray-500 mb-6">Aucun équipement ne correspond aux critères de recherche</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <p class="text-sm text-gray-700">
                        Affichage de 
                        <span class="font-medium">{{ $stocks->firstItem() }}</span>
                        à 
                        <span class="font-medium">{{ $stocks->lastItem() }}</span>
                        sur 
                        <span class="font-medium">{{ $stocks->total() }}</span>
                        équipements
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    {{ $stocks->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Section des derniers retours -->
    <div class="mt-8 p-6 bg-indigo-50 rounded-xl border border-indigo-200">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-indigo-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-indigo-800 mb-2">Derniers retours DECELER Électronique</h3>
                <p class="text-indigo-700 mb-4">Les équipements électroniques récemment retournés et enregistrés dans le système DECELER.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mt-4">
                    @forelse($stats['recent_entries'] as $recent)
                    <div class="bg-white p-4 rounded-lg border border-indigo-100 hover:shadow-md transition-shadow">
                        <div class="font-medium text-gray-900">{{ $recent->numero_serie }}</div>
                        @if($recent->equipment)
                            <div class="text-sm text-gray-600">{{ $recent->equipment->marque }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ $recent->equipment->modele ?? '' }}</div>
                        @endif
                        @if($recent->deceler)
                            <div class="mt-2 pt-2 border-t border-gray-100">
                                <div class="text-xs text-gray-500">
                                    <div class="flex items-center mb-1">
                                        <svg class="w-3 h-3 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $recent->deceler->date_retour ? $recent->deceler->date_retour->format('d/m/Y') : '' }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-3 h-3 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        {{ ucfirst($recent->deceler->origine ?? '') }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @empty
                    <div class="col-span-5 text-center py-4">
                        <div class="text-indigo-300 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <p class="text-indigo-500">Aucun retour électronique récent</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les détails DECELER -->
<div id="decelerModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Détails DECELER</h3>
            <button onclick="closeDecelerModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="modalContent" class="space-y-4">
            <!-- Contenu dynamique -->
        </div>
    </div>
</div>

<script>
// Fonction de recherche dynamique
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les éléments
    const searchInput = document.getElementById('searchInput');
    const etatFilter = document.getElementById('etatFilter');
    const categorieFilter = document.getElementById('categorieFilter');
    const origineFilter = document.getElementById('origineFilter');
    const dateFromFilter = document.getElementById('dateFromFilter');
    const dateToFilter = document.getElementById('dateToFilter');
    const resetButton = document.getElementById('resetFilters');
    const clearAllFilters = document.getElementById('clearAllFilters');
    const searchResultsInfo = document.getElementById('searchResultsInfo');
    const resultsCount = document.getElementById('resultsCount');
    const searchTerm = document.getElementById('searchTerm');
    const totalCount = document.getElementById('totalCount');
    const filteredCount = document.getElementById('filteredCount');
    const noResultsRow = document.querySelector('#noResultsRow');
    
    const stockRows = document.querySelectorAll('.stock-row');
    const totalStocks = stockRows.length;
    
    // Fonction pour normaliser le texte (supprime les accents)
    function normalizeText(text) {
        return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    }
    
    // Fonction pour mettre en surbrillance le texte correspondant
    function highlightText(element, searchTerm) {
        if (!searchTerm || !element) return;
        
        const text = element.textContent;
        const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        element.innerHTML = text.replace(regex, '<span class="search-highlight">$1</span>');
    }
    
    // Fonction pour enlever les surbrillances
    function removeHighlights() {
        document.querySelectorAll('.search-highlight').forEach(el => {
            const parent = el.parentNode;
            parent.replaceChild(document.createTextNode(el.textContent), el);
            parent.normalize();
        });
    }
    
    // Fonction pour comparer les dates
    function isDateBetween(dateStr, fromStr, toStr) {
        if (!dateStr) return false;
        
        const date = new Date(dateStr);
        const from = fromStr ? new Date(fromStr) : null;
        const to = toStr ? new Date(toStr) : null;
        
        let valid = true;
        
        if (from) {
            valid = valid && date >= from;
        }
        
        if (to) {
            // Ajouter un jour pour inclure la date de fin
            const toPlusOne = new Date(to);
            toPlusOne.setDate(toPlusOne.getDate() + 1);
            valid = valid && date < toPlusOne;
        }
        
        return valid;
    }
    
    // Fonction de filtrage
    function filterStocks() {
        const searchTermValue = normalizeText(searchInput.value.trim());
        const selectedEtat = normalizeText(etatFilter.value);
        const selectedCategorie = normalizeText(categorieFilter.value);
        const selectedOrigine = normalizeText(origineFilter.value);
        const dateFrom = dateFromFilter.value;
        const dateTo = dateToFilter.value;
        
        let visibleCount = 0;
        
        // Supprimer les anciennes surbrillances
        removeHighlights();
        
        // Masquer la ligne "aucun résultat" par défaut
        if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
        
        // Masquer les informations de recherche si pas de recherche
        if (!searchTermValue && !selectedEtat && !selectedCategorie && !selectedOrigine && !dateFrom && !dateTo) {
            searchResultsInfo.classList.add('hidden');
        } else {
            searchResultsInfo.classList.remove('hidden');
        }
        
        // Filtrer les lignes
        stockRows.forEach(row => {
            const numero = row.getAttribute('data-numero');
            const marque = row.getAttribute('data-marque');
            const modele = row.getAttribute('data-modele');
            const categorie = row.getAttribute('data-categorie');
            const etat = row.getAttribute('data-etat');
            const origine = row.getAttribute('data-origine');
            const diagnostic = row.getAttribute('data-diagnostic');
            const date = row.getAttribute('data-date');
            
            // Vérifier la correspondance avec la recherche
            const searchMatch = !searchTermValue || 
                numero.includes(searchTermValue) ||
                marque.includes(searchTermValue) ||
                modele.includes(searchTermValue) ||
                categorie.includes(searchTermValue) ||
                diagnostic.includes(searchTermValue);
            
            // Vérifier la correspondance avec les filtres
            const etatMatch = !selectedEtat || etat === selectedEtat;
            const categorieMatch = !selectedCategorie || categorie === selectedCategorie;
            const origineMatch = !selectedOrigine || origine === selectedOrigine;
            const dateMatch = !dateFrom && !dateTo ? true : isDateBetween(date, dateFrom, dateTo);
            
            if (searchMatch && etatMatch && categorieMatch && origineMatch && dateMatch) {
                row.style.display = '';
                visibleCount++;
                
                // Mettre en surbrillance le texte recherché
                if (searchTermValue) {
                    const numeroElement = row.querySelector('.stock-numero');
                    const marqueElement = row.querySelector('.stock-marque');
                    const modeleElement = row.querySelector('.stock-modele');
                    const categorieElement = row.querySelector('.stock-categorie');
                    const diagnosticElement = row.querySelector('.stock-diagnostic');
                    
                    if (numeroElement) highlightText(numeroElement, searchInput.value.trim());
                    if (marqueElement) highlightText(marqueElement, searchInput.value.trim());
                    if (modeleElement) highlightText(modeleElement, searchInput.value.trim());
                    if (categorieElement) highlightText(categorieElement, searchInput.value.trim());
                    if (diagnosticElement) highlightText(diagnosticElement, searchInput.value.trim());
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mettre à jour les informations de recherche
        updateSearchInfo(searchTermValue, visibleCount);
        
        // Afficher le message "aucun résultat" si besoin
        if (visibleCount === 0) {
            if (noResultsRow) {
                noResultsRow.style.display = '';
            }
        }
    }
    
    // Mettre à jour les informations de recherche
    function updateSearchInfo(searchTermValue, visibleCount) {
        if (resultsCount) {
            resultsCount.textContent = `${visibleCount} résultat${visibleCount > 1 ? 's' : ''}`;
        }
        
        if (filteredCount) {
            filteredCount.textContent = visibleCount === totalStocks ? '' : `${visibleCount} sur ${totalStocks}`;
        }
        
        if (searchTerm) {
            let infoText = '';
            if (searchTermValue) {
                infoText += `Recherche : "${searchInput.value}"`;
            }
            if (etatFilter.value) {
                if (infoText) infoText += ' • ';
                infoText += `État : ${etatFilter.value}`;
            }
            if (categorieFilter.value) {
                if (infoText) infoText += ' • ';
                infoText += `Catégorie : ${categorieFilter.value}`;
            }
            if (origineFilter.value) {
                if (infoText) infoText += ' • ';
                infoText += `Origine : ${origineFilter.value}`;
            }
            if (dateFromFilter.value || dateToFilter.value) {
                if (infoText) infoText += ' • ';
                infoText += `Date : ${dateFromFilter.value || '...'} → ${dateToFilter.value || '...'}`;
            }
            searchTerm.textContent = infoText;
        }
    }
    
    // Événements
    searchInput.addEventListener('input', filterStocks);
    etatFilter.addEventListener('change', filterStocks);
    categorieFilter.addEventListener('change', filterStocks);
    origineFilter.addEventListener('change', filterStocks);
    dateFromFilter.addEventListener('change', filterStocks);
    dateToFilter.addEventListener('change', filterStocks);
    
    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        etatFilter.value = '';
        categorieFilter.value = '';
        origineFilter.value = '';
        dateFromFilter.value = '';
        dateToFilter.value = '';
        filterStocks();
        searchInput.focus();
    });
    
    clearAllFilters.addEventListener('click', function() {
        searchInput.value = '';
        etatFilter.value = '';
        categorieFilter.value = '';
        origineFilter.value = '';
        dateFromFilter.value = '';
        dateToFilter.value = '';
        filterStocks();
        searchInput.focus();
    });
    
    // Debouncing pour les performances
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterStocks, 300);
    });
    
    // Recherche avec Entrée
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            filterStocks();
        }
    });
    
    // Initialiser avec les valeurs de l'URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search')) {
        searchInput.value = urlParams.get('search');
    }
    if (urlParams.has('etat')) {
        etatFilter.value = urlParams.get('etat');
    }
    if (urlParams.has('categorie')) {
        categorieFilter.value = urlParams.get('categorie');
    }
    if (urlParams.has('origine')) {
        origineFilter.value = urlParams.get('origine');
    }
    if (urlParams.has('date_from')) {
        dateFromFilter.value = urlParams.get('date_from');
    }
    if (urlParams.has('date_to')) {
        dateToFilter.value = urlParams.get('date_to');
    }
    
    // Initialiser le filtrage
    filterStocks();
});

// Fonctions pour le modal DECELER
function showDecelerDetails(decelerId) {
    const modal = document.getElementById('decelerModal');
    const content = document.getElementById('modalContent');
    
    // Ici, vous pouvez ajouter une requête AJAX pour récupérer les détails
    // Pour l'instant, affichons un message d'exemple
    content.innerHTML = `
        <div class="space-y-3">
            <div>
                <p class="text-sm font-medium text-gray-500">Chargement des détails...</p>
                <p class="text-sm text-gray-400">Les informations détaillées du retour DECELER s'afficheront ici.</p>
            </div>
            <div class="pt-3 border-t border-gray-200">
                <p class="text-sm text-gray-600">Pour l'instant, cette fonctionnalité est en développement.</p>
                <p class="text-xs text-gray-400 mt-1">Elle permettra d'afficher toutes les informations complètes du retour.</p>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeDecelerModal() {
    document.getElementById('decelerModal').classList.add('hidden');
}

// Fermer le modal en cliquant à l'extérieur
window.onclick = function(event) {
    const modal = document.getElementById('decelerModal');
    if (event.target == modal) {
        closeDecelerModal();
    }
}
</script>

<style>
/* Styles personnalisés */
.search-highlight {
    background-color: #C7D2FE !important;
    padding: 0.1em 0.2em !important;
    border-radius: 0.2em !important;
    font-weight: 600 !important;
    color: #000 !important;
}

/* Animation pour les résultats de recherche */
.stock-row {
    transition: all 0.3s ease !important;
}

.stock-row[style*="display: none"] {
    opacity: 0 !important;
    transform: translateX(-10px) !important;
    height: 0 !important;
    overflow: hidden !important;
}

/* Animation pour le modal */
@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#decelerModal > div {
    animation: modalFadeIn 0.3s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    table {
        font-size: 0.875rem;
    }
    
    .grid-cols-5 {
        grid-template-columns: repeat(2, 1fr);
    }
    
    /* Amélioration de la recherche sur mobile */
    input[type="text"], select, input[type="date"] {
        font-size: 16px; /* Empêche le zoom sur iOS */
    }
}

/* Animation douce pour les changements */
.transition-all {
    transition: all 0.3s ease;
}
</style>
@endsection