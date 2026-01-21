@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Équipements Hors Service</h1>
        <a href="{{ route('hors-service.create') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            Nouvelle Déclaration
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total HS</div>
            <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow p-4">
            <div class="text-sm text-yellow-500">En attente</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['en_attente'] }}</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4">
            <div class="text-sm text-green-500">Traités</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['traites'] }}</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-4">
            <div class="text-sm text-blue-500">Valeur résiduelle</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['valeur_totale'], 2, ',', ' ') }} €</div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Série</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date HS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Raison</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destinataire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valeur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($horsServices as $hs)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $hs->numero_serie }}</div>
                        @if($hs->equipment)
                            <div class="text-sm text-gray-500">{{ $hs->equipment->type }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $hs->date_hors_service->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $raisons = [
                                'panne' => 'Panne',
                                'obsolescence' => 'Obsolescence',
                                'accident' => 'Accident',
                                'autre' => 'Autre'
                            ];
                        @endphp
                        {{ $raisons[$hs->raison] ?? $hs->raison }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $hs->destinataire ? Str::limit($hs->destinataire, 25) : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($hs->valeur_residuelle)
                            {{ number_format($hs->valeur_residuelle, 2, ',', ' ') }} €
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($hs->date_traitement)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                Traité
                            </span>
                            <div class="text-xs text-gray-500">{{ $hs->date_traitement->format('d/m/Y') }}</div>
                        @else
                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                                En attente
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('hors-service.show', $hs->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                        <a href="{{ route('hors-service.edit', $hs->id) }}" class="text-green-600 hover:text-green-900 mr-3">Modifier</a>
                        @if(!$hs->date_traitement)
                            <button type="button" onclick="openTraiterModal({{ $hs->id }})" 
                                class="text-purple-600 hover:text-purple-900 mr-3">
                                Traiter
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        Aucun équipement hors service déclaré
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4">
            {{ $horsServices->links() }}
        </div>
    </div>
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
                    Valeur résiduelle (€)
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

<script>
function openTraiterModal(hsId) {
    const form = document.getElementById('traiterForm');
    form.action = `/hors-service/${hsId}/traiter`;
    document.getElementById('traiterModal').classList.remove('hidden');
}

function closeTraiterModal() {
    document.getElementById('traiterModal').classList.add('hidden');
}
</script>
@endsection