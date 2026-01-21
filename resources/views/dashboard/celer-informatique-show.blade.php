@extends('layouts.app')
@section('title', 'Détails Stock Celer')

@section('content')
<div class="bg-gray-50 min-h-screen py-6">
    <div class="container mx-auto px-4">
        <!-- En-tête avec actions -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Stock Celer - Informatique</h1>
                    <p class="text-gray-600">{{ $stock->equipment->marque }} {{ $stock->equipment->modele }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.celer-informatique') }}" class="btn-cofina-outline">
                        ↩️ Retour
                    </a>
                    <a href="{{ route('equipment.edit', $stock->equipment) }}" class="btn-cofina-outline bg-blue-50 border-blue-200 hover:bg-blue-100">
                        ✏️ Modifier
                    </a>
                    <a href="{{ route('equipment.transitions.show', $stock->equipment) }}" class="btn-cofina-primary">
                        🔄 Changer statut
                    </a>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Section gauche - Informations Stock et Equipment -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Carte Informations Équipement -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-red-600 mb-4 pb-2 border-b">📋 Informations Équipement</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">N° Série:</span>
                                <span class="font-bold">{{ $stock->numero_serie }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Type:</span>
                                <span class="font-medium">{{ $stock->equipment->type }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Marque:</span>
                                <span class="font-medium">{{ $stock->equipment->marque }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Modèle:</span>
                                <span class="font-medium">{{ $stock->equipment->modele }}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Catégorie:</span>
                                <span class="font-medium">{{ $stock->equipment->categorie->nom ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Prix:</span>
                                <span class="font-medium">{{ number_format($stock->equipment->prix, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">État:</span>
                                <span class="px-2 py-1 text-xs rounded-full font-medium capitalize bg-gray-100">
                                    {{ $stock->equipment->etat }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte Informations Stock -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-red-600 mb-4 pb-2 border-b"> Informations Stock</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Type Stock:</span>
                                <span class="font-bold">{{ $stock->type_stock }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Localisation:</span>
                                <span>{{ $stock->localisation_physique }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">État Stock:</span>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($stock->etat == 'disponible') bg-green-100 text-green-800
                                    @elseif($stock->etat == 'reserve') bg-yellow-100 text-yellow-800
                                    @elseif($stock->etat == 'en_transit') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $stock->etat }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Quantité:</span>
                                <span class="font-medium">{{ $stock->quantite }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Date Entrée:</span>
                                <span>{{ $stock->date_entree->format('d/m/Y') }}</span>
                            </div>
                            @if($stock->date_sortie)
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Date Sortie:</span>
                                <span class="text-red-600 font-medium">{{ $stock->date_sortie->format('d/m/Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Carte Informations Celer -->
                @if($stock->celer)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-red-600 mb-4 pb-2 border-b">🏢 Informations Celer</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            @if($stock->celer->numero_facture)
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">N° Facture:</span>
                                <span class="font-medium">{{ $stock->celer->numero_facture }}</span>
                            </div>
                            @endif
                            @if($stock->celer->date_acquisition)
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Date Acquisition:</span>
                                <span class="font-medium">{{ $stock->celer->date_acquisition->format('d/m/Y') }}</span>
                            </div>
                            @endif
                            @if($stock->celer->certificat_garantie)
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Certificat Garantie:</span>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-medium">
                                    Oui
                                </span>
                            </div>
                            @endif
                        </div>
                        <div class="space-y-3">
                            @if($stock->celer->emballage_origine)
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Emballage Origine:</span>
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 font-medium">
                                    Oui
                                </span>
                            </div>
                            @endif
                            @if($stock->celer->observations)
                            <div class="flex items-start">
                                <span class="w-40 text-gray-600">Observations:</span>
                                <span class="text-sm">{{ $stock->celer->observations }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Notes Stock -->
                @if($stock->observations)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-red-600 mb-4 pb-2 border-b">📝 Notes Stock</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-line">{{ $stock->observations }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Section droite - Actions -->
            <div class="space-y-6">
                <!-- Carte Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-red-600 mb-4 pb-2 border-b">⚡ Actions Rapides</h3>
                    <div class="space-y-3">
                        <a href="{{ route('equipment.show', $stock->equipment) }}" 
                           class="flex items-center justify-center w-full p-3 text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition">
                            <span class="mr-2">ℹ️</span> Voir Équipement Complet
                        </a>
                        <a href="{{ route('equipment.audit', $stock->equipment) }}" 
                           class="flex items-center justify-center w-full p-3 text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition">
                            <span class="mr-2">📋</span> Historique
                        </a>
                    </div>
                </div>

                <!-- Carte Dates -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-red-600 mb-4 pb-2 border-b">📅 Dates</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Créé le:</span>
                            <span class="text-sm">{{ $stock->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Modifié le:</span>
                            <span class="text-sm">{{ $stock->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Statut Badge -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-red-600 mb-4 pb-2 border-b">ℹ️ Statut</h3>
                    <div class="space-y-3">
                        <span class="block px-4 py-2 rounded-full text-sm font-bold bg-blue-100 text-blue-800 text-center">
                            Type Stock: {{ ucfirst($stock->type_stock) }}
                        </span>
                        @if($stock->date_sortie)
                            <span class="block px-4 py-2 rounded-full text-sm font-bold bg-red-100 text-red-800 text-center">
                                ⚠️ Sorti du stock
                            </span>
                        @else
                            <span class="block px-4 py-2 rounded-full text-sm font-bold bg-green-100 text-green-800 text-center">
                                ✅ En stock
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-cofina-primary {
        @apply bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition;
    }
    .btn-cofina-outline {
        @apply border border-gray-300 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition;
    }
</style>
@endpush