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
            <a href="{{ route('equipment.imports.form') }}"
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center {{ request()->routeIs('equipment.imports.*') ? 'ring-2 ring-green-300' : '' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                <span>Import Équipements</span>
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
            {{ $equipments->total() }} Équipement{{ $equipments->total() > 1 ? 's' : '' }}
            @if(request()->hasAny(['search', 'type', 'etat', 'filtre_rapide']))
                (filtrés)
            @else
                en parc
            @endif
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
            use App\Models\Equipment;
            
            $countReseau = Equipment::where('statut', 'parc')->where('type', 'Réseau')->count();
            $countInformatique = Equipment::where('statut', 'parc')->where('type', 'Informatique')->count();
            $countElectronique = Equipment::where('statut', 'parc')->where('type', 'Électronique')->count();
            $countEnService = Equipment::where('statut', 'parc')->whereIn('etat', ['neuf', 'bon','moyen'])->count();
            $countARemplacer = Equipment::where('statut', 'parc')->where('etat', 'mauvais')->count();
            
            $valeurReseau = Equipment::where('statut', 'parc')->where('type', 'Réseau')->sum('prix');
            $valeurInformatique = Equipment::where('statut', 'parc')->where('type', 'Informatique')->sum('prix');
            $valeurElectronique = Equipment::where('statut', 'parc')->where('type', 'Électronique')->sum('prix');
            
            $totalParcCount = Equipment::where('statut', 'parc')->count();
            $totalParcValue = Equipment::where('statut', 'parc')->sum('prix');
            
            $stats = [
                ['label' => 'Réseau', 'count' => $countReseau, 'valeur' => $valeurReseau, 'color' => 'blue', 'icon' => '🌐'],
                ['label' => 'Informatique', 'count' => $countInformatique, 'valeur' => $valeurInformatique, 'color' => 'green', 'icon' => '💻'],
                ['label' => 'Électronique', 'count' => $countElectronique, 'valeur' => $valeurElectronique, 'color' => 'purple', 'icon' => '🔌'],
                ['label' => 'En Service', 'count' => $countEnService, 'valeur' => 0, 'color' => 'green', 'icon' => '✓'],
                ['label' => 'À Remplacer', 'count' => $countARemplacer, 'valeur' => 0, 'color' => 'red', 'icon' => '⚠️'],
                ['label' => 'Valeur totale', 'count' => $totalParcCount, 'valeur' => $totalParcValue, 'color' => 'yellow', 'icon' => '💰'],
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

    <!-- Formulaire de filtres -->
    <form method="GET" action="{{ route('parc.index') }}" id="searchForm" class="bg-white rounded-xl shadow-md p-6 mb-8">
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
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition"
                           placeholder="Rechercher par N° série, nom, modèle, utilisateur, agence..."
                           value="{{ request('search') }}"
                           autocomplete="off">
                </div>
            </div>
            
            <div class="w-full md:w-48">
                <select name="type" id="typeFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition bg-white">
                    <option value="">Tous les types</option>
                    <option value="Réseau" {{ request('type') == 'Réseau' ? 'selected' : '' }}>Réseau</option>
                    <option value="Informatique" {{ request('type') == 'Informatique' ? 'selected' : '' }}>Informatique</option>
                    <option value="Électronique" {{ request('type') == 'Électronique' ? 'selected' : '' }}>Électronique</option>
                </select>
            </div>
            
            <div class="w-full md:w-48">
                <select name="etat" id="etatFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition bg-white">
                    <option value="">Tous les états</option>
                    <option value="neuf" {{ request('etat') == 'neuf' ? 'selected' : '' }}>Neuf</option>
                    <option value="bon" {{ request('etat') == 'bon' ? 'selected' : '' }}>Bon</option>
                    <option value="moyen" {{ request('etat') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                    <option value="mauvais" {{ request('etat') == 'mauvais' ? 'selected' : '' }}>Mauvais</option>
                </select>
            </div>
            
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center whitespace-nowrap">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Rechercher
            </button>
            
            <a href="{{ route('parc.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center whitespace-nowrap">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Réinitialiser
            </a>
        </div>
        
        <!-- Filtres rapides -->
        <input type="hidden" name="filtre_rapide" id="filtreRapideInput" value="{{ request('filtre_rapide') }}">
        
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-500 flex items-center mr-3">Filtres rapides :</span>
                <button type="button" class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full transition {{ !request()->hasAny(['search', 'type', 'etat', 'filtre_rapide']) ? 'bg-green-600 text-white' : 'bg-green-100 text-green-800 hover:bg-green-200' }}" data-filter="">
                    Tous
                </button>
                <button type="button" class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full transition {{ request('filtre_rapide') == 'reseau' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800 hover:bg-blue-200' }}" data-filter="reseau">
                    Réseau
                </button>
                <button type="button" class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full transition {{ request('filtre_rapide') == 'informatique' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-800 hover:bg-green-200' }}" data-filter="informatique">
                    Informatique
                </button>
                <button type="button" class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full transition {{ request('filtre_rapide') == 'electronique' ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-800 hover:bg-purple-200' }}" data-filter="electronique">
                    Électronique
                </button>
                <button type="button" class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full transition {{ request('filtre_rapide') == 'a_remplacer' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-800 hover:bg-red-200' }}" data-filter="a_remplacer">
                    À remplacer
                </button>
                <button type="button" class="filter-btn px-3 py-1.5 text-sm font-medium rounded-full transition {{ request('filtre_rapide') == 'non_affecte' ? 'bg-yellow-600 text-white' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' }}" data-filter="non_affecte">
                    Non affecté
                </button>
            </div>
        </div>
    </form>

    <!-- Informations de recherche -->
    @if(request()->hasAny(['search', 'type', 'etat', 'filtre_rapide']))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
        <div class="flex justify-between items-center">
            <div>
                <span class="text-green-800 font-medium">{{ $equipments->total() }} résultat{{ $equipments->total() > 1 ? 's' : '' }}</span>
                <span class="text-green-600 text-sm ml-4">
                    @if(request('search'))
                        Recherche : "{{ request('search') }}"
                    @endif
                    @if(request('type'))
                        • Type : {{ request('type') }}
                    @endif
                    @if(request('etat'))
                        • État : {{ ucfirst(request('etat')) }}
                    @endif
                    @if(request('filtre_rapide'))
                        • Filtre : {{ ucfirst(str_replace('_', ' ', request('filtre_rapide'))) }}
                    @endif
                </span>
            </div>
            <a href="{{ route('parc.index') }}" class="text-green-600 hover:text-green-800 text-sm font-medium inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Effacer les filtres
            </a>
        </div>
    </div>
    @endif

    <!-- Tableau des équipements -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Liste des Équipements</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        @if($equipments->total() > 0)
                            Affichage de {{ $equipments->firstItem() }} à {{ $equipments->lastItem() }} sur {{ $equipments->total() }} équipement{{ $equipments->total() > 1 ? 's' : '' }}
                        @else
                            Aucun équipement trouvé
                        @endif
                    </p>
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
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($equipments as $equipment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-10 w-10 bg-green-50 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $equipment->nom }}</div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        <span class="inline-flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            N°: {{ $equipment->numero_serie }}
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
                                    <div class="text-xs text-gray-400 mt-1">{{ $equipment->marque }} {{ $equipment->modele }}</div>
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
                                        {{ $equipment->parc->utilisateur_nom ? substr($equipment->parc->utilisateur_nom, 0, 1) : 'N' }}
                                    </span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">
                                        {{ trim(($equipment->parc->utilisateur_nom ?? '') . ' ' . ($equipment->parc->utilisateur_prenom ?? '')) ?: 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $equipment->parc->departement ?? 'N/A' }}</div>
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
                                    <div class="font-medium text-gray-900">
                                        @if($equipment->agence)
                                            {{ $equipment->agence->nom }}
                                        @else
                                            <span class="text-orange-600 italic">À assigner</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $equipment->parc->localisation ?? $equipment->localisation ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $equipment->date_livraison->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $equipment->date_livraison->diffForHumans() }}</div>
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
                                
                                <a href="{{ route('transitions.fiche-installation.download', $equipment->latestTransitionApproval->id) }}"
                                   class="text-teal-600 hover:text-teal-900 bg-teal-50 hover:bg-teal-100 p-2 rounded-lg transition"
                                   title="Télécharger installation PDF">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </a>
                                @endif
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
                                <h3 class="text-lg font-medium text-gray-900 mb-2">
                                    @if(request()->hasAny(['search', 'type', 'etat', 'filtre_rapide']))
                                        Aucun résultat trouvé
                                    @else
                                        Aucun équipement en parc
                                    @endif
                                </h3>
                                <p class="text-gray-500 mb-6">
                                    @if(request()->hasAny(['search', 'type', 'etat', 'filtre_rapide']))
                                        Essayez de modifier vos critères de recherche
                                    @else
                                        Commencez par affecter un équipement au parc
                                    @endif
                                </p>
                                <div class="flex gap-3">
                                    @if(request()->hasAny(['search', 'type', 'etat', 'filtre_rapide']))
                                        <a href="{{ route('parc.index') }}" 
                                           class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Réinitialiser les filtres
                                        </a>
                                    @else
                                        <a href="{{ route('equipment.imports.form') }}" 
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
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($equipments->hasPages())
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
        @endif
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
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    const typeFilter = document.getElementById('typeFilter');
    const etatFilter = document.getElementById('etatFilter');
    const filtreRapideInput = document.getElementById('filtreRapideInput');
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    // Debounce pour la recherche automatique (optionnel - soumission après 800ms sans frappe)
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        // Indicateur visuel pendant la frappe
        searchInput.classList.add('bg-yellow-50');
        
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            searchForm.submit();
        }, 800);
    });
    
    // Soumission automatique lors du changement de filtre
    typeFilter.addEventListener('change', function() {
        searchForm.submit();
    });
    
    etatFilter.addEventListener('change', function() {
        searchForm.submit();
    });
    
    // Gestion des filtres rapides
    filterButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.getAttribute('data-filter');
            
            // Mettre à jour le champ caché
            filtreRapideInput.value = filter;
            
            // Si filtre vide, réinitialiser aussi les autres filtres
            if (filter === '') {
                typeFilter.value = '';
                etatFilter.value = '';
                searchInput.value = '';
            }
            
            // Soumettre le formulaire
            searchForm.submit();
        });
    });
    
    // Touche Entrée pour soumettre immédiatement
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(debounceTimer); // Annuler le debounce
            searchForm.submit();
        }
    });
    
    // Retirer l'indicateur lors de la soumission
    searchForm.addEventListener('submit', function() {
        searchInput.classList.remove('bg-yellow-50');
        searchInput.classList.add('bg-gray-100');
        searchInput.disabled = true;
    });
});
</script>

<style>
/* Animation pour les transitions */
.transition-colors {
    transition: background-color 0.3s ease;
}

/* Indicateur de recherche */
.bg-yellow-50 {
    transition: background-color 0.2s ease;
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
    
    /* Empêche le zoom sur iOS */
    input[type="text"], 
    select {
        font-size: 16px;
    }
}
</style>
@endsection