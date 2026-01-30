@extends('layouts.app')

@section('content')
<style>
    .search-highlight {
        background-color: #FFEB3B !important;
        padding: 0.1em 0.2em !important;
        border-radius: 0.2em !important;
        font-weight: 600 !important;
        color: #000 !important;
    }
    
    .equipment-row {
        transition: all 0.3s ease !important;
    }
    
    .equipment-row[style*="display: none"] {
        opacity: 0 !important;
        transform: translateX(-10px) !important;
        height: 0 !important;
        overflow: hidden !important;
    }
    
    .filter-badge {
        transition: all 0.2s ease;
    }
    
    .filter-badge.active {
        box-shadow: 0 0 0 2px #3b82f6;
    }
</style>

<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Stock Informatique</h1>
            <p class="text-gray-600 mt-2">Gestion des équipements informatiques en stock</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <a href="{{ route('dashboard.celer-informatique.export') }}" 
               class="bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Exporter CSV
            </a>
            <a href="{{ route('equipment.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Ajouter Équipement
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
                    <p class="text-sm font-medium opacity-90">Nombre Total</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total'] }}</p>
                    <p class="text-sm opacity-80 mt-1">équipements</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Valeur Totale</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['valeur_totale'], 0, ',', ' ') }} FCFA</p>
                    <p class="text-sm opacity-80 mt-1">stock total</p>
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
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Rechercher par N° série, marque, modèle, fournisseur..."
                           value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="w-full md:w-48">
                <select id="etatFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="">Tous les états</option>
                    <option value="neuf" {{ request('etat') == 'neuf' ? 'selected' : '' }}>Neuf</option>
                    <option value="bon" {{ request('etat') == 'bon' ? 'selected' : '' }}>Bon</option>
                    <option value="moyen" {{ request('etat') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                    <option value="mauvais" {{ request('etat') == 'mauvais' ? 'selected' : '' }}>Mauvais</option>
                </select>
            </div>
            
            <button id="resetFilters" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Réinitialiser
            </button>
        </div>
        
        <!-- Filtres rapides -->
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-500 flex items-center mr-3">Filtres rapides :</span>
                <button class="filter-badge px-3 py-1.5 text-sm font-medium rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 transition" data-filter="all">
                    Tous
                </button>
                <button class="filter-badge px-3 py-1.5 text-sm font-medium rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition" data-filter="neuf">
                    Neuf
                </button>
                <button class="filter-badge px-3 py-1.5 text-sm font-medium rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 transition" data-filter="bon">
                    Bon
                </button>
                <button class="filter-badge px-3 py-1.5 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition" data-filter="moyen">
                    Moyen
                </button>
                <button class="filter-badge px-3 py-1.5 text-sm font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200 transition" data-filter="mauvais">
                    Mauvais
                </button>
                <button class="filter-badge px-3 py-1.5 text-sm font-medium rounded-full bg-purple-100 text-purple-800 hover:bg-purple-200 transition" data-filter="cher">
                    Plus chers (> 500k)
                </button>
                <button class="filter-badge px-3 py-1.5 text-sm font-medium rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition" data-filter="recent">
                    Récent (mois)
                </button>
            </div>
        </div>
    </div>

    <!-- Informations de recherche -->
    <div id="searchResultsInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 hidden">
        <div class="flex justify-between items-center">
            <div>
                <span id="resultsCount" class="text-blue-800 font-medium">0 résultats</span>
                <span id="searchTerm" class="text-blue-600 text-sm ml-4"></span>
            </div>
            <button id="clearAllFilters" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Tout effacer
            </button>
        </div>
    </div>

    <!-- Tableau des équipements informatiques -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Liste des Équipements Informatiques</h2>
                    <p class="text-sm text-gray-600 mt-1" id="totalCount">{{ $equipments->total() }} équipement{{ $equipments->total() > 1 ? 's' : '' }} au total</p>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marque/Modèle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">État</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Date Livraison</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">Fournisseur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix (FCFA)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="equipmentsTableBody">
                    @forelse($equipments as $equipment)
                    <tr class="equipment-row hover:bg-gray-50 transition-colors"
                        data-id="{{ $equipment->id }}"
                        data-numero="{{ strtolower($equipment->numero_serie) }}"
                        data-marque="{{ strtolower($equipment->marque ?? '') }}"
                        data-modele="{{ strtolower($equipment->modele ?? '') }}"
                        data-etat="{{ strtolower($equipment->etat ?? '') }}"
                        data-fournisseur="{{ strtolower($equipment->fournisseur->nom ?? '') }}"
                        data-prix="{{ $equipment->prix ?? 0 }}"
                        data-date="{{ $equipment->date_livraison ? $equipment->date_livraison->format('Y-m-d') : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-gray-900 equipment-numero">{{ $equipment->numero_serie }}</span>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <div class="font-medium text-gray-900 equipment-marque">{{ $equipment->marque ?? '-' }}</div>
                                <div class="text-sm text-gray-500 equipment-modele">{{ $equipment->modele ?? '-' }}</div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            @php
                                $etatColors = [
                                    'neuf' => 'bg-green-100 text-green-800 border-green-200',
                                    'bon' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'moyen' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'mauvais' => 'bg-red-100 text-red-800 border-red-200'
                                ];
                                $color = $etatColors[$equipment->etat] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $color }} border equipment-etat">
                                {{ ucfirst($equipment->etat ?? '-') }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            <div class="text-sm text-gray-700 equipment-date">
                                {{ $equipment->date_livraison ? $equipment->date_livraison->format('d/m/Y') : '-' }}
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 hidden xl:table-cell">
                            <div class="text-sm text-gray-700 equipment-fournisseur truncate max-w-xs">
                                {{ $equipment->fournisseur->nom ?? '-' }}
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-gray-900 equipment-prix">
                                {{ number_format($equipment->prix ?? 0, 0, ',', ' ') }} FCFA
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('equipment.show', $equipment->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition"
                                   title="Voir détails">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                
                                <a href="{{ route('equipment.edit', $equipment->id) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 p-2 rounded-lg transition"
                                   title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                
                                <a href="{{ route('equipment.transitions.', $equipment) }}"  
                                   class="text-purple-600 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 p-2 rounded-lg transition"
                                   title="Changer statut">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement trouvé</h3>
                                <p class="text-gray-500 mb-6">Commencez par ajouter votre premier équipement</p>
                                <a href="{{ route('equipment.create') }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Ajouter un équipement
                                </a>
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
                        <span class="font-medium">{{ $equipments->firstItem() }}</span>
                        à 
                        <span class="font-medium">{{ $equipments->lastItem() }}</span>
                        sur 
                        <span class="font-medium">{{ $equipments->total() }}</span>
                        équipements
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    {{ $equipments->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Informations -->
    <div class="mt-8 p-6 bg-blue-50 rounded-xl border border-blue-200">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Gestion du stock informatique</h3>
                <p class="text-blue-700 mb-3">Cette section permet de gérer votre stock d'équipements informatiques. Vous pouvez suivre les états, les fournisseurs, les prix et les dates de livraison.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">États des équipements</h4>
                        <p class="text-sm text-blue-700">Neuf, Bon, Moyen, Mauvais</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Suivi financier</h4>
                        <p class="text-sm text-blue-700">Valeur totale du stock en temps réel</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Gestion des fournisseurs</h4>
                        <p class="text-sm text-blue-700">Suivi des équipements par fournisseur</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction de recherche dynamique
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les éléments
    const searchInput = document.getElementById('searchInput');
    const etatFilter = document.getElementById('etatFilter');
    const resetButton = document.getElementById('resetFilters');
    const clearAllFilters = document.getElementById('clearAllFilters');
    const filterBadges = document.querySelectorAll('.filter-badge');
    const searchResultsInfo = document.getElementById('searchResultsInfo');
    const resultsCount = document.getElementById('resultsCount');
    const searchTerm = document.getElementById('searchTerm');
    const totalCount = document.getElementById('totalCount');
    const filteredCount = document.getElementById('filteredCount');
    const noResultsRow = document.querySelector('#noResultsRow');
    
    const equipmentRows = document.querySelectorAll('.equipment-row');
    const totalEquipments = equipmentRows.length;
    let currentFilter = '';
    
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
    
    // Fonction de filtrage
    function filterEquipments() {
        const searchTermValue = normalizeText(searchInput.value.trim());
        const selectedEtat = etatFilter.value;
        let visibleCount = 0;
        
        // Supprimer les anciennes surbrillances
        removeHighlights();
        
        // Masquer la ligne "aucun résultat" par défaut
        if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
        
        // Masquer les informations de recherche si pas de recherche
        if (!searchTermValue && !selectedEtat && !currentFilter) {
            searchResultsInfo.classList.add('hidden');
        } else {
            searchResultsInfo.classList.remove('hidden');
        }
        
        // Filtrer les lignes
        equipmentRows.forEach(row => {
            const numero = row.getAttribute('data-numero');
            const marque = row.getAttribute('data-marque');
            const modele = row.getAttribute('data-modele');
            const etat = row.getAttribute('data-etat');
            const fournisseur = row.getAttribute('data-fournisseur');
            const prix = parseFloat(row.getAttribute('data-prix') || 0);
            const date = row.getAttribute('data-date');
            
            // Vérifier la correspondance avec la recherche
            const searchMatch = !searchTermValue || 
                numero.includes(searchTermValue) ||
                marque.includes(searchTermValue) ||
                modele.includes(searchTermValue) ||
                fournisseur.includes(searchTermValue);
            
            // Vérifier la correspondance avec l'état
            const etatMatch = !selectedEtat || etat === selectedEtat;
            
            // Vérifier la correspondance avec le filtre rapide
            let filterMatch = true;
            if (currentFilter === 'neuf') {
                filterMatch = etat === 'neuf';
            } else if (currentFilter === 'bon') {
                filterMatch = etat === 'bon';
            } else if (currentFilter === 'moyen') {
                filterMatch = etat === 'moyen';
            } else if (currentFilter === 'mauvais') {
                filterMatch = etat === 'mauvais';
            } else if (currentFilter === 'cher') {
                filterMatch = prix > 500000;
            } else if (currentFilter === 'recent') {
                if (date) {
                    const equipmentDate = new Date(date);
                    const thirtyDaysAgo = new Date();
                    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
                    filterMatch = equipmentDate >= thirtyDaysAgo;
                } else {
                    filterMatch = false;
                }
            } else if (currentFilter === 'all') {
                filterMatch = true;
            }
            
            if (searchMatch && etatMatch && filterMatch) {
                row.style.display = '';
                visibleCount++;
                
                // Mettre en surbrillance le texte recherché
                if (searchTermValue) {
                    const numeroElement = row.querySelector('.equipment-numero');
                    const marqueElement = row.querySelector('.equipment-marque');
                    const modeleElement = row.querySelector('.equipment-modele');
                    const fournisseurElement = row.querySelector('.equipment-fournisseur');
                    
                    if (numeroElement) highlightText(numeroElement, searchInput.value.trim());
                    if (marqueElement) highlightText(marqueElement, searchInput.value.trim());
                    if (modeleElement) highlightText(modeleElement, searchInput.value.trim());
                    if (fournisseurElement) highlightText(fournisseurElement, searchInput.value.trim());
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mettre à jour les informations de recherche
        updateSearchInfo(searchTermValue, selectedEtat, visibleCount);
        
        // Afficher le message "aucun résultat" si besoin
        if (visibleCount === 0) {
            if (noResultsRow) {
                noResultsRow.style.display = '';
            }
        }
    }
    
    // Mettre à jour les informations de recherche
    function updateSearchInfo(searchTermValue, selectedEtat, visibleCount) {
        if (resultsCount) {
            resultsCount.textContent = `${visibleCount} résultat${visibleCount > 1 ? 's' : ''}`;
        }
        
        if (filteredCount) {
            filteredCount.textContent = visibleCount === totalEquipments ? '' : `${visibleCount} sur ${totalEquipments}`;
        }
        
        if (searchTerm) {
            let infoText = '';
            if (searchTermValue) {
                infoText += `Recherche : "${searchInput.value}"`;
            }
            if (selectedEtat) {
                if (infoText) infoText += ' • ';
                infoText += `État : ${getEtatName(selectedEtat)}`;
            }
            if (currentFilter && currentFilter !== 'all') {
                if (infoText) infoText += ' • ';
                infoText += `Filtre : ${getFilterName(currentFilter)}`;
            }
            searchTerm.textContent = infoText;
        }
    }
    
    // Obtenir le nom de l'état
    function getEtatName(etat) {
        switch(etat) {
            case 'neuf': return 'Neuf';
            case 'bon': return 'Bon';
            case 'moyen': return 'Moyen';
            case 'mauvais': return 'Mauvais';
            default: return etat;
        }
    }
    
    // Obtenir le nom du filtre
    function getFilterName(filter) {
        switch(filter) {
            case 'all': return 'Tous';
            case 'neuf': return 'Neuf';
            case 'bon': return 'Bon';
            case 'moyen': return 'Moyen';
            case 'mauvais': return 'Mauvais';
            case 'cher': return 'Plus chers';
            case 'recent': return 'Récent';
            default: return '';
        }
    }
    
    // Mettre à jour l'état des badges de filtre
    function updateFilterBadges() {
        filterBadges.forEach(badge => {
            if (badge.dataset.filter === currentFilter) {
                badge.classList.add('active');
            } else {
                badge.classList.remove('active');
            }
        });
    }
    
    // Événements
    searchInput.addEventListener('input', filterEquipments);
    etatFilter.addEventListener('change', filterEquipments);
    
    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        etatFilter.value = '';
        currentFilter = '';
        updateFilterBadges();
        filterEquipments();
        searchInput.focus();
    });
    
    clearAllFilters.addEventListener('click', function() {
        searchInput.value = '';
        etatFilter.value = '';
        currentFilter = '';
        updateFilterBadges();
        filterEquipments();
        searchInput.focus();
    });
    
    filterBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            currentFilter = this.dataset.filter;
            
            if (currentFilter === 'neuf' || currentFilter === 'bon' || currentFilter === 'moyen' || currentFilter === 'mauvais') {
                etatFilter.value = currentFilter;
            } else {
                etatFilter.value = '';
            }
            
            updateFilterBadges();
            filterEquipments();
        });
    });
    
    // Debouncing pour les performances
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterEquipments, 300);
    });
    
    // Recherche avec Entrée
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            filterEquipments();
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
    
    // Initialiser le filtrage
    filterEquipments();
});
</script>
@endsection