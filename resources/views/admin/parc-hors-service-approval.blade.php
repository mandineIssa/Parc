@extends('layouts.app')

@section('title', 'Détails Hors Service (Parc)')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="card-cofina">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-600">Demande #{{ $approval->id }}</p>
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

        <!-- Contenu spécifique parc -->
        <div class="mb-8 p-6 bg-green-50 rounded-lg border-2 border-green-200">
            <h2 class="text-xl font-bold text-green-800 mb-4">📋 Informations spécifiques (Parc)</h2>
            
            <!-- Vos champs spécifiques au parc ici -->
            @if(isset($data['parc_info']))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Dernier utilisateur</label>
                    <p>{{ $data['parc_info']['utilisateur_nom'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Département</label>
                    <p>{{ $data['parc_info']['departement'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Poste</label>
                    <p>{{ $data['parc_info']['poste_affecte'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Date affectation</label>
                    <p>{{ \Carbon\Carbon::parse($data['parc_info']['date_affectation'])->format('d/m/Y') ?? 'N/A' }}</p>
                </div>
            </div>
            @else
            <p class="text-gray-600">Aucune information spécifique parc disponible.</p>
            @endif
        </div>

        <!-- Informations générales (peut être un partial) -->
        @include('admin.partials.hors-service-general-info', ['approval' => $approval, 'data' => $data])

        <!-- Section validation si en attente -->
        @if($approval->status == 'pending' && auth()->user()->canApprove($approval))
        <div class="mt-8 p-6 bg-yellow-50 rounded-lg border-2 border-yellow-200">
            <h2 class="text-xl font-bold text-yellow-800 mb-4">✅ Validation</h2>
            
            <form action="{{ route('transitions.approve-parc-hors-service', $approval) }}" method="POST">
                @csrf
                
                <!-- Checklist de validation spécifique parc -->
                <div class="mb-6">
                    <h3 class="font-bold text-lg mb-3">Checklist de validation</h3>
                    <div class="space-y-3">
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_raison]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">La raison de mise hors service est justifiée</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_utilisateur]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">L'utilisateur a été notifié</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_documentation]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3">Toute la documentation est complète</span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn-cofina-success px-6 py-3">
                    ✅ Approuver la mise hors service
                </button>
            </form>
        </div>
        @endif

        <!-- Bouton retour -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.hors-service-approvals.list') }}" 
               class="btn-cofina-outline inline-flex items-center">
                ← Retour à la liste
            </a>
        </div>
    </div>
</div>
@endsection