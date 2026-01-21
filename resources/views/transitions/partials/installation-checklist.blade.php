@extends('layouts.app')

@section('title', 'Validation de la transition')

@section('content')
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
                <p class="font-bold">{{ $data['user_name'] ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600">{{ $data['departement'] ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600">{{ $data['poste_affecte'] ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Formulaire de validation -->
    @if($approval->status === 'pending' && in_array(strtolower(auth()->user()->role ?? ''), ['super_admin', 'admin']))
    <form method="POST" action="{{ route('transitions.approve', $approval) }}" class="space-y-8">
        @csrf

        <!-- FICHE DE MOUVEMENT COFINA -->
        <div class="card-cofina bg-white border-2 border-cofina-red">
            <!-- En-tête officielle -->
            <div class="bg-cofina-red text-white p-6 -mx-6 -mt-6 mb-6 rounded-t-lg">
                <h1 class="text-2xl font-bold text-center">COFINA SENEGAL - IT</h1>
                <h2 class="text-xl font-bold text-center mt-2">FICHE DE MOUVEMENT DE MATERIEL INFORMATIQUE</h2>
                <div class="mt-4 text-center">
                    <label class="inline-block">
                        Date d'application : 
                        <input type="date" name="date_application_mouvement" value="{{ date('Y-m-d') }}"
                               class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold">
                    </label>
                </div>
            </div>

            <!-- Section EXPÉDITEUR et RÉCEPTIONNAIRE -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- EXPÉDITEUR (Agent IT - Pré-rempli) -->
                <div class="border-2 border-blue-300 rounded-lg p-6 bg-blue-50">
                    <h3 class="text-lg font-bold text-blue-800 mb-4 text-center border-b-2 border-blue-300 pb-2">
                        EXPÉDITEUR
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Nom :</label>
                            <input type="text" name="expediteur_nom" value="{{ $data['agent_nom'] ?? '' }}"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Prénom :</label>
                            <input type="text" name="expediteur_prenom" value="{{ $data['agent_prenom'] ?? '' }}"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Fonction :</label>
                            <input type="text" name="expediteur_fonction" value="{{ $data['agent_fonction'] ?? 'AGENT IT' }}"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" readonly>
                        </div>
                    </div>
                </div>

                <!-- RÉCEPTIONNAIRE (Utilisateur final - Pré-rempli) -->
                <div class="border-2 border-green-300 rounded-lg p-6 bg-green-50">
                    <h3 class="text-lg font-bold text-green-800 mb-4 text-center border-b-2 border-green-300 pb-2">
                        RÉCEPTIONNAIRE
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Nom :</label>
                            <input type="text" name="receptionnaire_nom" value="{{ $data['user_name'] ?? '' }}"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Prénom :</label>
                            <input type="text" name="receptionnaire_prenom" value="{{ $data['user_name'] ?? '' }}"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Fonction :</label>
                            <input type="text" name="receptionnaire_fonction" value="{{ $data['poste_affecte'] ?? '' }}"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" readonly>
                        </div>
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
                        <label class="block text-sm font-semibold mb-1">LIEU DE DÉPART</label>
                        <input type="text" name="lieu_depart" value="SIEGE COFINA"
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">DESTINATION</label>
                        <input type="text" name="destination" value="{{ $data['departement'] ?? 'SIEGE COFINA' }}"
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">MOTIF</label>
                        <input type="text" name="motif" value="DOTATION"
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                    </div>
                </div>
            </div>

            <!-- Section signatures -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                <!-- Signature expéditeur -->
                <div class="border-2 border-blue-300 rounded-lg p-6 bg-blue-50">
                    <h4 class="font-bold text-blue-800 mb-3 text-center">Signature de l'expéditeur</h4>
                    <div class="mb-3">
                        <label class="block text-sm font-semibold mb-1">Date :</label>
                        <input type="date" name="date_expediteur" value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                    </div>
                    <div class="h-24 border-2 border-dashed border-blue-300 rounded bg-white flex items-center justify-center">
                        <span class="text-sm text-gray-500">Signature Agent IT</span>
                    </div>
                </div>

                <!-- Signature réceptionnaire -->
                <div class="border-2 border-green-300 rounded-lg p-6 bg-green-50">
                    <h4 class="font-bold text-green-800 mb-3 text-center">Signature du réceptionnaire</h4>
                    <div class="mb-3">
                        <label class="block text-sm font-semibold mb-1">Date :</label>
                        <input type="date" name="date_receptionnaire" value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                    </div>
                    <div class="h-24 border-2 border-dashed border-green-300 rounded bg-white flex items-center justify-center">
                        <span class="text-sm text-gray-500">Signature Utilisateur</span>
                    </div>
                </div>
            </div>

            <!-- NOTA -->
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                <p class="text-sm font-semibold text-yellow-800">
                    <strong>NOTA :</strong> Tout mouvement de matériel informatique nécessite le remplissage de cette fiche par l'expéditeur et le réceptionnaire qui doivent en garder une copie.
                </p>
            </div>
        </div>

        <!-- FICHE D'INSTALLATION COFINA -->
        <div class="card-cofina bg-white border-2 border-cofina-red">
            <!-- En-tête officielle -->
            <div class="bg-cofina-red text-white p-6 -mx-6 -mt-6 mb-6 rounded-t-lg">
                <h1 class="text-2xl font-bold text-center">COFINA SENEGAL - IT</h1>
                <h2 class="text-xl font-bold text-center mt-2">PROCÉDURE D'INSTALLATION DE MACHINES</h2>
                <div class="mt-4 text-center">
                    <label class="inline-block">
                        Date d'application : 
                        <input type="date" name="date_application" value="{{ date('Y-m-d') }}"
                               class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold">
                    </label>
                </div>
            </div>

            <!-- NOM DE L'AGENCE -->
            <div class="mb-8 p-4 bg-gray-50 rounded-lg border-2 border-gray-300">
                <label class="block font-bold text-lg mb-2 text-cofina-red">NOM DE L'AGENCE :</label>
                <input type="text" name="agence_nom" value="{{ $data['departement'] ?? '' }}"
                       class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg font-bold text-lg"
                       placeholder="Ex: SIÈGE, AGENCE NORD...">
            </div>

            <!-- SECTION INSTALLATION -->
            <div class="mb-8 border-3 border-blue-600 p-6 rounded-lg bg-blue-50">
                <div class="bg-blue-600 text-white px-4 py-3 -mx-6 -mt-6 mb-6 rounded-t-lg">
                    <h3 class="text-xl font-bold">INSTALLATION</h3>
                    <div class="mt-2">
                        Date : 
                        <input type="date" name="date_installation" value="{{ $data['date_affectation'] ?? date('Y-m-d') }}"
                               class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold">
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
                                   class="h-5 w-5 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3 text-sm">Sauvegarde des données par l'utilisateur avec l'assistance de l'IT</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[sauvegarde_outlook]" value="1"
                                   class="h-5 w-5 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3 text-sm">Sauvegarde du fichier .pst d'Outlook</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[sauvegarde_tous_utilisateurs]" value="1"
                                   class="h-5 w-5 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3 text-sm">Sauvegarde des données de tout utilisateur ayant ouvert la session sur la machine</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[reinstallation_os]" value="1"
                                   class="h-5 w-5 text-blue-600 rounded mt-1 flex-shrink-0">
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
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[logiciels_adobe]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm">Adobe</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[logiciels_ms_office]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm">MS Office</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[logiciels_kaspersky]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm">Kaspersky / NetAgent</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[logiciels_anydesk]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm">Any Desk</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[logiciels_jre]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm">JRE 7.40</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[logiciels_pilotes]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm">Pilotes du système</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[logiciels_chrome]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm">Google Chrome</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[logiciels_firefox]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm">Mozilla Firefox</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[logiciels_imprimante]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm">Imprimante</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[logiciels_zoom]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm">Zoom / Teams</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[logiciels_vpn]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm">VPN Client / Forticlient</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="checklist[logiciels_winrar]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm">WinRar</span>
                        </label>
                        <label class="flex items-center col-span-2">
                            <input type="checkbox" name="checklist[logiciels_scanner_naps2]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm">Scanner (NAPS2, ScanGear Tools)</span>
                        </label>
                    </div>
                </div>

                <!-- Mise en place des raccourcis -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-3 text-blue-800 border-b-2 border-blue-300 pb-2">
                        ☑️ Mise en place des raccourcis
                    </h4>
                    <div class="space-y-2 ml-4">
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[raccourcis_nafa]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-2 text-sm">NAFA (explor/maxthon/)</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[raccourcis_flexcube]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-2 text-sm">FLEXCUBE (explor/maxthon)</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[copie_logiciels_local]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-2 text-sm">Copie logiciels en local</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[applications_transfert]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-2 text-sm">Application de Transfert pour les caisses (RIA, Moneygram, WU)</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[applications_cc]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-2 text-sm">Application pour les CC (Reiz, Cofinalab, OMB, CréditFlow)</span>
                        </label>
                    </div>
                </div>

                <!-- Autres -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-3 text-blue-800 border-b-2 border-blue-300 pb-2">
                        ☑️ Autres
                    </h4>
                    <div class="space-y-2 ml-4">
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[creation_compte_admin]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-2 text-sm">Création d'un compte administrateur</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[integration_domaine]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-2 text-sm">Intégration de la machine dans le domaine</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[parametrage_messagerie]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-2 text-sm">Paramétrage Messagerie</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[partition_disque]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-2 text-sm">Partition du disque dur</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[desactivation_ports_usb]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-2 text-sm">Désactivation les ports USB</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[connexion_dossier_partage]" value="1"
                                   class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-2 text-sm">Connexion du dossier partagé</span>
                        </label>
                    </div>
                </div>

                <!-- Signature installateur -->
                <div class="bg-white p-4 rounded-lg border-2 border-blue-300">
                    <h4 class="font-bold mb-3 text-blue-800">✍️ Signature de l'installateur</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Nom :</label>
                            <input type="text" name="installateur_nom" value="{{ $data['agent_nom'] ?? '' }}"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Prénom :</label>
                            <input type="text" name="installateur_prenom" value="{{ $data['agent_prenom'] ?? '' }}"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Fonction :</label>
                            <input type="text" name="installateur_fonction" value="IT"
                                   class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION VÉRIFICATION -->
            <div class="border-3 border-green-600 p-6 rounded-lg bg-green-50">
                <div class="bg-green-600 text-white px-4 py-3 -mx-6 -mt-6 mb-6 rounded-t-lg">
                    <h3 class="text-xl font-bold">VÉRIFICATION</h3>
                    <div class="mt-2">
                        Date : 
                        <input type="date" name="date_verification" value="{{ date('Y-m-d') }}"
                               class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold">
                    </div>
                </div>

                <!-- Vérification -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-3 text-green-800 border-b-2 border-green-300 pb-2">
                        ☑️ Vérification
                    </h4>
                    <div class="space-y-3 ml-4">
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_logiciels_installes]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3 font-semibold">Logiciels installés</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_messagerie]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3 font-semibold">Messagerie</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_sauvegarde]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3 font-semibold">Authentification de la sauvegarde des données par l'IT et l'utilisateur</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_integration_ad]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3 font-semibold">Intégration dans l'AD</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_systeme_licence]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3 font-semibold">Système installé et licence</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_restauration]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3 font-semibold">Restauration des données et vérification de l'effectivité des données sur la machine réinstallée de l'utilisateur</span>
                        </label>
                    </div>
                </div>

                <!-- Autres -->
                <div class="mb-6">
                    <h4 class="font-bold text-lg mb-3 text-green-800 border-b-2 border-green-300 pb-2">
                        ☑️ Autres
                    </h4>
                    <div class="space-y-3 ml-4">
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_fiche_mouvement]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3 font-semibold">Remplir la fiche de mouvement</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_restriction_web]" value="1"
                                   class="h-4 w-4 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3 text-sm">Restriction des accès web (config Kaspersky)</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="checklist[verif_validation_installation]" value="1" required
                                   class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                            <span class="ml-3 font-semibold">Validation de l'installation</span>
                        </label>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Signature utilisateur -->
                    <div class="bg-white p-4 rounded-lg border-2 border-green-300">
                        <h4 class="font-bold mb-3 text-green-800">✍️ Signature de l'utilisateur</h4>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-sm font-semibold mb-1">Nom :</label>
                                <input type="text" value="{{ $data['user_name'] ?? '' }}"
                                       class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">Fonction :</label>
                                <input type="text" value="{{ $data['poste_affecte'] ?? '' }}"
                                       class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Signature vérificateur (Super Admin) -->
                    <div class="bg-white p-4 rounded-lg border-2 border-green-300">
                        <h4 class="font-bold mb-3 text-green-800">✍️ Signature du vérificateur *</h4>
                        <div class="space-y-2">
                            <div>
                                <label class="block text-sm font-semibold mb-1">Nom :</label>
                                <input type="text" name="verificateur_nom" required
                                       class="w-full px-3 py-2 border-2 border-green-300 rounded focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">Prénom :</label>
                                <input type="text" name="verificateur_prenom" required
                                       class="w-full px-3 py-2 border-2 border-green-300 rounded focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">Fonction :</label>
                                <input type="text" name="verificateur_fonction" value="Super Admin"
                                       class="w-full px-3 py-2 border-2 border-green-300 rounded focus:border-green-500">
                            </div>
                        </div>
                    </div>
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
                          placeholder="Remarques particulières, problèmes rencontrés, points d'attention..."></textarea>
            </div>

            <!-- Boutons d'action -->
            <div class="flex gap-4 mt-8 pt-6 border-t-2 border-cofina-red">
                <button type="submit" class="btn-cofina-success flex-1 py-4 text-lg font-bold">
                    ✅ VALIDER L'INSTALLATION ET APPROUVER
                </button>
                <button type="button" onclick="openRejectModal()" 
                        class="btn-cofina-danger flex-1 py-4 text-lg font-bold">
                    ❌ REJETER LA DEMANDE
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

<script>
function openRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}

// Fermer en cliquant dehors
document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>

@endsection