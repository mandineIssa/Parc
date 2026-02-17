@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Nouvelle Affectation</h1>
            <p class="text-gray-600 mt-2">Affecter un équipement à un utilisateur</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('parc.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour au parc
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
    <form action="{{ route('parc.store') }}" method="POST" class="bg-white rounded-xl shadow-md overflow-hidden">
        @csrf
        
        <!-- En-tête du formulaire -->
        <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
            <h2 class="text-lg font-semibold text-white">Informations d'affectation</h2>
            <p class="text-green-100 text-sm mt-1">Remplissez les champs ci-dessous pour créer une nouvelle affectation</p>
        </div>

        <!-- Corps du formulaire -->
        <div class="p-6 space-y-6">
            <!-- Équipement -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700" for="numero_serie">
                    Équipement <span class="text-red-500">*</span>
                </label>
                <select name="numero_serie" id="numero_serie" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition bg-white @error('numero_serie') border-red-500 @enderror">
                    <option value="" class="text-gray-500">Sélectionner un équipement</option>
                    @foreach($equipments as $equipment)
                        <option value="{{ $equipment->numero_serie }}" {{ old('numero_serie') == $equipment->numero_serie ? 'selected' : '' }}
                            class="py-2">
                            {{ $equipment->numero_serie }} - {{ $equipment->type }} ({{ $equipment->marque }} {{ $equipment->modele }})
                        </option>
                    @endforeach
                </select>
                @error('numero_serie')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Sélectionnez l'équipement à affecter</p>
            </div>

            <!-- Section Utilisateur -->
            <div class="space-y-4">
                <h3 class="text-md font-semibold text-gray-700 border-b pb-2">Informations de l'utilisateur</h3>
                
                <!-- Sélection utilisateur existant -->
      <!--           <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700" for="utilisateur_id">
                        Utilisateur existant
                    </label>
                    <select name="utilisateur_id" id="utilisateur_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition bg-white @error('utilisateur_id') border-red-500 @enderror">
                        <option value="" class="text-gray-500">-- Sélectionner un utilisateur existant (optionnel) --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('utilisateur_id') == $user->id ? 'selected' : '' }}
                                class="py-2">
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('utilisateur_id')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500">Optionnel - Sélectionnez un utilisateur existant ou saisissez un nouveau nom/prénom ci-dessous</p>
                </div> -->

                <!-- Nom et Prénom - Grille 2 colonnes -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom de l'utilisateur -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700" for="utilisateur_nom">
                            Nom de l'utilisateur <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input type="text" name="utilisateur_nom" id="utilisateur_nom" 
                                   value="{{ old('utilisateur_nom') }}" required
                                   placeholder="Ex: DIOP"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition @error('utilisateur_nom') border-red-500 @enderror">
                        </div>
                        @error('utilisateur_nom')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prénom de l'utilisateur -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700" for="utilisateur_prenom">
                            Prénom de l'utilisateur <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input type="text" name="utilisateur_prenom" id="utilisateur_prenom" 
                                   value="{{ old('utilisateur_prenom') }}" required
                                   placeholder="Ex: Amadou"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition @error('utilisateur_prenom') border-red-500 @enderror">
                        </div>
                        @error('utilisateur_prenom')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email de l'utilisateur (optionnel) -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700" for="email">
                        Email de l'utilisateur
                        <span class="text-gray-500 text-xs font-normal ml-1">(optionnel)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" 
                               value="{{ old('email') }}"
                               placeholder="ex: utilisateur@exemple.com"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition @error('email') border-red-500 @enderror">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500">Optionnel - Saisissez l'email pour recevoir les notifications</p>
                </div>
            </div>

            <!-- Département et Poste - Grille 2 colonnes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Département -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700" for="departement">
                        Département <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <input type="text" name="departement" id="departement" 
                               value="{{ old('departement') }}" required
                               placeholder="ex: Direction Informatique"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition @error('departement') border-red-500 @enderror">
                    </div>
                    @error('departement')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Poste -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700" for="poste_affecte">
                        Poste <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="text" name="poste_affecte" id="poste_affecte" 
                               value="{{ old('poste_affecte') }}" required
                               placeholder="ex: Responsable réseau"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition @error('poste_affecte') border-red-500 @enderror">
                    </div>
                    @error('poste_affecte')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Dates - Grille 2 colonnes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date d'affectation -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700" for="date_affectation">
                        Date d'affectation <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="date" name="date_affectation" id="date_affectation" 
                               value="{{ old('date_affectation', date('Y-m-d')) }}" required
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition @error('date_affectation') border-red-500 @enderror">
                    </div>
                    @error('date_affectation')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date de retour prévue -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-700" for="date_retour_prevue">
                        Date de retour prévue
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <input type="date" name="date_retour_prevue" id="date_retour_prevue" 
                               value="{{ old('date_retour_prevue') }}"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition @error('date_retour_prevue') border-red-500 @enderror">
                    </div>
                    @error('date_retour_prevue')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500">Optionnelle - Laissez vide si non définie</p>
                </div>
            </div>

            <!-- Statut d'usage -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700" for="statut_usage">
                    Statut d'usage <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="relative flex items-center p-4 border rounded-lg cursor-pointer transition
                        {{ old('statut_usage', 'en_service') == 'en_service' ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-green-300 hover:bg-green-50' }}">
                        <input type="radio" name="statut_usage" value="en_service" 
                               class="sr-only" 
                               {{ old('statut_usage', 'en_service') == 'en_service' ? 'checked' : '' }}>
                        <div class="flex items-center w-full">
                            <div class="flex-shrink-0 mr-3">
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center
                                    {{ old('statut_usage', 'en_service') == 'en_service' ? 'border-green-500 bg-green-500' : 'border-gray-300' }}">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <span class="block font-medium text-gray-900">En service</span>
                                <span class="text-xs text-gray-500">Équipement actif</span>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 border rounded-lg cursor-pointer transition
                        {{ old('statut_usage') == 'reserve' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300 hover:border-yellow-300 hover:bg-yellow-50' }}">
                        <input type="radio" name="statut_usage" value="reserve" 
                               class="sr-only"
                               {{ old('statut_usage') == 'reserve' ? 'checked' : '' }}>
                        <div class="flex items-center w-full">
                            <div class="flex-shrink-0 mr-3">
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center
                                    {{ old('statut_usage') == 'reserve' ? 'border-yellow-500 bg-yellow-500' : 'border-gray-300' }}">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <span class="block font-medium text-gray-900">Réservé</span>
                                <span class="text-xs text-gray-500">Attente d'activation</span>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 border rounded-lg cursor-pointer transition
                        {{ old('statut_usage') == 'maintenance' ? 'border-red-500 bg-red-50' : 'border-gray-300 hover:border-red-300 hover:bg-red-50' }}">
                        <input type="radio" name="statut_usage" value="maintenance" 
                               class="sr-only"
                               {{ old('statut_usage') == 'maintenance' ? 'checked' : '' }}>
                        <div class="flex items-center w-full">
                            <div class="flex-shrink-0 mr-3">
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center
                                    {{ old('statut_usage') == 'maintenance' ? 'border-red-500 bg-red-500' : 'border-gray-300' }}">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <span class="block font-medium text-gray-900">Maintenance</span>
                                <span class="text-xs text-gray-500">En réparation</span>
                            </div>
                        </div>
                    </label>
                </div>
                @error('statut_usage')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700" for="notes_affectation">
                    Notes
                </label>
                <textarea name="notes_affectation" id="notes_affectation" rows="4"
                    placeholder="Informations complémentaires sur l'affectation (état de l'équipement, remarques particulières, etc.)"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition resize-y @error('notes_affectation') border-red-500 @enderror">{{ old('notes_affectation') }}</textarea>
                @error('notes_affectation')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500">Optionnel - 500 caractères maximum</p>
            </div>

            <!-- Aperçu des informations -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-blue-800">Information importante</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Une fois l'affectation créée, l'équipement sera automatiquement marqué comme "Affecté" dans le parc. Un email de notification sera envoyé à l'utilisateur si une adresse email est renseignée.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pied du formulaire -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row justify-end gap-3">
            <a href="{{ route('parc.index') }}" 
               class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-sm font-semibold rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                Annuler
            </a>
            <button type="submit" 
                    class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                </svg>
                Créer l'affectation
            </button>
        </div>
    </form>

    <!-- Informations complémentaires -->
    <div class="mt-8 bg-green-50 rounded-xl border border-green-200 p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">À propos des affectations</h3>
                <div class="mt-2 text-sm text-green-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Les champs marqués d'un <span class="text-red-500">*</span> sont obligatoires</li>
                        <li>Un équipement ne peut être affecté qu'à un seul utilisateur à la fois</li>
                        <li>La date de retour prévue peut être modifiée ultérieurement</li>
                        <li>L'email est optionnel mais recommandé pour recevoir les notifications</li>
                        <li>Vous pouvez consulter l'historique des affectations depuis la fiche détaillée de l'équipement</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'affichage du statut sélectionné
    const radioGroups = document.querySelectorAll('input[type="radio"][name="statut_usage"]');
    
    radioGroups.forEach(radio => {
        radio.addEventListener('change', function() {
            // Retirer les classes de tous les labels parents
            document.querySelectorAll('label[class*="border-"]').forEach(label => {
                label.classList.remove('border-green-500', 'bg-green-50', 'border-yellow-500', 'bg-yellow-50', 'border-red-500', 'bg-red-50');
                label.classList.add('border-gray-300');
            });
            
            // Ajouter les classes au label parent du radio sélectionné
            const parentLabel = this.closest('label');
            if (this.value === 'en_service') {
                parentLabel.classList.add('border-green-500', 'bg-green-50');
            } else if (this.value === 'reserve') {
                parentLabel.classList.add('border-yellow-500', 'bg-yellow-50');
            } else if (this.value === 'maintenance') {
                parentLabel.classList.add('border-red-500', 'bg-red-50');
            }
            
            // Mettre à jour les cercles
            document.querySelectorAll('.w-6.h-6.rounded-full').forEach(circle => {
                circle.classList.remove('border-green-500', 'bg-green-500', 'border-yellow-500', 'bg-yellow-500', 'border-red-500', 'bg-red-500');
                circle.classList.add('border-gray-300');
            });
            
            const circle = this.closest('label').querySelector('.w-6.h-6.rounded-full');
            if (this.value === 'en_service') {
                circle.classList.add('border-green-500', 'bg-green-500');
            } else if (this.value === 'reserve') {
                circle.classList.add('border-yellow-500', 'bg-yellow-500');
            } else if (this.value === 'maintenance') {
                circle.classList.add('border-red-500', 'bg-red-500');
            }
        });
    });
    
    // Validation côté client
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
        }
    });
    
    // Retirer le rouge quand l'utilisateur commence à remplir
    form.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('border-red-500');
        });
        field.addEventListener('change', function() {
            this.classList.remove('border-red-500');
        });
    });

    // Gestion de la sélection utilisateur vs saisie manuelle
    const utilisateurSelect = document.getElementById('utilisateur_id');
    const nomInput = document.getElementById('utilisateur_nom');
    const prenomInput = document.getElementById('utilisateur_prenom');
    const emailInput = document.getElementById('email');
    
    utilisateurSelect.addEventListener('change', function() {
        if (this.value) {
            // Si un utilisateur existant est sélectionné, on peut pré-remplir nom, prénom et email
            // Note: Cette fonctionnalité nécessiterait une requête AJAX pour récupérer les infos
            // Pour l'instant, on laisse l'utilisateur saisir manuellement
            console.log('Utilisateur sélectionné:', this.value);
        }
    });
});
</script>

<style>
/* Animation pour les transitions */
.transition {
    transition: all 0.3s ease;
}

/* Style pour les champs requis */
[required] {
    background-image: radial-gradient(circle, #f56565 2px, transparent 2px);
    background-repeat: no-repeat;
    background-position: right 10px top 10px;
    background-size: 8px 8px;
}

/* Responsive */
@media (max-width: 640px) {
    [required] {
        background-position: right 8px top 8px;
    }
    
    .grid {
        gap: 1rem;
    }
}

/* Style pour les labels radio */
label[class*="border-"] {
    cursor: pointer;
    transition: all 0.2s ease;
}

label[class*="border-"]:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
</style>
@endsection