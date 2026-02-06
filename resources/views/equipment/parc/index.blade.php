@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Parc d'Équipements</h1>
            <p class="text-gray-600 mt-2">Gestion du parc informatique et des affectations</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <a href="{{ route('parc.import.form') }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                Importer CSV
            </a>
            <a href="{{ route('parc.export') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter CSV
            </a>
            <a href="{{ route('parc.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvelle affectation
            </a>
        </div>
    </div>

    <!-- Statistiques badges -->
    <div class="mb-6 flex flex-wrap gap-2">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-50 text-green-700 border border-green-100">
            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
            {{ $equipments->total() }} Équipements en parc
        </span>
        @if($prixTotal > 0)
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-50 text-blue-700 border border-blue-100">
            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
            {{ number_format($prixTotal, 2, ',', ' ') }} CFA
        </span>
        @endif
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-8">
        @php
            $countReseau = $equipments->where('type', 'Réseau')->count();
            $countInformatique = $equipments->where('type', 'Informatique')->count();
            $countElectronique = $equipments->where('type', 'Électronique')->count();
            $countEnService = $equipments->whereIn('etat', ['neuf', 'bon'])->count();
            $countARemplacer = $equipments->where('etat', 'mauvais')->count();
            
            $valeurReseau = $equipments->where('type', 'Réseau')->sum('prix');
            $valeurInformatique = $equipments->where('type', 'Informatique')->sum('prix');
            $valeurElectronique = $equipments->where('type', 'Électronique')->sum('prix');
            
            $stats = [
                ['label' => 'Réseau', 'count' => $countReseau, 'valeur' => $valeurReseau, 'color' => 'blue', 'icon' => '🌐'],
                ['label' => 'Informatique', 'count' => $countInformatique, 'valeur' => $valeurInformatique, 'color' => 'green', 'icon' => '💻'],
                ['label' => 'Électronique', 'count' => $countElectronique, 'valeur' => $valeurElectronique, 'color' => 'purple', 'icon' => '🔌'],
                ['label' => 'En Service', 'count' => $countEnService, 'valeur' => 0, 'color' => 'green', 'icon' => '✓'],
                ['label' => 'À Remplacer', 'count' => $countARemplacer, 'valeur' => 0, 'color' => 'red', 'icon' => '⚠️'],
                ['label' => 'Valeur totale', 'count' => $equipments->total(), 'valeur' => $prixTotal, 'color' => 'yellow', 'icon' => '💰'],
            ];
        @endphp
        
        @foreach($stats as $stat)
        <div class="bg-gradient-to-r from-{{ $stat['color'] }}-500 to-{{ $stat['color'] }}-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">{{ $stat['label'] }}</p>
                    <p class="text-3xl font-bold mt-2">{{ $stat['count'] }}</p>
                    @if($stat['valeur'] > 0)
                    <p class="text-sm opacity-80 mt-1">{{ number_format($stat['valeur'], 2, ',', ' ') }} CFA</p>
                    @else
                    <p class="text-sm opacity-80 mt-1">équipements</p>
                    @endif
                </div>
                <div class="bg-white/20 p-3 rounded-full text-xl">
                    {{ $stat['icon'] }}
                </div>
            </div>
        </div>
        @endforeach
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
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition"
                           placeholder="Rechercher par N° série, nom, modèle, utilisateur..."
                           value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="w-full md:w-48">
                <select id="typeFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition bg-white">
                    <option value="">Tous les types</option>
                    <option value="Réseau" {{ request('type') == 'Réseau' ? 'selected' : '' }}>Réseau</option>
                    <option value="Informatique" {{ request('type') == 'Informatique' ? 'selected' : '' }}>Informatique</option>
                    <option value="Électronique" {{ request('type') == 'Électronique' ? 'selected' : '' }}>Électronique</option>
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <select id="etatFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition bg-white">
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
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition" data-filter="all">
                    Tous
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 transition" data-filter="réseau">
                    Réseau
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition" data-filter="informatique">
                    Informatique
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-purple-100 text-purple-800 hover:bg-purple-200 transition" data-filter="électronique">
                    Électronique
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200 transition" data-filter="a_remplacer">
                    À remplacer
                </button>
                <button class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition" data-filter="non_affecte">
                    Non affecté
                </button>
            </div>
        </div>
    </div>

    <!-- Informations de recherche -->
    <div id="searchResultsInfo" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4 hidden">
        <div class="flex justify-between items-center">
            <div>
                <span id="resultsCount" class="text-green-800 font-medium">0 résultats</span>
                <span id="searchTerm" class="text-green-600 text-sm ml-4"></span>
            </div>
            <button id="clearAllFilters" class="text-green-600 hover:text-green-800 text-sm font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Tout effacer
            </button>
        </div>
    </div>

    <!-- Tableau des équipements -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Liste des Équipements</h2>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type & Valeur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Localisation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière Mise à jour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
<tbody class="bg-white divide-y divide-gray-200" id="equipmentsTableBody">
    @forelse($equipments as $equipment)
    <tr class="equipment-row hover:bg-gray-50 transition-colors"
        data-id="{{ $equipment->id }}"
        data-nom="{{ strtolower($equipment->nom) }}"
        data-numero="{{ strtolower($equipment->numero_serie) }}"
        data-modele="{{ strtolower($equipment->modele) }}"
        data-marque="{{ strtolower($equipment->marque) }}"
        data-type="{{ strtolower($equipment->type) }}"
        data-etat="{{ strtolower($equipment->etat) }}"
        data-utilisateur="{{ strtolower($equipment->parc && $equipment->parc->utilisateur ? $equipment->parc->utilisateur->name : '') }}"
        data-localisation="{{ strtolower($equipment->localisation) }}"
        data-agence="{{ strtolower($equipment->agence->nom ?? '') }}">
                        <td class="px-6 py-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 bg-green-50 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 equipment-nom">{{ $equipment->nom }}</div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        <span class="inline-flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            N°: <span class="equipment-numero">{{ $equipment->numero_serie }}</span>
                                        </span>
                                        @if($equipment->numero_codification)
                                        <span class="inline-flex items-center ml-3">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Codif: {{ $equipment->numero_codification }}
                                        </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1 equipment-modele">{{ $equipment->marque }} {{ $equipment->modele }}</div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="space-y-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    {{ $equipment->type == 'Réseau' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 
                                       ($equipment->type == 'Informatique' ? 'bg-green-100 text-green-800 border border-green-200' : 
                                       'bg-purple-100 text-purple-800 border border-purple-200') }}">
                                    {{ $equipment->type }}
                                </span>
                                
                                @if($equipment->prix)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($equipment->prix, 2, ',', ' ') }} CFA</div>
                                        @if($equipment->date_achat)
                                        <div class="text-xs text-gray-500">Achat: {{ $equipment->date_achat->format('m/Y') }}</div>
                                        @endif
                                    </div>
                                </div>
                                @else
                                <div class="text-gray-400 italic text-sm">Prix non renseigné</div>
                                @endif
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            @if($equipment->parc)
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-sm font-medium text-blue-600">
                                        {{ substr($equipment->parc->utilisateur_nom ?? 'N', 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 equipment-utilisateur">
                                        {{ $equipment->parc->utilisateur_nom ?? 'N/A' }} {{ $equipment->parc->utilisateur_prenom ?? '' }}
                                        
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $equipment->parc->department ?? 'N/A' }}</div>
                                </div>
                            </div>
                            @else
                            <span class="text-gray-400 italic">Non affecté</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-900 equipment-agence">
                                        {{ $equipment->agency->nom ?? 'Non assigné' }}
                                    </div>
                                    <div class="text-sm text-gray-500 equipment-localisation">
                                        {{ $equipment->parc->localisation ?? $equipment->localisation ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>


                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $equipment->date_livraison->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">Livré il y a {{ $equipment->date_livraison->diffForHumans() }}</div>
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
                                
                                @if($equipment->parc)
                                <a href="{{ route('parc.edit', $equipment->parc) }}" 
                                   class="text-purple-600 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 p-2 rounded-lg transition"
                                   title="Modifier affectation">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </a>
                                @endif
                                
                                <a href="{{ route('equipment.transitions.', $equipment) }}" 
                                   class="text-orange-600 hover:text-orange-900 bg-orange-50 hover:bg-orange-100 p-2 rounded-lg transition"
                                   title="Changer de statut">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </a>
                                
                                @if($equipment->latestTransitionApproval)
                                <a href="{{ route('transitions.fiche-mouvement.download', $equipment->latestTransitionApproval->id) }}"
                                   class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 p-2 rounded-lg transition"
                                   title="Télécharger mouvement PDF">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
                                @endif

                                @if($equipment->latestTransitionApproval)
                                <a href="{{ route('transitions.fiche-installation.download', $equipment->latestTransitionApproval->id) }}"
                                   class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 p-2 rounded-lg transition"
                                   title="Télécharger installation PDF">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </a>
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
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement en parc</h3>
                                <p class="text-gray-500 mb-6">Commencez par affecter un équipement au parc</p>
                                <div class="flex gap-3">
                                    <a href="{{ route('parc.import.form') }}" 
                                       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                                        </svg>
                                        Importer CSV
                                    </a>
                                    <a href="{{ route('parc.create') }}" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Nouvelle Affectation
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
    <div class="mt-8 p-6 bg-green-50 rounded-xl border border-green-200">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-green-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-green-800 mb-2">Gestion du parc d'équipements</h3>
                <p class="text-green-700 mb-3">Cette section permet de gérer l'ensemble de votre parc d'équipements. Vous pouvez suivre les affectations, les états, les valeurs et effectuer des recherches avancées.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="bg-white p-4 rounded-lg border border-green-100">
                        <h4 class="font-medium text-green-900 mb-2">Types d'équipements</h4>
                        <p class="text-sm text-green-700">Réseau, Informatique, Électronique avec leurs valeurs respectives</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-green-100">
                        <h4 class="font-medium text-green-900 mb-2">Suivi des affectations</h4>
                        <p class="text-sm text-green-700">Gestion des utilisateurs, départements et localisations</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-green-100">
                        <h4 class="font-medium text-green-900 mb-2">Export/Import</h4>
                        <p class="text-sm text-green-700">Importation et exportation en CSV pour une gestion facilitée</p>
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
    const typeFilter = document.getElementById('typeFilter');
    const etatFilter = document.getElementById('etatFilter');
    const resetButton = document.getElementById('resetFilters');
    const clearAllFilters = document.getElementById('clearAllFilters');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const searchResultsInfo = document.getElementById('searchResultsInfo');
    const resultsCount = document.getElementById('resultsCount');
    const searchTerm = document.getElementById('searchTerm');
    const totalCount = document.getElementById('totalCount');
    const filteredCount = document.getElementById('filteredCount');
    const noResultsRow = document.querySelector('#noResultsRow');
    
    const equipmentRows = document.querySelectorAll('.equipment-row');
    const totalEquipments = equipmentRows.length;
    let currentFilter = '';
    
    // Filtrer les lignes
equipmentRows.forEach(row => {
    const nom = row.getAttribute('data-nom');
    const numero = row.getAttribute('data-numero');
    const modele = row.getAttribute('data-modele');
    const marque = row.getAttribute('data-marque');
    const type = row.getAttribute('data-type');
    const etat = row.getAttribute('data-etat');
    const utilisateur = row.getAttribute('data-utilisateur'); // Maintenant contient "nom prénom"
    const localisation = row.getAttribute('data-localisation');
    const agence = row.getAttribute('data-agence');
    
    // Reste du code...
});
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
        const selectedType = typeFilter.value;
        const selectedEtat = etatFilter.value;
        let visibleCount = 0;
        
        // Supprimer les anciennes surbrillances
        removeHighlights();
        
        // Masquer la ligne "aucun résultat" par défaut
        if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
        
        // Masquer les informations de recherche si pas de recherche
        if (!searchTermValue && !selectedType && !selectedEtat && !currentFilter) {
            searchResultsInfo.classList.add('hidden');
        } else {
            searchResultsInfo.classList.remove('hidden');
        }
        
        // Filtrer les lignes
        equipmentRows.forEach(row => {
            const nom = row.getAttribute('data-nom');
            const numero = row.getAttribute('data-numero');
            const modele = row.getAttribute('data-modele');
            const marque = row.getAttribute('data-marque');
            const type = row.getAttribute('data-type');
            const etat = row.getAttribute('data-etat');
            const utilisateur = row.getAttribute('data-utilisateur');
            const localisation = row.getAttribute('data-localisation');
            const agence = row.getAttribute('data-agence');
            
            // Vérifier la correspondance avec la recherche
            const searchMatch = !searchTermValue || 
                nom.includes(searchTermValue) ||
                numero.includes(searchTermValue) ||
                modele.includes(searchTermValue) ||
                marque.includes(searchTermValue) ||
                utilisateur.includes(searchTermValue) ||
                localisation.includes(searchTermValue) ||
                agence.includes(searchTermValue);
            
            // Vérifier la correspondance avec le type
            const typeMatch = !selectedType || 
                (selectedType === 'Réseau' && type === 'réseau') ||
                (selectedType === 'Informatique' && type === 'informatique') ||
                (selectedType === 'Électronique' && type === 'électronique');
            
            // Vérifier la correspondance avec l'état
            const etatMatch = !selectedEtat || 
                (selectedEtat === 'neuf' && etat === 'neuf') ||
                (selectedEtat === 'bon' && etat === 'bon') ||
                (selectedEtat === 'moyen' && etat === 'moyen') ||
                (selectedEtat === 'mauvais' && etat === 'mauvais');
            
            // Vérifier la correspondance avec le filtre rapide
            let filterMatch = true;
            if (currentFilter === 'réseau') {
                filterMatch = type === 'réseau';
            } else if (currentFilter === 'informatique') {
                filterMatch = type === 'informatique';
            } else if (currentFilter === 'électronique') {
                filterMatch = type === 'électronique';
            } else if (currentFilter === 'a_remplacer') {
                filterMatch = etat === 'mauvais';
            } else if (currentFilter === 'non_affecte') {
                filterMatch = utilisateur === '';
            } else if (currentFilter === 'all') {
                filterMatch = true;
            }
            
            if (searchMatch && typeMatch && etatMatch && filterMatch) {
                row.style.display = '';
                visibleCount++;
                
                // Mettre en surbrillance le texte recherché
                if (searchTermValue) {
                    const nomElement = row.querySelector('.equipment-nom');
                    const numeroElement = row.querySelector('.equipment-numero');
                    const modeleElement = row.querySelector('.equipment-modele');
                    const utilisateurElement = row.querySelector('.equipment-utilisateur');
                    const localisationElement = row.querySelector('.equipment-localisation');
                    const agenceElement = row.querySelector('.equipment-agence');
                    
                    if (nomElement) highlightText(nomElement, searchInput.value.trim());
                    if (numeroElement) highlightText(numeroElement, searchInput.value.trim());
                    if (modeleElement) highlightText(modeleElement, searchInput.value.trim());
                    if (utilisateurElement) highlightText(utilisateurElement, searchInput.value.trim());
                    if (localisationElement) highlightText(localisationElement, searchInput.value.trim());
                    if (agenceElement) highlightText(agenceElement, searchInput.value.trim());
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mettre à jour les informations de recherche
        updateSearchInfo(searchTermValue, selectedType, selectedEtat, visibleCount);
        
        // Afficher le message "aucun résultat" si besoin
        if (visibleCount === 0) {
            if (noResultsRow) {
                noResultsRow.style.display = '';
            }
        }
    }
    
    // Mettre à jour les informations de recherche
    function updateSearchInfo(searchTermValue, selectedType, selectedEtat, visibleCount) {
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
            if (selectedType) {
                if (infoText) infoText += ' • ';
                infoText += `Type : ${selectedType}`;
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
            case 'réseau': return 'Réseau';
            case 'informatique': return 'Informatique';
            case 'électronique': return 'Électronique';
            case 'a_remplacer': return 'À remplacer';
            case 'non_affecte': return 'Non affecté';
            default: return '';
        }
    }
    
    // Mettre à jour l'état des boutons de filtre
    function updateFilterButtons() {
        filterButtons.forEach(btn => {
            const filter = btn.dataset.filter;
            if (filter === currentFilter) {
                btn.classList.add('ring-2', 'ring-offset-2', 'ring-green-500');
            } else {
                btn.classList.remove('ring-2', 'ring-offset-2', 'ring-green-500');
            }
        });
    }
    
    // Événements
    searchInput.addEventListener('input', filterEquipments);
    typeFilter.addEventListener('change', filterEquipments);
    etatFilter.addEventListener('change', filterEquipments);
    
    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        typeFilter.value = '';
        etatFilter.value = '';
        currentFilter = '';
        updateFilterButtons();
        filterEquipments();
        searchInput.focus();
    });
    
    clearAllFilters.addEventListener('click', function() {
        searchInput.value = '';
        typeFilter.value = '';
        etatFilter.value = '';
        currentFilter = '';
        updateFilterButtons();
        filterEquipments();
        searchInput.focus();
    });
    
    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            currentFilter = this.dataset.filter;
            
            if (currentFilter === 'réseau') {
                typeFilter.value = 'Réseau';
                etatFilter.value = '';
            } else if (currentFilter === 'informatique') {
                typeFilter.value = 'Informatique';
                etatFilter.value = '';
            } else if (currentFilter === 'électronique') {
                typeFilter.value = 'Électronique';
                etatFilter.value = '';
            } else if (currentFilter === 'a_remplacer') {
                typeFilter.value = '';
                etatFilter.value = 'mauvais';
            } else if (currentFilter === 'non_affecte') {
                typeFilter.value = '';
                etatFilter.value = '';
            } else if (currentFilter === 'all') {
                typeFilter.value = '';
                etatFilter.value = '';
            }
            
            updateFilterButtons();
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
    if (urlParams.has('type')) {
        typeFilter.value = urlParams.get('type');
    }
    if (urlParams.has('etat')) {
        etatFilter.value = urlParams.get('etat');
    }
    
    // Initialiser le filtrage
    filterEquipments();
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
    
    .grid-cols-6 {
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