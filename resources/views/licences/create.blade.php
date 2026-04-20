{{-- resources/views/licences/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouvelle licence')

@section('content')
<div class="p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('licences.index') }}" class="text-gray-500 hover:text-green-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Nouvelle licence</h1>
            <p class="text-sm text-gray-500">Fortinet · FAI · Certificat · Office 365</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('licences.store') }}" class="space-y-6">
        @csrf

        {{-- Type --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Type de licence</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach([
                    'Fortinet'  => ['color' => 'purple', 'icon' => '🛡'],
                    'FAI'       => ['color' => 'blue',   'icon' => '🌐'],
                    'Certificat'=> ['color' => 'orange', 'icon' => '🔐'],
                    'Office365' => ['color' => 'green',  'icon' => '📧'],
                ] as $type => $meta)
                <label class="cursor-pointer">
                    <input type="radio" name="type" value="{{ $type }}"
                           class="sr-only peer" id="type-{{ $type }}"
                           {{ old('type') === $type ? 'checked' : '' }}
                           onchange="switchType('{{ $type }}')">
                    <div class="border-2 border-gray-200 peer-checked:border-{{ $meta['color'] }}-500
                                peer-checked:bg-{{ $meta['color'] }}-50 rounded-xl p-4 text-center
                                transition-all hover:border-gray-300 cursor-pointer">
                        <div class="text-2xl mb-1">{{ $meta['icon'] }}</div>
                        <div class="text-sm font-semibold text-gray-700">{{ $type }}</div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('type')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Infos communes --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Informations générales</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site / Agence</label>
                    <select name="site_agence" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Sélectionner…</option>
                        @foreach($sites as $s)
                            <option value="{{ $s }}" @selected(old('site_agence') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut <span class="text-red-500">*</span></label>
                    <select name="statut" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        @foreach($statuts as $s)
                            <option value="{{ $s }}" @selected(old('statut') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Renouvellement prévu ?</label>
                    <div class="flex items-center gap-4 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="renouvellement_prevu" value="1" {{ old('renouvellement_prevu') == '1' ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Oui</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="renouvellement_prevu" value="0" {{ old('renouvellement_prevu', '0') == '0' ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Non</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dates communes --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Dates</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'activation</label>
                    <input type="date" name="date_activation" value="{{ old('date_activation') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                    <input type="date" name="date_expiration" value="{{ old('date_expiration') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de mise en service</label>
                    <input type="date" name="date_mise_en_service" value="{{ old('date_mise_en_service') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Échéance contrat</label>
                    <input type="date" name="echeance_contrat" value="{{ old('echeance_contrat') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
        </div>

        {{-- ── SECTION FORTINET ─────────────────────────────────────────────── --}}
        <div id="section-Fortinet" class="bg-white rounded-xl border border-purple-200 p-6 shadow-sm hidden">
            <h2 class="text-sm font-semibold text-purple-700 uppercase tracking-wide mb-4">🛡 Détails Fortinet</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modèle Fortinet</label>
                    <input type="text" name="modele" value="{{ old('modele') }}" placeholder="50G, 100F…"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de série</label>
                    <input type="text" name="numero_serie" value="{{ old('numero_serie') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de licence</label>
                    <select name="type_licence" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">—</option>
                        @foreach(['UTP Bundle', 'FortiCare', 'FortiGuard', 'Enterprise', 'UTM'] as $tl)
                            <option value="{{ $tl }}" @selected(old('type_licence') === $tl)>{{ $tl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix d'achat (FCFA)</label>
                    <input type="number" name="prix_achat" value="{{ old('prix_achat') }}" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
        </div>

        {{-- ── SECTION FAI ──────────────────────────────────────────────────── --}}
        <div id="section-FAI" class="bg-white rounded-xl border border-blue-200 p-6 shadow-sm hidden">
            <h2 class="text-sm font-semibold text-blue-700 uppercase tracking-wide mb-4">🌐 Détails FAI</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fournisseur</label>
                    <input type="text" name="fournisseur" value="{{ old('fournisseur') }}" placeholder="Orange, Sonatel, Expresso…"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro client / contrat</label>
                    <input type="text" name="numero_client" value="{{ old('numero_client') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de ligne</label>
                    <select name="type_ligne" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">—</option>
                        @foreach(['Fibre', 'ADSL', '4G Box', 'VSAT'] as $tl)
                            <option value="{{ $tl }}" @selected(old('type_ligne') === $tl)>{{ $tl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">IP publique</label>
                    <input type="text" name="ip_publique" value="{{ old('ip_publique') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Débit (MB)</label>
                    <input type="text" name="debit" value="{{ old('debit') }}" placeholder="10, 20, 100…"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant mensuel (FCFA)</label>
                    <input type="number" name="montant_mensuel" value="{{ old('montant_mensuel') }}" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- ── SECTION CERTIFICAT ───────────────────────────────────────────── --}}
        <div id="section-Certificat" class="bg-white rounded-xl border border-orange-200 p-6 shadow-sm hidden">
            <h2 class="text-sm font-semibold text-orange-700 uppercase tracking-wide mb-4">🔐 Détails Certificat</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Environnement</label>
                    <select name="environnement" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">—</option>
                        @foreach(['Production', 'Préproduction', 'Test', 'Développement'] as $env)
                            <option value="{{ $env }}" @selected(old('environnement') === $env)>{{ $env }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement (Serveur / IP)</label>
                    <input type="text" name="emplacement" value="{{ old('emplacement') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                    <input type="number" name="port" value="{{ old('port', 443) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durée (jours)</label>
                    <input type="number" name="duree_jours" value="{{ old('duree_jours', 365) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
        </div>

        {{-- ── SECTION OFFICE 365 ───────────────────────────────────────────── --}}
        <div id="section-Office365" class="bg-white rounded-xl border border-green-200 p-6 shadow-sm hidden">
            <h2 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-4">📧 Détails Office 365</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'utilisateur</label>
                    <input type="text" name="utilisateur" value="{{ old('utilisateur') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Département</label>
                    <input type="text" name="departement" value="{{ old('departement') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de licence</label>
                    <input type="text" name="type_licence" value="{{ old('type_licence') }}" placeholder="Business Basic, E3…"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Espace OneDrive utilisé</label>
                    <input type="text" name="espace_onedrive" value="{{ old('espace_onedrive') }}" placeholder="ex: 15 GB"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quota total</label>
                    <input type="text" name="quota_total" value="{{ old('quota_total') }}" placeholder="ex: 1 TB"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="flex items-center gap-3 md:col-span-2">
                    <input type="checkbox" name="teams" value="1" id="teams" {{ old('teams') ? 'checked' : '' }}
                           class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                    <label for="teams" class="text-sm font-medium text-gray-700">Microsoft Teams activé</label>
                </div>
            </div>
        </div>

        {{-- Contact technique --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Contact technique</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" name="contact_nom" value="{{ old('contact_nom') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="text" name="contact_tel" value="{{ old('contact_tel') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
        </div>

        {{-- Observation --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Observation</h2>
            <textarea name="observation" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="Informations complémentaires…">{{ old('observation') }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('licences.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                Annuler
            </a>
            <button type="submit"
                    class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                Enregistrer la licence
            </button>
        </div>
    </form>
</div>

<script>
const types = ['Fortinet', 'FAI', 'Certificat', 'Office365'];

function switchType(selected) {
    types.forEach(t => {
        const el = document.getElementById('section-' + t);
        if (el) el.classList.toggle('hidden', t !== selected);
    });
}

// Auto-open on page load if old value exists
document.addEventListener('DOMContentLoaded', function () {
    const checked = document.querySelector('input[name="type"]:checked');
    if (checked) switchType(checked.value);
});
</script>
@endsection
