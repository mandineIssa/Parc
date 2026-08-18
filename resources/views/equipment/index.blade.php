@extends('layouts.app')

@section('title', 'Gestion des Équipements')
@section('header', 'Inventaire des Équipements')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-end items-start md:items-center mb-8">
        <div class="flex gap-3 mt-4 md:mt-0 flex-wrap">
            <a href="{{ route('equipment.renewal') }}"
               class="border border-gray-300 hover:border-gray-400 bg-white text-gray-800 font-semibold py-3 px-6 rounded-lg shadow-sm hover:shadow transition flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Plan renouvellement
            </a>
            <a href="{{ route('equipment.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvel Équipement
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
        @php
            // Récupère les statistiques depuis le contrôleur
            // Si $stats n'est pas défini (pour compatibilité avec l'ancien code), on utilise une méthode de secours
            if (!isset($stats)) {
                $stats = [
                    'stock' => App\Models\Equipment::where('statut', 'stock')->count(),
                    'parc' => App\Models\Equipment::where('statut', 'parc')->count(),
                    'maintenance' => App\Models\Equipment::where('statut', 'maintenance')->count(),
                    'hors_service' => App\Models\Equipment::where('statut', 'hors_service')->count(),
                    'perdu' => App\Models\Equipment::where('statut', 'perdu')->count(),
                ];
            }
            
            $statsConfig = [
                ['label' => 'En Stock', 'key' => 'stock', 'statut' => 'stock', 'icon' => '📦', 'gradient' => 'linear-gradient(135deg, #7A0C1A 0%, #A61B29 100%)'],
                ['label' => 'En Parc', 'key' => 'parc', 'statut' => 'parc', 'icon' => '🖥️', 'gradient' => 'linear-gradient(135deg, #8F2432 0%, #BF3142 100%)'],
                ['label' => 'Maintenance', 'key' => 'maintenance', 'statut' => 'maintenance', 'icon' => '🔧', 'gradient' => 'linear-gradient(135deg, #525866 0%, #6B7280 100%)'],
                ['label' => 'Hors Service', 'key' => 'hors_service', 'statut' => 'hors_service', 'icon' => '⛔', 'gradient' => 'linear-gradient(135deg, #9F1F2C 0%, #D03140 100%)'],
                ['label' => 'Perdus', 'key' => 'perdu', 'statut' => 'perdu', 'icon' => '❓', 'gradient' => 'linear-gradient(135deg, #3D3D44 0%, #5E616B 100%)'],
            ];
        @endphp
        
        @foreach($statsConfig as $stat)
        @php
            $isActive = request('statut') === $stat['statut'];
            $targetUrl = route('equipment.index', array_merge(request()->except(['page', 'statut']), ['statut' => $stat['statut']]));
        @endphp
        <a href="{{ $targetUrl }}"
           class="rounded-xl shadow-lg p-6 text-white block transition-all duration-200 hover:-translate-y-1 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#A61B29] {{ $isActive ? 'ring-2 ring-offset-2 ring-[#A61B29]' : '' }}"
           style="background: {{ $stat['gradient'] }};">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">{{ $stat['label'] }}</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats[$stat['key']] ?? 0 }}</p>
                    <p class="text-sm opacity-80 mt-1">équipements</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full text-xl">
                    {{ $stat['icon'] }}
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <!-- Filtres (serveur) -->
    <form method="GET" action="{{ route('equipment.index') }}" id="equipmentFiltersForm" class="bg-white rounded-xl shadow-md p-6 mb-8">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           name="search"
                           id="searchInput"
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#A61B29] focus:border-[#A61B29] outline-none transition"
                           placeholder="Rechercher par nom, N° série, marque, modèle..."
                           value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="w-full md:w-48">
                <select name="statut" id="statutFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#A61B29] focus:border-[#A61B29] outline-none transition bg-white">
                    <option value="">Tous les statuts</option>
                    <option value="stock" {{ request('statut') == 'stock' ? 'selected' : '' }}>Stock</option>
                    <option value="parc" {{ request('statut') == 'parc' ? 'selected' : '' }}>Parc</option>
                    <option value="maintenance" {{ request('statut') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="hors_service" {{ request('statut') == 'hors_service' ? 'selected' : '' }}>Hors Service</option>
                    <option value="perdu" {{ request('statut') == 'perdu' ? 'selected' : '' }}>Perdu</option>
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <select name="type" id="typeFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#A61B29] focus:border-[#A61B29] outline-none transition bg-white">
                    <option value="">Tous les types</option>
                    <option value="Réseau" {{ request('type') == 'Réseau' ? 'selected' : '' }}>Réseau</option>
                    <option value="Informatique" {{ request('type') == 'Informatique' ? 'selected' : '' }}>Informatique</option>
                    <option value="Électronique" {{ request('type') == 'Électronique' ? 'selected' : '' }}>Électronique</option>
                </select>
            </div>
            
            <button type="submit" class="bg-[#A61B29] hover:bg-[#7A0C1A] text-white font-semibold py-3 px-6 rounded-lg transition">
                Filtrer
            </button>
            <a href="{{ route('equipment.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center">
                Réinitialiser
            </a>
        </div>
        
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-500 flex items-center mr-3">Filtres rapides :</span>
                @php
                    $quickFilters = [
                        '' => 'Tous',
                        'stock' => 'Stock',
                        'parc' => 'Parc',
                        'maintenance' => 'Maintenance',
                        'hors_service' => 'Hors Service',
                        'perdu' => 'Perdus',
                    ];
                @endphp
                @foreach($quickFilters as $value => $label)
                    @php
                        $isQuickActive = ($value === '' && !request('statut')) || request('statut') === $value;
                        $quickUrl = $value === ''
                            ? route('equipment.index', request()->except(['page', 'statut']))
                            : route('equipment.index', array_merge(request()->except(['page', 'statut']), ['statut' => $value]));
                    @endphp
                    <a href="{{ $quickUrl }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-full transition {{ $isQuickActive ? 'bg-[#A61B29] text-white ring-2 ring-offset-2 ring-[#A61B29]' : 'bg-[#FDF2F3] text-[#7A0C1A] hover:bg-[#F8DADC]' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </form>

    @if(request()->filled('search') || request()->filled('statut') || request()->filled('type'))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
        <div class="flex justify-between items-center">
            <div>
                <span class="text-blue-800 font-medium">{{ $equipments->total() }} résultat{{ $equipments->total() > 1 ? 's' : '' }}</span>
                <span class="text-blue-600 text-sm ml-4">
                    @if(request('statut')) Statut : {{ str_replace('_', ' ', request('statut')) }} @endif
                    @if(request('type')) • Type : {{ request('type') }} @endif
                    @if(request('search')) • Recherche : « {{ request('search') }} » @endif
                </span>
            </div>
            <a href="{{ route('equipment.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Tout effacer</a>
        </div>
    </div>
    @endif

    <!-- Tableau des équipements -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Liste des Équipements</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $equipments->total() }} équipement{{ $equipments->total() > 1 ? 's' : '' }} au total</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Renouvellement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Localisation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">Date De livraison</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="equipmentsTableBody">
                    @forelse($equipments as $equipment)
                    <tr class="equipment-row hover:bg-gray-50 transition-colors"
                        data-id="{{ $equipment->id }}"
                        data-nom="{{ strtolower($equipment->nom) }}"
                        data-numero-serie="{{ strtolower($equipment->numero_serie) }}"
                        data-numero-codification="{{ strtolower($equipment->numero_codification ?? '') }}"
                        data-marque="{{ strtolower($equipment->marque ?? '') }}"
                        data-modele="{{ strtolower($equipment->modele ?? '') }}"
                        data-statut="{{ $equipment->statut }}"
                        data-type="{{ strtolower($equipment->type) }}"
                        data-localisation="{{ strtolower($equipment->localisation ?? '') }}"
                        data-agence="{{ strtolower($equipment->agence->nom ?? '') }}">
                        <td class="px-6 py-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 equipment-nom">{{ $equipment->nom }}</div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        <span class="inline-flex items-center equipment-numero-serie">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            N°: {{ $equipment->numero_serie }}
                                        </span>
                                        @if($equipment->numero_codification)
                                        <span class="inline-flex items-center ml-3 equipment-numero-codification">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Codif: {{ $equipment->numero_codification }}
                                        </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1 equipment-marque-modele">
                                        {{ $equipment->marque }} {{ $equipment->modele }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $equipment->type == 'Réseau' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 
                                   ($equipment->type == 'Informatique' ? 'bg-green-100 text-green-800 border border-green-200' : 
                                   'bg-purple-100 text-purple-800 border border-purple-200') }}">
                                {{ $equipment->type }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusConfig = [
                                    'stock' => ['color' => 'blue', 'icon' => '📦'],
                                    'parc' => ['color' => 'green', 'icon' => '✓'],
                                    'maintenance' => ['color' => 'yellow', 'icon' => '🔧'],
                                    'hors_service' => ['color' => 'red', 'icon' => '⛔'],
                                    'perdu' => ['color' => 'gray', 'icon' => '❓'],
                                ];
                                $config = $statusConfig[$equipment->statut] ?? ['color' => 'gray', 'icon' => '?'];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium 
                                         bg-{{ $config['color'] }}-50 text-{{ $config['color'] }}-700 border border-{{ $config['color'] }}-100">
                                <span class="mr-2">{{ $config['icon'] }}</span>
                                {{ ucfirst(str_replace('_', ' ', $equipment->statut)) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 hidden md:table-cell align-top">
                            @include('equipment.partials.renewal-badge', ['equipment' => $equipment])
                            @if($equipment->age_equipement_annees !== null)
                                <div class="text-xs text-gray-500 mt-1">{{ number_format($equipment->age_equipement_annees, 1, ',', ' ') }} ans</div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 hidden lg:table-cell">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <div class="min-w-0">
                                    <div class="font-medium text-gray-900 equipment-agence truncate">{{ $equipment->agence->nom ?? 'Non assigné' }}</div>
                                    <div class="text-sm text-gray-500 equipment-localisation truncate">{{ $equipment->localisation }}</div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap hidden xl:table-cell">
                            @if($equipment->date_livraison)
                                <div class="text-sm text-gray-900">{{ $equipment->date_livraison->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $equipment->date_livraison->diffForHumans() }}</div>
                            @else
                                <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('equipment.show', $equipment) }}" 
                                   class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition"
                                   title="Voir détails">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                
                                <a href="{{ route('equipment.edit', $equipment) }}" 
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
                                
                                <form action="{{ route('equipment.destroy', $equipment) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition"
                                            title="Supprimer"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet équipement ? Cette action est irréversible.')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement trouvé</h3>
                                <p class="text-gray-500 mb-6">
                                    @if(request()->filled('search') || request()->filled('statut') || request()->filled('type'))
                                        Aucun résultat pour ces filtres. Essayez de les modifier.
                                    @else
                                        Commencez par ajouter votre premier équipement
                                    @endif
                                </p>
                                <div class="flex gap-3">
                                    @if(request()->filled('search') || request()->filled('statut') || request()->filled('type'))
                                        <a href="{{ route('equipment.index') }}"
                                           class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-6 rounded-lg inline-flex items-center">
                                            Réinitialiser les filtres
                                        </a>
                                    @endif
                                    <a href="{{ route('equipment.create') }}" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg inline-flex items-center">
                                        Nouvel Équipement
                                    </a>
                                </div>
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
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Gestion des équipements IT</h3>
                <p class="text-blue-700 mb-3">Cette section permet de gérer l'ensemble de votre parc informatique. Vous pouvez ajouter de nouveaux équipements, suivre leur statut, leur localisation et effectuer des opérations de maintenance.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Types d'équipements</h4>
                        <p class="text-sm text-blue-700">Réseau, Informatique, Électronique</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Statuts disponibles</h4>
                        <p class="text-sm text-blue-700">Stock, Parc, Maintenance, Hors Service, Perdu</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Gestion avancée</h4>
                        <p class="text-sm text-blue-700">Import/Export CSV, Suivi des transitions, Historique</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('equipmentFiltersForm');
    const statutFilter = document.getElementById('statutFilter');
    const typeFilter = document.getElementById('typeFilter');

    if (statutFilter) {
        statutFilter.addEventListener('change', function () { form.submit(); });
    }
    if (typeFilter) {
        typeFilter.addEventListener('change', function () { form.submit(); });
    }
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
.equipment-row {
    transition: all 0.3s ease !important;
}

.equipment-row[style*="display: none"] {
    opacity: 0 !important;
    transform: translateX(-10px) !important;
    height: 0 !important;
    overflow: hidden !important;
}

/* Responsive */
@media (max-width: 768px) {
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