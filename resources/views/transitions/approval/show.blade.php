@extends('layouts.app')
@section('title', 'Validation de la transition')
@section('content')
<!-- ============================================================
     FICHIER CORRIGÉ FINAL : transitions/approval/show.blade.php
     CORRECTION :
     1. Suppression du doublon $installationChecklist
     2. Correction des champs Expéditeur/Réceptionnaire
     ============================================================ -->

@php
    // ✅ DÉCLARATION UNIQUE DES CHECKLISTS
    $verificationChecklist = $verificationChecklist ?? $formData['verification_checklist'] ?? [];
    $installationChecklist = $installationChecklist ?? $formData['installation_checklist'] ?? [];
@endphp

@php
    // Fonction pour vérifier si une checkbox est cochée
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
            return in_array($value, ['1', 'true', 'on', 'yes']);
        }
        
        return false;
    }
    
    // ✅ PAS DE REDÉCLARATION DE $installationChecklist ICI
    $mouvementData = $formData['mouvement_data'] ?? [];
    $installationData = $formData['installation_data'] ?? [];
    $signatures = $formData['signatures'] ?? [];
    $signatureDetails = $formData['signature_details'] ?? [];
    $attachments = $formData['attachments'] ?? [];
@endphp

<div class="max-w-7xl mx-auto px-4 py-8">
    
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-cofina-red mb-2">
                    📋 Validation de Transition
                </h1>
                <p class="text-gray-600">
                    Demande #{{ str_pad($approval->id, 6, '0', STR_PAD_LEFT) }}
                </p>
            </div>
            <div>
                <a href="{{ route('admin.approvals') }}" class="btn-cofina-outline">
                    ← Retour aux approbations
                </a>
            </div>
        </div>
    </div>

    <!-- Statut -->
    <div class="card-cofina mb-8 {{ $approval->status === 'pending' ? 'border-l-4 border-yellow-500' : ($approval->status === 'approved' ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500') }}">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-cofina-red mb-2">Statut de la demande</h2>
                @if($approval->status === 'pending')
                    <span class="px-4 py-2 rounded-full bg-yellow-100 text-yellow-800 font-bold text-lg">
                        ⏳ En attente de validation
                    </span>
                @elseif($approval->status === 'approved')
                    <span class="px-4 py-2 rounded-full bg-green-100 text-green-800 font-bold text-lg">
                        ✅ Approuvée le {{ $approval->approved_at->format('d/m/Y à H:i') }}
                    </span>
                @elseif($approval->status === 'rejected')
                    <span class="px-4 py-2 rounded-full bg-red-100 text-red-800 font-bold text-lg">
                        ❌ Rejetée le {{ $approval->rejected_at->format('d/m/Y à H:i') }}
                    </span>
                @endif
            </div>
            @if($approval->status === 'approved' && $approval->approver)
                <div class="text-right">
                    <p class="text-sm text-gray-600">Validé par</p>
                    <p class="font-bold">{{ $approval->approver->name }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Informations de la transition -->
    <div class="card-cofina mb-8">
        <h2 class="text-2xl font-bold text-cofina-red mb-6 border-b-2 border-cofina-red pb-3">
            📦 Détails de la transition
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 font-semibold mb-2">Équipement</p>
                <p class="text-lg font-bold text-cofina-red">{{ $approval->equipment->nom ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600">N° Série: {{ $approval->equipment->numero_serie }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 font-semibold mb-2">Transition</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded font-bold">
                        {{ strtoupper($approval->from_status) }}
                    </span>
                    <span class="text-2xl text-gray-400">→</span>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded font-bold">
                        {{ strtoupper($approval->to_status) }}
                    </span>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 font-semibold mb-2">Utilisateur final</p>
                <p class="font-bold">{{ $formData['user_name'] ?? 'N/A' }}</p>
                <p class="font-bold">{{ $formData['user_prenom'] ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600">{{ $formData['user_email'] ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600">{{ $formData['departement'] ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600">{{ $formData['poste_affecte'] ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Indicateur de formulaire actif -->
    @if($approval->status === 'pending' && in_array(strtolower(auth()->user()->role ?? ''), ['super_admin', 'admin']))
    <div class="mb-4 flex justify-center space-x-2" id="formIndicator">
        <button type="button" onclick="showFicheMouvement()" 
                class="px-4 py-2 text-sm rounded-lg border border-gray-300 hover:border-cofina-red hover:bg-red-50 transition-colors">
            📄 Fiche de Mouvement
        </button>
        <button type="button" onclick="showFicheInstallation()" 
                class="px-4 py-2 text-sm rounded-lg border border-cofina-blue bg-blue-50 font-semibold">
            🖥️ Fiche d'Installation
        </button>
    </div>
    @endif

    <!-- =========================================
         FORMULAIRE FICHE DE MOUVEMENT
         ========================================= -->
    @if($approval->status === 'pending' && in_array(strtolower(auth()->user()->role ?? ''), ['super_admin', 'admin']))
    <form method="POST" action="{{ route('transitions.approve', $approval) }}" 
          id="ficheMouvementForm" class="space-y-8 hidden" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="form_type" value="mouvement">

        <div class="card-cofina bg-white border-2 border-cofina-red">
            <!-- En-tête -->
            <div class="bg-cofina-red text-white p-6 -mx-6 -mt-6 mb-6 rounded-t-lg">
                <h1 class="text-2xl font-bold text-center">COFINA SENEGAL - IT</h1>
                <h2 class="text-xl font-bold text-center mt-2">FICHE DE MOUVEMENT DE MATERIEL INFORMATIQUE</h2>
                <div class="mt-4 text-center">
                    <label class="inline-block">
                        Date d'application : 
                        <input type="date" name="date_application_mouvement" 
                               value="{{ $mouvementData['date_application_mouvement'] ?? date('Y-m-d') }}"
                               class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold" required>
                    </label>
                </div>
            </div>

            <!-- Section EXPÉDITEUR et RÉCEPTIONNAIRE -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- ✅ EXPÉDITEUR (Agent IT qui soumet) -->
                <div class="border-2 border-blue-300 rounded-lg p-6 bg-blue-50">
                    <h3 class="text-lg font-bold text-blue-800 mb-4 text-center border-b-2 border-blue-300 pb-2">
                        📤 EXPÉDITEUR (Agent IT)
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Nom :</label>
                            <input type="text" name="expediteur_nom" 
                                   value="{{ $formData['agent_nom'] ?? $mouvementData['expediteur_nom'] ?? auth()->user()->name }}"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-gray-100" readonly>
                                    <label class="block text-sm font-semibold mb-1">Prenom :</label>
                            <input type="text" name="expediteur_prenom" 
                                   value="{{ $formData['agent_prenom'] ?? $mouvementData['expediteur_prenom'] ?? auth()->user()->prenom }}"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-gray-100" readonly>
                                   

                            <p class="text-xs text-gray-500 mt-1">Agent IT qui effectue le mouvement</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Fonction :</label>
                            <input type="text" name="expediteur_fonction" 
                                   value="{{ $formData['agent_fonction'] ?? $mouvementData['expediteur_fonction'] ?? 'AGENT IT' }}"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-gray-100" readonly>
                        </div>
                        
                        @if(!empty($signatures['expediteur']))
                        <div class="mt-4">
                            <p class="text-sm font-semibold text-blue-700 mb-2">✓ Signature déjà fournie</p>
                            <div class="border border-gray-300 rounded p-2 bg-white">
                                <img src="{{ $signatures['expediteur'] }}" 
                                     alt="Signature expéditeur" 
                                     class="max-h-32 mx-auto signature-image">
                            </div>
                            <input type="hidden" name="signature_expediteur_data" 
                                   value="{{ $signatures['expediteur'] }}">
                        </div>
                        @endif
                    </div>
                </div>

                <!-- ✅ RÉCEPTIONNAIRE (Utilisateur final) -->
                <div class="border-2 border-green-300 rounded-lg p-6 bg-green-50">
                    <h3 class="text-lg font-bold text-green-800 mb-4 text-center border-b-2 border-green-300 pb-2">
                        📥 RÉCEPTIONNAIRE (Utilisateur final)
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Nom : *</label>
                            <input type="text" name="receptionnaire_nom" 
                                   value="{{ $formData['user_name'] ?? $mouvementData['receptionnaire_nom'] ?? '' }}"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-gray-100" readonly>

                                   <label class="block text-sm font-semibold mb-1">Prénom : *</label>
                            <input type="text" name="receptionnaire_prenom" 
                                   value="{{ $formData['user_prenom'] ?? $mouvementData['receptionnaire_prenom'] ?? '' }}"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-gray-100" readonly>
                            <p class="text-xs text-gray-500 mt-1">Utilisateur final qui reçoit l'équipement</p>
                        </div>
                        <div>
    <label class="block text-sm font-semibold mb-1">Fonction : *</label>
    <input type="text" name="receptionnaire_fonction" 
           value="{{ $formData['poste_affecte'] ?? $mouvementData['receptionnaire_fonction'] ?? '' }}"
           class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-gray-100" readonly>
</div>
                        
                        @if(!empty($signatures['receptionnaire']))
                        <div class="mt-4">
                            <p class="text-sm font-semibold text-green-700 mb-2">✓ Signature déjà fournie</p>
                            <div class="border border-gray-300 rounded p-2 bg-white">
                                <img src="{{ $signatures['receptionnaire'] }}" 
                                     alt="Signature réceptionnaire" 
                                     class="max-h-32 mx-auto signature-image">
                            </div>
                            <input type="hidden" name="signature_receptionnaire_data" 
                                   value="{{ $signatures['receptionnaire'] }}">
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Section détails du mouvement -->
            <div class="border-2 border-gray-300 rounded-lg p-6 bg-gray-50 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">TYPE DE MATERIEL</label>
                        <input type="text" value="{{ $approval->equipment->type ?? 'N/A' }}"
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">RÉFÉRENCE</label>
                        <input type="text" value="{{ $approval->equipment->numero_serie ?? 'N/A' }}"
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">LIEU DE DÉPART *</label>
                        <input type="text" name="lieu_depart" 
                               value="{{ $mouvementData['lieu_depart'] ?? 'SIEGE COFINA' }}"
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                    </div>
                    <div>
    <label class="block text-sm font-semibold mb-1">DESTINATION *</label>
    <input type="text" name="destination" 
           value="{{ $formData['destination'] ?? $formData['departement'] ?? $mouvementData['destination'] ?? '' }}"
           class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
</div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">MOTIF *</label>
                        <input type="text" name="motif" 
                               value="{{ $mouvementData['motif'] ?? $formData['affectation_reason'] ?? 'DOTATION' }}"
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                    </div>
                </div>
            </div>

            <!-- Section signatures -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                <div class="border-2 border-blue-300 rounded-lg p-6 bg-blue-50">
                    <h4 class="font-bold text-blue-800 mb-3 text-center">Signature de l'expéditeur</h4>
                    <div class="mb-3">
                        <label class="block text-sm font-semibold mb-1">Date : *</label>
                        <input type="date" name="date_expediteur" 
                               value="{{ $mouvementData['date_expediteur'] ?? date('Y-m-d') }}"
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                    </div>
                    @if(empty($signatures['expediteur']))
                    <div class="h-32 border-2 border-dashed border-blue-300 rounded bg-white flex items-center justify-center">
                        <span class="text-sm text-gray-500">Signature Agent IT (à compléter)</span>
                    </div>
                    @else
                    <p class="text-sm text-green-600 font-semibold">✓ Signature déjà fournie</p>
                    @endif
                </div>

                <div class="border-2 border-green-300 rounded-lg p-6 bg-green-50">
                    <h4 class="font-bold text-green-800 mb-3 text-center">Signature du réceptionnaire</h4>
                    <div class="mb-3">
                        <label class="block text-sm font-semibold mb-1">Date : *</label>
                        <input type="date" name="date_receptionnaire" 
                               value="{{ $mouvementData['date_receptionnaire'] ?? date('Y-m-d') }}"
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                    </div>
                    @if(empty($signatures['receptionnaire']))
                    <div class="h-32 border-2 border-dashed border-green-300 rounded bg-white flex items-center justify-center">
                        <span class="text-sm text-gray-500">Signature Utilisateur (à compléter)</span>
                    </div>
                    @else
                    <p class="text-sm text-green-600 font-semibold">✓ Signature déjà fournie</p>
                    @endif
                </div>
            </div>

            <!-- NOTA -->
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                <p class="text-sm font-semibold text-yellow-800">
                    <strong>NOTA :</strong> Tout mouvement de matériel informatique nécessite le remplissage de cette fiche par l'expéditeur et le réceptionnaire qui doivent en garder une copie.
                </p>
            </div>

            <!-- Boutons d'action -->
            <div class="flex gap-4 mt-8 pt-6 border-t-2 border-cofina-red">
                <button type="submit" class="btn-cofina-success flex-1 py-4 text-lg font-bold">
                    ✅ VALIDER LA FICHE MOUVEMENT ET APPROUVER
                </button>
                <button type="button" onclick="showFicheInstallation()" 
                        class="btn-cofina-outline flex-1 py-4 text-lg font-bold">
                    ← Retour à la fiche d'installation
                </button>
            </div>
        </div>
    </form>
    @endif

    <!-- Le reste du code continue normalement... -->
    <!-- Je ne répète pas tout pour gagner de l'espace -->
 <!-- FORMULAIRE FICHE D'INSTALLATION (affiché par défaut) -->
    @if($approval->status === 'pending' && in_array(strtolower(auth()->user()->role ?? ''), ['super_admin', 'admin']))
    <form method="POST" action="{{ route('transitions.approve', $approval) }}" 
          id="ficheInstallationForm" class="space-y-8" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="form_type" value="installation">

        <!-- FICHE D'INSTALLATION COFINA -->
        <div class="card-cofina bg-white border-2 border-cofina-red">
            <!-- En-tête officielle -->
            <div class="bg-cofina-red text-white p-6 -mx-6 -mt-6 mb-6 rounded-t-lg">
                <h1 class="text-2xl font-bold text-center">COFINA SENEGAL - IT</h1>
                <h2 class="text-xl font-bold text-center mt-2">PROCÉDURE D'INSTALLATION DE MACHINES</h2>
                <div class="mt-4 text-center">
                    <label class="inline-block">
                        Date d'application : 
                        <input type="date" name="date_application" 
                               value="{{ $installationData['date_application'] ?? date('Y-m-d') }}"
                               class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold" required>
                    </label>
                </div>
            </div>

            <!-- NOM DE L'AGENCE -->
          <!-- <div class="mb-8 p-4 bg-gray-50 rounded-lg border-2 border-gray-300">
                <label class="block font-bold text-lg mb-2 text-cofina-red">NOM DE L'AGENCE : *</label>
                <input type="text" name="agence_nom" 
                       value="{{ $formData['departement'] ?? $installationData['agence_nom'] ?? '' }}"
                       class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg font-bold text-lg"
                       placeholder="Ex: SIÈGE, AGENCE NORD..." required>
            </div>  -->

            <!-- NOM DE L'AGENCE -->
            <div class="mb-8 p-4 bg-gray-50 rounded-lg border-2 border-gray-300">
                <label class="block font-bold text-lg mb-2 text-cofina-red">NOM DE L'AGENCE : *</label>
                <input type="text" name="agence_nom"
                    value="{{  $installationData['agence_nom'] ?? '' }}"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg font-bold text-lg"
                    placeholder="Ex: SIÈGE, AGENCE NORD..." required>
            </div>
            <!-- SECTION INSTALLATION -->
            <div class="mb-8 border-3 border-blue-600 p-6 rounded-lg bg-blue-50">
                <div class="bg-blue-600 text-white px-4 py-3 -mx-6 -mt-6 mb-6 rounded-t-lg">
                    <h3 class="text-xl font-bold">INSTALLATION</h3>
                    <div class="mt-2">
                        Date : 
                        <input type="date" name="date_installation" 
                               value="{{ $formData['date_affectation'] ?? $installationData['date_installation'] ?? date('Y-m-d') }}"
                               class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold" required>
                    </div>
                </div>

                <!-- Prérequis -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-3 text-blue-800 border-b-2 border-blue-300 pb-2">
                        ☑️ Prérequis
                    </h4>
                    <div class="space-y-2 ml-4">
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[sauvegarde_donnees]" value="1"
                                   class="h-5 w-5 text-blue-600 rounded mt-1 flex-shrink-0"
                                   @checked(isChecked($installationChecklist, 'sauvegarde_donnees'))>
                            <span class="ml-3 text-sm">Sauvegarde des données par l'utilisateur avec l'assistance de l'IT</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[sauvegarde_outlook]" value="1"
                                   class="h-5 w-5 text-blue-600 rounded mt-1 flex-shrink-0"
                                   @checked(isChecked($installationChecklist, 'sauvegarde_outlook'))>
                            <span class="ml-3 text-sm">Sauvegarde du fichier .pst d'Outlook</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[sauvegarde_tous_utilisateurs]" value="1"
                                   class="h-5 w-5 text-blue-600 rounded mt-1 flex-shrink-0"
                                   @checked(isChecked($installationChecklist, 'sauvegarde_tous_utilisateurs'))>
                            <span class="ml-3 text-sm">Sauvegarde des données de tout utilisateur ayant ouvert la session sur la machine</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[reinstallation_os]" value="1"
                                   class="h-5 w-5 text-blue-600 rounded mt-1 flex-shrink-0"
                                   @checked(isChecked($installationChecklist, 'reinstallation_os'))>
                            <span class="ml-3 text-sm font-semibold">Réinstallation du Système d'exploitation</span>
                        </label>
                    </div>
                </div>

                <!-- Installation de logiciels -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-3 text-blue-800 border-b-2 border-blue-300 pb-2">
                        ☑️ Installation de logiciels
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 ml-4">
                        @foreach([
                            'logiciels_adobe' => 'Adobe',
                            'logiciels_ms_office' => 'MS Office',
                            'logiciels_kaspersky' => 'Kaspersky / NetAgent',
                            'logiciels_anydesk' => 'Any Desk',
                            'logiciels_jre' => 'JRE 7.40',
                            'logiciels_pilotes' => 'Pilotes du système',
                            'logiciels_chrome' => 'Google Chrome',
                            'logiciels_firefox' => 'Mozilla Firefox',
                            'logiciels_imprimante' => 'Imprimante',
                            'logiciels_zoom' => 'Zoom / Teams',
                            'logiciels_vpn' => 'VPN Client / Forticlient',
                            'logiciels_winrar' => 'WinRar',
                            'logiciels_scanner_naps2' => 'Scanner (NAPS2, ScanGear Tools)'
                        ] as $key => $label)
                            <label class="flex items-center {{ $key === 'logiciels_scanner_naps2' ? 'col-span-2' : '' }}">
                                <input type="checkbox" name="checklist[{{ $key }}]" value="1"
                                       class="h-4 w-4 text-blue-600 rounded"
                                       @checked(isChecked($installationChecklist, $key))>
                                <span class="ml-2 text-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Mise en place des raccourcis -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-3 text-blue-800 border-b-2 border-blue-300 pb-2">
                        ☑️ Mise en place des raccourcis
                    </h4>
                    <div class="space-y-2 ml-4">
                        @foreach([
                            'raccourcis_nafa' => 'NAFA (explor/maxthon/)',
                            'raccourcis_flexcube' => 'FLEXCUBE (explor/maxthon)',
                            'copie_logiciels_local' => 'Copie logiciels en local',
                            'applications_transfert' => 'Application de Transfert pour les caisses (RIA, Moneygram, WU)',
                            'applications_cc' => 'Application pour les CC (Reiz, Cofinalab, OMB, CréditFlow)'
                        ] as $key => $label)
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[{{ $key }}]" value="1"
                                       class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0"
                                       @checked(isChecked($installationChecklist, $key))>
                                <span class="ml-2 text-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Autres -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-3 text-blue-800 border-b-2 border-blue-300 pb-2">
                        ☑️ Autres
                    </h4>
                    <div class="space-y-2 ml-4">
                        @foreach([
                            'creation_compte_admin' => 'Création d\'un compte administrateur',
                            'integration_domaine' => 'Intégration de la machine dans le domaine',
                            'parametrage_messagerie' => 'Paramétrage Messagerie',
                            'partition_disque' => 'Partition du disque dur',
                            'desactivation_ports_usb' => 'Désactivation les ports USB',
                            'connexion_dossier_partage' => 'Connexion du dossier partagé'
                        ] as $key => $label)
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[{{ $key }}]" value="1"
                                       class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0"
                                       @checked(isChecked($installationChecklist, $key))>
                                <span class="ml-2 text-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Signature installateur -->
                <div class="bg-white p-4 rounded-lg border-2 border-blue-300">
                    <h4 class="font-bold mb-3 text-blue-800">✍️ Signature de l'installateur</h4>
                    <div class="signature-container">
                        <!-- Champs texte -->
                        <div class="mb-3 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1">Nom : *</label>
                                <input type="text" name="installateur_nom" 
                                       value="{{ $formData['agent_nom'] ?? $installationData['installateur_nom'] ?? $signatureDetails['installateur']['nom'] ?? auth()->user()->name }}"
                                       class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">Prénom : *</label>
                                <input type="text" name="installateur_prenom" 
                                       value="{{$formData['agent_prenom'] ?? $installationData['installateur_prenom'] ?? $installationData['installateur']['prenom'] ?? auth()->user()->prenom }}"
                                       
                                       class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                            </div>
                        </div>
                        
                        <div class="mb-3 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1">Fonction :</label>
                                <input type="text" name="installateur_fonction" 
                                       value="{{ $formData['agent_fonction'] ?? $installationData['installateur_fonction'] ?? $signatureDetails['installateur']['fonction'] ?? 'IT' }}"
                                       class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-gray-100" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">Date : *</label>
                                <input type="date" name="date_installation" 
                                       value="{{ $formData['date_affectation'] ?? $installationData['date_installation'] ?? date('Y-m-d') }}"
                                       class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                            </div>
                        </div>
                        
                        <!-- Zone de signature interactive -->
                        
                        
                        <!-- Afficher aussi la signature si elle existe déjà -->
                        @if(!empty($signatures['installateur']))
                        <div class="mt-4">
                            <p class="text-sm font-semibold text-blue-700 mb-2">Signature déjà fournie :</p>
                            <div class="border border-gray-300 rounded p-2 bg-white">
                                <img src="{{ $signatures['installateur'] }}" 
                                     alt="Signature installateur" 
                                     class="max-h-32 mx-auto signature-image">
                                <p class="text-xs text-center text-gray-500 mt-2">Signature de l'installateur</p>
                            </div>
                            <input type="hidden" name="signature_installateur_data" 
                                   value="{{ $signatures['installateur'] }}">
                        </div>
                        @endif
                        
                        <div class="text-center mt-2">
                            <span class="text-sm text-gray-500">Signature Installateur IT</span>
                        </div>
                    </div>
                </div>
            </div>

     
            <!-- ================= SECTION VÉRIFICATION ================= -->
<div class="border-3 border-green-600 p-6 rounded-lg bg-green-50">

    <!-- En-tête -->
    <div class="bg-green-600 text-white px-4 py-3 -mx-6 -mt-6 mb-6 rounded-t-lg">
        <h3 class="text-xl font-bold">VÉRIFICATION</h3>
        <div class="mt-2">
            Date :
            <input type="date"
                   name="date_verification"
                   value="{{ $formData['date_verification'] ?? date('Y-m-d') }}"
                   class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold"
                   required>
        </div>
    </div>

    <!-- ================= CHECKLIST DE VÉRIFICATION ================= -->
    <div class="mb-6">
        <h4 class="font-bold text-lg mb-3 text-green-800 border-b-2 border-green-300 pb-2">
            ☑️ Vérification
        </h4>

        <div class="space-y-3 ml-4">
            @foreach([
                'verif_logiciels_installes' => 'Logiciels installés',
                'verif_messagerie' => 'Messagerie',
                'verif_sauvegarde' => 'Authentification de la sauvegarde des données par l’IT et l’utilisateur',
                'verif_integration_ad' => 'Intégration dans l’AD',
                'verif_systeme_licence' => 'Système installé et licence',
                'verif_restauration' => 'Restauration et vérification des données'
            ] as $key => $label)
                <label class="flex items-start">
                    <input type="checkbox"
                           name="checklist[{{ $key }}]"
                           value="1"
                           
                           class="h-5 w-5 text-green-600 rounded mt-1"
                           @checked(isChecked($verificationChecklist, $key))>
                    <span class="ml-3 font-semibold">{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <!-- ================= AUTRES CONTRÔLES ================= -->
    <div class="mb-6">
        <h4 class="font-bold text-lg mb-3 text-green-800 border-b-2 border-green-300 pb-2">
            ☑️ Autres
        </h4>

        <div class="space-y-3 ml-4">
            <label class="flex items-start">
                <input type="checkbox"
                       name="checklist[verif_fiche_mouvement]"
                       value="1"
                       required
                       class="h-5 w-5 text-green-600 rounded mt-1"
                       @checked(isChecked($verificationChecklist, 'verif_fiche_mouvement'))>
                <span class="ml-3 font-semibold">Remplir la fiche de mouvement</span>
            </label>

            <label class="flex items-start">
                <input type="checkbox"
                       name="checklist[verif_restriction_web]"
                       value="1"
                       class="h-4 w-4 text-green-600 rounded mt-1"
                       @checked(isChecked($verificationChecklist, 'verif_restriction_web'))>
                <span class="ml-3 text-sm">Restriction des accès web (Kaspersky)</span>
            </label>

            <label class="flex items-start">
                <input type="checkbox"
                       name="checklist[verif_validation_installation]"
                       value="1"
                       required
                       class="h-5 w-5 text-green-600 rounded mt-1"
                       @checked(isChecked($verificationChecklist, 'verif_validation_installation'))>
                <span class="ml-3 font-semibold">Validation de l’installation</span>
            </label>
        </div>
    </div>

    <!-- ================= SIGNATURES ================= -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- ===== Signature Utilisateur (lecture seule) ===== -->
       

    <!-- ===== Signature Utilisateur (lecture seule) ===== -->
    <div class="bg-white p-4 rounded-lg border-2 border-gray-300">
        <h4 class="font-bold mb-3 text-gray-800">👤 Signature de l'utilisateur *</h4>

        <div class="grid grid-cols-2 gap-4 mb-3">
            <input type="text"
                   name="user_nom"
                   value="{{ $formData['utilisateur_nom'] ?? '' }}"
                   placeholder="Nom"
                   class="px-3 py-2 border-2 border-gray-300 rounded bg-gray-100"
                   readonly>

            <input type="text"
                   name="user_prenom"
                   value="{{ $formData['utilisateur_prenom'] ?? '' }}"
                   placeholder="Prénom"
                   class="px-3 py-2 border-2 border-gray-300 rounded bg-gray-100"
                   readonly>
        </div>

        <input type="text"
               name="user_fonction"
               value="{{ $formData['poste_affecte'] ?? '' }}"
               class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-gray-100 mb-3"
               readonly>

        @if(!empty($signatures['utilisateur']))
        <div class="border-2 border-gray-300 rounded bg-white h-32 mb-2 flex items-center justify-center">
            <img src="{{ $signatures['utilisateur'] }}" 
                 alt="Signature utilisateur" 
                 class="max-h-28 mx-auto signature-image">
        </div>
        @else
        <div class="border-2 border-gray-300 rounded bg-white h-32 mb-2 flex items-center justify-center">
            <span class="text-sm text-gray-500">Signature Utilisateur (à compléter)</span>
        </div>
        @endif
        
        <!-- Note pour l'utilisateur -->
        <p class="text-xs text-gray-500 text-center mt-2">
            Ces informations sont issues de l'affectation
        </p>
    </div> <!-- Fin de la première div -->

    <!-- ===== Signature Vérificateur ===== -->
    <div class="bg-white p-4 rounded-lg border-2 border-green-300">
        <h4 class="font-bold mb-3 text-green-800">✍️ Signature du vérificateur *</h4>

        <div class="grid grid-cols-2 gap-4 mb-3">
            <input type="text"
                   name="verificateur_nom"
                   value="{{ $formData['verificateur_nom'] ?? '' }}"
                   placeholder="Nom"
                   class="px-3 py-2 border-2 border-gray-300 rounded"
                   required>

            <input type="text"
                   name="verificateur_prenom"
                   value="{{ $formData['verificateur_prenom'] ?? '' }}"
                   placeholder="Prénom"
                   class="px-3 py-2 border-2 border-gray-300 rounded"
                   required>
        </div>

        <input type="text"
               name="verificateur_fonction"
               value="{{ $formData['verificateur_fonction'] ?? 'Super Admin' }}"
               class="w-full px-3 py-2 border-2 border-gray-300 rounded mb-3"
               required>

        <div class="border-2 border-gray-300 rounded bg-white h-32 mb-2">
            <canvas id="signatureCanvasVerificateur" class="w-full h-full"></canvas>
        </div>

        <input type="hidden" name="signature_verificateur" id="signatureVerificateur">
        
        <div class="flex gap-2 mt-2">
            <button type="button" onclick="clearSignature('verificateur')" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-3 rounded text-sm">
                Effacer
            </button>
            <button type="button" onclick="saveSignature('verificateur')" 
                    class="bg-green-500 hover:bg-green-600 text-white py-1 px-3 rounded text-sm">
                Sauvegarder
            </button>
        </div>
    </div> <!-- Fin de la deuxième div -->
    
</div> <!-- Fin de la grille -->
</div>

                <!-- Note importante -->
                <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-500 p-4">
                    <p class="text-sm font-semibold text-yellow-800">
                        <strong>NB :</strong> Toute installation de machine nécessite le remplissage de cette fiche par l'installateur et le vérificateur qui doivent en garder une copie avant d'acheminer la machine vers le Destinataire.
                    </p>
                </div>
            </div>

            <!-- Observations finales -->
            <div class="mt-6">
                <label for="observations" class="block font-bold text-cofina-red mb-2 text-lg">
                    💬 Observations / Remarques (optionnel)
                </label>
                <textarea name="observations" id="observations" rows="4"
                          class="w-full px-4 py-3 border-2 border-cofina-gray rounded-lg focus:border-cofina-red"
                          placeholder="Remarques particulières, problèmes rencontrés, points d'attention...">{{ $formData['observations'] ?? '' }}</textarea>
            </div>

            <!-- Boutons d'action -->
            <div class="flex gap-4 mt-8 pt-6 border-t-2 border-cofina-red">
                <button type="submit" class="btn-cofina-success flex-1 py-4 text-lg font-bold">
                    ✅ VALIDER L'INSTALLATION ET APPROUVER
                </button>
                <button type="button" onclick="showFicheMouvement()" 
                        class="btn-cofina-outline flex-1 py-4 text-lg font-bold">
                    📄 Voir la fiche de mouvement
                </button>
                <button type="button" onclick="openRejectModal()" 
                        class="btn-cofina-danger flex-1 py-4 text-lg font-bold">
                    ❌ REJETER
                </button>
            </div>
        </div>
    </form>
    @endif

    <!-- Si déjà validée/rejetée -->
@if($approval->status !== 'pending')
<div class="card-cofina">
    <h2 class="text-2xl font-bold text-cofina-red mb-6">
        📊 Résumé de la validation
    </h2>

    @if($approval->status === 'approved')
        <div class="bg-green-50 p-6 rounded-lg border-2 border-green-300">
            <p class="font-bold text-lg text-green-800 mb-4">
                ✅ Installation validée par {{ $approval->approver->name }}
            </p>
            <p class="text-sm text-gray-600">
                Date: {{ $approval->approved_at->format('d/m/Y à H:i') }}
            </p>
            @if($approval->validation_notes)
            <div class="mt-4 pt-4 border-t border-green-300">
                <p class="font-semibold mb-2">Observations:</p>
                <p>{{ $approval->validation_notes }}</p>
            </div>
            @endif
            
            <!-- SECTION IMPORT DE DOCUMENTS (NOUVEAU) -->
            <div class="mt-6 pt-6 border-t border-green-300">
                <h3 class="text-xl font-bold text-cofina-red mb-4">
                    📎 Documents associés
                </h3>
                
                <!-- Bouton pour ajouter des documents -->
                <div class="mb-6">
                    <button type="button" onclick="openAttachmentModal()" 
                            class="btn-cofina-outline flex items-center gap-2 hover:bg-blue-50 hover:border-blue-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Ajouter des documents (Images/PDF)
                    </button>
                </div>
                
                <!-- Liste des documents existants -->
                @if(!empty($attachments) && count($attachments) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    @foreach($attachments as $index => $attachment)
                    <div class="border border-gray-300 rounded-lg p-4 bg-white shadow-sm hover:shadow-md transition-shadow">
                        @if(in_array(strtolower(pathinfo($attachment['file'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                        <div class="mb-2">
                            <img src="{{ $attachment['file'] }}" alt="Document" class="w-full h-40 object-cover rounded">
                        </div>
                        @elseif(strtolower(pathinfo($attachment['file'], PATHINFO_EXTENSION)) === 'pdf')
                        <div class="mb-2 bg-red-50 p-4 rounded flex items-center justify-center">
                            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        @endif
                        <div class="text-center">
                            <p class="font-semibold text-sm truncate">{{ $attachment['name'] ?? 'Document' }}</p>
                            <p class="text-xs text-gray-500">{{ $attachment['date'] ?? '' }}</p>
                            <div class="mt-2 flex justify-center gap-2">
                                <a href="{{ $attachment['file'] }}" target="_blank" 
                                   class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">
                                    Voir
                                </a>
                                <button type="button" onclick="removeAttachment('{{ $attachment['file'] }}')"
                                        class="text-xs px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors">
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 mb-2">Aucun document associé</p>
                    <p class="text-sm text-gray-400">Ajoutez des photos de l'installation ou des documents PDF</p>
                </div>
                @endif
            
                <!-- BOUTONS POUR TÉLÉCHARGER LES FICHES (MODIFIÉ ICI) -->
                <div class="mt-8 pt-6 border-t border-green-300">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <a href="{{ route('transitions.fiche-installation.download', $approval->id) }}" 
                           class="btn-cofina-outline flex items-center gap-2 justify-center hover:bg-blue-50 hover:border-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                             Télécharger la fiche d'installation
                        </a>
                        
                        <a href="{{ route('transitions.fiche-mouvement.download', $approval->id) }}" 
                           class="btn-cofina-outline flex items-center gap-2 justify-center hover:bg-red-50 hover:border-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                             Télécharger la fiche de mouvement
                        </a>
                        
                        <a href="{{ route('transitions.approval.download', $approval->id) }}" 
                           class="btn-cofina-outline flex items-center gap-2 justify-center hover:bg-green-50 hover:border-green-500 transition-colors md:col-span-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                             Télécharger le document complet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif($approval->status === 'rejected')
        <div class="bg-red-50 p-6 rounded-lg border-2 border-red-300">
            <p class="font-bold text-lg text-red-800 mb-4">
                ❌ Demande rejetée
            </p>
            <p class="text-sm text-gray-600 mb-2">
                Date: {{ $approval->rejected_at->format('d/m/Y à H:i') }}
            </p>
            @if($approval->rejection_reason)
            <div class="mt-4 pt-4 border-t border-red-300">
                <p class="font-semibold mb-2">Raison:</p>
                <p>{{ $approval->rejection_reason }}</p>
            </div>
            @endif
        </div>
    @endif
</div>
@endif

</div>

<!-- Modal de rejet -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-8 rounded-lg max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-cofina-red mb-4">❌ Rejeter cette demande</h3>
        
        <form method="POST" action="{{ route('transitions.reject', $approval) }}">
            @csrf
            
            <div class="mb-6">
                <label for="raison_rejet" class="block font-bold text-cofina-red mb-2">
                    Raison du rejet *
                </label>
                <textarea name="raison_rejet" id="raison_rejet" rows="5"
                          class="w-full px-4 py-3 border-2 border-cofina-gray rounded-lg"
                          placeholder="Expliquez pourquoi vous rejetez cette demande..."
                          required></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-cofina-danger flex-1">
                    ❌ Confirmer le rejet
                </button>
                <button type="button" onclick="closeRejectModal()" 
                        class="btn-cofina-outline flex-1">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour ajouter des documents (NOUVEAU) -->
<div id="attachmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-8 rounded-lg max-w-lg w-full mx-4">
        <h3 class="text-2xl font-bold text-cofina-red mb-4">📎 Ajouter des documents</h3>
        
        <form id="attachmentForm" method="POST" action="{{ route('transitions.attachments.store', $approval) }}" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block font-bold text-cofina-red mb-2">
                    Nom du document *
                </label>
                <input type="text" name="attachment_name" id="attachment_name"
                       class="w-full px-4 py-3 border-2 border-cofina-gray rounded-lg"
                       placeholder="Ex: Photo installation, Bon de livraison..."
                       required>
            </div>
            
            <div class="mb-6">
                <label class="block font-bold text-cofina-red mb-2">
                    Fichier (Image ou PDF) *
                </label>
                <div class="border-2 border-dashed border-cofina-gray rounded-lg p-6 text-center">
                    <input type="file" name="attachment_file" id="attachment_file" 
                           class="hidden" accept="image/*,.pdf" required>
                    <div id="fileDropArea" class="cursor-pointer">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="text-gray-600 mb-2">Glissez-déposez un fichier ici ou cliquez pour sélectionner</p>
                        <p class="text-sm text-gray-400">Formats acceptés: JPG, PNG, GIF, PDF (Max: 5MB)</p>
                    </div>
                    <div id="filePreview" class="hidden mt-4">
                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div>
                                    <p id="fileName" class="font-semibold"></p>
                                    <p id="fileSize" class="text-sm text-gray-500"></p>
                                </div>
                            </div>
                            <button type="button" onclick="removeFile()" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-cofina-success flex-1">
                    📎 Enregistrer le document
                </button>
                <button type="button" onclick="closeAttachmentModal()" 
                        class="btn-cofina-outline flex-1">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts et modaux -->
 <style>
.signature-image {
    max-height: 120px;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 5px;
    background-color: white;
    display: block;
    margin: 0 auto;
}

canvas {
    display: block;
    cursor: crosshair;
}
</style>
<script>
// Variables globales pour les pads de signature
let signaturePadInstallateur = null;
let signaturePadVerificateur = null;

// Variables globales
let currentForm = 'installation'; // Par défaut, on affiche l'installation

// Affiche la fiche de mouvement
function showFicheMouvement() {
    hideAllForms();
    document.getElementById('ficheMouvementForm').classList.remove('hidden');
    
    // Met à jour l'indicateur
    const buttons = document.querySelectorAll('#formIndicator button');
    if (buttons.length >= 2) {
        buttons[0].classList.remove('border-gray-300');
        buttons[0].classList.add('border-cofina-red', 'bg-red-50', 'font-semibold');
        buttons[1].classList.remove('border-cofina-blue', 'bg-blue-50', 'font-semibold');
        buttons[1].classList.add('border-gray-300');
    }
    
    // Scroll vers le formulaire
    document.getElementById('ficheMouvementForm').scrollIntoView({ behavior: 'smooth' });
    currentForm = 'mouvement';
}

// Affiche la fiche d'installation (par défaut)
function showFicheInstallation() {
    hideAllForms();
    document.getElementById('ficheInstallationForm').classList.remove('hidden');
    
    // Met à jour l'indicateur
    const buttons = document.querySelectorAll('#formIndicator button');
    if (buttons.length >= 2) {
        buttons[1].classList.remove('border-gray-300');
        buttons[1].classList.add('border-cofina-blue', 'bg-blue-50', 'font-semibold');
        buttons[0].classList.remove('border-cofina-red', 'bg-red-50', 'font-semibold');
        buttons[0].classList.add('border-gray-300');
    }
    
    // Scroll vers le formulaire
    document.getElementById('ficheInstallationForm').scrollIntoView({ behavior: 'smooth' });
    currentForm = 'installation';
}

function hideAllForms() {
    document.getElementById('ficheMouvementForm').classList.add('hidden');
    document.getElementById('ficheInstallationForm').classList.add('hidden');
}

// Fonctions pour le modal de rejet
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}

// Fonctions pour le modal d'ajout de documents (NOUVEAU)
function openAttachmentModal() {
    document.getElementById('attachmentModal').classList.remove('hidden');
    document.getElementById('attachmentModal').classList.add('flex');
    resetAttachmentForm();
}

function closeAttachmentModal() {
    document.getElementById('attachmentModal').classList.add('hidden');
    document.getElementById('attachmentModal').classList.remove('flex');
}

function resetAttachmentForm() {
    document.getElementById('attachmentForm').reset();
    document.getElementById('filePreview').classList.add('hidden');
    document.getElementById('fileDropArea').classList.remove('hidden');
}

function removeFile() {
    document.getElementById('attachment_file').value = '';
    resetAttachmentForm();
}
function removeAttachment(fileUrl) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
        // Créer un formulaire pour envoyer les données
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("transitions.attachments.destroy", $approval) }}';
        
        // Ajouter le token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Ajouter la méthode spoofing (DELETE)
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);
        
        // Ajouter l'URL du fichier
        const fileInput = document.createElement('input');
        fileInput.type = 'hidden';
        fileInput.name = 'file_url';
        fileInput.value = fileUrl;
        form.appendChild(fileInput);
        
        // Ajouter le formulaire au DOM et le soumettre
        document.body.appendChild(form);
        form.submit();
    }
}

// Fonctions pour la gestion des signatures
function initSignaturePads() {
    // Initialiser la signature de l'installateur
    const canvasInstallateur = document.getElementById('signatureCanvasInstallateur');
    if (canvasInstallateur) {
        // Initialiser le canvas
        canvasInstallateur.width = canvasInstallateur.offsetWidth;
        canvasInstallateur.height = canvasInstallateur.offsetHeight;
        
        signaturePadInstallateur = new SignaturePad(canvasInstallateur, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });
    }
    
    // Initialiser la signature du vérificateur
    const canvasVerificateur = document.getElementById('signatureCanvasVerificateur');
    if (canvasVerificateur) {
        // Initialiser le canvas
        canvasVerificateur.width = canvasVerificateur.offsetWidth;
        canvasVerificateur.height = canvasVerificateur.offsetHeight;
        
        signaturePadVerificateur = new SignaturePad(canvasVerificateur, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });
    }
}

// Effacer une signature
function clearSignature(type) {
    if (type === 'installateur' && signaturePadInstallateur) {
        signaturePadInstallateur.clear();
        document.getElementById('signatureInstallateur').value = '';
    } else if (type === 'verificateur' && signaturePadVerificateur) {
        signaturePadVerificateur.clear();
        document.getElementById('signatureVerificateur').value = '';
    }
}

// Sauvegarder une signature
function saveSignature(type) {
    let signaturePad, hiddenInput;
    
    if (type === 'installateur') {
        signaturePad = signaturePadInstallateur;
        hiddenInput = document.getElementById('signatureInstallateur');
    } else if (type === 'verificateur') {
        signaturePad = signaturePadVerificateur;
        hiddenInput = document.getElementById('signatureVerificateur');
    }
    
    if (signaturePad && !signaturePad.isEmpty()) {
        const dataURL = signaturePad.toDataURL('image/png');
        hiddenInput.value = dataURL;
        alert('Signature sauvegardée avec succès!');
    } else {
        alert('Veuillez signer avant de sauvegarder!');
    }
}

// Gestion du drag and drop pour les fichiers
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les pads de signature
    initSignaturePads();
    
    // Redimensionner les canvas lors du redimensionnement de la fenêtre
    window.addEventListener('resize', function() {
        // Redimensionner le canvas de l'installateur
        const canvasInstallateur = document.getElementById('signatureCanvasInstallateur');
        if (canvasInstallateur) {
            canvasInstallateur.width = canvasInstallateur.offsetWidth;
            canvasInstallateur.height = canvasInstallateur.offsetHeight;
            if (signaturePadInstallateur) {
                signaturePadInstallateur.clear();
            }
        }
        
        // Redimensionner le canvas du vérificateur
        const canvasVerificateur = document.getElementById('signatureCanvasVerificateur');
        if (canvasVerificateur) {
            canvasVerificateur.width = canvasVerificateur.offsetWidth;
            canvasVerificateur.height = canvasVerificateur.offsetHeight;
            if (signaturePadVerificateur) {
                signaturePadVerificateur.clear();
            }
        }
    });
    
    // Gestion du clic en dehors des modaux pour les fermer
    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });
    
    document.getElementById('attachmentModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeAttachmentModal();
    });

    // Gestion du drag and drop pour les fichiers
    const fileDropArea = document.getElementById('fileDropArea');
    const fileInput = document.getElementById('attachment_file');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');

    if (fileDropArea) {
        // Clic pour sélectionner un fichier
        fileDropArea.addEventListener('click', () => fileInput.click());

        // Drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileDropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileDropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileDropArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            fileDropArea.classList.add('border-cofina-red', 'bg-red-50');
        }

        function unhighlight() {
            fileDropArea.classList.remove('border-cofina-red', 'bg-red-50');
        }

        fileDropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            handleFiles(files);
        }

        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];
                
                // Vérification de la taille (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Le fichier est trop volumineux. Taille maximum: 5MB');
                    return;
                }
                
                // Vérification du type de fichier
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                if (!validTypes.includes(file.type)) {
                    alert('Type de fichier non supporté. Formats acceptés: JPG, PNG, GIF, PDF');
                    return;
                }
                
                // Afficher la prévisualisation
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileDropArea.classList.add('hidden');
                filePreview.classList.remove('hidden');
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    }
});
</script>
<script>
// Dans votre JavaScript, ajouter :
document.querySelector('select[name="agency_id"]').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const agenceNom = selectedOption.textContent;
    document.getElementById('agence_nom_hidden').value = agenceNom;
});
</script>

<!-- SignaturePad Library -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
@endsection