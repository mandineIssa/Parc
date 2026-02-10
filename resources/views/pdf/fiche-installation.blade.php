<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche d'Installation - COFINA</title>
<style>
    /* ================= PAGE ================= */
    @page {
        size: A4;
        margin: 0;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "DejaVu Sans", Arial, Helvetica, sans-serif;
        font-size: 8.5pt; /* Réduit de 9pt à 8.5pt */
        color: #000;
        margin: 0;
        padding: 1mm 1mm;
        line-height: 1.1; /* Réduit l'interligne */
    }

    /* ================= EN-TÊTE ================= */
    .header-box {
        border: 2px solid #000;
        margin-bottom: 0.5mm; /* Réduit la marge */
    }

    .header-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .header-table td {
        border: 1px solid #000;
        padding: 1mm 1.5mm; /* Réduit le padding */
        vertical-align: middle;
    }

    .logo-cell {
        width: 18%; /* Réduit la largeur du logo */
    }

    .header-logo {
        width: 80px; /* Réduit la taille du logo */
        height: 80px;
    }

    .top-title {
        font-size: 11pt; /* Réduit de 12pt à 11pt */
        font-weight: bold;
        text-align: center;
        letter-spacing: 0.2px;
        padding: 0.5mm 0;
    }

    .bottom-title {
        font-size: 8.5pt; /* Réduit de 9pt à 8.5pt */
        font-weight: bold;
        text-transform: uppercase;
        text-align: center;
        padding: 0.3mm 0;
    }

    .date-cell {
        width: 28%; /* Réduit légèrement */
        font-size: 7.5pt;
        text-align: left;
    }

    /* ================= SECTION CONTAINER COMPACT ================= */
    .section-container {
        border: 1px solid #000;
        margin-bottom: 0.8mm; /* Réduit la marge */
        padding: 0.8mm; /* Réduit le padding */
        page-break-inside: avoid;
    }

    .section-header {
        background: #e30613;
        color: #fff;
        font-weight: bold;
        text-align: center;
        padding: 0.8mm; /* Réduit le padding */
        margin: -0.8mm -0.8mm 0.8mm -0.8mm; /* Ajusté au nouveau padding */
        font-size: 8.5pt;
    }

    /* ================= INFORMATIONS COMPACTES ================= */
    .info-table {
        width: 100%;
        border: 1px solid #000;
        border-collapse: collapse;
        margin-bottom: 0.5mm; /* Réduit la marge */
        font-size: 8pt; /* Réduit de 8.5pt à 8pt */
    }

    .info-table td {
        border: 1px dotted #000;
        padding: 0.3mm 0.8mm; /* Réduit le padding */
        vertical-align: middle;
    }

    .info-date {
        width: 25%;
        text-align: right;
        font-weight: bold;
        padding-left: 1.5mm;
    }

    /* ================= CHECKBOXES COMPACTES ================= */
    .subsection-title {
        font-weight: bold;
        font-size: 8pt; /* Réduit de 8.5pt à 8pt */
        margin: 0.3mm 0 0.2mm 0;
    }

    .checkbox-list {
        margin-bottom: 0.5mm; /* Réduit la marge */
    }
    
    .tight-spacing {
        margin-bottom: 0.3mm; /* Réduit de 0.5mm à 0.3mm */
    }

    .checkbox-item {
        margin-bottom: 0.3mm; /* Réduit de 0.5mm à 0.3mm */
        display: block;
        font-size: 7.5pt; /* Réduit de 8pt à 7.5pt */
        line-height: 1.1;
    }

    .checkbox-symbol {
        display: inline-block;
        width: 2.5mm; /* Réduit de 3mm à 2.5mm */
        height: 2.5mm;
        border: 1px solid #000;
        margin-right: 0.8mm; /* Réduit de 1mm à 0.8mm */
        vertical-align: middle;
        text-align: center;
        line-height: 2mm; /* Ajusté à la nouvelle taille */
        font-size: 6.5pt; /* Réduit de 7pt à 6.5pt */
        font-family: "DejaVu Sans";
    }

    .checkbox-symbol.checked::before {
        content: "✓";
    }

    .checkbox-symbol.empty::before {
        content: "";
    }

    /* ================= GRILLE LOGICIELS COMPACTE ================= */
    .software-grid-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.3mm; /* Réduit de 0.5mm à 0.3mm */
    }
    
    .software-grid-table td {
        width: 33.33%;
        padding: 0.1mm 0.8mm; /* Réduit le padding */
        vertical-align: top;
        border: none;
    }
    
    .software-cell {
        padding: 0.1mm 0; /* Réduit de 0.2mm à 0.1mm */
        font-size: 7.5pt; /* Réduit de 8pt à 7.5pt */
        line-height: 1.1;
        min-height: 3.5mm; /* Réduit de 4mm à 3.5mm */
    }

    /* ================= SIGNATURES COMPACTES ================= */
    .signature-box {
        margin-top: 0.3mm; /* Réduit de 0.5mm à 0.3mm */
        padding-top: 0.2mm; /* Réduit de 0.3mm à 0.2mm */
        border-top: 1px solid #000;
    }

    .signature-label {
        font-weight: bold;
        margin-bottom: 0.2mm; /* Réduit de 0.3mm à 0.2mm */
        font-size: 8pt; /* Réduit de 8.5pt à 8pt */
    }
    
    .signature-field {
        margin-bottom: 0.1mm; /* Réduit de 0.2mm à 0.1mm */
        font-size: 8pt; /* Réduit de 8.5pt à 8pt */
        line-height: 1;
    }
    
    .signature-underline {
        border-bottom: none;
        padding-bottom: 0.1mm; /* Réduit de 0.2mm à 0.1mm */
        min-width: 35mm; /* Réduit de 40mm à 35mm */
        display: inline-block;
    }
    
    .signature-img {
        max-width: 50mm; /* Réduit de 60mm à 50mm */
        max-height: 12mm; /* Réduit de 15mm à 12mm */
        margin-top: 0.2mm; /* Réduit de 0.3mm à 0.2mm */
        border: 1px solid #ccc;
    }

    /* ================= SIGNATURES EN 2 COLONNES ================= */
    .signature-columns {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1mm; /* Réduit de 2mm à 1mm */
    }
    
    .signature-columns td {
        padding: 0 0.5mm; /* Réduit le padding */
        vertical-align: top;
    }

    /* ================= NOTE FINALE ================= */
    .final-note {
        margin-top: 0.3mm; /* Réduit de 0.5mm à 0.3mm */
        font-size: 6.5pt; /* Réduit de 7pt à 6.5pt */
        font-style: italic;
        padding: 0.8mm; /* Réduit de 1mm à 0.8mm */
        line-height: 1.1;
        border: 1px dashed #999;
        background-color: #f9f9f9;
        text-align: center;
    }

    /* ================= CLASSES DE COMPACTAGE ================= */
    .compact {
        line-height: 1;
    }
    
    .extra-compact .checkbox-item {
        margin-bottom: 0.1mm; /* Réduit de 0.2mm à 0.1mm */
        font-size: 7.2pt; /* Réduit de 7.5pt à 7.2pt */
    }
    
    .extra-compact .software-cell {
        padding: 0.1mm 0;
        font-size: 7.2pt; /* Réduit de 7.5pt à 7.2pt */
        min-height: 3mm; /* Réduit de 3.5mm à 3mm */
    }

    /* ================= PAGE BREAK CONTROL ================= */
    .page-break-control {
        page-break-inside: avoid;
        page-break-after: avoid;
        page-break-before: avoid;
    }

    /* ================= ESPACE MISE EN PLACE RACCOURCIS ================= */
    .raccourcis-section {
        display: none; /* MASQUÉ POUR GAGNER DE LA PLACE */
    }
</style>
</head>
<body class="page-break-control">

@php
    function isChecked($checklist, $key) {
        if (!isset($checklist[$key])) {
            return false;
        }
        
        $value = $checklist[$key];
        
        if (is_bool($value)) {
            return $value;
        }
        
        if (is_numeric($value)) {
            return (int)$value === 1;
        }
        
        if (is_string($value)) {
            $value = strtolower(trim($value));
            return in_array($value, ['1', 'true', 'on', 'yes', 'vrai', 'oui', 'x', '✓']);
        }
        
        return false;
    }
    
    $logoUrl = null;
    if (file_exists(public_path('logo_Cofina.png'))) {
        $imageData = file_get_contents(public_path('logo_Cofina.png'));
        $logoUrl = 'data:image/png;base64,' . base64_encode($imageData);
    } elseif (file_exists(public_path('logo_Cofina.jpg'))) {
        $imageData = file_get_contents(public_path('logo_Cofina.jpg'));
        $logoUrl = 'data:image/jpeg;base64,' . base64_encode($imageData);
    }
    
    $checklist = $data['checklist'] ?? [];
    
    $dateApplication = $data['date_application'] ?? '.../....../......';
    if ($dateApplication && $dateApplication != '.../....../......') {
        try {
            $dateApplication = \Carbon\Carbon::parse($dateApplication)->format('d/m/Y');
        } catch (Exception $e) {}
    }
    
    $dateInstallation = $data['date_installation'] ?? '.../....../......';
    if ($dateInstallation && $dateInstallation != '.../...../....') {
        try {
            $dateInstallation = \Carbon\Carbon::parse($dateInstallation)->format('d/m/Y');
        } catch (Exception $e) {}
    }
    
    $dateVerification = $data['date_verification'] ?? '...../....../......';
    if ($dateVerification && $dateVerification != '...../....../......') {
        try {
            $dateVerification = \Carbon\Carbon::parse($dateVerification)->format('d/m/Y');
        } catch (Exception $e) {}
    }
    
    $agenceNom = $data['agence_nom'] ?? '';
    
    $installateurNom = $data['installateur_nom'] ?? '';
    $installateurPrenom = $data['installateur_prenom'] ?? '';
    $installateurFonction = $data['installateur_fonction'] ?? '';
    $sn = $data['sn'] ?? '';
    
    $verificateurNom = $data['verificateur_nom'] ?? '';
    $verificateurPrenom = $data['verificateur_prenom'] ?? '';
    $verificateurFonction = $data['verificateur_fonction'] ?? '';
    
    $utilisateurNom = $data['utilisateur_nom'] ?? $data['user_name'] ?? '';
    $utilisateurPrenom = $data['utilisateur_prenom'] ?? $data['user_prenom'] ?? '';
    $utilisateurFonction = $data['utilisateur_fonction'] ?? $data['poste_affecte'] ?? '';
    
    $signatureInstallateur = $data['signature_installateur'] ?? null;
    $signatureVerificateur = $data['signature_verificateur'] ?? $data['signature_verificateur_data'] ?? null;
    $signatureUtilisateur = $data['signature_utilisateur_data'] ?? $data['signature_utilisateur'] ?? null;
@endphp

<?php
// Ajouter la récupération de l'agence
if (isset($data['agency_id']) && !empty($data['agency_id'])) {
    $agency = App\Models\Agency::find($data['agency_id']);
    if ($agency) {
        $agenceNom = $agency->nom;
    }
}
?>

<!-- EN-TÊTE -->
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
            <td class="bottom-title">PROCEDURE D'INSTALLATION DE MACHINES</td>
            <td class="date-cell">
                <strong>Date d'application :</strong> {{ $dateApplication }}
            </td>
        </tr>
    </table>
</div>

<!-- SECTION INSTALLATION -->
<div class="section-container">
    <div class="section-header">INSTALLATION</div>
    <table class="info-table">
        <tr>
            <td>NOM DE L'AGENCE : {{ $agenceNom }}</td>
            <td class="info-date">Date : {{ $dateInstallation }}</td>
        </tr>
        <tr>
            <td colspan="2">Nom : {{ $installateurNom }}</td>
        </tr>
        <tr>
            <td colspan="2">Prénom : {{ $installateurPrenom }}</td>
        </tr>
        <tr>
            <td colspan="2">Fonction : {{ $installateurFonction }}</td>
        </tr>
        <tr>
            <td colspan="2">SN : {{ $sn }}</td>
        </tr>
    </table>
    
    <div class="subsection-title">Prérequis</div>
    <div class="checkbox-list tight-spacing extra-compact">
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'sauvegarde_donnees') ? 'checked' : 'empty' }}"></span>
            Sauvegarde des données par l'utilisateur avec l'assistance de l'IT
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'sauvegarde_outlook') ? 'checked' : 'empty' }}"></span>
            Sauvegarde du fichier .pst d'Outlook
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'sauvegarde_tous_utilisateurs') ? 'checked' : 'empty' }}"></span>
            Sauvegarde des données de tout utilisateur ayant ouvert la session sur la machine
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'reinstallation_os') ? 'checked' : 'empty' }}"></span>
            Réinstallation du Système d'exploitation
        </div>
    </div>

    <div class="subsection-title">Installation de logiciels</div>
    <div class="checkbox-list tight-spacing extra-compact">
        <table class="software-grid-table">
            <tr>
                <td>
                    <div class="software-cell compact">
                        <span class="checkbox-symbol {{ isChecked($checklist, 'logiciels_adobe') ? 'checked' : 'empty' }}"></span> Adobe
                    </div>
                    <div class="software-cell compact">
                        <span class="checkbox-symbol {{ isChecked($checklist, 'logiciels_anydesk') ? 'checked' : 'empty' }}"></span> Any Desk
                    </div>
                    <div class="software-cell compact">
                        <span class="checkbox-symbol {{ isChecked($checklist, 'logiciels_chrome') ? 'checked' : 'empty' }}"></span> Google Chrome
                    </div>
                    <div class="software-cell compact">
                        <span class="checkbox-symbol {{ isChecked($checklist, 'logiciels_zoom') ? 'checked' : 'empty' }}"></span> Zoom / Teams
                    </div>
                    <div class="software-cell compact">
                        <span class="checkbox-symbol {{ isChecked($checklist, 'logiciels_scanner_naps2') ? 'checked' : 'empty' }}"></span> Scanner (NAPS2)
                    </div>
                </td>
                <td>
                    <div class="software-cell compact">
                        <span class="checkbox-symbol {{ isChecked($checklist, 'logiciels_ms_office') ? 'checked' : 'empty' }}"></span> MS Office
                    </div>
                    <div class="software-cell compact">
                        <span class="checkbox-symbol {{ isChecked($checklist, 'logiciels_jre') ? 'checked' : 'empty' }}"></span> JRE 7.40
                    </div>
                    <div class="software-cell compact">
                        <span class="checkbox-symbol {{ isChecked($checklist, 'logiciels_firefox') ? 'checked' : 'empty' }}"></span> Firefox
                    </div>
                    <div class="software-cell compact">
                        <span class="checkbox-symbol {{ isChecked($checklist, 'logiciels_vpn') ? 'checked' : 'empty' }}"></span> VPN Client
                    </div>
                </td>
                <td>
                    <div class="software-cell compact">
                        <span class="checkbox-symbol {{ isChecked($checklist, 'logiciels_kaspersky') ? 'checked' : 'empty' }}"></span> Kaspersky
                    </div>
                    <div class="software-cell compact">
                        <span class="checkbox-symbol {{ isChecked($checklist, 'logiciels_pilotes') ? 'checked' : 'empty' }}"></span> Pilotes
                    </div>
                    <div class="software-cell compact">
                        <span class="checkbox-symbol {{ isChecked($checklist, 'logiciels_imprimante') ? 'checked' : 'empty' }}"></span> Imprimante
                    </div>
                    <div class="software-cell compact">
                        <span class="checkbox-symbol {{ isChecked($checklist, 'logiciels_winrar') ? 'checked' : 'empty' }}"></span> WinRar
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="subsection-title">Autres</div>
    <div class="checkbox-list tight-spacing extra-compact">
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'creation_compte_admin') ? 'checked' : 'empty' }}"></span>
            Création compte administrateur
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'integration_domaine') ? 'checked' : 'empty' }}"></span>
            Intégration dans le domaine
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'parametrage_messagerie') ? 'checked' : 'empty' }}"></span>
            Paramétrage Messagerie
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'partition_disque') ? 'checked' : 'empty' }}"></span>
            Partition du disque dur
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'desactivation_ports_usb') ? 'checked' : 'empty' }}"></span>
            Désactivation ports USB
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'connexion_dossier_partage') ? 'checked' : 'empty' }}"></span>
            Connexion dossier partagé
        </div>
    </div>
    
    <div class="signature-box">
        <div class="signature-label">☑ Signature de l'installateur</div>
        <div class="signature-field">
            Nom : <span class="signature-underline">{{ $installateurNom }}</span>
        </div>
        <div class="signature-field">
            Prénom : <span class="signature-underline">{{ $installateurPrenom }}</span>
        </div>
        <div class="signature-field">
            Fonction : <span class="signature-underline">{{ $installateurFonction }}</span>
        </div>
        @if($signatureInstallateur)
            <img src="{{ $signatureInstallateur }}" class="signature-img" alt="Signature installateur">
        @endif
    </div>
</div>

<!-- SECTION VÉRIFICATION -->
<div class="section-container">
    <div class="section-header">VERIFICATION</div>
    <table class="info-table">
        <tr>
            <td>Nom : {{ $verificateurNom }}</td>
            <td class="info-date">Date : {{ $dateVerification }}</td>
        </tr>
        <tr>
            <td colspan="2">Prénom : {{ $verificateurPrenom }}</td>
        </tr>
        <tr>
            <td colspan="2">Fonction : {{ $verificateurFonction }}</td>
        </tr>
    </table>
    
    <div class="checkbox-section extra-compact">
        <div class="section-title">Vérification</div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'verif_logiciels_installes') ? 'checked' : 'empty' }}"></span>
            Logiciels installés
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'verif_messagerie') ? 'checked' : 'empty' }}"></span>
            Messagerie
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'verif_sauvegarde') ? 'checked' : 'empty' }}"></span>
            Authentification de la sauvegarde des données par l'IT et l'utilisateur
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'verif_integration_ad') ? 'checked' : 'empty' }}"></span>
            Intégration dans l'AD
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'verif_systeme_licence') ? 'checked' : 'empty' }}"></span>
            Système installé et licence
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'verif_restauration') ? 'checked' : 'empty' }}"></span>
            Restauration des données et vérification de l'effectivité des données sur la machine réinstallée de l'utilisateur
        </div>
    </div>
    
    <div class="checkbox-section extra-compact">
        <div class="section-title">Autres</div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'verif_fiche_mouvement') ? 'checked' : 'empty' }}"></span>
            Remplir la fiche de mouvement
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'verif_restriction_web') ? 'checked' : 'empty' }}"></span>
            Restriction des accès web (config Kaspersky)
        </div>
        <div class="checkbox-item compact">
            <span class="checkbox-symbol {{ isChecked($checklist, 'verif_validation_installation') ? 'checked' : 'empty' }}"></span>
            Validation de l'installation
        </div>
    </div>
    
    <!-- Signatures en 2 colonnes -->
    <table class="signature-columns">
        <tr>
            <!-- Signature Utilisateur -->
            <td style="width: 50%;">
                <div class="signature-box">
                    <div class="signature-label">Signature de l'utilisateur</div>
                    @if(!empty($utilisateurNom) || !empty($utilisateurFonction))
                        <div class="signature-field">
                            Nom : <span class="signature-underline">{{ $utilisateurNom }}</span>
                        </div>
                        <div class="signature-field">
                            Prénom : <span class="signature-underline">{{ $utilisateurPrenom }}</span>
                        </div>
                        <div class="signature-field">
                            Fonction : <span class="signature-underline">{{ $utilisateurFonction }}</span>
                        </div>
                    @endif
                    @if($signatureUtilisateur)
                        <img src="{{ $signatureUtilisateur }}" class="signature-img" alt="Signature utilisateur">
                    @endif
                </div>
            </td>
            
            <!-- Signature Vérificateur -->
            <td style="width: 50%;">
                <div class="signature-box">
                    <div class="signature-label">Signature du vérificateur</div>
                    @if(!empty($verificateurNom))
                        <div class="signature-field">
                            Nom : <span class="signature-underline">{{ $verificateurNom }}</span>
                        </div>
                        <div class="signature-field">
                            Prénom : <span class="signature-underline">{{ $verificateurPrenom }}</span>
                        </div>
                        <div class="signature-field">
                            Fonction : <span class="signature-underline">{{ $verificateurFonction }}</span>
                        </div>
                    @endif
                    @if($signatureVerificateur)
                        <img src="{{ $signatureVerificateur }}" class="signature-img" alt="Signature vérificateur">
                    @endif
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- NOTE FINALE -->
<div class="final-note">
    <strong>NB :</strong> Toute installation de machine nécessite le remplissage de cette fiche par l'installateur et le vérificateur qui doivent en garder une copie avant d'acheminer la machine vers le Destinataire.
</div>

</body>
</html>