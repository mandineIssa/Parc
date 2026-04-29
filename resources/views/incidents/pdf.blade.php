{{-- resources/views/incidents/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport Incident ITIL - {{ $incident->reference }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            margin: 15px;
            color: #1a1a1a;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #dc2626;
            padding-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #dc2626;
        }
        .subtitle {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        .ref {
            font-size: 10px;
            color: #666;
            margin-top: 3px;
        }
        .section {
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .section-title {
            background: #f3f4f6;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 11px;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
        }
        .section-title-red {
            background: #dc2626;
            color: white;
        }
        .section-title-blue {
            background: #2563eb;
            color: white;
        }
        .section-title-orange {
            background: #ea580c;
            color: white;
        }
        .section-title-purple {
            background: #7c3aed;
            color: white;
        }
        .section-content {
            padding: 12px;
        }
        .grid-2 {
            display: table;
            width: 100%;
        }
        .grid-row {
            display: table-row;
        }
        .grid-cell {
            display: table-cell;
            padding: 5px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #6b7280;
            width: 130px;
        }
        .value {
            color: #1f2937;
        }
        .info-box {
            background: #f9fafb;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            border-left: 3px solid #dc2626;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-p1 { background: #fee2e2; color: #991b1b; }
        .badge-p2 { background: #fed7aa; color: #9a3412; }
        .badge-p3 { background: #fef3c7; color: #92400e; }
        .badge-p4 { background: #dcfce7; color: #166534; }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .signature {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #d1d5db;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background: #f3f4f6;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="title">📄 RAPPORT D'INCIDENT ITIL</div>
    <div class="subtitle">COFINA Mobile — Gestion des Incidents</div>
    <div class="ref">Référence : {{ $incident->reference }} | Formulaire E02002V01</div>
</div>

{{-- 1. INFORMATIONS GÉNÉRALES --}}
<div class="section">
    <div class="section-title section-title-red">1. INFORMATIONS GÉNÉRALES</div>
    <div class="section-content">
        <table>
            <tr><th width="30%">Champ</th><th>Valeur</th></tr>
            <tr><td><strong>Référence</strong></td><td>{{ $incident->reference }}</td></tr>
            <tr><td><strong>Date de survenue</strong></td><td>{{ $incident->date_incident->format('d/m/Y') }}</td></tr>
            <tr><td><strong>Heure de début</strong></td><td>{{ $incident->heure_debut ?: ($incident->heure_incident ?: 'Non renseignée') }}</td></tr>
            @if($incident->heure_resolution)<tr><td><strong>Heure de résolution</strong></td><td>{{ $incident->heure_resolution }}</td></tr>@endif
            @if($incident->duree_incident)<tr><td><strong>Durée</strong></td><td>{{ $incident->duree_incident }}</td></tr>@endif
            <tr><td><strong>Application concernée</strong></td><td>{{ $incident->application_concernee ?: '—' }}</td></tr>
            <tr><td><strong>Environnement</strong></td><td>{{ $incident->environnement ?: 'Production' }}</td></tr>
            <tr><td><strong>Criticité</strong></td><td><span class="badge badge-{{ strtolower($incident->niveau_criticite ?? 'p3') }}">{{ $incident->criticite_label }}</span></td></tr>
            <tr><td><strong>Type d'incident</strong></td><td>{{ $incident->type_label }}</td></tr>
        </table>
    </div>
</div>

{{-- 2. DECLARATION --}}
<div class="section">
    <div class="section-title section-title-red">2. DÉCLARATION</div>
    <div class="section-content">
        <table>
            <tr><th width="30%">Champ</th><th>Valeur</th></tr>
            <tr><td><strong>Déclarant</strong></td><td>{{ $incident->utilisateur }}</td></tr>
            <tr><td><strong>Entité / Service</strong></td><td>{{ $incident->entite }}</td></tr>
            <tr><td><strong>Fonction</strong></td><td>{{ $incident->fonction }}</td></tr>
            <tr><td><strong>Canal de remontée</strong></td><td>{{ $incident->point_entree_label }}</td></tr>
            <tr><td><strong>Service impacté</strong></td><td>{{ $incident->service_impacte ?: '—' }}</td></tr>
            <tr><td><strong>Nombre de clients impactés</strong></td><td>{{ $incident->nb_clients_impactes ?: '—' }}</td></tr>
            <tr><td><strong>Bloquant</strong></td><td>{{ $incident->bloquant ? 'OUI' : 'Non' }}</td></tr>
            <tr><td><strong>Reproductible</strong></td><td>{{ $incident->reproductible ? 'OUI' : 'Non' }}</td></tr>
        </table>
    </div>
</div>

{{-- 3. DESCRIPTION ET IMPACT --}}
<div class="section">
    <div class="section-title section-title-red">3. DESCRIPTION ET IMPACT</div>
    <div class="section-content">
        <div class="info-box">
            <strong>📝 Sujet :</strong><br>
            {{ $incident->sujet }}
        </div>
        <div class="info-box">
            <strong>📋 Description détaillée :</strong><br>
            {{ $incident->description }}
        </div>
        @if($incident->impact_metier)
        <div class="info-box" style="border-left-color: #ea580c;">
            <strong>⚠️ Impact métier :</strong><br>
            {{ $incident->impact_metier }}
        </div>
        @endif
    </div>
</div>

{{-- 4. TRAITEMENT N+1 --}}
@if($incident->n1_description_traitement)
<div class="section">
    <div class="section-title section-title-blue">4. TRAITEMENT NIVEAU HELPDESK (N+1)</div>
    <div class="section-content">
        <table>
            <tr><th width="30%">Champ</th><th>Valeur</th></tr>
            <tr><td><strong>Responsable</strong></td><td>{{ $incident->n1User?->name ?? '—' }}</td></tr>
            <tr><td><strong>Date de traitement</strong></td><td>{{ $incident->n1_date_traitement?->format('d/m/Y H:i') ?? '—' }}</td></tr>
            @if($incident->n1_autres_intervenants)<tr><td><strong>Intervenants</strong></td><td>{{ $incident->n1_autres_intervenants }}</td></tr>@endif
        </table>
        <div class="info-box">
            <strong>🔧 Description du traitement :</strong><br>
            {{ $incident->n1_description_traitement }}
        </div>
        <div class="info-box">
            <strong>💡 Solutions envisagées :</strong><br>
            {{ $incident->n1_solutions_envisagees }}
        </div>
        <p><strong>Décision :</strong> {{ $incident->n1_statut === 'cloture' ? '✅ Clôturé' : '⬆️ Transféré au N+2' }}</p>
    </div>
</div>
@endif

{{-- 5. TRAITEMENT N+2 --}}
@if($incident->n2_description_traitement)
<div class="section">
    <div class="section-title section-title-orange">5. TRAITEMENT SUPPORT NIVEAU 2 (N+2)</div>
    <div class="section-content">
        <table>
            <tr><th width="30%">Champ</th><th>Valeur</th></tr>
            <tr><td><strong>Responsable</strong></td><td>{{ $incident->n2User?->name ?? '—' }}</td></tr>
            <tr><td><strong>Date de traitement</strong></td><td>{{ $incident->n2_date_traitement?->format('d/m/Y H:i') ?? '—' }}</td></tr>
            @if($incident->n2_autres_intervenants)<tr><td><strong>Intervenants</strong></td><td>{{ $incident->n2_autres_intervenants }}</td></tr>@endif
        </table>
        <div class="info-box">
            <strong>🔧 Description du traitement :</strong><br>
            {{ $incident->n2_description_traitement }}
        </div>
        <div class="info-box">
            <strong>💡 Solutions envisagées :</strong><br>
            {{ $incident->n2_solutions_envisagees }}
        </div>
        <p><strong>Décision :</strong> {{ $incident->n2_statut === 'cloture' ? '✅ Clôturé' : '🎫 Ticket ouvert vers N+3' }}</p>
    </div>
</div>
@endif

{{-- 6. TRAITEMENT N+3 --}}
@if($incident->n3_description_traitement)
<div class="section">
    <div class="section-title section-title-purple">6. TRAITEMENT VALIDATEUR (N+3)</div>
    <div class="section-content">
        <table>
            <tr><th width="30%">Champ</th><th>Valeur</th></tr>
            <tr><td><strong>Responsable</strong></td><td>{{ $incident->n3User?->name ?? '—' }}</td></tr>
            <tr><td><strong>Date de traitement</strong></td><td>{{ $incident->n3_date_traitement?->format('d/m/Y H:i') ?? '—' }}</td></tr>
            @if($incident->n3_autres_intervenants)<tr><td><strong>Intervenants</strong></td><td>{{ $incident->n3_autres_intervenants }}</td></tr>@endif
        </table>
        <div class="info-box">
            <strong>🔧 Description du traitement :</strong><br>
            {{ $incident->n3_description_traitement }}
        </div>
        <div class="info-box">
            <strong>💡 Solutions envisagées :</strong><br>
            {{ $incident->n3_solutions_envisagees }}
        </div>
        <p><strong>Conclusion :</strong> ✅ Clôture définitive</p>
    </div>
</div>
@endif

{{-- 7. ANALYSE ET ACTIONS --}}
@if($incident->cause_racine || $incident->actions_correctives || $incident->actions_preventives)
<div class="section">
    <div class="section-title">7. ANALYSE ET ACTIONS</div>
    <div class="section-content">
        @if($incident->cause_racine)
        <div class="info-box" style="border-left-color: #dc2626;">
            <strong>🔍 Cause racine (Root Cause Analysis) :</strong><br>
            {{ $incident->cause_racine }}
        </div>
        @endif
        @if($incident->actions_correctives)
        <div class="info-box" style="border-left-color: #10b981;">
            <strong>✅ Actions correctives immédiates :</strong><br>
            <ul>
                @foreach($incident->actions_correctives as $action)
                <li>{{ $action }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if($incident->actions_preventives)
        <div class="info-box" style="border-left-color: #3b82f6;">
            <strong>🛡️ Actions préventives (CAPA) :</strong><br>
            <ul>
                @foreach($incident->actions_preventives as $action)
                <li>{{ $action }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
@endif

{{-- 8. CHRONOLOGIE --}}
@if($incident->chronologie && count($incident->chronologie) > 0)
<div class="section">
    <div class="section-title">8. CHRONOLOGIE DE L'INCIDENT</div>
    <div class="section-content">
        <table>
            <thead>
                <tr><th>Heure</th><th>Action</th></tr>
            </thead>
            <tbody>
                @foreach($incident->chronologie as $event)
                <tr>
                    <td>{{ $event['heure'] ?? $event['time'] ?? '—' }}</td>
                    <td>{{ $event['action'] ?? $event['description'] ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- 9. INFORMATIONS SLA --}}
<div class="section">
    <div class="section-title">9. INFORMATIONS SLA</div>
    <div class="section-content">
        <table>
            <tr><th width="30%">Champ</th><th>Valeur</th></tr>
            @if($incident->temps_resolution)<tr><td><strong>Temps de résolution</strong></td><td>{{ $incident->temps_resolution }}</td></tr>@endif
            <tr><td><strong>SLA respecté</strong></td><td>{{ $incident->sla_respecte ? '✅ Oui' : '❌ Non' }}</td></tr>
            @if($incident->date_cloture)<tr><td><strong>Date de clôture</strong></td><td>{{ $incident->date_cloture->format('d/m/Y H:i') }}</td></tr>@endif
            @if($incident->validePar)<tr><td><strong>Validé par</strong></td><td>{{ $incident->validePar->name }}</td></tr>@endif
            @if($incident->commentaires_cloture)<tr><td><strong>Commentaires</strong></td><td>{{ $incident->commentaires_cloture }}</td></tr>@endif
        </table>
    </div>
</div>

<div class="footer">
    Document généré le {{ now()->format('d/m/Y H:i:s') }} - Système de Gestion des Incidents COFINA ITIL
    <br>Document confidentiel - À usage interne uniquement
</div>

</body>
</html>