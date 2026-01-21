@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Détails de la Déclaration de Perte</h1>
        <div>
            @if($perdu->statut_recherche == 'en_cours')
                <button type="button" onclick="openRetrouverModal()" 
                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Marquer comme retrouvé
                </button>
            @endif
            <a href="{{ route('perdu.edit', $perdu->id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">
                Modifier
            </a>
            <a href="{{ route('perdu.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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
                @php
                    $badgeClasses = [
                        'en_cours' => 'bg-yellow-100 text-yellow-800',
                        'trouve' => 'bg-green-100 text-green-800',
                        'definitif' => 'bg-red-100 text-red-800'
                    ];
                    $statutLabels = [
                        'en_cours' => 'En recherche',
                        'trouve' => 'Retrouvé',
                        'definitif' => 'Définitif'
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $badgeClasses[$perdu->statut_recherche] ?? 'bg-gray-100' }}">
                    {{ $statutLabels[$perdu->statut_recherche] ?? $perdu->statut_recherche }}
                </span>
            </div>
        </div>
        
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Numéro de série</h3>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $perdu->numero_serie }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Date de disparition</h3>
                        <p class="mt-1 text-gray-900">{{ $perdu->date_disparition->format('d/m/Y') }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Lieu de disparition</h3>
                        <p class="mt-1 text-gray-900">{{ $perdu->lieu_disparition }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Type de disparition</h3>
                        @php
                            $types = [
                                'vol' => 'Vol',
                                'perte' => 'Perte',
                                'oubli' => 'Oubli',
                                'destruction' => 'Destruction'
                            ];
                        @endphp
                        <p class="mt-1 text-gray-900">{{ $types[$perdu->type_disparition] ?? $perdu->type_disparition }}</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    @if($perdu->equipment)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Informations équipement</h3>
                        <p class="mt-1 font-semibold text-gray-900">{{ $perdu->equipment->type }}</p>
                        <p class="text-gray-900">{{ $perdu->equipment->marque }} {{ $perdu->equipment->modele }}</p>
                        <p class="text-sm text-gray-500">Modèle: {{ $perdu->equipment->modele }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Plainte déposée</h3>
                        <p class="mt-1 text-gray-900">
                            @if($perdu->plainte_deposee)
                                <span class="text-red-600 font-semibold">Oui</span>
                                @if($perdu->numero_plainte)
                                    <div class="text-gray-900">N°: {{ $perdu->numero_plainte }}</div>
                                @endif
                            @else
                                <span class="text-gray-600">Non</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Valeur assurée</h3>
                        <p class="mt-1 text-gray-900">
                            @if($perdu->valeur_assuree)
                                {{ number_format($perdu->valeur_assuree, 2, ',', ' ') }} €
                            @else
                                Non renseignée
                            @endif
                        </p>
                    </div>
                    
                    @if($perdu->date_retrouvaille)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Date de retrouvaille</h3>
                        <p class="mt-1 text-green-600 font-semibold">
                            {{ $perdu->date_retrouvaille->format('d/m/Y') }}
                        </p>
                        <p class="text-sm text-gray-500">Lieu: {{ $perdu->lieu_retrouvaille }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="mt-8">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Circonstances</h3>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-gray-700 whitespace-pre-line">{{ $perdu->circonstances }}</p>
                </div>
            </div>
            
            @if($perdu->etat_retrouvaille)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">État lors de la retrouvaille</h3>
                <div class="bg-green-50 p-4 rounded">
                    <p class="text-gray-700 whitespace-pre-line">{{ $perdu->etat_retrouvaille }}</p>
                </div>
            </div>
            @endif
            
            @if($perdu->observations)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Observations</h3>
                <div class="bg-blue-50 p-4 rounded">
                    <p class="text-gray-700 whitespace-pre-line">{{ $perdu->observations }}</p>
                </div>
            </div>
            @endif
        </div>
        
        <div class="px-6 py-4 bg-gray-50 border-t">
            <div class="flex justify-between text-sm text-gray-500">
                <div>Créé le: {{ $perdu->created_at->format('d/m/Y H:i') }}</div>
                <div>Modifié le: {{ $perdu->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour marquer comme retrouvé -->
@if($perdu->statut_recherche == 'en_cours')
<div id="retrouverModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Marquer comme retrouvé</h3>
        <form action="{{ route('perdu.retrouver', $perdu->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_retrouvaille">
                    Date de retrouvaille *
                </label>
                <input type="date" name="date_retrouvaille" id="date_retrouvaille" required
                    value="{{ date('Y-m-d') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="lieu_retrouvaille">
                    Lieu de retrouvaille *
                </label>
                <input type="text" name="lieu_retrouvaille" id="lieu_retrouvaille" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Ex: Domicile de l'employé, Bureau...">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="etat_retrouvaille">
                    État de l'équipement *
                </label>
                <textarea name="etat_retrouvaille" id="etat_retrouvaille" rows="3" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Décrivez l'état dans lequel l'équipement a été retrouvé..."></textarea>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeRetrouverModal()"
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
function openRetrouverModal() {
    document.getElementById('retrouverModal').classList.remove('hidden');
}

function closeRetrouverModal() {
    document.getElementById('retrouverModal').classList.add('hidden');
}
</script>
@endif
@endsection