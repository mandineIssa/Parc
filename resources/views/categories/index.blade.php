@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Gestion des Catégories</h1>
            <p class="text-gray-600 mt-2">Organisation des équipements par type et sous-catégories</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <a href="{{ route('categories.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nouvelle Catégorie
            </a>
            <a href="{{ route('categories.trash') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Corbeille
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

    <!-- Statistiques par type -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Réseaux</p>
                    <p class="text-3xl font-bold mt-2">{{ $typeStats['réseaux'] ?? 0 }}</p>
                    <p class="text-sm opacity-80 mt-1">catégories</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Électronique</p>
                    <p class="text-3xl font-bold mt-2">{{ $typeStats['électronique'] ?? 0 }}</p>
                    <p class="text-sm opacity-80 mt-1">catégories</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Informatiques</p>
                    <p class="text-3xl font-bold mt-2">{{ $typeStats['informatiques'] ?? 0 }}</p>
                    <p class="text-sm opacity-80 mt-1">catégories</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
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
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Rechercher une catégorie..."
                           value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="w-full md:w-48">
                <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="">Tous les types</option>
                    <option value="réseaux" {{ request('type') == 'réseaux' ? 'selected' : '' }}>Réseaux</option>
                    <option value="électronique" {{ request('type') == 'électronique' ? 'selected' : '' }}>Électronique</option>
                    <option value="informatiques" {{ request('type') == 'informatiques' ? 'selected' : '' }}>Informatiques</option>
                </select>
            </div>
            
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filtrer
            </button>
        </div>
    </div>

    <!-- Tableau des catégories -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Liste des Catégories</h2>
            <p class="text-sm text-gray-600 mt-1">{{ $categories->total() }} catégories au total</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégories & Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sous-catégories</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statistiques</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($category->type == 'réseaux')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                                    </svg>
                                    Réseaux
                                </span>
                            @elseif($category->type == 'électronique')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                    </svg>
                                    Électronique
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    Informatiques
                                </span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="flex items-start">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $category->nom }}</h3>
                                        @if($category->has_subcategories)
                                            <span class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                                Sous-catégories
                                            </span>
                                        @endif
                                    </div>
                                    @if($category->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ $category->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            @if($category->equipment_list && count($category->equipment_list) > 0)
                                <div class="space-y-1">
                                    @foreach(array_slice($category->equipment_list, 0, 3) as $equipment)
                                        <div class="flex items-center text-sm text-gray-700">
                                            <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="truncate">{{ $equipment }}</span>
                                        </div>
                                    @endforeach
                                    @if(count($category->equipment_list) > 3)
                                        <button class="text-sm text-blue-600 hover:text-blue-800 font-medium mt-2">
                                            + {{ count($category->equipment_list) - 3 }} autres
                                        </button>
                                    @endif
                                </div>
                            @else
                                <span class="text-sm text-gray-400">Non spécifié</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-center">
                                    <span class="block text-2xl font-bold text-gray-900">{{ $category->equipment_count ?? 0 }}</span>
                                    <span class="text-xs text-gray-500">Équipements</span>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('categories.show', $category) }}" 
                                   class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition"
                                   title="Voir détails">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('categories.edit', $category) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 p-2 rounded-lg transition"
                                   title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition"
                                            title="Supprimer"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Tous les équipements associés deviendront orphelins.')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Sous-catégories (si existantes) -->
                    @if($category->subcategories && $category->subcategories->count() > 0)
                        @foreach($category->subcategories as $subcategory)
                        <tr class="bg-gray-50 hover:bg-gray-100 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="ml-6">
                                        <span class="text-xs text-gray-500">Sous-catégorie de</span>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-start">
                                    <div class="ml-6 flex-1">
                                        <h4 class="text-md font-medium text-gray-800">{{ $subcategory->nom }}</h4>
                                        @if($subcategory->description)
                                            <p class="text-xs text-gray-600 mt-1">{{ $subcategory->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                @if($subcategory->equipment_list && count($subcategory->equipment_list) > 0)
                                    <div class="space-y-1">
                                        @foreach(array_slice($subcategory->equipment_list, 0, 2) as $equipment)
                                            <div class="flex items-center text-xs text-gray-600">
                                                <svg class="w-3 h-3 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                {{ $equipment }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-center">
                                    <span class="text-lg font-semibold text-gray-700">{{ $subcategory->equipment_count ?? 0 }}</span>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-1">
                                    <a href="{{ route('categories.edit', $subcategory) }}" 
                                       class="text-yellow-600 hover:text-yellow-800 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                    
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune catégorie trouvée</h3>
                                <p class="text-gray-500 mb-6">Commencez par créer votre première catégorie</p>
                                <a href="{{ route('categories.create') }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg inline-flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Créer une catégorie
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
                        <span class="font-medium">{{ $categories->firstItem() }}</span>
                        à 
                        <span class="font-medium">{{ $categories->lastItem() }}</span>
                        sur 
                        <span class="font-medium">{{ $categories->total() }}</span>
                        catégories
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    {{ $categories->links() }}
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
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Structure des catégories</h3>
                <p class="text-blue-700 mb-3">Les catégories sont organisées en 3 types principaux : Réseaux, Électronique et Informatiques. Chaque catégorie peut contenir des sous-catégories pour une organisation plus détaillée.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Réseaux</h4>
                        <p class="text-sm text-blue-700">Connectivité, sécurité et infrastructure réseau</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Électronique</h4>
                        <p class="text-sm text-blue-700">Sécurité électronique, vidéosurveillance et contrôle d'accès</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Informatiques</h4>
                        <p class="text-sm text-blue-700">Postes utilisateurs, serveurs et matériel de support</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour voir tous les équipements -->
<div id="equipmentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Équipements typiques</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="modalContent" class="space-y-2">
            <!-- Contenu dynamique -->
        </div>
    </div>
</div>

<script>
// Fonction de recherche dynamique
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les éléments
    const searchInput = document.querySelector('input[placeholder="Rechercher une catégorie..."]');
    const typeSelect = document.querySelector('select');
    const filterButton = document.querySelector('button.bg-blue-600');
    const categoryRows = document.querySelectorAll('tbody tr');
    const totalCategoriesElement = document.querySelector('.text-sm.text-gray-600.mt-1');
    const emptyStateRow = document.querySelector('tbody tr:last-child');
    
    // Stocker toutes les lignes originales (pour réinitialiser)
    const allOriginalRows = Array.from(categoryRows);
    
    // Fonction pour normaliser le texte (supprime les accents)
    function normalizeText(text) {
        return text.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    }
    
    // Fonction pour mettre en surbrillance le texte correspondant
    function highlightText(element, searchTerm) {
        if (!searchTerm || !element) return;
        
        const text = element.textContent;
        const normalizedText = normalizeText(text);
        const normalizedSearch = normalizeText(searchTerm);
        
        if (normalizedText.includes(normalizedSearch)) {
            const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            element.innerHTML = text.replace(regex, '<span class="search-highlight">$1</span>');
        }
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
    function filterCategories() {
        const searchTerm = searchInput.value.trim();
        const selectedType = typeSelect.value;
        
        let visibleCount = 0;
        let hasVisibleRows = false;
        
        // Supprimer les anciennes surbrillances
        removeHighlights();
        
        allOriginalRows.forEach(row => {
            // Vérifier si c'est une ligne vide (état "aucune catégorie")
            if (row.classList.contains('text-center') || row.querySelector('td[colspan]')) {
                return;
            }
            
            // Récupérer les données de la ligne
            const categoryNameElement = row.querySelector('h3, h4');
            const categoryDescriptionElement = row.querySelector('.text-sm.text-gray-600, .text-xs.text-gray-600');
            const categoryName = categoryNameElement?.textContent || '';
            const categoryDescription = categoryDescriptionElement?.textContent || '';
            const categoryType = row.querySelector('.inline-flex.items-center')?.textContent?.trim() || '';
            
            // Vérifier la correspondance avec le type
            const typeMatch = !selectedType || 
                normalizeText(categoryType).includes(normalizeText(selectedType));
            
            // Vérifier la correspondance avec la recherche
            const searchMatch = !searchTerm || 
                normalizeText(categoryName).includes(normalizeText(searchTerm)) ||
                normalizeText(categoryDescription).includes(normalizeText(searchTerm));
            
            // Afficher ou masquer la ligne
            if (typeMatch && searchMatch) {
                row.style.display = '';
                visibleCount++;
                hasVisibleRows = true;
                
                // Mettre en surbrillance le texte recherché
                if (searchTerm) {
                    if (categoryNameElement) highlightText(categoryNameElement, searchTerm);
                    if (categoryDescriptionElement) highlightText(categoryDescriptionElement, searchTerm);
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Gérer les sous-catégories
        document.querySelectorAll('tbody tr').forEach(row => {
            if (row.classList.contains('bg-gray-50')) {
                const parentRow = row.previousElementSibling;
                if (!parentRow || parentRow.style.display === 'none') {
                    row.style.display = 'none';
                } else if (hasVisibleRows) {
                    // Compter les sous-catégories visibles
                    const subNameElement = row.querySelector('h4');
                    const subDescElement = row.querySelector('.text-xs.text-gray-600');
                    const subName = subNameElement?.textContent || '';
                    const subDesc = subDescElement?.textContent || '';
                    
                    const subTypeMatch = !selectedType || true; // Les sous-catégories héritent du type parent
                    const subSearchMatch = !searchTerm || 
                        normalizeText(subName).includes(normalizeText(searchTerm)) ||
                        normalizeText(subDesc).includes(normalizeText(searchTerm));
                    
                    if (subTypeMatch && subSearchMatch) {
                        row.style.display = '';
                        visibleCount++;
                        
                        // Mettre en surbrillance pour les sous-catégories
                        if (searchTerm) {
                            if (subNameElement) highlightText(subNameElement, searchTerm);
                            if (subDescElement) highlightText(subDescElement, searchTerm);
                        }
                    } else {
                        row.style.display = 'none';
                    }
                }
            }
        });
        
        // Gérer l'état "aucune catégorie"
        if (emptyStateRow) {
            if (hasVisibleRows) {
                emptyStateRow.style.display = 'none';
            } else {
                emptyStateRow.style.display = '';
                // Ajuster le message en fonction des filtres
                const messageElement = emptyStateRow.querySelector('h3');
                const descriptionElement = emptyStateRow.querySelector('p.text-gray-500');
                
                if (searchTerm || selectedType) {
                    if (messageElement) messageElement.textContent = 'Aucune catégorie ne correspond aux critères';
                    if (descriptionElement) descriptionElement.textContent = 'Essayez de modifier vos filtres de recherche';
                } else {
                    if (messageElement) messageElement.textContent = 'Aucune catégorie trouvée';
                    if (descriptionElement) descriptionElement.textContent = 'Commencez par créer votre première catégorie';
                }
            }
        }
        
        // Mettre à jour le compteur
        if (totalCategoriesElement) {
            totalCategoriesElement.textContent = `${visibleCount} catégorie${visibleCount > 1 ? 's' : ''} trouvée${visibleCount > 1 ? 's' : ''}`;
        }
    }
    
    // Événements
    searchInput.addEventListener('input', function() {
        filterCategories();
    });
    
    typeSelect.addEventListener('change', function() {
        filterCategories();
    });
    
    // Remplacer le bouton Filtrer par une réinitialisation
    if (filterButton) {
        filterButton.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Réinitialiser
        `;
        filterButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        filterButton.classList.add('bg-gray-200', 'hover:bg-gray-300', 'text-gray-800');
        
        filterButton.addEventListener('click', function(e) {
            e.preventDefault();
            searchInput.value = '';
            typeSelect.value = '';
            filterCategories();
            searchInput.focus();
        });
    }
    
    // Ajouter un délai pour éviter trop d'exécutions
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        
        // Afficher un indicateur de chargement rapide
        const originalPlaceholder = searchInput.placeholder;
        searchInput.placeholder = 'Recherche...';
        
        debounceTimer = setTimeout(() => {
            filterCategories();
            searchInput.placeholder = originalPlaceholder;
        }, 300);
    });
    
    // Recherche avec la touche Entrée
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            filterCategories();
        }
    });
    
    // Initialiser avec les valeurs de l'URL (si présentes)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search')) {
        searchInput.value = urlParams.get('search');
    }
    if (urlParams.has('type')) {
        typeSelect.value = urlParams.get('type');
    }
    
    // Appliquer le filtre initial
    filterCategories();
});

// Fonctions pour le modal
function showEquipmentList(equipmentList) {
    const modal = document.getElementById('equipmentModal');
    const content = document.getElementById('modalContent');
    
    content.innerHTML = '';
    
    equipmentList.forEach(item => {
        const div = document.createElement('div');
        div.className = 'flex items-center p-2 hover:bg-gray-50 rounded';
        div.innerHTML = `
            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-gray-700">${item}</span>
        `;
        content.appendChild(div);
    });
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('equipmentModal').classList.add('hidden');
}

// Fermer le modal en cliquant à l'extérieur
window.onclick = function(event) {
    const modal = document.getElementById('equipmentModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Rendre les boutons "autres" cliquables
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('button.text-blue-600').forEach(button => {
        button.addEventListener('click', function() {
            const categoryRow = this.closest('tr');
            const categoryName = categoryRow.querySelector('h3').textContent;
            
            // Récupérer tous les équipements de cette catégorie
            const equipmentItems = [];
            categoryRow.querySelectorAll('.text-sm.text-gray-700 .truncate').forEach(item => {
                equipmentItems.push(item.textContent);
            });
            
            showEquipmentList(equipmentItems);
        });
    });
});
</script>

<style>
/* Styles personnalisés */
.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Style pour la surbrillance de recherche */
.search-highlight {
    background-color: #FFEB3B;
    padding: 0.1em 0.2em;
    border-radius: 0.2em;
    font-weight: bold;
}

/* Animation pour les résultats de recherche */
tr {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

tr[style*="display: none"] {
    opacity: 0;
    transform: translateX(-10px);
    height: 0;
    overflow: hidden;
}

/* Animation pour le modal */
@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#equipmentModal > div {
    animation: modalFadeIn 0.3s ease-out;
}

/* Style pour les sous-catégories */
tr.bg-gray-50 {
    border-left: 4px solid #e5e7eb;
}

/* Style pour le champ de recherche actif */
input:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
    
    .grid-cols-3 {
        grid-template-columns: 1fr;
    }
    
    .flex-col {
        flex-direction: column;
    }
    
    /* Amélioration de la recherche sur mobile */
    input[type="text"], select {
        font-size: 16px; /* Empêche le zoom sur iOS */
    }
    
    .bg-gray-200 {
        width: 100%;
        justify-content: center;
    }
}

/* Animation douce pour les changements */
.transition-all {
    transition: all 0.3s ease;
}
</style>
@endsection