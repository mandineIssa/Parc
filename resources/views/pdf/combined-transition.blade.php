<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transition complète - {{ $equipment->numero_serie }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 40px; border: 1px solid #ccc; padding: 20px; }
        .section-title { background-color: #d32f2f; color: white; padding: 10px; margin: -20px -20px 20px -20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .signature-box { margin-top: 30px; border-top: 1px solid #000; padding-top: 10px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>COFINA SENEGAL - IT</h1>
        <h2>TRANSITION COMPLÈTE - FORMULAIRE 3 ÉTAPES</h2>
        <p>N° Demande: #{{ $approval->id }} | Équipement: {{ $equipment->nom }} | N° Série: {{ $equipment->numero_serie }}</p>
        <p>Date d'approbation: {{ $approval->approved_at->format('d/m/Y H:i') }}</p>
    </div>

    <!-- ÉTAPE 1: FICHE D'INSTALLATION -->
    <div class="section">
        <div class="section-title">
            <h3>ÉTAPE 1 - FICHE D'INSTALLATION</h3>
        </div>
        
        @php $install = $installation_data ?? []; @endphp
        
        <h4>Informations d'installation</h4>
        <table>
            <tr>
                <th>Agence</th>
                <td>{{ $install['agence_nom'] ?? 'N/A' }}</td>
                <th>Date d'application</th>
                <td>{{ isset($install['date_application']) ? \Carbon\Carbon::parse($install['date_application'])->format('d/m/Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Installateur</th>
                <td>{{ $install['installateur_nom'] ?? 'N/A' }}</td>
                <th>Fonction</th>
                <td>{{ $install['installateur_fonction'] ?? 'IT' }}</td>
            </tr>
        </table>
        
        <h4>Checklist d'installation</h4>
        @if(isset($install['checklist']))
            <table>
                <tr>
                    <th>Élément</th>
                    <th>Status</th>
                </tr>
                @foreach($install['checklist'] as $key => $value)
                    @if($value)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                            <td>✅ Complété</td>
                        </tr>
                    @endif
                @endforeach
            </table>
        @endif
        
        @if(isset($install['signature_installateur']))
            <div class="signature-box">
                <p><strong>Signature installateur :</strong></p>
                <img src="{{ $install['signature_installateur'] }}" style="max-width: 200px; max-height: 80px;">
            </div>
        @endif
    </div>

    <!-- ÉTAPE 2: AFFECTATION SIMPLE -->
    <div class="section">
        <div class="section-title">
            <h3>ÉTAPE 2 - AFFECTATION SIMPLE</h3>
        </div>
        
        @php $affect = $affectation_data ?? []; @endphp
        
        <table>
            <tr>
                <th>Utilisateur</th>
                <td>
                    @if(isset($affect['user_id']))
                        {{ \App\Models\User::find($affect['user_id'])->name ?? 'N/A' }}
                    @else
                        {{ $affect['responsable_name'] ?? 'N/A' }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Département</th>
                <td>{{ $affect['department'] ?? 'N/A' }}</td>
                <th>Poste</th>
                <td>{{ $affect['position'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Date d'affectation</th>
                <td>{{ isset($affect['affectation_date']) ? \Carbon\Carbon::parse($affect['affectation_date'])->format('d/m/Y') : 'N/A' }}</td>
                <th>Raison</th>
                <td>{{ $affect['affectation_reason'] ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- ÉTAPE 3: FICHE DE MOUVEMENT -->
    <div class="section">
        <div class="section-title">
            <h3>ÉTAPE 3 - FICHE DE MOUVEMENT</h3>
        </div>
        
        @php $mouv = $mouvement_data ?? []; @endphp
        
        <h4>Détails du mouvement</h4>
        <table>
            <tr>
                <th>Expéditeur</th>
                <td>{{ $mouv['expediteur_nom'] ?? 'N/A' }}</td>
                <th>Fonction</th>
                <td>{{ $mouv['expediteur_fonction'] ?? 'AGENT IT' }}</td>
            </tr>
            <tr>
                <th>Réceptionnaire</th>
                <td>{{ $mouv['receptionnaire_nom'] ?? 'N/A' }}</td>
                <th>Fonction</th>
                <td>{{ $mouv['receptionnaire_fonction'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Lieu de départ</th>
                <td>{{ $mouv['lieu_depart'] ?? 'N/A' }}</td>
                <th>Destination</th>
                <td>{{ $mouv['destination'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Motif</th>
                <td colspan="3">{{ $mouv['motif'] ?? 'N/A' }}</td>
            </tr>
        </table>
        
        @if(isset($mouv['signature_expediteur']))
            <div class="signature-box">
                <p><strong>Signature expéditeur :</strong></p>
                <img src="{{ $mouv['signature_expediteur'] }}" style="max-width: 200px; max-height: 80px;">
            </div>
        @endif
        
        @if(isset($mouv['signature_receptionnaire']))
            <div class="signature-box">
                <p><strong>Signature réceptionnaire :</strong></p>
                <img src="{{ $mouv['signature_receptionnaire'] }}" style="max-width: 200px; max-height: 80px;">
            </div>
        @endif
    </div>

    <!-- APPROBATION -->
    <div class="section">
        <div class="section-title">
            <h3>APPROBATION FINALE</h3>
        </div>
        
        <table>
            <tr>
                <th>Approuvée par</th>
                <td>{{ $approval->approver->name ?? 'N/A' }}</td>
                <th>Date d'approbation</th>
                <td>{{ $approval->approved_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <th>Statut équipement</th>
                <td colspan="3">
                    <strong>Stock → Parc</strong> - Transition complétée avec succès
                </td>
            </tr>
        </table>
        
        <div class="signature-box">
            <p><strong>Signature Super Admin :</strong></p>
            <p>___________________________________</p>
            <p>{{ $approval->approver->name ?? 'Super Admin' }}</p>
        </div>
    </div>
</body>
</html>