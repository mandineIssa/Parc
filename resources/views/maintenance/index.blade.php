@extends('layouts.app')

@section('title', 'Gestion des Maintenances')
@section('header', 'Gestion des Maintenances')

@section('content')
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Header avec statistiques -->
    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Suivi des Maintenances</h2>
                <p class="text-gray-600 mt-1">Suivez l'état de toutes les interventions de maintenance</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($stats['retard'] > 0)
                <a href="{{ route('maintenance.retard') }}" 
                   class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 transition duration-200">
                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                    {{ $stats['retard'] }} Retard(s)
                </a>
                @endif
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-50 text-blue-700 border border-blue-100">
                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                    {{ $stats['total'] }} Maintenance(s) au total
                </span>
            </div>
        </div>
    </div>

    <!-- Actions toolbar -->
    <div class="px-6 py-4 bg-gray-50 border-b">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <!-- Search and filters -->
            <div class="flex-1 w-full">
                <form action="{{ route('maintenance.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Rechercher par N° série, prestataire...">
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <select name="statut" 
                                class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">Tous les statuts</option>
                            <option value="en_cours" @selected(request('statut') == 'en_cours')>En cours</option>
                            <option value="terminee" @selected(request('statut') == 'terminee')>Terminée</option>
                            <option value="annulee" @selected(request('statut') == 'annulee')>Annulée</option>
                        </select>
                        
                        <select name="type_maintenance" 
                                class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">Tous les types</option>
                            <option value="preventive" @selected(request('type_maintenance') == 'preventive')>Préventive</option>
                            <option value="corrective" @selected(request('type_maintenance') == 'corrective')>Corrective</option>
                            <option value="contractuelle" @selected(request('type_maintenance') == 'contractuelle')>Contractuelle</option>
                            <option value="autre" @selected(request('type_maintenance') == 'autre')>Autre</option>
                        </select>
                        
                        <button type="submit" 
                                class="px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filtrer
                            </span>
                        </button>
                        
                        @if(request()->anyFilled(['statut', 'type_maintenance', 'search']))
                        <a href="{{ route('maintenance.index') }}" 
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
            
            <!-- Action buttons -->
            <div class="flex items-center space-x-2">
                <a href="{{ route('maintenance.retard') }}" 
                   class="px-4 py-2.5 {{ $stats['retard'] > 0 ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-400 cursor-not-allowed' }} text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200 flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Retards ({{ $stats['retard'] }})
                </a>
                
                <a href="{{ route('maintenance.create') }}" 
                   class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouvelle Maintenance
                </a>
            </div>
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
                    ['label' => 'En cours', 'count' => $stats['en_cours'], 'color' => 'yellow', 'icon' => '🔄'],
                    ['label' => 'Terminées', 'count' => $stats['termine'], 'color' => 'green', 'icon' => '✓'],
                    ['label' => 'Annulées', 'count' => $stats['annule'], 'color' => 'gray', 'icon' => '✗'],
                    ['label' => 'En retard', 'count' => $stats['retard'], 'color' => 'red', 'icon' => '⏰'],
                    ['label' => 'Coût total', 'count' => number_format($stats['cout_total'], 2, ',', ' ') . ' €', 'color' => 'blue', 'icon' => '💰'],
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
                             style="width: {{ in_array($stat['label'], ['En retard', 'Coût total']) ? 100 : ($stat['count'] / $stats['total'] * 100) }}%">
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
                        Type & Prestataire
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Dates
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                        Coût
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
                @forelse($maintenances as $maint)
                <tr class="hover:bg-gray-50 transition duration-150 {{ $maint->date_retour_prevue < now() && $maint->statut == 'en_cours' ? 'bg-red-50 hover:bg-red-100' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                                <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $maint->numero_serie }}</div>
                                @if($maint->equipment)
                                <div class="text-sm text-gray-500 mt-1">
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
                            <div class="text-sm text-gray-900 font-medium">{{ Str::limit($maint->prestataire, 25) }}</div>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="space-y-1">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <div class="text-sm text-gray-900">Départ: {{ $maint->date_depart->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">il y a {{ $maint->date_depart->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 {{ $maint->date_retour_prevue < now() && $maint->statut == 'en_cours' ? 'text-red-500' : 'text-gray-400' }} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-1">
                            <a href="{{ route('maintenance.show', $maint->id) }}" 
                               class="inline-flex items-center p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition duration-150"
                               title="Voir les détails">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            
                            <a href="{{ route('maintenance.edit', $maint->id) }}" 
                               class="inline-flex items-center p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition duration-150"
                               title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            
                            @if($maint->statut == 'en_cours')
                            <button type="button" onclick="openTerminerModal({{ $maint->id }})" 
                                    class="inline-flex items-center p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition duration-150"
                                    title="Terminer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            
                            <button type="button" onclick="openAnnulerModal({{ $maint->id }})" 
                                    class="inline-flex items-center p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition duration-150"
                                    title="Annuler">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune maintenance enregistrée</h3>
                            <p class="text-gray-500 mb-4">Commencez par créer une nouvelle maintenance</p>
                            <a href="{{ route('maintenance.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
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
    @if($maintenances->hasPages())
    <div class="px-6 py-4 border-t bg-gray-50">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Affichage de <span class="font-medium">{{ $maintenances->firstItem() }}</span> à 
                <span class="font-medium">{{ $maintenances->lastItem() }}</span> sur 
                <span class="font-medium">{{ $maintenances->total() }}</span> résultats
            </div>
            <div>
                {{ $maintenances->links() }}
            </div>
        </div>
    </div>
    @endif
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
@endsection

@push('scripts')
<script>
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

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTerminerModal();
        closeAnnulerModal();
    }
});

// Close modals on outside click
document.getElementById('terminerModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeTerminerModal();
});

document.getElementById('annulerModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeAnnulerModal();
});
</script>
@endpush