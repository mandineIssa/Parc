{{-- resources/views/eod/n1/form.blade.php --}}
@extends('layouts.app')

@section('title', isset($fiche) ? 'Fiche EOD du ' . $fiche->date_traitement->format('d/m/Y') : 'Nouvelle fiche EOD')
@section('header', 'Suivi EOD - N+1')

@section('content')
<style>
:root {
    --navy: #1a3a6b;
    --blue: #2b5396;
    --blue-light: #3d6bb5;
    --bg: #f0f2f5;
    --bg-row: #eef4fb;
    --bg-sub: #d6e4f0;
    --border: #b0bed0;
    --border-light: #c5d5e8;
    --green: #22a752;
    --green-dark: #16a34a;
    --red: #dc2626;
    --orange: #f39c12;
    --text-dark: #1a1a1a;
    --text-mid: #333;
    --text-light: #555;
    --text-gray: #888;
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

.eod-table th.sub {
    background: var(--bg-sub);
    color: var(--navy);
    font-size: 9px;
    font-weight: 700;
}

.eod-table th.group-header {
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

.eod-table td.label {
    font-weight: 700;
    background: var(--bg-row);
    color: var(--navy);
    text-align: left;
    white-space: nowrap;
}

.eod-table td.write-lg {
    height: 40px;
}

.eod-table td.write-xl {
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
    border: 2px solid var(--navy);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 20px;
}

.cofina-header .header-top {
    display: flex;
    align-items: stretch;
}

.cofina-header .logo {
    background: var(--navy);
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
    border-left: 2px solid var(--navy);
}

.cofina-header .title h2 {
    font-size: 15px;
    font-weight: 800;
    color: var(--navy);
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
    background: var(--blue-light);
    color: #fff;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 68px;
    border-left: 2px solid var(--navy);
    font-weight: 900;
    font-size: 13px;
}

.cofina-header .info-grid {
    border-top: 2px solid var(--navy);
    padding: 10px 16px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 30px;
    background: #f7faff;
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
    background: #f7faff;
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
.btn-remove {
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

.btn-remove:hover {
    opacity: 0.8;
}

.btn-add {
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

.btn-add:hover {
    opacity: 0.82;
}

.btn-add svg {
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

/* Section */
.section-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    overflow: hidden;
    margin-bottom: 20px;
}

.section-header {
    background: linear-gradient(to right, #f9fafb, #f3f4f6);
    padding: 12px 20px;
    border-bottom: 1px solid #e5e7eb;
}

.section-header h3 {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.section-body {
    padding: 20px;
}
</style>

<div class="container mx-auto px-4 py-8">
    <!-- En-tête avec navigation -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
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
        $isEditable = !isset($fiche) || $fiche->status === 'DRAFT' || $fiche->status === 'REJECTED';
        $isReadOnly = isset($fiche) && $fiche->status === 'PENDING_N2';
        $isValidated = isset($fiche) && $fiche->status === 'VALIDATED';
        $user = auth()->user();
    @endphp

    <!-- Formulaire principal -->
    <form method="POST" 
          action="{{ isset($fiche) ? route('eod.n1.update', $fiche) : route('eod.n1.store') }}" 
          id="eod-form"
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
                <div class="role-badge">{{ $user->name }} {{ $user->prenom }}</div>
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
                               value="{{ old('heure_lancement', $fiche->heure_lancement ?? '22h00') }}"
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
                               value="{{ old('heure_fin', $fiche->heure_fin ?? '') }}"
                               {{ !$isEditable ? 'disabled' : '' }}
                               style="border: none; background: transparent; width: auto; font-size: 11px; color: var(--text-dark); outline: none; padding: 0;" 
                               placeholder="—">
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date du traitement :</span>
                    <span>
                        <input type="date" name="date_traitement" 
                               value="{{ old('date_traitement', isset($fiche) && $fiche->date_traitement ? $fiche->date_traitement->format('Y-m-d') : date('Y-m-d')) }}"
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
        <div class="section-card">
            <div class="section-header">
                <h3>1. Sauvegarde — FLEX-BD</h3>
            </div>
            <div class="section-body">
                <table class="eod-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width:12%">Type</th>
                            <th colspan="3" class="group-header">Avant traitement</th>
                            <th colspan="3" class="group-header">Après traitement</th>
                            <th rowspan="2" style="width:8%">Heure</th>
                            <th rowspan="2" style="width:20%">Observations</th>
                        </tr>
                        <tr>
                            <th class="sub">INCR.</th>
                            <th class="sub">DIFF.</th>
                            <th class="sub">COMP.</th>
                            <th class="sub">INCR.</th>
                            <th class="sub">DIFF.</th>
                            <th class="sub">COMP.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="label">FLEX-BD</td>
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
        <div class="section-card">
            <div class="section-header">
                <h3>Sauvegarde générale</h3>
            </div>
            <div class="section-body">
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
                            <td class="label">Avant traitement</td>
                            <td><input type="text" name="sauvegarde_avant_incremental" value="{{ old('sauvegarde_avant_incremental', $fiche->sauvegarde_avant_incremental ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="sauvegarde_avant_differentiel" value="{{ old('sauvegarde_avant_differentiel', $fiche->sauvegarde_avant_differentiel ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="sauvegarde_avant_complet" value="{{ old('sauvegarde_avant_complet', $fiche->sauvegarde_avant_complet ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="sauvegarde_avant_heure" value="{{ old('sauvegarde_avant_heure', $fiche->sauvegarde_avant_heure ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                            <td><input type="text" name="sauvegarde_avant_observation" value="{{ old('sauvegarde_avant_observation', $fiche->sauvegarde_avant_observation ?? '') }}" {{ !$isEditable ? 'disabled' : '' }}></td>
                        </tr>
                        <tr>
                            <td class="label">Après traitement</td>
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
        <div class="section-card">
            <div class="section-header">
                <h3>2. Traitement Batch</h3>
            </div>
            <div class="section-body">
                <input type="hidden" name="batch_data" id="batch_data_json">
                
                <div id="batch-container" class="space-y-4">
                    @php
                        $batchData = isset($batchData) ? $batchData : [['batch' => '', 'debut' => '', 'fin' => '', 'observation' => '']];
                    @endphp
                    @foreach($batchData as $index => $batch)
                    <div class="batch-item grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg relative">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Batch</label>
                            <input type="text" data-batch="batch"
                                   value="{{ $batch['batch'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Heure début</label>
                            <input type="text" data-batch="debut"
                                   value="{{ $batch['debut'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Heure fin</label>
                            <input type="text" data-batch="fin"
                                   value="{{ $batch['fin'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-600 mb-1">Observation</label>
                            <input type="text" data-batch="observation"
                                   value="{{ $batch['observation'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-100' : '' }}">
                            @if($isEditable && $index > 0)
                            <button type="button" class="absolute -right-2 -top-2 btn-remove" onclick="this.closest('.batch-item').remove()">
                                ×
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($isEditable)
                <button type="button" onclick="addBatchRow()" class="btn-add mt-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter un batch
                </button>
                @endif
            </div>
        </div>

        <!-- 3. ÉMARGEMENT -->
        <div class="section-card">
            <div class="section-header">
                <h3>3. Émargement</h3>
            </div>
            <div class="section-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Émargement</label>
                        <textarea name="emargement" rows="3"
                                  {{ !$isEditable ? 'disabled' : '' }}
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-100' : '' }}">{{ old('emargement', $fiche->emargement ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Responsable Batch</label>
                        <input type="text" name="responsable_batch" 
                               value="{{ old('responsable_batch', $fiche->responsable_batch ?? '') }}"
                               {{ !$isEditable ? 'disabled' : '' }}
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-100' : '' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. INCIDENTS OBSERVÉS -->
        <div class="section-card">
            <div class="section-header">
                <h3>4. Incidents Observés</h3>
            </div>
            <div class="section-body">
                <input type="hidden" name="incidents_data" id="incidents_data_json">

                <div id="incidents-container" class="space-y-4">
                    @php
                        $incidentsData = isset($incidentsData) ? $incidentsData : [['heure' => '', 'incident' => '', 'impact' => '', 'action' => '', 'statut' => '']];
                    @endphp
                    @foreach($incidentsData as $index => $incident)
                    <div class="incident-item grid grid-cols-1 md:grid-cols-5 gap-4 p-4 bg-gray-50 rounded-lg relative">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Heure</label>
                            <input type="text" data-incident="heure"
                                   value="{{ $incident['heure'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Incident</label>
                            <input type="text" data-incident="incident"
                                   value="{{ $incident['incident'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Impact</label>
                            <input type="text" data-incident="impact"
                                   value="{{ $incident['impact'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Action corrective</label>
                            <input type="text" data-incident="action"
                                   value="{{ $incident['action'] ?? '' }}"
                                   {{ !$isEditable ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-100' : '' }}">
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-600 mb-1">Statut</label>
                            <select data-incident="statut" {{ !$isEditable ? 'disabled' : '' }}
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-100' : '' }}">
                                <option value="">Sélectionner</option>
                                <option value="Résolu" {{ ($incident['statut'] ?? '') == 'Résolu' ? 'selected' : '' }}>Résolu</option>
                                <option value="En cours" {{ ($incident['statut'] ?? '') == 'En cours' ? 'selected' : '' }}>En cours</option>
                                <option value="Non résolu" {{ ($incident['statut'] ?? '') == 'Non résolu' ? 'selected' : '' }}>Non résolu</option>
                            </select>
                            @if($isEditable && $index > 0)
                            <button type="button" class="absolute -right-2 -top-2 btn-remove" onclick="this.closest('.incident-item').remove()">
                                ×
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @if($isEditable)
                <button type="button" onclick="addIncidentRow()" class="btn-add mt-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter un incident
                </button>
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
                <button type="submit" id="btn-save" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Sauvegarder brouillon
                </button>
            @endif
            
            @if($isEditable && isset($fiche))
                <button type="button" onclick="submitToN2()" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Soumettre à N+2
                </button>
            @endif
        </div>
    </form>

    @if(isset($fiche))
    <form id="submit-n2-form" method="POST" action="{{ route('eod.n1.submit', $fiche) }}" style="display:none;">
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
});

// ==================== AJOUT DE LIGNES ====================

function addBatchRow() {
    const container = document.getElementById('batch-container');
    const div = document.createElement('div');
    div.className = 'batch-item grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg relative';
    div.innerHTML = `
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Batch</label>
            <input type="text" data-batch="batch" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Heure début</label>
            <input type="text" data-batch="debut" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Heure fin</label>
            <input type="text" data-batch="fin" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div class="relative">
            <label class="block text-sm font-medium text-gray-600 mb-1">Observation</label>
            <input type="text" data-batch="observation" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="button" class="absolute -right-2 -top-2 btn-remove" onclick="this.closest('.batch-item').remove()">×</button>
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
            <input type="text" data-incident="heure" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Incident</label>
            <input type="text" data-incident="incident" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Impact</label>
            <input type="text" data-incident="impact" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Action corrective</label>
            <input type="text" data-incident="action" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div class="relative">
            <label class="block text-sm font-medium text-gray-600 mb-1">Statut</label>
            <select data-incident="statut" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Sélectionner</option>
                <option value="Résolu">Résolu</option>
                <option value="En cours">En cours</option>
                <option value="Non résolu">Non résolu</option>
            </select>
            <button type="button" class="absolute -right-2 -top-2 btn-remove" onclick="this.closest('.incident-item').remove()">×</button>
        </div>
    `;
    container.appendChild(div);
}

// ==================== SOUMISSION À N+2 ====================

function submitToN2() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Soumettre à N+2 ?',
            text: 'La fiche sera transmise pour validation. Vous ne pourrez plus la modifier.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Oui, soumettre',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('submit-n2-form').submit();
            }
        });
    } else {
        if (confirm('Soumettre cette fiche à N+2 pour validation ?')) {
            document.getElementById('submit-n2-form').submit();
        }
    }
}
</script>
@endpush
@endsection