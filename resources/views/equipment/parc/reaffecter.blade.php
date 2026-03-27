@extends('layouts.app')

@section('title', 'Réaffecter un Équipement')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">

    {{-- En-tête --}}
    <div class="mb-8">
        <nav class="flex items-center text-sm text-gray-500 mb-4">
            <a href="{{ route('parc.index') }}" class="hover:text-gray-700">Parc</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('equipment.show', $equipment) }}" class="hover:text-gray-700">{{ $equipment->nom }}</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-800 font-medium">Réaffectation</span>
        </nav>

        <div class="flex items-center gap-4">
            <div class="h-12 w-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Réaffectation d'équipement</h1>
                <p class="text-gray-500 mt-0.5">Transférer l'équipement à un nouvel utilisateur</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Colonne gauche : récapitulatif équipement --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Carte équipement --}}
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="bg-indigo-600 px-5 py-4">
                    <h2 class="text-white font-semibold text-sm uppercase tracking-wide">Équipement concerné</h2>
                </div>
                <div class="p-5 space-y-3">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Nom</p>
                        <p class="font-semibold text-gray-900">{{ $equipment->nom }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">N° Série</p>
                        <p class="font-mono text-sm text-gray-700">{{ $equipment->numero_serie }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Type</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $equipment->type == 'Réseau' ? 'bg-blue-100 text-blue-800' :
                               ($equipment->type == 'Informatique' ? 'bg-green-100 text-green-800' :
                               'bg-purple-100 text-purple-800') }}">
                            {{ $equipment->type }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Marque / Modèle</p>
                        <p class="text-sm text-gray-700">{{ $equipment->marque }} {{ $equipment->modele }}</p>
                    </div>
                    @if($equipment->agence)
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Agence</p>
                        <p class="text-sm text-gray-700">{{ $equipment->agence->nom }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Carte ancienne affectation --}}
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="bg-gray-500 px-5 py-4">
                    <h2 class="text-white font-semibold text-sm uppercase tracking-wide">Affectation actuelle</h2>
                </div>
                <div class="p-5">
                    @if($equipment->parc && ($equipment->parc->utilisateur_nom || $equipment->parc->utilisateur_prenom))
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                            <span class="text-sm font-bold text-gray-600">
                                {{ strtoupper(substr($equipment->parc->utilisateur_nom ?? 'N', 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">
                                {{ trim(($equipment->parc->utilisateur_nom ?? '') . ' ' . ($equipment->parc->utilisateur_prenom ?? '')) }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $equipment->parc->departement ?? 'Département non renseigné' }}</p>
                        </div>
                    </div>
                    @if($equipment->parc->localisation)
                    <div class="flex items-center text-sm text-gray-500 gap-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $equipment->parc->localisation }}
                    </div>
                    @endif
                    @else
                    <div class="flex items-center gap-2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        <span class="italic text-sm">Non affecté actuellement</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Lien historique --}}
            <a href="{{ route('parc.reaffectations.index') }}"
               class="flex items-center gap-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium bg-indigo-50 hover:bg-indigo-100 px-4 py-3 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Voir tout l'historique des réaffectations
            </a>
        </div>

        {{-- Colonne droite : formulaire --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800">Nouvelle affectation</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Renseignez les informations du nouvel utilisateur</p>
                </div>

                <form action="{{ route('parc.reaffecter.store', $equipment) }}" method="POST" class="p-8 space-y-6">
                    @csrf

                    {{-- Erreurs --}}
                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Nouvel utilisateur --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                            <span class="h-5 w-5 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                            Nouvel utilisateur
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Nom <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nouveau_utilisateur_nom"
                                       value="{{ old('nouveau_utilisateur_nom') }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                       placeholder="Diallo" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Prénom</label>
                                <input type="text" name="nouveau_utilisateur_prenom"
                                       value="{{ old('nouveau_utilisateur_prenom') }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                       placeholder="Mamadou">
                            </div>
                        </div>
                    </div>

                    {{-- Département & Localisation --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                            <span class="h-5 w-5 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                            Département & Localisation
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Département</label>
                                <input type="text" name="nouveau_departement"
                                       value="{{ old('nouveau_departement') }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                       placeholder="Informatique / DSI / RH...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Localisation</label>
                                <input type="text" name="nouvelle_localisation"
                                       value="{{ old('nouvelle_localisation') }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                       placeholder="Bureau 203, Bâtiment A...">
                            </div>
                        </div>
                    </div>

                    {{-- Date & Motif --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                            <span class="h-5 w-5 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                            Date & Motif
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Date de réaffectation <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="date_reaffectation"
                                       value="{{ old('date_reaffectation', now()->format('Y-m-d')) }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                       required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Motif de la réaffectation</label>
                                <textarea name="motif" rows="3"
                                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition resize-none"
                                          placeholder="Ex : Mutation de l'ancien utilisateur, nouvel arrivant, remplacement de poste...">{{ old('motif') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Récapitulatif visuel avant/après --}}
                    <div class="bg-indigo-50 rounded-xl p-5 border border-indigo-100">
                        <h3 class="text-sm font-semibold text-indigo-800 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Récapitulatif du transfert
                        </h3>
                        <div class="flex items-center gap-4">
                            {{-- Ancien --}}
                            <div class="flex-1 bg-white rounded-lg p-3 border border-indigo-100">
                                <p class="text-xs text-gray-400 mb-1">Affectation actuelle</p>
                                <p class="font-medium text-gray-700 text-sm">
                                    {{ trim(($equipment->parc->utilisateur_nom ?? '') . ' ' . ($equipment->parc->utilisateur_prenom ?? '')) ?: 'Non affecté' }}
                                </p>
                                <p class="text-xs text-gray-400">{{ $equipment->parc->departement ?? '—' }}</p>
                            </div>
                            {{-- Flèche --}}
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </div>
                            </div>
                            {{-- Nouveau --}}
                            <div class="flex-1 bg-white rounded-lg p-3 border border-indigo-200">
                                <p class="text-xs text-indigo-400 mb-1">Nouvelle affectation</p>
                                <p class="font-medium text-indigo-700 text-sm" id="preview-nom">—</p>
                                <p class="text-xs text-indigo-400" id="preview-dept">—</p>
                            </div>
                        </div>
                    </div>

                    {{-- Boutons --}}
                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Confirmer la réaffectation
                        </button>
                        <a href="{{ route('parc.index') }}"
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg transition flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Preview live du nom/département dans le récapitulatif
const nomInput    = document.querySelector('[name="nouveau_utilisateur_nom"]');
const prenomInput = document.querySelector('[name="nouveau_utilisateur_prenom"]');
const deptInput   = document.querySelector('[name="nouveau_departement"]');
const previewNom  = document.getElementById('preview-nom');
const previewDept = document.getElementById('preview-dept');

function updatePreview() {
    const nom    = nomInput.value.trim();
    const prenom = prenomInput.value.trim();
    const dept   = deptInput.value.trim();

    previewNom.textContent  = [nom, prenom].filter(Boolean).join(' ') || '—';
    previewDept.textContent = dept || '—';
}

nomInput.addEventListener('input', updatePreview);
prenomInput.addEventListener('input', updatePreview);
deptInput.addEventListener('input', updatePreview);
</script>
@endsection