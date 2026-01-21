@extends('layouts.app')

@section('title', 'Équipements Hors Service')
@section('header', 'Équipements Hors Service')

@section('content')
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Header avec statistiques -->
    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-white border-b">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Équipements Hors Service</h2>
                <p class="text-gray-600 mt-1">Gestion des équipements inutilisables ou obsolètes</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($stats['en_attente'] > 0)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-50 text-yellow-700 border border-yellow-100">
                    <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                    {{ $stats['en_attente'] }} En attente
                </span>
                @endif
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-50 text-gray-700 border border-gray-100">
                    <span class="w-2 h-2 bg-gray-500 rounded-full mr-2"></span>
                    {{ $stats['total'] }} Hors service
                </span>
            </div>
        </div>
    </div>

    <!-- Actions toolbar -->
    <div class="px-6 py-4 bg-gray-50 border-b">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <!-- Search and filters -->
            <div class="flex-1 w-full">
                <form action="{{ route('hors-service.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                   placeholder="Rechercher par N° série, destinataire...">
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <select name="raison" 
                                class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white">
                            <option value="">Toutes les raisons</option>
                            <option value="panne" @selected(request('raison') == 'panne')>Panne</option>
                            <option value="obsolescence" @selected(request('raison') == 'obsolescence')>Obsolescence</option>
                            <option value="accident" @selected(request('raison') == 'accident')>Accident</option>
                            <option value="autre" @selected(request('raison') == 'autre')>Autre</option>
                        </select>
                        
                        <select name="statut" 
                                class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" @selected(request('statut') == 'en_attente')>En attente</option>
                            <option value="traite" @selected(request('statut') == 'traite')>Traité</option>
                        </select>
                        
                        <button type="submit" 
                                class="px-4 py-2.5 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition duration-200">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filtrer
                            </span>
                        </button>
                        
                        @if(request()->anyFilled(['raison', 'statut', 'search']))
                        <a href="{{ route('hors-service.index') }}" 
                           class="px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-200">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            
            <!-- Action buttons 
            <div class="flex items-center space-x-2">
                <a href="{{ route('hors-service.create') }}" 
                   class="px-4 py-2.5 bg-gradient-to-r from-orange-600 to-orange-700 text-white font-medium rounded-lg hover:from-orange-700 hover:to-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition duration-200 flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouvelle Déclaration
                </a>
            </div>-->
        </div>
    </div>

    @if(session('success'))
    <div class="m-4">
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics -->
    <div class="px-6 py-4 border-b bg-gradient-to-r from-gray-50 to-white">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            @php
                $statsCards = [
                    ['label' => 'Total HS', 'count' => $stats['total'], 'color' => 'gray', 'icon' => '📋'],
                    ['label' => 'En attente', 'count' => $stats['en_attente'], 'color' => 'yellow', 'icon' => '⏳'],
                    ['label' => 'Traités', 'count' => $stats['traites'], 'color' => 'green', 'icon' => '✓'],
                    ['label' => 'Valeur totale', 'count' => number_format($stats['valeur_totale'], 2, ',', ' ') . ' €', 'color' => 'blue', 'icon' => '💰'],
                    ['label' => 'Moyenne/valeur', 'count' => $stats['total'] > 0 ? number_format($stats['valeur_totale'] / $stats['total'], 2, ',', ' ') . ' €' : '0 €', 'color' => 'purple', 'icon' => '📊'],
                ];
            @endphp
            
            @foreach($statsCards as $stat)
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
                        @if($stats['total'] > 0)
                        <div class="h-full bg-{{ $stat['color'] }}-500 rounded-full" 
                             style="width: {{ in_array($stat['label'], ['Valeur totale', 'Moyenne/valeur']) ? 100 : ($stat['count'] / $stats['total'] * 100) }}%">
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
                        Date & Raison
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Destinataire
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Valeur
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Statut
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($horsServices as $hs)
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10 bg-orange-50 rounded-lg flex items-center justify-center mr-3">
                                <svg class="h-5 w-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $hs->numero_serie }}</div>
                                @if($hs->equipment)
                                <div class="text-sm text-gray-500 mt-1">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
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
                    
                    <td class="px-6 py-4">
                        @if($hs->destinataire)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <div class="text-sm text-gray-900">{{ Str::limit($hs->destinataire, 20) }}</div>
                        </div>
                        @else
                        <span class="text-gray-400 italic text-sm">Non défini</span>
                        @endif
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                @if($hs->valeur_residuelle)
                                <div class="font-medium text-gray-900">{{ number_format($hs->valeur_residuelle, 2, ',', ' ') }} €</div>
                                @if($hs->equipment && $hs->equipment->prix_achat)
                                <div class="text-xs text-gray-500">
                                    {{ round(($hs->valeur_residuelle / $hs->equipment->prix_achat) * 100) }}% valeur initiale
                                </div>
                                @endif
                                @else
                                <div class="text-gray-400 italic text-sm">Non estimée</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($hs->date_traitement)
                        <div class="space-y-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                Traité
                            </span>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
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
                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-1">
                            <a href="{{ route('hors-service.show', $hs->id) }}" 
                               class="inline-flex items-center p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition duration-150"
                               title="Voir les détails">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            
                            <a href="{{ route('hors-service.edit', $hs->id) }}" 
                               class="inline-flex items-center p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition duration-150"
                               title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            
                            @if(!$hs->date_traitement)
                            <button type="button" onclick="openTraiterModal({{ $hs->id }})" 
                                    class="inline-flex items-center p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition duration-150"
                                    title="Marquer comme traité">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
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
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement hors service</h3>
                            <p class="text-gray-500 mb-4">Tous vos équipements sont en état de fonctionnement</p>
                            <a href="{{ route('hors-service.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Nouvelle Déclaration
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($horsServices->hasPages())
    <div class="px-6 py-4 border-t bg-gray-50">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Affichage de <span class="font-medium">{{ $horsServices->firstItem() }}</span> à 
                <span class="font-medium">{{ $horsServices->lastItem() }}</span> sur 
                <span class="font-medium">{{ $horsServices->total() }}</span> résultats
            </div>
            <div>
                {{ $horsServices->links() }}
            </div>
        </div>
    </div>
    @endif
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
@endsection

@push('scripts')
<script>
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
@endpush