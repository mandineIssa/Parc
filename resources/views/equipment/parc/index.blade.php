@extends('layouts.app')

@section('content')
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Header avec statistiques -->
    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex flex-wrap gap-2">
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
            
            <!-- BOUTONS D'ACTION EN HAUT À DROITE -->
            <div class="flex gap-2">
                <a href="{{ route('parc.import.form') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                    </svg>
                    Importer CSV
                </a>
                
                <a href="{{ route('parc.export') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Exporter CSV
                </a>
                
                <a href="{{ route('parc.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouvelle affectation
                </a>
            </div>
        </div>
    </div>

    <!-- Actions toolbar -->
    <div class="px-6 py-4 bg-gray-50 border-b">
        <!-- Search and filters -->
        <form action="{{ route('parc.index') }}" method="GET" class="grid grid-cols-4 gap-3 items-end">
            <!-- Champ recherche -->
            <div class="col-span-1">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Rechercher par N° série, nom, modèle...">
                </div>
            </div>

            <!-- Type -->
            <div>
                <select name="type" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                    <option value="">Tous les types</option>
                    <option value="Réseau" @selected(request('type') == 'Réseau')>Réseau</option>
                    <option value="Informatique" @selected(request('type') == 'Informatique')>Informatique</option>
                    <option value="Électronique" @selected(request('type') == 'Électronique')>Électronique</option>
                </select>
            </div>

            <!-- État -->
            <div>
                <select name="etat" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                    <option value="">Tous les états</option>
                    <option value="neuf" @selected(request('etat') == 'neuf')>Neuf</option>
                    <option value="bon" @selected(request('etat') == 'bon')>Bon</option>
                    <option value="moyen" @selected(request('etat') == 'moyen')>Moyen</option>
                    <option value="mauvais" @selected(request('etat') == 'mauvais')>Mauvais</option>
                </select>
            </div>

            <!-- Boutons filtrer / réinitialiser -->
            <div class="flex gap-2">
                <button type="submit" 
                        class="px-4 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filtrer
                </button>

                @if(request()->anyFilled(['type', 'etat', 'search']))
                <a href="{{ route('parc.index') }}" 
                   class="px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Réinitialiser
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-white">
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
            @php
                $countReseau = $equipments->where('type', 'Réseau')->count();
                $countInformatique = $equipments->where('type', 'Informatique')->count();
                $countElectronique = $equipments->where('type', 'Électronique')->count();
                $countEnService = $equipments->whereIn('etat', ['neuf', 'bon'])->count();
                $countARemplacer = $equipments->where('etat', 'mauvais')->count();
                
                // Calcul des valeurs par type
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
            <div class="bg-white p-4 rounded-xl border border-gray-200 hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-2xl font-bold text-gray-800">{{ $stat['count'] }}</div>
                        <div class="text-sm text-gray-600 mt-1">{{ $stat['label'] }}</div>
                        @if($stat['valeur'] > 0)
                        <div class="text-xs font-medium text-blue-600 mt-1">
                            {{ number_format($stat['valeur'], 2, ',', ' ') }} CFA
                        </div>
                        @endif
                    </div>
                    <div class="text-2xl">{{ $stat['icon'] }}</div>
                </div>
                <div class="mt-3">
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        @if($equipments->total() > 0)
                        <div class="h-full bg-{{ $stat['color'] }}-500 rounded-full" 
                             style="width: {{ in_array($stat['label'], ['Valeur totale']) ? 100 : ($stat['count'] / $equipments->total() * 100) }}%">
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Équipement
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Type & Valeur
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Utilisateur
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Localisation
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Dernière Mise à jour
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($equipments as $equipment)
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10 bg-green-50 rounded-lg flex items-center justify-center mr-3">
                                <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $equipment->nom }}</div>
                                <div class="text-sm text-gray-500 mt-1">
                                    <span class="inline-flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                        N°: {{ $equipment->numero_serie }}
                                    </span>
                                    @if($equipment->numero_codification)
                                    <span class="inline-flex items-center ml-3">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
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
                        @if($equipment->parc && $equipment->parc->utilisateur)
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-sm font-medium text-blue-600">
                                    {{ substr($equipment->parc->utilisateur->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $equipment->parc->utilisateur->name }}</div>
                                <div class="text-sm text-gray-500">{{ $equipment->parc->departement }}</div>
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
                                <div class="font-medium text-gray-900">{{ $equipment->agence->nom ?? 'Non assigné' }}</div>
                                <div class="text-sm text-gray-500">{{ $equipment->localisation }}</div>
                            </div>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $equipment->date_livraison->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">Livré il y a {{ $equipment->date_livraison->diffForHumans() }}</div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-1">
                            <!-- 1. Voir les détails -->
                            <a href="{{ route('equipment.show', $equipment) }}" 
                               class="inline-flex items-center p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition duration-150"
                               title="Voir les détails">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            
                            <!-- 2. Modifier -->
                            <a href="{{ route('equipment.edit', $equipment) }}" 
                               class="inline-flex items-center p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition duration-150"
                               title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            
                            <!-- 3. Modifier affectation -->
                            @if($equipment->parc)
                            <a href="{{ route('parc.edit', $equipment->parc) }}" 
                               class="inline-flex items-center p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition duration-150"
                               title="Modifier affectation">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </a>
                            @endif
                            
                            <!-- 4. Changer de statut -->
                            <a href="{{ route('equipment.transitions.', $equipment) }}" 
                               class="inline-flex items-center p-2 text-gray-500 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition duration-150"
                               title="Changer de statut">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </a>
                            
                            <!-- 5. Télécharger PDF (uniquement si disponible pour cet équipement) -->
                            @if($equipment->latestTransitionApproval)
                            <a href="{{ route('transitions.fiche-mouvement.download', $equipment->latestTransitionApproval->id) }}"
                               class="inline-flex items-center p-2 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition"
                               title="Télécharger  mouvement le PDF">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                            @endif

                            <!-- 5. Télécharger PDF (uniquement si disponible pour cet équipement) -->
                            @if($equipment->latestTransitionApproval)
                            <a href="{{ route('transitions.fiche-installation.download', $equipment->latestTransitionApproval->id) }}"
                               class="inline-flex items-center p-2 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition"
                               title="Télécharger installation le PDF">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement en parc</h3>
                            <p class="text-gray-500 mb-4">Commencez par affecter un équipement au parc</p>
                            <div class="flex gap-3">
                                <a href="{{ route('parc.import.form') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                    </svg>
                                    Importer CSV
                                </a>
                                <a href="{{ route('parc.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
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
    @if($equipments->hasPages())
    <div class="px-6 py-4 border-t bg-gray-50">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Affichage de <span class="font-medium">{{ $equipments->firstItem() }}</span> à 
                <span class="font-medium">{{ $equipments->lastItem() }}</span> sur 
                <span class="font-medium">{{ $equipments->total() }}</span> résultats
            </div>
            <div>
                {{ $equipments->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter Alpine.js si nécessaire
    if (typeof Alpine === 'undefined') {
        console.warn('Alpine.js n\'est pas chargé. Certaines fonctionnalités peuvent ne pas fonctionner.');
    }
});
</script>
@endpush