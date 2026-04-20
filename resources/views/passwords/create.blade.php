{{-- resources/views/passwords/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Nouvelle fiche mot de passe')
@section('content')
<div class="p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('passwords.index') }}" class="text-gray-500 hover:text-red-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Nouvelle fiche mot de passe</h1>
            <p class="text-sm text-gray-500">Chiffrement AES-256 — Les mots de passe ne sont jamais stockés en clair</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <p class="text-sm font-semibold text-red-700 mb-1">Veuillez corriger les erreurs suivantes :</p>
        <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('passwords.store') }}"
          enctype="multipart/form-data" id="pwd-form" class="space-y-5" novalidate>
        @csrf

        {{-- ── ÉTAPE 1 : CATÉGORIE ──────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-6 h-6 rounded-full bg-red-600 text-white text-xs font-bold flex items-center justify-center">1</span>
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Catégorie <span class="text-red-500">*</span></h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @php
                $cats = [
                    'Serveur'               => ['icon'=>'🖥️','sub'=>'Physique / VM',            'color'=>'blue'],
                    'Réseau'                => ['icon'=>'🌐','sub'=>'Switch / Routeur / Firewall','color'=>'purple'],
                    'Base de données'       => ['icon'=>'🗄️','sub'=>'Oracle / MySQL / MSSQL',    'color'=>'green'],
                    'Sécurité électronique' => ['icon'=>'📷','sub'=>'Vidéo / Contrôle accès',    'color'=>'orange'],
                    'Active Directory'      => ['icon'=>'👤','sub'=>'Comptes AD / GPO',           'color'=>'indigo'],
                    'Modem/WiFi'            => ['icon'=>'📡','sub'=>'AP / Box 4G / ADSL',         'color'=>'teal'],
                ];
                $currentCat = old('categorie','');
                @endphp
                @foreach($cats as $cat => $m)
                <label class="cursor-pointer">
                    <input type="radio" name="categorie" value="{{ $cat }}" class="sr-only peer cat-radio"
                           {{ $currentCat === $cat ? 'checked' : '' }}>
                    <div class="border-2 border-gray-200 peer-checked:border-{{ $m['color'] }}-500 peer-checked:bg-{{ $m['color'] }}-50
                                rounded-xl p-4 text-center transition-all hover:border-gray-300 hover:shadow-sm">
                        <div class="text-2xl mb-1">{{ $m['icon'] }}</div>
                        <div class="text-sm font-semibold text-gray-700">{{ $cat }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $m['sub'] }}</div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('categorie')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Placeholder --}}
        <div id="placeholder" class="{{ $currentCat ? 'hidden' : '' }}
             bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-10 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            <p class="text-gray-500 font-medium">Sélectionnez une catégorie ci-dessus</p>
            <p class="text-sm text-gray-400 mt-1">Le formulaire adapté s'affichera automatiquement</p>
        </div>

        {{-- ════════════════════════════════════════════════════════════════════
             SECTION PRINCIPALE — visible selon catégorie
             IMPORTANT : un seul champ "nom", "compte", "mot_de_passe" pour tous
        ════════════════════════════════════════════════════════════════════ --}}
        <div id="main-form" class="{{ $currentCat ? '' : 'hidden' }} space-y-5">

            {{-- Titre de section dynamique --}}
            <div class="flex items-center gap-2 px-1">
                <span class="w-6 h-6 rounded-full bg-gray-800 text-white text-xs font-bold flex items-center justify-center" id="step-badge">2</span>
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide" id="section-title">Informations</h2>
            </div>

            {{-- ── Bloc commun NOM (présent dans toutes les catégories) ──────── --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm" id="block-principal">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1" id="label-nom">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" id="field-nom" value="{{ old('nom') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400"
                               placeholder="">
                        @error('nom')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- Nom EXI (Serveur, BDD) --}}
                    <div id="row-nom-exi" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom EXI</label>
                        <input type="text" name="nom_exi" value="{{ old('nom_exi') }}" placeholder="Identifiant EXI interne"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>

                    {{-- IP --}}
                    <div id="row-ip">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse IP</label>
                        <input type="text" name="adresse_ip" value="{{ old('adresse_ip') }}" placeholder="192.168.x.x"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>

                    {{-- Nom VM (Serveur, BDD) --}}
                    <div id="row-nom-vm" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom VM</label>
                        <input type="text" name="nom_vm" value="{{ old('nom_vm') }}" placeholder="Nom de la machine virtuelle"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>

                    {{-- IP VM (Serveur, BDD) --}}
                    <div id="row-ip-vm" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse IP VM</label>
                        <input type="text" name="adresse_ip_vm" value="{{ old('adresse_ip_vm') }}" placeholder="10.x.x.x"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>

                    {{-- Instance / Service (BDD) --}}
                    <div id="row-instance" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instance / Service</label>
                        <input type="text" name="instance" value="{{ old('instance') }}" placeholder="ex: ORCL, FLEXCUBE, MSSQLSERVER"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>

                    {{-- Type équipement (Réseau, Sécu, Modem) --}}
                    <div id="row-type-equip" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1" id="label-type-equip">Type</label>
                        <select name="type_equipement" id="select-type-equip"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <option value="">Sélectionner…</option>
                        </select>
                    </div>

                    {{-- OU / SSID (AD, Modem) --}}
                    <div id="row-extra" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1" id="label-extra">Extra</label>
                        <input type="text" name="nom_exi" id="field-extra" value="{{ old('nom_exi') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>

                    {{-- Protocole --}}
                    <div id="row-protocole">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Protocole d'accès</label>
                        <select name="protocole" id="select-protocole"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <option value="">—</option>
                        </select>
                    </div>

                    {{-- Site --}}
                    <div id="row-site">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                        <select name="site" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <option value="">Sélectionner…</option>
                            @foreach($sites as $s)<option value="{{ $s }}" @selected(old('site')===$s)>{{ $s }}</option>@endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ── Bloc Accès (compte + mot de passe) ─────────────────────────── --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Accès</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Compte BDD avec sélecteur --}}
                    <div id="row-compte-bdd" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Compte <span class="text-red-500">*</span></label>
                        <select id="select-compte-bdd"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                onchange="document.getElementById('field-compte').value=this.value==='Autre'?'':this.value">
                            <option value="">Sélectionner…</option>
                            @foreach(['SYS as SYSDBA','SYSTEM','DVACCTMGR','Utilisateur de gestion','sa (SQL Server)','root','Autre'] as $c)
                                <option value="{{ $c }}" @selected(old('compte')===$c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Compte texte libre --}}
                    <div id="row-compte-libre">
                        <label class="block text-sm font-medium text-gray-700 mb-1" id="label-compte">Compte <span class="text-red-500">*</span></label>
                        <input type="text" name="compte" id="field-compte" value="{{ old('compte') }}" placeholder="ex: admin, root…"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                        @error('compte')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- Mot de passe --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" name="mot_de_passe" id="field-mdp" value="{{ old('mot_de_passe') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-20 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <div class="absolute inset-y-0 right-0 flex items-center gap-0.5 pr-1">
                                <button type="button" onclick="toggleMdp()" title="Voir/masquer"
                                        class="p-1.5 text-gray-400 hover:text-gray-700 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button type="button" onclick="generateMdp()" title="Générer"
                                        class="p-1.5 text-gray-400 hover:text-green-600 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </button>
                            </div>
                        </div>
                        @error('mot_de_passe')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- ── Expiration & Renouvellement ─────────────────────────────────── --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Expiration & Renouvellement</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                        <input type="date" name="date_expiration" value="{{ old('date_expiration') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Renouvellement (jours)</label>
                        <input type="number" name="duree_renouvellement" value="{{ old('duree_renouvellement') }}"
                               min="1" placeholder="ex: 90"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>
                </div>
            </div>

            {{-- ── Champs libres (+ bouton Ajouter) ──────────────────────────── --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Champs personnalisés</h3>
                    <button type="button" onclick="addChampLibre()"
                            class="flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 border border-blue-200 hover:border-blue-400 px-3 py-1.5 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Ajouter un champ
                    </button>
                </div>
                <div id="champs-libres-container" class="space-y-3">
                    @forelse(old('champs_libres', []) as $i => $cl)
                    <div class="champ-libre-row flex gap-2 items-start">
                        <div class="flex-1 grid grid-cols-2 gap-2">
                            <input type="text" name="champs_libres[{{ $i }}][libelle]"
                                   value="{{ $cl['libelle'] ?? '' }}" placeholder="Libellé (ex: Port SSH, URL…)"
                                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <input type="text" name="champs_libres[{{ $i }}][contenu]"
                                   value="{{ $cl['contenu'] ?? '' }}" placeholder="Contenu / valeur"
                                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                        <button type="button" onclick="this.closest('.champ-libre-row').remove()"
                                class="mt-1 p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 italic" id="no-champs-msg">Aucun champ personnalisé. Cliquez sur "+ Ajouter" pour en créer.</p>
                    @endforelse
                </div>
            </div>

            {{-- ── Pièces jointes ──────────────────────────────────────────────── --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Pièces jointes</h3>
                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300
                              rounded-xl cursor-pointer bg-gray-50 hover:bg-blue-50 hover:border-blue-400 transition"
                       id="drop-zone">
                    <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-sm text-gray-500">Glissez vos fichiers ici ou <span class="text-blue-600 font-medium">cliquez pour parcourir</span></p>
                    <p class="text-xs text-gray-400 mt-0.5">Max 5 Mo par fichier</p>
                    <input type="file" name="fichiers[]" id="file-input" multiple class="hidden"
                           onchange="previewFiles(this.files)">
                </label>
                <div id="file-preview" class="mt-3 space-y-2"></div>
            </div>

            {{-- ── Partages initiaux ───────────────────────────────────────────── --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Partager l'accès</h3>
                    <button type="button" onclick="addPartage()"
                            class="flex items-center gap-1.5 text-xs font-medium text-purple-600 hover:text-purple-800 border border-purple-200 hover:border-purple-400 px-3 py-1.5 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Ajouter un utilisateur
                    </button>
                </div>
                <div id="partages-container" class="space-y-2">
                    <p class="text-xs text-gray-400 italic" id="no-partage-msg">Aucun partage. Vous pouvez en ajouter après création depuis la fiche.</p>
                </div>
                <div class="hidden" id="users-json">{{ json_encode($users->map(fn($u)=>['id'=>$u->id,'name'=>$u->name,'email'=>$u->email])) }}</div>
            </div>

            {{-- ── Description ─────────────────────────────────────────────────── --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Description / Notes</h3>
                <textarea name="description" rows="3" placeholder="Informations complémentaires, notes d'exploitation…"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">{{ old('description') }}</textarea>
            </div>

            {{-- Boutons --}}
            <div class="flex justify-end gap-3 pb-4">
                <a href="{{ route('passwords.index') }}"
                   class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    Annuler
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer la fiche
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// ── Config par catégorie ─────────────────────────────────────────────────────
const CAT_CONFIG = {
    'Serveur': {
        color: 'blue', label: '🖥️ Informations Serveur',
        nomPlaceholder: 'ex: SRV-FLEXCUBE-01, SRV-AD-01',
        showNomExi: true, showVm: true, showInstance: false,
        showTypeEquip: false, showExtraField: false,
        protocoles: ['SSH','RDP','HTTPS','HTTP','Console','SFTP'],
        comptePlaceholder: 'ex: root, administrator',
    },
    'Réseau': {
        color: 'purple', label: '🌐 Informations Équipement Réseau',
        nomPlaceholder: 'ex: Fortigate-AGP, SW-Core-01, RTR-TAMBA',
        showNomExi: false, showVm: false, showInstance: false,
        showTypeEquip: true, typeEquipLabel: "Type d'équipement",
        typeEquipOptions: ['Firewall Fortinet','Switch Cisco','Switch HP','Routeur Cisco','Routeur MikroTik','AP WiFi','Autre'],
        showExtraField: false,
        protocoles: ['SSH','HTTPS','HTTP','Telnet','Console','SNMP'],
        comptePlaceholder: 'ex: admin',
    },
    'Base de données': {
        color: 'green', label: '🗄️ Informations Base de Données',
        nomPlaceholder: 'ex: SRV-ORACLE-01, DB-FLEXCUBE',
        showNomExi: true, showVm: true, showInstance: true,
        showTypeEquip: false, showExtraField: false,
        protocoles: ['SSH','RDP','JDBC','ODBC','SQLPlus','SSMS'],
        useBddCompte: true,
        comptePlaceholder: 'Saisir si différent du sélecteur ci-dessus',
    },
    'Sécurité électronique': {
        color: 'orange', label: '📷 Sécurité Électronique',
        nomPlaceholder: 'ex: NVR-AGP, Contrôle-Accès-Hall',
        showNomExi: false, showVm: false, showInstance: false,
        showTypeEquip: true, typeEquipLabel: 'Type de système',
        typeEquipOptions: ["Vidéosurveillance (NVR/DVR)","Contrôle d'accès","Alarme intrusion","Interphone / Visiophone","Autre"],
        showExtraField: false,
        protocoles: ['HTTP','HTTPS','RTSP','Logiciel propriétaire'],
        comptePlaceholder: 'ex: admin',
    },
    'Active Directory': {
        color: 'indigo', label: '👤 Active Directory',
        nomPlaceholder: 'ex: COFINA.LOCAL, DC-01',
        showNomExi: false, showVm: false, showInstance: false,
        showTypeEquip: false,
        showExtraField: true, extraLabel: "Unité d'organisation (OU)",
        extraPlaceholder: 'ex: OU=Admins,DC=COFINA,DC=LOCAL',
        protocoles: [],
        comptePlaceholder: 'ex: Administrator, svc-admin',
    },
    'Modem/WiFi': {
        color: 'teal', label: '📡 Modem / WiFi',
        nomPlaceholder: 'ex: AP-AGP-RDC, Modem-Orange-TAMBA',
        showNomExi: false, showVm: false, showInstance: false,
        showTypeEquip: true, typeEquipLabel: 'Type',
        typeEquipOptions: ["Point d'accès WiFi","Modem ADSL","Box 4G","Routeur WiFi","VSAT"],
        showExtraField: true, extraLabel: 'SSID (réseau WiFi)',
        extraPlaceholder: 'Nom du réseau WiFi diffusé',
        protocoles: ['HTTP','HTTPS','Telnet','WPA2','WPA3'],
        comptePlaceholder: 'ex: admin',
    },
};

// ── Couleurs de focus par catégorie ─────────────────────────────────────────
const COLOR_RING = {
    blue:'focus:ring-blue-500', purple:'focus:ring-purple-500', green:'focus:ring-green-500',
    orange:'focus:ring-orange-500', indigo:'focus:ring-indigo-500', teal:'focus:ring-teal-500',
};

let champsCount = {{ count(old('champs_libres', [])) }};
let partagesCount = 0;

function switchCategorie(cat) {
    const cfg = CAT_CONFIG[cat];
    if (!cfg) return;

    // Afficher la zone principale
    document.getElementById('placeholder').classList.add('hidden');
    document.getElementById('main-form').classList.remove('hidden');

    // Mise à jour titre
    document.getElementById('section-title').textContent = cfg.label;
    document.getElementById('step-badge').className = `w-6 h-6 rounded-full bg-${cfg.color}-600 text-white text-xs font-bold flex items-center justify-center`;

    // Champs visibles selon config
    const show = (id, visible) => {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('hidden', !visible);
    };

    show('row-nom-exi',    cfg.showNomExi);
    show('row-nom-vm',     cfg.showVm);
    show('row-ip-vm',      cfg.showVm);
    show('row-instance',   cfg.showInstance);
    show('row-type-equip', cfg.showTypeEquip);
    show('row-extra',      cfg.showExtraField);

    // Protocoles
    const selProto = document.getElementById('select-protocole');
    selProto.innerHTML = '<option value="">—</option>';
    (cfg.protocoles || []).forEach(p => {
        const o = new Option(p, p);
        if ('{{ old('protocole') }}' === p) o.selected = true;
        selProto.add(o);
    });
    show('row-protocole', (cfg.protocoles || []).length > 0);

    // Type équipement
    if (cfg.showTypeEquip) {
        document.getElementById('label-type-equip').textContent = cfg.typeEquipLabel;
        const selType = document.getElementById('select-type-equip');
        selType.innerHTML = '<option value="">Sélectionner…</option>';
        cfg.typeEquipOptions.forEach(t => {
            const o = new Option(t, t);
            if ('{{ old('type_equipement') }}' === t) o.selected = true;
            selType.add(o);
        });
    }

    // Extra field
    if (cfg.showExtraField) {
        document.getElementById('label-extra').textContent = cfg.extraLabel;
        document.getElementById('field-extra').placeholder = cfg.extraPlaceholder;
    }

    // Compte BDD vs libre
    const isBdd = cat === 'Base de données';
    show('row-compte-bdd', isBdd);
    show('row-compte-libre', !isBdd);
    document.getElementById('field-compte').placeholder = cfg.comptePlaceholder || '';

    // Placeholder du nom
    document.getElementById('field-nom').placeholder = cfg.nomPlaceholder || '';

    // Scroll
    setTimeout(() => document.getElementById('main-form').scrollIntoView({behavior:'smooth',block:'start'}), 60);
}

// ── Champs libres ────────────────────────────────────────────────────────────
function addChampLibre() {
    const noMsg = document.getElementById('no-champs-msg');
    if (noMsg) noMsg.remove();

    const container = document.getElementById('champs-libres-container');
    const div = document.createElement('div');
    div.className = 'champ-libre-row flex gap-2 items-start';
    div.innerHTML = `
        <div class="flex-1 grid grid-cols-2 gap-2">
            <input type="text" name="champs_libres[${champsCount}][libelle]"
                   placeholder="Libellé (ex: Port SSH, URL console…)"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <input type="text" name="champs_libres[${champsCount}][contenu]"
                   placeholder="Contenu / valeur"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <button type="button" onclick="this.closest('.champ-libre-row').remove()"
                class="mt-1 p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>`;
    container.appendChild(div);
    champsCount++;
    div.querySelector('input').focus();
}

// ── Partages ─────────────────────────────────────────────────────────────────
function addPartage() {
    const noMsg = document.getElementById('no-partage-msg');
    if (noMsg) noMsg.remove();

    const users = JSON.parse(document.getElementById('users-json').textContent);
    const container = document.getElementById('partages-container');
    const div = document.createElement('div');
    div.className = 'partage-row flex gap-2 items-center';

    const userOpts = users.map(u => `<option value="${u.id}">${u.name} (${u.email})</option>`).join('');
    div.innerHTML = `
        <select name="partages[${partagesCount}][user_id]"
                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
            <option value="">Sélectionner un utilisateur…</option>
            ${userOpts}
        </select>
        <select name="partages[${partagesCount}][droit]"
                class="w-40 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
            <option value="lecture">Lecture seule</option>
            <option value="modification">Modification</option>
            <option value="administration">Administration</option>
        </select>
        <button type="button" onclick="this.closest('.partage-row').remove()"
                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>`;
    container.appendChild(div);
    partagesCount++;
}

// ── MDP toggle + générateur ───────────────────────────────────────────────────
function toggleMdp() {
    const f = document.getElementById('field-mdp');
    f.type = f.type === 'password' ? 'text' : 'password';
}

function generateMdp() {
    const chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%&*';
    let pwd = '';
    for (let i = 0; i < 16; i++) pwd += chars[Math.floor(Math.random() * chars.length)];
    const f = document.getElementById('field-mdp');
    f.value = pwd;
    f.type  = 'text';
    setTimeout(() => f.type = 'password', 3000);
}

// ── Upload preview ────────────────────────────────────────────────────────────
function previewFiles(files) {
    const container = document.getElementById('file-preview');
    container.innerHTML = '';
    Array.from(files).forEach(f => {
        const size = f.size < 1048576 ? Math.round(f.size/1024)+' KB' : (f.size/1048576).toFixed(1)+' MB';
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2';
        div.innerHTML = `<svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
            <span class="font-medium flex-1 truncate">${f.name}</span>
            <span class="text-xs text-gray-400 flex-shrink-0">${size}</span>`;
        container.appendChild(div);
    });
}

// Drop zone
document.addEventListener('DOMContentLoaded', () => {
    const zone = document.getElementById('drop-zone');
    const input = document.getElementById('file-input');

    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-blue-400','bg-blue-50'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('border-blue-400','bg-blue-50'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('border-blue-400','bg-blue-50');
        input.files = e.dataTransfer.files;
        previewFiles(e.dataTransfer.files);
    });

    // Auto-ouvrir si old('categorie')
    const checkedCat = document.querySelector('.cat-radio:checked');
    if (checkedCat) switchCategorie(checkedCat.value);

    // Écouter les radios
    document.querySelectorAll('.cat-radio').forEach(r => {
        r.addEventListener('change', () => switchCategorie(r.value));
    });
});
</script>
@endsection
