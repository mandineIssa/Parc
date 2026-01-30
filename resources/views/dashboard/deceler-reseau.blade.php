@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Stock DECELER - Réseau</h1>
            <p class="text-gray-600 mt-2">Gestion des équipements réseau retournés (DECELER)</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <a href="{{ route('dashboard.deceler-reseau.export') }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter CSV
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Total équipements</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total'] }}</p>
                    <p class="text-sm opacity-80 mt-1">DECELER Réseau</p>
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
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['valeur_totale'] * 0.3, 2, ',', ' ') }} €</p>
                    <p class="text-sm opacity-80 mt-1">30% de la valeur totale</p>
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
                           placeholder="Rechercher par N° série, marque, modèle..."
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
            
            <div class="w-full md:w-48">
                <select id="categorieFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
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
        
        <!-- Filtres rapides -->
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-500 flex items-center mr-3">Filtres rapides :</span>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 transition" data-filter="all">
                    Tous
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition" data-filter="neuf">
                    Neuf
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition" data-filter="moyen">
                    Moyen
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200 transition" data-filter="mauvais">
                    Mauvais
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-purple-100 text-purple-800 hover:bg-purple-200 transition" data-filter="parc">
                    Origine Parc
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-orange-100 text-orange-800 hover:bg-orange-200 transition" data-filter="maintenance">
                    Origine Maintenance
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

    <!-- Tableau des équipements DECELER Réseau -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Liste des Équipements DECELER Réseau</h2>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Raison retour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur résiduelle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="stocksTableBody">
                    @forelse($stocks as $stock)
                    <tr class="stock-row hover:bg-gray-50 transition-colors"
                        data-id="{{ $stock->id }}"
                        data-numero="{{ strtolower($stock->numero_serie) }}"
                        data-marque="{{ strtolower($stock->equipment ? $stock->equipment->marque : '') }}"
                        data-modele="{{ strtolower($stock->equipment ? $stock->equipment->modele : '') }}"
                        data-etat="{{ strtolower($stock->deceler ? $stock->deceler->etat_retour : '') }}"
                        data-categorie="{{ strtolower($stock->equipment ? $stock->equipment->categorie : '') }}"
                        data-origine="{{ strtolower($stock->deceler ? $stock->deceler->origine : '') }}">
                        <!-- N° de Série -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900 stock-numero">{{ $stock->numero_serie }}</div>
                            <div class="text-sm text-gray-500">Qty: {{ $stock->quantite }}</div>
                        </td>
                        
                        <!-- Type -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->equipment)
                                <div class="font-medium text-gray-900">{{ $stock->equipment->type ?? 'Réseau' }}</div>
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
                                        'neuf' => 'bg-green-100 text-green-800 border-green-200',
                                        'bon' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'moyen' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'mauvais' => 'bg-red-100 text-red-800 border-red-200'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $etatClasses[$stock->deceler->etat_retour] ?? 'bg-gray-100' }} border stock-etat">
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
                                        'parc' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'maintenance' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'hors_service' => 'bg-red-100 text-red-800 border-red-200'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $origineClasses[$stock->deceler->origine] ?? 'bg-gray-100' }} border stock-origine">
                                    {{ ucfirst($stock->deceler->origine) }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- Raison retour -->
                        <td class="px-6 py-4">
                            @if($stock->deceler && $stock->deceler->raison_retour)
                                <div class="text-sm text-gray-700 truncate max-w-xs">{{ $stock->deceler->raison_retour }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- Valeur résiduelle -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->deceler && $stock->deceler->valeur_residuelle)
                                <div class="font-bold text-gray-900">{{ number_format($stock->deceler->valeur_residuelle, 0, ',', ' ') }} €</div>
                            @elseif($stock->equipment && $stock->equipment->prix)
                                <div class="text-gray-600">{{ number_format($stock->equipment->prix * 0.3, 0, ',', ' ') }} €</div>
                                <div class="text-xs text-gray-400">(estimation 30%)</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($stock->equipment)
                                <a href="{{ route('equipment.show', $stock->equipment->id) }}" 
                                   class="text-green-600 hover:text-green-900 font-medium block mb-1">
                                    Détail équipement
                                </a>
                            @endif
                            @if($stock->deceler)
                                <a href="#" class="text-blue-600 hover:text-blue-900 text-xs">
                                    Voir retour
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement DECELER Réseau trouvé</h3>
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

    <!-- Récentes entrées DECELER Réseau -->
    <div class="mt-8 p-6 bg-blue-50 rounded-xl border border-blue-200">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Derniers retours DECELER Réseau</h3>
                <p class="text-blue-700 mb-3">Suivez les équipements réseau récemment retournés dans le stock DECELER</p>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mt-4">
                    @forelse($stats['recent_entries'] as $recent)
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <div class="font-medium text-gray-900">{{ $recent->numero_serie }}</div>
                        @if($recent->equipment)
                            <div class="text-sm text-gray-600">{{ $recent->equipment->marque }}</div>
                            <div class="text-xs text-gray-500">{{ $recent->equipment->modele ?? '' }}</div>
                        @endif
                        @if($recent->deceler)
                            <div class="text-xs text-gray-500 mt-2">
                                {{ $recent->deceler->origine ?? '' }} - {{ $recent->deceler->date_retour ? $recent->deceler->date_retour->format('d/m/Y') : '' }}
                            </div>
                        @endif
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">
                                Réseau
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-5 text-center text-gray-500 py-4">
                        Aucun retour réseau récent
                    </div>
                    @endforelse
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
    const categorieFilter = document.getElementById('categorieFilter');
    const resetButton = document.getElementById('resetFilters');
    const clearAllFilters = document.getElementById('clearAllFilters');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const searchResultsInfo = document.getElementById('searchResultsInfo');
    const resultsCount = document.getElementById('resultsCount');
    const searchTerm = document.getElementById('searchTerm');
    const totalCount = document.getElementById('totalCount');
    const filteredCount = document.getElementById('filteredCount');
    const noResultsRow = document.querySelector('#noResultsRow');
    
    const stockRows = document.querySelectorAll('.stock-row');
    const totalStocks = stockRows.length;
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
    function filterStocks() {
        const searchTermValue = normalizeText(searchInput.value.trim());
        const selectedEtat = etatFilter.value;
        const selectedCategorie = categorieFilter.value;
        let visibleCount = 0;
        
        // Supprimer les anciennes surbrillances
        removeHighlights();
        
        // Masquer la ligne "aucun résultat" par défaut
        if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
        
        // Masquer les informations de recherche si pas de recherche
        if (!searchTermValue && !selectedEtat && !selectedCategorie && !currentFilter) {
            searchResultsInfo.classList.add('hidden');
        } else {
            searchResultsInfo.classList.remove('hidden');
        }
        
        // Filtrer les lignes
        stockRows.forEach(row => {
            const numero = row.getAttribute('data-numero');
            const marque = row.getAttribute('data-marque');
            const modele = row.getAttribute('data-modele');
            const etat = row.getAttribute('data-etat');
            const categorie = row.getAttribute('data-categorie');
            const origine = row.getAttribute('data-origine');
            
            // Vérifier la correspondance avec la recherche
            const searchMatch = !searchTermValue || 
                numero.includes(searchTermValue) ||
                marque.includes(searchTermValue) ||
                modele.includes(searchTermValue) ||
                categorie.includes(searchTermValue);
            
            // Vérifier la correspondance avec l'état
            const etatMatch = !selectedEtat || etat === selectedEtat;
            
            // Vérifier la correspondance avec la catégorie
            const categorieMatch = !selectedCategorie || categorie.includes(normalizeText(selectedCategorie));
            
            // Vérifier la correspondance avec le filtre rapide
            let filterMatch = true;
            if (currentFilter === 'neuf') {
                filterMatch = etat === 'neuf';
            } else if (currentFilter === 'moyen') {
                filterMatch = etat === 'moyen';
            } else if (currentFilter === 'mauvais') {
                filterMatch = etat === 'mauvais';
            } else if (currentFilter === 'parc') {
                filterMatch = origine === 'parc';
            } else if (currentFilter === 'maintenance') {
                filterMatch = origine === 'maintenance';
            } else if (currentFilter === 'all') {
                filterMatch = true;
            }
            
            if (searchMatch && etatMatch && categorieMatch && filterMatch) {
                row.style.display = '';
                visibleCount++;
                
                // Mettre en surbrillance le texte recherché
                if (searchTermValue) {
                    const numeroElement = row.querySelector('.stock-numero');
                    const marqueElement = row.querySelector('.stock-marque');
                    const modeleElement = row.querySelector('.stock-modele');
                    const categorieElement = row.querySelector('.stock-categorie');
                    
                    if (numeroElement) highlightText(numeroElement, searchInput.value.trim());
                    if (marqueElement) highlightText(marqueElement, searchInput.value.trim());
                    if (modeleElement) highlightText(modeleElement, searchInput.value.trim());
                    if (categorieElement) highlightText(categorieElement, searchInput.value.trim());
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mettre à jour les informations de recherche
        updateSearchInfo(searchTermValue, selectedEtat, selectedCategorie, visibleCount);
        
        // Afficher le message "aucun résultat" si besoin
        if (visibleCount === 0) {
            if (noResultsRow) {
                noResultsRow.style.display = '';
            }
        }
    }
    
    // Mettre à jour les informations de recherche
    function updateSearchInfo(searchTermValue, selectedEtat, selectedCategorie, visibleCount) {
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
            if (selectedEtat) {
                if (infoText) infoText += ' • ';
                infoText += `État : ${getEtatName(selectedEtat)}`;
            }
            if (selectedCategorie) {
                if (infoText) infoText += ' • ';
                infoText += `Catégorie : ${selectedCategorie}`;
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
            case 'moyen': return 'Moyen';
            case 'mauvais': return 'Mauvais';
            case 'parc': return 'Origine Parc';
            case 'maintenance': return 'Origine Maintenance';
            default: return '';
        }
    }
    
    // Mettre à jour l'état des boutons de filtre
    function updateFilterButtons() {
        filterButtons.forEach(btn => {
            const filter = btn.dataset.filter;
            if (filter === currentFilter) {
                btn.classList.add('ring-2', 'ring-offset-2', 'ring-blue-500');
            } else {
                btn.classList.remove('ring-2', 'ring-offset-2', 'ring-blue-500');
            }
        });
    }
    
    // Événements
    searchInput.addEventListener('input', filterStocks);
    etatFilter.addEventListener('change', filterStocks);
    categorieFilter.addEventListener('change', filterStocks);
    
    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        etatFilter.value = '';
        categorieFilter.value = '';
        currentFilter = '';
        updateFilterButtons();
        filterStocks();
        searchInput.focus();
    });
    
    clearAllFilters.addEventListener('click', function() {
        searchInput.value = '';
        etatFilter.value = '';
        categorieFilter.value = '';
        currentFilter = '';
        updateFilterButtons();
        filterStocks();
        searchInput.focus();
    });
    
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            currentFilter = this.dataset.filter;
            
            if (currentFilter === 'neuf' || currentFilter === 'moyen' || currentFilter === 'mauvais') {
                etatFilter.value = currentFilter;
                categorieFilter.value = '';
            } else if (currentFilter === 'parc' || currentFilter === 'maintenance') {
                etatFilter.value = '';
                categorieFilter.value = '';
            } else if (currentFilter === 'all') {
                etatFilter.value = '';
                categorieFilter.value = '';
            }
            
            updateFilterButtons();
            filterStocks();
        });
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
    
    // Initialiser le filtrage
    filterStocks();
});
</script>

<style>
/* Styles personnalisés */
.search-highlight {
    background-color: #FFEB3B !important;
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

/* Responsive */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    table {
        font-size: 0.875rem;
    }
    
    .grid-cols-1 {
        grid-template-columns: 1fr;
    }
    
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
    
    /* Amélioration de la recherche sur mobile */
    input[type="text"], select {
        font-size: 16px; /* Empêche le zoom sur iOS */
    }
}

/* Animation douce pour les changements */
.transition-all {
    transition: all 0.3s ease;
}
</style>
@endsection