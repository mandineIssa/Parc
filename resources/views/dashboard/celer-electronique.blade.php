@extends('layouts.app')

@section('content')
<div class="container-fluid mx-auto px-4 py-8">
    <!-- Statistiques principales - Nombre total et Prix total -->
    <div class="grid grid-cols-1 gap-4 mb-6">
        <!-- Carte unique avec deux statistiques -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 5h6m-1 4v10m-4-10v10m-4 0h12a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <div class="flex items-center space-x-8">
                        <div>
                            <p class="text-sm text-gray-500">Nombre Total</p>
                            <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                        </div>
                        <div class="h-10 w-px bg-gray-300"></div>
                        <div>
                            <p class="text-sm text-gray-500">Prix Total</p>
                            <p class="text-2xl font-bold">{{ number_format($stats['valeur_totale'], 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- En-tête avec actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <!--<h1 class="text-3xl font-bold text-gray-800">Stock Celer - Électronique</h1>-->
            <!--<p class="text-gray-600 mt-1">Gestion des équipements électroniques en stock Celer</p>-->
        </div>
        <div class="mt-4 md:mt-0 space-x-2">
            <a href="{{ route('dashboard.celer-electronique.export') }}" 
               class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Exporter CSV
            </a>
            <a href="{{ route('equipment.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Ajouter
            </a>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-700">Recherche et Filtres</h2>
        </div>
        <form action="{{ route('dashboard.celer-electronique') }}" method="GET" class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Recherche par N° de série -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">N° de Série</label>
                    <input type="text" name="numero_serie" 
                           value="{{ request('numero_serie') }}"
                           placeholder="Ex: ABC12345"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Recherche par Marque -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Marque</label>
                    <input type="text" name="marque" 
                           value="{{ request('marque') }}"
                           placeholder="Ex: Samsung, Sony, LG"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Recherche par Modèle -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Modèle</label>
                    <input type="text" name="modele" 
                           value="{{ request('modele') }}"
                           placeholder="Ex: Galaxy, Bravia, OLED"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Filtre par État -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">État</label>
                    <select name="etat" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">Tous les états</option>
                        <option value="neuf" {{ request('etat') == 'neuf' ? 'selected' : '' }}>Neuf</option>
                        <option value="bon" {{ request('etat') == 'bon' ? 'selected' : '' }}>Bon</option>
                        <option value="moyen" {{ request('etat') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                        <option value="mauvais" {{ request('etat') == 'mauvais' ? 'selected' : '' }}>Mauvais</option>
                    </select>
                </div>
            </div>
            
            <!-- Deuxième ligne de filtres -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Recherche par Fournisseur -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Fournisseur</label>
                    <input type="text" name="fournisseur_nom" 
                           value="{{ request('fournisseur_nom') }}"
                           placeholder="Nom du fournisseur"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Recherche par Prix (min) -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Prix Min (FCFA)</label>
                    <input type="number" name="prix_min" 
                           value="{{ request('prix_min') }}"
                           placeholder="Ex: 50000"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <!-- Recherche par Prix (max) -->
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Prix Max (FCFA)</label>
                    <input type="number" name="prix_max" 
                           value="{{ request('prix_max') }}"
                           placeholder="Ex: 300000"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                 <!-- Troisième ligne avec recherche globale -->
            <div >
                <label class="block text-gray-700 text-sm font-medium mb-2">Recherche globale</label>
                <div class="relative">
                    <input type="text" name="search"
                           value="{{ request('search') }}"
                           placeholder="Rechercher par N° série, marque, modèle, localisation..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-10">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            </div>
            
           
            
            <!-- Boutons d'action -->
            <div class="flex items-end mt-6 gap-3">
                <button type="submit" 
                        class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Rechercher
                </button>
                
                @if(request()->anyFilled(['numero_serie', 'marque', 'modele', 'etat', 'fournisseur_nom', 'prix_min', 'prix_max', 'search']))
                <a href="{{ route('dashboard.celer-electronique') }}" 
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Réinitialiser
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tableau des équipements électroniques -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">N° de Série</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Marque</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Modèle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">État</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Date Livraison</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Fournisseur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Prix (FCFA)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($equipments as $equipment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900">{{ $equipment->numero_serie }}</span>
                                    @if($equipment->categorie)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Catégorie: {{ $equipment->categorie }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-900">{{ $equipment->marque ?? '-' }}</span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-900">{{ $equipment->modele ?? '-' }}</span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $etatColors = [
                                    'neuf' => 'bg-green-100 text-green-800',
                                    'bon' => 'bg-blue-100 text-blue-800',
                                    'moyen' => 'bg-yellow-100 text-yellow-800',
                                    'mauvais' => 'bg-red-100 text-red-800'
                                ];
                                $color = $etatColors[$equipment->etat] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $color }}">
                                {{ ucfirst($equipment->etat ?? '-') }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-900">
                                {{ $equipment->date_livraison ? $equipment->date_livraison->format('d/m/Y') : '-' }}
                            </span>
                            @if($equipment->date_livraison)
                            <div class="text-xs text-gray-500">
                                Livré il y a {{ $equipment->date_livraison->diffForHumans() }}
                            </div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-900">
                                {{ $equipment->fournisseur->nom ?? '-' }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-gray-900">
                                {{ number_format($equipment->prix ?? 0, 0, ',', ' ') }} FCFA
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('equipment.show', $equipment->id) }}" 
                                   class="inline-flex items-center p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition duration-150"
                                   title="Voir les détails">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                
                                <a href="{{ route('equipment.edit', $equipment->id) }}" 
                                   class="inline-flex items-center p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition duration-150"
                                   title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                
                                <a href="{{ route('equipment.transitions.', $equipment->id) }}" 
                                   class="inline-flex items-center p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition duration-150"
                                   title="Changer statut">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement électronique Celer trouvé</h3>
                                <p class="text-gray-500 mb-4">
                                    @if(request()->anyFilled(['numero_serie', 'marque', 'modele', 'etat', 'fournisseur_nom', 'prix_min', 'prix_max', 'search']))
                                        Aucun résultat ne correspond à vos critères de recherche
                                    @else
                                        Commencez par ajouter un équipement électronique
                                    @endif
                                </p>
                                <a href="{{ route('equipment.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Ajouter un équipement
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
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
</div>
@endsection

@push('styles')
<style>
    .bg-blue-50 { background-color: #eff6ff; }
    .bg-green-50 { background-color: #f0fdf4; }
    .bg-yellow-50 { background-color: #fefce8; }
    .bg-red-50 { background-color: #fef2f2; }
    .bg-purple-50 { background-color: #faf5ff; }
</style>
@endpush