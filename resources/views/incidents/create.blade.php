{{-- resources/views/incidents/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('incidents.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-red-600 transition-colors mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux fiches
        </a>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center shadow">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Formulaire conforme à la norme ITIL — Workflow N+1 → N+2 → N+3</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('incidents.store') }}" class="space-y-6">
        @csrf

        {{-- SECTION 1: INFORMATIONS GÉNÉRALES --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-red-600 px-6 py-3">
                <h2 class="text-white font-semibold text-sm uppercase tracking-wider">1. INFORMATIONS GÉNÉRALES</h2>
            </div>
            <div class="p-6 space-y-5">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Date de survenue <span class="text-red-500">*</span></label>
                        <input type="date" name="date_incident" value="{{ old('date_incident', now()->format('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Heure de début</label>
                        <input type="time" name="heure_debut" value="{{ old('heure_debut', now()->format('H:i')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Application concernée</label>
                        <input type="text" name="application_concernee" value="{{ old('application_concernee') }}"
                            placeholder="Ex: Core Banking / Flexcube, API Wallet..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Environnement</label>
                        <select name="environnement" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="Production">🏭 Production</option>
                            <option value="Recette">🧪 Recette</option>
                            <option value="Développement">💻 Développement</option>
                            <option value="Préproduction">📦 Préproduction</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Type d'incident</label>
                        <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="application">📱 Application</option>
                            <option value="logiciel">💿 Logiciel</option>
                            <option value="materiel">🖥️ Matériel</option>
                            <option value="reseau_telecom">🌐 Réseaux & Télécom</option>
                            <option value="infrastructure">🏗️ Infrastructure</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Criticité</label>
                        <select name="niveau_criticite" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="P1">🔴 P1 - Critique (production bloquée)</option>
                            <option value="P2">🟠 P2 - Élevé (dégradation majeure)</option>
                            <option value="P3">🟡 P3 - Moyen (impact partiel)</option>
                            <option value="P4">🟢 P4 - Faible (mineur)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Canal de remontée</label>
                        <select name="point_entree" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="itsm">🔧 Application ITSM</option>
                            <option value="hotline">📞 Hotline</option>
                            <option value="mail">✉️ Email</option>
                            <option value="telephone">📱 Téléphone</option>
                            <option value="application">📱 Application</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Déclarant</label>
                        <input type="text" name="utilisateur" value="{{ old('utilisateur', Auth::user()->name) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Entité / Service</label>
                        <input type="text" name="entite" value="{{ old('entite') }}"
                            placeholder="Ex: DSI, Agence Dakar Plateau..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fonction</label>
                        <input type="text" name="fonction" value="{{ old('fonction') }}"
                            placeholder="Ex: Responsable applicatif"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Service impacté</label>
                        <input type="text" name="service_impacte" value="{{ old('service_impacte') }}"
                            placeholder="Ex: Core Banking, API Wallet, Virements..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: DESCRIPTION & IMPACT --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-orange-600 px-6 py-3">
                <h2 class="text-white font-semibold text-sm uppercase tracking-wider">2. DESCRIPTION ET IMPACT</h2>
            </div>
            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sujet / Titre <span class="text-red-500">*</span></label>
                    <input type="text" name="sujet" value="{{ old('sujet') }}"
                        placeholder="Ex: Échec des transactions API wallet - désynchronisation comptable"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description détaillée <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="5" required
                        placeholder="Décrivez l'incident : ce qui s'est passé, les conditions, le message d'erreur..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Impact métier</label>
                    <textarea name="impact_metier" rows="3"
                        placeholder="Conséquences sur l'activité : nombre d'utilisateurs bloqués, retards, risques financiers..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">{{ old('impact_metier') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre de clients impactés</label>
                        <input type="number" name="nb_clients_impactes" value="{{ old('nb_clients_impactes', 1) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre d'utilisateurs impactés</label>
                        <input type="number" name="nb_utilisateurs_impactes" value="{{ old('nb_utilisateurs_impactes', 1) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="bloquant" value="1" {{ old('bloquant') ? 'checked' : '' }}
                            class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <span class="text-sm font-medium text-gray-700">🚨 Incident bloquant (production arrêtée)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="reproductible" value="1" {{ old('reproductible') ? 'checked' : '' }}
                            class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <span class="text-sm font-medium text-gray-700">🔄 Reproductible</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('incidents.index') }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                Annuler
            </a>
            <button type="submit"
                class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Soumettre l'incident ITIL
            </button>
        </div>
    </form>
</div>
@endsection