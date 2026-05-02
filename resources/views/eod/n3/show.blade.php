{{-- resources/views/eod/n3/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détail EOD - ' . $fiche->reference)
@section('header', 'Supervision EOD - Détail de la fiche')

@section('content')
@php
    $u = auth()->user();
    $canSignEodCtrlSlot = $u->canSignEodControllerSlot()
        && ! $fiche->controller_validated_at
        && in_array($fiche->status, ['PENDING_N3_CONTROLLER', 'PENDING_CONTROLLER'], true);
    $showControllerSlotReadOnly = $u->canAccessEodAsN3()
        && ! $u->canSignEodControllerSlot()
        && ! $fiche->controller_validated_at
        && in_array($fiche->status, ['PENDING_N3_CONTROLLER', 'PENDING_CONTROLLER'], true);
    $eodBackRoute = $u->eodSidebarShowsN3Section() ? 'eod.n3.index' : 'eod.controller.index';
@endphp
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
            @if(in_array($fiche->status, ['CLOSED', 'VALIDATED'], true))
            <a href="{{ route('eod.n2.pdf', $fiche) }}" target="_blank" class="bg-[#C8102E] hover:bg-[#a00d24] text-white font-semibold py-2 px-4 rounded-lg transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter PDF
            </a>
            @endif
            <a href="{{ route($eodBackRoute) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors inline-flex items-center">
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
                    <p class="font-medium">{{ trim(($fiche->creator?->prenom ?? '') . ' ' . ($fiche->creator?->name ?? '')) ?: '—' }}</p>
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

    <!-- Pièces jointes -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Pièces jointes</h2>
        </div>
        <div class="p-6">
            @php
                $existingAttachments = is_array($fiche->attachments ?? null) ? $fiche->attachments : [];
            @endphp
            @if(count($existingAttachments) > 0)
                <div class="space-y-2">
                    @foreach($existingAttachments as $att)
                        <a href="{{ asset('storage/' . ($att['path'] ?? '')) }}" target="_blank" class="block p-3 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-sm">
                            <span class="font-medium text-gray-800">{{ $att['name'] ?? 'Fichier joint' }}</span>
                            @if(!empty($att['uploaded_at']))
                                <span class="text-gray-500"> — {{ $att['uploaded_at'] }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Aucune pièce jointe.</p>
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
                <p class="mt-2 text-sm"><span class="text-gray-600">Responsable batch :</span> {{ $fiche->responsable_batch ?: '—' }}</p>
                @if($fiche->emargement_signature_path)
                    <p class="mt-2 text-xs text-gray-500">Signature émargement</p>
                    <img src="{{ asset('storage/'.$fiche->emargement_signature_path) }}" alt="" class="mt-1 max-h-28 rounded border border-gray-200">
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Signatures N+3 &amp; Controller</h3>
            </div>
            <div class="p-4 space-y-3 text-sm">
                @if($fiche->n3_validated_at)
                    <p><span class="text-gray-600">N+3 :</span> {{ trim(($fiche->n3Validator?->prenom ?? '') . ' ' . ($fiche->n3Validator?->name ?? '')) ?: '—' }}
                        — {{ $fiche->n3_validation_date ?? $fiche->n3_validated_at->format('d/m/Y H:i') }}</p>
                    @if($fiche->n3_signature_path)
                        <img src="{{ asset('storage/'.$fiche->n3_signature_path) }}" alt="Signature N+3" class="max-h-24 rounded border border-gray-200">
                    @endif
                @else
                    <p class="text-amber-800">Signature N+3 en attente.</p>
                @endif
                <hr class="border-gray-100">
                @if($fiche->controller_validated_at)
                    <p><span class="text-gray-600">Controller :</span> {{ trim(($fiche->controllerValidator?->prenom ?? '') . ' ' . ($fiche->controllerValidator?->name ?? '')) ?: '—' }}
                        — {{ $fiche->controller_validation_date ?? $fiche->controller_validated_at->format('d/m/Y H:i') }}</p>
                    @if($fiche->controller_signature_path)
                        <img src="{{ asset('storage/'.$fiche->controller_signature_path) }}" alt="Signature Controller" class="max-h-24 rounded border border-gray-200">
                    @elseif($fiche->controller_validation_visa)
                        <p class="text-gray-700">Visa : {{ $fiche->controller_validation_visa }}</p>
                    @endif
                @else
                    <p class="text-amber-800">Signature Controller en attente.</p>
                    @if($canSignEodCtrlSlot)
                        <a href="{{ route('eod.controller.edit', $fiche) }}" class="mt-3 inline-flex items-center px-4 py-2 bg-[#C8102E] hover:bg-[#a00d24] text-white text-sm font-semibold rounded-lg shadow-sm">
                            Signer en tant que Controller
                        </a>
                    @elseif($showControllerSlotReadOnly)
                        <div class="mt-3 rounded-lg border border-dashed border-gray-300 bg-gray-100/90 px-4 py-3 text-sm text-gray-500 pointer-events-none select-none opacity-90">
                            <p class="font-medium text-gray-600">Réservé au Contrôleur EOD (batch)</p>
                            <p class="mt-1">Seuls les comptes <strong>Contrôleur EOD (batch)</strong> ou la désignation <strong>Controller — validation batch EOD</strong> peuvent signer cette case.</p>
                        </div>
                    @endif
                @endif
                @if($fiche->status === 'VALIDATED')
                    <p class="text-xs text-gray-500 pt-2">Ancien flux (validation N+2 + Controller seul) — détails Head IT / Audit ci-dessous si renseignés.</p>
                    <p><span class="text-gray-600">Head IT:</span> {{ $fiche->validation_head_it_visa ?: '—' }}</p>
                    <p><span class="text-gray-600">Direction Audit:</span> {{ $fiche->validation_audit_visa ?: '—' }}</p>
                @endif
            </div>
        </div>
    </div>

    @if($fiche->status === 'PENDING_N3_CONTROLLER' && !$fiche->n3_validated_at)
    <div class="bg-white rounded-xl shadow-md border border-[#C8102E]/25 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-[#C8102E] to-[#4a4a4a] px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-white">Votre signature N+3</h2>
            <p class="text-sm text-red-100 mt-1">Signataire connecté : <strong>{{ trim((auth()->user()->prenom ?? '') . ' ' . (auth()->user()->name ?? '')) }}</strong></p>
        </div>
        <div class="p-6">
            @if(session('success'))<div class="mb-4 text-green-700 text-sm">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="mb-4 text-red-700 text-sm">{{ session('error') }}</div>@endif
            <form method="POST" action="{{ route('eod.n3.sign', $fiche) }}" enctype="multipart/form-data" class="space-y-4" id="n3-sign-form">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Date</label>
                        <input type="text" name="n3_validation_date" value="{{ old('n3_validation_date', date('d/m/Y')) }}" required class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Importer une signature (image)</label>
                        <input type="file" name="n3_signature_file" accept="image/*" class="block w-full text-sm text-gray-600">
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Ou signer ci-dessous</label>
                    <div class="border border-gray-300 rounded-lg bg-white overflow-hidden max-w-lg">
                        <canvas id="n3-sig-canvas" width="480" height="160" class="w-full touch-none cursor-crosshair" style="max-height:160px;"></canvas>
                    </div>
                    <input type="hidden" name="n3_signature_canvas" id="n3_signature_canvas" value="">
                    <button type="button" id="n3-sig-clear" class="mt-2 px-3 py-1.5 text-xs bg-gray-200 rounded-lg">Effacer</button>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Note (optionnel)</label>
                    <textarea name="n3_validation_note" rows="2" class="w-full rounded-lg border-gray-300 text-sm">{{ old('n3_validation_note') }}</textarea>
                </div>
                <button type="submit" class="px-5 py-2 bg-[#C8102E] hover:bg-[#a00d24] text-white rounded-lg text-sm font-semibold">Enregistrer la signature N+3</button>
            </form>
        </div>
    </div>
    @endif

    @if($canSignEodCtrlSlot)
    <div class="bg-white rounded-xl shadow-md border-2 border-[#C8102E]/25 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-[#C8102E] to-[#4a4a4a] px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-white">Votre signature Controller</h2>
            <p class="text-sm text-red-100 mt-1">
                @if($fiche->n3_validated_at)
                    La signature N+3 est enregistrée. Vous pouvez finaliser votre visa sur le formulaire dédié.
                @else
                    Vous pouvez signer en parallèle de N+3 ; la fiche sera clôturée lorsque les deux signatures seront complètes.
                @endif
            </p>
        </div>
        <div class="p-6 flex flex-wrap items-center gap-4">
            <a href="{{ route('eod.controller.edit', $fiche) }}" class="inline-flex items-center px-6 py-3 bg-[#C8102E] hover:bg-[#a00d24] text-white font-semibold rounded-lg transition-colors shadow">
                Ouvrir le formulaire de signature Controller
            </a>
            <span class="text-sm text-gray-500">Même contenu que la page « Validation Controller » pour cette fiche.</span>
        </div>
    </div>
    @elseif($showControllerSlotReadOnly)
    <div class="bg-gray-100 rounded-xl shadow-inner border border-gray-200 overflow-hidden mb-6 opacity-95 pointer-events-none select-none">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-200/80">
            <h2 class="text-lg font-semibold text-gray-600">Signature Controller</h2>
            <p class="text-sm text-gray-500 mt-1">Réservée au profil Contrôleur EOD — non disponible pour un compte N+3 ou Super Admin sans désignation Controller.</p>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-500">Seuls les comptes <strong>Contrôleur EOD (batch)</strong> ou <strong>Controller — validation batch EOD</strong> peuvent ouvrir le formulaire de signature.</p>
        </div>
    </div>
    @endif

    <!-- Historique -->
    @if($fiche->history && count($fiche->history) > 0)
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-[#C8102E] to-[#4a4a4a] px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-white">Historique des actions</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($fiche->history as $h)
                <div class="flex">
                    <div class="flex-shrink-0 mr-4">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2"
                             style="background: {{ $h['role'] === 'N1' ? 'rgba(200,16,46,0.08)' : ($h['role'] === 'N2' ? 'rgba(74,74,74,0.08)' : 'rgba(200,16,46,0.12)') }}; 
                                    border-color: {{ $h['role'] === 'N1' ? '#C8102E' : ($h['role'] === 'N2' ? '#4a4a4a' : '#a00d24') }};
                                    color: {{ $h['role'] === 'N1' ? '#C8102E' : ($h['role'] === 'N2' ? '#4a4a4a' : '#a00d24') }};">
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
        
        @if(in_array($fiche->status, ['CLOSED', 'VALIDATED'], true))
        <a href="{{ route('eod.n2.pdf', $fiche) }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-[#C8102E] hover:bg-[#a00d24] text-white font-semibold rounded-lg transition-colors shadow-lg hover:shadow-xl">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Télécharger le rapport PDF
        </a>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const canvas = document.getElementById('n3-sig-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let drawing = false;
    function pos(ev) {
        const r = canvas.getBoundingClientRect();
        const x = (ev.touches ? ev.touches[0].clientX : ev.clientX) - r.left;
        const y = (ev.touches ? ev.touches[0].clientY : ev.clientY) - r.top;
        return { x, y };
    }
    function start(ev) { drawing = true; ctx.beginPath(); const p = pos(ev); ctx.moveTo(p.x, p.y); ev.preventDefault(); }
    function move(ev) {
        if (!drawing) return;
        const p = pos(ev);
        ctx.strokeStyle = '#111'; ctx.lineWidth = 2; ctx.lineCap = 'round';
        ctx.lineTo(p.x, p.y); ctx.stroke();
        ev.preventDefault();
    }
    function end() { drawing = false; }
    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    window.addEventListener('mouseup', end);
    canvas.addEventListener('touchstart', start, { passive: false });
    canvas.addEventListener('touchmove', move, { passive: false });
    canvas.addEventListener('touchend', end);
    document.getElementById('n3-sig-clear')?.addEventListener('click', function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById('n3_signature_canvas').value = '';
    });
    document.getElementById('n3-sign-form')?.addEventListener('submit', function() {
        const blank = document.createElement('canvas');
        blank.width = canvas.width; blank.height = canvas.height;
        if (canvas.toDataURL() !== blank.toDataURL()) {
            document.getElementById('n3_signature_canvas').value = canvas.toDataURL('image/png');
        }
    });
})();
</script>
@endpush