{{-- resources/views/change/pdf/fiche.blade.php — fiche changement clôturée (charte COFINA) --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fiche changement {{ $ticket->ticket_id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            font-size: 8.5px;
            line-height: 1.35;
            color: #1e293b;
            background: #ffffff;
        }
        .page { width: 190mm; margin: 0 auto; padding: 5mm 0; }

        /* Charte COFINA (alignée fiche EOD PDF) */
        .header {
            border: 1.5px solid #C8102E;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 8px;
        }
        .header-title {
            background: #C8102E;
            color: white;
            text-align: center;
            padding: 6px 10px 4px;
        }
        .header-title h1 {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 2px;
        }
        .header-title .sub {
            font-size: 7.5px;
            color: #fecdd3;
            font-weight: 400;
        }
        .header-brand {
            background: #4a4a4a;
            color: white;
            padding: 3px 10px;
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: 2px;
            text-align: right;
            text-transform: uppercase;
        }
        .meta-strip {
            display: table;
            width: 100%;
            background: #fafafa;
            border-top: 0.4px solid #d1d5db;
            font-size: 8px;
        }
        .meta-strip-row { display: table-row; }
        .meta-strip-cell {
            display: table-cell;
            padding: 4px 8px;
            border: 0.4px solid #d1d5db;
            vertical-align: middle;
        }
        .meta-strip-cell .lbl { font-weight: 700; color: #4a4a4a; }
        .meta-strip-cell .val { color: #C8102E; font-weight: 600; }

        .section {
            border: 1px solid #d1d5db;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 5px;
            page-break-inside: avoid;
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
        .section-body { padding: 8px 10px; }

        table.meta { width: 100%; border-collapse: collapse; font-size: 8px; }
        table.meta th, table.meta td {
            border: 0.5px solid #d1d5db;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }
        table.meta th {
            background: #f0f0f0;
            width: 28%;
            font-weight: 700;
            color: #C8102E;
        }
        .block-text { white-space: pre-wrap; word-wrap: break-word; }

        table.hist { width: 100%; border-collapse: collapse; font-size: 8px; }
        table.hist th, table.hist td { border: 0.5px solid #d1d5db; padding: 4px; }
        table.hist th { background: #f0f0f0; color: #4a4a4a; font-weight: 700; }

        ul.files { margin: 4px 0 0 16px; padding: 0; }
        ul.files li { margin-bottom: 2px; }

        .footer {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1.5px solid #C8102E;
            display: table;
            width: 100%;
            font-size: 7px;
            color: #6b7280;
        }
        .footer-l { display: table-cell; text-align: left; vertical-align: middle; }
        .footer-c { display: table-cell; text-align: center; vertical-align: middle; }
        .footer-r { display: table-cell; text-align: right; vertical-align: middle; }
        .footer-brand { color: #C8102E; font-weight: 700; font-size: 8px; letter-spacing: 1px; }
    </style>
</head>
<body>

<div class="page">

<div class="header">
    <div class="header-title">
        <h1>Fiche de gestion du changement</h1>
        <div class="sub">Change Management &middot; Document généré après clôture &middot; {{ $dateGeneration }}</div>
    </div>
    <div class="header-brand">COFINA &mdash; Compagnie Financi&egrave;re Africaine</div>
    <div class="meta-strip">
        <div class="meta-strip-row">
            <div class="meta-strip-cell"><span class="lbl">ID ticket :</span> <span class="val">{{ $ticket->ticket_id }}</span></div>
            <div class="meta-strip-cell"><span class="lbl">N° ticket :</span> <span class="val">{{ $ticket->ticket_number ?: '—' }}</span></div>
        </div>
    </div>
</div>

<div class="section">
    <div class="section-head">Synthèse</div>
    <div class="section-body">
        <table class="meta">
            <tr><th>Statut</th><td>{{ $ticket->status_label }} (clôturé)</td></tr>
            <tr><th>Titre</th><td>{{ $ticket->titre }}</td></tr>
            <tr><th>Type</th><td>{{ $ticket->type }}</td></tr>
            <tr><th>Demandeur</th><td>{{ $ticket->prenom }} {{ $ticket->nom }} — {{ $ticket->departement }}</td></tr>
            <tr><th>Environnement</th><td>{{ $ticket->environnement }}</td></tr>
            <tr><th>Date de création</th><td>{{ $ticket->created_at?->format('d/m/Y H:i') }}</td></tr>
            <tr><th>Date de clôture</th><td>{{ $ticket->closed_at?->format('d/m/Y H:i') ?? '—' }}</td></tr>
            @if($ticket->close_note)
            <tr><th>Note de clôture</th><td class="block-text">{{ $ticket->close_note }}</td></tr>
            @endif
        </table>
    </div>
</div>

<div class="section">
    <div class="section-head">1. Problématique</div>
    <div class="section-body block-text">{{ $ticket->problematique ?: '—' }}</div>
</div>

<div class="section">
    <div class="section-head">2. Analyse d'impact &amp; risques</div>
    <div class="section-body">
        <table class="meta">
            <tr><th>Impact opérations</th><td>{{ $ticket->impact_ops ?: '—' }}</td></tr>
            <tr><th>Impact utilisateurs</th><td>{{ $ticket->impact_users ?: '—' }}</td></tr>
            <tr><th>Impact production</th><td>{{ $ticket->impact_prod ?: '—' }}</td></tr>
            <tr><th>Risques</th><td class="block-text">{{ $ticket->risques ?: '—' }}</td></tr>
            <tr><th>Rollback</th><td class="block-text">{{ $ticket->rollback ?: '—' }}</td></tr>
            <tr><th>Date d'exécution prévue</th><td>{{ $ticket->date_execution ? $ticket->date_execution->format('d/m/Y') : '—' }}</td></tr>
        </table>
    </div>
</div>

@if($ticket->files && count($ticket->files) > 0)
<div class="section">
    <div class="section-head">Fichiers joints (demandeur)</div>
    <div class="section-body">
        <ul class="files">
            @foreach($ticket->files as $f)
                <li>{{ is_array($f) ? ($f['name'] ?? 'fichier') : $f }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="section">
    <div class="section-head">3. Recommandation &amp; requête</div>
    <div class="section-body">
        <p><strong>Recommandation</strong></p>
        <div class="block-text">{{ $ticket->recommandation ?: '—' }}</div>
        @if($ticket->recomm_files && count($ticket->recomm_files) > 0)
            <p style="margin-top:6px;"><strong>Fiches de test</strong></p>
            <ul class="files">
                @foreach($ticket->recomm_files as $f)
                    <li>{{ is_array($f) ? ($f['name'] ?? 'fichier') : $f }}</li>
                @endforeach
            </ul>
        @endif
        <p style="margin-top:8px;"><strong>Requête / script</strong></p>
        <div class="block-text">{{ $ticket->requete ?: '—' }}</div>
    </div>
</div>

<div class="section">
    <div class="section-head">4. Exécution &amp; résultat</div>
    <div class="section-body">
        <table class="meta">
            <tr><th>Date d'exécution réelle</th><td>{{ $ticket->date_exec_reelle ? $ticket->date_exec_reelle->format('d/m/Y H:i') : '—' }}</td></tr>
            <tr><th>Opérateur</th><td>{{ $ticket->operateur ?: '—' }}</td></tr>
            <tr><th>Résultat</th><td class="block-text">{{ $ticket->resultat ?: '—' }}</td></tr>
            <tr><th>Écarts / anomalies</th><td class="block-text">{{ $ticket->ecarts ?: '—' }}</td></tr>
        </table>
        @if($ticket->exec_files && count($ticket->exec_files) > 0)
            <p style="margin-top:6px;"><strong>Screenshots / logs</strong></p>
            <ul class="files">
                @foreach($ticket->exec_files as $f)
                    <li>{{ is_array($f) ? ($f['name'] ?? 'fichier') : $f }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

@if($ticket->n2_progress_entries && count($ticket->n2_progress_entries) > 0)
<div class="section">
    <div class="section-head">Notes complémentaires</div>
    <div class="section-body">
        @foreach($ticket->n2_progress_entries as $entry)
            @if(is_array($entry) && !empty($entry['text']))
                @php
                    $_actor = trim(($entry['user_prenom'] ?? '') . ' ' . ($entry['user_nom'] ?? ''));
                @endphp
                <p style="margin-bottom:6px;"><span style="color:#6b7280;">{{ $entry['at'] ?? '' }}@if($_actor !== '') — {{ $_actor }}@endif</span></p>
                <div class="block-text" style="margin-bottom:8px;">{{ $entry['text'] }}</div>
            @endif
        @endforeach
    </div>
</div>
@endif

@if($ticket->n3_progress_entries && count($ticket->n3_progress_entries) > 0)
<div class="section">
    <div class="section-head">Commentaires de contrôle</div>
    <div class="section-body">
        @foreach($ticket->n3_progress_entries as $entry)
            @if(is_array($entry) && !empty($entry['text']))
                @php
                    $_actor = trim(($entry['user_prenom'] ?? '') . ' ' . ($entry['user_nom'] ?? ''));
                @endphp
                <p style="margin-bottom:6px;"><span style="color:#6b7280;">{{ $entry['at'] ?? '' }}@if($_actor !== '') — {{ $_actor }}@endif</span></p>
                <div class="block-text" style="margin-bottom:8px;">{{ $entry['text'] }}</div>
            @endif
        @endforeach
    </div>
</div>
@endif

@php
    $_histPdf = $historyForPdf ?? $ticket->history ?? [];
@endphp
@if(is_array($_histPdf) && count($_histPdf) > 0)
<div class="section">
    <div class="section-head">Historique des actions</div>
    <div class="section-body">
        <table class="hist">
            <thead>
                <tr><th>Date / heure</th><th>Intervenant</th><th>Action</th><th>Note</th></tr>
            </thead>
            <tbody>
                @foreach($_histPdf as $h)
                    @php
                        $_hActor = $h['intervenant_display'] ?? '';
                        if ($_hActor === '') {
                            $_hActor = trim(($h['user_prenom'] ?? '') . ' ' . ($h['user_nom'] ?? ''));
                            $_hActor = $_hActor !== '' ? $_hActor : ($h['role'] ?? '');
                        }
                    @endphp
                    <tr>
                        <td>{{ $h['at'] ?? '' }}</td>
                        <td>{{ $_hActor }}</td>
                        <td>{{ $h['action'] ?? '' }}</td>
                        <td>{{ $h['note'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@php
    $_gen = trim(($generateurPrenom ?? '') . ' ' . ($generateurNom ?? ''));
@endphp
<div class="footer">
    <div class="footer-l">Document confidentiel — Usage interne</div>
    <div class="footer-c">
        <span class="footer-brand">COFINA</span>
        &nbsp;&middot;&nbsp; Change Management
    </div>
    <div class="footer-r">
        Généré le {{ $dateGeneration }}@if($_gen !== '') — {{ $_gen }}@endif
    </div>
</div>

</div>
</body>
</html>
