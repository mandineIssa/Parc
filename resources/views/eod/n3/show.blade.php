{{-- resources/views/eod/n3/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détail EOD - ' . $fiche->reference)
@section('header', 'Supervision EOD - Détail de la fiche')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Fiche de suivi {{ $fiche->reference }}</h1>
            <p class="text-gray-600 mt-2">
                {{ $fiche->date_traitement->format('d/m/Y') }} · 
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $fiche->status_class }}">
                    {{ $fiche->status_label }}
                </span>
            </p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            @if($fiche->status === 'VALIDATED')
            <a href="{{ route('eod.n2.pdf', $fiche) }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter PDF
            </a>
            @endif
            <a href="{{ route('eod.n3.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>
    </div>

    <!-- Informations générales -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Informations générales</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Date traitement</p>
                    <p class="font-medium">{{ $fiche->date_traitement->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Heure lancement</p>
                    <p class="font-medium">{{ $fiche->heure_lancement }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Heure fin</p>
                    <p class="font-medium">{{ $fiche->heure_fin ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Statut global</p>
                    <p class="font-medium">{{ $fiche->statut_global ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Créateur</p>
                    <p class="font-medium">{{ $fiche->creator?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Validateur</p>
                    <p class="font-medium">{{ $fiche->validator?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Date validation</p>
                    <p class="font-medium">{{ $fiche->validated_at ? $fiche->validated_at->format('d/m/Y H:i') : '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Responsable suivi</p>
                    <p class="font-medium">{{ $fiche->responsable_suivi ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sauvegarde -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Avant traitement</h3>
            </div>
            <div class="p-4">
                <dl class="divide-y">
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-gray-600">Incrémental</dt>
                        <dd class="text-sm font-medium">{{ $fiche->sauvegarde_avant_incremental ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-gray-600">Différentiel</dt>
                        <dd class="text-sm font-medium">{{ $fiche->sauvegarde_avant_differentiel ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-gray-600">Complet</dt>
                        <dd class="text-sm font-medium">{{ $fiche->sauvegarde_avant_complet ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-gray-600">Heure</dt>
                        <dd class="text-sm font-medium">{{ $fiche->sauvegarde_avant_heure ?: '—' }}</dd>
                    </div>
                    <div class="py-2">
                        <dt class="text-sm text-gray-600 mb-1">Observations</dt>
                        <dd class="text-sm bg-gray-50 p-2 rounded">{{ $fiche->sauvegarde_avant_observation ?: '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Après traitement</h3>
            </div>
            <div class="p-4">
                <dl class="divide-y">
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-gray-600">Incrémental</dt>
                        <dd class="text-sm font-medium">{{ $fiche->sauvegarde_apres_incremental ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-gray-600">Différentiel</dt>
                        <dd class="text-sm font-medium">{{ $fiche->sauvegarde_apres_differentiel ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-gray-600">Complet</dt>
                        <dd class="text-sm font-medium">{{ $fiche->sauvegarde_apres_complet ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between py-2">
                        <dt class="text-sm text-gray-600">Heure</dt>
                        <dd class="text-sm font-medium">{{ $fiche->sauvegarde_apres_heure ?: '—' }}</dd>
                    </div>
                    <div class="py-2">
                        <dt class="text-sm text-gray-600 mb-1">Observations</dt>
                        <dd class="text-sm bg-gray-50 p-2 rounded">{{ $fiche->sauvegarde_apres_observation ?: '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- FLEX-BD -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">FLEX-BD</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><span class="text-xs text-gray-500 block">Avant Inc:</span> {{ $fiche->nafa_bd_avant_incremental ?: '—' }}</div>
                <div><span class="text-xs text-gray-500 block">Avant Diff:</span> {{ $fiche->nafa_bd_avant_differentiel ?: '—' }}</div>
                <div><span class="text-xs text-gray-500 block">Avant Complet:</span> {{ $fiche->nafa_bd_avant_complet ?: '—' }}</div>
                <div><span class="text-xs text-gray-500 block">Après Inc:</span> {{ $fiche->nafa_bd_apres_incremental ?: '—' }}</div>
                <div><span class="text-xs text-gray-500 block">Après Diff:</span> {{ $fiche->nafa_bd_apres_differentiel ?: '—' }}</div>
                <div><span class="text-xs text-gray-500 block">Après Complet:</span> {{ $fiche->nafa_bd_apres_complet ?: '—' }}</div>
                <div><span class="text-xs text-gray-500 block">Heure:</span> {{ $fiche->nafa_bd_heure ?: '—' }}</div>
                <div class="col-span-2"><span class="text-xs text-gray-500 block">Observations:</span> {{ $fiche->nafa_bd_observation ?: '—' }}</div>
            </div>
        </div>
    </div>

    <!-- Batch -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Traitements batch</h2>
        </div>
        <div class="p-6">
            @if($batchData && count($batchData) > 0)
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Batch</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Début</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Fin</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Observation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batchData as $batch)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $batch['batch'] ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $batch['debut'] ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $batch['fin'] ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $batch['observation'] ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-center py-4">Aucun batch renseigné</p>
            @endif
        </div>
    </div>

    <!-- Incidents -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Incidents observés</h2>
        </div>
        <div class="p-6">
            @if($incidentsData && count($incidentsData) > 0)
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Heure</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Incident</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Impact</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Action</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incidentsData as $incident)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $incident['heure'] ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $incident['incident'] ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $incident['impact'] ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $incident['action'] ?? '—' }}</td>
                            <td class="px-4 py-2">
                                @if(($incident['statut'] ?? '') == 'Résolu')
                                    <span class="text-green-600">✓ Résolu</span>
                                @elseif(($incident['statut'] ?? '') == 'En cours')
                                    <span class="text-yellow-600">⏳ En cours</span>
                                @else
                                    <span class="text-red-600">✗ Non résolu</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-center py-4">Aucun incident signalé</p>
            @endif
        </div>
    </div>

    <!-- Émargement et validation -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Émargement</h3>
            </div>
            <div class="p-4">
                <p class="text-sm bg-gray-50 p-3 rounded">{{ $fiche->emargement ?: '—' }}</p>
                <p class="mt-2 text-sm"><span class="text-gray-600">Responsable:</span> {{ $fiche->responsable_batch ?: '—' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Validation</h3>
            </div>
            <div class="p-4">
                @if($fiche->status === 'VALIDATED')
                    <div class="space-y-3">
                        <p><span class="text-gray-600">Head IT:</span> {{ $fiche->validation_head_it_visa ?: '—' }} ({{ $fiche->validation_head_it_date ?: '—' }})</p>
                        <p><span class="text-gray-600">Direction Audit:</span> {{ $fiche->validation_audit_visa ?: '—' }} ({{ $fiche->validation_audit_date ?: '—' }})</p>
                        @if($fiche->validation_note)
                            <p class="text-sm bg-green-50 p-2 rounded">{{ $fiche->validation_note }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-gray-500">Non validée</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Historique -->
    @if($fiche->history && count($fiche->history) > 0)
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Historique des actions</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($fiche->history as $h)
                <div class="flex">
                    <div class="flex-shrink-0 mr-4">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2"
                             style="background: {{ $h['role'] === 'N1' ? 'rgba(59,130,246,0.1)' : ($h['role'] === 'N2' ? 'rgba(16,185,129,0.1)' : 'rgba(139,92,246,0.1)') }}; 
                                    border-color: {{ $h['role'] === 'N1' ? '#3b82f6' : ($h['role'] === 'N2' ? '#10b981' : '#8b5cf6') }};
                                    color: {{ $h['role'] === 'N1' ? '#3b82f6' : ($h['role'] === 'N2' ? '#10b981' : '#8b5cf6') }};">
                            {{ $h['role'] }}
                        </div>
                    </div>
                    <div class="flex-1 pb-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900">{{ $h['action'] }}</p>
                            <span class="text-xs text-gray-500">{{ $h['at'] }}</span>
                        </div>
                        @if(!empty($h['note']))
                            <p class="text-sm text-gray-600 mt-1 italic">"{{ $h['note'] }}"</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Boutons finaux -->
    <div class="flex justify-between items-center mt-8">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
        
        @if($fiche->status === 'VALIDATED')
        <a href="{{ route('eod.n2.pdf', $fiche) }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Télécharger le rapport PDF
        </a>
        @endif
    </div>
</div>
@endsection