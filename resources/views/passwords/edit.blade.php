{{-- resources/views/passwords/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Modifier — ' . $password->nom)
@section('content')
<div class="p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('passwords.show', $password) }}" class="text-gray-500 hover:text-red-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Modifier — {{ $password->nom }}</h1>
            <p class="text-sm text-gray-500">{{ $password->categorie }} · {{ $password->site ?? '—' }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <p class="text-sm font-semibold text-red-700 mb-1">Veuillez corriger les erreurs :</p>
        <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('passwords.update', $password) }}"
          enctype="multipart/form-data" id="pwd-form" class="space-y-5" novalidate>
        @csrf @method('PUT')

        @php $currentCat = old('categorie', $password->categorie); @endphp

        {{-- ── Catégorie (affichage seuleument, non modifiable) ────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-6 h-6 rounded-full bg-red-600 text-white text-xs font-bold flex items-center justify-center">1</span>
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Catégorie</h2>
            </div>
            
            {{-- Champ caché pour garder la catégorie --}}
            <input type="hidden" name="categorie" value="{{ $currentCat }}">
            
            {{-- Affichage de la catégorie actuelle (non cliquable) --}}
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                @php
                $cats = [
                    'Serveur'=>['icon'=>'🖥️','sub'=>'Physique / VM','color'=>'blue'],
                    'Réseau'=>['icon'=>'🌐','sub'=>'Switch / Routeur / Firewall','color'=>'purple'],
                    'Base de données'=>['icon'=>'🗄️','sub'=>'Oracle / MySQL / MSSQL','color'=>'green'],
                    'Sécurité électronique'=>['icon'=>'📷','sub'=>'Vidéo / Contrôle accès','color'=>'orange'],
                    'Active Directory'=>['icon'=>'👤','sub'=>'Comptes AD / GPO','color'=>'indigo'],
                    'Modem/WiFi'=>['icon'=>'📡','sub'=>'AP / Box 4G / ADSL','color'=>'teal'],
                ];
                @endphp
                
                @if(isset($cats[$currentCat]))
                    <div class="flex items-center gap-4">
                        <div class="text-3xl">{{ $cats[$currentCat]['icon'] }}</div>
                        <div>
                            <div class="text-lg font-semibold text-gray-800">{{ $currentCat }}</div>
                            <div class="text-sm text-gray-500">{{ $cats[$currentCat]['sub'] }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-gray-700">{{ $currentCat }}</div>
                @endif
                
                <p class="text-xs text-gray-400 mt-3 italic">La catégorie ne peut pas être modifiée après création.</p>
            </div>
        </div>

        {{-- ── Section principale ───────────────────────────────────────────── --}}
        <div id="main-form" class="space-y-5">
            <div class="flex items-center gap-2 px-1">
                <span class="w-6 h-6 rounded-full bg-gray-800 text-white text-xs font-bold flex items-center justify-center" id="step-badge">2</span>
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide" id="section-title">Informations</h2>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm" id="block-principal">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1" id="label-nom">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" id="field-nom"
                               value="{{ old('nom', $password->nom) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                        @error('nom')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div id="row-nom-exi" class="{{ in_array($currentCat, ['Serveur', 'Base de données']) ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom EXI</label>
                        <input type="text" name="nom_exi" value="{{ old('nom_exi', $password->nom_exi) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>

                    <div id="row-ip">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse IP</label>
                        <input type="text" name="adresse_ip" value="{{ old('adresse_ip', $password->adresse_ip) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>

                    <div id="row-nom-vm" class="{{ in_array($currentCat, ['Serveur', 'Base de données']) ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom VM</label>
                        <input type="text" name="nom_vm" value="{{ old('nom_vm', $password->nom_vm) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>

                    <div id="row-ip-vm" class="{{ in_array($currentCat, ['Serveur', 'Base de données']) ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse IP VM</label>
                        <input type="text" name="adresse_ip_vm" value="{{ old('adresse_ip_vm', $password->adresse_ip_vm) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>

                    <div id="row-instance" class="{{ $currentCat == 'Base de données' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instance / Service</label>
                        <input type="text" name="instance" value="{{ old('instance', $password->instance) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>

                    <div id="row-type-equip" class="{{ in_array($currentCat, ['Réseau', 'Sécurité électronique', 'Modem/WiFi']) ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1" id="label-type-equip">Type</label>
                        <select name="type_equipement" id="select-type-equip"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <option value="">Sélectionner…</option>
                        </select>
                    </div>

                    <div id="row-extra" class="{{ $currentCat == 'Active Directory' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1" id="label-extra">Unité d'organisation (OU)</label>
                        <input type="text" name="nom_exi" id="field-extra" value="{{ old('nom_exi', $password->nom_exi) }}"
                               placeholder="OU=Admins,DC=COFINA,DC=LOCAL"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>

                    <div id="row-extra-wifi" class="{{ $currentCat == 'Modem/WiFi' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">SSID (réseau WiFi)</label>
                        <input type="text" name="ssid" id="field-ssid" value="{{ old('ssid', $password->ssid ?? '') }}"
                               placeholder="Nom du réseau WiFi diffusé"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>

                    <div id="row-protocole" class="{{ in_array($currentCat, ['Serveur', 'Réseau', 'Base de données', 'Sécurité électronique', 'Modem/WiFi']) ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Protocole d'accès</label>
                        <select name="protocole" id="select-protocole"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <option value="">—</option>
                        </select>
                    </div>

                    <div id="row-site">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Site</label>
                        <select name="site" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <option value="">Sélectionner…</option>
                            @foreach($sites as $s)<option value="{{ $s }}" @selected(old('site',$password->site)===$s)>{{ $s }}</option>@endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Accès --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Accès</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div id="row-compte-bdd" class="{{ $currentCat == 'Base de données' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Compte (sélecteur) <span class="text-red-500">*</span></label>
                        <select id="select-compte-bdd"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                onchange="if(this.value!=='Autre')document.getElementById('field-compte').value=this.value">
                            <option value="">Sélectionner…</option>
                            @foreach(['SYS as SYSDBA','SYSTEM','DVACCTMGR','Utilisateur de gestion','sa (SQL Server)','root','Autre'] as $c)
                                <option value="{{ $c }}" @selected(old('compte',$password->compte)===$c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="row-compte-libre" class="{{ $currentCat != 'Base de données' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1" id="label-compte">Compte <span class="text-red-500">*</span></label>
                        <input type="text" name="compte" id="field-compte"
                               value="{{ old('compte', $password->compte) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                        @error('compte')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nouveau mot de passe
                            <span class="text-xs text-gray-400 font-normal">(laisser vide = conserver)</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="mot_de_passe" id="field-mdp" placeholder="••••••••"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-20 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <div class="absolute inset-y-0 right-0 flex items-center gap-0.5 pr-1">
                                <button type="button" onclick="toggleMdp()" class="p-1.5 text-gray-400 hover:text-gray-700 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button type="button" onclick="generateMdp()" class="p-1.5 text-gray-400 hover:text-green-600 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Expiration --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Expiration & Renouvellement</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                        <input type="date" name="date_expiration"
                               value="{{ old('date_expiration', $password->date_expiration?->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Renouvellement (jours)</label>
                        <input type="number" name="duree_renouvellement" min="1"
                               value="{{ old('duree_renouvellement', $password->duree_renouvellement) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>
                </div>
            </div>

            {{-- Champs libres --}}
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
                    @forelse($password->champs_libres ?? [] as $i => $cl)
                    <div class="champ-libre-row flex gap-2 items-start">
                        <div class="flex-1 grid grid-cols-2 gap-2">
                            <input type="text" name="champs_libres[{{ $i }}][libelle]"
                                   value="{{ $cl['libelle'] ?? '' }}" placeholder="Libellé"
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
                    <p class="text-xs text-gray-400 italic" id="no-champs-msg">Aucun champ personnalisé.</p>
                    @endforelse
                </div>
            </div>

            {{-- Pièces jointes existantes + nouvelles --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Pièces jointes</h3>
                @if($password->fichiers->count())
                <div class="mb-4 space-y-2">
                    @foreach($password->fichiers as $f)
                    <div class="flex items-center gap-3 text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        <span class="flex-1 font-medium truncate">{{ $f->nom_original }}</span>
                        <span class="text-xs text-gray-400">{{ $f->taille_formatee }}</span>
                        <a href="{{ route('passwords.fichier.download', [$password, $f]) }}"
                           class="text-blue-600 hover:underline text-xs">Télécharger</a>
                        <form method="POST" action="{{ route('passwords.fichier.delete', [$password, $f]) }}"
                              onsubmit="return confirm('Supprimer ce fichier ?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 text-xs">Supprimer</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif
                <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300
                              rounded-xl cursor-pointer bg-gray-50 hover:bg-blue-50 hover:border-blue-400 transition" id="drop-zone">
                    <svg class="w-7 h-7 text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="text-sm text-gray-500">Ajouter des fichiers supplémentaires</p>
                    <input type="file" name="fichiers[]" id="file-input" multiple class="hidden" onchange="previewFiles(this.files)">
                </label>
                <div id="file-preview" class="mt-3 space-y-2"></div>
            </div>

            {{-- Description --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Description / Notes</h3>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">{{ old('description', $password->description) }}</textarea>
            </div>

            {{-- Zone dangereuse --}}
            <div class="bg-red-50 rounded-xl border border-red-200 p-5">
                <h3 class="text-sm font-semibold text-red-700 mb-2">Zone dangereuse</h3>
                <p class="text-xs text-red-500 mb-3">La suppression est définitive et journalisée.</p>
                <form method="POST" action="{{ route('passwords.destroy', $password) }}"
                      onsubmit="return confirm('Supprimer définitivement cette fiche ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="border border-red-400 text-red-700 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-medium transition">
                        Supprimer cette fiche
                    </button>
                </form>
            </div>

            <div class="flex justify-end gap-3 pb-4">
                <a href="{{ route('passwords.show', $password) }}"
                   class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Annuler</a>
                <button type="submit"
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>

<script>
const CURRENT_CAT = '{{ $currentCat }}';

const CAT_CONFIG = {
    'Serveur': {
        color:'blue',
        label:'🖥️ Informations Serveur',
        protocoles:['SSH','RDP','HTTPS','HTTP','Console','SFTP']
    },
    'Réseau': {
        color:'purple',
        label:'🌐 Équipement Réseau',
        typeEquipLabel:"Type d'équipement",
        typeEquipOptions:['Firewall Fortinet','Switch Cisco','Switch HP','Routeur Cisco','Routeur MikroTik','AP WiFi','Autre'],
        protocoles:['SSH','HTTPS','HTTP','Telnet','Console','SNMP']
    },
    'Base de données': {
        color:'green',
        label:'🗄️ Base de Données',
        protocoles:['SSH','RDP','JDBC','ODBC','SQLPlus','SSMS']
    },
    'Sécurité électronique': {
        color:'orange',
        label:"📷 Sécurité Électronique",
        typeEquipLabel:'Type de système',
        typeEquipOptions:["Vidéosurveillance (NVR/DVR)","Contrôle d'accès","Alarme intrusion","Interphone / Visiophone","Autre"],
        protocoles:['HTTP','HTTPS','RTSP','Logiciel propriétaire']
    },
    'Active Directory': {
        color:'indigo',
        label:'👤 Active Directory',
        protocoles:[]
    },
    'Modem/WiFi': {
        color:'teal',
        label:'📡 Modem / WiFi',
        typeEquipLabel:'Type',
        typeEquipOptions:["Point d'accès WiFi","Modem ADSL","Box 4G","Routeur WiFi","VSAT"],
        protocoles:['HTTP','HTTPS','Telnet','WPA2','WPA3']
    }
};

const CURRENT_VALUES = {
    protocole:    '{{ old('protocole', $password->protocole) }}',
    type_equip:   '{{ old('type_equipement', $password->type_equipement) }}',
};

let champsCount = {{ count($password->champs_libres ?? []) }};

function initFormForCategory(cat) {
    const cfg = CAT_CONFIG[cat];
    if (!cfg) return;

    document.getElementById('section-title').textContent = cfg.label;
    document.getElementById('step-badge').className = `w-6 h-6 rounded-full bg-${cfg.color}-600 text-white text-xs font-bold flex items-center justify-center`;

    // Initialiser les protocoles
    const selProto = document.getElementById('select-protocole');
    if (selProto && cfg.protocoles && cfg.protocoles.length > 0) {
        selProto.innerHTML = '<option value="">—</option>';
        cfg.protocoles.forEach(p => {
            const o = new Option(p,p);
            if (CURRENT_VALUES.protocole===p) o.selected=true;
            selProto.add(o);
        });
    }

    // Initialiser les types d'équipement si nécessaire
    if (cfg.typeEquipOptions) {
        const selT = document.getElementById('select-type-equip');
        if (selT) {
            selT.innerHTML = '<option value="">Sélectionner…</option>';
            cfg.typeEquipOptions.forEach(t => {
                const o = new Option(t,t);
                if (CURRENT_VALUES.type_equip===t) o.selected=true;
                selT.add(o);
            });
        }
    }
}

function addChampLibre() {
    const noMsg = document.getElementById('no-champs-msg');
    if (noMsg) noMsg.remove();
    const c = document.getElementById('champs-libres-container');
    const d = document.createElement('div');
    d.className = 'champ-libre-row flex gap-2 items-start';
    d.innerHTML = `<div class="flex-1 grid grid-cols-2 gap-2"><input type="text" name="champs_libres[${champsCount}][libelle]" placeholder="Libellé" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"><input type="text" name="champs_libres[${champsCount}][contenu]" placeholder="Contenu / valeur" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"></div><button type="button" onclick="this.closest('.champ-libre-row').remove()" class="mt-1 p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition flex-shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>`;
    c.appendChild(d); champsCount++;
    d.querySelector('input').focus();
}

function toggleMdp() { const f=document.getElementById('field-mdp'); f.type=f.type==='password'?'text':'password'; }
function generateMdp() {
    const chars='ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%&*';
    let pwd=''; for(let i=0;i<16;i++) pwd+=chars[Math.floor(Math.random()*chars.length)];
    const f=document.getElementById('field-mdp'); f.value=pwd; f.type='text';
    setTimeout(()=>f.type='password',3000);
}

function previewFiles(files) {
    const c=document.getElementById('file-preview'); c.innerHTML='';
    Array.from(files).forEach(f=>{
        const s=f.size<1048576?Math.round(f.size/1024)+' KB':(f.size/1048576).toFixed(1)+' MB';
        const d=document.createElement('div');
        d.className='flex items-center gap-2 text-sm text-gray-700 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2';
        d.innerHTML=`<svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg><span class="font-medium flex-1 truncate">${f.name}</span><span class="text-xs text-gray-400 flex-shrink-0">${s}</span>`;
        c.appendChild(d);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const zone=document.getElementById('drop-zone'), input=document.getElementById('file-input');
    if (zone) {
        zone.addEventListener('dragover',e=>{e.preventDefault();zone.classList.add('border-blue-400','bg-blue-50');});
        zone.addEventListener('dragleave',()=>zone.classList.remove('border-blue-400','bg-blue-50'));
        zone.addEventListener('drop',e=>{e.preventDefault();zone.classList.remove('border-blue-400','bg-blue-50');input.files=e.dataTransfer.files;previewFiles(e.dataTransfer.files);});
    }
    
    // Initialiser le formulaire avec la catégorie actuelle
    initFormForCategory(CURRENT_CAT);
});
</script>
@endsection