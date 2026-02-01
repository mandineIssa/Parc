@extends('layouts.app')

@section('title', 'Approbation Hors Service')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-cofina-red mb-6">Approbation de mise hors service</h1>
        
        <div class="mb-8">
            <h2 class="text-lg font-bold mb-4">Informations de l'équipement</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold">Nom:</label>
                    <p>{{ $approval->equipment->nom }}</p>
                </div>
                <div>
                    <label class="font-semibold">N° Série:</label>
                    <p>{{ $approval->equipment->numero_serie }}</p>
                </div>
                <div>
                    <label class="font-semibold">Modèle:</label>
                    <p>{{ $approval->equipment->modele }}</p>
                </div>
                <div>
                    <label class="font-semibold">Type:</label>
                    <p>{{ $approval->equipment->type }}</p>
                </div>
            </div>
        </div>
        
        <div class="mb-8">
            <h2 class="text-lg font-bold mb-4">Détails de la demande</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold">Raison:</label>
                    <p>{{ $data['raison'] }}</p>
                </div>
                <div>
                    <label class="font-semibold">Destinataire:</label>
                    <p>{{ $data['destinataire'] }}</p>
                </div>
                <div>
                    <label class="font-semibold">Valeur résiduelle:</label>
                    <p>{{ number_format($data['valeur_residuelle'] ?? 0, 2) }} FCFA</p>
                </div>
                <div>
                    <label class="font-semibold">Justificatif:</label>
                    <p>{{ $data['justificatif'] ?? 'Non spécifié' }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="font-semibold">Description de l'incident:</label>
                    <p class="bg-gray-50 p-3 rounded">{{ $data['description_incident'] }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="font-semibold">Observations:</label>
                    <p class="bg-gray-50 p-3 rounded">{{ $data['observations'] ?? 'Aucune' }}</p>
                </div>
            </div>
        </div>
        
        <div class="mb-8">
            <h2 class="text-lg font-bold mb-4">Informations de l'agent</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="font-semibold">Nom:</label>
                    <p>{{ $data['agent_nom'] }}</p>
                </div>
                <div>
                    <label class="font-semibold">Prénom:</label>
                    <p>{{ $data['agent_prenom'] }}</p>
                </div>
                <div>
                    <label class="font-semibold">Fonction:</label>
                    <p>{{ $data['agent_fonction'] }}</p>
                </div>
                <div>
                    <label class="font-semibold">Date de soumission:</label>
                    <p>{{ \Carbon\Carbon::parse($data['submitted_at'])->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
        
        @if($approval->status == 'pending')
        <div class="border-t pt-6">
            <h2 class="text-lg font-bold mb-4">Validation</h2>
            
            <form action="{{ route('transitions.approve-hors-service', $approval) }}" method="POST" id="approval-form">
                @csrf
                
                <div class="mb-6">
                    <h3 class="font-bold mb-3">Checklist de validation</h3>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[verif_raison]" value="1" required
                                   class="h-4 w-4 text-cofina-red rounded">
                            <span class="ml-2">La raison de mise hors service est justifiée</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[verif_justificatif]" value="1" required
                                   class="h-4 w-4 text-cofina-red rounded">
                            <span class="ml-2">Le justificatif est valide</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[verif_valeur_residuelle]" value="1"
                                   class="h-4 w-4 text-cofina-red rounded">
                            <span class="ml-2">La valeur résiduelle est correctement estimée</span>
                        </label>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="validation_notes" class="block font-bold mb-2">Notes de validation</label>
                    <textarea name="validation_notes" id="validation_notes" rows="3"
                              class="w-full px-3 py-2 border-2 border-gray-300 rounded"
                              placeholder="Remarques sur la validation..."></textarea>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="btn-cofina-success px-6 py-3">
                        ✅ Approuver la mise hors service
                    </button>
                    
                    <button type="button" onclick="showRejectModal()" class="btn-cofina-outline px-6 py-3">
                        ❌ Rejeter la demande
                    </button>
                </div>
            </form>
            
            <!-- Modal de rejet -->
            <div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
                <div class="flex items-center justify-center min-h-screen">
                    <div class="bg-white rounded-lg p-6 max-w-md w-full">
                        <h3 class="text-lg font-bold mb-4">Rejeter la demande</h3>
                        <form action="{{ route('transitions.reject-hors-service', $approval) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="raison_rejet" class="block font-bold mb-2">Raison du rejet *</label>
                                <textarea name="raison_rejet" id="raison_rejet" rows="3" required
                                          class="w-full px-3 py-2 border-2 border-red-300 rounded"
                                          placeholder="Expliquez pourquoi cette demande est rejetée..."></textarea>
                            </div>
                            <div class="flex gap-4">
                                <button type="submit" class="btn-cofina-outline-red px-4 py-2">
                                    Confirmer le rejet
                                </button>
                                <button type="button" onclick="hideRejectModal()" class="btn-cofina-outline px-4 py-2">
                                    Annuler
                                </button>
                            </div>
                        </form>
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
            </script>
        </div>
        @else
        <div class="border-t pt-6">
            <h2 class="text-lg font-bold mb-4">Statut de la demande</h2>
            <div class="p-4 rounded-lg {{ $approval->status == 'approved' ? 'bg-green-100' : 'bg-red-100' }}">
                <p class="font-bold {{ $approval->status == 'approved' ? 'text-green-800' : 'text-red-800' }}">
                    {{ $approval->status == 'approved' ? '✅ Demandé approuvée' : '❌ Demande rejetée' }}
                </p>
                @if($approval->approved_at)
                <p class="mt-2">Approuvée par: {{ $approval->approver->name ?? 'N/A' }}</p>
                <p>Date d'approbation: {{ $approval->approved_at->format('d/m/Y H:i') }}</p>
                @endif
                @if($approval->rejected_at)
                <p class="mt-2">Rejetée par: {{ $approval->rejected_by_user->name ?? 'N/A' }}</p>
                <p>Raison du rejet: {{ $approval->rejection_reason }}</p>
                @endif
            </div>
        </div>
        @endif
        
        <div class="mt-8 pt-6 border-t">
            <a href="{{ route('admin.approvals') }}" class="btn-cofina-outline inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour aux approbations
            </a>
        </div>
    </div>
</div>
@endsection