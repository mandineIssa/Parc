{{-- resources/views/passwords/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Fiche — ' . $password->nom)
@section('content')
<div class="p-6 max-w-5xl mx-auto space-y-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('passwords.index') }}" class="text-gray-500 hover:text-red-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    {{ $password->nom }}
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full
                        @switch($password->categorie)
                            @case('Serveur') bg-blue-100 text-blue-700 @break
                            @case('Réseau') bg-purple-100 text-purple-700 @break
                            @case('Base de données') bg-green-100 text-green-700 @break
                            @case('Sécurité électronique') bg-orange-100 text-orange-700 @break
                            @case('Active Directory') bg-indigo-100 text-indigo-700 @break
                            @case('Modem/WiFi') bg-teal-100 text-teal-700 @break
                            @default bg-gray-100 text-gray-700
                        @endswitch">{{ $password->categorie }}</span>
                </h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $password->site ?? '—' }} · Créée par {{ $password->creator?->name }}</p>
            </div>
        </div>
        <a href="{{ route('passwords.edit', $password) }}"
           class="flex items-center gap-1.5 border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Modifier
        </a>
    </div>

    @if(session('success'))
    <div id="flash-toast" class="fixed bottom-6 right-6 z-50 bg-green-600 text-white px-5 py-3 rounded-xl shadow-xl text-sm font-medium flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    <script>setTimeout(()=>document.getElementById('flash-toast')?.remove(),3500);</script>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Colonne principale ────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Informations --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Informations</h2>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                    @foreach([
                        'Nom EXI'         => $password->nom_exi,
                        'Adresse IP'      => $password->adresse_ip,
                        'Nom VM'          => $password->nom_vm,
                        'Adresse IP VM'   => $password->adresse_ip_vm,
                        'Instance'        => $password->instance,
                        'Type équipement' => $password->type_equipement,
                        'Protocole'       => $password->protocole,
                        'Site'            => $password->site,
                    ] as $lbl => $val)
                    @if($val)
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">{{ $lbl }}</dt>
                        <dd class="text-xs font-mono bg-gray-50 border border-gray-200 rounded px-2 py-1 text-gray-800">{{ $val }}</dd>
                    </div>
                    @endif
                    @endforeach
                </dl>
            </div>

            {{-- ══ ACCÈS SÉCURISÉ (OTP) ══════════════════════════════════════════ --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-5">Accès sécurisé</h2>

                {{-- Compte --}}
                <div class="mb-5">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1.5">Compte</p>
                    <div class="flex items-center gap-2">
                        <code class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm font-mono font-semibold text-gray-800">{{ $password->compte }}</code>
                        <button onclick="copyText('{{ addslashes($password->compte) }}', this)"
                                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Copier">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Mot de passe --}}
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Mot de passe</p>

                    {{-- ÉTAPE 1 : Bouton envoyer code --}}
                    <div id="otp-step1">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="flex-1 bg-gray-100 border border-gray-200 rounded-lg px-3 py-2.5 text-gray-400 font-mono text-sm select-none tracking-widest">
                                ● ● ● ● ● ● ● ● ● ● ● ●
                            </div>
                            <button id="btn-send-otp" onclick="sendOtp()"
                                    class="flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Recevoir le code
                            </button>
                        </div>
                        <p class="text-xs text-gray-400">
                            Un code à 6 chiffres sera envoyé à <strong class="font-mono">{{ auth()->user()->email }}</strong>
                        </p>
                    </div>

                    {{-- ÉTAPE 2 : Saisir le code --}}
                    <div id="otp-step2" class="hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-blue-900">Code envoyé !</p>
                                    <p class="text-xs text-blue-700 mt-0.5">
                                        Vérifiez l'adresse <strong id="otp-email-display" class="font-mono">{{ auth()->user()->email }}</strong>
                                    </p>
                                    <p class="text-xs text-blue-600 mt-1 flex items-center gap-2">
                                        <span>Expire dans <strong id="otp-timer" class="text-blue-800">5:00</strong></span>
                                        <span>·</span>
                                        <button type="button" onclick="sendOtp()" class="underline hover:no-underline">Renvoyer</button>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Zone debug si mail KO --}}
                        <div id="otp-debug" class="hidden mb-3 bg-amber-50 border border-amber-300 rounded-xl p-3">
                            <p class="text-xs font-bold text-amber-800 mb-1">⚠ Email non reçu (mode DEBUG)</p>
                            <p class="text-xs text-amber-700 mb-2">
                                Vérifiez la config MAIL_* dans votre <code>.env</code>.<br>
                                Code de test :
                            </p>
                            <div class="flex items-center gap-2">
                                <code id="otp-debug-code"
                                      class="flex-1 text-center text-2xl font-mono font-black tracking-[0.4em] bg-white border-2 border-amber-300 rounded-lg px-4 py-2 text-amber-900"></code>
                                <button onclick="document.getElementById('otp-input').value=document.getElementById('otp-debug-code').textContent.trim()"
                                        class="text-xs bg-amber-600 hover:bg-amber-700 text-white px-3 py-2 rounded-lg font-medium transition">
                                    Utiliser
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="text" id="otp-input" maxlength="6"
                                   placeholder="0  0  0  0  0  0"
                                   inputmode="numeric"
                                   class="flex-1 border-2 border-gray-300 focus:border-blue-500 rounded-xl px-4 py-3
                                          text-xl font-mono font-bold tracking-[0.5em] text-center text-gray-800
                                          focus:outline-none transition">
                            <button onclick="verifyOtp()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl text-sm font-bold transition">
                                Valider
                            </button>
                            <button onclick="resetOtp()" title="Annuler"
                                    class="p-3 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <p id="otp-error" class="hidden mt-2 text-xs text-red-700 font-medium bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                            ⚠ Code invalide ou expiré. Demandez un nouveau code.
                        </p>
                    </div>

                    {{-- ÉTAPE 3 : Mot de passe révélé --}}
                    <div id="otp-step3" class="hidden">
                        <div class="flex items-center gap-2 mb-1.5">
                            <input type="text" id="mdp-revealed" readonly
                                   class="flex-1 bg-green-50 border-2 border-green-300 rounded-xl px-4 py-2.5 text-sm font-mono font-bold text-green-800 select-all">
                            <button id="btn-copy-mdp" onclick="copyMdp()"
                                    class="flex items-center gap-1.5 border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-2.5 rounded-xl text-sm font-medium transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Copier
                            </button>
                            <button onclick="hideMdp()" title="Masquer"
                                    class="p-2.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        <p class="text-xs text-amber-600">⏱ Masqué automatiquement dans <span id="hide-timer" class="font-bold">60</span>s</p>
                    </div>
                </div>
            </div>

            {{-- Champs libres --}}
            @if(!empty($password->champs_libres))
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Champs personnalisés</h2>
                <dl class="space-y-3">
                    @foreach($password->champs_libres as $cl)
                    @if(!empty($cl['libelle']))
                    <div class="flex items-center gap-3">
                        <dt class="text-xs text-gray-500 font-semibold w-36 flex-shrink-0 uppercase tracking-wide">{{ $cl['libelle'] }}</dt>
                        <dd class="flex-1 font-mono text-xs bg-gray-50 border border-gray-200 rounded px-3 py-1.5 text-gray-800">{{ $cl['contenu'] ?? '—' }}</dd>
                    </div>
                    @endif
                    @endforeach
                </dl>
            </div>
            @endif

            {{-- Description --}}
            @if($password->description)
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</h2>
                <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $password->description }}</p>
            </div>
            @endif

            {{-- Pièces jointes --}}
            @if($password->fichiers->count())
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">
                    Pièces jointes ({{ $password->fichiers->count() }})
                </h2>
                <div class="space-y-2">
                    @foreach($password->fichiers as $f)
                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 hover:bg-blue-50 hover:border-blue-200 transition">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        <span class="flex-1 text-sm font-medium text-gray-800 truncate">{{ $f->nom_original }}</span>
                        <span class="text-xs text-gray-400">{{ $f->taille_formatee }}</span>
                        <a href="{{ route('passwords.fichier.download', [$password, $f]) }}"
                           class="flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-semibold hover:underline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Télécharger
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ══ PARTAGES D'ACCÈS ══════════════════════════════════════════════ --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-700">Partages d'accès</h2>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $password->shares->count() }} utilisateur(s) ayant accès</p>
                    </div>
                    <button onclick="toggleSharePanel()"
                            class="flex items-center gap-1.5 bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-lg text-xs font-semibold transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Partager l'accès
                    </button>
                </div>

                {{-- Panneau ajout multiple --}}
                <div id="share-panel" class="hidden bg-purple-50 border-b border-purple-200 p-5">
                    <form method="POST" action="{{ route('passwords.share', $password) }}" id="share-form">
                        @csrf
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-semibold text-purple-800">Partager avec plusieurs utilisateurs</p>
                            <button type="button" onclick="addShareRow()"
                                    class="flex items-center gap-1 text-xs text-purple-700 border border-purple-300 hover:border-purple-500 hover:bg-purple-100 px-2.5 py-1.5 rounded-lg transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                + Ajouter une ligne
                            </button>
                        </div>

                        {{-- En-têtes colonnes --}}
                        <div class="grid grid-cols-12 gap-2 mb-2 px-1">
                            <div class="col-span-5 text-xs text-purple-600 font-semibold uppercase tracking-wide">Utilisateur</div>
                            <div class="col-span-3 text-xs text-purple-600 font-semibold uppercase tracking-wide">Rôle</div>
                            <div class="col-span-3 text-xs text-purple-600 font-semibold uppercase tracking-wide">Expiration</div>
                            <div class="col-span-1"></div>
                        </div>

                        <div id="share-rows" class="space-y-2 mb-4">
                            {{-- Ligne 0 initiale --}}
                            <div class="share-row grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-5">
                                    <select name="partages[0][user_id]" required
                                            class="w-full border border-purple-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                                        <option value="">Sélectionner…</option>
                                        @foreach($availableUsers as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-3">
                                    <select name="partages[0][droit]"
                                            class="w-full border border-purple-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                                        <option value="lecture">👁 Lecture</option>
                                        <option value="modification">✏️ Modification</option>
                                        <option value="administration">⚙️ Admin</option>
                                    </select>
                                </div>
                                <div class="col-span-3">
                                    <input type="date" name="partages[0][expiration]"
                                           class="w-full border border-purple-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                                </div>
                                <div class="col-span-1 flex justify-center">
                                    <button type="button" onclick="removeShareRow(this)"
                                            class="p-1.5 text-purple-300 hover:text-red-500 transition rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 pt-2 border-t border-purple-200">
                            <button type="button" onclick="toggleSharePanel()"
                                    class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50 transition">
                                Annuler
                            </button>
                            <button type="submit"
                                    class="flex items-center gap-2 px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-semibold transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Enregistrer les partages
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Tableau des partages existants --}}
                @if($password->shares->count())
                <div>
                    {{-- En-têtes --}}
                    <div class="grid grid-cols-12 gap-2 px-5 py-2.5 bg-gray-50 border-b border-gray-200
                                text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <div class="col-span-4">Utilisateur</div>
                        <div class="col-span-2">Rôle</div>
                        <div class="col-span-3">Expiration</div>
                        <div class="col-span-2">Ajouté le</div>
                        <div class="col-span-1 text-right">Action</div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach($password->shares as $share)
                        <div class="grid grid-cols-12 gap-2 px-5 py-3 items-center hover:bg-gray-50 transition">

                            {{-- Avatar + nom --}}
                            <div class="col-span-4 flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 text-xs font-bold text-purple-700">
                                    {{ strtoupper(substr($share->user?->name ?? '?', 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $share->user?->name ?? 'Supprimé' }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $share->user?->email }}</p>
                                </div>
                            </div>

                            {{-- Rôle modifiable inline --}}
                            <div class="col-span-2">
                                <form method="POST"
                                      action="{{ route('passwords.share.update', [$password, $share]) }}"
                                      id="rf-{{ $share->id }}">
                                    @csrf @method('PATCH')
                                    <select name="droit"
                                            onchange="document.getElementById('rf-{{ $share->id }}').submit()"
                                            class="w-full border rounded-lg px-2 py-1 text-xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-400 transition
                                                   {{ $share->droit==='administration' ? 'border-red-200 bg-red-50 text-red-700' :
                                                      ($share->droit==='modification'  ? 'border-amber-200 bg-amber-50 text-amber-700' :
                                                                                         'border-gray-200 bg-gray-50 text-gray-700') }}">
                                        <option value="lecture"        @selected($share->droit==='lecture')>👁 Lecture</option>
                                        <option value="modification"   @selected($share->droit==='modification')>✏️ Modif.</option>
                                        <option value="administration" @selected($share->droit==='administration')>⚙️ Admin</option>
                                    </select>
                                </form>
                            </div>

                            {{-- Expiration --}}
                            <div class="col-span-3">
                                @if($share->expiration)
                                    @php $exp = $share->expiration->isPast(); @endphp
                                    <span class="inline-flex items-center gap-1.5 text-xs {{ $exp ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $exp ? 'bg-red-500' : 'bg-green-500' }}"></span>
                                        {{ $share->expiration->format('d/m/Y') }}
                                        @if($exp) <span class="text-red-500">(expiré)</span> @endif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Permanent
                                    </span>
                                @endif
                            </div>

                            {{-- Date ajout --}}
                            <div class="col-span-2 text-xs text-gray-400">
                                {{ $share->created_at->format('d/m/Y') }}
                            </div>

                            {{-- Révoquer --}}
                            <div class="col-span-1 flex justify-end">
                                <form method="POST"
                                      action="{{ route('passwords.share.revoke', [$password, $share]) }}"
                                      onsubmit="return confirm('Révoquer l\'accès de {{ addslashes($share->user?->name ?? '') }} ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Révoquer l'accès">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="px-6 py-10 text-center">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-sm text-gray-400">Aucun partage actif</p>
                    <button onclick="toggleSharePanel()" class="mt-2 text-xs text-purple-600 hover:underline">Partager maintenant</button>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Colonne droite ────────────────────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Expiration --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Expiration</h2>
                @if($password->date_expiration)
                    @php $s = $password->statut_expiration; @endphp
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full {{ $s==='Expiré'?'bg-red-500':($s==='Bientôt'?'bg-amber-500':'bg-green-500') }}"></span>
                        <span class="text-sm font-bold {{ $s==='Expiré'?'text-red-600':($s==='Bientôt'?'text-amber-600':'text-green-700') }}">{{ $s }}</span>
                    </div>
                    <p class="text-sm font-mono font-semibold text-gray-800">{{ $password->date_expiration->format('d/m/Y') }}</p>
                    @if($password->duree_renouvellement)
                    <p class="text-xs text-gray-400 mt-1">Renouvellement : {{ $password->duree_renouvellement }} jours</p>
                    @endif
                @else
                    <p class="text-sm text-gray-400 italic">Pas de date d'expiration</p>
                @endif
            </div>

            {{-- Méta --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Informations</h2>
                <dl class="space-y-2 text-xs">
                    <div class="flex justify-between gap-2">
                        <dt class="font-semibold text-gray-600">Créé par</dt>
                        <dd class="text-gray-700 text-right">{{ $password->creator?->name }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="font-semibold text-gray-600">Créé le</dt>
                        <dd class="text-gray-500 font-mono">{{ $password->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if($password->updater)
                    <div class="flex justify-between gap-2">
                        <dt class="font-semibold text-gray-600">Modifié par</dt>
                        <dd class="text-gray-700 text-right">{{ $password->updater->name }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="font-semibold text-gray-600">Modifié le</dt>
                        <dd class="text-gray-500 font-mono">{{ $password->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- Journal d'accès --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Journal d'accès</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($password->logs->sortByDesc('created_at')->take(20) as $log)
            <div class="px-6 py-3 flex items-center gap-4 text-sm">
                <span class="w-28 text-xs text-gray-400 font-mono flex-shrink-0">{{ $log->created_at->format('d/m H:i') }}</span>
                <span class="font-medium text-gray-700 w-28 truncate flex-shrink-0">{{ $log->user?->name ?? '—' }}</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold flex-shrink-0
                    @switch($log->action)
                        @case('consultation')  bg-blue-50 text-blue-700 @break
                        @case('creation')      bg-green-50 text-green-700 @break
                        @case('modification')  bg-amber-50 text-amber-700 @break
                        @case('suppression')   bg-red-50 text-red-700 @break
                        @case('partage')       bg-purple-50 text-purple-700 @break
                    @endswitch">{{ ucfirst($log->action) }}</span>
                @if($log->details)<span class="text-xs text-gray-400 truncate">{{ $log->details }}</span>@endif
                <span class="ml-auto text-xs text-gray-300 font-mono flex-shrink-0">{{ $log->ip_address }}</span>
            </div>
            @empty
            <div class="px-6 py-6 text-sm text-gray-400 text-center">Aucune activité</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ══ SCRIPTS ══════════════════════════════════════════════════════════════════ --}}
<script>
const OTP_SEND   = '{{ route("passwords.otp.send",   $password) }}';
const OTP_VERIFY = '{{ route("passwords.otp.verify", $password) }}';
const CSRF       = '{{ csrf_token() }}';

let timerInterval = null;
let hideCountdown = null;

// ─── OTP ──────────────────────────────────────────────────────────────────────
async function sendOtp() {
    const btn = document.getElementById('btn-send-otp');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg> Envoi…`;

    try {
        const res  = await fetch(OTP_SEND, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();

        // Mettre à jour l'email affiché
        if (data.email) {
            document.getElementById('otp-email-display').textContent = data.email;
        }

        // Si mail KO et mode debug : afficher le code
        if (data.debug_code) {
            document.getElementById('otp-debug').classList.remove('hidden');
            document.getElementById('otp-debug-code').textContent = data.debug_code;
        }

        document.getElementById('otp-step1').classList.add('hidden');
        document.getElementById('otp-step2').classList.remove('hidden');
        startTimer(300);
        setTimeout(() => document.getElementById('otp-input').focus(), 100);

    } catch(e) {
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Recevoir le code`;
        alert('Erreur réseau. Vérifiez votre connexion.');
    }
}

function startTimer(s) {
    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        s--;
        const el = document.getElementById('otp-timer');
        if (el) el.textContent = `${Math.floor(s/60)}:${String(s%60).padStart(2,'0')}`;
        if (s <= 0) { clearInterval(timerInterval); resetOtp(); }
    }, 1000);
}

async function verifyOtp() {
    const code  = document.getElementById('otp-input').value.trim();
    const errEl = document.getElementById('otp-error');
    errEl.classList.add('hidden');
    if (!code) return;

    try {
        const res  = await fetch(OTP_VERIFY, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ otp: code })
        });
        const data = await res.json();

        if (!res.ok) {
            errEl.classList.remove('hidden');
            document.getElementById('otp-input').value = '';
            document.getElementById('otp-input').focus();
            return;
        }

        clearInterval(timerInterval);
        document.getElementById('otp-step2').classList.add('hidden');
        document.getElementById('otp-step3').classList.remove('hidden');
        document.getElementById('mdp-revealed').value = data.mot_de_passe;

        let sec = 60;
        clearInterval(hideCountdown);
        hideCountdown = setInterval(() => {
            sec--;
            const el = document.getElementById('hide-timer');
            if (el) el.textContent = sec;
            if (sec <= 0) { clearInterval(hideCountdown); hideMdp(); }
        }, 1000);

    } catch(e) {
        errEl.classList.remove('hidden');
    }
}

function resetOtp() {
    clearInterval(timerInterval);
    document.getElementById('otp-step1').classList.remove('hidden');
    document.getElementById('otp-step2').classList.add('hidden');
    document.getElementById('otp-step3').classList.add('hidden');
    document.getElementById('otp-input').value = '';
    document.getElementById('otp-error').classList.add('hidden');
    document.getElementById('otp-debug').classList.add('hidden');
    const btn = document.getElementById('btn-send-otp');
    btn.disabled = false;
    btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Recevoir le code`;
}

function hideMdp() {
    clearInterval(hideCountdown);
    document.getElementById('mdp-revealed').value = '';
    document.getElementById('otp-step3').classList.add('hidden');
    document.getElementById('otp-step1').classList.remove('hidden');
}

async function copyMdp() {
    const val = document.getElementById('mdp-revealed').value;
    await navigator.clipboard.writeText(val);
    const btn = document.getElementById('btn-copy-mdp');
    const orig = btn.innerHTML;
    btn.innerHTML = '✓ Copié !';
    btn.className = btn.className.replace('border-gray-300 text-gray-700', 'border-green-400 text-green-700 bg-green-50');
    setTimeout(() => {
        btn.innerHTML = orig;
        btn.className = btn.className.replace('border-green-400 text-green-700 bg-green-50', 'border-gray-300 text-gray-700');
    }, 2000);
}

async function copyText(text, btn) {
    await navigator.clipboard.writeText(text);
    const orig = btn.innerHTML;
    btn.innerHTML = '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
    setTimeout(() => btn.innerHTML = orig, 2000);
}

// ─── Partages ─────────────────────────────────────────────────────────────────
let shareRowCount = 1;
const AVAILABLE_USERS = @json($availableUsers->map(fn($u)=>['id'=>$u->id,'name'=>$u->name,'email'=>$u->email]));

function toggleSharePanel() {
    const p = document.getElementById('share-panel');
    p.classList.toggle('hidden');
    if (!p.classList.contains('hidden')) {
        p.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function addShareRow() {
    const container = document.getElementById('share-rows');
    const div = document.createElement('div');
    div.className = 'share-row grid grid-cols-12 gap-2 items-center';

    const userOpts = AVAILABLE_USERS
        .map(u => `<option value="${u.id}">${u.name} — ${u.email}</option>`)
        .join('');

    div.innerHTML = `
        <div class="col-span-5">
            <select name="partages[${shareRowCount}][user_id]" required
                    class="w-full border border-purple-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                <option value="">Sélectionner…</option>
                ${userOpts}
            </select>
        </div>
        <div class="col-span-3">
            <select name="partages[${shareRowCount}][droit]"
                    class="w-full border border-purple-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
                <option value="lecture">👁 Lecture</option>
                <option value="modification">✏️ Modification</option>
                <option value="administration">⚙️ Admin</option>
            </select>
        </div>
        <div class="col-span-3">
            <input type="date" name="partages[${shareRowCount}][expiration]"
                   class="w-full border border-purple-200 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
        </div>
        <div class="col-span-1 flex justify-center">
            <button type="button" onclick="removeShareRow(this)"
                    class="p-1.5 text-purple-300 hover:text-red-500 transition rounded">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>`;
    container.appendChild(div);
    shareRowCount++;
    div.querySelector('select').focus();
}

function removeShareRow(btn) {
    const rows = document.querySelectorAll('.share-row');
    if (rows.length <= 1) {
        btn.closest('.share-row').querySelector('select').value = '';
        return;
    }
    btn.closest('.share-row').remove();
}

// Événements clavier
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('otp-input')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); verifyOtp(); }
    });
    // Accepter uniquement les chiffres
    document.getElementById('otp-input')?.addEventListener('input', e => {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
});
</script>
@endsection
