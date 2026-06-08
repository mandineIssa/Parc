@extends('layouts.app')

@section('title', 'Détails de l\'approbation')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
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
                        ✅ Approuvée
                    </span>
                @elseif($approval->status === 'rejected')
                    <span class="px-4 py-2 rounded-full bg-red-100 text-red-800 font-bold text-lg">
                        ❌ Rejetée
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
                </div>
            </div>

            <!-- Transition -->
            <div class="card-cofina">
                <h2 class="text-xl font-bold text-cofina-red mb-4 border-b-2 border-cofina-red pb-2">
                    🔄 Transition demandée
                </h2>
                <div class="text-center p-6">
                    <div class="flex items-center justify-center gap-4 mb-6">
                        <div class="text-center">
                            <div class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg font-bold text-lg mb-2">
                                {{ strtoupper($approval->from_status) }}
                            </div>
                            <p class="text-sm text-gray-600">Statut actuel</p>
                        </div>
                        <div class="text-3xl text-cofina-red">→</div>
                        <div class="text-center">
                            <div class="px-4 py-2 bg-green-100 text-green-800 rounded-lg font-bold text-lg mb-2">
                                {{ strtoupper($approval->to_status) }}
                            </div>
                            <p class="text-sm text-gray-600">Nouveau statut</p>
                        </div>
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

    @if($approval->status === 'pending')
    <!-- Boutons d'action -->
    <div class="mt-8 pt-6 border-t-2 border-cofina-red">
        <div class="flex gap-4">
            <a href="{{ route('transitions.approval.show', $approval) }}" 
               class="btn-cofina-success flex-1 py-4 text-center text-lg font-bold">
                📋 Compléter les fiches et valider
            </a>
            
            <form method="POST" action="{{ route('transitions.reject', $approval) }}" class="flex-1">
                @csrf
                <button type="submit" 
                        onclick="return confirm('Êtes-vous sûr de vouloir rejeter cette demande ?')"
                        class="btn-cofina-danger w-full py-4 text-lg font-bold">
                    ❌ Rejeter la demande
                </button>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection