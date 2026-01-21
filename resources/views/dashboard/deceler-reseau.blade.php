{{-- resources/views/dashboard/deceler-reseau.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 22 22">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total équipements DECELER Réseau</p>
                    <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        
        <!-- Valeur totale -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 22 22">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Valeur résiduelle totale</p>
                    <p class="text-2xl font-bold">{{ number_format($stats['valeur_totale'] * 0.3, 2, ',', ' ') }} €</p>
                    <p class="text-xs text-gray-400">(30% de la valeur totale)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Stock DECELER - Réseau</h1>
            <p class="text-gray-600 mt-1">Gestion des équipements réseau retournés (DECELER)</p>
        </div>
        <div class="mt-4 md:mt-0 space-x-2">
            <a href="{{ route('dashboard.deceler-reseau.export') }}" 
               class="bg-green-300 hover:bg-green-400 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 22 22">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Exporter CSV
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-700">Filtres DECELER Réseau</h2>
        </div>
        <form action="{{ route('dashboard.deceler-reseau.filter') }}" method="GET" class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="etat">
                        État retour
                    </label>
                    <select name="etat" id="etat" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                        <option value="">Tous les états</option>
                        <option value="neuf" {{ request('etat') == 'neuf' ? 'selected' : '' }}>Neuf</option>
                        <option value="bon" {{ request('etat') == 'bon' ? 'selected' : '' }}>Bon</option>
                        <option value="moyen" {{ request('etat') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                        <option value="mauvais" {{ request('etat') == 'mauvais' ? 'selected' : '' }}>Mauvais</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="categorie">
                        Catégorie
                    </label>
                    <select name="categorie" id="categorie" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                        <option value="">Toutes catégories</option>
                        @foreach($categoryStats as $category => $count)
                            @if($category)
                                <option value="{{ $category }}" {{ request('categorie') == $category ? 'selected' : '' }}>
                                    {{ $category }} ({{ $count }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="origine">
                        Origine retour
                    </label>
                    <select name="origine" id="origine" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                        <option value="">Toutes origines</option>
                        <option value="parc" {{ request('origine') == 'parc' ? 'selected' : '' }}>Parc</option>
                        <option value="maintenance" {{ request('origine') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="hors_service" {{ request('origine') == 'hors_service' ? 'selected' : '' }}>Hors Service</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="marque">
                        Marque
                    </label>
                    <input type="text" name="marque" id="marque" 
                           value="{{ request('marque') }}"
                           placeholder="Saisir une marque..."
                           class="shadow border rounded w-full py-2 px-3 text-gray-700">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="date_from">
                        Date retour (début)
                    </label>
                    <input type="date" name="date_from" id="date_from" 
                           value="{{ request('date_from') }}"
                           class="shadow border rounded w-full py-2 px-3 text-gray-700">
                </div>
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="date_to">
                        Date retour (fin)
                    </label>
                    <input type="date" name="date_to" id="date_to" 
                           value="{{ request('date_to') }}"
                           class="shadow border rounded w-full py-2 px-3 text-gray-700">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filtrer
                    </button>
                    <a href="{{ route('dashboard.deceler-reseau') }}" 
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Réinitialiser
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tableau des équipements DECELER Réseau -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° de Série</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">État retour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marque / Modèle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date retour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Origine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Raison retour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valeur résiduelle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stocks as $stock)
                    <tr>
                        <!-- N° de Série -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $stock->numero_serie }}</div>
                            <div class="text-sm text-gray-500">Qty: {{ $stock->quantite }}</div>
                        </td>
                        
                        <!-- Type -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->equipment)
                                <div class="font-medium text-gray-900">{{ $stock->equipment->type ?? 'Réseau' }}</div>
                                <div class="text-xs text-gray-500">{{ $stock->equipment->categorie ?? '' }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- État retour -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->deceler && $stock->deceler->etat_retour)
                                @php
                                    $etatClasses = [
                                        'neuf' => 'bg-green-100 text-green-800',
                                        'bon' => 'bg-blue-100 text-blue-800',
                                        'moyen' => 'bg-yellow-100 text-yellow-800',
                                        'mauvais' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $etatClasses[$stock->deceler->etat_retour] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($stock->deceler->etat_retour) }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- Marque / Modèle -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->equipment)
                                <div class="font-medium text-gray-900">{{ $stock->equipment->marque ?? '' }}</div>
                                <div class="text-sm text-gray-600">{{ $stock->equipment->modele ?? '-' }}</div>
                            @else
                                <span class="text-red-500">Équipement non trouvé</span>
                            @endif
                        </td>
                                                
                        <!-- Date retour -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->deceler && $stock->deceler->date_retour)
                                <div class="font-medium text-gray-900">{{ $stock->deceler->date_retour->format('d/m/Y') }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- Origine -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->deceler && $stock->deceler->origine)
                                @php
                                    $origineClasses = [
                                        'parc' => 'bg-blue-100 text-blue-800',
                                        'maintenance' => 'bg-yellow-100 text-yellow-800',
                                        'hors_service' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $origineClasses[$stock->deceler->origine] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($stock->deceler->origine) }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- Raison retour -->
                        <td class="px-6 py-4">
                            @if($stock->deceler && $stock->deceler->raison_retour)
                                <div class="text-sm text-gray-700 truncate max-w-xs">{{ $stock->deceler->raison_retour }}</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- Valeur résiduelle -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->deceler && $stock->deceler->valeur_residuelle)
                                <div class="font-bold text-gray-900">{{ number_format($stock->deceler->valeur_residuelle, 0, ',', ' ') }} €</div>
                            @elseif($stock->equipment && $stock->equipment->prix)
                                <div class="text-gray-600">{{ number_format($stock->equipment->prix * 0.3, 0, ',', ' ') }} €</div>
                                <div class="text-xs text-gray-400">(estimation 30%)</div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        
                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($stock->equipment)
                                <a href="{{ route('equipment.show', $stock->equipment->id) }}" 
                                   class="text-green-600 hover:text-green-900 font-medium block mb-1">
                                    Détail équipement
                                </a>
                            @endif
                            @if($stock->deceler)
                                <a href="#" class="text-blue-600 hover:text-blue-900 text-xs">
                                    Voir retour
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            Aucun équipement réseau DECELER trouvé
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4">
            {{ $stocks->links() }}
        </div>
    </div>

    <!-- Récentes entrées DECELER Réseau -->
    <div class="mt-6 bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-700">Derniers retours DECELER Réseau</h2>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                @forelse($stats['recent_entries'] as $recent)
                <div class="border rounded p-4 hover:bg-gray-50">
                    <div class="font-medium text-gray-900">{{ $recent->numero_serie }}</div>
                    @if($recent->equipment)
                        <div class="text-sm text-gray-600">{{ $recent->equipment->marque }}</div>
                        <div class="text-xs text-gray-500">{{ $recent->equipment->modele ?? '' }}</div>
                    @endif
                    @if($recent->deceler)
                        <div class="text-xs text-gray-500 mt-2">
                            {{ $recent->deceler->origine ?? '' }} - {{ $recent->deceler->date_retour ? $recent->deceler->date_retour->format('d/m/Y') : '' }}
                        </div>
                    @endif
                    <div class="mt-2">
                        <span class="px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded">
                            Réseau
                        </span>
                    </div>
                </div>
                @empty
                <div class="col-span-5 text-center text-gray-500 py-4">
                    Aucun retour réseau récent
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styles personnalisés pour le dashboard DECELER Réseau */
    .bg-orange-50 { background-color: #fff7ed; }
    .bg-orange-100 { background-color: #ffedd5; }
    .text-orange-800 { color: #9a3412; }
</style>
@endpush