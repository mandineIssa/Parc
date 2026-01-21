@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Détails Équipement Réseau Celer</h1>
        <a href="{{ route('dashboard.celer-reseau') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Retour au dashboard
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b bg-indigo-50">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-700">Informations Équipement Réseau</h2>
                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                    Celer - Réseau
                </span>
            </div>
        </div>
        
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations stock -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Numéro de série</h3>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $stock->numero_serie }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Type stock</h3>
                        <p class="mt-1 text-gray-900">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                                {{ $stock->type_stock }}
                            </span>
                        </p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Localisation physique</h3>
                        <p class="mt-1 text-gray-900">{{ $stock->localisation_physique }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">État</h3>
                        @php
                            $etatClasses = [
                                'neuf' => 'bg-green-100 text-green-800',
                                'bon' => 'bg-blue-100 text-blue-800',
                                'moyen' => 'bg-yellow-100 text-yellow-800',
                                'mauvais' => 'bg-red-100 text-red-800'
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded-full text-sm font-medium {{ $etatClasses[$stock->etat] ?? 'bg-gray-100' }}">
                            {{ ucfirst($stock->etat) }}
                        </span>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Quantité</h3>
                        <p class="mt-1 text-gray-900">{{ $stock->quantite }} unité(s)</p>
                    </div>
                </div>
                
                <!-- Informations dates -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Date d'entrée</h3>
                        <p class="mt-1 text-gray-900">{{ $stock->date_entree ? $stock->date_entree->format('d/m/Y') : 'Non renseignée' }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Date de sortie</h3>
                        @if($stock->date_sortie)
                            <p class="mt-1 text-red-600 font-semibold">{{ $stock->date_sortie->format('d/m/Y') }}</p>
                        @else
                            <p class="mt-1 text-green-600 font-semibold">En stock</p>
                        @endif
                    </div>
                    
                    @if($stock->celer && $stock->celer->date_acquisition)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Date d'acquisition</h3>
                            <p class="mt-1 text-gray-900">{{ $stock->celer->date_acquisition->format('d/m/Y') }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Informations Celer -->
            @if($stock->celer)
            <div class="mt-8 pt-8 border-t">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Informations Celer</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Numéro de facture</h4>
                        <p class="mt-1 text-gray-900">{{ $stock->celer->numero_facture ?? 'Non renseigné' }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Certificat de garantie</h4>
                        @if($stock->celer->certificat_garantie)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">Disponible</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm">Non disponible</span>
                        @endif
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Emballage d'origine</h4>
                        @if($stock->celer->emballage_origine)
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">Oui</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm">Non</span>
                        @endif
                    </div>
                    
                    @if($stock->celer->caracteristiques_specifiques)
                    <div class="md:col-span-2">
                        <h4 class="text-sm font-medium text-gray-500">Caractéristiques spécifiques</h4>
                        <p class="mt-1 text-gray-900">{{ $stock->celer->caracteristiques_specifiques }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Informations équipement -->
            @if($stock->equipment)
            <div class="mt-8 pt-8 border-t">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Informations Équipement Réseau</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Type</h4>
                        <p class="mt-1 text-gray-900">{{ $stock->equipment->type }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Marque</h4>
                        <p class="mt-1 text-gray-900">{{ $stock->equipment->marque }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Modèle</h4>
                        <p class="mt-1 text-gray-900">{{ $stock->equipment->modele }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Catégorie</h4>
                        <p class="mt-1 text-gray-900">{{ $stock->equipment->categorie ?? 'Non spécifiée' }}</p>
                    </div>
                    
                    @if($stock->equipment->ports)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Nombre de ports</h4>
                        <p class="mt-1 text-gray-900">{{ $stock->equipment->ports }}</p>
                    </div>
                    @endif
                    
                    @if($stock->equipment->vitesse)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Vitesse</h4>
                        <p class="mt-1 text-gray-900">{{ $stock->equipment->vitesse }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Valeur</h4>
                        <p class="mt-1 text-gray-900">
                            @if($stock->equipment->valeur)
                                {{ number_format($stock->equipment->valeur, 2, ',', ' ') }} €
                            @else
                                Non renseignée
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Statut équipement</h4>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                            {{ $stock->equipment->statut }}
                        </span>
                    </div>
                    
                    @if($stock->equipment->description)
                    <div class="md:col-span-2">
                        <h4 class="text-sm font-medium text-gray-500">Description</h4>
                        <p class="mt-1 text-gray-900">{{ $stock->equipment->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Observations -->
            @if($stock->observations)
            <div class="mt-8 pt-8 border-t">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Observations</h3>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-gray-700 whitespace-pre-line">{{ $stock->observations }}</p>
                </div>
            </div>
            @endif
        </div>
        
        <div class="px-6 py-4 bg-gray-50 border-t">
            <div class="flex justify-between text-sm text-gray-500">
                <div>Enregistré le: {{ $stock->created_at->format('d/m/Y H:i') }}</div>
                <div>Modifié le: {{ $stock->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection