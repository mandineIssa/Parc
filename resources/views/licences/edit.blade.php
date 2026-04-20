{{-- resources/views/licences/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier — ' . $licence->nom)

@section('content')
<div class="p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('licences.index') }}" class="text-gray-500 hover:text-green-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Modifier — {{ $licence->nom }}</h1>
            <p class="text-sm text-gray-500">{{ $licence->type }} · {{ $licence->site_agence ?? '—' }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('licences.update', $licence) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Type (readonly en édition) --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Type de licence</h2>
            <input type="hidden" name="type" value="{{ $licence->type }}">
            <div class="flex items-center gap-3">
                <span class="text-2xl">
                    @switch($licence->type)
                        @case('Fortinet')   🛡 @break
                        @case('FAI')        🌐 @break
                        @case('Certificat') 🔐 @break
                        @case('Office365')  📧 @break
                    @endswitch
                </span>
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    @switch($licence->type)
                        @case('Fortinet')   bg-purple-100 text-purple-700 @break
                        @case('FAI')        bg-blue-100 text-blue-700 @break
                        @case('Certificat') bg-orange-100 text-orange-700 @break
                        @case('Office365')  bg-green-100 text-green-700 @break
                    @endswitch
                ">{{ $licence->type }}</span>
                <span class="text-xs text-gray-400">(non modifiable)</span>
            </div>
        </div>

        {{-- Infos générales --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Informations générales</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom', $licence->nom) }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site / Agence</label>
                    <select name="site_agence" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Sélectionner…</option>
                        @foreach($sites as $s)
                            <option value="{{ $s }}" @selected(old('site_agence', $licence->site_agence) === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut <span class="text-red-500">*</span></label>
                    <select name="statut" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        @foreach($statuts as $s)
                            <option value="{{ $s }}" @selected(old('statut', $licence->statut) === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Renouvellement prévu ?</label>
                    <div class="flex items-center gap-4 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="renouvellement_prevu" value="1"
                                   {{ old('renouvellement_prevu', $licence->renouvellement_prevu ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Oui</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="renouvellement_prevu" value="0"
                                   {{ old('renouvellement_prevu', $licence->renouvellement_prevu ? '1' : '0') == '0' ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Non</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dates --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Dates</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach([
                    'date_activation'      => "Date d'activation",
                    'date_expiration'      => "Date d'expiration",
                    'date_mise_en_service' => 'Date de mise en service',
                    'echeance_contrat'     => 'Échéance contrat',
                ] as $field => $label)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                    <input type="date" name="{{ $field }}"
                           value="{{ old($field, $licence->$field?->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                @endforeach
            </div>
        </div>

        {{-- Champs spécifiques au type --}}
        @if($licence->type === 'Fortinet')
        <div class="bg-white rounded-xl border border-purple-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-purple-700 uppercase tracking-wide mb-4">🛡 Détails Fortinet</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modèle</label>
                    <input type="text" name="modele" value="{{ old('modele', $licence->modele) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de série</label>
                    <input type="text" name="numero_serie" value="{{ old('numero_serie', $licence->numero_serie) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de licence</label>
                    <select name="type_licence" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        @foreach(['UTP Bundle','FortiCare','FortiGuard','Enterprise','UTM'] as $tl)
                            <option value="{{ $tl }}" @selected(old('type_licence', $licence->type_licence) === $tl)>{{ $tl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix d'achat (FCFA)</label>
                    <input type="number" name="prix_achat" value="{{ old('prix_achat', $licence->prix_achat) }}" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
        </div>

        @elseif($licence->type === 'FAI')
        <div class="bg-white rounded-xl border border-blue-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-blue-700 uppercase tracking-wide mb-4">🌐 Détails FAI</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fournisseur</label>
                    <input type="text" name="fournisseur" value="{{ old('fournisseur', $licence->fournisseur) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro client / contrat</label>
                    <input type="text" name="numero_client" value="{{ old('numero_client', $licence->numero_client) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de ligne</label>
                    <select name="type_ligne" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(['Fibre','ADSL','4G Box','VSAT'] as $tl)
                            <option value="{{ $tl }}" @selected(old('type_ligne', $licence->type_ligne) === $tl)>{{ $tl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">IP publique</label>
                    <input type="text" name="ip_publique" value="{{ old('ip_publique', $licence->ip_publique) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Débit (MB)</label>
                    <input type="text" name="debit" value="{{ old('debit', $licence->debit) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant mensuel (FCFA)</label>
                    <input type="number" name="montant_mensuel" value="{{ old('montant_mensuel', $licence->montant_mensuel) }}" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        @elseif($licence->type === 'Certificat')
        <div class="bg-white rounded-xl border border-orange-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-orange-700 uppercase tracking-wide mb-4">🔐 Détails Certificat</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Environnement</label>
                    <select name="environnement" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        @foreach(['Production','Préproduction','Test','Développement'] as $env)
                            <option value="{{ $env }}" @selected(old('environnement', $licence->environnement) === $env)>{{ $env }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement</label>
                    <input type="text" name="emplacement" value="{{ old('emplacement', $licence->emplacement) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                    <input type="number" name="port" value="{{ old('port', $licence->port) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durée (jours)</label>
                    <input type="number" name="duree_jours" value="{{ old('duree_jours', $licence->duree_jours) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
        </div>

        @elseif($licence->type === 'Office365')
        <div class="bg-white rounded-xl border border-green-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-4">📧 Détails Office 365</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Utilisateur</label>
                    <input type="text" name="utilisateur" value="{{ old('utilisateur', $licence->utilisateur) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Département</label>
                    <input type="text" name="departement" value="{{ old('departement', $licence->departement) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $licence->email) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de licence</label>
                    <input type="text" name="type_licence" value="{{ old('type_licence', $licence->type_licence) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Espace OneDrive</label>
                    <input type="text" name="espace_onedrive" value="{{ old('espace_onedrive', $licence->espace_onedrive) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quota total</label>
                    <input type="text" name="quota_total" value="{{ old('quota_total', $licence->quota_total) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="flex items-center gap-3 md:col-span-2">
                    <input type="checkbox" name="teams" value="1" id="teams" {{ old('teams', $licence->teams) ? 'checked' : '' }}
                           class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                    <label for="teams" class="text-sm font-medium text-gray-700">Microsoft Teams activé</label>
                </div>
            </div>
        </div>
        @endif

        {{-- Contact --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Contact technique</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" name="contact_nom" value="{{ old('contact_nom', $licence->contact_nom) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $licence->contact_email) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="text" name="contact_tel" value="{{ old('contact_tel', $licence->contact_tel) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
        </div>

        {{-- Observation --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Observation</h2>
            <textarea name="observation" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('observation', $licence->observation) }}</textarea>
        </div>

        {{-- Danger zone --}}
        <div class="bg-red-50 rounded-xl border border-red-200 p-5">
            <h2 class="text-sm font-semibold text-red-700 mb-2">Zone dangereuse</h2>
            <form method="POST" action="{{ route('licences.destroy', $licence) }}"
                  onsubmit="return confirm('Supprimer définitivement cette licence ?')">
                @csrf @method('DELETE')
                <button type="submit" class="border border-red-400 text-red-700 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-medium transition">
                    Supprimer cette licence
                </button>
            </form>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('licences.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                Annuler
            </a>
            <button type="submit"
                    class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
