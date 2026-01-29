@extends('layouts.app')

@section('title', 'Équipements Hors Service')
@section('header', 'Équipements Hors Service')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Équipements Hors Service</h1>
            <p class="text-gray-600 mt-2">Gestion des équipements inutilisables ou obsolètes</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <!-- Commenté pour l'instant -->
            <!-- <a href="{{ route('hors-service.create') }}" 
               class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvelle Déclaration
            </a> -->
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
                    <p class="text-sm font-medium opacity-90">Total HS</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total'] }}</p>
                    <p class="text-sm opacity-80 mt-1">équipements</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    📋
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">En attente</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['en_attente'] }}</p>
                    <p class="text-sm opacity-80 mt-1">à traiter</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    ⏳
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Traités</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['traites'] }}</p>
                    <p class="text-sm opacity-80 mt-1">résolus</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    ✓
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Valeur totale</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['valeur_totale'], 0, ',', ' ') }} €</p>
                    <p class="text-sm opacity-80 mt-1">estimée</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    💰
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Moyenne/valeur</p>
                    <p class="text-3xl font-bold mt-2">
                        @if($stats['total'] > 0)
                        {{ number_format($stats['valeur_totale'] / $stats['total'], 0, ',', ' ') }} €
                        @else
                        0 €
                        @endif
                    </p>
                    <p class="text-sm opacity-80 mt-1">par équipement</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    📊
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
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition"
                           placeholder="Rechercher par N° série, équipement, destinataire..."
                           value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="w-full md:w-48">
                <select id="raisonFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-white">
                    <option value="">Toutes les raisons</option>
                    <option value="panne" {{ request('raison') == 'panne' ? 'selected' : '' }}>Panne</option>
                    <option value="obsolescence" {{ request('raison') == 'obsolescence' ? 'selected' : '' }}>Obsolescence</option>
                    <option value="accident" {{ request('raison') == 'accident' ? 'selected' : '' }}>Accident</option>
                    <option value="autre" {{ request('raison') == 'autre' ? 'selected' : '' }}>Autre</option>
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <select id="statutFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-white">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="traite" {{ request('statut') == 'traite' ? 'selected' : '' }}>Traité</option>
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
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-orange-100 text-orange-800 hover:bg-orange-200 transition" data-filter="all">
                    Tous
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition" data-filter="en_attente">
                    En attente
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition" data-filter="traite">
                    Traités
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200 transition" data-filter="panne">
                    Pannes
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 transition" data-filter="obsolescence">
                    Obsolètes
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-purple-100 text-purple-800 hover:bg-purple-200 transition" data-filter="valeur_estimee">
                    Avec valeur
                </button>
            </div>
        </div>
    </div>

    <!-- Informations de recherche -->
    <div id="searchResultsInfo" class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4 hidden">
        <div class="flex justify-between items-center">
            <div>
                <span id="resultsCount" class="text-orange-800 font-medium">0 résultats</span>
                <span id="searchTerm" class="text-orange-600 text-sm ml-4"></span>
            </div>
            <button id="clearAllFilters" class="text-orange-600 hover:text-orange-800 text-sm font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Tout effacer
            </button>
        </div>
    </div>

    <!-- Tableau des équipements hors service -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Liste des Équipements Hors Service</h2>
                    <p class="text-sm text-gray-600 mt-1" id="totalCount">{{ $horsServices->total() }} déclaration{{ $horsServices->total() > 1 ? 's' : '' }} au total</p>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Raison</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Destinataire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Valeur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="horsServicesTableBody">
                    @forelse($horsServices as $hs)
                    <tr class="hs-row hover:bg-gray-50 transition-colors"
                        data-id="{{ $hs->id }}"
                        data-numero="{{ strtolower($hs->numero_serie) }}"
                        data-equipement="{{ strtolower($hs->equipment ? $hs->equipment->nom : '') }}"
                        data-destinataire="{{ strtolower($hs->destinataire ?? '') }}"
                        data-raison="{{ $hs->raison }}"
                        data-statut="{{ $hs->date_traitement ? 'traite' : 'en_attente' }}"
                        data-valeur="{{ $hs->valeur_residuelle ?? 0 }}">
                        <td class="px-6 py-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 bg-orange-50 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 hs-numero">{{ $hs->numero_serie }}</div>
                                    @if($hs->equipment)
                                    <div class="text-sm text-gray-500 mt-1 hs-equipement">
                                        {{ $hs->equipment->nom }} - {{ $hs->equipment->type }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $hs->equipment->marque }} {{ $hs->equipment->modele }}
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
                                        <div class="text-sm text-gray-900">{{ $hs->date_hors_service->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">il y a {{ $hs->date_hors_service->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div>
                                    @php
                                        $raisonColors = [
                                            'panne' => 'bg-red-100 text-red-800 border-red-200',
                                            'obsolescence' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'accident' => 'bg-orange-100 text-orange-800 border-orange-200',
                                            'autre' => 'bg-gray-100 text-gray-800 border-gray-200'
                                        ];
                                        $raisonLabels = [
                                            'panne' => 'Panne',
                                            'obsolescence' => 'Obsolescence',
                                            'accident' => 'Accident',
                                            'autre' => 'Autre'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $raisonColors[$hs->raison] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $raisonLabels[$hs->raison] ?? $hs->raison }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 hidden lg:table-cell">
                            @if($hs->destinataire)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <div class="text-sm text-gray-900 hs-destinataire">{{ Str::limit($hs->destinataire, 20) }}</div>
                            </div>
                            @else
                            <span class="text-gray-400 italic text-sm">Non défini</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 hidden md:table-cell">
                            @if($hs->valeur_residuelle)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900">{{ number_format($hs->valeur_residuelle, 2, ',', ' ') }} €</div>
                                    @if($hs->equipment && $hs->equipment->prix_achat)
                                    <div class="text-xs text-gray-500">
                                        {{ round(($hs->valeur_residuelle / $hs->equipment->prix_achat) * 100) }}% valeur initiale
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @else
                            <div class="text-gray-400 italic text-sm">Non estimée</div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($hs->date_traitement)
                            <div class="space-y-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    Traité
                                </span>
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <div class="text-xs text-gray-500">{{ $hs->date_traitement->format('d/m/Y') }}</div>
                                </div>
                            </div>
                            @else
                            <div class="space-y-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    En attente
                                </span>
                                <div class="text-xs text-gray-500">
                                    {{ $hs->date_hors_service->diffInDays(now()) }} jour(s) d'attente
                                </div>
                            </div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('hors-service.show', $hs->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition"
                                   title="Voir détails">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                
                                <a href="{{ route('hors-service.edit', $hs->id) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 p-2 rounded-lg transition"
                                   title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                
                                @if(!$hs->date_traitement)
                                <button type="button" onclick="openTraiterModal({{ $hs->id }})" 
                                        class="text-purple-600 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 p-2 rounded-lg transition"
                                        title="Marquer comme traité">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
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
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement hors service</h3>
                                <p class="text-gray-500 mb-6">Tous vos équipements sont en état de fonctionnement</p>
                                <!-- Commenté pour l'instant -->
                                <!-- <a href="{{ route('hors-service.create') }}" 
                                   class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-6 rounded-lg inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Nouvelle Déclaration
                                </a> -->
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
                        <span class="font-medium">{{ $horsServices->firstItem() }}</span>
                        à 
                        <span class="font-medium">{{ $horsServices->lastItem() }}</span>
                        sur 
                        <span class="font-medium">{{ $horsServices->total() }}</span>
                        déclarations
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    {{ $horsServices->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Informations -->
    <div class="mt-8 p-6 bg-orange-50 rounded-xl border border-orange-200">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-orange-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-orange-800 mb-2">Gestion des équipements hors service</h3>
                <p class="text-orange-700 mb-3">Cette section permet de suivre les équipements inutilisables, obsolètes ou en panne. Vous pouvez déclarer de nouvelles mises hors service, suivre leur traitement et gérer leur valeur résiduelle.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="bg-white p-4 rounded-lg border border-orange-100">
                        <h4 class="font-medium text-orange-900 mb-2">Raisons principales</h4>
                        <p class="text-sm text-orange-700">Panne, Obsolescence, Accident, Autre</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-orange-100">
                        <h4 class="font-medium text-orange-900 mb-2">Valeur résiduelle</h4>
                        <p class="text-sm text-orange-700">Estimation de la valeur des équipements hors service</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-orange-100">
                        <h4 class="font-medium text-orange-900 mb-2">Suivi des traitements</h4>
                        <p class="text-sm text-orange-700">Gestion des destinataires et dates de traitement</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour marquer comme traité -->
<div id="traiterModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Marquer comme traité</h3>
        <form id="traiterForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_traitement">
                    Date de traitement *
                </label>
                <input type="date" name="date_traitement" id="date_traitement" required
                    value="{{ date('Y-m-d') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="destinataire">
                    Destinataire
                </label>
                <input type="text" name="destinataire" id="destinataire"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Ex: Recyclage, Don, Vente...">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="valeur_residuelle">
                    Valeur résiduelle (CFA)
                </label>
                <input type="number" step="0.01" name="valeur_residuelle" id="valeur_residuelle"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="observations_traitement">
                    Observations
                </label>
                <textarea name="observations_traitement" id="observations_traitement" rows="3"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Observations sur le traitement..."></textarea>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeTraiterModal()"
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
    const raisonFilter = document.getElementById('raisonFilter');
    const statutFilter = document.getElementById('statutFilter');
    const resetButton = document.getElementById('resetFilters');
    const clearAllFilters = document.getElementById('clearAllFilters');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const searchResultsInfo = document.getElementById('searchResultsInfo');
    const resultsCount = document.getElementById('resultsCount');
    const searchTerm = document.getElementById('searchTerm');
    const totalCount = document.getElementById('totalCount');
    const filteredCount = document.getElementById('filteredCount');
    const noResultsRow = document.querySelector('#noResultsRow');
    
    const hsRows = document.querySelectorAll('.hs-row');
    const totalHS = hsRows.length;
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
    function filterHorsServices() {
        const searchTermValue = normalizeText(searchInput.value.trim());
        const selectedRaison = raisonFilter.value;
        const selectedStatut = statutFilter.value;
        let visibleCount = 0;
        
        // Supprimer les anciennes surbrillances
        removeHighlights();
        
        // Masquer la ligne "aucun résultat" par défaut
        if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
        
        // Masquer les informations de recherche si pas de recherche
        if (!searchTermValue && !selectedRaison && !selectedStatut && !currentFilter) {
            searchResultsInfo.classList.add('hidden');
        } else {
            searchResultsInfo.classList.remove('hidden');
        }
        
        // Filtrer les lignes
        hsRows.forEach(row => {
            const numero = row.getAttribute('data-numero');
            const equipement = row.getAttribute('data-equipement');
            const destinataire = row.getAttribute('data-destinataire');
            const raison = row.getAttribute('data-raison');
            const statut = row.getAttribute('data-statut');
            const valeur = parseFloat(row.getAttribute('data-valeur'));
            
            // Vérifier la correspondance avec la recherche
            const searchMatch = !searchTermValue || 
                numero.includes(searchTermValue) ||
                equipement.includes(searchTermValue) ||
                destinataire.includes(searchTermValue);
            
            // Vérifier la correspondance avec la raison
            const raisonMatch = !selectedRaison || raison === selectedRaison;
            
            // Vérifier la correspondance avec le statut
            const statutMatch = !selectedStatut || statut === selectedStatut;
            
            // Vérifier la correspondance avec le filtre rapide
            let filterMatch = true;
            if (currentFilter === 'en_attente') {
                filterMatch = statut === 'en_attente';
            } else if (currentFilter === 'traite') {
                filterMatch = statut === 'traite';
            } else if (currentFilter === 'panne') {
                filterMatch = raison === 'panne';
            } else if (currentFilter === 'obsolescence') {
                filterMatch = raison === 'obsolescence';
            } else if (currentFilter === 'valeur_estimee') {
                filterMatch = valeur > 0;
            } else if (currentFilter === 'all') {
                filterMatch = true;
            }
            
            if (searchMatch && raisonMatch && statutMatch && filterMatch) {
                row.style.display = '';
                visibleCount++;
                
                // Mettre en surbrillance le texte recherché
                if (searchTermValue) {
                    const numeroElement = row.querySelector('.hs-numero');
                    const equipementElement = row.querySelector('.hs-equipement');
                    const destinataireElement = row.querySelector('.hs-destinataire');
                    
                    if (numeroElement) highlightText(numeroElement, searchInput.value.trim());
                    if (equipementElement) highlightText(equipementElement, searchInput.value.trim());
                    if (destinataireElement) highlightText(destinataireElement, searchInput.value.trim());
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mettre à jour les informations de recherche
        updateSearchInfo(searchTermValue, selectedRaison, selectedStatut, visibleCount);
        
        // Afficher le message "aucun résultat" si besoin
        if (visibleCount === 0) {
            if (noResultsRow) {
                noResultsRow.style.display = '';
            }
        }
    }
    
    // Mettre à jour les informations de recherche
    function updateSearchInfo(searchTermValue, selectedRaison, selectedStatut, visibleCount) {
        if (resultsCount) {
            resultsCount.textContent = `${visibleCount} résultat${visibleCount > 1 ? 's' : ''}`;
        }
        
        if (filteredCount) {
            filteredCount.textContent = visibleCount === totalHS ? '' : `${visibleCount} sur ${totalHS}`;
        }
        
        if (searchTerm) {
            let infoText = '';
            if (searchTermValue) {
                infoText += `Recherche : "${searchInput.value}"`;
            }
            if (selectedRaison) {
                if (infoText) infoText += ' • ';
                infoText += `Raison : ${getRaisonName(selectedRaison)}`;
            }
            if (selectedStatut) {
                if (infoText) infoText += ' • ';
                infoText += `Statut : ${getStatutName(selectedStatut)}`;
            }
            if (currentFilter && currentFilter !== 'all') {
                if (infoText) infoText += ' • ';
                infoText += `Filtre : ${getFilterName(currentFilter)}`;
            }
            searchTerm.textContent = infoText;
        }
    }
    
    // Obtenir le nom de la raison
    function getRaisonName(raison) {
        switch(raison) {
            case 'panne': return 'Panne';
            case 'obsolescence': return 'Obsolescence';
            case 'accident': return 'Accident';
            case 'autre': return 'Autre';
            default: return raison;
        }
    }
    
    // Obtenir le nom du statut
    function getStatutName(statut) {
        switch(statut) {
            case 'en_attente': return 'En attente';
            case 'traite': return 'Traité';
            default: return statut;
        }
    }
    
    // Obtenir le nom du filtre
    function getFilterName(filter) {
        switch(filter) {
            case 'all': return 'Tous';
            case 'en_attente': return 'En attente';
            case 'traite': return 'Traités';
            case 'panne': return 'Pannes';
            case 'obsolescence': return 'Obsolètes';
            case 'valeur_estimee': return 'Avec valeur estimée';
            default: return '';
        }
    }
    
    // Mettre à jour l'état des boutons de filtre
    function updateFilterButtons() {
        filterButtons.forEach(btn => {
            const filter = btn.dataset.filter;
            if (filter === currentFilter) {
                btn.classList.add('ring-2', 'ring-offset-2', 'ring-orange-500');
            } else {
                btn.classList.remove('ring-2', 'ring-offset-2', 'ring-orange-500');
            }
        });
    }
    
    // Événements
    searchInput.addEventListener('input', filterHorsServices);
    raisonFilter.addEventListener('change', filterHorsServices);
    statutFilter.addEventListener('change', filterHorsServices);
    
    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        raisonFilter.value = '';
        statutFilter.value = '';
        currentFilter = '';
        updateFilterButtons();
        filterHorsServices();
        searchInput.focus();
    });
    
    clearAllFilters.addEventListener('click', function() {
        searchInput.value = '';
        raisonFilter.value = '';
        statutFilter.value = '';
        currentFilter = '';
        updateFilterButtons();
        filterHorsServices();
        searchInput.focus();
    });
    
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            currentFilter = this.dataset.filter;
            
            if (currentFilter === 'en_attente') {
                statutFilter.value = 'en_attente';
                raisonFilter.value = '';
            } else if (currentFilter === 'traite') {
                statutFilter.value = 'traite';
                raisonFilter.value = '';
            } else if (currentFilter === 'panne') {
                raisonFilter.value = 'panne';
                statutFilter.value = '';
            } else if (currentFilter === 'obsolescence') {
                raisonFilter.value = 'obsolescence';
                statutFilter.value = '';
            } else if (currentFilter === 'all') {
                raisonFilter.value = '';
                statutFilter.value = '';
            } else {
                raisonFilter.value = '';
                statutFilter.value = '';
            }
            
            updateFilterButtons();
            filterHorsServices();
        });
    });
    
    // Debouncing pour les performances
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterHorsServices, 300);
    });
    
    // Recherche avec Entrée
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            filterHorsServices();
        }
    });
    
    // Initialiser avec les valeurs de l'URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search')) {
        searchInput.value = urlParams.get('search');
    }
    if (urlParams.has('raison')) {
        raisonFilter.value = urlParams.get('raison');
    }
    if (urlParams.has('statut')) {
        statutFilter.value = urlParams.get('statut');
    }
    
    // Initialiser le filtrage
    filterHorsServices();
});

// Modal functions
function openTraiterModal(hsId) {
    const form = document.getElementById('traiterForm');
    form.action = `/hors-service/${hsId}/traiter`;
    document.getElementById('traiterModal').classList.remove('hidden');
}

function closeTraiterModal() {
    document.getElementById('traiterModal').classList.add('hidden');
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTraiterModal();
    }
});

// Close modal on outside click
document.getElementById('traiterModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeTraiterModal();
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
.hs-row {
    transition: all 0.3s ease !important;
}

.hs-row[style*="display: none"] {
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