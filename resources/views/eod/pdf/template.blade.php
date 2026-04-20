{{-- resources/views/eod/pdf/template.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fiche EOD - {{ $fiche->reference ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            font-size: 8.5px;
            line-height: 1.35;
            color: #1e293b;
            background: #ffffff;
        }

        .page {
            width: 190mm;
            margin: 0 auto;
            padding: 5mm 0;
        }

        /*
         * ── PALETTE COFINA ──────────────────────────────
         *  Rouge principal : #C8102E
         *  Rouge foncé     : #a00d24
         *  Gris foncé      : #4a4a4a  (second. brand)
         *  Gris moyen      : #6b7280
         *  Gris clair bg   : #f0f0f0
         *  Border          : #d1d5db
         * ─────────────────────────────────────────────────
         */

        /* ── HEADER ── */
        .header {
            border: 1.5px solid #C8102E;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 4px;
        }

        .header-title {
            background: #C8102E;
            color: white;
            text-align: center;
            padding: 5px 10px 2px;
        }

        .header-title h1 {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 1px;
        }

        .header-title .sub {
            font-size: 7.5px;
            color: #fecdd3;
            font-weight: 400;
        }

        .header-brand {
            background: #4a4a4a;
            color: white;
            padding: 2px 10px;
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: 2px;
            text-align: right;
            text-transform: uppercase;
        }

        .meta-grid { display: table; width: 100%; border-collapse: collapse; }
        .meta-row  { display: table-row; }

        .meta-cell {
            display: table-cell;
            padding: 3px 8px;
            border: 0.4px solid #d1d5db;
            font-size: 8px;
            vertical-align: middle;
        }

        .meta-cell.label {
            background: #f0f0f0;
            font-weight: 700;
            color: #C8102E;
            width: 90px;
            border-right: 1.5px solid #d1d5db;
        }

        .status-bar { display: table; width: 100%; background: #fafafa; border: 0.4px solid #d1d5db; border-top: none; }
        .status-bar-inner { display: table-row; }

        .status-cell {
            display: table-cell;
            padding: 3px 8px;
            font-size: 8px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .status-cell .lbl { font-weight: 700; color: #4a4a4a; }
        .status-cell .val { color: #C8102E; font-weight: 600; }

        /* ── SECTIONS ── */
        .section {
            border: 1px solid #d1d5db;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 4px;
        }

        .section-head {
            background: #C8102E;
            color: white;
            padding: 4px 10px;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .section-head .num {
            display: inline-block;
            width: 14px; height: 14px;
            line-height: 14px;
            background: rgba(255,255,255,0.20);
            border-radius: 3px;
            text-align: center;
            font-size: 9px;
            margin-right: 6px;
            vertical-align: middle;
        }

        /* ── TABLES ── */
        table { width: 100%; border-collapse: collapse; font-size: 8px; }

        th {
            background: #f0f0f0;
            color: #4a4a4a;
            font-weight: 700;
            padding: 4px 5px;
            text-align: center;
            border: 0.5px solid #9ca3af;
            font-size: 7.5px;
            text-transform: uppercase;
        }

        td {
            padding: 4px 6px;
            border: 0.5px solid #d1d5db;
            color: #1e293b;
            vertical-align: middle;
        }

        .row-label {
            background: #f0f0f0;
            font-weight: 700;
            color: #C8102E;
            white-space: nowrap;
        }

        .sub-head td {
            background: #4a4a4a;
            color: white;
            font-weight: 700;
            text-align: center;
            padding: 3px 5px;
            font-size: 8px;
            border: none;
        }

        .text-center { text-align: center; }
        .text-left   { text-align: left; }
        .bold        { font-weight: 700; }

        .fill-row td    { height: 13px; }
        .fill-row-lg td { height: 22px; }

        /* ── SIGNATURES ── */
        .sig-grid {
            display: table;
            width: 100%;
            padding: 6px;
            background: #fafafa;
            border-collapse: separate;
            border-spacing: 6px;
        }

        .sig-row  { display: table-row; }

        .sig-cell {
            display: table-cell;
            width: 50%;
            padding: 3px;
            vertical-align: top;
        }

        .sig-card { border: 1px solid #d1d5db; border-radius: 3px; overflow: hidden; }

        .sig-card-head {
            background: #4a4a4a;
            color: white;
            padding: 4px 10px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8px;
        }

        .sig-card table td { height: 20px; }

        /* ── FOOTER ── */
        .footer {
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1.5px solid #C8102E;
            display: table;
            width: 100%;
            font-size: 7px;
            color: #6b7280;
        }

        .footer-l { display: table-cell; text-align: left;   vertical-align: middle; }
        .footer-c { display: table-cell; text-align: center; vertical-align: middle; }
        .footer-r { display: table-cell; text-align: right;  vertical-align: middle; }
        .footer-brand { color: #C8102E; font-weight: 700; font-size: 8px; letter-spacing: 1px; }
    </style>
</head>
<body>
<div class="page">

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-title">
            <h1>Fiche de Suivi &mdash; Traitement de Fin de Journ&eacute;e</h1>
            <div class="sub">{{ $fiche->systeme ?? 'Oracle FLEXCUBE Core Banking' }} &middot; Document interne</div>
        </div>
        <div class="header-brand">COFINA &mdash; Compagnie Financi&egrave;re Africaine</div>

        <div class="meta-grid">
            <div class="meta-row">
                <div class="meta-cell label">Institution :</div>
                <div class="meta-cell">{{ $fiche->institution ?? 'COFINA' }}</div>
                <div class="meta-cell label">Syst&egrave;me :</div>
                <div class="meta-cell">{{ $fiche->systeme ?? 'Oracle FLEXCUBE Core Banking' }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-cell label">Date du traitement :</div>
                <div class="meta-cell">{{ $fiche->date_traitement ? $fiche->date_traitement->format('d/m/Y') : '&nbsp;' }}</div>
                <div class="meta-cell label">Responsable suivi :</div>
                <div class="meta-cell">{{ $fiche->responsable_suivi ?? 'Service IT &ndash; Exploitation' }}</div>
            </div>
        </div>

        <div class="status-bar">
            <div class="status-bar-inner">
                <div class="status-cell">
                    <span class="lbl">Heure lancement EOD :</span>
                    <span class="val">&nbsp;{{ $fiche->heure_lancement ?? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' }}</span>
                </div>
                <div class="status-cell">
                    <span class="lbl">Heure de fin EOD :</span>
                    <span class="val">&nbsp;{{ $fiche->heure_fin ?? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' }}</span>
                </div>
                <div class="status-cell">
                    <span class="lbl">Statut global :</span>
                    @if(($fiche->statut_global ?? '') == 'Succès')
                        &nbsp;<span style="background:#dcfce7;color:#166534;border:1px solid #86efac;padding:1px 7px;border-radius:20px;font-weight:700;font-size:7.5px;">&#10003; Succ&egrave;s</span>
                    @elseif(($fiche->statut_global ?? '') == 'Avertissement')
                        &nbsp;<span style="background:#fef9c3;color:#854d0e;border:1px solid #fde047;padding:1px 7px;border-radius:20px;font-weight:700;font-size:7.5px;">&#9888; Avertissement</span>
                    @elseif(($fiche->statut_global ?? '') == 'Échec')
                        &nbsp;<span style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;padding:1px 7px;border-radius:20px;font-weight:700;font-size:7.5px;">&#10007; &Eacute;chec</span>
                    @else
                        &nbsp;<span style="font-size:8px;color:#4a4a4a;">&#9633; Succ&egrave;s &nbsp; &#9633; Avertissement &nbsp; &#9633; &Eacute;chec</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── SECTION 1 : SAUVEGARDE ── --}}
    <div class="section">
        <div class="section-head"><span class="num">1</span>Sauvegarde</div>

        <table>
            <tr class="sub-head"><td colspan="9">FLEX-BD</td></tr>
            <tr>
                <th rowspan="2">Type</th>
                <th colspan="3">Avant traitement</th>
                <th colspan="3">Apr&egrave;s traitement</th>
                <th rowspan="2">Heure</th>
                <th rowspan="2">Observations</th>
            </tr>
            <tr>
                <th>INCR.</th><th>DIFF.</th><th>COMP.</th>
                <th>INCR.</th><th>DIFF.</th><th>COMP.</th>
            </tr>
            <tr class="fill-row">
                <td class="row-label">FLEX-BD</td>
                <td class="text-center">{{ $fiche->nafa_bd_avant_incremental ?: '' }}</td>
                <td class="text-center">{{ $fiche->nafa_bd_avant_differentiel ?: '' }}</td>
                <td class="text-center">{{ $fiche->nafa_bd_avant_complet ?: '' }}</td>
                <td class="text-center">{{ $fiche->nafa_bd_apres_incremental ?: '' }}</td>
                <td class="text-center">{{ $fiche->nafa_bd_apres_differentiel ?: '' }}</td>
                <td class="text-center">{{ $fiche->nafa_bd_apres_complet ?: '' }}</td>
                <td class="text-center">{{ $fiche->nafa_bd_heure ?: '' }}</td>
                <td>{{ $fiche->nafa_bd_observation ?: '' }}</td>
            </tr>
        </table>

        <table>
            <tr class="sub-head"><td colspan="6">Sauvegarde g&eacute;n&eacute;rale</td></tr>
            <tr>
                <th>P&eacute;riode</th>
                <th>Incr&eacute;mental</th>
                <th>Diff&eacute;rentiel</th>
                <th>Complet</th>
                <th>Heure</th>
                <th>Observations</th>
            </tr>
            <tr class="fill-row">
                <td class="row-label">Avant traitement</td>
                <td class="text-center">{{ $fiche->sauvegarde_avant_incremental ?: '' }}</td>
                <td class="text-center">{{ $fiche->sauvegarde_avant_differentiel ?: '' }}</td>
                <td class="text-center">{{ $fiche->sauvegarde_avant_complet ?: '' }}</td>
                <td class="text-center">{{ $fiche->sauvegarde_avant_heure ?: '' }}</td>
                <td>{{ $fiche->sauvegarde_avant_observation ?: '' }}</td>
            </tr>
            <tr class="fill-row">
                <td class="row-label">Apr&egrave;s traitement</td>
                <td class="text-center">{{ $fiche->sauvegarde_apres_incremental ?: '' }}</td>
                <td class="text-center">{{ $fiche->sauvegarde_apres_differentiel ?: '' }}</td>
                <td class="text-center">{{ $fiche->sauvegarde_apres_complet ?: '' }}</td>
                <td class="text-center">{{ $fiche->sauvegarde_apres_heure ?: '' }}</td>
                <td>{{ $fiche->sauvegarde_apres_observation ?: '' }}</td>
            </tr>
        </table>
    </div>

    {{-- ── SECTION 2 : TRAITEMENT BATCH ── --}}
    <div class="section">
        <div class="section-head"><span class="num">2</span>Traitement Batch</div>
        <table>
            <thead>
                <tr>
                    <th class="text-left" style="width:38%">Batch / Traitement</th>
                    <th style="width:13%">Heure D&eacute;but</th>
                    <th style="width:13%">Heure Fin</th>
                    <th class="text-left">Observations</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batchData as $batch)
                <tr class="fill-row">
                    <td class="bold">{{ $batch['batch'] ?? '' }}</td>
                    <td class="text-center">{{ $batch['debut'] ?? '' }}</td>
                    <td class="text-center">{{ $batch['fin'] ?? '' }}</td>
                    <td>{{ $batch['observation'] ?? '' }}</td>
                </tr>
                @empty
                @for($i = 0; $i < 8; $i++)
                <tr class="fill-row"><td></td><td></td><td></td><td></td></tr>
                @endfor
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── SECTION 3 : ÉMARGEMENT ── --}}
    <div class="section">
        <div class="section-head"><span class="num">3</span>&Eacute;margement</div>
        <table>
            <tr>
                <th style="width:50%">&Eacute;margement</th>
                <th style="width:50%">Responsable Batch</th>
            </tr>
            <tr class="fill-row-lg">
                <td>{{ $fiche->emargement ?: '' }}</td>
                <td>{{ $fiche->responsable_batch ?: '' }}</td>
            </tr>
        </table>
    </div>

    {{-- ── SECTION 4 : INCIDENTS ── --}}
    <div class="section">
        <div class="section-head"><span class="num">4</span>Incidents Observ&eacute;s</div>
        <table>
            <thead>
                <tr>
                    <th style="width:10%">Heure</th>
                    <th class="text-left" style="width:28%">Incident</th>
                    <th style="width:12%">Impact</th>
                    <th class="text-left" style="width:32%">Action corrective</th>
                    <th style="width:12%">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidentsData as $incident)
                <tr class="fill-row">
                    <td class="text-center bold">{{ $incident['heure'] ?? '' }}</td>
                    <td>{{ $incident['incident'] ?? '' }}</td>
                    <td class="text-center">{{ $incident['impact'] ?? '' }}</td>
                    <td>{{ $incident['action'] ?? '' }}</td>
                    <td class="text-center">
                        @switch($incident['statut'] ?? '')
                            @case('Résolu')
                                <span style="background:#dcfce7;color:#166534;border:1px solid #86efac;padding:1px 6px;border-radius:20px;font-weight:700;font-size:7px;">R&eacute;solu</span>
                                @break
                            @case('En cours')
                                <span style="background:#fef9c3;color:#854d0e;border:1px solid #fde047;padding:1px 6px;border-radius:20px;font-weight:700;font-size:7px;">En cours</span>
                                @break
                            @case('Non résolu')
                                <span style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;padding:1px 6px;border-radius:20px;font-weight:700;font-size:7px;">Non r&eacute;solu</span>
                                @break
                            @default
                        @endswitch
                    </td>
                </tr>
                @empty
                @for($i = 0; $i < 3; $i++)
                <tr class="fill-row"><td></td><td></td><td></td><td></td><td></td></tr>
                @endfor
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── SECTION 5 : VALIDATION ── --}}
    <div class="section">
        <div class="section-head"><span class="num">5</span>V&eacute;rification / Validation</div>
        <div class="sig-grid">
            <div class="sig-row">

                <div class="sig-cell">
                    <div class="sig-card">
                        <div class="sig-card-head">Head IT</div>
                        <table>
                            <tr class="fill-row-lg">
                                <td class="row-label" style="width:30%">Date</td>
                                <td>{{ $fiche->validation_head_it_date ?: '' }}</td>
                            </tr>
                            <tr class="fill-row-lg">
                                <td class="row-label">Visa</td>
                                <td>{{ $fiche->validation_head_it_visa ?: '' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="sig-cell">
                    <div class="sig-card">
                        <div class="sig-card-head">Direction Audit</div>
                        <table>
                            <tr class="fill-row-lg">
                                <td class="row-label" style="width:30%">Date</td>
                                <td>{{ $fiche->validation_audit_date ?: '' }}</td>
                            </tr>
                            <tr class="fill-row-lg">
                                <td class="row-label">Visa</td>
                                <td>{{ $fiche->validation_audit_visa ?: '' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        @if(!empty($fiche->validation_note))
        <div style="margin:0 8px 8px;padding:6px 10px;background:#fff7ed;border:1px solid #fdba74;border-left:3px solid #C8102E;border-radius:3px;font-size:7.5px;color:#7c2d12;">
            <strong>Note :</strong> {{ $fiche->validation_note }}
        </div>
        @endif
    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-l">&#128274; Document confidentiel &mdash; Usage interne uniquement</div>
        <div class="footer-c">
            <span class="footer-brand">COFINA</span>
            &nbsp;&middot;&nbsp; Oracle FLEXCUBE &middot; EOD Tracking
        </div>
        <div class="footer-r">
            G&eacute;n&eacute;r&eacute; le {{ $dateGeneration ?? now()->format('d/m/Y H:i') }}
            &nbsp;|&nbsp; {{ $generateur ?? 'Syst&egrave;me IT' }}
        </div>
    </div>

</div>
</body>
</html>