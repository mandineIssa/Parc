{{-- resources/views/dashboard/deceler-informatique.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Stock DECELER - Informatique</h1>
            <p class="text-gray-600 mt-2">Gestion des équipements informatiques retournés (DECELER)</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <a href="{{ route('dashboard.deceler-informatique.export') }}" 
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
                    <p class="text-sm font-medium opacity-90">Total équipements DECELER</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total'] }}</p>
                    <p class="text-sm opacity-80 mt-1">équipements informatiques</p>
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
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['valeur_totale'], 0, ',', ' ') }} €</p>
                    <p class="text-sm opacity-80 mt-1">estimation totale</p>
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
        <form action="{{ route('dashboard.deceler-informatique.filter') }}" method="GET" class="space-y-4">
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
                               name="search"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                               placeholder="Rechercher par N° série, marque, modèle..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="w-full md:w-48">
                    <select name="etat" id="etatFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                        <option value="">Tous les états</option>
                        <option value="neuf" {{ request('etat') == 'neuf' ? 'selected' : '' }}>Neuf</option>
                        <option value="bon" {{ request('etat') == 'bon' ? 'selected' : '' }}>Bon</option>
                        <option value="moyen" {{ request('etat') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                        <option value="mauvais" {{ request('etat') == 'mauvais' ? 'selected' : '' }}>Mauvais</option>
                    </select>
                </div>
                
                <div class="w-full md:w-48">
                    <select name="categorie" id="categoryFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                        <option value="">Toutes catégories</option>
                        @foreach($categoryStats as $category => $count)
                            @if($category)
                                <option value="{{ $category }}" {{ request('categorie') == $category ? 'selected' : '' }}>
                                    {{ $category }} ({{ $count }})
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
            
            <div class="flex flex-col md:flex-row gap-4 mt-4">
                <div class="w-full md:w-48">
                    <select name="origine" id="origineFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                        <option value="">Toutes origines</option>
                        <option value="parc" {{ request('origine') == 'parc' ? 'selected' : '' }}>Parc</option>
                        <option value="maintenance" {{ request('origine') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="hors_service" {{ request('origine') == 'hors_service' ? 'selected' : '' }}>Hors Service</option>
                    </select>
                </div>
                
                <div class="flex-1">
                    <input type="text" 
                           name="localisation" 
                           id="localisationInput"
                           value="{{ request('localisation') }}"
                           placeholder="Rechercher par localisation..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                
                <div class="w-full md:w-48">
                    <input type="date" 
                           name="date_from" 
                           id="dateFromInput"
                           value="{{ request('date_from') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                
                <div class="w-full md:w-48">
                    <input type="date" 
                           name="date_to" 
                           id="dateToInput"
                           value="{{ request('date_to') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Filtrer
                </button>
            </div>
        </form>
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

    <!-- Tableau des équipements DECELER -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Liste des Équipements DECELER</h2>
                    <p class="text-sm text-gray-600 mt-1" id="totalCount">{{ $stocks->total() }} équipement{{ $stocks->total() > 1 ? 's' : '' }} DECELER au total</p>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marque</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">État retour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom de l'Équipement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modèle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date retour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur résiduelle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="stocksTableBody">
                    @forelse($stocks as $stock)
                    <tr class="stock-row hover:bg-gray-50 transition-colors"
                        data-numero="{{ strtolower($stock->numero_serie) }}"
                        data-marque="{{ strtolower($stock->equipment ? $stock->equipment->marque : '') }}"
                        data-modele="{{ strtolower($stock->equipment ? $stock->equipment->modele : '') }}"
                        data-etat="{{ strtolower($stock->deceler ? $stock->deceler->etat_retour : '') }}"
                        data-categorie="{{ strtolower($stock->equipment ? $stock->equipment->type : '') }}"
                        data-origine="{{ strtolower($stock->deceler ? $stock->deceler->origine : '') }}"
                        data-localisation="{{ strtolower($stock->equipment ? $stock->equipment->localisation : '') }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900 stock-numero">{{ $stock->numero_serie }}</div>
                            <div class="text-sm text-gray-500">Quantité: {{ $stock->quantite }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->equipment && $stock->equipment->marque)
                                <div class="font-medium text-gray-900 stock-marque">{{ $stock->equipment->marque }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->deceler && $stock->deceler->etat_retour)
                                @php
                                    $etatColors = [
                                        'neuf' => 'bg-green-100 text-green-800 border-green-200',
                                        'bon' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'moyen' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'mauvais' => 'bg-red-100 text-red-800 border-red-200'
                                    ];
                                    $etat = $stock->deceler->etat_retour;
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $etatColors[$etat] ?? 'bg-gray-100 text-gray-800' }} border stock-etat">
                                    {{ ucfirst($etat) }}
                                </span>
                            @elseif($stock->equipment && $stock->equipment->etat)
                                <div class="font-medium text-gray-900">{{ $stock->equipment->etat }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->equipment)
                                <div class="font-medium text-gray-900 stock-nom">{{ $stock->equipment->nom ?? $stock->equipment->type }}</div>
                                <div class="text-xs text-gray-500">{{ $stock->equipment->marque ?? '' }}</div>
                            @else
                                <span class="text-red-500">Équipement non trouvé</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->equipment)
                                <div class="font-medium text-gray-900 stock-modele">{{ $stock->equipment->modele ?? '-' }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->deceler && $stock->deceler->date_retour)
                                <div class="font-medium text-gray-900">{{ $stock->deceler->date_retour->format('d/m/Y') }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->deceler && $stock->deceler->origine)
                                <div class="font-medium text-gray-900 stock-origine">{{ ucfirst($stock->deceler->origine) }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->deceler && $stock->deceler->valeur_residuelle)
                                <div class="font-bold text-gray-900">{{ number_format($stock->deceler->valeur_residuelle, 0, ',', ' ') }} €</div>
                            @elseif($stock->equipment && $stock->equipment->prix)
                                <div class="text-gray-500">{{ number_format($stock->equipment->prix * 0.3, 0, ',', ' ') }} € (30%)</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($stock->equipment)
                                <a href="{{ route('equipment.show', $stock->equipment->id) }}" 
                                   class="text-green-600 hover:text-green-900 font-medium">
                                    Détail équipement
                                </a>
                            @endif
                            @if($stock->deceler)
                                <br>
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
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement DECELER trouvé</h3>
                                <p class="text-gray-500 mb-6">Aucun équipement informatique retourné</p>
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

    <!-- Récentes entrées DECELER -->
    <div class="mt-8 p-6 bg-blue-50 rounded-xl border border-blue-200">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Derniers retours DECELER</h3>
                <p class="text-blue-700 mb-4">Les équipements les plus récemment retournés dans le stock DECELER</p>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @forelse($stats['recent_entries'] as $recent)
                    <div class="bg-white p-4 rounded-lg border border-blue-100 hover:shadow-md transition">
                        <div class="font-medium text-blue-900">{{ $recent->numero_serie }}</div>
                        @if($recent->equipment)
                            <div class="text-sm text-blue-700 mt-1">{{ $recent->equipment->marque }} {{ $recent->equipment->modele }}</div>
                        @endif
                        @if($recent->deceler && $recent->deceler->date_retour)
                            <div class="text-xs text-blue-600 mt-2">
                                Retour: {{ $recent->deceler->date_retour->format('d/m/Y') }}
                            </div>
                        @endif
                        <div class="mt-2">
                            <span class="px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded">
                                {{ $recent->deceler->origine ?? 'DECELER' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-5 text-center text-blue-500 py-4">
                        Aucun retour récent
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
    const categoryFilter = document.getElementById('categoryFilter');
    const origineFilter = document.getElementById('origineFilter');
    const localisationInput = document.getElementById('localisationInput');
    const dateFromInput = document.getElementById('dateFromInput');
    const dateToInput = document.getElementById('dateToInput');
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
    
    // Fonction de filtrage
    function filterStocks() {
        const searchTermValue = normalizeText(searchInput.value.trim());
        const selectedEtat = etatFilter.value;
        const selectedCategory = categoryFilter.value;
        const selectedOrigine = origineFilter.value;
        const localisationValue = normalizeText(localisationInput.value.trim());
        let visibleCount = 0;
        
        // Supprimer les anciennes surbrillances
        removeHighlights();
        
        // Masquer la ligne "aucun résultat" par défaut
        if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
        
        // Masquer les informations de recherche si pas de recherche
        if (!searchTermValue && !selectedEtat && !selectedCategory && !selectedOrigine && !localisationValue) {
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
            const localisation = row.getAttribute('data-localisation');
            
            // Vérifier la correspondance avec la recherche
            const searchMatch = !searchTermValue || 
                numero.includes(searchTermValue) ||
                marque.includes(searchTermValue) ||
                modele.includes(searchTermValue);
            
            // Vérifier la correspondance avec l'état
            const etatMatch = !selectedEtat || etat === selectedEtat;
            
            // Vérifier la correspondance avec la catégorie
            const categoryMatch = !selectedCategory || categorie === selectedCategory;
            
            // Vérifier la correspondance avec l'origine
            const origineMatch = !selectedOrigine || origine === selectedOrigine;
            
            // Vérifier la correspondance avec la localisation
            const localisationMatch = !localisationValue || localisation.includes(localisationValue);
            
            if (searchMatch && etatMatch && categoryMatch && origineMatch && localisationMatch) {
                row.style.display = '';
                visibleCount++;
                
                // Mettre en surbrillance le texte recherché
                if (searchTermValue) {
                    const numeroElement = row.querySelector('.stock-numero');
                    const marqueElement = row.querySelector('.stock-marque');
                    const modeleElement = row.querySelector('.stock-modele');
                    const nomElement = row.querySelector('.stock-nom');
                    
                    if (numeroElement) highlightText(numeroElement, searchInput.value.trim());
                    if (marqueElement) highlightText(marqueElement, searchInput.value.trim());
                    if (modeleElement) highlightText(modeleElement, searchInput.value.trim());
                    if (nomElement) highlightText(nomElement, searchInput.value.trim());
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mettre à jour les informations de recherche
        updateSearchInfo(searchTermValue, selectedEtat, selectedCategory, selectedOrigine, localisationValue, visibleCount);
        
        // Afficher le message "aucun résultat" si besoin
        if (visibleCount === 0) {
            if (noResultsRow) {
                noResultsRow.style.display = '';
            }
        }
    }
    
    // Mettre à jour les informations de recherche
    function updateSearchInfo(searchTermValue, selectedEtat, selectedCategory, selectedOrigine, localisationValue, visibleCount) {
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
            if (selectedCategory) {
                if (infoText) infoText += ' • ';
                infoText += `Catégorie : ${selectedCategory}`;
            }
            if (selectedOrigine) {
                if (infoText) infoText += ' • ';
                infoText += `Origine : ${getOrigineName(selectedOrigine)}`;
            }
            if (localisationValue) {
                if (infoText) infoText += ' • ';
                infoText += `Localisation : ${localisationInput.value}`;
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
    
    // Obtenir le nom de l'origine
    function getOrigineName(origine) {
        switch(origine) {
            case 'parc': return 'Parc';
            case 'maintenance': return 'Maintenance';
            case 'hors_service': return 'Hors Service';
            default: return origine;
        }
    }
    
    // Événements
    searchInput.addEventListener('input', filterStocks);
    etatFilter.addEventListener('change', filterStocks);
    categoryFilter.addEventListener('change', filterStocks);
    origineFilter.addEventListener('change', filterStocks);
    localisationInput.addEventListener('input', filterStocks);
    
    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        etatFilter.value = '';
        categoryFilter.value = '';
        origineFilter.value = '';
        localisationInput.value = '';
        dateFromInput.value = '';
        dateToInput.value = '';
        filterStocks();
        searchInput.focus();
    });
    
    clearAllFilters.addEventListener('click', function() {
        searchInput.value = '';
        etatFilter.value = '';
        categoryFilter.value = '';
        origineFilter.value = '';
        localisationInput.value = '';
        dateFromInput.value = '';
        dateToInput.value = '';
        filterStocks();
        searchInput.focus();
    });
    
    // Debouncing pour les performances
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterStocks, 300);
    });
    
    localisationInput.addEventListener('input', function() {
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
        categoryFilter.value = urlParams.get('categorie');
    }
    if (urlParams.has('origine')) {
        origineFilter.value = urlParams.get('origine');
    }
    if (urlParams.has('localisation')) {
        localisationInput.value = urlParams.get('localisation');
    }
    if (urlParams.has('date_from')) {
        dateFromInput.value = urlParams.get('date_from');
    }
    if (urlParams.has('date_to')) {
        dateToInput.value = urlParams.get('date_to');
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

/* Badge pour état */
.bg-green-100 { background-color: #d1fae5 !important; }
.bg-blue-100 { background-color: #dbeafe !important; }
.bg-yellow-100 { background-color: #fef3c7 !important; }
.bg-red-100 { background-color: #fee2e2 !important; }

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
    input[type="text"], 
    input[type="date"], 
    select {
        font-size: 16px; /* Empêche le zoom sur iOS */
    }
}

/* Animation douce pour les changements */
.transition-all {
    transition: all 0.3s ease;
}
</style>
@endsection