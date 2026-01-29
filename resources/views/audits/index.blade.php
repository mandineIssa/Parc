@extends('layouts.app')

@section('title', 'Journal d\'Audit')
@section('header', 'Journal des Activités')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Journal d'Audit</h1>
            <p class="text-gray-600 mt-2">Suivi des activités et modifications du système</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <a href="{{ route('audits.export') }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter
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

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Total</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total'] ?? 0 }}</p>
                    <p class="text-sm opacity-80 mt-1">activités</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    📊
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Créations</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['by_action']['create'] ?? 0 }}</p>
                    <p class="text-sm opacity-80 mt-1">nouveaux</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    ➕
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Modifications</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['by_action']['update'] ?? 0 }}</p>
                    <p class="text-sm opacity-80 mt-1">changements</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    ✏️
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Transitions</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['by_action']['transition'] ?? 0 }}</p>
                    <p class="text-sm opacity-80 mt-1">changements d'état</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    🔄
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="w-full md:w-48">
                <select id="actionFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="">Toutes les actions</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Création</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Modification</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Suppression</option>
                    <option value="transition" {{ request('action') == 'transition' ? 'selected' : '' }}>Transition</option>
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <select id="modelFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="">Tous les modèles</option>
                    <option value="Equipment" {{ request('model_type') == 'Equipment' ? 'selected' : '' }}>Équipement</option>
                    <option value="Parc" {{ request('model_type') == 'Parc' ? 'selected' : '' }}>Parc</option>
                    <option value="Maintenance" {{ request('model_type') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="Stock" {{ request('model_type') == 'Stock' ? 'selected' : '' }}>Stock</option>
                    <option value="Category" {{ request('model_type') == 'Category' ? 'selected' : '' }}>Catégorie</option>
                    <option value="Agency" {{ request('model_type') == 'Agency' ? 'selected' : '' }}>Agence</option>
                    <option value="Supplier" {{ request('model_type') == 'Supplier' ? 'selected' : '' }}>Fournisseur</option>
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <select id="periodFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="7" {{ request('period') == '7' ? 'selected' : '' }}>7 derniers jours</option>
                    <option value="30" {{ request('period') == '30' || !request('period') ? 'selected' : '' }}>30 derniers jours</option>
                    <option value="90" {{ request('period') == '90' ? 'selected' : '' }}>3 derniers mois</option>
                    <option value="365" {{ request('period') == '365' ? 'selected' : '' }}>1 an</option>
                </select>
            </div>
            
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
                           placeholder="Rechercher par utilisateur, notes, ID..."
                           value="{{ request('search') }}">
                </div>
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
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200 transition" data-filter="all">
                    Toutes
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition" data-filter="create">
                    Créations
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 transition" data-filter="update">
                    Modifications
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200 transition" data-filter="delete">
                    Suppressions
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-purple-100 text-purple-800 hover:bg-purple-200 transition" data-filter="transition">
                    Transitions
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition" data-filter="today">
                    Aujourd'hui
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition" data-filter="week">
                    Cette semaine
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

    <!-- Tableau des audits -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Journal des Activités</h2>
                    <p class="text-sm text-gray-600 mt-1" id="totalCount">{{ $audits->total() }} activité{{ $audits->total() > 1 ? 's' : '' }} au total</p>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Heure</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modèle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Détails</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="auditsTableBody">
                    @forelse($audits as $audit)
                    <tr class="audit-row hover:bg-gray-50 transition-colors"
                        data-id="{{ $audit->id }}"
                        data-action="{{ $audit->action }}"
                        data-model="{{ class_basename($audit->model_type) }}"
                        data-user="{{ strtolower($audit->user->name ?? '') }}"
                        data-notes="{{ strtolower($audit->notes ?? '') }}"
                        data-date="{{ $audit->created_at->format('Y-m-d') }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $audit->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $audit->created_at->format('H:i:s') }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $actionColors = [
                                    'create' => 'bg-green-100 text-green-800',
                                    'update' => 'bg-blue-100 text-blue-800',
                                    'delete' => 'bg-red-100 text-red-800',
                                    'transition' => 'bg-purple-100 text-purple-800'
                                ];
                                $actionIcons = [
                                    'create' => '➕',
                                    'update' => '✏️',
                                    'delete' => '🗑️',
                                    'transition' => '🔄'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $actionColors[$audit->action] ?? 'bg-gray-100 text-gray-800' }}">
                                <span class="mr-1">{{ $actionIcons[$audit->action] ?? '📝' }}</span>
                                @if($audit->action == 'transition')
                                    {{ $audit->transition_type }}
                                @elseif($audit->action == 'create')
                                    Création
                                @elseif($audit->action == 'update')
                                    Modification
                                @elseif($audit->action == 'delete')
                                    Suppression
                                @else
                                    {{ $audit->action }}
                                @endif
                            </span>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 audit-model">
                                @switch(class_basename($audit->model_type))
                                    @case('Equipment')
                                        📦 Équipement
                                        @break
                                    @case('Parc')
                                        👨‍💼 Parc
                                        @break
                                    @case('Maintenance')
                                        🔧 Maintenance
                                        @break
                                    @case('Stock')
                                        📊 Stock
                                        @break
                                    @case('Category')
                                        📁 Catégorie
                                        @break
                                    @case('Agency')
                                        🏢 Agence
                                        @break
                                    @case('Supplier')
                                        🚚 Fournisseur
                                        @break
                                    @default
                                        {{ class_basename($audit->model_type) }}
                                @endswitch
                            </div>
                            <div class="text-xs text-gray-500 audit-model-id">ID: {{ $audit->model_id }}</div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 audit-user">{{ $audit->user->name ?? 'Système' }}</div>
                            <div class="text-xs text-gray-500">{{ $audit->user->email ?? '' }}</div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 audit-notes">{{ Str::limit($audit->notes, 100) }}</div>
                            @if($audit->action == 'update' && !empty($audit->formatted_changes))
                            <div class="mt-1">
                                <button type="button" 
                                        onclick="toggleChanges({{ $audit->id }})"
                                        class="text-xs text-blue-600 hover:text-blue-900">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Voir les changements
                                </button>
                                <div id="changes-{{ $audit->id }}" class="hidden mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs">
                                    @foreach($audit->formatted_changes as $change)
                                    <div class="mb-2 last:mb-0">
                                        <div class="font-semibold text-gray-700 mb-1">{{ $change['field'] }}</div>
                                        <div class="flex items-center text-xs">
                                            <span class="px-2 py-1 bg-red-50 text-red-700 rounded mr-2 flex-1">
                                                {{ $change['old'] }}
                                            </span>
                                            <svg class="w-4 h-4 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                            </svg>
                                            <span class="px-2 py-1 bg-green-50 text-green-700 rounded ml-2 flex-1">
                                                {{ $change['new'] }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                                {{ $audit->ip_address }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune activité enregistrée</h3>
                                <p class="text-gray-500 mb-6">Les activités apparaîtront ici au fur et à mesure</p>
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
                        <span class="font-medium">{{ $audits->firstItem() }}</span>
                        à 
                        <span class="font-medium">{{ $audits->lastItem() }}</span>
                        sur 
                        <span class="font-medium">{{ $audits->total() }}</span>
                        activités
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    {{ $audits->links() }}
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
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Journal d'audit système</h3>
                <p class="text-blue-700 mb-3">Cette section enregistre toutes les activités importantes du système. Chaque création, modification, suppression ou changement d'état est tracé avec date, utilisateur et détails.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Traçabilité complète</h4>
                        <p class="text-sm text-blue-700">Toutes les actions sont enregistrées avec utilisateur, heure et adresse IP</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Détails des changements</h4>
                        <p class="text-sm text-blue-700">Visualisez les modifications spécifiques apportées à chaque enregistrement</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Filtrage avancé</h4>
                        <p class="text-sm text-blue-700">Recherchez et filtrez par type d'action, modèle, période ou utilisateur</p>
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
    const actionFilter = document.getElementById('actionFilter');
    const modelFilter = document.getElementById('modelFilter');
    const periodFilter = document.getElementById('periodFilter');
    const resetButton = document.getElementById('resetFilters');
    const clearAllFilters = document.getElementById('clearAllFilters');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const searchResultsInfo = document.getElementById('searchResultsInfo');
    const resultsCount = document.getElementById('resultsCount');
    const searchTerm = document.getElementById('searchTerm');
    const totalCount = document.getElementById('totalCount');
    const filteredCount = document.getElementById('filteredCount');
    const noResultsRow = document.querySelector('#noResultsRow');
    
    const auditRows = document.querySelectorAll('.audit-row');
    const totalAudits = auditRows.length;
    let currentFilter = '';
    let currentPeriod = '';
    
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
    
    // Fonction pour vérifier si une date est aujourd'hui
    function isToday(dateString) {
        const today = new Date().toISOString().split('T')[0];
        return dateString === today;
    }
    
    // Fonction pour vérifier si une date est cette semaine
    function isThisWeek(dateString) {
        const date = new Date(dateString);
        const today = new Date();
        const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay()));
        const endOfWeek = new Date(today.setDate(today.getDate() - today.getDay() + 6));
        
        return date >= startOfWeek && date <= endOfWeek;
    }
    
    // Fonction de filtrage
    function filterAudits() {
        const searchTermValue = normalizeText(searchInput.value.trim());
        const selectedAction = actionFilter.value;
        const selectedModel = modelFilter.value;
        const selectedPeriod = periodFilter.value;
        let visibleCount = 0;
        
        // Supprimer les anciennes surbrillances
        removeHighlights();
        
        // Masquer la ligne "aucun résultat" par défaut
        if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
        
        // Masquer les informations de recherche si pas de recherche
        if (!searchTermValue && !selectedAction && !selectedModel && !currentFilter) {
            searchResultsInfo.classList.add('hidden');
        } else {
            searchResultsInfo.classList.remove('hidden');
        }
        
        // Filtrer les lignes
        auditRows.forEach(row => {
            const action = row.getAttribute('data-action');
            const model = row.getAttribute('data-model');
            const user = row.getAttribute('data-user');
            const notes = row.getAttribute('data-notes');
            const date = row.getAttribute('data-date');
            
            // Vérifier la correspondance avec la recherche
            const searchMatch = !searchTermValue || 
                user.includes(searchTermValue) ||
                notes.includes(searchTermValue) ||
                model.toLowerCase().includes(searchTermValue);
            
            // Vérifier la correspondance avec l'action
            const actionMatch = !selectedAction || action === selectedAction;
            
            // Vérifier la correspondance avec le modèle
            const modelMatch = !selectedModel || model === selectedModel;
            
            // Vérifier la correspondance avec le filtre rapide
            let filterMatch = true;
            if (currentFilter === 'create') {
                filterMatch = action === 'create';
            } else if (currentFilter === 'update') {
                filterMatch = action === 'update';
            } else if (currentFilter === 'delete') {
                filterMatch = action === 'delete';
            } else if (currentFilter === 'transition') {
                filterMatch = action === 'transition';
            } else if (currentFilter === 'today') {
                filterMatch = isToday(date);
            } else if (currentFilter === 'week') {
                filterMatch = isThisWeek(date);
            } else if (currentFilter === 'all') {
                filterMatch = true;
            }
            
            // Vérifier la correspondance avec la période
            let periodMatch = true;
            if (selectedPeriod) {
                const auditDate = new Date(date);
                const daysAgo = new Date();
                daysAgo.setDate(daysAgo.getDate() - parseInt(selectedPeriod));
                periodMatch = auditDate >= daysAgo;
            }
            
            if (searchMatch && actionMatch && modelMatch && filterMatch && periodMatch) {
                row.style.display = '';
                visibleCount++;
                
                // Mettre en surbrillance le texte recherché
                if (searchTermValue) {
                    const userElement = row.querySelector('.audit-user');
                    const notesElement = row.querySelector('.audit-notes');
                    const modelElement = row.querySelector('.audit-model');
                    const modelIdElement = row.querySelector('.audit-model-id');
                    
                    if (userElement) highlightText(userElement, searchInput.value.trim());
                    if (notesElement) highlightText(notesElement, searchInput.value.trim());
                    if (modelElement) highlightText(modelElement, searchInput.value.trim());
                    if (modelIdElement) highlightText(modelIdElement, searchInput.value.trim());
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mettre à jour les informations de recherche
        updateSearchInfo(searchTermValue, selectedAction, selectedModel, selectedPeriod, visibleCount);
        
        // Afficher le message "aucun résultat" si besoin
        if (visibleCount === 0) {
            if (noResultsRow) {
                noResultsRow.style.display = '';
            }
        }
    }
    
    // Mettre à jour les informations de recherche
    function updateSearchInfo(searchTermValue, selectedAction, selectedModel, selectedPeriod, visibleCount) {
        if (resultsCount) {
            resultsCount.textContent = `${visibleCount} activité${visibleCount > 1 ? 's' : ''}`;
        }
        
        if (filteredCount) {
            filteredCount.textContent = visibleCount === totalAudits ? '' : `${visibleCount} sur ${totalAudits}`;
        }
        
        if (searchTerm) {
            let infoText = '';
            if (searchTermValue) {
                infoText += `Recherche : "${searchInput.value}"`;
            }
            if (selectedAction) {
                if (infoText) infoText += ' • ';
                infoText += `Action : ${getActionName(selectedAction)}`;
            }
            if (selectedModel) {
                if (infoText) infoText += ' • ';
                infoText += `Modèle : ${getModelName(selectedModel)}`;
            }
            if (selectedPeriod && selectedPeriod !== '30') {
                if (infoText) infoText += ' • ';
                infoText += `Période : ${getPeriodName(selectedPeriod)}`;
            }
            if (currentFilter && currentFilter !== 'all') {
                if (infoText) infoText += ' • ';
                infoText += `Filtre : ${getFilterName(currentFilter)}`;
            }
            searchTerm.textContent = infoText;
        }
    }
    
    // Obtenir le nom de l'action
    function getActionName(action) {
        switch(action) {
            case 'create': return 'Création';
            case 'update': return 'Modification';
            case 'delete': return 'Suppression';
            case 'transition': return 'Transition';
            default: return action;
        }
    }
    
    // Obtenir le nom du modèle
    function getModelName(model) {
        switch(model) {
            case 'Equipment': return 'Équipement';
            case 'Parc': return 'Parc';
            case 'Maintenance': return 'Maintenance';
            case 'Stock': return 'Stock';
            case 'Category': return 'Catégorie';
            case 'Agency': return 'Agence';
            case 'Supplier': return 'Fournisseur';
            default: return model;
        }
    }
    
    // Obtenir le nom de la période
    function getPeriodName(period) {
        switch(period) {
            case '7': return '7 derniers jours';
            case '30': return '30 derniers jours';
            case '90': return '3 derniers mois';
            case '365': return '1 an';
            default: return `${period} jours`;
        }
    }
    
    // Obtenir le nom du filtre
    function getFilterName(filter) {
        switch(filter) {
            case 'all': return 'Toutes';
            case 'create': return 'Créations';
            case 'update': return 'Modifications';
            case 'delete': return 'Suppressions';
            case 'transition': return 'Transitions';
            case 'today': return 'Aujourd\'hui';
            case 'week': return 'Cette semaine';
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
    searchInput.addEventListener('input', filterAudits);
    actionFilter.addEventListener('change', filterAudits);
    modelFilter.addEventListener('change', filterAudits);
    periodFilter.addEventListener('change', filterAudits);
    
    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        actionFilter.value = '';
        modelFilter.value = '';
        periodFilter.value = '30';
        currentFilter = '';
        updateFilterButtons();
        filterAudits();
        searchInput.focus();
    });
    
    clearAllFilters.addEventListener('click', function() {
        searchInput.value = '';
        actionFilter.value = '';
        modelFilter.value = '';
        periodFilter.value = '30';
        currentFilter = '';
        updateFilterButtons();
        filterAudits();
        searchInput.focus();
    });
    
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            currentFilter = this.dataset.filter;
            
            if (currentFilter === 'create') {
                actionFilter.value = 'create';
            } else if (currentFilter === 'update') {
                actionFilter.value = 'update';
            } else if (currentFilter === 'delete') {
                actionFilter.value = 'delete';
            } else if (currentFilter === 'transition') {
                actionFilter.value = 'transition';
            } else if (currentFilter === 'all') {
                actionFilter.value = '';
            }
            
            updateFilterButtons();
            filterAudits();
        });
    });
    
    // Debouncing pour les performances
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterAudits, 300);
    });
    
    // Recherche avec Entrée
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            filterAudits();
        }
    });
    
    // Initialiser avec les valeurs de l'URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search')) {
        searchInput.value = urlParams.get('search');
    }
    if (urlParams.has('action')) {
        actionFilter.value = urlParams.get('action');
    }
    if (urlParams.has('model_type')) {
        modelFilter.value = urlParams.get('model_type');
    }
    if (urlParams.has('period')) {
        periodFilter.value = urlParams.get('period');
    }
    
    // Initialiser le filtrage
    filterAudits();
});

// Fonction pour afficher/masquer les changements
function toggleChanges(auditId) {
    const element = document.getElementById('changes-' + auditId);
    const button = element.previousElementSibling;
    
    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
        button.innerHTML = `
            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
            </svg>
            Masquer les changements
        `;
    } else {
        element.classList.add('hidden');
        button.innerHTML = `
            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Voir les changements
        `;
    }
}
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
.audit-row {
    transition: all 0.3s ease !important;
}

.audit-row[style*="display: none"] {
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
    
    .grid-cols-4 {
        grid-template-columns: repeat(2, 1fr);
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