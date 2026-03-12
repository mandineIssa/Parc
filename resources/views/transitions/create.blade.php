@extends('layouts.app')
@section('title', 'Transition d\'État')
@section('header', 'Affectation')

@section('content')
<div class="card-cofina max-w-4xl mx-auto">
    <div class="mb-6 text-center">
        <h3 class="text-lg font-bold text-cofina-red mb-2">Équipement: {{ $equipment->nom }}</h3>
        <p class="text-gray-600 flex flex-wrap justify-center gap-4">
            <span class="inline-flex items-center mr-4">
                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                N° Série: <span class="font-bold ml-1">{{ $equipment->numero_serie }}</span>
            </span>
            <span class="inline-flex items-center">
                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Statut: <span
                    class="font-bold ml-1 {{ $equipment->statut == 'stock' ? 'text-green-600' : ($equipment->statut == 'parc' ? 'text-blue-600' : 'text-yellow-600') }}">
                    {{ ucfirst($equipment->statut) }}
                </span>
            </span>
        </p>
    </div>

    <!-- Carte des transitions disponibles -->
    <div class="mb-8">
        <h4 class="text-xl font-bold text-cofina-red mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-cofina-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
            Transitions disponibles
        </h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @if($equipment->statut == 'stock')
            <!-- Stock → Parc -->
            <div class="transition-card active:bg-red-50" data-target="stock-to-parc">
                <div class="flex items-center p-3">
                    <div class="p-2 border border-gray-200 bg-white rounded-lg mr-3">
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                                    clip-rule="evenodd" />
                            </svg>
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-bold text-base">Affecter au Parc</h5>
                        <p class="text-xs text-gray-500">Sortir du stock pour affectation</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>

            <!-- Stock → Hors Service -->
            <div class="transition-card active:bg-red-50" data-target="stock-to-hors-service">
                <div class="flex items-center p-3">
                    <div class="p-2 border border-gray-200 bg-white rounded-lg mr-3">
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-bold text-base">Mettre Hors Service</h5>
                        <p class="text-xs text-gray-500">Équipement neuf défectueux</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
            @endif

            @if($equipment->statut == 'parc')
            <!-- Parc → Maintenance -->
            <div class="transition-card active:bg-red-50" data-target="parc-to-maintenance">
                <div class="flex items-center p-3">
                    <div class="p-2 border border-gray-200 bg-white rounded-lg mr-3">
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                            <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-bold text-base">Envoyer en Maintenance</h5>
                        <p class="text-xs text-gray-500">Pour réparation ou entretien</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>

            <!-- Parc → Hors Service -->
            <div class="transition-card active:bg-red-50" data-target="parc-to-hors-service">
                <div class="flex items-center p-3">
                    <div class="p-2 border border-gray-200 bg-white rounded-lg mr-3">
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-bold text-base">Mettre Hors Service</h5>
                        <p class="text-xs text-gray-500">Irréparable ou obsolète</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>

            <!-- Parc → Perdu -->
            <div class="transition-card active:bg-red-50" data-target="parc-to-perdu">
                <div class="flex items-center p-3">
                    <div class="p-2 border border-gray-200 bg-white rounded-lg mr-3">
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-bold text-base">Déclarer Perdu</h5>
                        <p class="text-xs text-gray-500">Vol, perte ou disparition</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
            {{-- Parc → Stock Décélé --}}
            <div class="transition-card active:bg-orange-50" data-target="parc-to-stock-decele">
                <div class="flex items-center p-3">
                    <div class="p-2 border border-gray-200 bg-white rounded-lg mr-3">
                        <div class="flex items-center space-x-1">
                            {{-- Icône Parc (utilisateur vert) --}}
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{-- Flèche --}}
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            {{-- Icône Stock Décélé (boîte orange) --}}
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-bold text-base">Retour Stock Décélé</h5>
                        <p class="text-xs text-gray-500">Restitution et mise en stock décélé</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
            @endif

            @if($equipment->statut == 'maintenance')
            <!-- Maintenance → Stock -->
            <div class="transition-card active:bg-red-50" data-target="maintenance-to-stock">
                <div class="flex items-center p-3">
                    <div class="p-2 border border-gray-200 bg-white rounded-lg mr-3">
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-bold text-base">Retour au Stock</h5>
                        <p class="text-xs text-gray-500">Maintenance terminée</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>

            <!-- Maintenance → Hors Service -->
            <div class="transition-card active:bg-red-50" data-target="maintenance-to-hors-service">
                <div class="flex items-center p-3">
                    <div class="p-2 border border-gray-200 bg-white rounded-lg mr-3">
                        <div class="flex items-center space-x-1">
                            <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h5 class="font-bold text-base">Déclarer Irréparable</h5>
                        <p class="text-xs text-gray-500">Coût de réparation trop élevé</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Conteneur pour le flux de travail 3 étapes -->
    <div id="three-step-flow-container" class="mt-6 hidden">
        <!-- Barre de progression -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <div class="w-1/4">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-cofina-red text-white flex items-center justify-center font-bold border-4 border-white shadow-md">
                            1
                        </div>
                        <span class="mt-2 text-sm font-semibold">Fiche d'Installation</span>
                    </div>
                </div>
                <div class="w-1/4">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center font-bold border-4 border-white shadow-md">
                            2
                        </div>
                        <span class="mt-2 text-sm text-gray-600">Affectation Simple</span>
                    </div>
                </div>
                <div class="w-1/4">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center font-bold border-4 border-white shadow-md">
                            3
                        </div>
                        <span class="mt-2 text-sm text-gray-600">Fiche de Mouvement</span>
                    </div>
                </div>
                <div class="w-1/4">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center font-bold border-4 border-white shadow-md">
                            4
                        </div>
                        <span class="mt-2 text-sm text-gray-600">Validation</span>
                    </div>
                </div>
            </div>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-cofina-red w-1/4 transition-all duration-500" id="progress-bar"></div>
            </div>
        </div>

        <!-- Conteneur des formulaires -->
        <div id="forms-container">
            <!-- Les formulaires s'affichent ici dynamiquement -->
        </div>

        <!-- Navigation entre les étapes -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between hidden" id="navigation-buttons">
            <button type="button" onclick="previousStep()" class="btn-cofina-outline px-6 py-3">
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Étape précédente
            </button>
            
            <div class="flex gap-4">
                <button type="button" onclick="saveDraft()" class="btn-cofina-outline px-6 py-3">
                    💾 Sauvegarder brouillon
                </button>
                <button type="button" onclick="nextStep()" class="btn-cofina px-6 py-3">
                    Étape suivante
                    <svg class="w-5 h-5 ml-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Bouton de soumission final -->
        <div class="mt-8 pt-6 border-t border-gray-200 text-center hidden" id="final-submit">
            <button type="button" onclick="submitAllForms()" class="btn-cofina-success px-8 py-4 text-lg font-bold">
                ✅ SOUMETTRE POUR APPROBATION
            </button>
            <p class="text-sm text-gray-600 mt-2">
                Toutes les informations seront soumises pour approbation
            </p>
        </div>
    </div>

    <div id="three-step-decele-container" class="mt-6 hidden"></div>

    <!-- Conteneur pour les formulaires de transition simple -->
    <div id="transition-form-container" class="mt-6 hidden">
        <!-- Les formulaires s'affichent ici dynamiquement -->
    </div>

    <!-- Templates pour les formulaires du flux 3 étapes -->
    <div class="hidden">
        <!-- ÉTAPE 1: Fiche d'Installation -->
        <form id="installation-step-form" class="step-form" data-step="1">
            <input type="hidden" name="form_type" value="installation">
            
            <div class="card-cofina bg-white border-2 border-cofina-red">
                <div class="bg-cofina-red text-white p-6 -mx-6 -mt-6 mb-6 rounded-t-lg">
                    <h1 class="text-2xl font-bold text-center">COFINA SENEGAL - IT</h1>
                    <h2 class="text-xl font-bold text-center mt-2">PROCÉDURE D'INSTALLATION DE MACHINES</h2>
                    <div class="mt-4 text-center">
                        <label class="inline-block">
                            Date d'application :
                            <input type="date" name="date_application" value="{{ date('Y-m-d') }}"
                                class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold" required>
                        </label>
                    </div>
                </div>
                <!-- NOM DE L'AGENCE -->

                                
                
            <div class="mb-8 p-4 bg-gray-50 rounded-lg border-2 border-gray-300">
                <label class="block font-bold text-lg mb-2 text-cofina-red">
                    Agence <span class="text-red-600">*</span>
                </label>
                <select name="agency_id"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg font-bold text-lg focus:outline-none focus:border-red-600"
                    required>
                    <option value="">-- Sélectionner --</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->id }}">{{ $agency->nom }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="agence_nom" id="agence_nom_hidden">
            </div>
              
                <!-- SECTION INSTALLATION -->
                <div class="mb-8 border-3 border-blue-600 p-6 rounded-lg bg-blue-50">
                    <div class="bg-blue-600 text-white px-4 py-3 -mx-6 -mt-6 mb-6 rounded-t-lg">
                        <h3 class="text-xl font-bold">INSTALLATION</h3>
                        <div class="mt-2">
                            Date :
                            <input type="date" name="date_installation" value="{{ date('Y-m-d') }}"
                                class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold" required>
                        </div>
                    </div>

                    <!-- Prérequis -->
                    <div class="mb-6">
                        <h4 class="font-bold text-lg mb-3 text-blue-800 border-b-2 border-blue-300 pb-2">
                            ☑️ Prérequis
                        </h4>
                        <div class="space-y-2 ml-4">
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[sauvegarde_donnees]" value="1"
                                    class="h-5 w-5 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-3 text-sm">Sauvegarde des données par l'utilisateur avec l'assistance de
                                    l'IT</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[sauvegarde_outlook]" value="1"
                                    class="h-5 w-5 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-3 text-sm">Sauvegarde du fichier .pst d'Outlook</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[sauvegarde_tous_utilisateurs]" value="1"
                                    class="h-5 w-5 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-3 text-sm">Sauvegarde des données de tout utilisateur ayant ouvert la
                                    session sur la machine</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[reinstallation_os]" value="1"
                                    class="h-5 w-5 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-3 text-sm font-semibold">Réinstallation du Système d'exploitation</span>
                            </label>
                        </div>
                    </div>

                    <!-- Installation de logiciels -->
                    <div class="mb-6">
                        <h4 class="font-bold text-lg mb-3 text-blue-800 border-b-2 border-blue-300 pb-2">
                            ☑️ Installation de logiciels
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 ml-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="checklist[logiciels_adobe]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded">
                                <span class="ml-2 text-sm">Adobe</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="checklist[logiciels_ms_office]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded">
                                <span class="ml-2 text-sm">MS Office</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="checklist[logiciels_kaspersky]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded">
                                <span class="ml-2 text-sm">Kaspersky / NetAgent</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="checklist[logiciels_anydesk]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded">
                                <span class="ml-2 text-sm">Any Desk</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="checklist[logiciels_jre]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded">
                                <span class="ml-2 text-sm">JRE 7.40</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="checklist[logiciels_pilotes]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded">
                                <span class="ml-2 text-sm">Pilotes du système</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="checklist[logiciels_chrome]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded">
                                <span class="ml-2 text-sm">Google Chrome</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="checklist[logiciels_firefox]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded">
                                <span class="ml-2 text-sm">Mozilla Firefox</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="checklist[logiciels_imprimante]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded">
                                <span class="ml-2 text-sm">Imprimante</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="checklist[logiciels_zoom]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded">
                                <span class="ml-2 text-sm">Zoom / Teams</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="checklist[logiciels_vpn]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded">
                                <span class="ml-2 text-sm">VPN Client / Forticlient</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="checklist[logiciels_winrar]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded">
                                <span class="ml-2 text-sm">WinRar</span>
                            </label>
                            <label class="flex items-center col-span-2">
                                <input type="checkbox" name="checklist[logiciels_scanner_naps2]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded">
                                <span class="ml-2 text-sm">Scanner (NAPS2, ScanGear Tools)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Mise en place des raccourcis -->
                    <div class="mb-6">
                        <h4 class="font-bold text-lg mb-3 text-blue-800 border-b-2 border-blue-300 pb-2">
                            ☑️ Mise en place des raccourcis
                        </h4>
                        <div class="space-y-2 ml-4">
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[raccourcis_nafa]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-2 text-sm">NAFA (explor/maxthon/)</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[raccourcis_flexcube]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-2 text-sm">FLEXCUBE (explor/maxthon)</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[copie_logiciels_local]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-2 text-sm">Copie logiciels en local</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[applications_transfert]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-2 text-sm">Application de Transfert pour les caisses (RIA, Moneygram,
                                    WU)</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[applications_cc]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-2 text-sm">Application pour les CC (Reiz, Cofinalab, OMB,
                                    CréditFlow)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Autres -->
                    <div class="mb-6">
                        <h4 class="font-bold text-lg mb-3 text-blue-800 border-b-2 border-blue-300 pb-2">
                            ☑️ Autres
                        </h4>
                        <div class="space-y-2 ml-4">
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[creation_compte_admin]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-2 text-sm">Création d'un compte administrateur</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[integration_domaine]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-2 text-sm">Intégration de la machine dans le domaine</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[parametrage_messagerie]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-2 text-sm">Paramétrage Messagerie</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[partition_disque]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-2 text-sm">Partition du disque dur</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[desactivation_ports_usb]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-2 text-sm">Désactivation les ports USB</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[connexion_dossier_partage]" value="1"
                                    class="h-4 w-4 text-blue-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-2 text-sm">Connexion du dossier partagé</span>
                            </label>
                        </div>
                    </div>

                    <!-- Signature installateur -->
                    <div class="bg-white p-4 rounded-lg border-2 border-blue-300">
                        <h4 class="font-bold mb-3 text-blue-800">✍️ Signature de l'installateur</h4>
                        <div class="signature-container">
                            <div class="mb-3 grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-1">Nom :</label>
                                    <input type="text" name="installateur_nom" value="{{ auth()->user()->name ?? '' }}"
                                        class="w-full px-3 py-2 border-2 border-gray-300 rounded" >
                                </div>

                               <div>
                                    <label class="block text-sm font-semibold mb-1">Prénom:</label>
                                    <input type="text" name="installateur_prenom" value="{{ auth()->user()->prenom ?? '' }}"
                                        class="w-full px-3 py-2 border-2 border-gray-300 rounded" >
                                </div> 

                                <div>
                                    <label class="block text-sm font-semibold mb-1">Fonction :</label>
                                    <input type="text" name="installateur_fonction" value="IT"
                                        class="w-full px-3 py-2 border-2 border-gray-300 rounded" >
                                </div>
                            </div>
                            <div class="signature-pad-container mb-3">
                                <canvas class="signature-pad border-2 border-gray-300 rounded bg-white w-full h-32"
                                    id="signatureCanvasInstallateur"></canvas>
                            </div>
                            <div class="flex gap-2 mb-2">
                                <button type="button" class="btn-cofina-outline text-xs py-1 px-2 flex-1"
                                    onclick="clearSignature('installateur')">
                                    Effacer
                                </button>
                                <button type="button" class="btn-cofina text-xs py-1 px-2 flex-1"
                                    onclick="saveSignature('installateur')">
                                    Sauvegarder
                                </button>
                            </div>
                            <input type="hidden" name="signature_installateur" id="signatureInstallateur">
                            <div class="text-center">
                                <span class="text-sm text-gray-500">Signature Installateur IT</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION VÉRIFICATION - SEULEMENT POUR SUPER ADMIN -->
                @if(auth()->check() && strtolower(auth()->user()->role) === 'super_admin')
                <div class="border-3 border-green-600 p-6 rounded-lg bg-green-50">
                    <div class="bg-green-600 text-white px-4 py-3 -mx-6 -mt-6 mb-6 rounded-t-lg">
                        <h3 class="text-xl font-bold">VÉRIFICATION</h3>
                        <div class="mt-2">
                            Date :
                            <input type="date" name="date_verification" value="{{ date('Y-m-d') }}"
                                class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold" required>
                        </div>
                    </div>

                    <!-- Vérification -->
                    <div class="mb-6">
                        <h4 class="font-bold text-lg mb-3 text-green-800 border-b-2 border-green-300 pb-2">
                            ☑️ Vérification
                        </h4>
                        <div class="space-y-3 ml-4">
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[verif_logiciels_installes]" value="1" required
                                    class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-3 font-semibold">Logiciels installés</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[verif_messagerie]" value="1" required
                                    class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-3 font-semibold">Messagerie</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[verif_sauvegarde]" value="1" required
                                    class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-3 font-semibold">Authentification de la sauvegarde des données par l'IT
                                    et l'utilisateur</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[verif_integration_ad]" value="1" required
                                    class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-3 font-semibold">Intégration dans l'AD</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[verif_systeme_licence]" value="1" required
                                    class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-3 font-semibold">Système installé et licence</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[verif_restauration]" value="1" required
                                    class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-3 font-semibold">Restauration des données et vérification de
                                    l'effectivité des données sur la machine réinstallée de l'utilisateur</span>
                            </label>
                        </div>
                    </div>

                    <!-- Autres -->
                    <div class="mb-6">
                        <h4 class="font-bold text-lg mb-3 text-green-800 border-b-2 border-green-300 pb-2">
                            ☑️ Autres
                        </h4>
                        <div class="space-y-3 ml-4">
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[verif_fiche_mouvement]" value="1" required
                                    class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-3 font-semibold">Remplir la fiche de mouvement</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[verif_restriction_web]" value="1"
                                    class="h-4 w-4 text-green-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-3 text-sm">Restriction des accès web (config Kaspersky)</span>
                            </label>
                            <label class="flex items-start">
                                <input type="checkbox" name="checklist[verif_validation_installation]" value="1"
                                    required class="h-5 w-5 text-green-600 rounded mt-1 flex-shrink-0">
                                <span class="ml-3 font-semibold">Validation de l'installation</span>
                            </label>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Observations finales -->
                <div class="mt-6">
                    <label for="observations" class="block font-bold text-cofina-red mb-2 text-lg">
                        💬 Observations / Remarques (optionnel)
                    </label>
                    <textarea name="observations" id="observations" rows="4"
                        class="w-full px-4 py-3 border-2 border-cofina-gray rounded-lg focus:border-cofina-red"
                        placeholder="Remarques particulières, problèmes rencontrés, points d'attention..."></textarea>
                </div>
            </div>
        </form>

<!-- ÉTAPE 2: Affectation Simple -->
<!-- ÉTAPE 2: Affectation Simple -->
<form id="affectation-step-form" class="step-form hidden" data-step="2">
    <input type="hidden" name="form_type" value="affectation_simple">
    <input type="hidden" name="transition_type" value="stock_to_parc">

    <div class="card-cofina bg-white border-2 border-cofina-blue">
        <div class="bg-cofina-blue text-white p-4 -mx-6 -mt-6 mb-6 rounded-t-lg">
            <h2 class="text-xl font-bold text-center">📦 → 👨‍💼 Affectation Simple</h2>
        </div>

        <div class="space-y-6">
            <!-- Informations de l'équipement -->
            <div class="bg-blue-50 p-4 rounded-lg border-2 border-blue-200">
                <h4 class="font-bold text-blue-800 mb-2">Équipement à affecter</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- ✅ NOM DE L'ÉQUIPEMENT - À SAISIR -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">
                            <span class="text-red-500">*</span> Nom de l'équipement:
                        </label>
                        <input type="text" name="equipment_name" 
                            placeholder="Ex: PC-DIRECTION-01"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-cofina-blue" 
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Modèle:</label>
                        <input type="text" value="{{ $equipment->modele ?? 'Non spécifié' }}"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">N° Série:</label>
                        <input type="text" value="{{ $equipment->numero_serie }}"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-gray-50" readonly>
                    </div>
                </div>
            </div>

            <!-- ... reste du formulaire inchangé ... -->

            <!-- Section d'affectation -->
            <div class="bg-green-50 p-4 rounded-lg border-2 border-green-200">
                <h4 class="font-bold text-green-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Information du destinataire
                </h4>

                <!-- Utilisateur * - Input texte au lieu de select -->
              <!-- Utilisateur - Nom et Prénom séparés -->
<div class="mb-4 grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold mb-2">
            <span class="text-red-500">*</span> Nom
        </label>
        <input type="text" name="utilisateur_nom" placeholder="Saisir le nom"
            class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-cofina-red" 
            required
            value="{{ old('utilisateur_nom') }}"
            autocomplete="off">
    </div>
    <div>
        <label class="block text-sm font-semibold mb-2">
            <span class="text-red-500">*</span> Prénom
        </label>
        <input type="text" name="utilisateur_prenom" placeholder="Saisir le prénom"
            class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-cofina-red" 
            required
            value="{{ old('utilisateur_prenom') }}"
            autocomplete="off">
    </div>
</div>

                <!-- Département * -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">
                        <span class="text-red-500">*</span> Département
                    </label>
                    <select name="department" class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                        <option value="">-- Sélectionner un département --</option>
                        <option value="IT">IT</option>
                        <option value="RH">Ressources Humaines</option>
                        <option value="Comptabilité">Comptabilité</option>
                        <option value="Finance">Finance</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Ventes">Ventes</option>
                        <option value="Direction">Direction</option>
                        <option value="Operations">Opérations</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Administratif">Administratif</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>

                <!-- Poste * -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">
                        <span class="text-red-500">*</span> Poste
                    </label>
                    <select name="position" class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                        <option value="">-- Sélectionner un poste --</option>
                        <option value="Directeur">Directeur</option>
                        <option value="Manager">Manager</option>
                        <option value="Chef de Projet">Chef de Projet</option>
                        <option value="Technicien">Technicien</option>
                        <option value="Développeur">Développeur</option>
                        <option value="Analyste">Analyste</option>
                        <option value="Consultant">Consultant</option>
                        <option value="Administrateur">Administrateur</option>
                        <option value="Assistant">Assistant</option>
                        <option value="Agent">Agent</option>
                        <option value="Stagiaire">Stagiaire</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>

                <!-- Date d'affectation * -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">
                        <span class="text-red-500">*</span> Date d'affectation
                    </label>
                    <input type="date" name="affectation_date" value="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                </div>

                <!-- Raison d'affectation -->
                <div>
                    <label class="block text-sm font-semibold mb-2">Raison d'affectation</label>
                    <select name="affectation_reason" class="w-full px-3 py-2 border-2 border-gray-300 rounded mb-2">
                        <option value="">-- Sélectionner une raison --</option>
                        <option value="Nouvelle embauche">Nouvelle embauche</option>
                        <option value="Remplacement d'équipement">Remplacement d'équipement</option>
                        <option value="Changement de poste">Changement de poste</option>
                        <option value="Besoins opérationnels">Besoins opérationnels</option>
                        <option value="Mise à niveau">Mise à niveau</option>
                        <option value="Autre">Autre</option>
                    </select>
                    <textarea name="affectation_reason_detail" rows="2"
                        class="w-full px-3 py-2 border-2 border-gray-300 rounded"
                        placeholder="Détails supplémentaires (optionnel)..."></textarea>
                </div>
            </div>

            <!-- Informations complémentaires -->
            <div class="bg-yellow-50 p-4 rounded-lg border-2 border-yellow-200">
                <h4 class="font-bold text-yellow-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Informations complémentaires
                </h4>

                <!-- Localisation -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Localisation</label>
                    <input type="text" name="localisation" placeholder="Ex: Bureau 201, Agence Dakar..."
                        class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                </div>

                <!-- Numéro de téléphone -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Numéro de téléphone</label>
                    <input type="tel" name="telephone" placeholder="Ex: +221 77 123 45 67"
                        class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold mb-2">Email</label>
                    <input type="email" name="email" placeholder="Ex: utilisateur@entreprise.com"
                        class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                </div>
            </div>

            <!-- Responsable -->
           <!-- EXPÉDITEUR (Agent IT) -->
<div class="border-2 border-blue-300 rounded-lg p-6 bg-blue-50">
    <h3 class="text-lg font-bold text-blue-800 mb-4 text-center border-b-2 border-blue-300 pb-2">
        EXPÉDITEUR
    </h3>
    <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Nom :</label>
                <input type="text" name="expediteur_nom" value="{{ auth()->user()->name ?? '' }}"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Prénom :</label>
                <input type="text" name="expediteur_prenom" value="{{ auth()->user()->prenom ?? '' }}"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Fonction :</label>
            <input type="text" name="expediteur_fonction" value="AGENT IT"
                class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" required>
        </div>
    </div>
</div>
                
                <!-- Date de validation -->
                <div class="mt-4">
                    <label class="block text-sm font-semibold text-gray-600 mb-1">Date de validation:</label>
                    <input type="date" name="validation_date" value="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                </div>
            </div>
        </div>
    </div>
</form>

        <!-- ÉTAPE 3: Fiche de Mouvement -->
        <form id="mouvement-step-form" class="step-form hidden" data-step="3">
            <input type="hidden" name="form_type" value="mouvement">
            <input type="hidden" name="transition_type" value="stock_to_parc">

            <div class="card-cofina bg-white border-2 border-cofina-red">
                <!-- En-tête officielle -->
                <div class="bg-cofina-red text-white p-6 -mx-6 -mt-6 mb-6 rounded-t-lg">
                    <h1 class="text-2xl font-bold text-center">COFINA SENEGAL - IT</h1>
                    <h2 class="text-xl font-bold text-center mt-2">FICHE DE MOUVEMENT DE MATERIEL INFORMATIQUE</h2>
                    <div class="mt-4 text-center">
                        <label class="inline-block">
                            Date d'application :
                            <input type="date" name="date_application_mouvement" value="{{ date('Y-m-d') }}"
                                class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold" required>
                        </label>
                    </div>
                </div>

                <!-- Section EXPÉDITEUR et RÉCEPTIONNAIRE -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    
                   <!-- EXPÉDITEUR (Agent IT) -->
<div class="border-2 border-blue-300 rounded-lg p-6 bg-blue-50">
    <h3 class="text-lg font-bold text-blue-800 mb-4 text-center border-b-2 border-blue-300 pb-2">
        EXPÉDITEUR
    </h3>
    <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Nom :</label>
                <input type="text" name="expediteur_nom" value="{{ auth()->user()->name ?? '' }}"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Prénom :</label>
                <input type="text" name="expediteur_prenom" value="{{ auth()->user()->prenom ?? '' }}"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Fonction :</label>
            <input type="text" name="expediteur_fonction" value="AGENT IT"
                class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" required>
        </div>
    </div>
</div>

                    <!-- RÉCEPTIONNAIRE (Utilisateur final - Champs à saisir) -->
                 
<div class="border-2 border-green-300 rounded-lg p-6 bg-green-50">
    <h3 class="text-lg font-bold text-green-800 mb-4 text-center border-b-2 border-green-300 pb-2">
        RÉCEPTIONNAIRE
    </h3>
    <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Nom :</label>
                <input type="text" name="receptionnaire_nom" placeholder="Saisir le nom"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Prénom :</label>
                <input type="text" name="receptionnaire_prenom" placeholder="Saisir le prénom"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Fonction :</label>
            <input type="text" name="receptionnaire_fonction" placeholder="Saisir la fonction"
                class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
        </div>
    </div>
</div>
                </div>

                <!-- Section détails du mouvement -->
                <div class="border-2 border-gray-300 rounded-lg p-6 bg-gray-50 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">TYPE DE MATERIEL</label>
                            <input type="text" value="{{ $equipment->type ?? 'N/A' }}"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">RÉFÉRENCE</label>
                            <input type="text" value="{{ $equipment->numero_serie ?? 'N/A' }}"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">LIEU DE DÉPART</label>
                            <input type="text" name="lieu_depart" placeholder="Saisir le lieu de départ"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                        </div>
                        <div>
    <label class="block text-sm font-semibold mb-1">DESTINATION *</label>
    <input type="text" name="destination" 
           value="{{ $formData['agence_nom'] ?? $formData['destination'] ?? $mouvementData['destination'] ?? '' }}"
           class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
</div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">MOTIF</label>
                            <input type="text" name="motif" placeholder="Saisir le motif"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                        </div>
                    </div>
                </div>

                <!-- Section signatures fonctionnelles -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                    <!-- Signature expéditeur -->
                    <div class="border-2 border-blue-300 rounded-lg p-6 bg-blue-50">
                        <h4 class="font-bold text-blue-800 mb-3 text-center">Signature de l'expéditeur</h4>
                        <div class="mb-3">
                            <label class="block text-sm font-semibold mb-1">Date :</label>
                            <input type="date" name="date_expediteur" value="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                        </div>
                        <div class="signature-container">
                            <div class="signature-pad-container mb-3">
                                <canvas class="signature-pad border-2 border-gray-300 rounded bg-white w-full h-32"
                                    id="signatureCanvasExpediteur"></canvas>
                            </div>
                            <div class="flex gap-2 mb-2">
                                <button type="button" class="btn-cofina-outline text-xs py-1 px-2 flex-1"
                                    onclick="clearSignature('expediteur')">
                                    Effacer
                                </button>
                                <button type="button" class="btn-cofina text-xs py-1 px-2 flex-1"
                                    onclick="saveSignature('expediteur')">
                                    Sauvegarder
                                </button>
                            </div>
                            <input type="hidden" name="signature_expediteur" id="signatureExpediteur">
                            <div class="text-center">
                                <span class="text-sm text-gray-500">Signature Agent IT</span>
                            </div>
                        </div>
                    </div>

                    <!-- Signature réceptionnaire - SEULEMENT POUR SUPER ADMIN -->
                    @if(auth()->check() && strtolower(auth()->user()->role) === 'super_admin')
                    <div class="border-2 border-green-300 rounded-lg p-6 bg-green-50">
                        <h4 class="font-bold text-green-800 mb-3 text-center">Signature du réceptionnaire</h4>
                        <div class="mb-3">
                            <label class="block text-sm font-semibold mb-1">Date :</label>
                            <input type="date" name="date_receptionnaire" value="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                        </div>
                        <div class="signature-container">
                            <div class="signature-pad-container mb-3">
                                <canvas class="signature-pad border-2 border-gray-300 rounded bg-white w-full h-32"
                                    id="signatureCanvasReceptionnaire"></canvas>
                            </div>
                            <div class="flex gap-2 mb-2">
                                <button type="button" class="btn-cofina-outline text-xs py-1 px-2 flex-1"
                                    onclick="clearSignature('receptionnaire')">
                                    Effacer
                                </button>
                                <button type="button" class="btn-cofina text-xs py-1 px-2 flex-1"
                                    onclick="saveSignature('receptionnaire')">
                                    Sauvegarder
                                </button>
                            </div>
                            <input type="hidden" name="signature_receptionnaire" id="signatureReceptionnaire">
                            <div class="text-center">
                                <span class="text-sm text-gray-500">Signature Utilisateur</span>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Message pour non-super admin -->
                    <div class="border-2 border-gray-200 rounded-lg p-6 bg-gray-50">
                        <h4 class="font-bold text-gray-600 mb-3 text-center">Signature du réceptionnaire</h4>
                        <div class="text-center py-8">
                            <div class="text-yellow-500 mb-3">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-600 font-semibold">
                                Cette section est réservée aux Super Administrateurs
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Seul un Super Admin peut compléter la signature du réceptionnaire
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- NOTA -->
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                    <p class="text-sm font-semibold text-yellow-800">
                        <strong>NOTA :</strong> Tout mouvement de matériel informatique nécessite le remplissage de
                        cette fiche par l'expéditeur et le réceptionnaire qui doivent en garder une copie.
                    </p>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Templates pour les autres transitions via includes -->
    <div class="hidden">
        @include('transitions.partials.parc-to-maintenance')
        @include('transitions.partials.parc-to-hors-service')
        @include('transitions.partials.parc-to-perdu')
        @include('transitions.partials.stock-to-hors-service')
        @include('transitions.partials.maintenance-to-stock')
        @include('transitions.partials.maintenance-to-hors-service')
        @include('transitions.partials.parc-to-stock-decele')
    </div>

    <!-- Bouton retour -->
    <div class="mt-8 pt-6 border-t border-cofina-gray text-left">
        <a href="{{ route('equipment.show', $equipment) }}" class="btn-cofina-outline inline-flex items-center text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour à la fiche
        </a>
    </div>
</div>

<style>
.transition-card {
    @apply p-0 border border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover: border-cofina-red hover:shadow-sm bg-white;
}

.transition-card.active {
    @apply border-2 border-cofina-red bg-red-50;
}

.step-form {
    @apply p-0 border-0 rounded-lg bg-white;
}

.step-form.hidden {
    display: none;
}

.step-form.active {
    display: block;
}

.animate-fadeIn {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.signature-pad {
    touch-action: none;
}

/* Style pour les étapes */
.step-number {
    @apply w-10 h-10 rounded-full flex items-center justify-center font-bold border-4 border-white shadow-md;
}

.step-number.active {
    @apply bg-cofina-red text-white;
}

.step-number.completed {
    @apply bg-green-500 text-white;
}

.step-number.pending {
    @apply bg-gray-300 text-gray-700;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>

// Variables globales

const isSuperAdmin = @json(auth()->check() && strtolower(auth()->user()->role) === 'super_admin');

let signaturePads = {};

let activeCard = null;

let currentStep = 1;

let formData = {

    installation: {},

    affectation_simple: {},

    mouvement: {}

};



document.addEventListener('DOMContentLoaded', function() {

    const cards = document.querySelectorAll('.transition-card');

    const transitionContainer = document.getElementById('transition-form-container');

    const threeStepContainer = document.getElementById('three-step-flow-container');



    // ✅ AJOUT: Event delegation pour capturer le nom de l'agence (NOUVEAU CODE)

    document.addEventListener('change', function(e) {

        // Vérifier si c'est le select d'agence qui a changé

        if (e.target && e.target.name === 'agency_id') {

            const agenceNomHidden = document.getElementById('agence_nom_hidden');

            const selectedOption = e.target.options[e.target.selectedIndex];

            

            if (agenceNomHidden && selectedOption.value) {

                agenceNomHidden.value = selectedOption.textContent.trim();

                console.log('✅ Agence capturée:', selectedOption.textContent.trim());

            } else if (agenceNomHidden) {

                agenceNomHidden.value = '';

            }

        }

    });



    // ✅ ANCIEN CODE CONSERVÉ: Tentative de capture directe (gardé pour compatibilité)

    const agencySelect = document.getElementById('agency_select');

    const agenceNomHidden = document.getElementById('agence_nom_hidden');

    

    if (agencySelect && agenceNomHidden) {

        agencySelect.addEventListener('change', function() {

            const selectedOption = this.options[this.selectedIndex];

            if (selectedOption.value) {

                agenceNomHidden.value = selectedOption.textContent.trim();

            } else {

                agenceNomHidden.value = '';

            }

        });

    }



    // ✅ ANCIEN CODE CONSERVÉ: Gestion des cartes de transition

    cards.forEach(card => {

        card.addEventListener('click', function() {

            const target = this.dataset.target;

            

            // Retirer la classe active de toutes les cartes

            cards.forEach(c => c.classList.remove('active'));

            

            // Ajouter la classe active à la carte cliquée

            this.classList.add('active');

            activeCard = this;

            

            // Vider les conteneurs

            transitionContainer.innerHTML = '';

            transitionContainer.classList.add('hidden');

            threeStepContainer.innerHTML = '';

            threeStepContainer.classList.add('hidden');

            

            // Gestion selon le type de transition

                     if (target === 'stock-to-parc') {
                startThreeStepFlow();
 
            } else if (target === 'parc-to-stock-decele') {
                // Vider aussi le conteneur décélé au cas où
                const deceleContainer = document.getElementById('three-step-decele-container');
                if (deceleContainer) {
                    deceleContainer.innerHTML = '';
                    deceleContainer.classList.add('hidden');
                }
                startDeceleFlow();
 
            } else {
                showSimpleTransitionForm(target);
            }

        });

    });

});



function startThreeStepFlow() {

    const threeStepContainer = document.getElementById('three-step-flow-container');

    const formsContainer = document.getElementById('forms-container');

    

    // Afficher le conteneur

    threeStepContainer.classList.remove('hidden');

    threeStepContainer.innerHTML = `

        <!-- Barre de progression -->

        <div class="mb-8">

            <div class="flex justify-between items-center mb-4">

                <div class="w-1/4">

                    <div class="flex flex-col items-center">

                        <div class="step-number active">

                            1

                        </div>

                        <span class="mt-2 text-sm font-semibold">Fiche d'Installation</span>

                    </div>

                </div>

                <div class="w-1/4">

                    <div class="flex flex-col items-center">

                        <div class="step-number pending">

                            2

                        </div>

                        <span class="mt-2 text-sm text-gray-600">Affectation Simple</span>

                    </div>

                </div>

                <div class="w-1/4">

                    <div class="flex flex-col items-center">

                        <div class="step-number pending">

                            3

                        </div>

                        <span class="mt-2 text-sm text-gray-600">Fiche de Mouvement</span>

                    </div>

                </div>

                <div class="w-1/4">

                    <div class="flex flex-col items-center">

                        <div class="step-number pending">

                            4

                        </div>

                        <span class="mt-2 text-sm text-gray-600">Validation</span>

                    </div>

                </div>

            </div>

            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">

                <div class="h-full bg-cofina-red w-1/4 transition-all duration-500" id="progress-bar"></div>

            </div>

        </div>



        <!-- Conteneur des formulaires -->

        <div id="forms-container"></div>



        <!-- Navigation entre les étapes -->

        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between" id="navigation-buttons">

            <button type="button" onclick="previousStep()" class="btn-cofina-outline px-6 py-3">

                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />

                </svg>

                Étape précédente

            </button>

            

            <div class="flex gap-4">

                <button type="button" onclick="saveDraft()" class="btn-cofina-outline px-6 py-3">

                    💾 Sauvegarder brouillon

                </button>

                <button type="button" onclick="nextStep()" class="btn-cofina px-6 py-3">

                    Étape suivante

                    <svg class="w-5 h-5 ml-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />

                    </svg>

                </button>

            </div>

        </div>



        <!-- Bouton de soumission final -->

        <div class="mt-8 pt-6 border-t border-gray-200 text-center hidden" id="final-submit">

            <button type="button" onclick="submitAllForms()" class="btn-cofina-success px-8 py-4 text-lg font-bold">

                ✅ SOUMETTRE POUR APPROBATION

            </button>

            <p class="text-sm text-gray-600 mt-2">

                Toutes les informations seront soumises pour approbation

            </p>

        </div>

    `;

    

    // Initialiser l'étape 1

    currentStep = 1;

    showStep(currentStep);

    

    // Scroll vers le conteneur

    threeStepContainer.scrollIntoView({ behavior: 'smooth' });

}



function showStep(stepNumber) {

    const formsContainer = document.getElementById('forms-container');

    const navigationButtons = document.getElementById('navigation-buttons');

    const finalSubmit = document.getElementById('final-submit');

    

    // Vider le conteneur

    formsContainer.innerHTML = '';

    

    // Afficher le formulaire correspondant

    let formTemplate;

    switch(stepNumber) {

        case 1:

            formTemplate = document.getElementById('installation-step-form').cloneNode(true);

            break;

        case 2:

            formTemplate = document.getElementById('affectation-step-form').cloneNode(true);

            break;

        case 3:

            formTemplate = document.getElementById('mouvement-step-form').cloneNode(true);

            break;

    }

    

// Si étape 3 (mouvement), pré-remplir avec les données de l'affectation

    if (stepNumber === 3 && formData.affectation_simple) {

        setTimeout(() => {

            const form = document.querySelector('#forms-container form');

            if (form) {

                // Pré-remplir le réceptionnaire avec les données de l'affectation

                const nomField = form.querySelector('[name="receptionnaire_nom"]');

                const prenomField = form.querySelector('[name="receptionnaire_prenom"]');

                const fonctionField = form.querySelector('[name="receptionnaire_fonction"]');

                

                if (nomField) nomField.value = formData.affectation_simple.utilisateur_nom || '';

                if (prenomField) prenomField.value = formData.affectation_simple.utilisateur_prenom || '';

                if (fonctionField) fonctionField.value = formData.affectation_simple.position || '';

                const destinationField = form.querySelector('[name="destination"]');

            if (destinationField) {

                // Priorité : agence_nom de l'installation

                destinationField.value = formData.installation.agence_nom || '';

            }

            }

        }, 100);

    }



    formTemplate.classList.remove('hidden');

    formsContainer.appendChild(formTemplate);

    

    // Initialiser les signatures si nécessaire

    if (stepNumber === 1) {

        initializeSignaturePads('installation');

    } else if (stepNumber === 3) {

        initializeSignaturePads('mouvement');

    }

    

    // Remplir avec les données sauvegardées

    fillFormWithSavedData(stepNumber);

    

    // Mettre à jour la progression

    updateProgressBar(stepNumber);

    updateStepNumbers(stepNumber);

    

    // Gérer la visibilité des boutons

    if (stepNumber === 3) {

        navigationButtons.classList.add('hidden');

        finalSubmit.classList.remove('hidden');

    } else {

        navigationButtons.classList.remove('hidden');

        finalSubmit.classList.add('hidden');

    }

}



function nextStep() {

    // Sauvegarder les données de l'étape actuelle

    if (!saveCurrentStepData()) {

        alert('Veuillez remplir tous les champs obligatoires');

        return;

    }

    

    // Passer à l'étape suivante

    if (currentStep < 3) {

        currentStep++;

        showStep(currentStep);

        

        // Scroll vers le haut du formulaire

        document.getElementById('forms-container').scrollIntoView({ behavior: 'smooth' });

    }

}



function previousStep() {

    if (currentStep > 1) {

        // Sauvegarder les données de l'étape actuelle

        saveCurrentStepData();

        

        currentStep--;

        showStep(currentStep);

        

        // Scroll vers le haut du formulaire

        document.getElementById('forms-container').scrollIntoView({ behavior: 'smooth' });

    }

}



function saveCurrentStepData() {

    const form = document.querySelector('#forms-container form');

    if (!form) return false;

    

    const formType = form.querySelector('input[name="form_type"]').value;

    const data = {};

    

    // Collecter les données du formulaire

    const formElements = form.elements;

    

    for (let element of formElements) {

        if (element.name && element.type !== 'button' && element.type !== 'submit') {

            const name = element.name;

            

            if (element.type === 'checkbox') {

                const value = element.checked ? '1' : '0';

                

                if (name.includes('[') && name.includes(']')) {

                    const match = name.match(/^(\w+)\[(.+)\]$/);

                    if (match) {

                        const arrayName = match[1];

                        const key = match[2];

                        

                        if (!data[arrayName]) {

                            data[arrayName] = {};

                        }

                        data[arrayName][key] = value;

                    }

                } else {

                    data[name] = value;

                }

            } else {

                data[name] = element.value;

            }

        }

    }

    

    // Vérifier les champs requis

    const requiredFields = form.querySelectorAll('[required]');

    let isValid = true;

    

    requiredFields.forEach(field => {

        if (!field.value.trim() && field.type !== 'checkbox') {

            isValid = false;

            field.style.borderColor = 'red';

        } else {

            field.style.borderColor = '';

        }

        

        if (field.type === 'checkbox' && field.required && !field.checked) {

            isValid = false;

            field.style.outline = '2px solid red';

        } else if (field.type === 'checkbox') {

            field.style.outline = '';

        }

    });

    

    if (!isValid) {

        return false;

    }

    

    // Sauvegarder dans formData

    formData[formType] = data;

    

    console.log('Données sauvegardées pour', formType, ':', data);

    

    return true;

}



function submitAllForms() {

    // Sauvegarder l'étape actuelle

    if (!saveCurrentStepData()) {

        alert('Veuillez remplir tous les champs obligatoires de l\'étape actuelle');

        return;

    }

    

    // Vérifier que toutes les étapes ont été remplies

    if (!formData.installation || Object.keys(formData.installation).length === 0) {

        alert('Veuillez remplir la fiche d\'installation');

        showStep(1);

        return;

    }

    

    if (!formData.affectation_simple || Object.keys(formData.affectation_simple).length === 0) {

        alert('Veuillez remplir l\'affectation simple');

        showStep(2);

        return;

    }

    

    if (!formData.mouvement || Object.keys(formData.mouvement).length === 0) {

        alert('Veuillez remplir la fiche de mouvement');

        showStep(3);

        return;

    }

    

    // Vérifier les signatures

    if (!formData.mouvement.signature_expediteur) {

        alert('Veuillez sauvegarder la signature de l\'expéditeur dans la fiche de mouvement');

        showStep(3);

        return;

    }

    

    if (!formData.installation.signature_installateur) {

        alert('Veuillez sauvegarder la signature de l\'installateur dans la fiche d\'installation');

        showStep(1);

        return;

    }

    

    // Si Super Admin, vérifier les signatures supplémentaires

    if (isSuperAdmin) {

        if (!formData.installation.signature_verificateur) {

            alert('Veuillez sauvegarder la signature du vérificateur (Super Admin)');

            showStep(1);

            return;

        }

        

        if (!formData.installation.signature_utilisateur) {

            alert('Veuillez sauvegarder la signature de l\'utilisateur');

            showStep(1);

            return;

        }

        

        if (!formData.mouvement.signature_receptionnaire) {

            alert('Veuillez sauvegarder la signature du réceptionnaire');

            showStep(3);

            return;

        }

    }

    

    // Préparer les données pour l'envoi

    const allData = {

        ...formData,

        equipment_id: {{ $equipment->id }},

        transition_type: 'stock_to_parc',

        _token: document.querySelector('meta[name="csrf-token"]')?.content || ''

    };

    

    // Afficher un indicateur de chargement

    const submitBtn = document.querySelector('#final-submit .btn-cofina-success');

    const originalText = submitBtn.innerHTML;

    submitBtn.innerHTML = '⏳ Envoi en cours...';

    submitBtn.disabled = true;

    

    // Envoyer les données au serveur

    fetch('{{ route("transitions.submitAll", $equipment) }}', {

        method: 'POST',

        headers: {

            'Content-Type': 'application/json',

            'Accept': 'application/json',

            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''

        },

        body: JSON.stringify(allData)

    })

    .then(response => response.json())

    .then(data => {

        if (data.success) {

            if (data.redirect_url) {

                window.location.href = data.redirect_url;

            } else {

                window.location.reload();

            }

        } else {

            alert(data.message || 'Erreur lors de la soumission');

            if (data.errors) {

                console.error('Validation errors:', data.errors);

            }

            submitBtn.innerHTML = originalText;

            submitBtn.disabled = false;

        }

    })

    .catch(error => {

        console.error('Error:', error);

        alert('Erreur de connexion au serveur');

        submitBtn.innerHTML = originalText;

        submitBtn.disabled = false;

    });

}



function fillFormWithSavedData(stepNumber) {

    let formType;

    switch(stepNumber) {

        case 1: formType = 'installation'; break;

        case 2: formType = 'affectation_simple'; break;

        case 3: formType = 'mouvement'; break;

    }

    

    const savedData = formData[formType];

    if (!savedData) return;

    

    const form = document.querySelector('#forms-container form');

    if (!form) return;

    

    Object.keys(savedData).forEach(key => {

        const field = form.querySelector(`[name="${key}"]`);

        if (field) {

            if (field.type === 'checkbox') {

                field.checked = savedData[key] === '1';

            } else {

                field.value = savedData[key];

            }

        }

    });

}



function updateProgressBar(step) {

    const progressBar = document.getElementById('progress-bar');

    if (progressBar) {

        const width = (step / 3) * 75;

        progressBar.style.width = `${width}%`;

    }

}



function updateStepNumbers(currentStep) {

    const stepNumbers = document.querySelectorAll('.step-number');

    const stepLabels = document.querySelectorAll('.step-number + span');

    

    stepNumbers.forEach((number, index) => {

        const stepIndex = index + 1;

        

        if (stepIndex < currentStep) {

            number.className = 'step-number completed';

        } else if (stepIndex === currentStep) {

            number.className = 'step-number active';

        } else {

            number.className = 'step-number pending';

        }

    });

    

    stepLabels.forEach((label, index) => {

        const stepIndex = index + 1;

        if (stepIndex <= currentStep) {

            label.classList.remove('text-gray-600');

            label.classList.add('font-semibold');

        } else {

            label.classList.add('text-gray-600');

            label.classList.remove('font-semibold');

        }

    });

}



function initializeSignaturePads(formType) {

    signaturePads = {};

    

    let canvasIds = [];

    

    if (formType === 'installation') {

        canvasIds = ['signatureCanvasInstallateur'];

        if (isSuperAdmin) {

            canvasIds.push('signatureCanvasUtilisateur', 'signatureCanvasVerificateur');

        }

    } else if (formType === 'mouvement') {

        canvasIds = ['signatureCanvasExpediteur'];

        if (isSuperAdmin) {

            canvasIds.push('signatureCanvasReceptionnaire');

        }

    }

    

    canvasIds.forEach((canvasId) => {

        const canvas = document.getElementById(canvasId);

        if (canvas) {

            const signaturePad = new SignaturePad(canvas, {

                backgroundColor: 'rgb(255, 255, 255)',

                penColor: 'rgb(0, 0, 0)',

                minWidth: 1,

                maxWidth: 3,

            });

            

            signaturePads[canvasId] = signaturePad;

            resizeCanvas(canvas);

            

            window.addEventListener('resize', () => resizeCanvas(canvas));

        }

    });

}



function resizeCanvas(canvas) {

    const ratio = Math.max(window.devicePixelRatio || 1, 1);

    canvas.width = canvas.offsetWidth * ratio;

    canvas.height = canvas.offsetHeight * ratio;

    canvas.getContext("2d").scale(ratio, ratio);

    

    const canvasId = canvas.id;

    if (signaturePads[canvasId]) {

        signaturePads[canvasId].clear();

    }

}



function clearSignature(type) {

    const canvasId = getCanvasId(type);

    if (signaturePads[canvasId]) {

        signaturePads[canvasId].clear();

        const signatureInput = document.getElementById(`signature${capitalizeFirst(type)}`);

        if (signatureInput) {

            signatureInput.value = '';

        }

    }

}



function saveSignature(type) {

    const canvasId = getCanvasId(type);

    if (signaturePads[canvasId] && !signaturePads[canvasId].isEmpty()) {

        const signatureData = signaturePads[canvasId].toDataURL('image/png');

        const signatureInput = document.getElementById(`signature${capitalizeFirst(type)}`);

        if (signatureInput) {

            signatureInput.value = signatureData;

            alert('Signature sauvegardée avec succès!');

        }

    } else {

        alert('Veuillez d\'abord signer dans la zone prévue.');

    }

}



function getCanvasId(type) {

    const canvasMap = {

        'expediteur': 'signatureCanvasExpediteur',

        'receptionnaire': 'signatureCanvasReceptionnaire',

        'installateur': 'signatureCanvasInstallateur',

        'utilisateur': 'signatureCanvasUtilisateur',

        'verificateur': 'signatureCanvasVerificateur'

    };

    return canvasMap[type] || '';

}



function capitalizeFirst(string) {

    return string.charAt(0).toUpperCase() + string.slice(1);

}



function saveDraft() {

    if (saveCurrentStepData()) {

        alert('Brouillon sauvegardé avec succès!');

    }

}



// Fonctions pour les autres transitions

function showSimpleTransitionForm(target) {

    const transitionContainer = document.getElementById('transition-form-container');

    

    const form = document.getElementById(target + '-form');

    if (form) {

        const clonedForm = form.cloneNode(true);

        clonedForm.classList.remove('hidden');

        clonedForm.style.display = 'block';

        clonedForm.classList.add('animate-fadeIn');

        

        transitionContainer.innerHTML = '';

        transitionContainer.appendChild(clonedForm);

        transitionContainer.classList.remove('hidden');

        

        transitionContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });

    }

}



function closeSimpleForm() {

    const transitionContainer = document.getElementById('transition-form-container');

    const threeStepContainer = document.getElementById('three-step-flow-container');

    

    transitionContainer.innerHTML = '';

    transitionContainer.classList.add('hidden');

    threeStepContainer.innerHTML = '';

    threeStepContainer.classList.add('hidden');

    

    if (activeCard) {

        activeCard.classList.remove('active');

    }

}

document.querySelector('select[name="agency_id"]').addEventListener('change', function() {

    const selectedOption = this.options[this.selectedIndex];

    const agenceNom = selectedOption.textContent;

    document.getElementById('agence_nom_hidden').value = agenceNom;

});


// ══════════════════════════════════════════════════════════════════
//  FLUX PARC → STOCK DÉCÉLÉ
// ══════════════════════════════════════════════════════════════════
 
let signaturePadsDecele = {};
let currentStepDecele   = 1;
let formDataDecele = { retour: {}, deceleration: {}, mouvement_decele: {} };
 
function startDeceleFlow() {
    const container = document.getElementById('three-step-decele-container');
    if (!container) {
        console.error('❌ #three-step-decele-container introuvable. Avez-vous ajouté la Correction 1 ?');
        return;
    }
 
    container.classList.remove('hidden');
    container.innerHTML = `
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <div class="w-1/3 flex flex-col items-center">
                    <div class="step-number-decele active" id="decele-step-1">1</div>
                    <span class="mt-2 text-sm font-semibold">Fiche de Retour</span>
                </div>
                <div class="w-1/3 flex flex-col items-center">
                    <div class="step-number-decele pending" id="decele-step-2">2</div>
                    <span class="mt-2 text-sm text-gray-600">Fiche Décélération</span>
                </div>
                <div class="w-1/3 flex flex-col items-center">
                    <div class="step-number-decele pending" id="decele-step-3">3</div>
                    <span class="mt-2 text-sm text-gray-600">Fiche de Mouvement</span>
                </div>
            </div>
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-orange-500 transition-all duration-500"
                     id="progress-bar-decele" style="width:33%"></div>
            </div>
        </div>
        <div id="forms-container-decele"></div>
        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between" id="nav-decele">
            <button type="button" onclick="previousStepDecele()" class="btn-cofina-outline px-6 py-3">
                ← Étape précédente
            </button>
            <div class="flex gap-4">
                <button type="button" onclick="saveDraftDecele()" class="btn-cofina-outline px-6 py-3">
                    💾 Brouillon
                </button>
                <button type="button" onclick="nextStepDecele()" class="btn-cofina px-6 py-3">
                    Étape suivante →
                </button>
            </div>
        </div>
        <div class="mt-8 pt-6 border-t border-gray-200 text-center hidden" id="final-submit-decele">
            <button type="button" onclick="submitAllDecele()"
                class="btn-cofina-success px-8 py-4 text-lg font-bold">
                ✅ SOUMETTRE POUR APPROBATION
            </button>
            <p class="text-sm text-gray-600 mt-2">
                L'équipement sera transféré en stock décélé après validation
            </p>
        </div>`;
 
    currentStepDecele = 1;
    formDataDecele = { retour: {}, deceleration: {}, mouvement_decele: {} };
    showStepDecele(1);
    container.scrollIntoView({ behavior: 'smooth' });
}
 
function showStepDecele(step) {
    const fc  = document.getElementById('forms-container-decele');
    const nav = document.getElementById('nav-decele');
    const fin = document.getElementById('final-submit-decele');
    fc.innerHTML = '';
 
    const ids = {
        1: 'retour-step-form-decele',
        2: 'deceleration-step-form',
        3: 'mouvement-decele-step-form'
    };
 
    const tmpl = document.getElementById(ids[step]);
    if (!tmpl) {
        fc.innerHTML = `<div class="p-4 bg-red-50 border-2 border-red-400 rounded text-red-700 font-bold">
            ❌ Template "${ids[step]}" introuvable.<br>
            Vérifiez que <code>@include('transitions.partials.parc-to-stock-decele')</code>
            est présent dans le bloc &lt;div class="hidden"&gt;.
        </div>`;
        return;
    }
 
    const clone = tmpl.cloneNode(true);
    clone.classList.remove('hidden');
    clone.removeAttribute('id');
    fc.appendChild(clone);
 
    // Pré-remplissage étape 3
    if (step === 3) {
        setTimeout(() => {
            const form = fc.querySelector('form'); if (!form) return;
            const r = formDataDecele.retour       || {};
            const d = formDataDecele.deceleration || {};
            const sv = (n, v) => { const el = form.querySelector(`[name="${n}"]`); if (el) el.value = v; };
            sv('expediteur_decele_nom',      r.detenteur_nom        || '');
            sv('expediteur_decele_prenom',   r.detenteur_prenom     || '');
            sv('expediteur_decele_fonction', r.detenteur_poste      || '');
            sv('lieu_depart_decele',         r.localisation_actuelle|| '');
            sv('destination_decele',         d.localisation_physique|| 'STOCK DÉCÉLÉ');
        }, 100);
    }
 
    if (step === 1) initSignaturesDecele('retour');
    if (step === 3) initSignaturesDecele('mouvement_decele');
 
    // Restaurer données sauvegardées
    const typeMap = { 1:'retour', 2:'deceleration', 3:'mouvement_decele' };
    const saved = formDataDecele[typeMap[step]];
    if (saved) {
        const form = fc.querySelector('form');
        if (form) Object.keys(saved).forEach(k => {
            const el = form.querySelector(`[name="${k}"]`); if (!el) return;
            el.type === 'checkbox' ? el.checked = saved[k] === '1' : el.value = saved[k];
        });
    }
 
    updateProgressDecele(step);
    updateStepNumbersDecele(step);
 
    if (step === 3) { nav.classList.add('hidden');    fin.classList.remove('hidden'); }
    else            { nav.classList.remove('hidden'); fin.classList.add('hidden'); }
}
 
function nextStepDecele() {
    if (!saveCurrentStepDeceleData()) { alert('Veuillez remplir tous les champs obligatoires.'); return; }
    if (currentStepDecele < 3) {
        currentStepDecele++;
        showStepDecele(currentStepDecele);
        document.getElementById('forms-container-decele').scrollIntoView({ behavior: 'smooth' });
    }
}
 
function previousStepDecele() {
    if (currentStepDecele > 1) {
        saveCurrentStepDeceleData();
        currentStepDecele--;
        showStepDecele(currentStepDecele);
        document.getElementById('forms-container-decele').scrollIntoView({ behavior: 'smooth' });
    }
}
 
function saveCurrentStepDeceleData() {
    const form = document.querySelector('#forms-container-decele form');
    if (!form) return false;
    const formType = form.querySelector('input[name="form_type"]')?.value;
    if (!formType) return false;
 
    const data = {};
    for (const el of form.elements) {
        if (!el.name || el.type === 'button' || el.type === 'submit') continue;
        if (el.type === 'checkbox') {
            const m = el.name.match(/^(\w+)\[(.+)\]$/);
            if (m) { if (!data[m[1]]) data[m[1]] = {}; data[m[1]][m[2]] = el.checked ? '1' : '0'; }
            else data[el.name] = el.checked ? '1' : '0';
        } else {
            data[el.name] = el.value;
        }
    }
 
    let isValid = true;
    form.querySelectorAll('[required]').forEach(f => {
        if (f.type !== 'checkbox' && !f.value.trim()) { isValid = false; f.style.borderColor = 'red'; }
        else f.style.borderColor = '';
    });
    if (!isValid) return false;
 
    formDataDecele[formType] = data;
    return true;
}
 
function updateProgressDecele(step) {
    const b = document.getElementById('progress-bar-decele');
    if (b) b.style.width = `${(step / 3) * 100}%`;
}
 
function updateStepNumbersDecele(cur) {
    [1, 2, 3].forEach(i => {
        const el = document.getElementById(`decele-step-${i}`); if (!el) return;
        el.className = 'step-number-decele ' + (i < cur ? 'completed' : i === cur ? 'active' : 'pending');
    });
}
 
function initSignaturesDecele(formType) {
    signaturePadsDecele = {};
    const ids = formType === 'retour'
        ? ['signatureCanvasAgentRetour']
        : ['signatureCanvasExpediteurDecele', ...(isSuperAdmin ? ['signatureCanvasReceptionnaireDecele'] : [])];
 
    ids.forEach(id => {
        const c = document.querySelector(`#forms-container-decele #${id}`); if (!c) return;
        const r = Math.max(window.devicePixelRatio || 1, 1);
        c.width = c.offsetWidth * r; c.height = c.offsetHeight * r;
        c.getContext('2d').scale(r, r);
        signaturePadsDecele[id] = new SignaturePad(c, {
            backgroundColor: 'rgb(255,255,255)', penColor: 'rgb(0,0,0)', minWidth: 1, maxWidth: 3
        });
    });
}
 
const _dm = {
    agent_retour:          { c: 'signatureCanvasAgentRetour',          i: 'signatureAgentRetour' },
    expediteur_decele:     { c: 'signatureCanvasExpediteurDecele',     i: 'signatureExpediteurDecele' },
    receptionnaire_decele: { c: 'signatureCanvasReceptionnaireDecele', i: 'signatureReceptionnaireDecele' }
};
 
function clearSignatureDecele(type) {
    const m = _dm[type]; if (!m) return;
    const p = signaturePadsDecele[m.c]; if (p) p.clear();
    const inp = document.querySelector(`#forms-container-decele #${m.i}`);
    if (inp) inp.value = '';
}
 
function saveSignatureDecele(type) {
    const m = _dm[type]; if (!m) return;
    const p = signaturePadsDecele[m.c];
    if (p && !p.isEmpty()) {
        const inp = document.querySelector(`#forms-container-decele #${m.i}`);
        if (inp) { inp.value = p.toDataURL('image/png'); alert('Signature sauvegardée !'); }
    } else {
        alert('Veuillez signer dans la zone prévue.');
    }
}
 
function toggleDecelerationFields(value) {
    ['bon', 'reparable', 'irreparable'].forEach(v => {
        const el = document.querySelector(`#forms-container-decele #msg-${v}`);
        if (el) el.classList.toggle('hidden', v !== value);
    });
}
 
function saveDraftDecele() {
    if (saveCurrentStepDeceleData()) alert('Brouillon sauvegardé !');
}
 
function submitAllDecele() {
    if (!saveCurrentStepDeceleData()) { alert('Champs obligatoires manquants.'); return; }
    if (!formDataDecele.retour?.detenteur_nom)              { alert('Remplissez la fiche de retour (étape 1).');        showStepDecele(1); return; }
    if (!formDataDecele.deceleration?.etat_retour)          { alert('Remplissez la fiche de décélération (étape 2).'); showStepDecele(2); return; }
    if (!formDataDecele.mouvement_decele?.destination_decele){ alert('Remplissez la fiche de mouvement (étape 3).');    showStepDecele(3); return; }
    if (!formDataDecele.mouvement_decele?.signature_expediteur_decele) {
        alert('Veuillez sauvegarder la signature expéditeur (étape 3).');
        showStepDecele(3); return;
    }
 
    const allData = {
        ...formDataDecele,
        equipment_id:    {{ $equipment->id }},
        transition_type: 'parc_to_stock_decele',
        _token: document.querySelector('meta[name="csrf-token"]')?.content || ''
    };
 
    const btn = document.querySelector('#final-submit-decele button');
    const orig = btn.innerHTML;
    btn.innerHTML = '⏳ Envoi en cours...'; btn.disabled = true;
 
    fetch('{{ route("transitions.submitDecele", $equipment) }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': allData._token },
        body: JSON.stringify(allData)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { window.location.href = data.redirect_url || window.location.href; }
        else { alert(data.message || 'Erreur.'); btn.innerHTML = orig; btn.disabled = false; }
    })
    .catch(() => { alert('Erreur connexion.'); btn.innerHTML = orig; btn.disabled = false; });
}
 
// ══ Styles pour les indicateurs d'étapes décélé ══
const _styleDecele = document.createElement('style');
_styleDecele.textContent = `
    .step-number-decele {
        width:2.5rem; height:2.5rem; border-radius:9999px;
        display:flex; align-items:center; justify-content:center;
        font-weight:700; border:4px solid white; box-shadow:0 2px 4px rgba(0,0,0,.2);
    }
    .step-number-decele.active    { background:#f97316; color:white; }
    .step-number-decele.completed { background:#22c55e; color:white; }
    .step-number-decele.pending   { background:#d1d5db; color:#374151; }
`;
document.head.appendChild(_styleDecele);

</script>
@endsection