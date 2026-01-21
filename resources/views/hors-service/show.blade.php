@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Détails Hors Service</h1>
        <div>
            @if(!$horsService->date_traitement)
                <button type="button" onclick="openTraiterModal()" 
                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Marquer comme traité
                </button>
            @endif
            <a href="{{ route('hors-service.edit', $horsService->id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">
                Modifier
            </a>
            <a href="{{ route('hors-service.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Retour
            </a>
        </div>
    </div>
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-700">Informations de la déclaration</h2>
                @if($horsService->date_traitement)
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        Traité le {{ $horsService->date_traitement->format('d/m/Y') }}
                    </span>
                @else
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                        En attente de traitement
                    </span>
                @endif
            </div>
        </div>
        
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Numéro de série</h3>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $horsService->numero_serie }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Date hors service</h3>
                        <p class="mt-1 text-gray-900">{{ $horsService->date_hors_service->format('d/m/Y') }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Raison</h3>
                        @php
                            $raisons = [
                                'panne' => 'Panne',
                                'obsolescence' => 'Obsolescence',
                                'accident' => 'Accident',
                                'autre' => 'Autre'
                            ];
                        @endphp
                        <p class="mt-1 text-gray-900">{{ $raisons[$horsService->raison] ?? $horsService->raison }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Destinataire</h3>
                        <p class="mt-1 text-gray-900">{{ $horsService->destinataire ?? 'Non spécifié' }}</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    @if($horsService->equipment)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Informations équipement</h3>
                        <p class="mt-1 font-semibold text-gray-900">{{ $horsService->equipment->type }}</p>
                        <p class="text-gray-900">{{ $horsService->equipment->marque }} {{ $horsService->equipment->modele }}</p>
                        <p class="text-sm text-gray-500">Modèle: {{ $horsService->equipment->modele }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Valeur résiduelle</h3>
                        <p class="mt-1 text-gray-900">
                            @if($horsService->valeur_residuelle)
                                {{ number_format($horsService->valeur_residuelle, 2, ',', ' ') }} €
                            @else
                                Non renseignée
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Date de traitement</h3>
                        <p class="mt-1 text-gray-900">
                            @if($horsService->date_traitement)
                                {{ $horsService->date_traitement->format('d/m/Y') }}
                            @else
                                <span class="text-yellow-600">En attente</span>
                            @endif
                        </p>
                    </div>
                    
                    @if($horsService->justificatif)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Justificatif</h3>
                        <a href="{{ route('hors-service.download-justificatif', $horsService->id) }}" 
                           class="inline-flex items-center text-blue-600 hover:text-blue-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Télécharger le justificatif
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="mt-8">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Description de l'incident</h3>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-gray-700 whitespace-pre-line">{{ $horsService->description_incident }}</p>
                </div>
            </div>
            
            @if($horsService->observations)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Observations</h3>
                <div class="bg-blue-50 p-4 rounded">
                    <p class="text-gray-700 whitespace-pre-line">{{ $horsService->observations }}</p>
                </div>
            </div>
            @endif
        </div>
        
        <div class="px-6 py-4 bg-gray-50 border-t">
            <div class="flex justify-between text-sm text-gray-500">
                <div>Créé le: {{ $horsService->created_at->format('d/m/Y H:i') }}</div>
                <div>Modifié le: {{ $horsService->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour marquer comme traité -->
@if(!$horsService->date_traitement)
<div id="traiterModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Marquer comme traité</h3>
        <form action="{{ route('hors-service.traiter', $horsService->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_traitement">
                    Date de traitement *
                </label>
                <input type="date" name="date_traitement" id="date_traitement" required
                    value="{{ date('Y-m-d') }}"
                    min="{{ $horsService->date_hors_service->format('Y-m-d') }}"
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
function openTraiterModal() {
    document.getElementById('traiterModal').classList.remove('hidden');
}

function closeTraiterModal() {
    document.getElementById('traiterModal').classList.add('hidden');
}
</script>
@endif
@endsection