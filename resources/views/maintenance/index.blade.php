@extends('layouts.app')

@section('title', 'Gestion des Maintenances')
@section('header', 'Gestion des Maintenances')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Gestion des Maintenances</h1>
            <p class="text-gray-600 mt-2">Suivez l'état de toutes les interventions de maintenance</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            @if($stats['retard'] > 0)
            <a href="{{ route('maintenance.retard') }}" 
               class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Retards ({{ $stats['retard'] }})
            </a>
            @endif
            <a href="{{ route('maintenance.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvelle Maintenance
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
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">En cours</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['en_cours'] }}</p>
                    <p class="text-sm opacity-80 mt-1">maintenances</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    🔄
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Terminées</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['termine'] }}</p>
                    <p class="text-sm opacity-80 mt-1">maintenances</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    ✓
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Annulées</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['annule'] }}</p>
                    <p class="text-sm opacity-80 mt-1">maintenances</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    ✗
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">En retard</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['retard'] }}</p>
                    <p class="text-sm opacity-80 mt-1">maintenances</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    ⏰
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Coût total</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['cout_total'], 0, ',', ' ') }} €</p>
                    <p class="text-sm opacity-80 mt-1">cumulé</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    💰
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
                           placeholder="Rechercher par N° série, équipement, prestataire..."
                           value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="w-full md:w-48">
                <select id="statutFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="">Tous les statuts</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                    <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <select id="typeFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="">Tous les types</option>
                    <option value="preventive" {{ request('type_maintenance') == 'preventive' ? 'selected' : '' }}>Préventive</option>
                    <option value="corrective" {{ request('type_maintenance') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                    <option value="contractuelle" {{ request('type_maintenance') == 'contractuelle' ? 'selected' : '' }}>Contractuelle</option>
                    <option value="autre" {{ request('type_maintenance') == 'autre' ? 'selected' : '' }}>Autre</option>
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
                    Toutes
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition" data-filter="en_cours">
                    En cours
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition" data-filter="terminee">
                    Terminées
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200 transition" data-filter="retard">
                    En retard
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-purple-100 text-purple-800 hover:bg-purple-200 transition" data-filter="avec_cout">
                    Avec coût
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

    <!-- Tableau des maintenances -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Liste des Maintenances</h2>
                    <p class="text-sm text-gray-600 mt-1" id="totalCount">{{ $maintenances->total() }} maintenance{{ $maintenances->total() > 1 ? 's' : '' }} au total</p>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type & Prestataire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coût</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="maintenancesTableBody">
                    @forelse($maintenances as $maint)
                    <tr class="maintenance-row hover:bg-gray-50 transition-colors {{ $maint->date_retour_prevue < now() && $maint->statut == 'en_cours' ? 'bg-red-50 hover:bg-red-100' : '' }}"
                        data-id="{{ $maint->id }}"
                        data-numero="{{ strtolower($maint->numero_serie) }}"
                        data-equipement="{{ strtolower($maint->equipment ? $maint->equipment->nom : '') }}"
                        data-prestataire="{{ strtolower($maint->prestataire ?? '') }}"
                        data-statut="{{ $maint->statut }}"
                        data-type="{{ $maint->type_maintenance }}"
                        data-cout="{{ $maint->cout ?? 0 }}"
                        data-retard="{{ $maint->date_retour_prevue < now() && $maint->statut == 'en_cours' ? 'oui' : 'non' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 maintenance-numero">{{ $maint->numero_serie }}</div>
                                    @if($maint->equipment)
                                    <div class="text-sm text-gray-500 mt-1 maintenance-equipement">
                                        {{ $maint->equipment->nom }} - {{ $maint->equipment->type }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                @php
                                    $typeColors = [
                                        'preventive' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'corrective' => 'bg-orange-100 text-orange-800 border-orange-200',
                                        'contractuelle' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'autre' => 'bg-gray-100 text-gray-800 border-gray-200'
                                    ];
                                    $typeLabels = [
                                        'preventive' => 'Préventive',
                                        'corrective' => 'Corrective',
                                        'contractuelle' => 'Contractuelle',
                                        'autre' => 'Autre'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$maint->type_maintenance] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $typeLabels[$maint->type_maintenance] ?? $maint->type_maintenance }}
                                </span>
                                <div class="text-sm text-gray-900 font-medium maintenance-prestataire">{{ Str::limit($maint->prestataire, 25) }}</div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <div>
                                        <div class="text-sm text-gray-900">Départ: {{ $maint->date_depart->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">il y a {{ $maint->date_depart->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 {{ $maint->date_retour_prevue < now() && $maint->statut == 'en_cours' ? 'text-red-500' : 'text-gray-400' }} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <div class="text-sm {{ $maint->date_retour_prevue < now() && $maint->statut == 'en_cours' ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                            Retour prévu: {{ $maint->date_retour_prevue->format('d/m/Y') }}
                                        </div>
                                        @if($maint->date_retour_prevue < now() && $maint->statut == 'en_cours')
                                        <div class="text-xs text-red-500">En retard de {{ $maint->date_retour_prevue->diffInDays(now()) }} jour(s)</div>
                                        @endif
                                    </div>
                                </div>
                                @if($maint->date_retour_reelle)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <div class="text-sm text-green-600">
                                        Retour: {{ $maint->date_retour_reelle->format('d/m/Y') }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    @if($maint->cout)
                                    <div class="font-medium text-gray-900">{{ number_format($maint->cout, 2, ',', ' ') }} €</div>
                                    @else
                                    <div class="text-gray-400 italic">Non défini</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $badgeClasses = [
                                    'en_cours' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'terminee' => 'bg-green-100 text-green-800 border-green-200',
                                    'annulee' => 'bg-gray-100 text-gray-800 border-gray-200'
                                ];
                                $statutLabels = [
                                    'en_cours' => 'En cours',
                                    'terminee' => 'Terminée',
                                    'annulee' => 'Annulée'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $badgeClasses[$maint->statut] ?? 'bg-gray-100' }} border">
                                {{ $statutLabels[$maint->statut] ?? $maint->statut }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('maintenance.show', $maint->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition"
                                   title="Voir détails">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                
                                <a href="{{ route('maintenance.edit', $maint->id) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 p-2 rounded-lg transition"
                                   title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                
                                @if($maint->statut == 'en_cours')
                                <button type="button" onclick="openTerminerModal({{ $maint->id }})" 
                                        class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 p-2 rounded-lg transition"
                                        title="Terminer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                
                                <button type="button" onclick="openAnnulerModal({{ $maint->id }})" 
                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition"
                                        title="Annuler">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune maintenance enregistrée</h3>
                                <p class="text-gray-500 mb-6">Commencez par créer une nouvelle maintenance</p>
                                <a href="{{ route('maintenance.create') }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Nouvelle Maintenance
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
                        <span class="font-medium">{{ $maintenances->firstItem() }}</span>
                        à 
                        <span class="font-medium">{{ $maintenances->lastItem() }}</span>
                        sur 
                        <span class="font-medium">{{ $maintenances->total() }}</span>
                        maintenances
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    {{ $maintenances->links() }}
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
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Gestion des maintenances</h3>
                <p class="text-blue-700 mb-3">Cette section permet de suivre toutes les interventions de maintenance sur vos équipements. Vous pouvez planifier des maintenances préventives, suivre les interventions correctives et gérer les contrats de maintenance.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Types de maintenance</h4>
                        <p class="text-sm text-blue-700">Préventive, Corrective, Contractuelle, Autre</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Suivi des délais</h4>
                        <p class="text-sm text-blue-700">Visualisez les maintenances en retard pour un suivi optimal</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Gestion des coûts</h4>
                        <p class="text-sm text-blue-700">Suivez les coûts de maintenance pour chaque équipement</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour terminer -->
<div id="terminerModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Terminer la maintenance</h3>
        <form id="terminerForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_retour_reelle">
                    Date de retour réelle *
                </label>
                <input type="date" name="date_retour_reelle" id="date_retour_reelle" required
                    value="{{ date('Y-m-d') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="travaux_realises">
                    Travaux réalisés *
                </label>
                <textarea name="travaux_realises" id="travaux_realises" rows="3" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Décrivez les travaux réalisés..."></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="cout">
                    Coût (€) *
                </label>
                <input type="number" step="0.01" name="cout" id="cout" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="observations_fin">
                    Observations
                </label>
                <textarea name="observations_fin" id="observations_fin" rows="2"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeTerminerModal()"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Annuler
                </button>
                <button type="submit"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Confirmer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour annuler -->
<div id="annulerModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Annuler la maintenance</h3>
        <form id="annulerForm" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="raison_annulation">
                    Raison de l'annulation *
                </label>
                <textarea name="raison_annulation" id="raison_annulation" rows="3" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Pourquoi annulez-vous cette maintenance ?"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeAnnulerModal()"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Annuler
                </button>
                <button type="submit"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Confirmer l'annulation
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
    
    const maintenanceRows = document.querySelectorAll('.maintenance-row');
    const totalMaintenances = maintenanceRows.length;
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
    function filterMaintenances() {
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
        maintenanceRows.forEach(row => {
            const numero = row.getAttribute('data-numero');
            const equipement = row.getAttribute('data-equipement');
            const prestataire = row.getAttribute('data-prestataire');
            const statut = row.getAttribute('data-statut');
            const type = row.getAttribute('data-type');
            const cout = parseFloat(row.getAttribute('data-cout') || 0);
            const retard = row.getAttribute('data-retard');
            
            // Vérifier la correspondance avec la recherche
            const searchMatch = !searchTermValue || 
                numero.includes(searchTermValue) ||
                equipement.includes(searchTermValue) ||
                prestataire.includes(searchTermValue);
            
            // Vérifier la correspondance avec le statut
            const statutMatch = !selectedStatut || statut === selectedStatut;
            
            // Vérifier la correspondance avec le type
            const typeMatch = !selectedType || type === selectedType;
            
            // Vérifier la correspondance avec le filtre rapide
            let filterMatch = true;
            if (currentFilter === 'en_cours') {
                filterMatch = statut === 'en_cours';
            } else if (currentFilter === 'terminee') {
                filterMatch = statut === 'terminee';
            } else if (currentFilter === 'retard') {
                filterMatch = retard === 'oui';
            } else if (currentFilter === 'avec_cout') {
                filterMatch = cout > 0;
            } else if (currentFilter === 'all') {
                filterMatch = true;
            }
            
            if (searchMatch && statutMatch && typeMatch && filterMatch) {
                row.style.display = '';
                visibleCount++;
                
                // Mettre en surbrillance le texte recherché
                if (searchTermValue) {
                    const numeroElement = row.querySelector('.maintenance-numero');
                    const equipementElement = row.querySelector('.maintenance-equipement');
                    const prestataireElement = row.querySelector('.maintenance-prestataire');
                    
                    if (numeroElement) highlightText(numeroElement, searchInput.value.trim());
                    if (equipementElement) highlightText(equipementElement, searchInput.value.trim());
                    if (prestataireElement) highlightText(prestataireElement, searchInput.value.trim());
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
            filteredCount.textContent = visibleCount === totalMaintenances ? '' : `${visibleCount} sur ${totalMaintenances}`;
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
            case 'en_cours': return 'En cours';
            case 'terminee': return 'Terminée';
            case 'annulee': return 'Annulée';
            default: return statut;
        }
    }
    
    // Obtenir le nom du type
    function getTypeName(type) {
        switch(type) {
            case 'preventive': return 'Préventive';
            case 'corrective': return 'Corrective';
            case 'contractuelle': return 'Contractuelle';
            case 'autre': return 'Autre';
            default: return type;
        }
    }
    
    // Obtenir le nom du filtre
    function getFilterName(filter) {
        switch(filter) {
            case 'all': return 'Toutes';
            case 'en_cours': return 'En cours';
            case 'terminee': return 'Terminées';
            case 'retard': return 'En retard';
            case 'avec_cout': return 'Avec coût';
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
    searchInput.addEventListener('input', filterMaintenances);
    statutFilter.addEventListener('change', filterMaintenances);
    typeFilter.addEventListener('change', filterMaintenances);
    
    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        statutFilter.value = '';
        typeFilter.value = '';
        currentFilter = '';
        updateFilterButtons();
        filterMaintenances();
        searchInput.focus();
    });
    
    clearAllFilters.addEventListener('click', function() {
        searchInput.value = '';
        statutFilter.value = '';
        typeFilter.value = '';
        currentFilter = '';
        updateFilterButtons();
        filterMaintenances();
        searchInput.focus();
    });
    
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            currentFilter = this.dataset.filter;
            
            if (currentFilter === 'en_cours') {
                statutFilter.value = 'en_cours';
                typeFilter.value = '';
            } else if (currentFilter === 'terminee') {
                statutFilter.value = 'terminee';
                typeFilter.value = '';
            } else if (currentFilter === 'all') {
                statutFilter.value = '';
                typeFilter.value = '';
            } else if (currentFilter === 'retard') {
                statutFilter.value = 'en_cours';
                typeFilter.value = '';
            } else {
                statutFilter.value = '';
                typeFilter.value = '';
            }
            
            updateFilterButtons();
            filterMaintenances();
        });
    });
    
    // Debouncing pour les performances
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterMaintenances, 300);
    });
    
    // Recherche avec Entrée
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            filterMaintenances();
        }
    });
    
    // Initialiser avec les valeurs de l'URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search')) {
        searchInput.value = urlParams.get('search');
    }
    if (urlParams.has('statut')) {
        statutFilter.value = urlParams.get('statut');
    }
    if (urlParams.has('type_maintenance')) {
        typeFilter.value = urlParams.get('type_maintenance');
    }
    
    // Initialiser le filtrage
    filterMaintenances();
});

// Modal functions
function openTerminerModal(maintenanceId) {
    const form = document.getElementById('terminerForm');
    form.action = `/maintenance/${maintenanceId}/terminer`;
    document.getElementById('terminerModal').classList.remove('hidden');
}

function closeTerminerModal() {
    document.getElementById('terminerModal').classList.add('hidden');
}

function openAnnulerModal(maintenanceId) {
    const form = document.getElementById('annulerForm');
    form.action = `/maintenance/${maintenanceId}/annuler`;
    document.getElementById('annulerModal').classList.remove('hidden');
}

function closeAnnulerModal() {
    document.getElementById('annulerModal').classList.add('hidden');
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTerminerModal();
        closeAnnulerModal();
    }
});

// Close modal on outside click
document.getElementById('terminerModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeTerminerModal();
});

document.getElementById('annulerModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeAnnulerModal();
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
.maintenance-row {
    transition: all 0.3s ease !important;
}

.maintenance-row[style*="display: none"] {
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