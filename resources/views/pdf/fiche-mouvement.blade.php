<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche de Mouvement - COFINA</title>

    <style>
        @page {
            size: A4;
            margin: 4mm 6mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #000;
            border: 2px solid #000;
            padding: 4mm;
        }

        /* ================= EN-TÊTE ================= */
        .header-box {
            border: 2px solid #000;
            margin-bottom: 8px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .header-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: middle;
        }

        .logo-cell {
            width: 10%;
            padding: 0px;
            margin: 0px;
        }

        .header-logo {
            max-width: 110px;
            max-height: 70px;
        }

        .top-title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
        }

        .bottom-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        .date-cell {
            width: 32%;
            font-size: 10pt;
            text-align: left;
        }

        /* ================= SECTIONS ================= */
        .two-columns {
            display: table;
            width: 100%;
            margin-top: 6px;
        }

        .actor-column {
            display: table-cell;
            width: 50%;
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .section-header {
            background: #e30613;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 3px;
            margin: -6px -6px 6px -6px;
            font-size: 10pt;
        }

        .field-row {
            margin-bottom: 6px;
        }

        .field-label {
            font-weight: bold;
            display: inline-block;
            width: 80px;
        }

        /* ================= TABLEAU ================= */
        table.material-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.material-table th,
        table.material-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        table.material-table th {
            background: #f2f2f2;
            font-size: 9pt;
        }

        /* ================= SIGNATURES ================= */
        .validation-columns {
            display: table;
            width: 100%;
            margin-top: 8px;
        }

        .validation-column {
            display: table-cell;
            width: 50%;
            border: 1px solid #000;
            padding: 6px;
        }

        .validation-title {
            background: #e30613;
            color: #fff;
            text-align: center;
            font-weight: bold;
            padding: 3px;
            margin: -6px -6px 6px -6px;
            font-size: 9pt;
        }

        .signature-field {
            margin-bottom: 6px;
        }

        .signature-label {
            font-weight: bold;
        }

        .signature-area {
            height: 70px;
            margin-top: 8px;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signature-img {
            max-width: 150px;
            max-height: 40px;
        }

        /* ================= NOTA ================= */
        .note-text {
            margin-top: 10px;
            font-size: 8pt;
            font-style: italic;
        }

        /* ================= ESPACEMENT ENTRE LES 3 PARTIES ================= */

/* Espace après Expéditeur / Réceptionnaire */
.two-columns {
    margin-bottom: 15px; /* augmente ou diminue ici */
}

/* Espace avant et après le tableau */
.material-table {
    margin-top: 15px;
    margin-bottom: 15px;
}

/* Espace avant la section signatures */
.validation-columns {
    margin-top: 15px;
}

    </style>
</head>

<body>

@php
    $logoUrl = null;
    if (file_exists(public_path('logo_Cofina.png'))) {
        $imageData = file_get_contents(public_path('logo_Cofina.png'));
        $logoUrl = 'data:image/png;base64,' . base64_encode($imageData);
    } elseif (file_exists(public_path('logo_Cofina.jpg'))) {
        $imageData = file_get_contents(public_path('logo_Cofina.jpg'));
        $logoUrl = 'data:image/jpeg;base64,' . base64_encode($imageData);
    }

    $mouvementData = $data['mouvement_data'] ?? [];
    $signatures = $data['signatures'] ?? [];

    $expediteurData = [
        'nom' => $data['expediteur_nom'] ?? '',
        'prenom' => $data['expediteur_prenom'] ?? '',
        'fonction' => $data['expediteur_fonction'] ?? ''
    ];

    $receptionnaireData = [
        'nom' => $data['receptionnaire_nom'] ?? '',
        'prenom' => $data['receptionnaire_prenom'] ?? '',
        'fonction' => $data['receptionnaire_fonction'] ?? ''
    ];

    $dateApplication = isset($data['date_application_mouvement'])
        ? \Carbon\Carbon::parse($data['date_application_mouvement'])->format('d/m/Y')
        : '……/……/………';

    $dateExpediteur = isset($data['date_expediteur'])
        ? \Carbon\Carbon::parse($data['date_expediteur'])->format('d/m/Y')
        : '……/……/………';

    $dateReceptionnaire = isset($data['date_receptionnaire'])
        ? \Carbon\Carbon::parse($data['date_receptionnaire'])->format('d/m/Y')
        : '……/……/………';
@endphp

<!-- ================= EN-TÊTE ================= -->
<div class="header-box">
    <table class="header-table">
        <tr>
            <td class="logo-cell" rowspan="2">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" class="header-logo" alt="COFINA">
                @endif
            </td>
            <td class="top-title" colspan="2">COFINA SENEGAL - IT</td>
        </tr>
        <tr>
            <td class="bottom-title">FICHE DE MOUVEMENT DE MATERIEL INFORMATIQUE</td>
            <td class="date-cell">
                <strong>Date d'application :</strong> {{ $dateApplication }}
            </td>
        </tr>
    </table>
</div>

<!-- ================= EXPEDITEUR / RECEPTIONNAIRE ================= -->
<div class="two-columns">
    <div class="actor-column">
        <div class="section-header">EXPEDITEUR</div>
        <div class="field-row">
            <span class="field-label">Nom :</span> 
            <span>{{ $expediteurData['nom'] }}</span>
        </div>
<!--         <div class="field-row">
            <span class="field-label">Prénom :</span> 
            <span>{{ $expediteurData['prenom'] }}</span>
        </div> -->
        <div class="field-row">
            <span class="field-label">Fonction :</span> 
            <span>{{ $expediteurData['fonction'] }}</span>
        </div>
    </div>

    <div class="actor-column">
        <div class="section-header">RECEPTIONNAIRE</div>
        <div class="field-row">
            <span class="field-label">Nom :</span> 
            <span>{{ $receptionnaireData['nom'] }}</span>
        </div>
<!--         <div class="field-row">
            <span class="field-label">Prénom :</span> 
            <span>{{ $receptionnaireData['prenom'] }}</span>
        </div> -->
        <div class="field-row">
            <span class="field-label">Fonction :</span> 
            <span>{{ $receptionnaireData['fonction'] }}</span>
        </div>
    </div>
</div>

<!-- ================= TABLEAU ================= -->
<table class="material-table">
    <thead>
        <tr>
            <th style="width: 20%;">TYPE DE MATERIEL</th>
            <th style="width: 20%;">REFERENCE</th>
            <th style="width: 20%;">LIEU DE DEPART</th>
            <th style="width: 20%;">DESTINATION</th>
            <th style="width: 20%;">MOTIF</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: left; padding-left: 10px;">{{ $equipment->type ?? 'Informatique' }}</td>
            <td>{{ $equipment->numero_serie ?? '' }}</td>
            <td>{{ $data['lieu_depart'] ?? '' }}</td>
            <td>{{ $data['destination'] ?? '' }}</td>
            <td>{{ $data['motif'] ?? '' }}</td>
        </tr>
    </tbody>
</table>

<!-- ================= SIGNATURES ================= -->
<div class="validation-columns">
    <div class="validation-column">
        <div class="validation-title">Signature de l'expéditeur</div>
        <div class="signature-field"><strong>Date :</strong> {{ $dateExpediteur }}</div>
        <div class="signature-area">
            @if(!empty($data['signature_expediteur']))
                <img src="{{ $data['signature_expediteur'] }}" class="signature-img">
            @endif
        </div>
    </div>

    <div class="validation-column">
        <div class="validation-title">Signature du réceptionnaire</div>
        <div class="signature-field"><strong>Date :</strong> {{ $dateReceptionnaire }}</div>
        <div class="signature-area">
            @if(!empty($data['signature_receptionnaire']))
                <img src="{{ $data['signature_receptionnaire'] }}" class="signature-img">
            @endif
        </div>
    </div>
</div>

<!-- ================= NOTA ================= -->
<div class="note-text">
    <strong>NOTA :</strong> Tout mouvement de matériel informatique doit faire l'objet de cette fiche signée par l'expéditeur et le réceptionnaire.
</div>

</body>
</html>