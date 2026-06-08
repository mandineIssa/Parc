@extends('layouts.app')

@section('title', 'Détails Maintenance → Stock')

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
                    
                    <!-- Badge état retour -->
                    @if(isset($data['etat_apres_maintenance']))
                    <div class="mt-2">
                        @php
                            $etatInfo = [
                                'parfait' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Parfait état'],
                                'bon' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'Bon état'],
                                'moyen' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'État moyen'],
                                'usage' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'label' => 'Bon état d\'usage'],
                            ];
                            $currentEtat = $etatInfo[$data['etat_apres_maintenance']] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => $data['etat_apres_maintenance']];
                        @endphp
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $currentEtat['bg'] }} {{ $currentEtat['text'] }}">
                            {{ $currentEtat['label'] }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informations de l'équipement -->
        <div class="mb-8 p-6 bg-blue-50 rounded-lg border-2 border-blue-200">
            <h2 class="text-xl font-bold text-blue-800 mb-4">📦 Équipement concerné</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Nom</label>
                    <p class="text-lg font-bold">{{ $approval->equipment->nom ?? 'N/A' }}</p>
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
                    <label class="block text-sm font-semibold text-gray-600">Valeur initiale</label>
                    <p>{{ number_format($approval->equipment->valeur_initiale ?? 0, 2) }} FCFA</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Statut actuel</label>
                    <p class="font-bold {{ $approval->equipment->statut == 'stock' ? 'text-green-600' : 'text-orange-600' }}">
                        {{ ucfirst($approval->equipment->statut) }}
                    </p>
                </div>
            </div>
            
            <!-- Informations de maintenance -->
            @if(isset($data['maintenance_info']) && $data['maintenance_info'])
            <div class="mt-4 pt-4 border-t border-blue-300">
                <h3 class="font-bold text-blue-700 mb-2">🔧 Historique maintenance</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Date entrée</label>
                        <p>{{ \Carbon\Carbon::parse($data['maintenance_info']['date_entree'])->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Type intervention</label>
                        <p>{{ $data['maintenance_info']['type_intervention'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Problème initial</label>
                        <p class="text-sm">{{ $data['maintenance_info']['probleme_constate'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Détails du retour au stock -->
        <div class="mb-8 p-6 bg-green-50 rounded-lg border-2 border-green-200">
            <h2 class="text-xl font-bold text-green-800 mb-4">✅ Détails du retour au stock</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(isset($data['type_maintenance']))
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Type de maintenance</label>
                    <div class="p-3 bg-white rounded border">
                        @php
                            $types = [
                                'preventive' => 'Maintenance préventive',
                                'curative' => 'Maintenance curative',
                                'ameliorative' => 'Maintenance améliorative',
                                'corrective' => 'Maintenance corrective',
                                'autre' => 'Autre'
                            ];
                        @endphp
                        <span class="font-bold">{{ $types[$data['type_maintenance']] ?? $data['type_maintenance'] }}</span>
                    </div>
                </div>
                @endif
                
                @if(isset($data['etat_apres_maintenance']))
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">État après maintenance</label>
                    <div class="p-3 bg-white rounded border">
                        <span class="font-bold">{{ $data['etat_apres_maintenance'] }}</span>
                    </div>
                </div>
                @endif
                
                @if(isset($data['cout_maintenance']) && $data['cout_maintenance'])
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Coût de maintenance</label>
                    <div class="p-3 bg-white rounded border">
                        <span class="font-bold">{{ number_format($data['cout_maintenance'], 2) }} FCFA</span>
                    </div>
                </div>
                @endif
                
                @if(isset($data['date_retour']))
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Date de retour</label>
                    <div class="p-3 bg-white rounded border">
                        <span>{{ \Carbon\Carbon::parse($data['date_retour'])->format('d/m/Y') }}</span>
                    </div>
                </div>
                @endif
                
                @if(isset($data['technicien_nom']))
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Technicien responsable</label>
                    <div class="p-3 bg-white rounded border">
                        <span class="font-bold">{{ $data['technicien_nom'] }}</span>
                    </div>
                </div>
                @endif
                
                @if(isset($data['duree_maintenance']) && $data['duree_maintenance'])
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Durée de maintenance</label>
                    <div class="p-3 bg-white rounded border">
                        <span>{{ $data['duree_maintenance'] }} jours</span>
                    </div>
                </div>
                @endif
                
                @if(isset($data['garantie_maintenance']) && $data['garantie_maintenance'])
                <div>
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Garantie maintenance</label>
                    <div class="p-3 bg-white rounded border">
                        <span class="font-bold text-blue-600">{{ $data['garantie_maintenance'] }} mois</span>
                    </div>
                </div>
                @endif
                
                @if(isset($data['travaux_effectues']))
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Travaux effectués</label>
                    <div class="p-3 bg-white rounded border min-h-[120px]">
                        {{ $data['travaux_effectues'] }}
                    </div>
                </div>
                @endif
                
                @if(isset($data['observations']))
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-600 mb-2">Observations</label>
                    <div class="p-3 bg-white rounded border">
                        {{ $data['observations'] ?? 'Aucune observation' }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Checklist de vérification -->
        @if(isset($data['checklist']) && !empty($data['checklist']))
        <div class="mb-8 p-6 bg-yellow-50 rounded-lg border-2 border-yellow-200">
            <h2 class="text-xl font-bold text-yellow-800 mb-4">📋 Checklist de vérification</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @if(isset($data['checklist']['fonctionnel_test']) && $data['checklist']['fonctionnel_test'] == '1')
                <div class="flex items-center text-green-600">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Test fonctionnel OK</span>
                </div>
                @endif
                
                @if(isset($data['checklist']['nettoyage']) && $data['checklist']['nettoyage'] == '1')
                <div class="flex items-center text-green-600">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Nettoyage complet</span>
                </div>
                @endif
                
                @if(isset($data['checklist']['pieces_remplacees']) && $data['checklist']['pieces_remplacees'] == '1')
                <div class="flex items-center text-green-600">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Pièces remplacées listées</span>
                </div>
                @endif
                
                @if(isset($data['checklist']['documentation']) && $data['checklist']['documentation'] == '1')
                <div class="flex items-center text-green-600">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Documentation mise à jour</span>
                </div>
                @endif
                
                @if(isset($data['checklist']['test_securite']) && $data['checklist']['test_securite'] == '1')
                <div class="flex items-center text-green-600">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Tests de sécurité OK</span>
                </div>
                @endif
                
                @if(isset($data['checklist']['accessoires_complets']) && $data['checklist']['accessoires_complets'] == '1')
                <div class="flex items-center text-green-600">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span>Accessoires complets</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Informations du demandeur -->
        <div class="mb-8 p-6 bg-blue-50 rounded-lg border-2 border-blue-200">
            <h2 class="text-xl font-bold text-blue-800 mb-4">👤 Demandeur</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Nom</label>
                    <p>{{ $data['agent_nom'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Prénom</label>
                    <p>{{ $data['agent_prenom'] ?? '' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Fonction</label>
                    <p>{{ $data['agent_fonction'] ?? 'AGENT IT' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Date demande</label>
                    <p>{{ isset($data['submitted_at']) ? \Carbon\Carbon::parse($data['submitted_at'])->format('d/m/Y H:i') : $approval->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Section validation (si en attente et utilisateur peut valider) -->
        @if($approval->status == 'pending' && auth()->user()->canApprove($approval))
        <div class="mt-8 p-6 bg-yellow-50 rounded-lg border-2 border-yellow-200">
            <h2 class="text-xl font-bold text-yellow-800 mb-4">✅ Validation</h2>
            
            <form action="{{ route('transitions.approve-maintenance-to-stock', $approval) }}" method="POST" id="validation-form">
                @csrf
                
                <div class="mb-6">
                    <h3 class="font-bold text-lg mb-3">Checklist de validation</h3>
                    <div class="space-y-3">
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_travaux]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">Les travaux effectués sont correctement documentés</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_etat]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">L'état après maintenance correspond à la réalité</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_documentation]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">La documentation est complète</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_cout]" value="1"
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">Le coût de maintenance est justifié</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_garantie]" value="1"
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">La garantie maintenance est appropriée</span>
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
                        ✅ Approuver le retour au stock
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
                        <form action="{{ route('transitions.reject-maintenance-to-stock', $approval) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="raison_rejet" class="block font-bold mb-2">Raison du rejet *</label>
                                <textarea name="raison_rejet" id="raison_rejet" rows="4" required
                                          class="w-full px-3 py-2 border-2 border-red-300 rounded focus:border-red-500"
                                          placeholder="Expliquez pourquoi cette demande est rejetée..."></textarea>
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
            <a href="{{ route('admin.maintenance-to-stock-approvals.list') }}" 
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
const modal = document.getElementById('reject-modal');
if (modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            hideRejectModal();
        }
    });
}
</script>
@endsection