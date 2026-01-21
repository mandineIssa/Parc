@extends('layouts.app')

@section('title', 'Gestion des Équipements')
@section('header', 'Inventaire des Équipements')

@section('content')
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Header avec statistiques -->
    <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-gray-50 to-white border-b">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-4">
            <div class="flex-1 min-w-0">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 break-words">Inventaire des Équipements</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Gérez et suivez tous vos équipements IT</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm bg-blue-50 text-blue-700 border border-blue-100 whitespace-nowrap">
                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                    {{ $equipments->total() }} Équipements
                </span>
                <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm bg-green-50 text-green-700 border border-green-100 whitespace-nowrap">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    {{ $equipments->where('statut', 'parc')->count() }} Actifs
                </span>
            </div>
        </div>
    </div>

    <!-- Actions toolbar -->
<div class="px-4 sm:px-6 py-3 sm:py-4 bg-gray-50 border-b">
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-3 sm:gap-4">
        <!-- Search and filters -->
        <div class="flex-1 w-full">
            <form action="{{ route('equipment.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <div class="flex-1 min-w-0 max-w-xs">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-gray-400" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Rechercher...">
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2">
                    <select name="statut" 
                            class="px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white w-full sm:w-auto">
                        <option value="">Tous les statuts</option>
                        <option value="stock" @selected(request('statut') == 'stock')>Stock</option>
                        <option value="parc" @selected(request('statut') == 'parc')>Parc</option>
                        <option value="maintenance" @selected(request('statut') == 'maintenance')>Maintenance</option>
                        <option value="hors_service" @selected(request('statut') == 'hors_service')>Hors Service</option>
                        <option value="perdu" @selected(request('statut') == 'perdu')>Perdu</option>
                    </select>
                    
                    <select name="type" 
                            class="px-3 sm:px-4 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white w-full sm:w-auto">
                        <option value="">Tous les types</option>
                        <option value="Réseau" @selected(request('type') == 'Réseau')>Réseau</option>
                        <option value="Informatique" @selected(request('type') == 'Informatique')>Informatique</option>
                        <option value="Électronique" @selected(request('type') == 'Électronique')>Électronique</option>
                    </select>
                    
                    <button type="submit" 
                            class="px-3 sm:px-4 py-2 sm:py-2.5 bg-blue-600 text-white text-sm sm:text-base font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 whitespace-nowrap">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filtrer
                        </span>
                    </button>
                    
                    @if(request()->anyFilled(['statut', 'type', 'search']))
                    <a href="{{ route('equipment.index') }}" 
                       class="px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 text-gray-700 text-sm sm:text-base font-medium rounded-lg hover:bg-gray-50 transition duration-200 whitespace-nowrap">
                        <span class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Réinitialiser
                        </span>
                    </a>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Action buttons -->
        <div class="flex items-center gap-2 flex-shrink-0">
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open"
                        class="px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-200 flex items-center whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Exporter
                </button>
                
                <div x-show="open" x-transition 
                     class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-10 py-1">
                    <a href="{{ route('equipment.export', request()->all()) }}" 
                       class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div>
                                <div class="font-medium">Équipements</div>
                                <div class="text-xs text-gray-500">Format Excel</div>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('equipment.export.full', request()->all()) }}" 
                       class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div>
                                <div class="font-medium">Export complet</div>
                                <div class="text-xs text-gray-500">Multi-feuilles</div>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('audits.export') }}" 
                       class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <div>
                                <div class="font-medium">Journal d'audit</div>
                                <div class="text-xs text-gray-500">Format CSV</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            
            <a href="{{ route('equipment.import.form') }}" 
               class="px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-200 flex items-center justify-center whitespace-nowrap">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Importer
            </a>
            
            <a href="{{ route('equipment.create') }}" 
               class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 flex items-center justify-center shadow-sm whitespace-nowrap">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvel Équipement
            </a>
        </div>
    </div>
</div>

    <!-- Statistics -->
    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b bg-gradient-to-r from-gray-50 to-white">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2 sm:gap-3">
            @php
                $stats = [
                    ['label' => 'En Stock', 'count' => $equipments->where('statut', 'stock')->count(), 'color' => 'blue', 'icon' => '📦'],
                    ['label' => 'En Parc', 'count' => $equipments->where('statut', 'parc')->count(), 'color' => 'green', 'icon' => '🖥️'],
                    ['label' => 'Maintenance', 'count' => $equipments->where('statut', 'maintenance')->count(), 'color' => 'yellow', 'icon' => '🔧'],
                    ['label' => 'Hors Service', 'count' => $equipments->where('statut', 'hors_service')->count(), 'color' => 'red', 'icon' => '⛔'],
                    ['label' => 'Perdus', 'count' => $equipments->where('statut', 'perdu')->count(), 'color' => 'gray', 'icon' => '❓'],
                ];
            @endphp
            
            @foreach($stats as $stat)
            <div class="bg-white p-4 rounded-xl border border-gray-200 hover:shadow-md transition duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-2xl font-bold text-gray-800">{{ $stat['count'] }}</div>
                        <div class="text-sm text-gray-600 mt-1">{{ $stat['label'] }}</div>
                    </div>
                    <div class="text-2xl">{{ $stat['icon'] }}</div>
                </div>
                <div class="mt-3">
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $stat['color'] }}-500 rounded-full" 
                             style="width: {{ $equipments->total() > 0 ? ($stat['count'] / $equipments->total() * 100) : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto -mx-4 sm:mx-0">
        <div class="inline-block min-w-full align-middle">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Équipement
                        </th>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden md:table-cell">
                            Type
                        </th>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden lg:table-cell">
                            Localisation
                        </th>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider hidden xl:table-cell">
                            Dernière Mise à jour
                        </th>
                        <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($equipments as $equipment)
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="px-3 sm:px-6 py-3 sm:py-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                                <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
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
                    
                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap hidden md:table-cell">
                        <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-medium 
                            {{ $equipment->type == 'Réseau' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 
                               ($equipment->type == 'Informatique' ? 'bg-green-100 text-green-800 border border-green-200' : 
                               'bg-purple-100 text-purple-800 border border-purple-200') }}">
                            {{ $equipment->type }}
                        </span>
                    </td>
                    
                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
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
                    
                    <td class="px-3 sm:px-6 py-3 sm:py-4 hidden lg:table-cell">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900 truncate">{{ $equipment->agence->nom ?? 'Non assigné' }}</div>
                                <div class="text-xs sm:text-sm text-gray-500 truncate">{{ $equipment->localisation }}</div>
                            </div>
                        </div>
                    </td>
                    
                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap hidden xl:table-cell">
                        <div class="text-xs sm:text-sm text-gray-900">{{ $equipment->date_livraison->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $equipment->date_livraison->diffForHumans() }}</div>
                    </td>
                    
                    <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-1 sm:space-x-2">
                            <a href="{{ route('equipment.show', $equipment) }}" 
                               class="inline-flex items-center p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition duration-150"
                               title="Voir les détails">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            
                            <a href="{{ route('equipment.edit', $equipment) }}" 
                               class="inline-flex items-center p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition duration-150"
                               title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            
                            <a href="{{ route('equipment.transitions.', $equipment) }}" 
                               class="inline-flex items-center p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition duration-150"
                               title="Changer statut">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </a>
                            
                            <form action="{{ route('equipment.destroy', $equipment) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition duration-150"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet équipement ? Cette action est irréversible.')"
                                        title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
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
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement trouvé</h3>
                            <p class="text-gray-500 mb-4">Commencez par ajouter votre premier équipement</p>
                            <a href="{{ route('equipment.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Ajouter un équipement
                            </a>
                            {{-- Ajoutez ce bouton dans votre vue equipment/index.blade.php --}}
{{-- À placer à côté du bouton "Ajouter un Équipement" --}}

<div class="flex gap-3">
    {{-- Bouton existant --}}
    <a href="{{ route('equipment.create') }}" 
       class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Ajouter un Équipement
    </a>
    
    {{-- NOUVEAU: Bouton Import CSV --}}
    <a href="{{ route('equipment.import.form') }}" 
       class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
        </svg>
        Import CSV
    </a>
    
    {{-- BONUS: Bouton Export (optionnel) --}}
    <a href="{{ route('equipment.export') }}" 
       class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
        </svg>
        Export CSV
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
    <div class="px-4 sm:px-6 py-3 sm:py-4 border-t bg-gray-50">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs sm:text-sm text-gray-700 text-center sm:text-left">
                Affichage de <span class="font-medium">{{ $equipments->firstItem() }}</span> à 
                <span class="font-medium">{{ $equipments->lastItem() }}</span> sur 
                <span class="font-medium">{{ $equipments->total() }}</span> résultats
            </div>
            <div class="w-full sm:w-auto">
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