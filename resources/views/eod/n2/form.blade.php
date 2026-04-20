{{-- resources/views/eod/n2/form.blade.php --}}
@extends('layouts.app')

@section('title', 'Validation EOD - ' . $fiche->date_traitement->format('d/m/Y'))
@section('header', 'Suivi EOD - N+2')

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
    font-size: 10.5px;
}

.eod-table td.label {
    font-weight: 700;
    background: var(--bg-row);
    color: var(--navy);
    text-align: left;
    white-space: nowrap;
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
    background: var(--green-dark);
    color: #fff;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 120px;
    border-left: 2px solid var(--navy);
    font-weight: 600;
    font-size: 13px;
    white-space: nowrap;
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

.section-card.readonly {
    background: #f9fafb;
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

/* Grille d'information */
.info-grid-compact {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.info-item {
    background: #f9fafb;
    padding: 12px;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}

.info-item .label {
    font-size: 11px;
    color: #6b7280;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-item .value {
    font-size: 13px;
    font-weight: 500;
    color: #1f2937;
}

/* Tableaux en lecture seule */
.table-readonly {
    width: 100%;
    border-collapse: collapse;
}

.table-readonly th {
    background: #e5e7eb;
    color: #374151;
    font-weight: 600;
    padding: 8px 10px;
    font-size: 11px;
    text-align: left;
    border: 1px solid #d1d5db;
}

.table-readonly td {
    padding: 8px 10px;
    border: 1px solid #d1d5db;
    font-size: 11px;
    background: #fff;
}

/* Bloc validation */
.validation-block {
    border: 2px solid var(--green-dark);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 20px;
}

.validation-header {
    background: linear-gradient(135deg, var(--green-dark), #15803d);
    padding: 14px 20px;
}

.validation-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: #fff;
    margin: 0;
}

.validation-body {
    padding: 20px;
    background: #fff;
}

.signature-box {
    border: 1.5px solid var(--navy);
    border-radius: 6px;
    overflow: hidden;
}

.signature-header {
    background: var(--blue);
    color: #fff;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    padding: 8px 12px;
    text-align: center;
}

.signature-row {
    display: flex;
    border-top: 1px solid var(--border);
}

.signature-label {
    font-weight: 600;
    color: var(--navy);
    background: var(--bg-row);
    font-size: 11px;
    padding: 10px 12px;
    border-right: 1px solid var(--border);
    min-width: 60px;
}

.signature-value {
    flex: 1;
    padding: 8px 12px;
}

.signature-value input {
    width: 100%;
    border: none;
    background: transparent;
    font-size: 11px;
    outline: none;
    padding: 2px;
}

.signature-value input:focus {
    background: #fff9e6;
}
</style>

@php
    $user = auth()->user();
    $userName = $user->name . ' ' . $user->prenom;
@endphp

<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Validation - Fiche du {{ $fiche->date_traitement->format('d/m/Y') }}</h1>
            <p class="text-gray-600 mt-2">
                {{ $fiche->reference }} · 
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $fiche->status_class }}">
                    {{ $fiche->status_label }}
                </span>
            </p>
        </div>
        @if($fiche->status === 'VALIDATED')
        <a href="{{ route('eod.n2.pdf', $fiche) }}" target="_blank"
           class="mt-4 md:mt-0 px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Télécharger PDF
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- COFINA HEADER avec nom de l'utilisateur connecté -->
    <div class="cofina-header">
        <div class="header-top">
            <div class="logo">COFINA</div>
            <div class="title">
                <h2>Fiche de Suivi — Traitement de Fin de Journée</h2>
                <p>Oracle FLEXCUBE Core Banking · Document interne</p>
            </div>
            <div class="role-badge" title="Validateur connecté">
                {{ $userName }}
            </div>
        </div>
        <div class="info-grid">
            <div class="info-row">
                <span class="info-label">Institution :</span>
                <span>COFINA</span>
            </div>
            <div class="info-row">
                <span class="info-label">Heure de lancement EOD :</span>
                <span>{{ $fiche->heure_lancement ?? '22h00' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Système :</span>
                <span>Oracle FLEXCUBE Core Banking</span>
            </div>
            <div class="info-row">
                <span class="info-label">Heure de fin EOD :</span>
                <span>{{ $fiche->heure_fin ?: '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date du traitement :</span>
                <span>{{ $fiche->date_traitement->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Statut global :</span>
                @if($fiche->statut_global == 'Succès')
                    <span class="statut-success">
                        <span class="check">✓</span> Succès
                    </span>
                @else
                    <span>{{ $fiche->statut_global ?: '—' }}</span>
                @endif
            </div>
        </div>
        <div class="responsable">
            <strong>Responsable suivi :</strong> {{ $fiche->responsable_suivi ?? 'Service IT – Exploitation' }}
        </div>
    </div>

    <!-- État d'attente -->
    @if($fiche->status === 'PENDING_N2')
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-amber-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-amber-800 font-medium">En attente de validation N+2</p>
                <p class="text-sm text-amber-700">Soumis par {{ $fiche->creator?->name ?? 'N+1' }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- 1. SAUVEGARDE - NAFA-BD -->
    <div class="section-card readonly">
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
                        <td>{{ $fiche->nafa_bd_avant_incremental ?: '' }}</td>
                        <td>{{ $fiche->nafa_bd_avant_differentiel ?: '' }}</td>
                        <td>{{ $fiche->nafa_bd_avant_complet ?: '' }}</td>
                        <td>{{ $fiche->nafa_bd_apres_incremental ?: '' }}</td>
                        <td>{{ $fiche->nafa_bd_apres_differentiel ?: '' }}</td>
                        <td>{{ $fiche->nafa_bd_apres_complet ?: '' }}</td>
                        <td>{{ $fiche->nafa_bd_heure ?: '' }}</td>
                        <td>{{ $fiche->nafa_bd_observation ?: '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sauvegarde générale -->
    <div class="section-card readonly">
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
                        <td>{{ $fiche->sauvegarde_avant_incremental ?: '' }}</td>
                        <td>{{ $fiche->sauvegarde_avant_differentiel ?: '' }}</td>
                        <td>{{ $fiche->sauvegarde_avant_complet ?: '' }}</td>
                        <td>{{ $fiche->sauvegarde_avant_heure ?: '' }}</td>
                        <td>{{ $fiche->sauvegarde_avant_observation ?: '' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Après traitement</td>
                        <td>{{ $fiche->sauvegarde_apres_incremental ?: '' }}</td>
                        <td>{{ $fiche->sauvegarde_apres_differentiel ?: '' }}</td>
                        <td>{{ $fiche->sauvegarde_apres_complet ?: '' }}</td>
                        <td>{{ $fiche->sauvegarde_apres_heure ?: '' }}</td>
                        <td>{{ $fiche->sauvegarde_apres_observation ?: '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 2. TRAITEMENT - Batch -->
    <div class="section-card readonly">
        <div class="section-header">
            <h3>2. Traitement Batch</h3>
        </div>
        <div class="section-body">
            @if($batchData && count($batchData) > 0)
                <table class="table-readonly">
                    <thead>
                        <tr>
                            <th>Batch</th>
                            <th>Heure début</th>
                            <th>Heure fin</th>
                            <th>Observations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batchData as $batch)
                        <tr>
                            <td>{{ $batch['batch'] ?? '—' }}</td>
                            <td>{{ $batch['debut'] ?? '—' }}</td>
                            <td>{{ $batch['fin'] ?? '—' }}</td>
                            <td>{{ $batch['observation'] ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-center py-4 italic">Aucun batch renseigné</p>
            @endif
        </div>
    </div>

    <!-- 3. ÉMARGEMENT -->
    <div class="section-card readonly">
        <div class="section-header">
            <h3>3. Émargement</h3>
        </div>
        <div class="section-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-sm font-medium text-gray-600 mb-1">Émargement</div>
                    <div class="bg-white p-3 rounded-lg border border-gray-200 min-h-[60px]">{{ $fiche->emargement ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-600 mb-1">Responsable Batch</div>
                    <div class="bg-white p-3 rounded-lg border border-gray-200">{{ $fiche->responsable_batch ?: '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. INCIDENTS OBSERVÉS -->
    <div class="section-card readonly">
        <div class="section-header">
            <h3>4. Incidents Observés</h3>
        </div>
        <div class="section-body">
            @if($incidentsData && count($incidentsData) > 0)
                <table class="table-readonly">
                    <thead>
                        <tr>
                            <th>Heure</th>
                            <th>Incident</th>
                            <th>Impact</th>
                            <th>Action corrective</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incidentsData as $incident)
                        <tr>
                            <td>{{ $incident['heure'] ?? '—' }}</td>
                            <td>{{ $incident['incident'] ?? '—' }}</td>
                            <td>{{ $incident['impact'] ?? '—' }}</td>
                            <td>{{ $incident['action'] ?? '—' }}</td>
                            <td>
                                @if(($incident['statut'] ?? '') == 'Résolu')
                                    <span class="text-green-600 font-semibold">✓ Résolu</span>
                                @elseif(($incident['statut'] ?? '') == 'En cours')
                                    <span class="text-amber-600">⏳ En cours</span>
                                @elseif(($incident['statut'] ?? '') == 'Non résolu')
                                    <span class="text-red-600">✗ Non résolu</span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-center py-4 italic">Aucun incident signalé</p>
            @endif
        </div>
    </div>

    <!-- 5. VÉRIFICATION / VALIDATION -->
    @if($fiche->status === 'PENDING_N2')
    <div class="validation-block">
        <div class="validation-header">
            <h3>5. Vérification / Validation</h3>
        </div>
        <div class="validation-body">
            <form method="POST" action="{{ route('eod.n2.validate', $fiche) }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Head IT -->
                    <div class="signature-box">
                        <div class="signature-header">Head IT</div>
                        <div class="signature-row">
                            <div class="signature-label">Date</div>
                            <div class="signature-value">
                                <input type="text" name="validation_head_it_date" value="{{ date('d/m/Y') }}" placeholder="JJ/MM/AAAA">
                            </div>
                        </div>
                        <div class="signature-row">
                            <div class="signature-label">Visa</div>
                            <div class="signature-value">
                                <input type="text" name="validation_head_it_visa" placeholder="Nom / Signature">
                            </div>
                        </div>
                    </div>

                    <!-- Direction Audit -->
                    <div class="signature-box">
                        <div class="signature-header">Direction Audit</div>
                        <div class="signature-row">
                            <div class="signature-label">Date</div>
                            <div class="signature-value">
                                <input type="text" name="validation_audit_date" value="{{ date('d/m/Y') }}" placeholder="JJ/MM/AAAA">
                            </div>
                        </div>
                        <div class="signature-row">
                            <div class="signature-label">Visa</div>
                            <div class="signature-value">
                                <input type="text" name="validation_audit_visa" placeholder="Nom / Signature">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Note de validation</label>
                    <textarea name="validation_note" rows="3" 
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                              placeholder="Observations, commentaires..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="showRejectModal()"
                            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Rejeter
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Valider la fiche
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- État VALIDÉ -->
    @if($fiche->status === 'VALIDATED')
    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-start">
                <svg class="w-8 h-8 text-green-600 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-xl font-bold text-green-800">FICHE VALIDÉE</h3>
                    <p class="text-sm text-green-700 mt-1">
                        Validée le {{ $fiche->validated_at ? $fiche->validated_at->format('d/m/Y H:i') : '' }}
                    </p>
                    @if($fiche->validation_note)
                        <p class="text-sm text-green-700 mt-3 bg-white p-3 rounded border border-green-200">
                            <strong>Note :</strong> {{ $fiche->validation_note }}
                        </p>
                    @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        @if($fiche->validation_head_it_date || $fiche->validation_head_it_visa)
                        <div class="bg-white p-3 rounded border border-green-200">
                            <h4 class="font-semibold text-green-800 mb-2">Head IT</h4>
                            <p class="text-sm">Date: {{ $fiche->validation_head_it_date ?: '—' }}</p>
                            <p class="text-sm">Visa: {{ $fiche->validation_head_it_visa ?: '—' }}</p>
                        </div>
                        @endif
                        @if($fiche->validation_audit_date || $fiche->validation_audit_visa)
                        <div class="bg-white p-3 rounded border border-green-200">
                            <h4 class="font-semibold text-green-800 mb-2">Direction Audit</h4>
                            <p class="text-sm">Date: {{ $fiche->validation_audit_date ?: '—' }}</p>
                            <p class="text-sm">Visa: {{ $fiche->validation_audit_visa ?: '—' }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('eod.n2.pdf', $fiche) }}" target="_blank"
               class="flex-shrink-0 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Télécharger PDF
            </a>
        </div>
    </div>
    @endif

    <!-- Boutons navigation -->
    <div class="flex justify-between pt-4">
        <a href="{{ route('eod.n2.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour à la liste
        </a>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>
    </div>
</div>

<!-- Modal Rejet -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 rounded-t-xl">
            <h3 class="text-lg font-semibold text-white">⚠️ Confirmer le rejet</h3>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('eod.n2.reject', $fiche) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motif du rejet</label>
                    <textarea name="rejet_note" rows="3" required
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                              placeholder="Veuillez préciser le motif du rejet..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideRejectModal()" 
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                        Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('pdf_auto'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.open("{{ route('eod.n2.pdf', $fiche) }}", '_blank');
    });
</script>
@endif

<style>
#rejectModal {
    backdrop-filter: blur(4px);
    transition: all 0.3s ease;
}
#rejectModal .bg-white {
    animation: modalSlideIn 0.3s ease-out;
}
@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-20px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

@push('scripts')
<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}
</script>
@endpush
@endsection