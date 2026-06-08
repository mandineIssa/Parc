@extends('layouts.app')

@section('title', 'Mon Espace Utilisateur')

@section('header', 'Mon Espace Personnel')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- En-tête -->
    <div class="mb-8">
    </div>

    <!-- Mes équipements -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-6">💻 Mes équipements affectés</h2>

        @if($myEquipments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($myEquipments as $equipment)
            <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">{{ $equipment->nom }}</h3>
                        <p class="text-sm text-gray-500">{{ $equipment->modele }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">
                        Affecté
                    </span>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm">
                        <span class="text-gray-500 mr-2">N° Série:</span>
                        <span class="font-mono">{{ $equipment->numero_serie }}</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <span class="text-gray-500 mr-2">Type:</span>
                        <span>{{ $equipment->type }}</span>
                    </div>
                    @if($equipment->parc->date_affectation ?? false)
                    <div class="flex items-center text-sm">
                        <span class="text-gray-500 mr-2">Affecté le:</span>
                        <span>{{ $equipment->parc->date_affectation->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('equipment.show', $equipment) }}" 
                       class="btn-cofina-outline text-xs flex-1 text-center">
                        👁️ Voir détails
                    </a>
                    <button onclick="reportIssue({{ $equipment->id }})" 
                            class="btn-cofina-outline text-xs flex-1 text-center bg-yellow-50">
                        ⚠️ Signaler problème
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <div class="text-4xl mb-4">💻</div>
            <p class="text-lg">Aucun équipement affecté</p>
            <p class="text-sm mt-2">Contactez le service IT pour obtenir un équipement</p>
        </div>
        @endif
    </div>

    <!-- Mes demandes -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-6">📝 Mes demandes récentes</h2>

        @if($myRequests->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Équipement
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Type demande
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($myRequests as $request)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $request->equipment->nom ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $request->equipment->numero_serie ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">
                                {{ $request->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @switch($request->status)
                                @case('pending')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        ⏳ En attente
                                    </span>
                                    @break
                                @case('approved')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        ✅ Approuvé
                                    </span>
                                    @break
                                @case('rejected')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        ❌ Rejeté
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $request->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <div class="text-4xl mb-4">📭</div>
            <p class="text-lg">Aucune demande récente</p>
        </div>
        @endif
    </div>

    <!-- Historique des mouvements -->
    @if($movementHistory->count() > 0)
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">🔄 Historique des mouvements</h2>
        
        <div class="space-y-4">
            @foreach($movementHistory as $movement)
            <div class="border-l-4 border-blue-500 pl-4 py-2">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium">{{ $movement->equipment->nom ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-500">
                            {{ ucfirst($movement->from_status) }} → {{ ucfirst($movement->to_status) }}
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $movement->approved_at ? $movement->approved_at->format('d/m/Y') : $movement->created_at->format('d/m/Y') }}
                    </div>
                </div>
                @if($movement->approver)
                <div class="text-xs text-gray-500 mt-1">
                    Approuvé par: {{ $movement->approver->name }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
function reportIssue(equipmentId) {
    if (confirm('Souhaitez-vous signaler un problème avec cet équipement ?')) {
        // Rediriger vers la page de création d'une demande de maintenance
        window.location.href = `/transitions/create?equipment_id=${equipmentId}&type=maintenance`;
    }
}
</script>
@endsection