@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Détails de la Maintenance</h1>
        <div>
            @if($maintenance->statut == 'en_cours')
                <button type="button" onclick="openTerminerModal()" 
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Terminer
                </button>
                <button type="button" onclick="openAnnulerModal()" 
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Annuler
                </button>
            @endif
            <a href="{{ route('maintenance.edit', $maintenance->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                Modifier
            </a>
            <a href="{{ route('maintenance.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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
                <h2 class="text-lg font-semibold text-gray-700">Informations de la maintenance</h2>
                @php
                    $badgeClasses = [
                        'en_cours' => 'bg-yellow-100 text-yellow-800',
                        'terminee' => 'bg-green-100 text-green-800',
                        'annulee' => 'bg-gray-100 text-gray-800'
                    ];
                    $statutLabels = [
                        'en_cours' => 'En cours',
                        'terminee' => 'Terminée',
                        'annulee' => 'Annulée'
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $badgeClasses[$maintenance->statut] ?? 'bg-gray-100' }}">
                    {{ $statutLabels[$maintenance->statut] ?? $maintenance->statut }}
                </span>
            </div>
        </div>
        
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Numéro de série</h3>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $maintenance->numero_serie }}</p>
                    </div>
                    
                    @if($maintenance->equipment)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Équipement</h3>
                        <p class="mt-1 text-gray-900">{{ $maintenance->equipment->type }}</p>
                        <p class="text-sm text-gray-500">{{ $maintenance->equipment->marque }} {{ $maintenance->equipment->modele }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Type de maintenance</h3>
                        @php
                            $types = [
                                'preventive' => 'Préventive',
                                'corrective' => 'Corrective',
                                'contractuelle' => 'Contractuelle',
                                'autre' => 'Autre'
                            ];
                        @endphp
                        <p class="mt-1 text-gray-900">{{ $types[$maintenance->type_maintenance] ?? $maintenance->type_maintenance }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Prestataire</h3>
                        <p class="mt-1 text-gray-900">{{ $maintenance->prestataire }}</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Dates</h3>
                        <div class="grid grid-cols-2 gap-4 mt-2">
                            <div>
                                <p class="text-xs text-gray-500">Départ</p>
                                <p class="text-gray-900">{{ $maintenance->date_depart->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Retour prévu</p>
                                <p class="text-gray-900 {{ $maintenance->date_retour_prevue < now() && $maintenance->statut == 'en_cours' ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $maintenance->date_retour_prevue->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @if($maintenance->date_retour_reelle)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Retour réel</h3>
                        <p class="mt-1 text-green-600 font-semibold">{{ $maintenance->date_retour_reelle->format('d/m/Y') }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Coût</h3>
                        <p class="mt-1 text-gray-900">
                            @if($maintenance->cout)
                                {{ number_format($maintenance->cout, 2, ',', ' ') }} €
                            @else
                                Non renseigné
                            @endif
                        </p>
                    </div>
                    
                    @if($maintenance->date_retour_prevue < now() && $maintenance->statut == 'en_cours')
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Retard</h3>
                        <p class="mt-1 text-red-600 font-semibold">
                            {{ now()->diffInDays($maintenance->date_retour_prevue) }} jour(s)
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="mt-8">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Description de la panne / besoin</h3>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-gray-700 whitespace-pre-line">{{ $maintenance->description_panne }}</p>
                </div>
            </div>
            
            @if($maintenance->travaux_realises)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Travaux réalisés</h3>
                <div class="bg-green-50 p-4 rounded">
                    <p class="text-gray-700 whitespace-pre-line">{{ $maintenance->travaux_realises }}</p>
                </div>
            </div>
            @endif
            
            @if($maintenance->observations)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Observations</h3>
                <div class="bg-blue-50 p-4 rounded">
                    <p class="text-gray-700 whitespace-pre-line">{{ $maintenance->observations }}</p>
                </div>
            </div>
            @endif
        </div>
        
        <div class="px-6 py-4 bg-gray-50 border-t">
            <div class="flex justify-between text-sm text-gray-500">
                <div>Créé le: {{ $maintenance->created_at->format('d/m/Y H:i') }}</div>
                <div>Modifié le: {{ $maintenance->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@if($maintenance->statut == 'en_cours')
<div id="terminerModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Terminer la maintenance</h3>
        <form action="{{ route('maintenance.terminer', $maintenance->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_retour_reelle">
                    Date de retour réelle *
                </label>
                <input type="date" name="date_retour_reelle" id="date_retour_reelle" required
                    value="{{ date('Y-m-d') }}"
                    min="{{ $maintenance->date_depart->format('Y-m-d') }}"
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

<div id="annulerModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Annuler la maintenance</h3>
        <form action="{{ route('maintenance.annuler', $maintenance->id) }}" method="POST">
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

<script>
function openTerminerModal() {
    document.getElementById('terminerModal').classList.remove('hidden');
}

function closeTerminerModal() {
    document.getElementById('terminerModal').classList.add('hidden');
}

function openAnnulerModal() {
    document.getElementById('annulerModal').classList.remove('hidden');
}

function closeAnnulerModal() {
    document.getElementById('annulerModal').classList.add('hidden');
}
</script>
@endif
@endsection