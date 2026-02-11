@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Modifier l'Affectation</h1>
                <p class="text-gray-600 mt-2">Modification de l'affectation parc</p>
            </div>
            <a href="{{ route('parc.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>
    </div>

    <!-- Messages d'erreur globaux -->
    @if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="text-red-800 font-semibold mb-2">Erreurs de validation :</h3>
                <ul class="list-disc list-inside text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Formulaire -->
    <form action="{{ route('parc.update', $parc->id) }}" method="POST" class="bg-white shadow-md rounded-xl p-8">
        @csrf
        @method('PUT')
        
        <!-- Section 1: Équipement (NON MODIFIABLE) -->
        <div class="mb-8 pb-8 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
                Équipement
            </h2>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de série</label>
                        <div class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-gray-900 font-medium">
                            {{ $parc->numero_serie }}
                        </div>
                    </div>
                    
                    @if($parc->equipment)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type & Modèle</label>
                        <div class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-gray-700">
                            {{ $parc->equipment->type }} - {{ $parc->equipment->marque }} {{ $parc->equipment->modele }}
                        </div>
                    </div>
                    @endif
                </div>
                
                <p class="text-sm text-gray-500 mt-3 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    L'équipement ne peut pas être changé lors de la modification. Pour affecter à un autre équipement, créez une nouvelle affectation.
                </p>
            </div>
        </div>

        <!-- Section 2: Informations utilisateur -->
        <div class="mb-8 pb-8 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                Informations Utilisateur
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom utilisateur -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="utilisateur_nom">
                        Nom de l'utilisateur <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="utilisateur_nom" 
                           id="utilisateur_nom" 
                           value="{{ old('utilisateur_nom', $parc->utilisateur_nom) }}" 
                           required
                           placeholder="Ex: DIOP"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    @error('utilisateur_nom')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prénom utilisateur -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="utilisateur_prenom">
                        Prénom de l'utilisateur <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="utilisateur_prenom" 
                           id="utilisateur_prenom" 
                           value="{{ old('utilisateur_prenom', $parc->utilisateur_prenom) }}" 
                           required
                           placeholder="Ex: Amadou"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    @error('utilisateur_prenom')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Département -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="departement">
                        Département <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="departement" 
                           id="departement" 
                           value="{{ old('departement', $parc->departement) }}" 
                           required
                           placeholder="Ex: IT, RH, Finance..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    @error('departement')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Poste affecté -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="poste_affecte">
                        Poste affecté <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="poste_affecte" 
                           id="poste_affecte" 
                           value="{{ old('poste_affecte', $parc->poste_affecte) }}" 
                           required
                           placeholder="Ex: Développeur, Administrateur..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    @error('poste_affecte')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut d'usage -->
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-semibold mb-2" for="statut_usage">
                        Statut d'usage <span class="text-red-500">*</span>
                    </label>
                    <select name="statut_usage" id="statut_usage" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                        <option value="actif" {{ old('statut_usage', $parc->statut_usage) == 'actif' ? 'selected' : '' }}>
                            ✅ Actif - En utilisation
                        </option>
                        <option value="inactif" {{ old('statut_usage', $parc->statut_usage) == 'inactif' ? 'selected' : '' }}>
                            ⏸️ Inactif - Non utilisé
                        </option>
                        <option value="en_pret" {{ old('statut_usage', $parc->statut_usage) == 'en_pret' ? 'selected' : '' }}>
                            🔄 En prêt - Temporaire
                        </option>
                    </select>
                    @error('statut_usage')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 3: Dates -->
        <div class="mb-8 pb-8 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                Dates d'affectation
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date d'affectation -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="date_affectation">
                        Date d'affectation <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="date_affectation" 
                           id="date_affectation" 
                           value="{{ old('date_affectation', $parc->date_affectation ? $parc->date_affectation->format('Y-m-d') : '') }}" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    @error('date_affectation')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date de retour prévue -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2" for="date_retour_prevue">
                        Date de retour prévue
                        <span class="text-gray-500 text-sm font-normal">(optionnel)</span>
                    </label>
                    <input type="date" 
                           name="date_retour_prevue" 
                           id="date_retour_prevue" 
                           value="{{ old('date_retour_prevue', $parc->date_retour_prevue ? $parc->date_retour_prevue->format('Y-m-d') : '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                    @error('date_retour_prevue')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">
                        💡 Pour les affectations temporaires uniquement
                    </p>
                </div>
            </div>
        </div>

        <!-- Section 4: Notes -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                </svg>
                Notes et commentaires
            </h2>

            <div>
                <label class="block text-gray-700 font-semibold mb-2" for="notes_affectation">
                    Notes d'affectation
                    <span class="text-gray-500 text-sm font-normal">(optionnel)</span>
                </label>
                <textarea name="notes_affectation" 
                          id="notes_affectation" 
                          rows="4"
                          placeholder="Ajoutez des informations complémentaires sur cette affectation..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition resize-none">{{ old('notes_affectation', $parc->notes_affectation) }}</textarea>
                @error('notes_affectation')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a href="{{ route('parc.index') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Annuler
            </a>
            
            <button type="submit" 
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer les modifications
            </button>
        </div>
    </form>

    <!-- Informations supplémentaires -->
    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="text-blue-800 font-semibold mb-1">Remarques importantes</h3>
                <ul class="text-blue-700 text-sm space-y-1">
                    <li>• Le numéro de série de l'équipement ne peut pas être modifié</li>
                    <li>• La date d'affectation doit être antérieure à la date de retour prévue</li>
                    <li>• Les champs marqués d'un astérisque (<span class="text-red-500">*</span>) sont obligatoires</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
/* Améliorer le style des select */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

/* Animation pour les champs en focus */
input:focus, select:focus, textarea:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Responsive */
@media (max-width: 768px) {
    input[type="text"],
    input[type="date"],
    select,
    textarea {
        font-size: 16px; /* Empêche le zoom sur iOS */
    }
}
</style>
@endsection