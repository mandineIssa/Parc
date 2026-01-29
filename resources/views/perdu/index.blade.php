@extends('layouts.app')

@section('title', 'Équipements Perdus/Sous Doublure')
@section('header', 'Équipements Perdus/Sous Doublure')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Équipements Perdus/Sous Doublure</h1>
            <p class="text-gray-600 mt-2">Suivi des équipements perdus, volés ou sous doublure</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <a href="{{ route('perdu.create') }}" 
               class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvelle Déclaration
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
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Total</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total'] }}</p>
                    <p class="text-sm opacity-80 mt-1">équipements</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    📋
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">En recherche</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['en_recherche'] }}</p>
                    <p class="text-sm opacity-80 mt-1">actifs</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    🔍
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Retrouvés</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['trouves'] }}</p>
                    <p class="text-sm opacity-80 mt-1">équipements</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    ✓
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Définitifs</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['definitif'] }}</p>
                    <p class="text-sm opacity-80 mt-1">irrécupérables</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    ✗
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Avec plainte</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['avec_plainte'] ?? 0 }}</p>
                    <p class="text-sm opacity-80 mt-1">déclarations</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    ⚖️
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
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition"
                           placeholder="Rechercher par N° série, équipement, lieu..."
                           value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="w-full md:w-48">
                <select id="statutFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition bg-white">
                    <option value="">Tous les statuts</option>
                    <option value="en_cours" {{ request('statut_recherche') == 'en_cours' ? 'selected' : '' }}>En recherche</option>
                    <option value="trouve" {{ request('statut_recherche') == 'trouve' ? 'selected' : '' }}>Retrouvé</option>
                    <option value="definitif" {{ request('statut_recherche') == 'definitif' ? 'selected' : '' }}>Définitif</option>
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <select id="typeFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition bg-white">
                    <option value="">Tous les types</option>
                    <option value="vol" {{ request('type_disparition') == 'vol' ? 'selected' : '' }}>Vol</option>
                    <option value="perte" {{ request('type_disparition') == 'perte' ? 'selected' : '' }}>Perte</option>
                    <option value="oubli" {{ request('type_disparition') == 'oubli' ? 'selected' : '' }}>Oubli</option>
                    <option value="destruction" {{ request('type_disparition') == 'destruction' ? 'selected' : '' }}>Destruction</option>
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
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200 transition" data-filter="all">
                    Tous
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 transition" data-filter="en_cours">
                    En recherche
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition" data-filter="trouve">
                    Retrouvés
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200 transition" data-filter="definitif">
                    Définitifs
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-purple-100 text-purple-800 hover:bg-purple-200 transition" data-filter="avec_plainte">
                    Avec plainte
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition" data-filter="doublure">
                    Avec doublure
                </button>
            </div>
        </div>
    </div>

    <!-- Informations de recherche -->
    <div id="searchResultsInfo" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4 hidden">
        <div class="flex justify-between items-center">
            <div>
                <span id="resultsCount" class="text-red-800 font-medium">0 résultats</span>
                <span id="searchTerm" class="text-red-600 text-sm ml-4"></span>
            </div>
            <button id="clearAllFilters" class="text-red-600 hover:text-red-800 text-sm font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Tout effacer
            </button>
        </div>
    </div>

    <!-- Tableau des équipements perdus -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Liste des Équipements Perdus</h2>
                    <p class="text-sm text-gray-600 mt-1" id="totalCount">{{ $perdus->total() }} déclaration{{ $perdus->total() > 1 ? 's' : '' }} au total</p>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disparition</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type & Détails</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plainte</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="perdusTableBody">
                    @forelse($perdus as $perdu)
                    <tr class="perdu-row hover:bg-gray-50 transition-colors"
                        data-id="{{ $perdu->id }}"
                        data-numero="{{ strtolower($perdu->numero_serie) }}"
                        data-equipement="{{ strtolower($perdu->equipment ? $perdu->equipment->nom : '') }}"
                        data-lieu="{{ strtolower($perdu->lieu_disparition ?? '') }}"
                        data-statut="{{ $perdu->statut_recherche }}"
                        data-type="{{ $perdu->type_disparition }}"
                        data-plainte="{{ $perdu->plainte_deposee ? 'oui' : 'non' }}"
                        data-doublure="{{ $perdu->doublure_utilisee ? 'oui' : 'non' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 bg-red-50 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 perdu-numero">{{ $perdu->numero_serie }}</div>
                                    @if($perdu->equipment)
                                    <div class="text-sm text-gray-500 mt-1 perdu-equipement">
                                        {{ $perdu->equipment->nom }} - {{ $perdu->equipment->type }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $perdu->equipment->marque }} {{ $perdu->equipment->modele }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <div class="text-sm text-gray-900">{{ $perdu->date_disparition->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">il y a {{ $perdu->date_disparition->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <div class="text-sm text-gray-600 perdu-lieu">{{ Str::limit($perdu->lieu_disparition, 25) }}</div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                @php
                                    $typeColors = [
                                        'vol' => 'bg-red-100 text-red-800 border-red-200',
                                        'perte' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'oubli' => 'bg-orange-100 text-orange-800 border-orange-200',
                                        'destruction' => 'bg-gray-100 text-gray-800 border-gray-200'
                                    ];
                                    $typeLabels = [
                                        'vol' => 'Vol',
                                        'perte' => 'Perte',
                                        'oubli' => 'Oubli',
                                        'destruction' => 'Destruction'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$perdu->type_disparition] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $typeLabels[$perdu->type_disparition] ?? $perdu->type_disparition }}
                                </span>
                                @if($perdu->doublure_utilisee)
                                <div class="text-xs text-blue-600 mt-1">
                                    <span class="inline-flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Doublure activée
                                    </span>
                                </div>
                                @endif
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                @if($perdu->plainte_deposee)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    Plainte déposée
                                </span>
                                @if($perdu->numero_plainte)
                                <div class="text-xs text-gray-600">
                                    N°: {{ $perdu->numero_plainte }}
                                </div>
                                @endif
                                @if($perdu->date_plainte)
                                <div class="text-xs text-gray-500">
                                    {{ $perdu->date_plainte->format('d/m/Y') }}
                                </div>
                                @endif
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                    Pas de plainte
                                </span>
                                @endif
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $badgeClasses = [
                                    'en_cours' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'trouve' => 'bg-green-100 text-green-800 border-green-200',
                                    'definitif' => 'bg-red-100 text-red-800 border-red-200'
                                ];
                                $statutLabels = [
                                    'en_cours' => 'En recherche',
                                    'trouve' => 'Retrouvé',
                                    'definitif' => 'Définitif'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $badgeClasses[$perdu->statut_recherche] ?? 'bg-gray-100' }} border">
                                {{ $statutLabels[$perdu->statut_recherche] ?? $perdu->statut_recherche }}
                            </span>
                            @if($perdu->date_retrouvaille)
                            <div class="text-xs text-green-600 mt-1">
                                Retrouvé le {{ $perdu->date_retrouvaille->format('d/m/Y') }}
                            </div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('perdu.show', $perdu->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition"
                                   title="Voir détails">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                
                                <a href="{{ route('perdu.edit', $perdu->id) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 p-2 rounded-lg transition"
                                   title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                
                                @if($perdu->statut_recherche == 'en_cours')
                                <button type="button" onclick="openRetrouverModal({{ $perdu->id }})" 
                                        class="text-purple-600 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 p-2 rounded-lg transition"
                                        title="Marquer comme retrouvé">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement perdu déclaré</h3>
                                <p class="text-gray-500 mb-6">Tous vos équipements sont en sécurité</p>
                                <a href="{{ route('perdu.create') }}" 
                                   class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Nouvelle Déclaration
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
                        <span class="font-medium">{{ $perdus->firstItem() }}</span>
                        à 
                        <span class="font-medium">{{ $perdus->lastItem() }}</span>
                        sur 
                        <span class="font-medium">{{ $perdus->total() }}</span>
                        déclarations
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    {{ $perdus->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Informations -->
    <div class="mt-8 p-6 bg-red-50 rounded-xl border border-red-200">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-red-800 mb-2">Gestion des équipements perdus</h3>
                <p class="text-red-700 mb-3">Cette section permet de suivre les équipements perdus, volés ou sous doublure. Vous pouvez déclarer de nouvelles pertes, suivre leur statut et marquer les équipements retrouvés.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="bg-white p-4 rounded-lg border border-red-100">
                        <h4 class="font-medium text-red-900 mb-2">Statuts disponibles</h4>
                        <p class="text-sm text-red-700">En recherche, Retrouvé, Définitif</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-red-100">
                        <h4 class="font-medium text-red-900 mb-2">Types de disparition</h4>
                        <p class="text-sm text-red-700">Vol, Perte, Oubli, Destruction</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-red-100">
                        <h4 class="font-medium text-red-900 mb-2">Gestion des plaintes</h4>
                        <p class="text-sm text-red-700">Suivi des plaintes déposées et numéros de référence</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour marquer comme retrouvé -->
<div id="retrouverModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Marquer comme retrouvé</h3>
        <form id="retrouverForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_retrouvaille">
                    Date de retrouvaille *
                </label>
                <input type="date" name="date_retrouvaille" id="date_retrouvaille" required
                    value="{{ date('Y-m-d') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="lieu_retrouvaille">
                    Lieu de retrouvaille *
                </label>
                <input type="text" name="lieu_retrouvaille" id="lieu_retrouvaille" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Où l'équipement a été retrouvé">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="etat_retrouvaille">
                    État de l'équipement *
                </label>
                <textarea name="etat_retrouvaille" id="etat_retrouvaille" rows="3" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Décrivez l'état dans lequel l'équipement a été retrouvé..."></textarea>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeRetrouverModal()"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Annuler
                </button>
                <button type="submit"
                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    Confirmer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Fonction de recherche dynamique
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les éléments
    const searchInput = document.getElementById('searchInput');
    const statutFilter = document.getElementById('statutFilter');
    const typeFilter = document.getElementById('typeFilter');
    const resetButton = document.getElementById('resetFilters');
    const clearAllFilters = document.getElementById('clearAllFilters');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const searchResultsInfo = document.getElementById('searchResultsInfo');
    const resultsCount = document.getElementById('resultsCount');
    const searchTerm = document.getElementById('searchTerm');
    const totalCount = document.getElementById('totalCount');
    const filteredCount = document.getElementById('filteredCount');
    const noResultsRow = document.querySelector('#noResultsRow');
    
    const perduRows = document.querySelectorAll('.perdu-row');
    const totalPerdus = perduRows.length;
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
    function filterPerdus() {
        const searchTermValue = normalizeText(searchInput.value.trim());
        const selectedStatut = statutFilter.value;
        const selectedType = typeFilter.value;
        let visibleCount = 0;
        
        // Supprimer les anciennes surbrillances
        removeHighlights();
        
        // Masquer la ligne "aucun résultat" par défaut
        if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
        
        // Masquer les informations de recherche si pas de recherche
        if (!searchTermValue && !selectedStatut && !selectedType && !currentFilter) {
            searchResultsInfo.classList.add('hidden');
        } else {
            searchResultsInfo.classList.remove('hidden');
        }
        
        // Filtrer les lignes
        perduRows.forEach(row => {
            const numero = row.getAttribute('data-numero');
            const equipement = row.getAttribute('data-equipement');
            const lieu = row.getAttribute('data-lieu');
            const statut = row.getAttribute('data-statut');
            const type = row.getAttribute('data-type');
            const plainte = row.getAttribute('data-plainte');
            const doublure = row.getAttribute('data-doublure');
            
            // Vérifier la correspondance avec la recherche
            const searchMatch = !searchTermValue || 
                numero.includes(searchTermValue) ||
                equipement.includes(searchTermValue) ||
                lieu.includes(searchTermValue);
            
            // Vérifier la correspondance avec le statut
            const statutMatch = !selectedStatut || statut === selectedStatut;
            
            // Vérifier la correspondance avec le type
            const typeMatch = !selectedType || type === selectedType;
            
            // Vérifier la correspondance avec le filtre rapide
            let filterMatch = true;
            if (currentFilter === 'en_cours') {
                filterMatch = statut === 'en_cours';
            } else if (currentFilter === 'trouve') {
                filterMatch = statut === 'trouve';
            } else if (currentFilter === 'definitif') {
                filterMatch = statut === 'definitif';
            } else if (currentFilter === 'avec_plainte') {
                filterMatch = plainte === 'oui';
            } else if (currentFilter === 'doublure') {
                filterMatch = doublure === 'oui';
            } else if (currentFilter === 'all') {
                filterMatch = true;
            }
            
            if (searchMatch && statutMatch && typeMatch && filterMatch) {
                row.style.display = '';
                visibleCount++;
                
                // Mettre en surbrillance le texte recherché
                if (searchTermValue) {
                    const numeroElement = row.querySelector('.perdu-numero');
                    const equipementElement = row.querySelector('.perdu-equipement');
                    const lieuElement = row.querySelector('.perdu-lieu');
                    
                    if (numeroElement) highlightText(numeroElement, searchInput.value.trim());
                    if (equipementElement) highlightText(equipementElement, searchInput.value.trim());
                    if (lieuElement) highlightText(lieuElement, searchInput.value.trim());
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mettre à jour les informations de recherche
        updateSearchInfo(searchTermValue, selectedStatut, selectedType, visibleCount);
        
        // Afficher le message "aucun résultat" si besoin
        if (visibleCount === 0) {
            if (noResultsRow) {
                noResultsRow.style.display = '';
            }
        }
    }
    
    // Mettre à jour les informations de recherche
    function updateSearchInfo(searchTermValue, selectedStatut, selectedType, visibleCount) {
        if (resultsCount) {
            resultsCount.textContent = `${visibleCount} résultat${visibleCount > 1 ? 's' : ''}`;
        }
        
        if (filteredCount) {
            filteredCount.textContent = visibleCount === totalPerdus ? '' : `${visibleCount} sur ${totalPerdus}`;
        }
        
        if (searchTerm) {
            let infoText = '';
            if (searchTermValue) {
                infoText += `Recherche : "${searchInput.value}"`;
            }
            if (selectedStatut) {
                if (infoText) infoText += ' • ';
                infoText += `Statut : ${getStatutName(selectedStatut)}`;
            }
            if (selectedType) {
                if (infoText) infoText += ' • ';
                infoText += `Type : ${getTypeName(selectedType)}`;
            }
            if (currentFilter && currentFilter !== 'all') {
                if (infoText) infoText += ' • ';
                infoText += `Filtre : ${getFilterName(currentFilter)}`;
            }
            searchTerm.textContent = infoText;
        }
    }
    
    // Obtenir le nom du statut
    function getStatutName(statut) {
        switch(statut) {
            case 'en_cours': return 'En recherche';
            case 'trouve': return 'Retrouvé';
            case 'definitif': return 'Définitif';
            default: return statut;
        }
    }
    
    // Obtenir le nom du type
    function getTypeName(type) {
        switch(type) {
            case 'vol': return 'Vol';
            case 'perte': return 'Perte';
            case 'oubli': return 'Oubli';
            case 'destruction': return 'Destruction';
            default: return type;
        }
    }
    
    // Obtenir le nom du filtre
    function getFilterName(filter) {
        switch(filter) {
            case 'all': return 'Tous';
            case 'en_cours': return 'En recherche';
            case 'trouve': return 'Retrouvés';
            case 'definitif': return 'Définitifs';
            case 'avec_plainte': return 'Avec plainte';
            case 'doublure': return 'Avec doublure';
            default: return '';
        }
    }
    
    // Mettre à jour l'état des boutons de filtre
    function updateFilterButtons() {
        filterButtons.forEach(btn => {
            const filter = btn.dataset.filter;
            if (filter === currentFilter) {
                btn.classList.add('ring-2', 'ring-offset-2', 'ring-red-500');
            } else {
                btn.classList.remove('ring-2', 'ring-offset-2', 'ring-red-500');
            }
        });
    }
    
    // Événements
    searchInput.addEventListener('input', filterPerdus);
    statutFilter.addEventListener('change', filterPerdus);
    typeFilter.addEventListener('change', filterPerdus);
    
    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        statutFilter.value = '';
        typeFilter.value = '';
        currentFilter = '';
        updateFilterButtons();
        filterPerdus();
        searchInput.focus();
    });
    
    clearAllFilters.addEventListener('click', function() {
        searchInput.value = '';
        statutFilter.value = '';
        typeFilter.value = '';
        currentFilter = '';
        updateFilterButtons();
        filterPerdus();
        searchInput.focus();
    });
    
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            currentFilter = this.dataset.filter;
            
            if (currentFilter === 'en_cours') {
                statutFilter.value = 'en_cours';
                typeFilter.value = '';
            } else if (currentFilter === 'trouve') {
                statutFilter.value = 'trouve';
                typeFilter.value = '';
            } else if (currentFilter === 'definitif') {
                statutFilter.value = 'definitif';
                typeFilter.value = '';
            } else if (currentFilter === 'all') {
                statutFilter.value = '';
                typeFilter.value = '';
            } else {
                statutFilter.value = '';
                typeFilter.value = '';
            }
            
            updateFilterButtons();
            filterPerdus();
        });
    });
    
    // Debouncing pour les performances
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterPerdus, 300);
    });
    
    // Recherche avec Entrée
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            filterPerdus();
        }
    });
    
    // Initialiser avec les valeurs de l'URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search')) {
        searchInput.value = urlParams.get('search');
    }
    if (urlParams.has('statut_recherche')) {
        statutFilter.value = urlParams.get('statut_recherche');
    }
    if (urlParams.has('type_disparition')) {
        typeFilter.value = urlParams.get('type_disparition');
    }
    
    // Initialiser le filtrage
    filterPerdus();
});

// Modal functions
function openRetrouverModal(perduId) {
    const form = document.getElementById('retrouverForm');
    form.action = `/perdu/${perduId}/retrouver`;
    document.getElementById('retrouverModal').classList.remove('hidden');
}

function closeRetrouverModal() {
    document.getElementById('retrouverModal').classList.add('hidden');
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRetrouverModal();
    }
});

// Close modal on outside click
document.getElementById('retrouverModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRetrouverModal();
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
.perdu-row {
    transition: all 0.3s ease !important;
}

.perdu-row[style*="display: none"] {
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
    
    .grid-cols-5 {
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