@extends('layouts.app')

@section('title', 'Détails Déclaration Perte')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="card-cofina">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-cofina-red mb-2">
                        🔍 Détails de déclaration de perte
                    </h1>
                    <p class="text-gray-600">
                        Demande #{{ $approval->id }} - 
                        {{ $approval->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                
                <div class="text-right">
                    @switch($approval->status)
                        @case('pending')
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                ⏳ En attente
                            </span>
                            @break
                        @case('approved')
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                ✅ Approuvé
                            </span>
                            @break
                        @case('rejected')
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                ❌ Rejeté
                            </span>
                            @break
                    @endswitch
                </div>
            </div>
        </div>

        <!-- Informations de l'équipement -->
        <div class="mb-8 p-6 bg-blue-50 rounded-lg border-2 border-blue-200">
            <h2 class="text-xl font-bold text-blue-800 mb-4">📦 Équipement concerné</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Nom</label>
                    <p class="text-lg font-bold">{{ $approval->equipment->nom }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">N° Série</label>
                    <p class="font-mono text-lg">{{ $approval->equipment->numero_serie }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Modèle</label>
                    <p>{{ $approval->equipment->modele ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Département</label>
                    <p>{{ $approval->equipment->departement ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Poste staff</label>
                    <p>{{ $approval->equipment->poste_staff ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Date acquisition</label>
                    <p>{{ $approval->equipment->date_acquisition ? \Carbon\Carbon::parse($approval->equipment->date_acquisition)->format('d/m/Y') : 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Détails de la disparition -->
        <div class="mb-8 p-6 bg-red-50 rounded-lg border-2 border-red-200">
            <h2 class="text-xl font-bold text-red-800 mb-4">🔍 Détails de la disparition</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Type de disparition</label>
                    <div class="p-3 bg-white rounded border">
                        @php
                            $types = [
                                'vol' => 'Vol',
                                'perte' => 'Perte',
                                'non_localise' => 'Non localisé'
                            ];
                        @endphp
                        <span class="font-bold text-red-600">
                            {{ $types[$data['type_disparition']] ?? $data['type_disparition'] }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Date de disparition</label>
                    <div class="p-3 bg-white rounded border">
                        <span class="font-bold">
                            {{ \Carbon\Carbon::parse($data['date_disparition'])->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Lieu de disparition</label>
                    <div class="p-3 bg-white rounded border">
                        {{ $data['lieu_disparition'] }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Responsable</label>
                    <div class="p-3 bg-white rounded border">
                        {{ $data['responsable_disparition'] ?? 'Non spécifié' }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Plainte déposée</label>
                    <div class="p-3 bg-white rounded border">
                        <span class="font-bold {{ $data['plainte_deposee'] ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['plainte_deposee'] ? '✅ Oui' : '❌ Non' }}
                        </span>
                        @if($data['plainte_deposee'] && isset($data['numero_plainte']))
                        <div class="text-sm mt-1">
                            N° plainte: {{ $data['numero_plainte'] }}
                        </div>
                        @endif
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Valeur assurée</label>
                    <div class="p-3 bg-white rounded border">
                        @if(isset($data['valeur_assuree']) && $data['valeur_assuree'] > 0)
                            <span class="font-bold">{{ number_format($data['valeur_assuree'], 2) }} FCFA</span>
                        @else
                            <span class="text-gray-500">Non assuré</span>
                        @endif
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Circonstances</label>
                    <div class="p-3 bg-white rounded border min-h-[100px]">
                        {{ $data['circonstances'] }}
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Mesures prises</label>
                    <div class="p-3 bg-white rounded border">
                        {{ $data['mesures_prises'] ?? 'Aucune mesure signalée' }}
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Observations</label>
                    <div class="p-3 bg-white rounded border">
                        {{ $data['observations'] ?? 'Aucune observation' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations du demandeur -->
        <div class="mb-8 p-6 bg-green-50 rounded-lg border-2 border-green-200">
            <h2 class="text-xl font-bold text-green-800 mb-4">👤 Demandeur</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Nom</label>
                    <p>{{ $data['agent_nom'] }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Prénom</label>
                    <p>{{ $data['agent_prenom'] }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Fonction</label>
                    <p>{{ $data['agent_fonction'] }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Date déclaration</label>
                    <p>{{ \Carbon\Carbon::parse($data['submitted_at'])->format('d/m/Y H:i') }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600">Statut demande</label>
                    <p class="font-bold">
                        {{ $data['is_super_admin_submission'] ? 'Soumission directe Super Admin' : 'Soumission standard' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Section validation (si en attente et utilisateur peut valider) -->
        @if($approval->status == 'pending' && auth()->user()->canApprove($approval))
        <div class="mt-8 p-6 bg-yellow-50 rounded-lg border-2 border-yellow-200">
            <h2 class="text-xl font-bold text-yellow-800 mb-4">✅ Validation</h2>
            
            <form action="{{ route('transitions.approve-perdu', $approval) }}" method="POST" id="validation-form">
                @csrf
                
                <div class="mb-6">
                    <h3 class="font-bold text-lg mb-3">Checklist de validation</h3>
                    <div class="space-y-3">
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_circonstances]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">Les circonstances sont clairement décrites</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_mesures_prises]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">Les mesures nécessaires ont été prises</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_plainte]" value="1"
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">Plainte déposée si nécessaire (vol)</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_documentation]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">La documentation est complète</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_responsable]" value="1"
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">Le responsable a été identifié</span>
                        </label>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="validation_notes" class="block font-bold mb-2">Notes de validation</label>
                    <textarea name="validation_notes" id="validation_notes" rows="3"
                              class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cofina-red"
                              placeholder="Remarques, observations, instructions supplémentaires..."></textarea>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="btn-cofina-success px-6 py-3 text-lg">
                        ✅ Approuver la déclaration
                    </button>
                    
                    <button type="button" onclick="showRejectModal()" class="btn-cofina-outline-red px-6 py-3">
                        ❌ Rejeter la demande
                    </button>
                </div>
            </form>
            
            <!-- Modal de rejet -->
            <div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="bg-white rounded-lg p-6 max-w-md w-full">
                        <h3 class="text-lg font-bold mb-4">Rejeter la déclaration</h3>
                        <form action="{{ route('transitions.reject-perdu', $approval) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="raison_rejet" class="block font-bold mb-2">Raison du rejet *</label>
                                <textarea name="raison_rejet" id="raison_rejet" rows="4" required
                                          class="w-full px-3 py-2 border-2 border-red-300 rounded focus:border-red-500"
                                          placeholder="Expliquez précisément pourquoi cette déclaration est rejetée..."></textarea>
                            </div>
                            <div class="flex gap-4">
                                <button type="submit" class="btn-cofina-outline-red px-4 py-2 flex-1">
                                    Confirmer le rejet
                                </button>
                                <button type="button" onclick="hideRejectModal()" 
                                        class="btn-cofina-outline px-4 py-2 flex-1">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Historique (si approuvé ou rejeté) -->
        @if($approval->status != 'pending')
        <div class="mt-8 p-6 {{ $approval->status == 'approved' ? 'bg-green-50' : 'bg-red-50' }} rounded-lg border-2 {{ $approval->status == 'approved' ? 'border-green-200' : 'border-red-200' }}">
            <h2 class="text-xl font-bold {{ $approval->status == 'approved' ? 'text-green-800' : 'text-red-800' }} mb-4">
                @if($approval->status == 'approved')
                ✅ Décision d'approbation
                @else
                ❌ Décision de rejet
                @endif
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Décision par</label>
                    <p class="font-bold">{{ $approval->approver->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Date décision</label>
                    <p>{{ $approval->approved_at ? $approval->approved_at->format('d/m/Y H:i') : 
                         ($approval->rejected_at ? $approval->rejected_at->format('d/m/Y H:i') : 'N/A') }}</p>
                </div>
                
                @if($approval->validation_notes)
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600">Notes de validation</label>
                    <p class="p-3 bg-white rounded border">{{ $approval->validation_notes }}</p>
                </div>
                @endif
                
                @if($approval->rejection_reason)
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600">Raison du rejet</label>
                    <p class="p-3 bg-white rounded border text-red-600">{{ $approval->rejection_reason }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Boutons d'action -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
            <a href="{{ route('admin.perdu-approvals.list') }}" 
               class="btn-cofina-outline inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à la liste
            </a>
            
            @if($approval->status == 'approved')
            <div class="flex gap-4">
                
                <a href="{{ route('equipment.show', $approval->equipment) }}" 
                   class="btn-cofina inline-flex items-center">
                    👁️ Voir l'équipement
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('reject-modal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('reject-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideRejectModal();
    }
});
</script>
@endsection