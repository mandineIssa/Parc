@extends('layouts.app')

@section('title', 'Déclarations de Perte')

@section('header', 'Déclarations de Perte')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="card-cofina">
        <!-- En-tête -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-cofina-red mb-2">🔍 Déclarations de perte</h1>
            <p class="text-gray-600">Liste des équipements déclarés perdus en attente de validation</p>
        </div>

        <!-- Filtres -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
            <form method="GET" action="{{ route('admin.perdu-approvals.list') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Statut</label>
                    <select name="status" class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Type</label>
                    <select name="type" class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                        <option value="">Tous les types</option>
                        <option value="vol" {{ request('type') == 'vol' ? 'selected' : '' }}>Vol</option>
                        <option value="perte" {{ request('type') == 'perte' ? 'selected' : '' }}>Perte</option>
                        <option value="non_localise" {{ request('type') == 'non_localise' ? 'selected' : '' }}>Non localisé</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="N° série, nom équipement..."
                           class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                </div>
                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" class="btn-cofina px-6 py-2">
                        🔍 Filtrer
                    </button>
                    <a href="{{ route('admin.perdu-approvals.list') }}" class="btn-cofina-outline px-6 py-2">
                        ↺ Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        <!-- Tableau des déclarations -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Équipement
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Type perte
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Date / Lieu
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Demandeur
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Date déclaration
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($approvals as $approval)
                    @php
                        $data = json_decode($approval->data, true);
                        $equipment = $approval->equipment;
                        $typeLabels = [
                            'vol' => 'Vol',
                            'perte' => 'Perte',
                            'non_localise' => 'Non localisé'
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $equipment->nom ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $equipment->numero_serie ?? '' }}</div>
                            <div class="text-xs text-gray-400">{{ $equipment->modele ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($data['type_disparition'] == 'vol') bg-red-100 text-red-800
                                @elseif($data['type_disparition'] == 'perte') bg-orange-100 text-orange-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $typeLabels[$data['type_disparition']] ?? $data['type_disparition'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <div class="font-semibold">{{ \Carbon\Carbon::parse($data['date_disparition'])->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $data['lieu_disparition'] ?? '' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                {{ $approval->submitter->name ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $data['agent_fonction'] ?? '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                {{ $approval->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $approval->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($approval->status)
                                @case('pending')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        ⏳ En attente
                                    </span>
                                    @break
                                @case('approved')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        ✅ Approuvé
                                    </span>
                                    @if($approval->approved_at)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $approval->approved_at->format('d/m/Y') }}
                                    </div>
                                    @endif
                                    @break
                                @case('rejected')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        ❌ Rejeté
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.perdu-approval', $approval) }}" 
                               class="text-cofina-blue hover:text-cofina-red mr-3">
                                👁️ Voir
                            </a>
                            
                            @if($approval->status == 'pending' && auth()->user()->canApprove($approval))
                            <a href="{{ route('transitions.approval.show', $approval) }}?action=approve" 
                               class="text-green-600 hover:text-green-900 mr-3">
                                ✅ Valider
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <div class="text-lg mb-2">📭 Aucune déclaration trouvée</div>
                            <p class="text-sm">Il n'y a pas de déclaration de perte pour le moment.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($approvals->hasPages())
        <div class="mt-6">
            {{ $approvals->withQueryString()->links() }}
        </div>
        @endif

        <!-- Statistiques -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="text-sm font-semibold text-blue-800">Total déclarations</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $approvals->total() }}</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="text-sm font-semibold text-yellow-800">En attente</div>
                    <div class="text-2xl font-bold text-yellow-600">
                        {{ $approvals->where('status', 'pending')->count() }}
                    </div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="text-sm font-semibold text-red-800">Vols déclarés</div>
                    <div class="text-2xl font-bold text-red-600">
                        {{ collect($approvals->items())->filter(function($item) {
                            $data = json_decode($item->data, true);
                            return ($data['type_disparition'] ?? '') == 'vol';
                        })->count() }}
                    </div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="text-sm font-semibold text-green-800">Approuvées</div>
                    <div class="text-2xl font-bold text-green-600">
                        {{ $approvals->where('status', 'approved')->count() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Bouton retour -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.dashboard') }}" class="btn-cofina-outline inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour au tableau de bord
            </a>
        </div>
    </div>
</div>
@endsection