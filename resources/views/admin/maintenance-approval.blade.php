@extends('layouts.app')

@section('title', 'Détails Approbation Maintenance')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="card-cofina">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
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
                    <label class="block text-sm font-semibold text-gray-600">Type</label>
                    <p>{{ $approval->equipment->type ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Utilisateur actuel</label>
                    <p>{{ $data['parc_info']['utilisateur_nom'] ?? 'Non affecté' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Département</label>
                    <p>{{ $data['parc_info']['departement'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Détails de la maintenance -->
        <div class="mb-8 p-6 bg-gray-50 rounded-lg border-2 border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-4">🔧 Détails de la maintenance</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Type de maintenance</label>
                    <div class="p-3 bg-white rounded border">
                        @php
                            $types = [
                                'preventive' => '🛡️ Préventive',
                                'corrective' => '🔧 Corrective',
                                'curative' => '🏥 Curative'
                            ];
                        @endphp
                        <span class="font-bold">
                            {{ $types[$data['type_maintenance']] ?? $data['type_maintenance'] }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Prestataire</label>
                    <div class="p-3 bg-white rounded border">
                        <span class="font-bold">{{ $data['prestataire'] }}</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Date retour prévue</label>
                    <div class="p-3 bg-white rounded border">
                        <span class="font-bold">
                            {{ \Carbon\Carbon::parse($data['date_retour_prevue'])->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Priorité</label>
                    <div class="p-3 bg-white rounded border">
                        @php
                            $priorites = [
                                'normal' => '🟢 Normale',
                                'urgent' => '🟡 Urgente',
                                'critique' => '🔴 Critique'
                            ];
                        @endphp
                        <span class="font-bold {{ $data['priorite'] == 'critique' ? 'text-red-600' : ($data['priorite'] == 'urgent' ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $priorites[$data['priorite']] ?? $data['priorite'] }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Coût estimé</label>
                    <div class="p-3 bg-white rounded border">
                        <span class="font-bold">
                            {{ isset($data['cout_estime']) ? number_format($data['cout_estime'], 2) . ' FCFA' : 'Non estimé' }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Localisation</label>
                    <div class="p-3 bg-white rounded border">
                        {{ $data['localisation'] ?? 'Non spécifiée' }}
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Description de la panne</label>
                    <div class="p-3 bg-white rounded border min-h-[100px]">
                        {{ $data['description_panne'] }}
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Notes</label>
                    <div class="p-3 bg-white rounded border">
                        {{ $data['notes'] ?? 'Aucune note' }}
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
                    <label class="block text-sm font-semibold text-gray-600">Date demande</label>
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
            
            <form action="{{ route('transitions.approve-maintenance', $approval) }}" method="POST" id="validation-form">
                @csrf
                
                <div class="mb-6">
                    <h3 class="font-bold text-lg mb-3">Checklist de validation</h3>
                    <div class="space-y-3">
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_type_maintenance]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">Le type de maintenance est approprié</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_prestataire]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">Le prestataire est validé</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_description]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">La description de la panne est complète</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_cout_estime]" value="1"
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">Le coût estimé est raisonnable</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_urgence]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">L'urgence est correctement évaluée</span>
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
                        ✅ Approuver l'envoi en maintenance
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
                        <h3 class="text-lg font-bold mb-4">Rejeter la demande</h3>
                        <form action="{{ route('transitions.reject-maintenance', $approval) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="raison_rejet" class="block font-bold mb-2">Raison du rejet *</label>
                                <textarea name="raison_rejet" id="raison_rejet" rows="4" required
                                          class="w-full px-3 py-2 border-2 border-red-300 rounded focus:border-red-500"
                                          placeholder="Expliquez précisément pourquoi cette demande est rejetée..."></textarea>
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
            <a href="{{ route('admin.maintenance-approvals.list') }}" 
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