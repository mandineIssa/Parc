@extends('layouts.app')
@section('title', 'Ajouter un Équipement')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Ajouter un Équipement</h1>

        <!-- ÉTAPE 1: Sélection Type, Catégorie, Sous-Catégorie -->
        <div class="mb-8 bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">1. Classification de l'équipement</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Type -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Type d'équipement *</label>
                    <select id="type" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required onchange="updateCategories()">
                        <option value="">-- Sélectionner --</option>
                        <option value="Réseau">🌐 Réseau</option>
                        <option value="Électronique">📹 Électronique</option>
                        <option value="Informatique">💻 Informatique</option>
                        <option value="Logiciel">📦 Logiciel</option>
                    </select>
                </div>

                <!-- Catégorie (dynamique selon Type) -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Catégorie *</label>
                    <select id="categorie" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required onchange="updateSousCategories()" disabled>
                        <option value="">-- Sélectionner type d'abord --</option>
                    </select>
                </div>

                <!-- Sous-Catégorie (dynamique selon Catégorie) -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Sous-Catégorie *</label>
                    <select id="sous_categorie" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required onchange="showMainForm()" disabled>
                        <option value="">-- Sélectionner catégorie d'abord --</option>
                    </select>
                </div>
            </div>

            <!-- Message d'instruction -->
            <div id="selection-message" class="mt-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-blue-700 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Sélectionnez le type, la catégorie et la sous-catégorie pour afficher le formulaire correspondant.
                </p>
            </div>
        </div>

        <!-- FORMULAIRE PRINCIPAL (caché au début) -->
        <div id="main-form" class="hidden">
            <form id="equipmentForm" action="{{ route('equipment.store') }}" method="POST" class="bg-white rounded-lg shadow-lg p-8">
                @csrf

                <!-- Champs cachés pour type, catégorie, sous-catégorie -->
                <input type="hidden" name="type" id="hidden-type">
                <input type="hidden" name="categorie" id="hidden-category">
                <input type="hidden" name="sous_categorie" id="hidden-sub-category">
                <!-- Informations communes -->
                <div class="mb-8 pb-8 border-b">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">2. Informations de base</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- N° Série -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">N° Série *</label>
                            <input type="text" name="numero_serie" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" required>
                        </div>

                        <!-- Marque -->
                        <div id="field-marque" class="">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Marque *</label>
                            <input type="text" name="marque" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" required>
                        </div>

                        <!-- Modèle -->
                        <div id="field-modele" class="">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Modèle *</label>
                            <input type="text" name="modele" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" required>
                        </div>

                        <!-- Agence 
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Agence *</label>
                            <select name="agency_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->nom }}</option>
                                @endforeach
                            </select>
                        </div>-->

                        <!-- Fournisseur -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Fournisseur</label>
                            <select name="fournisseur_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                                <option value="">-- Sélectionner --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                                                
                        
                        <!-- Garantie -->
                        <div id="field-modele" class="">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Garantie *</label>
                            <input type="text" name="garantie" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" required>
                        </div>
                        <!-- Date Livraison -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Date Livraison *</label>
                            <input type="date" name="date_livraison" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" required>
                        </div>

                        <!-- Prix -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Prix (FCFA) *</label>
                            <input type="number" name="prix" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" required>
                        </div>
                          
                         <!-- refernece facture  -->
                        <div id="field-modele" class="">
                            <label class="block text-sm font-bold text-gray-700 mb-2">REF Facture</label>
                            <input type="text" name="reference_facture" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" required>
                        </div>

                        <!-- Localisation -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">LocalIT</label>
                            <input type="text" name="localisation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                        </div>

                        <!-- État -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">État *</label>
                            <select name="etat" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="neuf">Neuf</option>
                                <option value="bon">Bon</option>
                                <option value="moyen">Moyen</option>
                                <option value="mauvais">Mauvais</option>
                            </select>
                        </div>
                        <div>
                             <label class="block text-sm font-bold text-gray-700 mb-2">Adresse MAC</label>
                             <input type="text" name="adresse_mac" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" placeholder="00:11:22:33:44:55">
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="contrat_maintenance" value="1" class="mr-2" onchange="toggleContractFields()">
                                <span class="text-sm font-bold text-gray-700">Contrat Maintenance</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Champs Spécifiques (dynamiques) -->
                <div id="specificFields" class="mb-8 pb-8 border-b">
                    <!-- Section Réseau -->
                    <div id="section-reseau" class="hidden">
                        <!-- Section commune réseau -->
                        <!--<div class="mb-8 pb-8 border-b">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">3. Informations Réseau</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">État *</label>
                                    <select name="etat_reseau" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                                        <option value="">-- Sélectionner --</option>
                                        <option value="En stock">En stock</option>
                                        <option value="Doté">Doté</option>
                                        <option value="Mise en rebus">Mise en rebus</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Adresse MAC</label>
                                    <input type="text" name="adresse_mac" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" placeholder="00:11:22:33:44:55">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Adresse IP</label>
                                    <input type="text" name="adresse_ip" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" placeholder="192.168.1.1">
                                </div>
                            </div>
                        </div>-->

                        <!-- Sous-catégories Réseau Détails -->
                        <div id="subcategory-reseau-details" class="mt-6">
                            <!-- Switches (L2/L3) -->
                            <div id="subcat-switches" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🖧 Détails Switches (L2/L3)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_switch" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="L2">L2</option>
                                            <option value="L3">L3</option>
                                            <option value="empilable">Empilable</option>
                                            <option value="manageable">Manageable</option>
                                            <option value="non_manageable">Non-manageable</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ports Ethernet *</label>
                                        <input type="number" name="ports_ethernet" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 24">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ports PoE</label>
                                        <input type="number" name="ports_poe" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 8">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Puissance PoE totale (W)</label>
                                        <input type="number" name="puissance_poe_totale" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 240">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Vitesse ports *</label>
                                        <select name="vitesse_ports" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="100M">100 Mbps</option>
                                            <option value="1G">1 Gbps</option>
                                            <option value="10G">10 Gbps</option>
                                            <option value="25G">25 Gbps</option>
                                            <option value="40G">40 Gbps</option>
                                            <option value="100G">100 Gbps</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">VLAN supportés</label>
                                        <input type="number" name="vlan_supportes" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 256">
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Protocoles supportés</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_switch[]" value="STP" class="mr-1">
                                                <span class="text-sm">STP</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_switch[]" value="RSTP" class="mr-1">
                                                <span class="text-sm">RSTP</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_switch[]" value="OSPF" class="mr-1">
                                                <span class="text-sm">OSPF</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_switch[]" value="BGP" class="mr-1">
                                                <span class="text-sm">BGP</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Firmware version</label>
                                        <input type="text" name="firmware_switch" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: V15.2(4)E5">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_switch" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_switch" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="fonctionnel">Fonctionnel</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="hors_service">Hors service</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_switch" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Réseau IT">
                                    </div>
                                </div>
                            </div>

                            <!-- Routeurs -->
                            <div id="subcat-routeurs" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🛣️ Détails Routeurs</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_routeur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="edge">Edge</option>
                                            <option value="core">Core</option>
                                            <option value="branch">Branch</option>
                                            <option value="VPN">VPN</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ports *</label>
                                        <input type="number" name="nombre_ports_routeur" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 8">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Débit max (Mbps)</label>
                                        <input type="number" name="debit_max_routeur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 1000">
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Protocoles supportés</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_routeur[]" value="BGP" class="mr-1">
                                                <span class="text-sm">BGP</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_routeur[]" value="OSPF" class="mr-1">
                                                <span class="text-sm">OSPF</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_routeur[]" value="RIP" class="mr-1">
                                                <span class="text-sm">RIP</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_routeur[]" value="NAT" class="mr-1">
                                                <span class="text-sm">NAT</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Interfaces</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="interfaces_routeur[]" value="Ethernet" class="mr-1">
                                                <span class="text-sm">Ethernet</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="interfaces_routeur[]" value="Fibre" class="mr-1">
                                                <span class="text-sm">Fibre</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="interfaces_routeur[]" value="WAN" class="mr-1">
                                                <span class="text-sm">WAN</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="interfaces_routeur[]" value="SFP" class="mr-1">
                                                <span class="text-sm">SFP</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Firmware version</label>
                                        <input type="text" name="firmware_routeur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: IOS 15.4">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_routeur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_routeur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="maintenance">Maintenance</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_routeur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Admin Réseau">
                                    </div>
                                </div>
                            </div>

                            <!-- Points d'accès Wi-Fi -->
                            <div id="subcat-wifi" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">📶 Détails Points d'Accès Wi-Fi</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_wifi" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="autonome">AP Autonome</option>
                                            <option value="controle">AP Contrôlé</option>
                                            <option value="cloud">Cloud-managed</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Normes Wi-Fi supportées</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="normes_wifi[]" value="802.11a" class="mr-1">
                                                <span class="text-sm">802.11a</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="normes_wifi[]" value="802.11b" class="mr-1">
                                                <span class="text-sm">802.11b</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="normes_wifi[]" value="802.11g" class="mr-1">
                                                <span class="text-sm">802.11g</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="normes_wifi[]" value="802.11n" class="mr-1">
                                                <span class="text-sm">802.11n</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="normes_wifi[]" value="802.11ac" class="mr-1">
                                                <span class="text-sm">802.11ac</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="normes_wifi[]" value="802.11ax" class="mr-1">
                                                <span class="text-sm">802.11ax (Wi-Fi 6)</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Bandes supportées</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="bandes_wifi[]" value="2.4GHz" class="mr-1">
                                                <span class="text-sm">2.4 GHz</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="bandes_wifi[]" value="5GHz" class="mr-1">
                                                <span class="text-sm">5 GHz</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="bandes_wifi[]" value="dual" class="mr-1">
                                                <span class="text-sm">Dual-band</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Sécurité supportée</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="securite_wifi[]" value="WPA2" class="mr-1">
                                                <span class="text-sm">WPA2</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="securite_wifi[]" value="WPA3" class="mr-1">
                                                <span class="text-sm">WPA3</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="securite_wifi[]" value="802.1X" class="mr-1">
                                                <span class="text-sm">802.1X</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Utilisateurs simultanés</label>
                                        <input type="number" name="utilisateurs_simultanes_wifi" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 50">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Support PoE</label>
                                        <select name="support_poe_wifi" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Firmware version</label>
                                        <input type="text" name="firmware_wifi" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 5.2.3.1">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_wifi" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_wifi" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="maintenance">Maintenance</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_wifi" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service IT">
                                    </div>
                                </div>
                            </div>

                            <!-- Modems -->
                            <div id="subcat-modems" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🌐 Détails Modems</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_modem" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="DSL">DSL</option>
                                            <option value="fibre">Fibre</option>
                                            <option value="cable">Câble</option>
                                            <option value="4G">4G/LTE</option>
                                            <option value="5G">5G</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Vitesse max (Mbps) *</label>
                                        <input type="number" name="vitesse_max_modem" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 1000">
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Protocoles supportés</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_modem[]" value="PPPoE" class="mr-1">
                                                <span class="text-sm">PPPoE</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_modem[]" value="DHCP" class="mr-1">
                                                <span class="text-sm">DHCP</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_modem[]" value="NAT" class="mr-1">
                                                <span class="text-sm">NAT</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_modem[]" value="IPv6" class="mr-1">
                                                <span class="text-sm">IPv6</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Interfaces LAN</label>
                                        <input type="number" name="interfaces_lan_modem" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 4">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_modem" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_modem" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="maintenance">Maintenance</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_modem" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Réseau">
                                    </div>
                                </div>
                            </div>

                            <!-- Convertisseurs Fibre -->
                            <div id="subcat-convertisseurs" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🔄 Détails Convertisseurs Fibre</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_convertisseur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="SFP">SFP</option>
                                            <option value="GBIC">GBIC</option>
                                            <option value="media_converter">Media Converter</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Vitesse supportée *</label>
                                        <select name="vitesse_convertisseur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="100M">100 Mbps</option>
                                            <option value="1G">1 Gbps</option>
                                            <option value="10G">10 Gbps</option>
                                            <option value="25G">25 Gbps</option>
                                            <option value="40G">40 Gbps</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type fibre *</label>
                                        <select name="type_fibre_convertisseur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="SM">Single Mode (SM)</option>
                                            <option value="MM">Multi Mode (MM)</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Distance max (km)</label>
                                        <input type="number" step="0.1" name="distance_max_convertisseur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 10.0">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Connecteur *</label>
                                        <select name="connecteur_convertisseur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="LC">LC</option>
                                            <option value="SC">SC</option>
                                            <option value="ST">ST</option>
                                            <option value="FC">FC</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alimentation</label>
                                        <select name="alimentation_convertisseur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="PoE">PoE</option>
                                            <option value="12V">12V DC</option>
                                            <option value="5V">5V DC</option>
                                            <option value="220V">220V AC</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_convertisseur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_convertisseur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="maintenance">Maintenance</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_convertisseur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Fibre">
                                    </div>
                                </div>
                            </div>

                            <!-- Pare-feu (Firewall) -->
                            <div id="subcat-firewall" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🛡️ Détails Pare-feu (Firewall)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_firewall" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="hardware">Hardware</option>
                                            <option value="virtuel">Virtuel</option>
                                            <option value="cloud">Cloud</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Débit firewall (Gbps)</label>
                                        <input type="number" step="0.1" name="debit_firewall" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 5.0">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Débit VPN (Gbps)</label>
                                        <input type="number" step="0.1" name="debit_vpn_firewall" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 2.0">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Interfaces réseau</label>
                                        <input type="number" name="interfaces_firewall" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 8">
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fonctions supportées</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="fonctions_firewall[]" value="NAT" class="mr-1">
                                                <span class="text-sm">NAT</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="fonctions_firewall[]" value="ACL" class="mr-1">
                                                <span class="text-sm">ACL</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="fonctions_firewall[]" value="DPI" class="mr-1">
                                                <span class="text-sm">DPI</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="fonctions_firewall[]" value="VPN" class="mr-1">
                                                <span class="text-sm">VPN</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Haute disponibilité</label>
                                        <select name="haute_disponibilite_firewall" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif_passif">Actif/Passif</option>
                                            <option value="actif_actif">Actif/Actif</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Firmware version</label>
                                        <input type="text" name="firmware_firewall" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: PAN-OS 10.2">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Contrat support</label>
                                        <select name="contrat_support_firewall" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date fin support</label>
                                        <input type="date" name="date_fin_support_firewall" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_firewall" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="hors_service">Hors service</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_firewall" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Sécurité IT">
                                    </div>
                                </div>
                            </div>

                            <!-- UTM / Appliances de sécurité -->
                            <div id="subcat-utm" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🛡️ Détails UTM / Appliances de sécurité</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fonctions activées</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="fonctions_utm[]" value="firewall" class="mr-1">
                                                <span class="text-sm">Firewall</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="fonctions_utm[]" value="antivirus" class="mr-1">
                                                <span class="text-sm">Antivirus</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="fonctions_utm[]" value="antispam" class="mr-1">
                                                <span class="text-sm">Antispam</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="fonctions_utm[]" value="filtrage_web" class="mr-1">
                                                <span class="text-sm">Filtrage Web</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="fonctions_utm[]" value="ids_ips" class="mr-1">
                                                <span class="text-sm">IDS/IPS</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Débit global UTM (Mbps)</label>
                                        <input type="number" name="debit_utm" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Utilisateurs supportés</label>
                                        <input type="number" name="utilisateurs_utm" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 100">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Interfaces réseau</label>
                                        <input type="number" name="interfaces_utm" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 6">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">VPN supporté</label>
                                        <select name="vpn_utm" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="IPsec">IPsec</option>
                                            <option value="SSL">SSL</option>
                                            <option value="les_deux">Les deux</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type licence</label>
                                        <input type="text" name="type_licence_utm" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Annuelle">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date expiration licence</label>
                                        <input type="date" name="date_expiration_licence_utm" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Firmware version</label>
                                        <input type="text" name="firmware_utm" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: UTM 9.1">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_utm" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="maintenance">Maintenance</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_utm" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Sécurité IT">
                                    </div>
                                </div>
                            </div>

                            <!-- Passerelles VPN -->
                            <div id="subcat-vpn" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🔐 Détails Passerelles VPN</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_vpn" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="materiel">Matériel</option>
                                            <option value="logiciel">Logiciel</option>
                                            <option value="cloud">Cloud</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Protocoles VPN</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_vpn[]" value="IPsec" class="mr-1">
                                                <span class="text-sm">IPsec</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_vpn[]" value="SSL" class="mr-1">
                                                <span class="text-sm">SSL</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_vpn[]" value="L2TP" class="mr-1">
                                                <span class="text-sm">L2TP</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="protocoles_vpn[]" value="OpenVPN" class="mr-1">
                                                <span class="text-sm">OpenVPN</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tunnels simultanés</label>
                                        <input type="number" name="tunnels_simultanes_vpn" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 100">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Utilisateurs max</label>
                                        <input type="number" name="utilisateurs_max_vpn" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Authentification</label>
                                        <select name="authentification_vpn" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="certificat">Certificat</option>
                                            <option value="LDAP">LDAP</option>
                                            <option value="MFA">MFA</option>
                                            <option value="RADIUS">RADIUS</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Débit VPN max (Mbps)</label>
                                        <input type="number" name="debit_max_vpn" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 1000">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Interfaces réseau</label>
                                        <input type="number" name="interfaces_vpn" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 4">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Firmware version</label>
                                        <input type="text" name="firmware_vpn" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 2.5.1">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_vpn" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="maintenance">Maintenance</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_vpn" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Sécurité Réseau">
                                    </div>
                                </div>
                            </div>

                            <!-- IDS / IPS -->
                            <div id="subcat-ids-ips" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🚨 Détails IDS / IPS</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_ids_ips" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="IDS">IDS</option>
                                            <option value="IPS">IPS</option>
                                            <option value="combiné">IDS/IPS combiné</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mode *</label>
                                        <select name="mode_ids_ips" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="passif">Passif</option>
                                            <option value="inline">Inline</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Débit analyse (Mbps)</label>
                                        <input type="number" name="debit_analyse_ids_ips" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 1000">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Moteur signatures</label>
                                        <select name="moteur_signatures" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Snort">Snort</option>
                                            <option value="Suricata">Suricata</option>
                                            <option value="Proprietaire">Propriétaire</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mise à jour signatures</label>
                                        <select name="maj_signatures" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="auto">Auto</option>
                                            <option value="manuel">Manuel</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fréquence mise à jour</label>
                                        <input type="text" name="frequence_maj" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Quotidienne">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Intégration SIEM</label>
                                        <select name="integration_siem" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Événements/jour</label>
                                        <input type="number" name="evenements_par_jour" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 5000">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Firmware version</label>
                                        <input type="text" name="firmware_ids_ips" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 3.2.1">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_ids_ips" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="maintenance">Maintenance</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_ids_ips" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: SOC Team">
                                    </div>
                                </div>
                            </div>

                            <!-- Infrastructure & Support -->
                            <!-- Baies et armoires réseau -->
                            <div id="subcat-baies-armoires" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🗄️ Détails Baies et Armoires Réseau</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_baie" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="baie_19_pouces">Baie 19 pouces</option>
                                            <option value="armoire_murale">Armoire murale</option>
                                            <option value="armoire_plancher">Armoire de plancher</option>
                                            <option value="rack_ouvert">Rack ouvert</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Hauteur (U) *</label>
                                        <input type="number" name="hauteur_baie" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 42">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Largeur (mm)</label>
                                        <input type="number" name="largeur_baie" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 600">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Profondeur (mm)</label>
                                        <input type="number" name="profondeur_baie" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 1000">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacité max (kg)</label>
                                        <input type="number" name="capacite_max_baie" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Portes</label>
                                        <select name="portes_baie" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="avant_arriere">Avant et arrière</option>
                                            <option value="avant">Avant uniquement</option>
                                            <option value="aucune">Aucune</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ventilation intégrée</label>
                                        <select name="ventilation_baie" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Éclairage</label>
                                        <select name="eclairage_baie" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Localisation *</label>
                                        <input type="text" name="localisation_baie" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: Salle serveurs, Datacenter">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_baie" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="pleine">Pleine</option>
                                            <option value="partiellement_remplie">Partiellement remplie</option>
                                            <option value="vide">Vide</option>
                                            <option value="maintenance">Maintenance</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_baie" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Infrastructure">
                                    </div>
                                </div>
                            </div>

                            <!-- Panneaux de brassage -->
                            <div id="subcat-panneaux-brassage" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🔌 Détails Panneaux de Brassage</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_panneau_brassage" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="RJ45">RJ45</option>
                                            <option value="fibre">Fibre optique</option>
                                            <option value="coaxial">Coaxial</option>
                                            <option value="mixte">Mixte</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de ports *</label>
                                        <input type="number" name="ports_panneau_brassage" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 48">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                                        <select name="categorie_panneau_brassage" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Cat5e">Cat5e</option>
                                            <option value="Cat6">Cat6</option>
                                            <option value="Cat6a">Cat6a</option>
                                            <option value="Cat7">Cat7</option>
                                            <option value="OM3">OM3 (fibre)</option>
                                            <option value="OM4">OM4 (fibre)</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Hauteur (U)</label>
                                        <input type="number" name="hauteur_panneau_brassage" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 1">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de montage</label>
                                        <select name="montage_panneau_brassage" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="rack">Rack</option>
                                            <option value="mural">Mural</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_panneau_brassage" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="installe">Installé</option>
                                            <option value="en_stock">En stock</option>
                                            <option value="a_installer">À installer</option>
                                            <option value="maintenance">Maintenance</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Localisation</label>
                                        <input type="text" name="localisation_panneau_brassage" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Baie 1, Salle réseau">
                                    </div>
                                </div>
                            </div>

                            <!-- Câblage RJ45 / Fibre optique -->
                            <div id="subcat-cablage" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🔌 Détails Câblage RJ45 / Fibre Optique</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de câble *</label>
                                        <select name="type_cable" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="RJ45">RJ45 (Cuivre)</option>
                                            <option value="fibre_monomode">Fibre Monomode</option>
                                            <option value="fibre_multimode">Fibre Multimode</option>
                                            <option value="coaxial">Coaxial</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie/Classe *</label>
                                        <select name="categorie_cable" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Cat5e">Cat5e</option>
                                            <option value="Cat6">Cat6</option>
                                            <option value="Cat6a">Cat6a</option>
                                            <option value="Cat7">Cat7</option>
                                            <option value="Cat8">Cat8</option>
                                            <option value="OM3">OM3 (fibre)</option>
                                            <option value="OM4">OM4 (fibre)</option>
                                            <option value="OS1">OS1 (monomode)</option>
                                            <option value="OS2">OS2 (monomode)</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Longueur (mètres) *</label>
                                        <input type="number" step="0.1" name="longueur_cable" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 5.0">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de câbles *</label>
                                        <input type="number" name="nombre_cables" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 10">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
                                        <input type="text" name="couleur_cable" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Bleu, Jaune, Rouge">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de gaine</label>
                                        <select name="type_gaine" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="LSZH">LSZH (Low Smoke Zero Halogen)</option>
                                            <option value="PVC">PVC</option>
                                            <option value="plenum">Plenum</option>
                                            <option value="riser">Riser</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Paire blindée (UTP/STP)</label>
                                        <select name="blindage_cable" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="UTP">UTP (Non blindé)</option>
                                            <option value="FTP">FTP</option>
                                            <option value="STP">STP</option>
                                            <option value="SFTP">SFTP</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État</label>
                                        <select name="etat_cable" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="neuf">Neuf</option>
                                            <option value="utilise">Utilisé</option>
                                            <option value="endommage">Endommagé</option>
                                            <option value="recycle">À recycler</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement</label>
                                        <input type="text" name="emplacement_cable" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Baie câblage, Salle réseau">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                        <input type="date" name="date_installation_cable" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                </div>
                            </div>

                            <!-- Onduleurs (UPS) -->
                            <div id="subcat-onduleurs" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">⚡ Détails Onduleurs (UPS)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_onduleur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="offline">Off-line</option>
                                            <option value="line_interactive">Line-interactive</option>
                                            <option value="online">On-line</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Puissance (VA) *</label>
                                        <input type="number" name="puissance_onduleur" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 1500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Puissance (W) *</label>
                                        <input type="number" name="puissance_w_onduleur" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 1000">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Autonomie (minutes)</label>
                                        <input type="number" name="autonomie_onduleur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 30">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Batteries intégrées</label>
                                        <input type="number" name="batteries_onduleur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 2">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type batterie</label>
                                        <select name="type_batterie_onduleur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="AGM">AGM</option>
                                            <option value="Gel">Gel</option>
                                            <option value="Lithium">Lithium-ion</option>
                                            <option value="Plomb">Plomb-acide</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Sorties *</label>
                                        <input type="number" name="sorties_onduleur" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 8">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Interface de gestion</label>
                                        <select name="interface_gestion_onduleur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="USB">USB</option>
                                            <option value="Ethernet">Ethernet</option>
                                            <option value="RS232">RS232</option>
                                            <option value="aucune">Aucune</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Protection</label>
                                        <input type="text" name="protection_onduleur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Surtension, Foudre">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                        <input type="date" name="date_installation_onduleur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_onduleur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="fonctionnel">Fonctionnel</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="batterie_a_remplacer">Batterie à remplacer</option>
                                            <option value="hors_service">Hors service</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_onduleur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Infrastructure">
                                    </div>
                                </div>
                            </div>

                            <!-- PDU (Multiprises intelligentes) -->
                            <div id="subcat-pdu" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🔌 Détails PDU (Multiprises intelligentes)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_pdu" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="basique">Basique</option>
                                            <option value="intelligente">Intelligente</option>
                                            <option value="mesuree">Mesurée</option>
                                            <option value="commutee">Commutée</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de prises *</label>
                                        <input type="number" name="prises_pdu" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 16">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Courant max (A) *</label>
                                        <input type="number" name="courant_max_pdu" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 16">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tension (V) *</label>
                                        <select name="tension_pdu" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="110">110V</option>
                                            <option value="220">220V</option>
                                            <option value="230">230V</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de prise</label>
                                        <select name="type_prise_pdu" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="C13">C13</option>
                                            <option value="C19">C19</option>
                                            <option value="IEC">IEC</option>
                                            <option value="Schuko">Schuko</option>
                                            <option value="mixte">Mixte</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Interface de gestion</label>
                                        <select name="interface_gestion_pdu" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="aucune">Aucune</option>
                                            <option value="RS232">RS232</option>
                                            <option value="Ethernet">Ethernet</option>
                                            <option value="SNMP">SNMP</option>
                                            <option value="Web">Web</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mesures supportées</label>
                                        <input type="text" name="mesures_pdu" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Courant, Tension, Puissance">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Hauteur (U)</label>
                                        <input type="number" name="hauteur_pdu" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 1">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement *</label>
                                        <input type="text" name="emplacement_pdu" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: Baie 1, Salle serveurs">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_pdu" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="hors_service">Hors service</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_pdu" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Infrastructure">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Électronique complète -->
                    <div id="section-electronique" class="hidden">
                        <!--<div class="mb-8 pb-8 border-b">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">3. Informations Électronique</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">État *</label>
                                    <select name="etat_electronique" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                                        <option value="">-- Sélectionner --</option>
                                        <option value="En stock">En stock</option>
                                        <option value="Doté">Doté</option>
                                        <option value="Mise en rebus">Mise en rebus</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Adresse IP</label>
                                    <input type="text" name="adresse_ip_elec" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">N° Codification</label>
                                    <input type="text" name="numero_codification" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                                </div>
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="contrat_maintenance" value="1" class="mr-2" onchange="toggleContractFields()">
                                        <span class="text-sm font-bold text-gray-700">Contrat Maintenance</span>
                                    </label>
                                </div>
                            </div>
                        </div>-->

                        <!-- Sous-catégories Électroniques Détails -->
                        <div id="subcategory-electronique-details" class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <!-- Caméras IP -->
                            <div id="subcat-cameras" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🎥 Détails Caméra IP</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de caméra *</label>
                                        <select name="type_camera" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Fixe">Fixe</option>
                                            <option value="PTZ">PTZ</option>
                                            <option value="Dôme">Dôme</option>
                                            <option value="Bullet">Bullet</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Résolution *</label>
                                        <select name="resolution_camera" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="720p">720p (HD)</option>
                                            <option value="1080p">1080p (Full HD)</option>
                                            <option value="2MP">2MP</option>
                                            <option value="4MP">4MP</option>
                                            <option value="5MP">5MP</option>
                                            <option value="4K">4K (8MP)</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Angle de vue (°)</label>
                                        <input type="number" name="angle_vue" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 90">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Zoom optique (x)</label>
                                        <input type="number" step="0.1" name="zoom_optique" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 3.0">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Zoom numérique (x)</label>
                                        <input type="number" step="0.1" name="zoom_numerique" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 12.0">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Vision nocturne (mètres)</label>
                                        <input type="number" name="vision_nocturne" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 30">
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Compression vidéo</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="compression_video[]" value="H.264" class="mr-1">
                                                <span class="text-sm">H.264</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="compression_video[]" value="H.265" class="mr-1">
                                                <span class="text-sm">H.265</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="compression_video[]" value="H.265+" class="mr-1">
                                                <span class="text-sm">H.265+</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="compression_video[]" value="MJPEG" class="mr-1">
                                                <span class="text-sm">MJPEG</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse IP</label>
                                        <input type="text" name="adresse_ip_camera" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="192.168.1.100">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse MAC</label>
                                        <input type="text" name="adresse_mac_camera" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="00:11:22:33:44:55">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alimentation *</label>
                                        <select name="alimentation_camera" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="PoE">PoE</option>
                                            <option value="12V DC">12V DC</option>
                                            <option value="24V AC">24V AC</option>
                                            <option value="220V">220V</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Norme PoE</label>
                                        <select name="norme_poe" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="">Non applicable</option>
                                            <option value="802.3af">802.3af</option>
                                            <option value="802.3at">802.3at (PoE+)</option>
                                            <option value="802.3bt">802.3bt (PoE++)</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Indice de protection</label>
                                        <select name="indice_protection" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="IP65">IP65</option>
                                            <option value="IP66">IP66</option>
                                            <option value="IP67">IP67</option>
                                            <option value="IP68">IP68</option>
                                            <option value="IP54">IP54 (intérieur)</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Audio</label>
                                        <select name="audio_camera" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="non">Non</option>
                                            <option value="oui">Oui</option>
                                            <option value="bidirectionnel">Bidirectionnel</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Détection intelligente</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="detection_intelligente[]" value="mouvement" class="mr-1">
                                                <span class="text-sm">Mouvement</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="detection_intelligente[]" value="visage" class="mr-1">
                                                <span class="text-sm">Visage</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="detection_intelligente[]" value="intrusion" class="mr-1">
                                                <span class="text-sm">Intrusion</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="detection_intelligente[]" value="ligne_virtuelle" class="mr-1">
                                                <span class="text-sm">Ligne virtuelle</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement physique *</label>
                                        <input type="text" name="emplacement_camera" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: Entrée principale, Parking nord">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                        <input type="date" name="date_installation_camera" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_detaille_camera" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="hors_service">Hors service</option>
                                            <option value="en_test">En test</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_camera" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Sécurité">
                                    </div>
                                </div>
                            </div>

                            <!-- NVR / DVR -->
                            <div id="subcat-nvr-dvr" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">📼 Détails NVR / DVR</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_nvr_dvr" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="NVR">NVR (IP)</option>
                                            <option value="DVR">DVR (Analogique)</option>
                                            <option value="Hybride">Hybride</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Canaux supportés *</label>
                                        <select name="canaux_supportes" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="4">4 canaux</option>
                                            <option value="8">8 canaux</option>
                                            <option value="16">16 canaux</option>
                                            <option value="32">32 canaux</option>
                                            <option value="64">64 canaux</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Résolution max par canal</label>
                                        <input type="text" name="resolution_max_canal" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 4MP @ 15 fps">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Stockage interne (To)</label>
                                        <input type="number" step="0.1" name="stockage_interne" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 2.0">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de disques</label>
                                        <input type="number" name="nombre_disques" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 2">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">RAID supporté</label>
                                        <select name="raid_support" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="RAID 0">RAID 0</option>
                                            <option value="RAID 1">RAID 1</option>
                                            <option value="RAID 5">RAID 5</option>
                                            <option value="RAID 10">RAID 10</option>
                                            <option value="Aucun">Aucun</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Protocoles supportés</label>
                                        <select name="protocoles_supportes[]" class="w-full px-3 py-2 border border-gray-300 rounded" multiple>
                                            <option value="ONVIF">ONVIF</option>
                                            <option value="RTSP">RTSP</option>
                                            <option value="PSIA">PSIA</option>
                                            <option value="GB28181">GB28181</option>
                                            <option value="CGI">CGI</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ports réseau (Gigabit)</label>
                                        <input type="number" name="ports_reseau_nvr" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 2">
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Sorties vidéo</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="sorties_video[]" value="HDMI" class="mr-1">
                                                <span class="text-sm">HDMI</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="sorties_video[]" value="VGA" class="mr-1">
                                                <span class="text-sm">VGA</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="sorties_video[]" value="BNC" class="mr-1">
                                                <span class="text-sm">BNC</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="sorties_video[]" value="DisplayPort" class="mr-1">
                                                <span class="text-sm">DisplayPort</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Accès distant</label>
                                        <select name="acces_distant" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Utilisateurs max</label>
                                        <input type="number" name="utilisateurs_max" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 32">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Firmware version</label>
                                        <input type="text" name="firmware_version" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: V4.30.005">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                        <input type="date" name="date_installation_nvr" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_nvr_dvr" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="en_production">En production</option>
                                            <option value="en_test">En test</option>
                                            <option value="en_maintenance">En maintenance</option>
                                            <option value="hors_service">Hors service</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_nvr" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Surveillance">
                                    </div>
                                </div>
                            </div>

                            <!-- Serveurs d'archivage vidéos -->
                            <div id="subcat-archivage-video" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🗄️ Détails Serveur d'Archivage Vidéo</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_archivage" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Serveur dédié">Serveur dédié</option>
                                            <option value="NAS">NAS</option>
                                            <option value="SAN">SAN</option>
                                            <option value="Baie de stockage">Baie de stockage</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacité totale (To) *</label>
                                        <input type="number" step="0.1" name="capacite_totale_archivage" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 48.0">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">RAID configuré</label>
                                        <select name="raid_archivage" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="RAID 5">RAID 5</option>
                                            <option value="RAID 6">RAID 6</option>
                                            <option value="RAID 10">RAID 10</option>
                                            <option value="RAID 50">RAID 50</option>
                                            <option value="RAID 60">RAID 60</option>
                                            <option value="JBOD">JBOD</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Conservation (jours)</label>
                                        <input type="number" name="duree_conservation" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 90">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Débit écriture (Mbps)</label>
                                        <input type="number" name="debit_ecriture" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Flux supportés</label>
                                        <input type="number" name="flux_supportes" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 128">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">OS / Logiciel VMS</label>
                                        <input type="text" name="os_vms" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Milestone XProtect, Windows Server 2022">
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Redondance</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="redondance[]" value="alimentation" class="mr-1">
                                                <span class="text-sm">Alimentation</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="redondance[]" value="reseau" class="mr-1">
                                                <span class="text-sm">Réseau</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="redondance[]" value="controleurs" class="mr-1">
                                                <span class="text-sm">Contrôleurs</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="redondance[]" value="ventilation" class="mr-1">
                                                <span class="text-sm">Ventilation</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Sauvegarde secondaire</label>
                                        <select name="sauvegarde_secondaire" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Localisation *</label>
                                        <input type="text" name="localisation_archivage" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: Salle serveurs, Datacenter principal">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_archivage" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_archivage" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="en_production">En production</option>
                                            <option value="en_test">En test</option>
                                            <option value="en_maintenance">En maintenance</option>
                                            <option value="hors_service">Hors service</option>
                                            <option value="en_backup">En backup</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_archivage" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Infrastructure">
                                    </div>
                                </div>
                            </div>

                            <!-- Moniteurs de contrôle -->
                            <div id="subcat-moniteurs-controle" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🖥️ Détails Moniteur de Contrôle</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Taille écran (pouces) *</label>
                                        <input type="number" name="taille_ecran_moniteur" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 24">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Résolution *</label>
                                        <select name="resolution_moniteur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="1920x1080">1920x1080 (Full HD)</option>
                                            <option value="2560x1440">2560x1440 (2K)</option>
                                            <option value="3840x2160">3840x2160 (4K)</option>
                                            <option value="1366x768">1366x768 (HD)</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type d'écran *</label>
                                        <select name="type_ecran_moniteur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="LCD">LCD</option>
                                            <option value="LED">LED</option>
                                            <option value="OLED">OLED</option>
                                            <option value="IPS">IPS</option>
                                            <option value="TN">TN</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Entrées vidéo</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="entrees_video[]" value="HDMI" class="mr-1">
                                                <span class="text-sm">HDMI</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="entrees_video[]" value="VGA" class="mr-1">
                                                <span class="text-sm">VGA</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="entrees_video[]" value="DisplayPort" class="mr-1">
                                                <span class="text-sm">DisplayPort</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="entrees_video[]" value="DVI" class="mr-1">
                                                <span class="text-sm">DVI</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="entrees_video[]" value="BNC" class="mr-1">
                                                <span class="text-sm">BNC</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="entrees_video[]" value="Composite" class="mr-1">
                                                <span class="text-sm">Composite</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mode affichage</label>
                                        <select name="mode_affichage" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="mur_images">Mur d'images</option>
                                            <option value="multi_vues">Multi-vues</option>
                                            <option value="plein_ecran">Plein écran</option>
                                            <option value="sequence">Séquence</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Utilisation *</label>
                                        <select name="utilisation_moniteur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="24/7">24/7 (Opérationnel continu)</option>
                                            <option value="standard">Standard (8h/jour)</option>
                                            <option value="surveillance">Surveillance</option>
                                            <option value="affichage">Affichage public</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Support mural</label>
                                        <select name="support_mural" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement *</label>
                                        <input type="text" name="emplacement_moniteur" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: PC Sécurité, Salle de contrôle">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                        <input type="date" name="date_installation_moniteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_moniteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="en_maintenance">En maintenance</option>
                                            <option value="hors_service">Hors service</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_moniteur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Agent de sécurité">
                                    </div>
                                </div>
                            </div>

                            <!-- Badges RFID -->
                            <div id="subcat-badges-rfid" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🪪 Détails Badge RFID</h3>
                                
                                <!-- Type badge -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Type d'élément *</label>
                                    <select name="type_element_rfid" class="w-full px-3 py-2 border border-gray-300 rounded" required onchange="toggleRFIDDetails()">
                                        <option value="">Sélectionner</option>
                                        <option value="badge">Badge RFID</option>
                                        <option value="lecteur">Lecteur RFID</option>
                                    </select>
                                </div>
                                
                                <!-- Badge RFID -->
                                <div id="badge-rfid-details" class="hidden p-4 bg-blue-50 rounded mb-4">
                                    <h4 class="font-medium text-gray-700 mb-3">Badge RFID</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de badge *</label>
                                            <select name="type_badge" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                                <option value="">Sélectionner</option>
                                                <option value="MIFARE Classic">MIFARE Classic</option>
                                                <option value="MIFARE DESFire">MIFARE DESFire</option>
                                                <option value="Proximity">Proximity</option>
                                                <option value="NFC">NFC</option>
                                                <option value="HID">HID</option>
                                                <option value="iCLASS">iCLASS</option>
                                                <option value="Seos">Seos</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Numéro unique *</label>
                                            <input type="text" name="numero_unique_badge" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 001A2B3C4D">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Fréquence *</label>
                                            <select name="frequence_badge" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                                <option value="125 kHz">125 kHz</option>
                                                <option value="13.56 MHz">13.56 MHz</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Niveau de sécurité</label>
                                            <select name="niveau_securite" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="UID">UID</option>
                                                <option value="DESFire EV1">DESFire EV1</option>
                                                <option value="DESFire EV2">DESFire EV2</option>
                                                <option value="AES">AES</option>
                                                <option value="3DES">3DES</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-span-1 md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Utilisateur affecté</label>
                                            <input type="text" name="utilisateur_badge" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: John Doe">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date d'émission</label>
                                            <input type="date" name="date_emission" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                                            <input type="date" name="date_expiration_badge" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">État du badge *</label>
                                            <select name="etat_badge" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                                <option value="actif">Actif</option>
                                                <option value="desactive">Désactivé</option>
                                                <option value="perdu">Perdu</option>
                                                <option value="vole">Volé</option>
                                                <option value="expire">Expiré</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-span-1 md:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Commentaires</label>
                                            <textarea name="commentaires_badge" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="Informations complémentaires..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Lecteur RFID -->
                                <div id="lecteur-rfid-details" class="hidden p-4 bg-green-50 rounded">
                                    <h4 class="font-medium text-gray-700 mb-3">Lecteur RFID</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                            <select name="type_lecteur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                                <option value="">Sélectionner</option>
                                                <option value="interieur">Intérieur</option>
                                                <option value="exterieur">Extérieur</option>
                                                <option value="portatif">Portatif</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Technologie supportée *</label>
                                            <select name="technologie_lecteur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                                <option value="">Sélectionner</option>
                                                <option value="125 kHz">125 kHz</option>
                                                <option value="13.56 MHz">13.56 MHz</option>
                                                <option value="UHF">UHF</option>
                                                <option value="multi_frequence">Multi-fréquence</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Interface *</label>
                                            <select name="interface_lecteur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                                <option value="">Sélectionner</option>
                                                <option value="Wiegand">Wiegand</option>
                                                <option value="RS485">RS485</option>
                                                <option value="TCP/IP">TCP/IP</option>
                                                <option value="USB">USB</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Alimentation</label>
                                            <select name="alimentation_lecteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="PoE">PoE</option>
                                                <option value="12V DC">12V DC</option>
                                                <option value="24V DC">24V DC</option>
                                                <option value="220V">220V</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Indice de protection</label>
                                        <select name="ip_lecteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="IP65">IP65</option>
                                            <option value="IP66">IP66</option>
                                            <option value="IP67">IP67</option>
                                            <option value="IP54">IP54</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Distance de lecture (cm)</label>
                                        <input type="number" name="distance_lecture" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 15">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement</label>
                                        <input type="text" name="emplacement_lecteur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Entrée principale, Porte bureau">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse IP</label>
                                        <input type="text" name="adresse_ip_lecteur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="192.168.1.150">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                        <input type="date" name="date_installation_lecteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_lecteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="en_maintenance">En maintenance</option>
                                            <option value="hors_service">Hors service</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_lecteur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Sécurité">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Serrures électroniques -->
                        <div id="subcat-serrure-electronique" class="hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">🔐 Détails Serrure Électronique</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                    <select name="type_serrure" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                        <option value="">Sélectionner</option>
                                        <option value="Fail-safe">Fail-safe (Alimenté = Verrouillé)</option>
                                        <option value="Fail-secure">Fail-secure (Alimenté = Déverrouillé)</option>
                                    </select>
                                </div>
                                
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mode d'ouverture</label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="mode_ouverture[]" value="RFID" class="mr-1">
                                            <span class="text-sm">RFID</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="mode_ouverture[]" value="code" class="mr-1">
                                            <span class="text-sm">Code</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="mode_ouverture[]" value="biometrie" class="mr-1">
                                            <span class="text-sm">Biométrie</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="mode_ouverture[]" value="cle" class="mr-1">
                                            <span class="text-sm">Clé mécanique</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tension d'alimentation</label>
                                    <select name="tension_serrure" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        <option value="12V DC">12V DC</option>
                                        <option value="24V DC">24V DC</option>
                                        <option value="220V">220V AC</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contrôleur associé</label>
                                    <input type="text" name="controleur_associe" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Mercury, Isonas">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Temps déverrouillage (s)</label>
                                    <input type="number" name="temps_deverrouillage" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 3">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Usage *</label>
                                    <select name="usage_serrure" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                        <option value="">Sélectionner</option>
                                        <option value="porte_bureau">Porte bureau</option>
                                        <option value="salle_serveur">Salle serveur</option>
                                        <option value="coffre">Coffre</option>
                                        <option value="armoire">Armoire sécurisée</option>
                                        <option value="zone_restreinte">Zone restreinte</option>
                                    </select>
                                </div>
                                
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement *</label>
                                    <input type="text" name="emplacement_serrure" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: Bureau 101, Salle serveurs">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                    <input type="date" name="date_installation_serrure" class="w-full px-3 py-2 border border-gray-300 rounded">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                    <select name="etat_serrure" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                        <option value="en_maintenance">En maintenance</option>
                                        <option value="hors_service">Hors service</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                    <input type="text" name="responsable_serrure" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Maintenance">
                                </div>
                            </div>
                        </div>

                        <!-- Tourniquets / Portillons -->
                        <div id="subcat-tourniquets" class="hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">🚪 Détails Tourniquet / Portillon</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                    <select name="type_tourniquet" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                        <option value="">Sélectionner</option>
                                        <option value="tripode">Tripode</option>
                                        <option value="pleine_hauteur">Pleine hauteur</option>
                                        <option value="portillon">Portillon</option>
                                        <option value="tourniquet_optique">Tourniquet optique</option>
                                        <option value="barriere">Barrière</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sens de passage *</label>
                                    <select name="sens_passage" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                        <option value="">Sélectionner</option>
                                        <option value="entree">Entrée uniquement</option>
                                        <option value="sortie">Sortie uniquement</option>
                                        <option value="bidirectionnel">Bidirectionnel</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Débit max (personnes/min)</label>
                                    <input type="number" name="debit_max" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 30">
                                </div>
                                
                                <div class="col-span-1 md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mode d'authentification</label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="authentification_tourniquet[]" value="badge" class="mr-1">
                                            <span class="text-sm">Badge</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="authentification_tourniquet[]" value="QR" class="mr-1">
                                            <span class="text-sm">QR Code</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="authentification_tourniquet[]" value="biometrie" class="mr-1">
                                            <span class="text-sm">Biométrie</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="authentification_tourniquet[]" value="visage" class="mr-1">
                                            <span class="text-sm">Reconnaissance faciale</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Interface de contrôle</label>
                                    <select name="interface_controle_tourniquet" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        <option value="RS485">RS485</option>
                                        <option value="TCP/IP">TCP/IP</option>
                                        <option value="Wiegand">Wiegand</option>
                                        <option value="Modbus">Modbus</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alimentation</label>
                                    <select name="alimentation_tourniquet" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        <option value="220V">220V AC</option>
                                        <option value="24V DC">24V DC</option>
                                        <option value="PoE">PoE</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Usage *</label>
                                    <select name="usage_tourniquet" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                        <option value="">Sélectionner</option>
                                        <option value="hall_principal">Hall principal</option>
                                        <option value="zone_securisee">Zone sécurisée</option>
                                        <option value="parking">Parking</option>
                                        <option value="cantine">Cantine</option>
                                        <option value="vestiaire">Vestiaire</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                    <input type="date" name="date_installation_tourniquet" class="w-full px-3 py-2 border border-gray-300 rounded">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                    <select name="etat_tourniquet" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                        <option value="en_maintenance">En maintenance</option>
                                        <option value="hors_service">Hors service</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                    <input type="text" name="responsable_tourniquet" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Sécurité">
                                </div>
                            </div>
                        </div>

                        <!-- Unités de contrôle et logiciel -->
                        <div id="subcat-controle-software" class="hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">🧠 Détails Contrôle d'Accès</h3>
                            
                            <!-- Type contrôle -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type d'équipement *</label>
                                <select name="type_controle_acces" class="w-full px-3 py-2 border border-gray-300 rounded" required onchange="toggleControleDetails()">
                                    <option value="">Sélectionner</option>
                                    <option value="unite_controle">Unité de contrôle</option>
                                    <option value="logiciel_controle">Logiciel de contrôle</option>
                                </select>
                            </div>
                            
                            <!-- Unité de contrôle -->
                            <div id="unite-controle-details" class="hidden p-4 bg-blue-50 rounded mb-4">
                                <h4 class="font-medium text-gray-700 mb-3">Unité de Contrôle (Controller)</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Portes gérées *</label>
                                        <select name="portes_gernees" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="1">1 porte</option>
                                            <option value="2">2 portes</option>
                                            <option value="4">4 portes</option>
                                            <option value="8">8 portes</option>
                                            <option value="16">16 portes</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Lecteurs supportés</label>
                                        <input type="number" name="lecteurs_supportes" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 8">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Interface réseau *</label>
                                        <select name="interface_reseau_controleur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Ethernet">Ethernet</option>
                                            <option value="RS485">RS485</option>
                                            <option value="RS232">RS232</option>
                                            <option value="Modbus">Modbus</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Protocoles supportés</label>
                                        <select name="protocoles_controleur[]" class="w-full px-3 py-2 border border-gray-300 rounded" multiple>
                                            <option value="OSDP">OSDP</option>
                                            <option value="Wiegand">Wiegand</option>
                                            <option value="BACnet">BACnet</option>
                                            <option value="Modbus">Modbus</option>
                                            <option value="Proprietaire">Propriétaire</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alimentation</label>
                                        <select name="alimentation_controleur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="12V DC">12V DC</option>
                                            <option value="24V DC">24V DC</option>
                                            <option value="PoE">PoE</option>
                                            <option value="220V">220V AC</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Batterie secours</label>
                                        <select name="batterie_secours" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mémoire événements</label>
                                        <input type="number" name="memoire_evenements" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 10000">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Firmware version</label>
                                        <input type="text" name="firmware_controleur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: V2.1.5">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse IP</label>
                                        <input type="text" name="adresse_ip_controleur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="192.168.1.200">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                        <input type="date" name="date_installation_controleur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_controleur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="en_maintenance">En maintenance</option>
                                            <option value="hors_service">Hors service</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_controleur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service IT">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Logiciel de contrôle -->
                            <div id="logiciel-controle-details" class="hidden p-4 bg-green-50 rounded">
                                <h4 class="font-medium text-gray-700 mb-3">Logiciel de Contrôle d'Accès</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du logiciel *</label>
                                        <input type="text" name="nom_logiciel_controle" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: Genetec, LenelS2">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Éditeur *</label>
                                        <input type="text" name="editeur_logiciel_controle" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: Genetec Inc., Honeywell">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Version installée *</label>
                                        <input type="text" name="version_logiciel_controle" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 5.10 SR2">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de licence *</label>
                                        <select name="type_licence_controle" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Perpetuelle">Perpétuelle</option>
                                            <option value="Abonnement annuel">Abonnement annuel</option>
                                            <option value="Abonnement mensuel">Abonnement mensuel</option>
                                            <option value="Trial">Trial</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Utilisateurs max</label>
                                        <input type="number" name="utilisateurs_max_logiciel" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 100">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Portes gérées</label>
                                        <input type="number" name="portes_logiciel" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 50">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date expiration licence</label>
                                        <input type="date" name="date_expiration_logiciel_controle" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Serveur d'hébergement</label>
                                        <input type="text" name="serveur_hebergement" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: SRV-SEC-01">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Sauvegarde configuration</label>
                                        <select name="sauvegarde_configuration" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="quotidienne">Quotidienne</option>
                                            <option value="hebdomadaire">Hebdomadaire</option>
                                            <option value="mensuelle">Mensuelle</option>
                                            <option value="aucune">Aucune</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Support actif</label>
                                        <select name="support_actif" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable IT</label>
                                        <input type="text" name="responsable_it_controle" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Informatique">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alarmes anti-intrusion -->
                        <div id="subcat-alarme-anti-intrusion" class="hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">🚨 Détails Alarme Anti-Intrusion</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                    <select name="type_alarme" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                        <option value="">Sélectionner</option>
                                        <option value="filaire">Filaire</option>
                                        <option value="sans_fil">Sans fil</option>
                                        <option value="hybride">Hybride</option>
                                        <option value="IP">IP</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Norme *</label>
                                    <select name="norme_alarme" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                        <option value="">Sélectionner</option>
                                        <option value="EN 50131 Grade 2">EN 50131 Grade 2</option>
                                        <option value="EN 50131 Grade 3">EN 50131 Grade 3</option>
                                        <option value="EN 50131 Grade 4">EN 50131 Grade 4</option>
                                        <option value="NF C 48-000">NF C 48-000</option>
                                        <option value="ISO 9001">ISO 9001</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Zones gérées</label>
                                    <input type="number" name="zones_alarme" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 8">
                                </div>
                                
                                <div class="col-span-1 md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Mode de communication</label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="communication_alarme[]" value="GSM" class="mr-1">
                                            <span class="text-sm">GSM</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="communication_alarme[]" value="IP" class="mr-1">
                                            <span class="text-sm">IP</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="communication_alarme[]" value="RTC" class="mr-1">
                                            <span class="text-sm">RTC</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="communication_alarme[]" value="Radio" class="mr-1">
                                            <span class="text-sm">Radio</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Batterie secours (h)</label>
                                    <input type="number" name="autonomie_batterie" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 24">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Intégration vidéosurveillance</label>
                                    <select name="integration_video" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        <option value="oui">Oui</option>
                                        <option value="non">Non</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Intégration contrôle d'accès</label>
                                    <select name="integration_controle_acces" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        <option value="oui">Oui</option>
                                        <option value="non">Non</option>
                                    </select>
                                </div>
                                
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement *</label>
                                    <input type="text" name="emplacement_alarme" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: Bureau principal, Salle coffre">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                    <input type="date" name="date_installation_alarme" class="w-full px-3 py-2 border border-gray-300 rounded">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                    <select name="etat_alarme" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        <option value="actif">Actif</option>
                                        <option value="inactif">Inactif</option>
                                        <option value="en_maintenance">En maintenance</option>
                                        <option value="hors_service">Hors service</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                    <input type="text" name="responsable_alarme" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Sécurité">
                                </div>
                            </div>
                        </div>

                        <!-- Détecteurs de mouvement -->
                        <div id="subcat-detecteur-mouvement" class="hidden">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">🕵️ Détails Détecteur de Mouvement</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Technologie *</label>
                                    <select name="technologie_detecteur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                        <option value="">Sélectionner</option>
                                        <option value="PIR">PIR (Infrarouge passif)</option>
                                        <option value="Double">Double technologie</option>
                                        <option value="Micro-ondes">Micro-ondes</option>
                                        <option value="Ultrason">Ultrason</option>
                                        <option value="Hybride">Hybride PIR+Micro-ondes</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Angle de détection (°)</label>
                                    <input type="number" name="angle_detection" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 90">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Portée (mètres)</label>
                                    <input type="number" name="portee_detecteur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 12">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type d'installation *</label>
                                    <select name="type_installation_detecteur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                        <option value="">Sélectionner</option>
                                        <option value="interieur">Intérieur</option>
                                        <option value="exterieur">Extérieur</option>
                                        <option value="plafond">Plafond</option>
                                        <option value="mur">Mur</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Immunité animaux (kg)</label>
                                        <input type="number" name="immunite_animaux" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 15">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alimentation</label>
                                        <select name="alimentation_detecteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="pile">Pile</option>
                                            <option value="filaire">Filaire</option>
                                            <option value="sans_fil">Sans fil</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Zone associée</label>
                                        <input type="text" name="zone_associee" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Zone 1 - Couloir principal">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                        <input type="date" name="date_installation_detecteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_detecteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="en_maintenance">En maintenance</option>
                                            <option value="hors_service">Hors service</option>
                                            <option value="test">En test</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Détecteurs d'ouverture -->
                            <div id="subcat-detecteur-ouverture" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🚪 Détails Détecteur d'Ouverture</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_detecteur_ouverture" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="contact_magnetique">Contact magnétique</option>
                                            <option value="reed_switch">Reed switch</option>
                                            <option value="capteur_vibration">Capteur de vibration</option>
                                            <option value="infrarouge">Infrarouge de proximité</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Support *</label>
                                        <select name="support_detecteur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="porte">Porte</option>
                                            <option value="fenetre">Fenêtre</option>
                                            <option value="coffre">Coffre</option>
                                            <option value="armoire">Armoire</option>
                                            <option value="vitrine">Vitrine</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mode de connexion *</label>
                                        <select name="mode_connexion_detecteur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="filaire">Filaire</option>
                                            <option value="sans_fil">Sans fil</option>
                                            <option value="hybride">Hybride</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Distance max ouverture (mm)</label>
                                        <input type="number" name="distance_ouverture" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 25">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Zone associée</label>
                                        <input type="text" name="zone_associee_ouverture" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Zone 2 - Porte principale">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                        <input type="date" name="date_installation_detecteur_ouverture" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_detecteur_ouverture" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="en_maintenance">En maintenance</option>
                                            <option value="hors_service">Hors service</option>
                                            <option value="test">En test</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Centrale d'alarme -->
                            <div id="subcat-centrale-alarme" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">🧠 Détails Centrale d'Alarme</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Zones supportées *</label>
                                        <select name="zones_supportees" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="8">8 zones</option>
                                            <option value="16">16 zones</option>
                                            <option value="32">32 zones</option>
                                            <option value="64">64 zones</option>
                                            <option value="128">128 zones</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Détecteurs max</label>
                                        <input type="number" name="detecteurs_max" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 50">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Protocoles de communication</label>
                                        <select name="protocoles_communication_centrale[]" class="w-full px-3 py-2 border border-gray-300 rounded" multiple>
                                            <option value="GSM">GSM</option>
                                            <option value="IP">IP</option>
                                            <option value="RTC">RTC</option>
                                            <option value="Radio">Radio</option>
                                            <option value="RS485">RS485</option>
                                            <option value="RS232">RS232</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Accès distant</label>
                                        <select name="acces_distant_centrale" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Historique événements</label>
                                        <input type="number" name="historique_evenements" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 1000">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Batterie secours (Ah)</label>
                                        <input type="number" step="0.1" name="capacite_batterie_secours" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 7.2">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Numéro SIM</label>
                                        <input type="text" name="numero_sim" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: +33 6 12 34 56 78">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse IP</label>
                                        <input type="text" name="adresse_ip_centrale" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="192.168.1.250">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                        <input type="date" name="date_installation_centrale" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_centrale" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="actif">Actif</option>
                                            <option value="inactif">Inactif</option>
                                            <option value="en_maintenance">En maintenance</option>
                                            <option value="hors_service">Hors service</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_centrale" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Sécurité">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Informatique (existante) -->
            <div id="section-informatique" class="hidden">
                <!-- ... (code informatique existant) ... -->
                 <!-- Informatique -->
                    
                       <!-- <h2 class="text-xl font-semibold text-gray-800 mb-4">3. Informations Informatique</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">État Stock *</label>
                                <select name="etat_stock" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="En stock">En stock</option>
                                    <option value="Doté">Doté</option>
                                    <option value="En rupture">En rupture</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Adresse MAC</label>
                                <input type="text" name="adresse_mac_info" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" placeholder="00:11:22:33:44:55">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Adresse IP</label>
                                <input type="text" name="adresse_ip_info" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500" placeholder="192.168.1.1">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Département</label>
                                <input type="text" name="departement" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Poste Staff</label>
                                <input type="text" name="poste_staff" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                            </div>
                        </div>-->
                        
                        <!-- Sous-catégories Informatiques Détails -->
                        <div id="subcategory-details" class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <!-- Ordinateurs de bureau -->
                            <div id="subcat-bureau" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Ordinateur de Bureau</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Processeur -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Processeur</label>
                                        <input type="text" name="processeur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fréquence (GHz)</label>
                                        <input type="text" name="frequence_processeur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Cœurs/Threads</label>
                                        <input type="text" name="coeurs_threads" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 4/8">
                                    </div>
                                    
                                    <!-- RAM -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">RAM (Go)</label>
                                        <input type="number" name="ram_capacite" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type RAM</label>
                                        <select name="type_ram" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="">Sélectionner</option>
                                            <option value="DDR3">DDR3</option>
                                            <option value="DDR4">DDR4</option>
                                            <option value="DDR5">DDR5</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Stockage -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Stockage (Go)</label>
                                        <input type="number" name="stockage_capacite" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type Stockage</label>
                                        <select name="type_stockage" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="">Sélectionner</option>
                                            <option value="HDD">HDD</option>
                                            <option value="SSD">SSD</option>
                                            <option value="NVMe">NVMe</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Carte mère -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Carte mère</label>
                                        <input type="text" name="carte_mere" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Carte graphique -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Carte graphique</label>
                                        <input type="text" name="carte_graphique" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type Graphique</label>
                                        <select name="type_graphique" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="intégrée">Intégrée</option>
                                            <option value="dédiée">Dédiée</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Système d'exploitation -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Système d'exploitation</label>
                                        <input type="text" name="systeme_exploitation" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Ports -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ports</label>
                                        <input type="text" name="ports" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: USB 3.0, HDMI, Ethernet">
                                    </div>
                                    
                                    <!-- Alimentation -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alimentation (W)</label>
                                        <input type="number" name="puissance_alimentation" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Boîtier -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Boîtier</label>
                                        <select name="boitier" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Tour">Tour</option>
                                            <option value="Mini-tour">Mini-tour</option>
                                            <option value="Micro">Micro</option>
                                            <option value="SFF">SFF</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Date mise en service -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ordinateurs portables -->
                            <div id="subcat-portable" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Ordinateur Portable</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Processeur -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Processeur</label>
                                        <input type="text" name="processeur_portable" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- RAM -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">RAM (Go)</label>
                                        <input type="number" name="ram_portable" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Stockage -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Stockage (Go)</label>
                                        <input type="number" name="stockage_portable" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Écran -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Taille écran (")</label>
                                        <input type="number" step="0.1" name="taille_ecran" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Résolution écran</label>
                                        <input type="text" name="resolution_ecran" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 1920x1080">
                                    </div>
                                    
                                    <!-- Carte graphique -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Carte graphique</label>
                                        <input type="text" name="carte_graphique_portable" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Batterie -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Batterie</label>
                                        <input type="text" name="batterie" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 5000mAh">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État batterie</label>
                                        <select name="etat_batterie" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="bon">Bon</option>
                                            <option value="moyen">Moyen</option>
                                            <option value="faible">Faible</option>
                                            <option value="à remplacer">À remplacer</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Clavier -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Clavier</label>
                                        <select name="clavier" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="AZERTY">AZERTY</option>
                                            <option value="QWERTY">QWERTY</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Webcam -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Webcam</label>
                                        <select name="webcam" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Connectivité -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Wi-Fi</label>
                                        <input type="text" name="wifi" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Wi-Fi 6">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Bluetooth</label>
                                        <input type="text" name="bluetooth" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Bluetooth 5.0">
                                    </div>
                                    
                                    <!-- Système d'exploitation -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Système d'exploitation</label>
                                        <input type="text" name="os_portable" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Chargeur -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Chargeur</label>
                                        <select name="chargeur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Date mise en service -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_portable" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Écrans -->
                            <div id="subcat-ecran" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Écran</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Taille et résolution -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Taille (")</label>
                                        <input type="number" step="0.1" name="taille_ecran_moniteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Résolution</label>
                                        <select name="resolution_ecran_moniteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="HD">HD (1366x768)</option>
                                            <option value="Full HD">Full HD (1920x1080)</option>
                                            <option value="2K">2K (2560x1440)</option>
                                            <option value="4K">4K (3840x2160)</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Type dalle -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type dalle</label>
                                        <select name="type_dalle" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="IPS">IPS</option>
                                            <option value="TN">TN</option>
                                            <option value="VA">VA</option>
                                            <option value="OLED">OLED</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Fréquence -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fréquence (Hz)</label>
                                        <input type="number" name="frequence_ecran" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Connectiques -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Connectiques</label>
                                        <div class="space-x-4">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="connectiques[]" value="HDMI" class="mr-1">
                                                <span class="text-sm">HDMI</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="connectiques[]" value="VGA" class="mr-1">
                                                <span class="text-sm">VGA</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="connectiques[]" value="DisplayPort" class="mr-1">
                                                <span class="text-sm">DisplayPort</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="connectiques[]" value="DVI" class="mr-1">
                                                <span class="text-sm">DVI</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Temps de réponse -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Temps réponse (ms)</label>
                                        <input type="number" name="temps_reponse" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Support réglable -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Support réglable</label>
                                        <select name="support_reglable" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Date mise en service -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_ecran" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Claviers / Souris -->
                            <div id="subcat-peripherique" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Périphérique</h3>
                                
                                <!-- Clavier -->
                                <div class="mb-6 p-4 bg-gray-100 rounded">
                                    <h4 class="font-medium text-gray-700 mb-3">Clavier</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                            <select name="type_clavier" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="filaire">Filaire</option>
                                                <option value="sans-fil">Sans-fil</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Connexion</label>
                                            <input type="text" name="connexion_clavier" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: USB, Bluetooth">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Disposition</label>
                                            <select name="disposition_clavier" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="AZERTY">AZERTY</option>
                                                <option value="QWERTY">QWERTY</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Souris -->
                                <div class="p-4 bg-gray-100 rounded">
                                    <h4 class="font-medium text-gray-700 mb-3">Souris</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                            <select name="type_souris" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="filaire">Filaire</option>
                                                <option value="sans-fil">Sans-fil</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Connexion</label>
                                            <input type="text" name="connexion_souris" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Technologie</label>
                                            <select name="technologie_souris" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="optique">Optique</option>
                                                <option value="laser">Laser</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">DPI</label>
                                            <input type="number" name="dpi_souris" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Imprimantes -->
                            <div id="subcat-imprimante" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Imprimante</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_imprimante" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Laser">Laser</option>
                                            <option value="Jet d'encre">Jet d'encre</option>
                                            <option value="Thermique">Thermique</option>
                                            <option value="Matricielle">Matricielle</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Fonction -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fonction *</label>
                                        <select name="fonction_imprimante" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Simple">Simple (Impression)</option>
                                            <option value="Multifonction">Multifonction</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Couleur -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Couleur *</label>
                                        <select name="couleur_imprimante" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Noir & blanc">Noir & blanc</option>
                                            <option value="Couleur">Couleur</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Vitesse -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Vitesse (ppm)</label>
                                        <input type="number" name="vitesse_impression" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 30">
                                    </div>
                                    
                                    <!-- Résolution -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Résolution (dpi)</label>
                                        <input type="text" name="resolution_imprimante" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 1200x1200">
                                    </div>
                                    
                                    <!-- Recto-verso -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Recto-verso</label>
                                        <select name="recto_verso" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="non">Non</option>
                                            <option value="manuel">Manuel</option>
                                            <option value="automatique">Automatique</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Connectivité -->
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Connectivité</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="connectivite[]" value="USB" class="mr-1">
                                                <span class="text-sm">USB</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="connectivite[]" value="Ethernet" class="mr-1">
                                                <span class="text-sm">Ethernet</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="connectivite[]" value="Wi-Fi" class="mr-1">
                                                <span class="text-sm">Wi-Fi</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="connectivite[]" value="Bluetooth" class="mr-1">
                                                <span class="text-sm">Bluetooth</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Adresse IP -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse IP</label>
                                        <input type="text" name="adresse_ip_imprimante" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="192.168.1.100">
                                    </div>
                                    
                                    <!-- Formats -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Formats supportés</label>
                                        <select name="formats_supportes[]" class="w-full px-3 py-2 border border-gray-300 rounded" multiple>
                                            <option value="A4">A4</option>
                                            <option value="A3">A3</option>
                                            <option value="A5">A5</option>
                                            <option value="Lettre">Lettre</option>
                                            <option value="Legal">Legal</option>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Maintenez Ctrl pour sélectionner plusieurs</p>
                                    </div>
                                    
                                    <!-- Volume mensuel -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Volume mensuel (pages)</label>
                                        <input type="number" name="volume_mensuel" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Compteur pages -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Compteur pages</label>
                                        <input type="number" name="compteur_pages" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Informations consommables -->
                                    <div class="col-span-1 md:col-span-3 mt-4 p-4 bg-blue-50 rounded">
                                        <h4 class="font-medium text-gray-700 mb-3">Informations consommables</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Référence toner/cartouche</label>
                                                <input type="text" name="reference_toner" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Niveau toner (%)</label>
                                                <input type="number" name="niveau_toner" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date changement</label>
                                                <input type="date" name="date_changement_toner" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Date mise en service -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_imprimante" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- État détaillé -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_detaille" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="fonctionnel">Fonctionnel</option>
                                            <option value="en panne">En panne</option>
                                            <option value="en maintenance">En maintenance</option>
                                            <option value="toner vide">Toner vide</option>
                                            <option value="papier bloqué">Papier bloqué</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Scanners -->
                            <div id="subcat-scanner" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Scanner</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_scanner" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="À plat">À plat</option>
                                            <option value="Chargeur automatique (ADF)">Chargeur automatique (ADF)</option>
                                            <option value="Portable">Portable</option>
                                            <option value="À main">À main</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Résolution -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Résolution optique (dpi)</label>
                                        <input type="number" name="resolution_scanner" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 1200">
                                    </div>
                                    
                                    <!-- Vitesse -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Vitesse (pages/min)</label>
                                        <input type="number" name="vitesse_scanner" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 25">
                                    </div>
                                    
                                    <!-- Formats -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Formats supportés</label>
                                        <select name="formats_scanner[]" class="w-full px-3 py-2 border border-gray-300 rounded" multiple>
                                            <option value="A4">A4</option>
                                            <option value="A3">A3</option>
                                            <option value="A5">A5</option>
                                            <option value="Carte">Carte de visite</option>
                                            <option value="Photo">Photo</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Recto-verso -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Recto-verso</label>
                                        <select name="recto_verso_scanner" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="non">Non</option>
                                            <option value="oui">Oui</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Connectivité -->
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Connectivité</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="connectivite_scanner[]" value="USB" class="mr-1">
                                                <span class="text-sm">USB</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="connectivite_scanner[]" value="Ethernet" class="mr-1">
                                                <span class="text-sm">Ethernet</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="connectivite_scanner[]" value="Wi-Fi" class="mr-1">
                                                <span class="text-sm">Wi-Fi</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Logiciel -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Logiciel associé</label>
                                        <input type="text" name="logiciel_scanner" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: VueScan, ABBYY">
                                    </div>
                                    
                                    <!-- Date mise en service -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_scanner" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- État détaillé -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_scanner" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="fonctionnel">Fonctionnel</option>
                                            <option value="en panne">En panne</option>
                                            <option value="en maintenance">En maintenance</option>
                                            <option value="verre sale">Verre sale</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Onduleurs (UPS) -->
                            <div id="subcat-onduleur" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Onduleur (UPS)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Puissance -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Puissance (VA) *</label>
                                        <input type="number" name="puissance_ups" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: 1500">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Puissance (Watts)</label>
                                        <input type="number" name="puissance_watts" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 900">
                                    </div>
                                    
                                    <!-- Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_ups" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Offline">Offline</option>
                                            <option value="Line-Interactive">Line-Interactive</option>
                                            <option value="Online">Online</option>
                                            <option value="Double conversion">Double conversion</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Autonomie -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Autonomie (min)</label>
                                        <input type="number" name="autonomie_ups" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 30">
                                    </div>
                                    
                                    <!-- Prises -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de prises</label>
                                        <input type="number" name="nombre_prises" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 8">
                                    </div>
                                    
                                    <!-- Type prises -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de prises</label>
                                        <input type="text" name="type_prises" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: IEC C13">
                                    </div>
                                    
                                    <!-- Batterie -->
                                    <div class="col-span-1 md:col-span-3 p-4 bg-blue-50 rounded">
                                        <h4 class="font-medium text-gray-700 mb-3">Informations batterie</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Type batterie</label>
                                                <select name="type_batterie" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                    <option value="Plomb-acide">Plomb-acide</option>
                                                    <option value="Lithium-ion">Lithium-ion</option>
                                                    <option value="NiMH">NiMH</option>
                                                    <option value="Gel">Gel</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Capacité (Ah)</label>
                                                <input type="number" step="0.1" name="capacite_batterie" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 7.2">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date installation</label>
                                                <input type="date" name="date_installation_batterie" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date remplacement</label>
                                                <input type="date" name="date_remplacement_batterie" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Protection -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Protection surtension</label>
                                        <select name="protection_surtension" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Port communication -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Port communication</label>
                                        <select name="port_communication" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="aucun">Aucun</option>
                                            <option value="USB">USB</option>
                                            <option value="RJ45">RJ45</option>
                                            <option value="RS232">RS232</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Date mise en service -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_ups" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- État détaillé -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_ups" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="fonctionnel">Fonctionnel</option>
                                            <option value="en panne">En panne</option>
                                            <option value="batterie faible">Batterie faible</option>
                                            <option value="en test">En test</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Projecteurs / Écrans interactifs -->
                            <div id="subcat-projection" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Projecteur/Écran interactif</h3>
                                
                                <!-- Sélection type -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Type d'équipement *</label>
                                    <select name="type_projection" class="w-full px-3 py-2 border border-gray-300 rounded" required onchange="toggleProjectionDetails()">
                                        <option value="">Sélectionner</option>
                                        <option value="projecteur">Projecteur</option>
                                        <option value="ecran_interactif">Écran interactif</option>
                                    </select>
                                </div>
                                
                                <!-- Détails Projecteur -->
                                <div id="projecteur-details" class="hidden p-4 bg-blue-50 rounded mb-4">
                                    <h4 class="font-medium text-gray-700 mb-3">Projecteur</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Technologie *</label>
                                            <select name="technologie_projecteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="">Sélectionner</option>
                                                <option value="LCD">LCD</option>
                                                <option value="DLP">DLP</option>
                                                <option value="LED">LED</option>
                                                <option value="Laser">Laser</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Luminosité (lumens)</label>
                                            <input type="number" name="luminosite" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 3500">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Résolution native</label>
                                            <select name="resolution_projecteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="SVGA">SVGA (800x600)</option>
                                                <option value="XGA">XGA (1024x768)</option>
                                                <option value="WXGA">WXGA (1280x800)</option>
                                                <option value="Full HD">Full HD (1920x1080)</option>
                                                <option value="4K">4K (3840x2160)</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Rapport de projection</label>
                                            <input type="text" name="rapport_projection" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 1.5-2.0:1">
                                        </div>
                                        
                                        <!-- Connectiques -->
                                        <div class="col-span-1 md:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Connectiques</label>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="connectiques_projecteur[]" value="HDMI" class="mr-1">
                                                    <span class="text-sm">HDMI</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="connectiques_projecteur[]" value="VGA" class="mr-1">
                                                    <span class="text-sm">VGA</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="connectiques_projecteur[]" value="USB" class="mr-1">
                                                    <span class="text-sm">USB</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="connectiques_projecteur[]" value="RJ45" class="mr-1">
                                                    <span class="text-sm">RJ45</span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Haut-parleur intégré</label>
                                            <select name="haut_parleur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="non">Non</option>
                                                <option value="oui">Oui</option>
                                        </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Durée vie lampe (h)</label>
                                            <input type="number" name="duree_vie_lampe" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 5000">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Heures utilisation</label>
                                            <input type="number" name="heures_utilisation" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                            <input type="date" name="date_mise_service_projecteur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Détails Écran interactif -->
                                <div id="ecran-interactif-details" class="hidden p-4 bg-green-50 rounded">
                                    <h4 class="font-medium text-gray-700 mb-3">Écran interactif</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Taille (pouces) *</label>
                                            <input type="number" name="taille_ecran_interactif" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 65">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Résolution</label>
                                            <select name="resolution_ecran_interactif" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="Full HD">Full HD (1920x1080)</option>
                                                <option value="2K">2K (2560x1440)</option>
                                                <option value="4K">4K (3840x2160)</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Technologie tactile</label>
                                            <select name="technologie_tactile" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="Infrarouge">Infrarouge</option>
                                                <option value="Capacitive">Capacitive</option>
                                                <option value="Optique">Optique</option>
                                                <option value="Resistive">Resistive</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Points de contact</label>
                                            <input type="number" name="points_contact" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 10">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Système intégré</label>
                                            <select name="systeme_integre" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="Android">Android</option>
                                                <option value="Windows">Windows</option>
                                                <option value="Linux">Linux</option>
                                                <option value="Propriétaire">Propriétaire</option>
                                            </select>
                                        </div>
                                        
                                        <!-- Connectivité -->
                                        <div class="col-span-1 md:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Connectivité</label>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="connectivite_ecran[]" value="HDMI" class="mr-1">
                                                    <span class="text-sm">HDMI</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="connectivite_ecran[]" value="USB" class="mr-1">
                                                    <span class="text-sm">USB</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="connectivite_ecran[]" value="VGA" class="mr-1">
                                                    <span class="text-sm">VGA</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="connectivite_ecran[]" value="Wi-Fi" class="mr-1">
                                                    <span class="text-sm">Wi-Fi</span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Haut-parleurs intégrés</label>
                                            <select name="haut_parleurs_ecran" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="non">Non</option>
                                                <option value="oui">Oui</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Support</label>
                                            <select name="support_ecran" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="mural">Mural</option>
                                                <option value="mobile">Mobile</option>
                                                <option value="pied">Sur pied</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                            <input type="date" name="date_mise_service_ecran_interactif" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                            <select name="etat_ecran_interactif" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="fonctionnel">Fonctionnel</option>
                                                <option value="en panne">En panne</option>
                                                <option value="écran cassé">Écran cassé</option>
                                                <option value="tactile défectueux">Tactile défectueux</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Serveurs physiques -->
                            <div id="subcat-serveur" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Serveur Physique</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Format -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Format *</label>
                                        <select name="format_serveur" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Rack">Rack</option>
                                            <option value="Tour">Tour</option>
                                            <option value="Blade">Blade</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Processeur -->
                                    <div class="col-span-1 md:col-span-3 p-4 bg-blue-50 rounded mb-4">
                                        <h4 class="font-medium text-gray-700 mb-3">Processeur(s)</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Modèle</label>
                                                <input type="text" name="processeur_serveur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Intel Xeon E5-2690">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                                <input type="number" name="nombre_processeurs" class="w-full px-3 py-2 border border-gray-300 rounded" value="1" min="1">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Cœurs total</label>
                                                <input type="number" name="coeurs_processeur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 16">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Fréquence (GHz)</label>
                                                <input type="number" step="0.1" name="frequence_processeur_serveur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 2.6">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- RAM -->
                                    <div class="col-span-1 md:col-span-3 p-4 bg-blue-50 rounded mb-4">
                                        <h4 class="font-medium text-gray-700 mb-3">Mémoire RAM</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Capacité totale (Go)</label>
                                                <input type="number" name="ram_serveur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 64">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Type RAM</label>
                                                <select name="type_ram_serveur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                    <option value="DDR4 ECC">DDR4 ECC</option>
                                                    <option value="DDR5 ECC">DDR5 ECC</option>
                                                    <option value="DDR4">DDR4</option>
                                                    <option value="DDR3 ECC">DDR3 ECC</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre slots</label>
                                                <input type="number" name="slots_ram" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 8">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Slots utilisés</label>
                                                <input type="number" name="slots_ram_utilises" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 4">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Stockage -->
                                    <div class="col-span-1 md:col-span-3 p-4 bg-blue-50 rounded mb-4">
                                        <h4 class="font-medium text-gray-700 mb-3">Stockage interne</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Capacité totale (Go)</label>
                                                <input type="number" name="stockage_serveur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 2000">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                                <select name="type_stockage_serveur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                    <option value="HDD">HDD</option>
                                                    <option value="SSD">SSD</option>
                                                    <option value="NVMe">NVMe</option>
                                                    <option value="Mixte">Mixte</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Configuration RAID</label>
                                                <select name="raid_serveur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                    <option value="RAID 0">RAID 0</option>
                                                    <option value="RAID 1">RAID 1</option>
                                                    <option value="RAID 5">RAID 5</option>
                                                    <option value="RAID 6">RAID 6</option>
                                                    <option value="RAID 10">RAID 10</option>
                                                    <option value="Aucun">Aucun</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Contrôleur RAID</label>
                                                <input type="text" name="controleur_raid" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: PERC H730">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Carte réseau -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Ports réseau</label>
                                        <input type="number" name="ports_reseau" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 4">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Débit réseau</label>
                                        <select name="debit_reseau" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="1 GbE">1 GbE</option>
                                            <option value="10 GbE">10 GbE</option>
                                            <option value="25 GbE">25 GbE</option>
                                            <option value="40 GbE">40 GbE</option>
                                            <option value="100 GbE">100 GbE</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Carte gestion -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Carte gestion</label>
                                        <select name="carte_gestion" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="iDRAC">iDRAC (Dell)</option>
                                            <option value="iLO">iLO (HP)</option>
                                            <option value="IPMI">IPMI</option>
                                            <option value="BMC">BMC</option>
                                            <option value="Aucune">Aucune</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Système -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Système d'exploitation</label>
                                        <input type="text" name="os_serveur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Windows Server 2022">
                                    </div>
                                    
                                    <!-- Hyperviseur -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Hyperviseur</label>
                                        <select name="hyperviseur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Aucun">Aucun</option>
                                            <option value="VMware vSphere">VMware vSphere</option>
                                            <option value="Microsoft Hyper-V">Microsoft Hyper-V</option>
                                            <option value="Proxmox VE">Proxmox VE</option>
                                            <option value="Citrix Hypervisor">Citrix Hypervisor</option>
                                            <option value="KVM">KVM</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">VM hébergées</label>
                                        <input type="number" name="nombre_vm" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 10">
                                    </div>
                                    
                                    <!-- Adresses -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse IP</label>
                                        <input type="text" name="adresse_ip_serveur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="192.168.1.10">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse MAC</label>
                                        <input type="text" name="adresse_mac_serveur" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="00:11:22:33:44:55">
                                    </div>
                                    
                                    <!-- Rack -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rack U occupées</label>
                                        <input type="number" name="u_rack" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 2">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alimentation redondante</label>
                                        <select name="alimentation_redondante" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Date mise en service -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_serveur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- État détaillé -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_serveur" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="En production">En production</option>
                                            <option value="En test">En test</option>
                                            <option value="En maintenance">En maintenance</option>
                                            <option value="Hors service">Hors service</option>
                                            <option value="En stock">En stock</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- NAS / SAN -->
                            <div id="subcat-nas" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails NAS / SAN</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_nas" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="NAS">NAS (Network Attached Storage)</option>
                                            <option value="SAN">SAN (Storage Area Network)</option>
                                            <option value="Unifié">Unifié (NAS/SAN)</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Protocole -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Protocole(s)</label>
                                        <select name="protocole_nas[]" class="w-full px-3 py-2 border border-gray-300 rounded" multiple>
                                            <option value="NFS">NFS</option>
                                            <option value="SMB/CIFS">SMB/CIFS</option>
                                            <option value="iSCSI">iSCSI</option>
                                            <option value="FC">Fibre Channel</option>
                                            <option value="FCoE">FCoE</option>
                                            <option value="S3">S3</option>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Maintenez Ctrl pour sélectionner plusieurs</p>
                                    </div>
                                    
                                    <!-- Capacités -->
                                    <div class="col-span-1 md:col-span-3 p-4 bg-blue-50 rounded mb-4">
                                        <h4 class="font-medium text-gray-700 mb-3">Capacités de stockage</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Capacité brute (To)</label>
                                                <input type="number" step="0.1" name="capacite_brute" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 24.0">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Capacité utile (To)</label>
                                                <input type="number" step="0.1" name="capacite_utile" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 16.0">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Configuration RAID</label>
                                                <select name="raid_nas" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                    <option value="RAID 5">RAID 5</option>
                                                    <option value="RAID 6">RAID 6</option>
                                                    <option value="RAID 10">RAID 10</option>
                                                    <option value="RAID 50">RAID 50</option>
                                                    <option value="RAID 60">RAID 60</option>
                                                    <option value="JBOD">JBOD</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Baies disques -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de baies</label>
                                        <input type="number" name="baies_disques" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 12">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Baies utilisées</label>
                                        <input type="number" name="baies_utilisees" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 8">
                                    </div>
                                    
                                    <!-- Types disques -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Types de disques</label>
                                        <select name="type_disques[]" class="w-full px-3 py-2 border border-gray-300 rounded" multiple>
                                            <option value="HDD SAS">HDD SAS</option>
                                            <option value="HDD SATA">HDD SATA</option>
                                            <option value="SSD SAS">SSD SAS</option>
                                            <option value="SSD SATA">SSD SATA</option>
                                            <option value="NVMe">NVMe</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Débit réseau -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Débit réseau</label>
                                        <select name="debit_nas" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="1 GbE">1 GbE</option>
                                            <option value="10 GbE">10 GbE</option>
                                            <option value="25 GbE">25 GbE</option>
                                            <option value="40 GbE">40 GbE</option>
                                            <option value="100 GbE">100 GbE</option>
                                            <option value="FC 8Gb">FC 8Gb</option>
                                            <option value="FC 16Gb">FC 16Gb</option>
                                            <option value="FC 32Gb">FC 32Gb</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Adresse IP -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse IP</label>
                                        <input type="text" name="adresse_ip_nas" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="192.168.1.20">
                                    </div>
                                    
                                    <!-- Redondance -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Contrôleurs redondants</label>
                                        <select name="controleurs_redondants" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alimentation redondante</label>
                                        <select name="alimentation_redondante_nas" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Firmware -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Firmware / OS</label>
                                        <input type="text" name="firmware_nas" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: TrueNAS, DSM, ONTAP">
                                    </div>
                                    
                                    <!-- Version -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Version firmware</label>
                                        <input type="text" name="version_firmware" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 12.1">
                                    </div>
                                    
                                    <!-- Date mise en service -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_nas" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- État détaillé -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_nas" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="En production">En production</option>
                                            <option value="En test">En test</option>
                                            <option value="En maintenance">En maintenance</option>
                                            <option value="Hors service">Hors service</option>
                                            <option value="En stock">En stock</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Baies de stockage -->
                            <div id="subcat-baie-stockage" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Baie de Stockage</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_baie" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Disk Array">Disk Array</option>
                                            <option value="JBOD">JBOD (Just a Bunch Of Disks)</option>
                                            <option value="Enclosure">Enclosure</option>
                                            <option value="Tape Library">Tape Library</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Capacité -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacité totale (To)</label>
                                        <input type="number" step="0.1" name="capacite_baie" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 48.0">
                                    </div>
                                    
                                    <!-- Tiroirs -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de tiroirs</label>
                                        <input type="number" name="tiroirs_baie" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 2">
                                    </div>
                                    
                                    <!-- Disques supportés -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Types disques supportés</label>
                                        <select name="disques_supportes[]" class="w-full px-3 py-2 border border-gray-300 rounded" multiple>
                                            <option value="2.5 SAS">2.5" SAS</option>
                                            <option value="3.5 SAS">3.5" SAS</option>
                                            <option value="2.5 SATA">2.5" SATA</option>
                                            <option value="3.5 SATA">3.5" SATA</option>
                                            <option value="SSD SAS">SSD SAS</option>
                                            <option value="SSD SATA">SSD SATA</option>
                                            <option value="NVMe">NVMe</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Connexion -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Connexion</label>
                                        <select name="connexion_baie" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="SAS">SAS</option>
                                            <option value="FC">Fibre Channel</option>
                                            <option value="iSCSI">iSCSI</option>
                                            <option value="SATA">SATA</option>
                                            <option value="USB">USB</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Contrôleurs -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre contrôleurs</label>
                                        <input type="number" name="controleurs_baie" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 2">
                                    </div>
                                    
                                    <!-- Redondance -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Redondance alimentation</label>
                                        <select name="redondance_alimentation" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Redondance contrôleurs</label>
                                        <select name="redondance_controleurs" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Emplacement -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement datacenter</label>
                                        <input type="text" name="emplacement_datacenter" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Salle serveur A, Rack 42">
                                    </div>
                                    
                                    <!-- Rack -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rack U occupées</label>
                                        <input type="number" name="u_baie" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 4">
                                    </div>
                                    
                                    <!-- Date mise en service -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_baie" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- État détaillé -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_baie" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="En production">En production</option>
                                            <option value="En test">En test</option>
                                            <option value="En maintenance">En maintenance</option>
                                            <option value="Hors service">Hors service</option>
                                            <option value="En stock">En stock</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Solutions de backup -->
                            <div id="subcat-backup" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Solution de Backup</h3>
                                
                                <!-- Sélection type -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de solution *</label>
                                    <select name="type_backup" class="w-full px-3 py-2 border border-gray-300 rounded" required onchange="toggleBackupDetails()">
                                        <option value="">Sélectionner</option>
                                        <option value="tape_library">Tape Library</option>
                                        <option value="disque_externe">Disque dur externe</option>
                                        <option value="appliance">Appliance de backup</option>
                                        <option value="cloud">Solution Cloud</option>
                                    </select>
                                </div>
                                
                                <!-- Tape Library -->
                                <div id="tape-library-details" class="hidden p-4 bg-blue-50 rounded mb-4">
                                    <h4 class="font-medium text-gray-700 mb-3">Tape Library</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Technologie</label>
                                            <select name="technologie_tape" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="LTO-5">LTO-5</option>
                                                <option value="LTO-6">LTO-6</option>
                                                <option value="LTO-7">LTO-7</option>
                                                <option value="LTO-8">LTO-8</option>
                                                <option value="LTO-9">LTO-9</option>
                                                <option value="DLT">DLT</option>
                                                <option value="DAT">DAT</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre lecteurs</label>
                                            <input type="number" name="lecteurs_tape" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 2">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre slots</label>
                                            <input type="number" name="slots_tape" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 24">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Capacité totale (To)</label>
                                            <input type="number" step="0.1" name="capacite_tape" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 192.0">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Logiciel sauvegarde</label>
                                            <input type="text" name="logiciel_backup" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Veeam, Backup Exec">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Interface</label>
                                            <select name="interface_tape" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="SAS">SAS</option>
                                                <option value="FC">Fibre Channel</option>
                                                <option value="iSCSI">iSCSI</option>
                                                <option value="USB">USB</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                            <input type="date" name="date_mise_service_tape" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                            <select name="etat_tape" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="En production">En production</option>
                                                <option value="En test">En test</option>
                                                <option value="En maintenance">En maintenance</option>
                                                <option value="Hors service">Hors service</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Disque dur externe -->
                                <div id="disque-externe-details" class="hidden p-4 bg-green-50 rounded">
                                    <h4 class="font-medium text-gray-700 mb-3">Disque dur externe</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Capacité (To)</label>
                                            <input type="number" step="0.1" name="capacite_disque" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 4.0">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Interface</label>
                                            <select name="interface_disque" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="USB 3.0">USB 3.0</option>
                                                <option value="USB 3.1">USB 3.1</option>
                                                <option value="USB-C">USB-C</option>
                                                <option value="Thunderbolt 3">Thunderbolt 3</option>
                                                <option value="Thunderbolt 4">Thunderbolt 4</option>
                                                <option value="eSATA">eSATA</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Chiffrement</label>
                                            <select name="chiffrement_disque" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="oui">Oui</option>
                                                <option value="non">Non</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Usage</label>
                                            <select name="usage_disque" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="Backup">Backup</option>
                                                <option value="Archive">Archive</option>
                                                <option value="Transfert">Transfert de données</option>
                                                <option value="Portable">Stockage portable</option>
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Localisation</label>
                                            <input type="text" name="localisation_disque" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Bureau IT, Coffre-fort">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                            <input type="date" name="date_mise_service_disque" class="w-full px-3 py-2 border border-gray-300 rounded">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                            <select name="etat_disque" class="w-full px-3 py-2 border border-gray-300 rounded">
                                                <option value="Fonctionnel">Fonctionnel</option>
                                                <option value="En utilisation">En utilisation</option>
                                                <option value="En panne">En panne</option>
                                                <option value="À vérifier">À vérifier</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Outils de diagnostic -->
                            <div id="subcat-outil-diagnostic" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Outil de Diagnostic</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type d'outil *</label>
                                        <select name="type_outil" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Testeur réseau">Testeur réseau</option>
                                            <option value="Analyseur Wi-Fi">Analyseur Wi-Fi</option>
                                            <option value="Multimètre">Multimètre</option>
                                            <option value="Oscilloscope">Oscilloscope</option>
                                            <option value="Testeur fibre optique">Testeur fibre optique</option>
                                            <option value="Générateur de trafic">Générateur de trafic</option>
                                            <option value="Analyseur de protocole">Analyseur de protocole</option>
                                            <option value="Testeur câblage">Testeur câblage</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Fonctions -->
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fonctions supportées</label>
                                        <input type="text" name="fonctions_outil" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Ping, Traceroute, Analyse spectrale">
                                    </div>
                                    
                                    <!-- Normes -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Normes supportées</label>
                                        <input type="text" name="normes_outil" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: IEEE 802.3, 802.11">
                                    </div>
                                    
                                    <!-- Interface -->
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Interface(s)</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="interface_outil[]" value="USB" class="mr-1">
                                                <span class="text-sm">USB</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="interface_outil[]" value="Ethernet" class="mr-1">
                                                <span class="text-sm">Ethernet</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="interface_outil[]" value="Wi-Fi" class="mr-1">
                                                <span class="text-sm">Wi-Fi</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="interface_outil[]" value="Bluetooth" class="mr-1">
                                                <span class="text-sm">Bluetooth</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Alimentation -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alimentation</label>
                                        <select name="alimentation_outil" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Batterie">Batterie</option>
                                            <option value="Secteur">Secteur</option>
                                            <option value="Mixte">Mixte</option>
                                            <option value="USB">USB</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Date acquisition -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'acquisition</label>
                                        <input type="date" name="date_acquisition_outil" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Responsable -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                        <input type="text" name="responsable_outil" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: John Doe">
                                    </div>
                                    
                                    <!-- Localisation -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Localisation / Affectation</label>
                                        <input type="text" name="affectation_outil" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service IT, Van de maintenance">
                                    </div>
                                    
                                    <!-- État détaillé -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_outil" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Disponible">Disponible</option>
                                            <option value="En prêt">En prêt</option>
                                            <option value="En réparation">En réparation</option>
                                            <option value="Hors service">Hors service</option>
                                            <option value="Calibration nécessaire">Calibration nécessaire</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- KVM -->
                            <div id="subcat-kvm" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails KVM (Keyboard Video Mouse)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_kvm" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Analogique">Analogique</option>
                                            <option value="IP">KVM over IP</option>
                                            <option value="USB">USB</option>
                                            <option value="PS/2">PS/2</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Ports -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de ports</label>
                                        <input type="number" name="ports_kvm" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 8">
                                    </div>
                                    
                                    <!-- Types ports -->
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Types de ports vidéo</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="ports_video[]" value="VGA" class="mr-1">
                                                <span class="text-sm">VGA</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="ports_video[]" value="HDMI" class="mr-1">
                                                <span class="text-sm">HDMI</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="ports_video[]" value="DisplayPort" class="mr-1">
                                                <span class="text-sm">DisplayPort</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="ports_video[]" value="DVI" class="mr-1">
                                                <span class="text-sm">DVI</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Accès distant -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Accès distant</label>
                                        <select name="acces_distant_kvm" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Authentification -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Authentification</label>
                                        <select name="authentification_kvm" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Locale">Locale</option>
                                            <option value="LDAP">LDAP</option>
                                            <option value="Active Directory">Active Directory</option>
                                            <option value="Radius">Radius</option>
                                            <option value="Aucune">Aucune</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Résolution -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Résolution max</label>
                                        <select name="resolution_kvm" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="1920x1080">1920x1080</option>
                                            <option value="2560x1440">2560x1440</option>
                                            <option value="3840x2160">3840x2160</option>
                                            <option value="VGA">VGA (640x480)</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Alimentation -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alimentation</label>
                                        <select name="alimentation_kvm" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Secteur">Secteur</option>
                                            <option value="PoE">PoE</option>
                                            <option value="USB">USB</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Montage rack -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Montage rack</label>
                                        <select name="montage_rack_kvm" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Rack U -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rack U occupées</label>
                                        <input type="number" name="u_kvm" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 1">
                                    </div>
                                    
                                    <!-- Date mise en service -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date mise en service</label>
                                        <input type="date" name="date_mise_service_kvm" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- État détaillé -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_kvm" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="En production">En production</option>
                                            <option value="En test">En test</option>
                                            <option value="En maintenance">En maintenance</option>
                                            <option value="Hors service">Hors service</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Barres de test -->
                            <div id="subcat-barre-test" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Barre de Test</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_barre" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="RJ45">RJ45</option>
                                            <option value="Fibre optique">Fibre optique</option>
                                            <option value="Coaxial">Coaxial</option>
                                            <option value="RJ11">RJ11</option>
                                            <option value="USB">USB</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Catégorie -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie supportée</label>
                                        <select name="categorie_barre" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Cat5e">Cat5e</option>
                                            <option value="Cat6">Cat6</option>
                                            <option value="Cat6a">Cat6a</option>
                                            <option value="Cat7">Cat7</option>
                                            <option value="Cat8">Cat8</option>
                                            <option value="SM">Fibre SM</option>
                                            <option value="MM">Fibre MM</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Fonctions -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fonctions</label>
                                        <select name="fonctions_barre[]" class="w-full px-3 py-2 border border-gray-300 rounded" multiple>
                                            <option value="Continuité">Continuité</option>
                                            <option value="Longueur">Longueur</option>
                                            <option value="Diaphonie">Diaphonie</option>
                                            <option value="Impédance">Impédance</option>
                                            <option value="Polarité">Polarité</option>
                                            <option value="Puissance">Puissance (fibre)</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Normes -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Normes supportées</label>
                                        <input type="text" name="normes_barre" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: TIA/EIA-568, ISO/IEC 11801">
                                    </div>
                                    
                                    <!-- Alimentation -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alimentation</label>
                                        <select name="alimentation_barre" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Batterie">Batterie</option>
                                            <option value="Secteur">Secteur</option>
                                            <option value="USB">USB</option>
                                            <option value="Pile">Pile</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Date acquisition -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'acquisition</label>
                                        <input type="date" name="date_acquisition_barre" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Calibration -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date dernière calibration</label>
                                        <input type="date" name="date_calibration" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Prochaine calibration -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Prochaine calibration</label>
                                        <input type="date" name="prochaine_calibration" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Localisation -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Localisation</label>
                                        <input type="text" name="localisation_barre" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Armoire câblage, Van technique">
                                    </div>
                                    
                                    <!-- État détaillé -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État détaillé</label>
                                        <select name="etat_barre" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Disponible">Disponible</option>
                                            <option value="En prêt">En prêt</option>
                                            <option value="En réparation">En réparation</option>
                                            <option value="Calibration nécessaire">Calibration nécessaire</option>
                                            <option value="Hors service">Hors service</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Logiciels systèmes -->
                            <div id="subcat-logiciel-systeme" class="hidden">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Détails Logiciel Système</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Nom -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du logiciel *</label>
                                        <input type="text" name="nom_logiciel" class="w-full px-3 py-2 border border-gray-300 rounded" required placeholder="ex: Windows Server 2022">
                                    </div>
                                    
                                    <!-- Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                        <select name="type_logiciel_systeme" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Système d'exploitation">Système d'exploitation</option>
                                            <option value="Utilitaire">Utilitaire</option>
                                            <option value="Métier">Métier</option>
                                            <option value="Sécurité">Sécurité</option>
                                            <option value="Bureautique">Bureautique</option>
                                            <option value="Base de données">Base de données</option>
                                            <option value="Virtualisation">Virtualisation</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Éditeur -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Éditeur</label>
                                        <input type="text" name="editeur_logiciel" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Microsoft">
                                    </div>
                                    
                                    <!-- Version -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Version</label>
                                        <input type="text" name="version_logiciel" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: 2022">
                                    </div>
                                    
                                    <!-- Type licence -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de licence</label>
                                        <select name="type_licence_logiciel" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Perpétuelle">Perpétuelle</option>
                                            <option value="Abonnement annuel">Abonnement annuel</option>
                                            <option value="Abonnement mensuel">Abonnement mensuel</option>
                                            <option value="Gratuit">Gratuit</option>
                                            <option value="Open Source">Open Source</option>
                                            <option value="Trial">Trial</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Licences -->
                                    <div class="col-span-1 md:col-span-3 p-4 bg-blue-50 rounded mb-4">
                                        <h4 class="font-medium text-gray-700 mb-3">Gestion des licences</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre total licences</label>
                                                <input type="number" name="nombre_licences_logiciel" class="w-full px-3 py-2 border border-gray-300 rounded" value="1" min="1">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Licences utilisées</label>
                                                <input type="number" name="licences_utilisees" class="w-full px-3 py-2 border border-gray-300 rounded" value="0" min="0">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Licences disponibles</label>
                                                <input type="number" name="licences_disponibles" class="w-full px-3 py-2 border border-gray-300 rounded" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Référence -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Référence licence</label>
                                        <input type="text" name="reference_licence_logiciel" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: FPP-12345">
                                    </div>
                                    
                                    <!-- Dates -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'installation</label>
                                        <input type="date" name="date_installation" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                                        <input type="date" name="date_expiration_logiciel" class="w-full px-3 py-2 border border-gray-300 rounded">
                                    </div>
                                    
                                    <!-- Support -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Support technique</label>
                                        <select name="support_technique" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Responsable -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable IT</label>
                                        <input type="text" name="responsable_it" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="ex: Service Informatique">
                                    </div>
                                    
                                    <!-- Conformité -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Conformité légale</label>
                                        <select name="conformite_legale" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Conforme">Conforme</option>
                                            <option value="Non conforme">Non conforme</option>
                                            <option value="En vérification">En vérification</option>
                                            <option value="À régulariser">À régulariser</option>
                                        </select>
                                    </div>
                                    
                                    <!-- État -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">État</label>
                                        <select name="etat_logiciel_systeme" class="w-full px-3 py-2 border border-gray-300 rounded">
                                            <option value="Actif">Actif</option>
                                            <option value="Inactif">Inactif</option>
                                            <option value="Expiré">Expiré</option>
                                            <option value="À renouveler">À renouveler</option>
                                            <option value="En test">En test</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Notes -->
                                    <div class="col-span-1 md:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea name="notes_logiciel" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded" placeholder="Informations complémentaires..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                </div>

            </div>

            <!-- Section Logiciel (existante) -->
            <div id="section-logiciel" class="hidden">
                <!-- ... (code logiciel existant) ... -->
                 
                <!-- Logiciel (section principale) -->
                
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">3. Informations Logiciel</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Éditeur</label>
                            <input type="text" name="editeur" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Version</label>
                            <input type="text" name="version" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Type Licence *</label>
                            <select name="type_licence" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                                <option value="Perpétuelle">Perpétuelle</option>
                                <option value="Abonnement annuel">Abonnement annuel</option>
                                <option value="Abonnement mensuel">Abonnement mensuel</option>
                                <option value="Trial">Trial</option>
                                <option value="Gratuit">Gratuit</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nombre Licences</label>
                            <input type="number" name="nombre_licences" value="1" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Date Expiration</label>
                            <input type="date" name="date_expiration_licence" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Référence Licence</label>
                            <input type="text" name="reference_licence" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">État *</label>
                            <select name="etat_logiciel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500">
                                <option value="Actif">Actif</option>
                                <option value="Inactif">Inactif</option>
                                <option value="Expiré">Expiré</option>
                                <option value="À renouveler">À renouveler</option>
                            </select>
                        </div>
                    </div>
                
            </div>
        </div>
<!-- ... reste du code avant ... -->

                <!-- Boutons de soumission (cachés initialement) -->
               <!-- Boutons de soumission (cachés initialement) -->
                <div id="submit-buttons" class="hidden flex gap-4 mt-8 pt-8 border-t">
                    <button type="button" onclick="validateForm(event)" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        ✅ Créer l'équipement
                    </button>
                    <a href="{{ route('equipment.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition">
                        ↩️ Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Données de mapping Type -> Catégories -> Sous-catégories
const categoriesData = {
    'Réseau': {
        'Connectivité & Transmission': ['Switches (L2/L3) 🖧', 'Routeurs 🛣️', 'Points d\'accès Wi-Fi / Contrôleurs Wi-Fi 📶', 'Modems 🌐', 'Convertisseurs Fibre (SFP, GBIC, Media converter) 🔄'],
        'Sécurité Réseau': ['Pare-feu (Firewall) 🛡️', 'UTM / Appliances de sécurité 🛡️', 'Passerelles VPN 🔐', 'IDS/IPS 🚨'],
        'Infrastructure & Support': ['Baies et armoires réseau 🗄️', 'Panneaux de brassage 🔌', 'Câblage RJ45 / Fibre optique 🔌', 'Onduleurs (UPS) ⚡', 'PDU (Multiprises intelligentes) 🔌']
    },
    'Électronique': {
        'Vidéosurveillance (CCTV)': ['Caméras IP (fixes, PTZ, dôme) 🎥', 'NVR / DVR 📼', 'Serveurs d\'archivage vidéos 🗄️', 'Moniteurs de contrôle 🖥️'],
        'Contrôle d\'accès': ['Badges / Lecteurs RFID 🪪', 'Serrures électroniques 🔐', 'Tourniquets / Portillons 🚪', 'Unités de contrôle et software 🧠'],
        'Systèmes d\'alarme': ['Alarmes anti-intrusion 🚨', 'Détecteurs de mouvement 🕵️', 'Détecteurs d\'ouverture 🚪', 'Centrale d\'alarme 🧠']
    },
    'Informatique': {
        'Postes Utilisateurs': ['Ordinateurs de bureau', 'Ordinateurs portables', 'Écrans', 'Claviers / Souris'],
        'Périphériques': ['Imprimantes 🖨️', 'Scanners 📠', 'Onduleurs individuels 🔋', 'Projecteurs / Écrans interactifs 📽️'],
        'Serveurs & Stockage': ['Serveurs physiques (Rack / Tour) 🖥️', 'NAS / SAN 🗄️', 'Baies de stockage 🧱', 'Solutions de backup (Tape library, disque dur externe) 💾'],
        'Matériel d\'Administration & Support': ['Outils de diagnostic (IT / Réseau) 🧰', 'KVM (Keyboard Video Mouse) 🖥️', 'Barras de test (câblage) 🧪', 'Logiciels systèmes et outils métiers 💻']
    },
    'Logiciel': {
        'Logiciels': ['Système d\'exploitation', 'Antivirus', 'Bureautique', 'Sécurité', 'Utilitaire', 'Métier']
    }
};

// Mapping Type vers section d'affichage
const typeSectionMap = {
    'Réseau': 'section-reseau',
    'Électronique': 'section-electronique',
    'Informatique': 'section-informatique',
    'Logiciel': 'section-logiciel'
};

// Mapping des sous-catégories réseau
const reseauSubcatMap = {
    'Switches (L2/L3) 🖧': 'subcat-switches',
    'Routeurs 🛣️': 'subcat-routeurs',
    'Points d\'accès Wi-Fi / Contrôleurs Wi-Fi 📶': 'subcat-wifi',
    'Modems 🌐': 'subcat-modems',
    'Convertisseurs Fibre (SFP, GBIC, Media converter) 🔄': 'subcat-convertisseurs',
    'Pare-feu (Firewall) 🛡️': 'subcat-firewall',
    'UTM / Appliances de sécurité 🛡️': 'subcat-utm',
    'Passerelles VPN 🔐': 'subcat-vpn',
    'IDS/IPS 🚨': 'subcat-ids-ips',
    'Baies et armoires réseau 🗄️': 'subcat-baies-armoires',
    'Panneaux de brassage 🔌': 'subcat-panneaux-brassage',
    'Câblage RJ45 / Fibre optique 🔌': 'subcat-cablage',
    'Onduleurs (UPS) ⚡': 'subcat-onduleurs',
    'PDU (Multiprises intelligentes) 🔌': 'subcat-pdu'
};

// Mapping des sous-catégories électroniques
const electroniqueSubcatMap = {
    'Caméras IP (fixes, PTZ, dôme) 🎥': 'subcat-cameras',
    'NVR / DVR 📼': 'subcat-nvr-dvr',
    'Serveurs d\'archivage vidéos 🗄️': 'subcat-archivage-video',
    'Moniteurs de contrôle 🖥️': 'subcat-moniteurs-controle',
    'Badges / Lecteurs RFID 🪪': 'subcat-badges-rfid',
    'Serrures électroniques 🔐': 'subcat-serrure-electronique',
    'Tourniquets / Portillons 🚪': 'subcat-tourniquets',
    'Unités de contrôle et software 🧠': 'subcat-controle-software',
    'Alarmes anti-intrusion 🚨': 'subcat-alarme-anti-intrusion',
    'Détecteurs de mouvement 🕵️': 'subcat-detecteur-mouvement',
    'Détecteurs d\'ouverture 🚪': 'subcat-detecteur-ouverture',
    'Centrale d\'alarme 🧠': 'subcat-centrale-alarme'
};

// Mapping des sous-catégories informatiques
const informatiqueSubcatMap = {
    'Ordinateurs de bureau': 'subcat-bureau',
    'Ordinateurs portables': 'subcat-portable',
    'Écrans': 'subcat-ecran',
    'Claviers / Souris': 'subcat-peripherique',
    'Imprimantes 🖨️': 'subcat-imprimante',
    'Scanners 📠': 'subcat-scanner',
    'Onduleurs individuels 🔋': 'subcat-onduleur',
    'Projecteurs / Écrans interactifs 📽️': 'subcat-projection',
    'Serveurs physiques (Rack / Tour) 🖥️': 'subcat-serveur',
    'NAS / SAN 🗄️': 'subcat-nas',
    'Baies de stockage 🧱': 'subcat-baie-stockage',
    'Solutions de backup (Tape library, disque dur externe) 💾': 'subcat-backup',
    'Outils de diagnostic (IT / Réseau) 🧰': 'subcat-outil-diagnostic',
    'KVM (Keyboard Video Mouse) 🖥️': 'subcat-kvm',
    'Barras de test (câblage) 🧪': 'subcat-barre-test',
    'Logiciels systèmes et outils métiers 💻': 'subcat-logiciel-systeme'
};

// Fonction pour vérifier si tous les choix sont faits
function checkFormComplete() {
    const type = document.getElementById('type').value;
    const categorie = document.getElementById('categorie').value;
    const sousCategorie = document.getElementById('sous_categorie').value;
    const submitButtons = document.getElementById('submit-buttons');
    
    // Vérifier si tous les 3 sélecteurs sont remplis
    if (type && categorie && sousCategorie) {
        submitButtons.classList.remove('hidden');
    } else {
        submitButtons.classList.add('hidden');
    }
}

function updateCategories() {
    const type = document.getElementById('type').value;
    const categorieSelect = document.getElementById('categorie');
    const sousCategorieSelect = document.getElementById('sous_categorie');
    const mainForm = document.getElementById('main-form');
    
    // Réinitialiser
    categorieSelect.innerHTML = '<option value="">-- Sélectionner --</option>';
    sousCategorieSelect.innerHTML = '<option value="">-- Sélectionner catégorie d\'abord --</option>';
    mainForm.classList.add('hidden');
    document.getElementById('submit-buttons').classList.add('hidden');
    
    if (type && categoriesData[type]) {
        Object.keys(categoriesData[type]).forEach(cat => {
            const option = document.createElement('option');
            option.value = cat;
            option.textContent = cat;
            categorieSelect.appendChild(option);
        });
        categorieSelect.disabled = false;
    } else {
        categorieSelect.disabled = true;
    }
    
    sousCategorieSelect.disabled = true;
}

function updateSousCategories() {
    const type = document.getElementById('type').value;
    const categorie = document.getElementById('categorie').value;
    const sousCategorieSelect = document.getElementById('sous_categorie');
    const mainForm = document.getElementById('main-form');
    
    // Réinitialiser
    sousCategorieSelect.innerHTML = '<option value="">-- Sélectionner --</option>';
    mainForm.classList.add('hidden');
    document.getElementById('submit-buttons').classList.add('hidden');
    
    if (type && categorie && categoriesData[type] && categoriesData[type][categorie]) {
        categoriesData[type][categorie].forEach(souscat => {
            const option = document.createElement('option');
            option.value = souscat;
            option.textContent = souscat;
            sousCategorieSelect.appendChild(option);
        });
        sousCategorieSelect.disabled = false;
    } else {
        sousCategorieSelect.disabled = true;
    }
}

function showMainForm() {
    const type = document.getElementById('type').value;
    const categorie = document.getElementById('categorie').value;
    const sousCategorie = document.getElementById('sous_categorie').value;
    const mainForm = document.getElementById('main-form');
    
    console.log('showMainForm appelée avec:', { type, categorie, sousCategorie });
    
    if (type && categorie && sousCategorie) {
        // Remplir les champs cachés AVEC LES BONS NOMS
        document.getElementById('hidden-type').value = type;
        document.getElementById('hidden-category').value = categorie;
        document.getElementById('hidden-sub-category').value = sousCategorie;
        
        // Afficher le formulaire principal
        mainForm.classList.remove('hidden');
        
        // Masquer le message d'instruction
        document.getElementById('selection-message').classList.add('hidden');
        
        // Mettre à jour les champs spécifiques
        updateSpecificFields();
        
        // Afficher les boutons de soumission
        document.getElementById('submit-buttons').classList.remove('hidden');
        
        // DEBUG: Vérifier que les champs sont bien remplis
        console.log('Champs cachés après remplissage:');
        console.log('hidden-type:', document.getElementById('hidden-type').value);
        console.log('hidden-category:', document.getElementById('hidden-category').value);
        console.log('hidden-sub-category:', document.getElementById('hidden-sub-category').value);
        
        // Faire défiler vers le formulaire
        mainForm.scrollIntoView({ behavior: 'smooth' });
    } else {
        console.log('Champs manquants:', { type, categorie, sousCategorie });
        mainForm.classList.add('hidden');
        document.getElementById('submit-buttons').classList.add('hidden');
    }
}

function updateSpecificFields() {
    const type = document.getElementById('type').value;
    const sousCategorie = document.getElementById('sous_categorie').value;
    
    // Masquer tous les sections principales
    document.querySelectorAll('[id^="section-"]').forEach(el => el.classList.add('hidden'));
    
    // Afficher la section correspondante au type
    if (typeSectionMap[type]) {
        document.getElementById(typeSectionMap[type]).classList.remove('hidden');
    }
    
    // Afficher les champs marque/modele sauf pour Logiciel
    const marqueField = document.getElementById('field-marque');
    const modeleField = document.getElementById('field-modele');
    
    if (type && type !== 'Logiciel') {
        marqueField.classList.remove('hidden');
        modeleField.classList.remove('hidden');
    } else {
        marqueField.classList.add('hidden');
        modeleField.classList.add('hidden');
    }
    
    // Gestion des sous-catégories Réseau
    if (type === 'Réseau') {
        // Masquer tous les détails de sous-catégorie réseau
        document.querySelectorAll('[id^="subcat-"]').forEach(el => el.classList.add('hidden'));
        
        // Afficher les détails correspondants
        if (reseauSubcatMap[sousCategorie]) {
            document.getElementById(reseauSubcatMap[sousCategorie]).classList.remove('hidden');
        }
    }
    
    // Gestion des sous-catégories Électronique
    if (type === 'Électronique') {
        // Masquer tous les détails de sous-catégorie électronique
        document.querySelectorAll('[id^="subcat-"]').forEach(el => el.classList.add('hidden'));
        
        // Afficher les détails correspondants
        if (electroniqueSubcatMap[sousCategorie]) {
            document.getElementById(electroniqueSubcatMap[sousCategorie]).classList.remove('hidden');
            
            // Réinitialiser les détails des sous-formulaires spécifiques
            if (sousCategorie === 'Badges / Lecteurs RFID 🪪') {
                resetRFIDDetails();
            }
            if (sousCategorie === 'Unités de contrôle et software 🧠') {
                resetControleDetails();
            }
        }
    }
    
    // Gestion des sous-catégories Informatique
    if (type === 'Informatique') {
        // Masquer tous les détails de sous-catégorie informatique
        document.querySelectorAll('[id^="subcat-"]').forEach(el => el.classList.add('hidden'));
        
        // Afficher les détails correspondants
        if (informatiqueSubcatMap[sousCategorie]) {
            document.getElementById(informatiqueSubcatMap[sousCategorie]).classList.remove('hidden');
            
            // Réinitialiser les détails de projection
            if (sousCategorie === 'Projecteurs / Écrans interactifs 📽️') {
                resetProjectionDetails();
            }
            // Réinitialiser les détails de backup
            if (sousCategorie === 'Solutions de backup (Tape library, disque dur externe) 💾') {
                resetBackupDetails();
            }
            // Calculer licences disponibles pour logiciels systèmes
            if (sousCategorie === 'Logiciels systèmes et outils métiers 💻') {
                calculateAvailableLicenses();
            }
        }
    }
    
    // Vérifier si le formulaire est complet
    checkFormComplete();
}

// Fonctions auxiliaires pour les sous-formulaires spécifiques
function toggleContractFields() {
    const checkbox = document.querySelector('input[name="contrat_maintenance"]');
    const contractFields = document.getElementById('contract-fields');
    if (contractFields) {
        contractFields.classList.toggle('hidden', !checkbox.checked);
    }
}

function toggleRFIDDetails() {
    const type = document.querySelector('select[name="type_element_rfid"]');
    if (type) {
        const value = type.value;
        const badgeDetails = document.getElementById('badge-rfid-details');
        const lecteurDetails = document.getElementById('lecteur-rfid-details');
        
        badgeDetails.classList.add('hidden');
        lecteurDetails.classList.add('hidden');
        
        if (value === 'badge') {
            badgeDetails.classList.remove('hidden');
        } else if (value === 'lecteur') {
            lecteurDetails.classList.remove('hidden');
        }
    }
}

function toggleControleDetails() {
    const type = document.querySelector('select[name="type_controle_acces"]');
    if (type) {
        const value = type.value;
        const uniteDetails = document.getElementById('unite-controle-details');
        const logicielDetails = document.getElementById('logiciel-controle-details');
        
        uniteDetails.classList.add('hidden');
        logicielDetails.classList.add('hidden');
        
        if (value === 'unite_controle') {
            uniteDetails.classList.remove('hidden');
        } else if (value === 'logiciel_controle') {
            logicielDetails.classList.remove('hidden');
        }
    }
}

function toggleProjectionDetails() {
    const type = document.querySelector('select[name="type_projection"]');
    if (type) {
        const value = type.value;
        const projecteurDetails = document.getElementById('projecteur-details');
        const ecranDetails = document.getElementById('ecran-interactif-details');
        
        projecteurDetails.classList.add('hidden');
        ecranDetails.classList.add('hidden');
        
        if (value === 'projecteur') {
            projecteurDetails.classList.remove('hidden');
        } else if (value === 'ecran_interactif') {
            ecranDetails.classList.remove('hidden');
        }
    }
}

function toggleBackupDetails() {
    const type = document.querySelector('select[name="type_backup"]');
    if (type) {
        const value = type.value;
        const tapeDetails = document.getElementById('tape-library-details');
        const disqueDetails = document.getElementById('disque-externe-details');
        
        tapeDetails.classList.add('hidden');
        disqueDetails.classList.add('hidden');
        
        if (value === 'tape_library') {
            tapeDetails.classList.remove('hidden');
        } else if (value === 'disque_externe') {
            disqueDetails.classList.remove('hidden');
        }
    }
}

function resetRFIDDetails() {
    const select = document.querySelector('select[name="type_element_rfid"]');
    if (select) {
        select.value = '';
        const badgeDetails = document.getElementById('badge-rfid-details');
        const lecteurDetails = document.getElementById('lecteur-rfid-details');
        if (badgeDetails && lecteurDetails) {
            badgeDetails.classList.add('hidden');
            lecteurDetails.classList.add('hidden');
        }
    }
}

function resetControleDetails() {
    const select = document.querySelector('select[name="type_controle_acces"]');
    if (select) {
        select.value = '';
        const uniteDetails = document.getElementById('unite-controle-details');
        const logicielDetails = document.getElementById('logiciel-controle-details');
        if (uniteDetails && logicielDetails) {
            uniteDetails.classList.add('hidden');
            logicielDetails.classList.add('hidden');
        }
    }
}

function resetProjectionDetails() {
    const select = document.querySelector('select[name="type_projection"]');
    if (select) {
        select.value = '';
        const projecteurDetails = document.getElementById('projecteur-details');
        const ecranDetails = document.getElementById('ecran-interactif-details');
        if (projecteurDetails && ecranDetails) {
            projecteurDetails.classList.add('hidden');
            ecranDetails.classList.add('hidden');
        }
    }
}

function resetBackupDetails() {
    const select = document.querySelector('select[name="type_backup"]');
    if (select) {
        select.value = '';
        const tapeDetails = document.getElementById('tape-library-details');
        const disqueDetails = document.getElementById('disque-externe-details');
        if (tapeDetails && disqueDetails) {
            tapeDetails.classList.add('hidden');
            disqueDetails.classList.add('hidden');
        }
    }
}

function calculateAvailableLicenses() {
    const total = document.querySelector('input[name="nombre_licences_logiciel"]');
    const used = document.querySelector('input[name="licences_utilisees"]');
    const available = document.querySelector('input[name="licences_disponibles"]');
    
    if (total && used && available) {
        total.addEventListener('input', updateAvailableLicenses);
        used.addEventListener('input', updateAvailableLicenses);
        updateAvailableLicenses();
    }
}

function updateAvailableLicenses() {
    const total = document.querySelector('input[name="nombre_licences_logiciel"]');
    const used = document.querySelector('input[name="licences_utilisees"]');
    const available = document.querySelector('input[name="licences_disponibles"]');
    
    if (total && used && available) {
        const totalVal = parseInt(total.value) || 0;
        const usedVal = parseInt(used.value) || 0;
        available.value = Math.max(0, totalVal - usedVal);
    }
}

// FONCTION DE VALIDATION PRINCIPALE
function validateForm(event) {
    event.preventDefault();
    
    // Récupérer les sélections principales
    const type = document.getElementById('type').value;
    const categorie = document.getElementById('categorie').value;
    const sousCategorie = document.getElementById('sous_categorie').value;
    
    // Validation des sélections
    if (!type || !categorie || !sousCategorie) {
        alert('Veuillez compléter la classification de l\'équipement :\n- Type\n- Catégorie\n- Sous-catégorie');
        return false;
    }
    
    // Remplir les champs cachés
    document.getElementById('hidden-type').value = type;
    document.getElementById('hidden-category').value = categorie;
    document.getElementById('hidden-sub-category').value = sousCategorie;
    
    // Validation des champs communs obligatoires
    const errors = [];
    
    // Champs communs obligatoires (toujours visibles)
    if (!document.querySelector('[name="numero_serie"]').value) errors.push('N° Série');
    
    if (type !== 'Logiciel') {
        if (!document.querySelector('[name="marque"]').value) errors.push('Marque');
        if (!document.querySelector('[name="modele"]').value) errors.push('Modèle');
    }
    
    if (!document.querySelector('[name="date_livraison"]').value) errors.push('Date Livraison');
    if (!document.querySelector('[name="prix"]').value) errors.push('Prix');
    if (!document.querySelector('[name="etat"]').value) errors.push('État');
    if (!document.querySelector('[name="garantie"]').value) errors.push('Garantie');
    
    // Validation spécifique selon le type
    if (type === 'Réseau') {
        if (!document.querySelector('[name="etat_reseau"]').value) errors.push('État Réseau');
    }
    
    if (type === 'Électronique') {
        if (!document.querySelector('[name="etat_electronique"]').value) errors.push('État Électronique');
    }
    
    if (type === 'Informatique') {
        if (!document.querySelector('[name="etat_stock"]').value) errors.push('État Stock');
    }
    
    if (type === 'Logiciel') {
        if (!document.querySelector('[name="type_licence"]').value) errors.push('Type Licence');
        if (!document.querySelector('[name="etat_logiciel"]').value) errors.push('État Logiciel');
    }
    
    // Si erreurs, afficher et arrêter
    if (errors.length > 0) {
        alert('Champs obligatoires manquants :\n\n• ' + errors.join('\n• '));
        return false;
    }
    
    // Si tout est bon, soumettre le formulaire
    document.getElementById('equipmentForm').submit();
}

// Initialisation après chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Écouter les changements sur les sélecteurs
    document.getElementById('type').addEventListener('change', function() {
        updateCategories();
        checkFormComplete();
    });
    
    document.getElementById('categorie').addEventListener('change', function() {
        updateSousCategories();
        checkFormComplete();
    });
    
    document.getElementById('sous_categorie').addEventListener('change', function() {
        showMainForm();
        checkFormComplete();
    });
    
    // Modifier le comportement du bouton de soumission
    const submitButton = document.querySelector('button[type="submit"]');
    if (submitButton) {
        // Remplacer par bouton avec validation personnalisée
        submitButton.type = 'button';
        submitButton.addEventListener('click', validateForm);
    }
    
    // Initialiser la vérification
    checkFormComplete();
    
    // Ajouter les écouteurs pour les sous-formulaires
    // RFID
    const typeRfid = document.querySelector('select[name="type_element_rfid"]');
    if (typeRfid) {
        typeRfid.addEventListener('change', toggleRFIDDetails);
    }
    
    // Contrôle d'accès
    const typeControle = document.querySelector('select[name="type_controle_acces"]');
    if (typeControle) {
        typeControle.addEventListener('change', toggleControleDetails);
    }
    
    // Projection
    const typeProjection = document.querySelector('select[name="type_projection"]');
    if (typeProjection) {
        typeProjection.addEventListener('change', toggleProjectionDetails);
    }
    
    // Backup
    const typeBackup = document.querySelector('select[name="type_backup"]');
    if (typeBackup) {
        typeBackup.addEventListener('change', toggleBackupDetails);
    }
    
    // Contrat maintenance
    const contratCheckbox = document.querySelector('input[name="contrat_maintenance"]');
    if (contratCheckbox) {
        contratCheckbox.addEventListener('change', toggleContractFields);
    }
});
</script>
<script>
// FONCTION DE DÉBOGAGE DÉTAILLÉE
function debugFormData(event) {
    event.preventDefault();
    
    console.log('=== DÉBOGAGE DU FORMULAIRE ===');
    
    // 1. Afficher les sélections principales
    const type = document.getElementById('type').value;
    const categorie = document.getElementById('categorie').value;
    const sousCategorie = document.getElementById('sous_categorie').value;
    
    console.log('Sélections principales:');
    console.log('- Type:', type);
    console.log('- Catégorie:', categorie);
    console.log('- Sous-catégorie:', sousCategorie);
    console.log('');
    
    // 2. Afficher les champs cachés
    const hiddenType = document.getElementById('hidden-type').value;
    const hiddenCategory = document.getElementById('hidden-category').value;
    const hiddenSubCategory = document.getElementById('hidden-sub-category').value;
    
    console.log('Champs cachés:');
    console.log('- hidden-type:', hiddenType);
    console.log('- hidden-category:', hiddenCategory);
    console.log('- hidden-sub-category:', hiddenSubCategory);
    console.log('');
    
    // 3. Collecter TOUTES les données du formulaire
    const form = document.getElementById('equipmentForm');
    const formData = new FormData(form);
    const formDataObject = {};
    
    console.log('=== TOUTES LES DONNÉES DU FORMULAIRE ===');
    
    // Parcourir toutes les entrées
    for (let [key, value] of formData.entries()) {
        formDataObject[key] = value;
        console.log(`${key}:`, value);
    }
    
    console.log('');
    
    // 4. Vérifier les champs spécifiques problématiques
    const problemFields = [
        'categorie_id', 
        'sous_categorie_id',
        'numero_serie',
        'marque',
        'modele',
        'etat'
    ];
    
    console.log('=== CHAMPS PROBLÉMATIQUES ===');
    problemFields.forEach(field => {
        const value = formData.get(field);
        console.log(`${field}:`, value ? `✓ "${value}"` : '✗ MANQUANT');
    });
    
    console.log('');
    
    // 5. Vérifier la section active
    console.log('=== SECTION ACTIVE ===');
    const sections = ['section-reseau', 'section-electronique', 'section-informatique', 'section-logiciel'];
    sections.forEach(section => {
        const el = document.getElementById(section);
        if (el && !el.classList.contains('hidden')) {
            console.log(`Section active: ${section}`);
        }
    });
    
    // 6. Créer un objet de données structuré pour l'API
    const structuredData = {};
    
    // Données de base
    structuredData.type = type;
    structuredData.category = categorie;
    structuredData.sub_category = sousCategorie;
    
    // Données communes
    structuredData.numero_serie = formData.get('numero_serie');
    structuredData.marque = formData.get('marque');
    structuredData.modele = formData.get('modele');
    structuredData.date_livraison = formData.get('date_livraison');
    structuredData.prix = formData.get('prix');
    structuredData.etat = formData.get('etat');
    
    // 7. Afficher les données structurées
    console.log('=== DONNÉES STRUCTURÉES (JSON) ===');
    console.log(JSON.stringify(structuredData, null, 2));
    
    // 8. Simuler l'envoi pour voir ce qui serait envoyé
    console.log('=== SIMULATION D\'ENVOI ===');
    const simulatedData = {};
    for (let [key, value] of formData.entries()) {
        simulatedData[key] = value;
    }
    console.log('Données qui seraient envoyées:', simulatedData);
    
    // 9. Demander confirmation pour envoyer les vraies données
    const confirmSend = confirm('Voulez-vous voir les données réelles envoyées au serveur ?\n\nAppuyez sur OK pour afficher dans la console, ou Annuler pour annuler le débogage.');
    
    if (confirmSend) {
        // Soumettre le formulaire (en commentant d'abord la prévention par défaut)
        event.stopPropagation();
        console.log('=== ENVOI DES DONNÉES RÉELLES ===');
        
        // Remplir les champs manquants si nécessaire
        if (!formData.get('categorie')) {
            document.getElementById('hidden-category').value = categorie;
            console.log('⚠️ Champ "categorie" rempli avec:', categorie);
        }
        
        if (!formData.get('sub_category')) {
            document.getElementById('hidden-sub-category').value = sousCategorie;
            console.log('⚠️ Champ "sub_category" rempli avec:', sousCategorie);
        }
        
        // Ajouter un écouteur pour capturer la réponse
        const originalSubmit = form.submit;
        form.addEventListener('submit', function(e) {
            console.log('Formulaire soumis avec succès');
        });
        
        // Soumettre le formulaire
        form.submit();
    }
    
    return false;
}

// MODIFIEZ LA FONCTION validateForm POUR INCLURE LE DÉBOGAGE
function validateForm(event) {
    // D'abord, faire le débogage
    debugFormData(event);
    
    // Ensuite, continuer avec la validation normale (commentée temporairement)
    // event.preventDefault();
    
    // Votre code de validation existant...
    // ... (gardez votre code de validation ici)
    
    return false; // Empêcher la soumission normale pendant le débogage
}

// AJOUTEZ UN BOUTON DE DÉBOGAGE (optionnel)
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter un bouton de débogage
    const submitButtons = document.getElementById('submit-buttons');
    if (submitButtons) {
        const debugButton = document.createElement('button');
        debugButton.type = 'button';
        debugButton.className = 'bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-lg transition';
        debugButton.textContent = '🐛 Debug Form';
        debugButton.onclick = debugFormData;
        
        submitButtons.insertBefore(debugButton, submitButtons.firstChild);
    }
});

// AJOUTEZ CE SCRIPT DANS VOTRE FORMULAIRE BLADE
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('equipmentForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Vérifier que les champs cachés sont remplis
        const hiddenType = document.getElementById('hidden-type').value;
        const hiddenCategory = document.getElementById('hidden-category').value;
        const hiddenSubCategory = document.getElementById('hidden-sub-category').value;
        
        console.log('Vérification des champs cachés:');
        console.log('Type:', hiddenType);
        console.log('Catégorie:', hiddenCategory);
        console.log('Sous-catégorie:', hiddenSubCategory);
        
        if (!hiddenType || !hiddenCategory || !hiddenSubCategory) {
            alert('ERREUR: Les champs de classification ne sont pas remplis!\n\n' +
                  'Veuillez sélectionner:\n' +
                  '1. Type d\'équipement\n' +
                  '2. Catégorie\n' +
                  '3. Sous-catégorie\n\n' +
                  'Revenez à l\'étape 1 et faites vos sélections.');
            return false;
        }
        
        // Remplir aussi les champs visibles au cas où
        document.getElementById('type').value = hiddenType;
        document.getElementById('categorie').value = hiddenCategory;
        document.getElementById('sous_categorie').value = hiddenSubCategory;
        
        // Soumettre le formulaire
        form.submit();
    });
});
</script>
<script>
// TEST FINAL - VÉRIFICATION DES DONNÉES
console.log('=== TEST FORMULAIRE ===');
console.log('Type:', document.getElementById('hidden-type').value);
console.log('Catégorie:', document.getElementById('hidden-category').value);
console.log('Sous-catégorie:', document.getElementById('hidden-sub-category').value);
console.log('Numéro série:', document.querySelector('[name="numero_serie"]').value);
console.log('Marque:', document.querySelector('[name="marque"]').value);
console.log('Modèle:', document.querySelector('[name="modele"]').value);
console.log('Prix:', document.querySelector('[name="prix"]').value);

// Vérifier que les champs cachés sont bien remplis
const type = document.getElementById('hidden-type').value;
const categorie = document.getElementById('hidden-category').value;
const sousCategorie = document.getElementById('hidden-sub-category').value;

if (!type || !categorie || !sousCategorie) {
    console.error('❌ ERREUR: Champs de classification non remplis!');
    alert('Veuillez d\'abord sélectionner le type, la catégorie et la sous-catégorie');
} else {
    console.log('✅ Champs de classification OK');
}
</script>
@endsection