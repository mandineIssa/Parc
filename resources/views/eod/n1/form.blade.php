{{-- resources/views/eod/n1/form.blade.php --}}
@extends('layouts.app')

@section('title', isset($fiche) ? 'Fiche EOD du ' . $fiche->date_traitement->format('d/m/Y') : 'Nouvelle fiche EOD')
@section('header', 'Suivi EOD - N+1')

@section('content')
<style scoped>
/* Styles isolés pour le formulaire EOD */
:root {
    /* Charte graphique COFINA (PDF EOD) */
    --cofina-red: #C8102E;
    --cofina-red-dark: #a00d24;
    --cofina-grey: #4a4a4a;
    --navy: #4a4a4a;
    --blue: #C8102E;
    --blue-light: #a00d24;
    --bg: #f3f4f6;
    --bg-row: #f0f0f0;
    --bg-sub: #e5e5e5;
    --border: #d1d5db;
    --border-light: #e5e7eb;
    --green: #16a34a;
    --green-dark: #15803d;
    --red: #b91c1c;
    --orange: #ca8a04;
    --text-dark: #1a1a1a;
    --text-mid: #333;
    --text-light: #4a4a4a;
    --text-gray: #6b7280;
}

/* Style tableau professionnel */
.eod-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
    margin-bottom: 8px;
}

.eod-table th {
    background: var(--blue);
    color: #fff;
    font-weight: 700;
    padding: 6px 8px;
    border: 1px solid var(--navy);
    font-size: 10px;
    text-transform: uppercase;
    text-align: center;
}

.eod-table th.eod-sub {
    background: var(--bg-sub);
    color: var(--navy);
    font-size: 9px;
    font-weight: 700;
}

.eod-table th.eod-group-header {
    background: var(--blue-light);
    color: #fff;
    font-size: 10px;
    text-align: center;
    padding: 4px;
}

.eod-table td {
    padding: 6px 8px;
    border: 1px solid var(--border);
    vertical-align: middle;
    background: #fff;
}

.eod-table td.eod-label {
    font-weight: 700;
    background: var(--bg-row);
    color: var(--navy);
    text-align: left;
    white-space: nowrap;
}

.eod-table td.eod-write-lg {
    height: 40px;
}

.eod-table td.eod-write-xl {
    height: 55px;
}

/* Inputs dans les tableaux */
.eod-table input,
.eod-table select {
    width: 100%;
    border: none;
    background: transparent;
    font-size: 10.5px;
    color: var(--text-dark);
    outline: none;
    padding: 2px;
}

.eod-table input:focus,
.eod-table select:focus {
    background: #fff9e6;
}

/* En-tête COFINA */
.cofina-header {
    border: 2px solid var(--cofina-red);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 20px;
}

.cofina-header .header-top {
    display: flex;
    align-items: stretch;
}

.cofina-header .logo {
    background: var(--cofina-red);
    color: #fff;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 100px;
    font-size: 24px;
    font-weight: 900;
    letter-spacing: 1px;
}

.cofina-header .title {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 12px 20px;
    border-left: 2px solid var(--cofina-red);
}

.cofina-header .title h2 {
    font-size: 15px;
    font-weight: 800;
    color: var(--cofina-red);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 0;
    text-align: center;
}

.cofina-header .title p {
    font-size: 10px;
    color: var(--text-light);
    margin-top: 2px;
}

.cofina-header .role-badge {
    background: var(--cofina-grey);
    color: #fff;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 68px;
    border-left: 2px solid var(--cofina-red);
    font-weight: 900;
    font-size: 13px;
}

.cofina-header .info-grid {
    border-top: 2px solid var(--cofina-red);
    padding: 10px 16px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 30px;
    background: #fafafa;
    font-size: 11px;
}

.cofina-header .info-row {
    display: flex;
    gap: 5px;
    align-items: baseline;
}

.cofina-header .info-label {
    font-weight: 700;
    color: var(--text-dark);
    white-space: nowrap;
    min-width: 95px;
}

.cofina-header .responsable {
    border-top: 1px solid var(--border-light);
    padding: 6px 16px;
    font-size: 11px;
    color: var(--text-mid);
    background: #fafafa;
    font-style: italic;
}

/* Statut succès */
.statut-success {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.statut-success .check {
    display: inline-block;
    width: 14px;
    height: 14px;
    background: var(--green);
    border-radius: 2px;
    text-align: center;
    line-height: 14px;
    color: #fff;
    font-size: 9px;
    font-weight: 900;
}

/* Boutons */
.eod-btn-remove {
    background: var(--red);
    color: #fff;
    border: none;
    border-radius: 3px;
    width: 20px;
    height: 20px;
    cursor: pointer;
    font-weight: 900;
    font-size: 13px;
    line-height: 20px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.eod-btn-remove:hover {
    opacity: 0.8;
}

.eod-btn-add {
    background: var(--blue-light);
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 8px 16px;
    font-weight: 700;
    font-size: 11px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: opacity 0.15s;
}

.eod-btn-add:hover {
    opacity: 0.82;
}

.eod-btn-add svg {
    width: 16px;
    height: 16px;
}

/* Badge statut incident */
.badge-statut {
    font-weight: 700;
    font-size: 10px;
}

.badge-statut.resolu {
    color: var(--green);
}

.badge-statut.en-cours {
    color: var(--orange);
}

.badge-statut.non-resolu {
    color: var(--red);
}

/* Section EOD */
.eod-section-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    overflow: hidden;
    margin-bottom: 20px;
}

.eod-section-header {
    background: linear-gradient(to right, #C8102E, #4a4a4a);
    padding: 12px 20px;
    border-bottom: none;
}

.eod-section-header h3 {
    font-size: 14px;
    font-weight: 600;
    color: #ffffff;
    margin: 0;
}

.eod-section-body {
    padding: 20px;
}
</style>

<div class="container mx-auto px-4 py-8 eod-form-container">
    <!-- En-tête avec navigation -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-[#C8102E]">
                {{ isset($fiche) ? 'Fiche du ' . $fiche->date_traitement->format('d/m/Y') : 'Nouvelle fiche de suivi EOD' }}
            </h1>
            @if(isset($fiche))
                <p class="text-gray-600 mt-2">
                    {{ $fiche->reference }} · 
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $fiche->status_class }}">
                        {{ $fiche->status_label }}
                    </span>
                </p>
            @else
                <p class="text-gray-600 mt-2">Saisissez les informations du traitement de fin de journée</p>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(isset($fiche) && $fiche->status === 'PENDING_N3_CONTROLLER')
        <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-900 px-4 py-3 rounded-lg text-sm">
            Cette fiche est en attente des signatures <strong>N+3</strong> et <strong>Controller</strong>. Vous ne pouvez plus la modifier.
        </div>
    @endif

    @if(isset($fiche) && in_array($fiche->status, ['CLOSED', 'VALIDATED'], true))
        <div class="mb-6 flex flex-wrap gap-3 justify-end">
            <a href="{{ route('eod.n2.pdf', $fiche) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-[#C8102E] hover:bg-[#a00d24] text-white text-sm font-semibold rounded-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Télécharger le PDF
            </a>
        </div>
    @endif

    @if(isset($fiche) && $fiche->status === 'REJECTED')
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            ⚠️ Cette fiche a été rejetée par N+2.
            @if($fiche->history && count($fiche->history) > 0)
                @php
                    $lastReject = collect($fiche->history)->where('action', 'Fiche rejetée')->last();
                @endphp
                @if($lastReject && !empty($lastReject['note']))
                    <br><strong>Motif :</strong> {{ $lastReject['note'] }}
                @endif
            @endif
        </div>
    @endif

    @php
        $eodRoutePrefix = $eodRoutePrefix ?? 'eod.n1';
        $isEditable = !isset($fiche) || $fiche->status === 'DRAFT' || $fiche->status === 'REJECTED';
        $isReadOnly = isset($fiche) && $fiche->status === 'PENDING_N3_CONTROLLER';
        $isValidated = isset($fiche) && in_array($fiche->status, ['CLOSED', 'VALIDATED'], true);
        $user = auth()->user();
        $responsableBatchDisplay = isset($fiche)
            ? ($fiche->responsable_batch ?? '')
            : trim(($user->prenom ?? '') . ' ' . ($user->name ?? ''));
    @endphp

    <!-- Formulaire principal -->
    <form method="POST" 
          action="{{ isset($fiche) ? route($eodRoutePrefix . '.update', $fiche) : route($eodRoutePrefix . '.store') }}" 
          id="eod-form"
          enctype="multipart/form-data"
          class="space-y-6">
        @csrf
        @if(isset($fiche))
            @method('PUT')
        @endif

        <!-- COFINA HEADER -->
        <div class="cofina-header">
            <div class="header-top">
                <div class="logo">COFINA</div>
                <div class="title">
                    <h2>Fiche de Suivi — Traitement de Fin de Journée</h2>
                    <p>Oracle FLEXCUBE Core Banking · Document interne</p>
                </div>
                <div class="role-badge">{{ trim(($user->prenom ?? '') . ' ' . ($user->name ?? '')) }}</div>
            </div>
            <div class="info-grid">
                <div class="info-row">
                    <span class="info-label">Institution :</span>
                    <span>COFINA</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Heure de lancement EOD :</span>
                    <span>
                        <input type="text" name="heure_lancement" 
                               value="{{ old('heure_lancement', $fiche->heure_lancement ?? now()->format('H:i')) }}"
                               {{ !$isEditable ? 'disabled' : '' }}
                               style="border: none; background: transparent; width: auto; font-size: 11px; color: var(--text-dark); outline: none; padding: 0;">
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Système :</span>
                    <span>Oracle FLEXCUBE Core Banking</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Heure de fin EOD :</span>
                    <span>
                        <input type="text" name="heure_fin" 
                               value="{{ old('heure_fin', $fiche->heure_fin ?? now()->format('H:i')) }}"
                               {{ !$isEditable ? 'disabled' : '' }}
                               style="border: none; background: transparent; width: auto; font-size: 11px; color: var(--text-dark); outline: none; padding: 0;" 
                               placeholder="—">
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date du traitement :</span>
                    <span>
                        <input type="date" name="date_traitement" 
                               value="{{ old('date_traitement', isset($fiche) && $fiche->date_traitement ? $fiche->date_traitement->format('Y-m-d') : now()->format('Y-m-d')) }}"
                               {{ !$isEditable ? 'disabled' : '' }}
                               style="border: none; background: transparent; width: auto; font-size: 11px; color: var(--text-dark); outline: none; padding: 0;">
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Statut global :</span>
                    <span>
                        <select name="statut_global" {{ !$isEditable ? 'disabled' : '' }}
                                style="border: none; background: transparent; font-size: 11px; color: var(--text-dark); outline: none; padding: 0;">
                            <option value="">—</option>
                            <option value="Succès" {{ (old('statut_global', $fiche->statut_global ?? '') == 'Succès') ? 'selected' : '' }}>Succès</option>
                            <option value="Échec" {{ (old('statut_global', $fiche->statut_global ?? '') == 'Échec') ? 'selected' : '' }}>Échec</option>
                            <option value="Partiel" {{ (old('statut_global', $fiche->statut_global ?? '') == 'Partiel') ? 'selected' : '' }}>Partiel</option>
                        </select>
                    </span>
                </div>
            </div>
            <div class="responsable">
                <strong>Responsable suivi :</strong> 
                <input type="text" name="responsable_suivi" 
                       value="{{ old('responsable_suivi', $fiche->responsable_suivi ?? 'Service IT – Exploitation') }}"
                       {{ !$isEditable ? 'disabled' : '' }}
                       style="border: none; background: transparent; font-size: 11px; color: var(--text-mid); outline: none; padding: 0; font-style: italic; width: auto;">
            </div>
        </div>

        <!-- 1. SAUVEGARDE - NAFA-BD -->
        <div class="eod-section-card">
            <div class="eod-section-header">
                <h3>1. Sauvegarde — FLEX-BD</h3>
            </div>
            <div class="eod-section-body">
                <table class="eod-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width:12%">Type</th>
                            <th colspan="3" class="eod-group-header">Avant traitement</th>
                            <th colspan="3" class="eod-group-header">Après traitement</th>
                            <th rowspan="2" style="width:8%">Heure</th>
                            <th rowspan="2" style="width:20%">Observations</th>
                        </tr>
                        <tr>
                            <th class="eod-sub">INCR.</th>
                            <th class="eod-sub">DIFF.</th>
                            <th class="eod-sub">COMP.</th>
                            <th class="eod-sub">INCR.</th>
                            <th class="eod-sub">DIFF.</th>
                            <th class="eod-sub">COMP.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="eod-label">FLEX-BD</td>
                            <td><input type="text" name="nafa_bd_avant_incremental" value="{{ old('nafa_bd_avant_incremental', $fiche->nafa_bd_avant_incremental ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="nafa_bd_avant_differentiel" value="{{ old('nafa_bd_avant_differentiel', $fiche->nafa_bd_avant_differentiel ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="nafa_bd_avant_complet" value="{{ old('nafa_bd_avant_complet', $fiche->nafa_bd_avant_complet ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="nafa_bd_apres_incremental" value="{{ old('nafa_bd_apres_incremental', $fiche->nafa_bd_apres_incremental ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="nafa_bd_apres_differentiel" value="{{ old('nafa_bd_apres_differentiel', $fiche->nafa_bd_apres_differentiel ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="nafa_bd_apres_complet" value="{{ old('nafa_bd_apres_complet', $fiche->nafa_bd_apres_complet ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="nafa_bd_heure" value="{{ old('nafa_bd_heure', $fiche->nafa_bd_heure ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="nafa_bd_observation" value="{{ old('nafa_bd_observation', $fiche->nafa_bd_observation ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sauvegarde générale -->
        <div class="eod-section-card">
            <div class="eod-section-header">
                <h3>Sauvegarde générale</h3>
            </div>
            <div class="eod-section-body">
                <table class="eod-table">
                    <thead>
                        <tr>
                            <th style="width:16%">Période</th>
                            <th style="width:15%">Incrémental</th>
                            <th style="width:15%">Différentiel</th>
                            <th style="width:15%">Complet</th>
                            <th style="width:10%">Heure</th>
                            <th>Observations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="eod-label">Avant traitement</td>
                            <td><input type="text" name="sauvegarde_avant_incremental" value="{{ old('sauvegarde_avant_incremental', $fiche->sauvegarde_avant_incremental ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="sauvegarde_avant_differentiel" value="{{ old('sauvegarde_avant_differentiel', $fiche->sauvegarde_avant_differentiel ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="sauvegarde_avant_complet" value="{{ old('sauvegarde_avant_complet', $fiche->sauvegarde_avant_complet ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="sauvegarde_avant_heure" value="{{ old('sauvegarde_avant_heure', $fiche->sauvegarde_avant_heure ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="sauvegarde_avant_observation" value="{{ old('sauvegarde_avant_observation', $fiche->sauvegarde_avant_observation ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                        </tr>
                        <tr>
                            <td class="eod-label">Après traitement</td>
                            <td><input type="text" name="sauvegarde_apres_incremental" value="{{ old('sauvegarde_apres_incremental', $fiche->sauvegarde_apres_incremental ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="sauvegarde_apres_differentiel" value="{{ old('sauvegarde_apres_differentiel', $fiche->sauvegarde_apres_differentiel ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="sauvegarde_apres_complet" value="{{ old('sauvegarde_apres_complet', $fiche->sauvegarde_apres_complet ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="sauvegarde_apres_heure" value="{{ old('sauvegarde_apres_heure', $fiche->sauvegarde_apres_heure ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="sauvegarde_apres_observation" value="{{ old('sauvegarde_apres_observation', $fiche->sauvegarde_apres_observation ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. TRAITEMENT - Batch -->
        <div class="eod-section-card">
            <div class="eod-section-header">
                <h3>2. Traitement Batch</h3>
            </div>
            <div class="eod-section-body">
                <input type="hidden" name="batch_data" id="batch_data_json">
                
                @php
                    $batchData = isset($batchData) ? $batchData : [];
                    $batchData = collect($batchData)->filter(function ($batch) {
                        return trim((string) ($batch['batch'] ?? '')) !== ''
                            || trim((string) ($batch['debut'] ?? '')) !== ''
                            || trim((string) ($batch['fin'] ?? '')) !== ''
                            || trim((string) ($batch['observation'] ?? '')) !== '';
                    })->values()->all();
                @endphp
                <div id="batch-container" class="space-y-4">
                    @forelse($batchData as $index => $batch)
                    <div class="batch-item grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg relative">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Batch</label>
                            <input type="text" data-batch="batch"
                                   value="{{ $batch['batch'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E] {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Heure début</label>
                            <input type="text" data-batch="debut"
                                   value="{{ $batch['debut'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E] {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Heure fin</label>
                            <input type="text" data-batch="fin"
                                   value="{{ $batch['fin'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E] {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-600 mb-1">Observation</label>
                            <input type="text" data-batch="observation"
                                   value="{{ $batch['observation'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E] {{ !$isEditable ? 'bg-gray-100' : '' }}">
                            @if($isEditable && $index > 0)
                            <button type="button" class="absolute -right-2 -top-2 eod-btn-remove" onclick="this.closest('.batch-item').remove()">
                                ×
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    @endforelse
                </div>
                
                @if($isEditable)
                <button type="button" onclick="addBatchRow()" class="eod-btn-add mt-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter un batch
                </button>
                <p class="text-xs text-gray-500 mt-2">Cliquez sur "Ajouter un batch" uniquement si nécessaire.</p>
                @endif
            </div>
        </div>

        <!-- 3. ÉMARGEMENT -->
        <div class="eod-section-card">
            <div class="eod-section-header">
                <h3>3. Émargement</h3>
            </div>
            <div class="eod-section-body space-y-4">
                <p class="text-xs text-gray-500">Note libre, signature manuscrite (canvas) et/ou photo importée. Le responsable batch est celui du compte connecté (mis à jour à l’enregistrement).</p>
                <input type="hidden" name="emargement" value="{{ old('emargement', isset($fiche) ? ($fiche->emargement ?? '') : '') }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Responsable batch</label>
                    <input type="text" readonly
                           value="{{ $responsableBatchDisplay }}"
                           class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-800 shadow-sm max-w-xl">
                </div>
                @if($isEditable)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Signature (canvas)</label>
                        <div class="border border-gray-300 rounded-lg bg-white overflow-hidden">
                            <canvas id="emargement-canvas" width="440" height="160" class="w-full touch-none cursor-crosshair" style="max-height:160px;"></canvas>
                        </div>
                        <input type="hidden" name="emargement_signature_canvas" id="emargement_signature_canvas" value="">
                        <div class="flex gap-2 mt-2">
                            <button type="button" id="emargement-clear" class="px-3 py-1.5 text-xs bg-gray-200 rounded-lg">Effacer</button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ou importer une photo / scan</label>
                        <input type="file" name="emargement_signature_file" accept="image/*" capture="environment"
                               class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-red-50 file:text-[#C8102E]">
                    </div>
                </div>
                @elseif(isset($fiche) && $fiche->emargement_signature_path)
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-1">Signature enregistrée</p>
                    <img src="{{ asset('storage/'.$fiche->emargement_signature_path) }}" alt="Signature émargement" class="max-h-32 rounded border border-gray-200">
                </div>
                @endif
            </div>
        </div>

        <!-- 4. INCIDENTS OBSERVÉS -->
        <div class="eod-section-card">
            <div class="eod-section-header">
                <h3>4. Incidents Observés</h3>
            </div>
            <div class="eod-section-body">
                <input type="hidden" name="incidents_data" id="incidents_data_json">

                @php
                    $incidentsData = isset($incidentsData) ? $incidentsData : [];
                    $incidentsData = collect($incidentsData)->filter(function ($incident) {
                        return trim((string) ($incident['heure'] ?? '')) !== ''
                            || trim((string) ($incident['incident'] ?? '')) !== ''
                            || trim((string) ($incident['impact'] ?? '')) !== ''
                            || trim((string) ($incident['action'] ?? '')) !== ''
                            || trim((string) ($incident['statut'] ?? '')) !== '';
                    })->values()->all();
                @endphp
                <div id="incidents-container" class="space-y-4">
                    @forelse($incidentsData as $index => $incident)
                    <div class="incident-item grid grid-cols-1 md:grid-cols-5 gap-4 p-4 bg-gray-50 rounded-lg relative">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Heure</label>
                            <input type="text" data-incident="heure"
                                   value="{{ $incident['heure'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E] {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Incident</label>
                            <input type="text" data-incident="incident"
                                   value="{{ $incident['incident'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E] {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Impact</label>
                            <input type="text" data-incident="impact"
                                   value="{{ $incident['impact'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E] {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Action corrective</label>
                            <input type="text" data-incident="action"
                                   value="{{ $incident['action'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E] {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-600 mb-1">Statut</label>
                            <select data-incident="statut" {{ !$isEditable ? 'disabled' : '' }}
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E] {{ !$isEditable ? 'bg-gray-100' : '' }}">
                                <option value="">Sélectionner</option>
                                <option value="Résolu" {{ ($incident['statut'] ?? '') == 'Résolu' ? 'selected' : '' }}>Résolu</option>
                                <option value="En cours" {{ ($incident['statut'] ?? '') == 'En cours' ? 'selected' : '' }}>En cours</option>
                                <option value="Non résolu" {{ ($incident['statut'] ?? '') == 'Non résolu' ? 'selected' : '' }}>Non résolu</option>
                            </select>
                            @if($isEditable && $index > 0)
                            <button type="button" class="absolute -right-2 -top-2 eod-btn-remove" onclick="this.closest('.incident-item').remove()">
                                ×
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    @endforelse
                </div>
                
                @if($isEditable)
                <button type="button" onclick="addIncidentRow()" class="eod-btn-add mt-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter un incident
                </button>
                <p class="text-xs text-gray-500 mt-2">Cliquez sur "Ajouter un incident" uniquement si nécessaire.</p>
                @endif
            </div>
        </div>

        <!-- 5. PIÈCES JOINTES -->
        <div class="eod-section-card">
            <div class="eod-section-header">
                <h3>5. Pièces jointes</h3>
            </div>
            <div class="eod-section-body space-y-4">
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
                    <p class="text-sm text-gray-500">Aucune pièce jointe pour le moment.</p>
                @endif

                @if($isEditable)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ajouter des fichiers</label>
                        <input type="file" name="eod_attachments_files[]" multiple
                               class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-red-50 file:text-[#C8102E]">
                        <p class="text-xs text-gray-500 mt-1">Formats: PDF, Word, Excel, CSV, TXT, JPG, PNG (max 10 Mo par fichier).</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex justify-end space-x-3 pt-4">
            <a href="{{ route('eod.n1.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
            
            @if($isEditable)
                <button type="submit" id="btn-save" class="px-6 py-2 bg-[#C8102E] hover:bg-[#a00d24] text-white font-semibold rounded-lg transition-colors inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Save
                </button>
            @endif
            
            @if($isEditable && isset($fiche))
                <button type="button" onclick="submitToN3Controller()" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Soumettre à N+3 et Controller
                </button>
            @endif
        </div>
    </form>

    @if(isset($fiche))
    <form id="submit-n3-form" method="POST" action="{{ route($eodRoutePrefix . '.submit', $fiche) }}" style="display:none;">
        @csrf
    </form>
    @endif

    <!-- Bouton de retour au dashboard -->
    <div class="mt-8">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Retour au Dashboard
        </a>
    </div>
</div>

@push('scripts')
<script>
// ==================== SERIALISATION JSON ====================

function serializeFormData() {
    // Batch
    const batchItems = document.querySelectorAll('.batch-item');
    const batchData = Array.from(batchItems).map(item => ({
        batch:       item.querySelector('[data-batch="batch"]')?.value || '',
        debut:       item.querySelector('[data-batch="debut"]')?.value || '',
        fin:         item.querySelector('[data-batch="fin"]')?.value || '',
        observation: item.querySelector('[data-batch="observation"]')?.value || ''
    }));
    document.getElementById('batch_data_json').value = JSON.stringify(batchData);

    // Incidents
    const incidentItems = document.querySelectorAll('.incident-item');
    const incidentsData = Array.from(incidentItems).map(item => ({
        heure:    item.querySelector('[data-incident="heure"]')?.value || '',
        incident: item.querySelector('[data-incident="incident"]')?.value || '',
        impact:   item.querySelector('[data-incident="impact"]')?.value || '',
        action:   item.querySelector('[data-incident="action"]')?.value || '',
        statut:   item.querySelector('[data-incident="statut"]')?.value || ''
    }));
    document.getElementById('incidents_data_json').value = JSON.stringify(incidentsData);
}

document.getElementById('eod-form').addEventListener('submit', function(e) {
    serializeFormData();
    flushEmargementCanvas();
});

(function initEmargementCanvas() {
    const canvas = document.getElementById('emargement-canvas');
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
        ctx.strokeStyle = '#111';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
        ev.preventDefault();
    }
    function end() { drawing = false; }
    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    window.addEventListener('mouseup', end);
    canvas.addEventListener('touchstart', start, { passive: false });
    canvas.addEventListener('touchmove', move, { passive: false });
    canvas.addEventListener('touchend', end);
    document.getElementById('emargement-clear')?.addEventListener('click', function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById('emargement_signature_canvas').value = '';
    });
})();

function flushEmargementCanvas() {
    const canvas = document.getElementById('emargement-canvas');
    const hid = document.getElementById('emargement_signature_canvas');
    if (canvas && hid && canvas.getContext) {
        const blank = document.createElement('canvas');
        blank.width = canvas.width;
        blank.height = canvas.height;
        if (canvas.toDataURL() !== blank.toDataURL()) {
            hid.value = canvas.toDataURL('image/png');
        }
    }
}

// ==================== AJOUT DE LIGNES ====================

function addBatchRow() {
    const container = document.getElementById('batch-container');
    const div = document.createElement('div');
    div.className = 'batch-item grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg relative';
    div.innerHTML = `
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Batch</label>
            <input type="text" data-batch="batch" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E]">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Heure début</label>
            <input type="text" data-batch="debut" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E]">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Heure fin</label>
            <input type="text" data-batch="fin" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E]">
        </div>
        <div class="relative">
            <label class="block text-sm font-medium text-gray-600 mb-1">Observation</label>
            <input type="text" data-batch="observation" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E]">
            <button type="button" class="absolute -right-2 -top-2 eod-btn-remove" onclick="this.closest('.batch-item').remove()">×</button>
        </div>
    `;
    container.appendChild(div);
}

function addIncidentRow() {
    const container = document.getElementById('incidents-container');
    const div = document.createElement('div');
    div.className = 'incident-item grid grid-cols-1 md:grid-cols-5 gap-4 p-4 bg-gray-50 rounded-lg relative';
    div.innerHTML = `
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Heure</label>
            <input type="text" data-incident="heure" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E]">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Incident</label>
            <input type="text" data-incident="incident" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E]">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Impact</label>
            <input type="text" data-incident="impact" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E]">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Action corrective</label>
            <input type="text" data-incident="action" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E]">
        </div>
        <div class="relative">
            <label class="block text-sm font-medium text-gray-600 mb-1">Statut</label>
            <select data-incident="statut" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E]">
                <option value="">Sélectionner</option>
                <option value="Résolu">Résolu</option>
                <option value="En cours">En cours</option>
                <option value="Non résolu">Non résolu</option>
            </select>
            <button type="button" class="absolute -right-2 -top-2 eod-btn-remove" onclick="this.closest('.incident-item').remove()">×</button>
        </div>
    `;
    container.appendChild(div);
}

// ==================== SOUMISSION N+3 + CONTROLLER ====================

function submitToN3Controller() {
    serializeFormData();
    flushEmargementCanvas();
    const doSubmit = function() {
        document.getElementById('submit-n3-form').submit();
    };
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Soumettre à N+3 et Controller ?',
            text: 'La fiche sera transmise pour les deux signatures. Vous ne pourrez plus la modifier tant qu’elle n’est pas renvoyée en rejet.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Oui, soumettre',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) doSubmit();
        });
    } else {
        if (confirm('Soumettre cette fiche à N+3 et au Controller ?')) doSubmit();
    }
}
</script>
@endpush
@endsection