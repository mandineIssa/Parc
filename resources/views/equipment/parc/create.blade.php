@extends('layouts.app')

@section('content')
<div class="w-full max-w-4xl mx-auto px-4 sm:px-6 py-8">
    <div class="flex flex-col md:flex-row justify-end items-start md:items-center mb-8 gap-4">
        <a href="{{ route('parc.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                Retour au parc
            </a>
    </div>

    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
        <ul class="list-disc list-inside text-red-700 text-sm">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
    </div>
    @endif

    <form action="{{ route('parc.store') }}" method="POST" id="parc-form" class="bg-white rounded-xl shadow-md overflow-hidden">
        @csrf
        
        <div class="px-6 py-4 bg-gradient-to-r from-[#C8102E] to-[#a00d24]">
            <h2 class="text-lg font-semibold text-white">Nouvelle entrée parc</h2>
            <p class="text-red-100 text-sm mt-1">Une notification sera envoyée après enregistrement</p>
        </div>

        <div class="p-6 space-y-8">

            {{-- 1. Classification --}}
            <section>
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">1. Classification</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Type d'équipement <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required onchange="updateCategories()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                            <option value="">-- Sélectionner --</option>
                            @foreach(['Informatique', 'Réseau', 'Électronique', 'Logiciel'] as $t)
                                <option value="{{ $t }}" {{ old('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Catégorie <span class="text-red-500">*</span></label>
                        <select name="categorie" id="categorie" required onchange="updateSousCategories()" disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                            <option value="">-- Sélectionner le type d'abord --</option>
                    </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Sous-Catégorie <span class="text-red-500">*</span></label>
                        <select name="sous_categorie" id="sous_categorie" required disabled
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                            <option value="">-- Sélectionner la catégorie d'abord --</option>
                        </select>
                    </div>
                </div>
            </section>

            {{-- 2. Informations de base --}}
            <section>
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">2. Informations de base</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">N° Série <span class="text-red-500">*</span></label>
                        <input type="text" name="numero_serie" value="{{ old('numero_serie') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                        </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nom équipement <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required placeholder="Ex: PC Bureau DELL"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Marque <span class="text-red-500">*</span></label>
                        <input type="text" name="marque" value="{{ old('marque') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Modèle <span class="text-red-500">*</span></label>
                        <input type="text" name="modele" value="{{ old('modele') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
            </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Fournisseur <span class="text-red-500">*</span></label>
                        <select name="fournisseur_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                            <option value="">-- Sélectionner --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('fournisseur_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->nom }}</option>
                            @endforeach
                        </select>
                        </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Garantie <span class="text-red-500">*</span></label>
                        <input type="text" name="garantie" value="{{ old('garantie') }}" required placeholder="Ex: 3 ans"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Date Livraison <span class="text-red-500">*</span></label>
                        <input type="date" name="date_livraison" value="{{ old('date_livraison', date('Y-m-d')) }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Prix (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" name="prix" step="0.01" min="0" value="{{ old('prix') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                        </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">N° Facture</label>
                        <input type="text" name="reference_facture" value="{{ old('reference_facture') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Emplacement (Agence)</label>
                        <select name="agency_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                            <option value="">-- Sélectionner --</option>
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->id }}" {{ old('agency_id') == $agency->id ? 'selected' : '' }}>{{ $agency->nom }}</option>
                            @endforeach
                        </select>
                </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">État <span class="text-red-500">*</span></label>
                        <select name="etat" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                            <option value="">-- Sélectionner --</option>
                            @foreach(['neuf' => 'Neuf', 'bon' => 'Bon', 'moyen' => 'Moyen', 'mauvais' => 'Mauvais'] as $val => $label)
                                <option value="{{ $val }}" {{ old('etat') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
            </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Adresse MAC</label>
                        <input type="text" name="adresse_mac" value="{{ old('adresse_mac') }}" placeholder="00:11:22:33:44:55"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                        </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Adresse IP</label>
                        <input type="text" name="adresse_ip" value="{{ old('adresse_ip') }}" placeholder="192.168.1.10"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                    </div>
                </div>
            </section>

            {{-- 3. Affectation --}}
            <section>
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">3. Affectation utilisateur</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Nom <span class="text-red-500">*</span></label>
                            <input type="text" name="utilisateur_nom" id="utilisateur_nom" value="{{ old('utilisateur_nom') }}" required
                                class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Prénom <span class="text-red-500">*</span></label>
                            <input type="text" name="utilisateur_prenom" id="utilisateur_prenom" value="{{ old('utilisateur_prenom') }}" required
                                class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                    </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">E-mail</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Téléphone</label>
                            <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}"
                                class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                            </div>
                            <div>
                            <label class="block text-sm font-semibold text-gray-700">Département <span class="text-red-500">*</span></label>
                            <input type="text" name="departement" id="departement" value="{{ old('departement') }}" required
                                class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Poste affecté <span class="text-red-500">*</span></label>
                            <input type="text" name="poste_affecte" id="poste_affecte" value="{{ old('poste_affecte') }}" required
                                class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                            </div>
                            <div>
                            <label class="block text-sm font-semibold text-gray-700">Position / grade <span class="text-red-500">*</span></label>
                            <select name="position" required class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                                <option value="">— Choisir —</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos }}" {{ old('position') === $pos ? 'selected' : '' }}>{{ $pos }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Localisation bureau</label>
                            <input type="text" name="localisation" value="{{ old('localisation') }}"
                                class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                            </div>
                            <div>
                            <label class="block text-sm font-semibold text-gray-700">Date d'affectation <span class="text-red-500">*</span></label>
                            <input type="date" name="date_affectation" value="{{ old('date_affectation', date('Y-m-d')) }}" required
                                class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Date retour prévue</label>
                            <input type="date" name="date_retour_prevue" value="{{ old('date_retour_prevue') }}"
                                class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Motif d'affectation</label>
                            <select name="affectation_reason" class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                                <option value="">— Sélectionner —</option>
                                @foreach($affectationReasons as $reason)
                                    <option value="{{ $reason }}" {{ old('affectation_reason') === $reason ? 'selected' : '' }}>{{ $reason }}</option>
                                @endforeach
                            </select>
            </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">Statut d'usage <span class="text-red-500">*</span></label>
                            <select name="statut_usage" required class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">
                                <option value="actif" {{ old('statut_usage', 'actif') === 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="inactif" {{ old('statut_usage') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                                <option value="en_pret" {{ old('statut_usage') === 'en_pret' ? 'selected' : '' }}>En prêt</option>
                            </select>
            </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Détail du motif</label>
                        <textarea name="affectation_reason_detail" rows="2" class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">{{ old('affectation_reason_detail') }}</textarea>
                        </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Notes</label>
                        <textarea name="notes_affectation" rows="3" class="w-full mt-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#C8102E]">{{ old('notes_affectation') }}</textarea>
                    </div>
                </div>
            </section>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
            <a href="{{ route('parc.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white font-semibold text-sm">Annuler</a>
            <button type="submit" class="px-8 py-3 rounded-lg text-white font-semibold text-sm bg-[#C8102E] hover:bg-[#a00d24] shadow-md">
                Ajouter au parc
            </button>
        </div>
    </form>
</div>

<script src="{{ asset('js/equipment-categories.js') }}"></script>
<script>
const updateCategories = window.updateCategoriesParc;
const updateSousCategories = window.updateSousCategoriesParc;
const categoriesData = window.categoriesData;

function restoreCategorySelection() {
    const oldType = @json(old('type'));
    const oldCategorie = @json(old('categorie'));
    const oldSousCategorie = @json(old('sous_categorie'));

    if (!oldType) return;

    document.getElementById('type').value = oldType;
    updateCategories();

    if (oldCategorie) {
        document.getElementById('categorie').value = oldCategorie;
        updateSousCategories();
    }
    if (oldSousCategorie) {
        document.getElementById('sous_categorie').value = oldSousCategorie;
    }
}

document.addEventListener('DOMContentLoaded', restoreCategorySelection);
</script>
@endsection
