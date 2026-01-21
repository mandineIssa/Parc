@extends('layouts.app')

@section('title', 'Détails de l\'approbation')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-cofina-red mb-2">
                    📋 Détails de l'approbation
                </h1>
                <p class="text-gray-600">
                    Demande #{{ str_pad($approval->id, 6, '0', STR_PAD_LEFT) }}
                </p>
            </div>
            <div>
                <a href="{{ route('admin.approvals') }}" class="btn-cofina-outline">
                    ← Retour aux approbations
                </a>
            </div>
        </div>
    </div>

    <!-- Statut -->
    <div class="card-cofina mb-8 {{ $approval->status === 'pending' ? 'border-l-4 border-yellow-500' : ($approval->status === 'approved' ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500') }}">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-cofina-red mb-2">Statut</h2>
                @if($approval->status === 'pending')
                    <span class="px-4 py-2 rounded-full bg-yellow-100 text-yellow-800 font-bold text-lg">
                        ⏳ En attente de validation
                    </span>
                @elseif($approval->status === 'approved')
                    <span class="px-4 py-2 rounded-full bg-green-100 text-green-800 font-bold text-lg">
                        ✅ Approuvée le {{ $approval->approved_at->format('d/m/Y à H:i') }}
                    </span>
                @elseif($approval->status === 'rejected')
                    <span class="px-4 py-2 rounded-full bg-red-100 text-red-800 font-bold text-lg">
                        ❌ Rejetée le {{ $approval->rejected_at->format('d/m/Y à H:i') }}
                    </span>
                @endif
            </div>
            @if($approval->approver)
                <div class="text-right">
                    <p class="text-sm text-gray-600">Traité par</p>
                    <p class="font-bold">{{ $approval->approver->name }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Informations de base -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Colonne gauche : Équipement et transition -->
        <div class="space-y-6">
            <!-- Équipement -->
            <div class="card-cofina">
                <h2 class="text-xl font-bold text-cofina-red mb-4 border-b-2 border-cofina-red pb-2">
                    🖥️ Équipement
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nom :</span>
                        <span class="font-bold">{{ $approval->equipment->nom ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">N° Série :</span>
                        <span class="font-bold text-cofina-red">{{ $approval->equipment->numero_serie ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Type :</span>
                        <span>{{ $approval->equipment->type ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Marque :</span>
                        <span>{{ $approval->equipment->marque ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Statut actuel :</span>
                        <span class="px-2 py-1 rounded bg-gray-100 text-gray-800 text-sm">
                            {{ strtoupper($approval->equipment->statut ?? 'N/A') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Personnes concernées -->
        <div class="space-y-6">
            <!-- Utilisateur final -->
            <div class="card-cofina">
                <h2 class="text-xl font-bold text-cofina-red mb-4 border-b-2 border-cofina-red pb-2">
                    👤 Utilisateur final
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nom complet :</span>
                        <span class="font-bold">{{ $data['user_name'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Département :</span>
                        <span class="font-bold text-cofina-red">{{ $data['departement'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Poste :</span>
                        <span>{{ $data['poste_affecte'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date prévue :</span>
                        <span>{{ isset($data['date_affectation']) ? \Carbon\Carbon::parse($data['date_affectation'])->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Demandeur -->
            <div class="card-cofina">
                <h2 class="text-xl font-bold text-cofina-red mb-4 border-b-2 border-cofina-red pb-2">
                    📝 Demandeur
                </h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nom :</span>
                        <span class="font-bold">{{ $approval->submitter->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email :</span>
                        <span>{{ $approval->submitter->email ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date de demande :</span>
                        <span>{{ $approval->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transition -->
    <div class="mt-8 card-cofina">
        <h2 class="text-xl font-bold text-cofina-red mb-4 border-b-2 border-cofina-red pb-2">
            🔄 Transition
        </h2>
        <div class="text-center p-6">
            <div class="flex items-center justify-center gap-4 mb-6">
                <div class="text-center">
                    <div class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg font-bold text-lg mb-2">
                        {{ strtoupper($approval->from_status) }}
                    </div>
                    <p class="text-sm text-gray-600">Ancien statut</p>
                </div>
                <div class="text-3xl text-cofina-red">→</div>
                <div class="text-center">
                    <div class="px-4 py-2 bg-green-100 text-green-800 rounded-lg font-bold text-lg mb-2">
                        {{ strtoupper($approval->to_status) }}
                    </div>
                    <p class="text-sm text-gray-600">Nouveau statut</p>
                </div>
            </div>
            <p class="text-gray-600">
                Type: <span class="font-bold">{{ $approval->type ?? 'stock_to_parc' }}</span>
            </p>
        </div>
    </div>

    @if($approval->status === 'pending')
    <!-- Boutons d'action -->
    <div class="mt-8 pt-6 border-t-2 border-cofina-red">
        <div class="flex gap-4">
            <a href="{{ route('transitions.approval.show', $approval) }}" 
               class="btn-cofina-success flex-1 py-4 text-center text-lg font-bold">
                📋 Compléter les fiches et valider
            </a>
            
            <!-- Formulaire de rejet avec bouton qui déclenche la modal -->
            <form method="POST" action="{{ route('transitions.reject', $approval) }}" id="rejectForm" class="flex-1">
                @csrf
                <!-- Champ caché pour la raison du rejet -->
                <input type="hidden" name="raison_rejet" id="hiddenRejectionReason" value="">
                
                <button type="button" 
                        onclick="showRejectionModal()"
                        class="btn-cofina-danger w-full py-4 text-lg font-bold">
                    ❌ Rejeter la demande
                </button>
            </form>
        </div>
        <p class="text-sm text-gray-500 text-center mt-4">
            <strong>Note :</strong> "Compléter les fiches" vous redirigera vers le formulaire de validation avec les fiches de mouvement et d'installation.
        </p>
    </div>
    @endif

</div>

<!-- Modal pour saisir la raison du rejet -->
<div id="rejectionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-bold text-cofina-red mb-4">Raison du rejet</h3>
            <div class="mt-2 px-7 py-3">
                <textarea id="modalRejectionReason" 
                          class="w-full border border-gray-300 rounded p-2" 
                          rows="4" 
                          placeholder="Veuillez indiquer la raison du rejet..."
                          required></textarea>
                <p class="text-sm text-gray-500 mt-2">Ce champ est obligatoire pour rejeter la demande.</p>
            </div>
            <div class="flex gap-2 justify-center mt-4">
                <button onclick="submitRejection()" 
                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 font-bold">
                    Confirmer le rejet
                </button>
                <button onclick="closeModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showRejectionModal() {
    document.getElementById('rejectionModal').classList.remove('hidden');
    document.getElementById('modalRejectionReason').focus();
}

function closeModal() {
    document.getElementById('rejectionModal').classList.add('hidden');
    document.getElementById('modalRejectionReason').value = '';
}

function submitRejection() {
    const reason = document.getElementById('modalRejectionReason').value.trim();
    
    if (!reason) {
        alert('Veuillez saisir une raison pour le rejet.');
        document.getElementById('modalRejectionReason').focus();
        return;
    }
    
    // Confirmation finale
    if (!confirm('Êtes-vous sûr de vouloir rejeter cette demande ? Cette action est irréversible.')) {
        return;
    }
    
    // Mettre à jour le champ caché
    document.getElementById('hiddenRejectionReason').value = reason;
    
    // Soumettre le formulaire
    document.getElementById('rejectForm').submit();
}

// Fermer la modal avec la touche Échap
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});

// Empêcher la fermeture en cliquant à l'extérieur
document.getElementById('rejectionModal').addEventListener('click', function(event) {
    if (event.target === this) {
        if (confirm('Annuler le rejet ? Les données saisies seront perdues.')) {
            closeModal();
        }
    }
});
</script>

<style>
#rejectionModal {
    backdrop-filter: blur(2px);
}

#rejectionModal > div {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>
@endsection