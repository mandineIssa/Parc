@extends('layouts.app')
@section('title', 'Détails Équipement')

@section('content')
<div class="bg-gray-50 min-h-screen py-6">
    <div class="container mx-auto px-4">
        <!-- En-tête avec actions -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $equipment->nom ?? $equipment->marque . ' ' . $equipment->modele }}</h1>
                    <p class="text-gray-600">{{ $equipment->marque }} {{ $equipment->modele }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.celer-informatique') }}" class="btn-cofina-outline">
                        ↩️ Retour
                    </a>
                    
                    <a href="{{ route('equipment.edit', $equipment) }}" class="btn-cofina-outline bg-blue-50 border-blue-200 hover:bg-blue-100">
                        ✏️ Modifier
                    </a>
                    <a href="{{ route('equipment.transitions.', $equipment) }}" class="btn-cofina-primary">
                        🔄 Changer statut
                    </a>
                </div>
            </div>
            
            <!-- Badge statut -->
            <div class="mt-4">
                @php
                    $statusColors = [
                        'stock' => 'bg-blue-100 text-blue-800 border border-blue-200',
                        'parc' => 'bg-green-100 text-green-800 border border-green-200',
                        'maintenance' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                        'hors_service' => 'bg-red-100 text-red-800 border border-red-200',
                        'perdu' => 'bg-gray-100 text-gray-800 border border-gray-200'
                    ];
                @endphp
                <span class="px-4 py-2 rounded-full text-sm font-bold {{ $statusColors[$equipment->statut] ?? 'bg-gray-100' }}">
                    Statut: {{ ucfirst(str_replace('_', ' ', $equipment->statut)) }}
                </span>
                <span class="ml-2 px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm">
                    N° Série: <span class="font-bold">{{ $equipment->numero_serie }}</span>
                </span>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Section gauche - Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Carte Informations Générales -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-cofina-red mb-4 pb-2 border-b">📋 Informations Générales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Codification:</span>
                                <span class="font-medium">{{ $equipment->numero_codification ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Type:</span>
                                <span class="font-medium">{{ $equipment->type }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Catégorie:</span>
                                <span class="font-medium">
                                    @if($equipment->detail && $equipment->detail->categorie)
                                        {{ $equipment->detail->categorie }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Sous-catégorie:</span>
                                <span class="font-medium">
                                    @if($equipment->detail && $equipment->detail->sous_categorie)
                                        {{ $equipment->detail->sous_categorie }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Agence:</span>
                                <span class="font-medium">{{ $equipment->agence->nom ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Localisation:</span>
                                <span class="font-medium">{{ $equipment->localisation }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">État:</span>
                                <span class="px-2 py-1 text-xs rounded-full font-medium capitalize bg-gray-100">
                                    {{ $equipment->etat }}
                                </span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Prix:</span>
                                <span class="font-medium">{{ number_format($equipment->prix, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Date Livraison:</span>
                                <span class="font-medium">{{ $equipment->date_livraison->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-32 text-gray-600">Garantie:</span>
                                <span class="font-medium">{{ $equipment->garantie }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte Caractéristiques -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-cofina-red mb-4 pb-2 border-b">⚙️ Caractéristiques</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Marque:</span>
                                <span class="font-medium">{{ $equipment->marque }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Modèle:</span>
                                <span class="font-medium">{{ $equipment->modele }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Fournisseur:</span>
                                <span class="font-medium">{{ $equipment->fournisseur->nom ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Réf. Facture:</span>
                                <span class="font-medium">{{ $equipment->reference_facture ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Réf. Installation:</span>
                                <span class="font-medium">{{ $equipment->reference_installation ?? 'N/A' }}</span>
                            </div>
                            @if($equipment->date_mise_service)
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Mise en service:</span>
                                <span class="font-medium">{{ $equipment->date_mise_service->format('d/m/Y') }}</span>
                            </div>
                            @endif
                            @if($equipment->date_amortissement)
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Amortissement:</span>
                                <span class="font-medium">{{ $equipment->date_amortissement->format('d/m/Y') }}</span>
                            </div>
                            @endif
                            @if($equipment->detail && $equipment->detail->contrat_maintenance)
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Contrat maintenance:</span>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-medium">
                                    ✓ Actif
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informations Réseau -->
                @if($equipment->adresse_ip || $equipment->adresse_mac)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-cofina-red mb-4 pb-2 border-b">🌐 Informations Réseau</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($equipment->adresse_ip)
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-sm text-gray-600 mb-1">Adresse IP</div>
                            <div class="font-mono text-gray-800">{{ $equipment->adresse_ip }}</div>
                        </div>
                        @endif
                        @if($equipment->adresse_mac)
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <div class="text-sm text-gray-600 mb-1">Adresse MAC</div>
                            <div class="font-mono text-gray-800">{{ $equipment->adresse_mac }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Données spécifiques (lecture seule) -->
                <!-- Données spécifiques (lecture seule) -->
@if($equipment->detail && !empty($specificData))
<div class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden">
    <!-- Header -->
    <div class="bg-gray-50 px-6 py-4 border-b">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Données spécifiques (lecture seule)
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    Ces informations sont verrouillées et ne peuvent être modifiées que lors de la création.
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                Lecture seule
            </span>
        </div>
    </div>
    
    <!-- Contenu -->
    <div class="p-6">
        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-6">
            <div class="flex items-center mb-3">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-gray-700">
                    Les données spécifiques sont définies lors de la création de l'équipement et ne peuvent pas être modifiées par la suite.
                </p>
            </div>
        </div>
        
        <!-- Grille de données -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($specificData as $key => $value)
                @if(!empty($value))
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ ucfirst(str_replace(['_', '-'], ' ', $key)) }}
                    </h4>
                    <div class="space-y-2">
                        <div>
                            <span class="text-sm font-medium text-gray-800">
                                @if(is_array($value))
                                    @if(count($value) > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($value as $item)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $item }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400">Non défini</span>
                                    @endif
                                @elseif(is_bool($value))
                                    <span class="inline-flex items-center">
                                        @if($value)
                                            <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Oui
                                        @else
                                            <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                            Non
                                        @endif
                                    </span>
                                @elseif($value === null || $value === '')
                                    <span class="text-gray-400">Non défini</span>
                                @elseif(is_string($value) && (str_contains($value, 'http') || str_contains($value, 'www.')))
                                    <a href="{{ $value }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline">{{ $value }}</a>
                                @else
                                    {{ $value }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
        
        <!-- Bouton d'action (optionnel, vous pouvez le commenter si non souhaité) -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Pour modifier ces données, créez un nouvel équipement
                </div>
            </div>
        </div>
    </div>
</div>
@endif

                <!-- Notes -->
                @if($equipment->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-cofina-red mb-4 pb-2 border-b">📝 Notes</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-line">{{ $equipment->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Section droite - Informations statut et actions -->
            <div class="space-y-6">
                <!-- Carte Informations selon statut -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-cofina-red mb-4 pb-2 border-b">
                        @switch($equipment->statut)
                            @case('stock') 📦 Stock @break
                            @case('parc') 👥 Parc @break
                            @case('maintenance') 🛠️ Maintenance @break
                            @case('hors_service') 🚫 Hors Service @break
                            @case('perdu') ⚠️ Perdu @break
                            @default ℹ️ Informations
                        @endswitch
                    </h3>
                    
                    @switch($equipment->statut)
                        @case('stock')
                            @if($equipment->stock)
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Type Stock:</span>
                                    <span class="font-bold">{{ $equipment->stock->type_stock }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Localisation:</span>
                                    <span>{{ $equipment->stock->localisation_physique }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">État Stock:</span>
                                    <span>{{ $equipment->stock->etat }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Date Entrée:</span>
                                    <span>{{ $equipment->stock->date_entree->format('d/m/Y') }}</span>
                                </div>
                                @if($equipment->stock->date_sortie)
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Date Sortie:</span>
                                    <span class="text-red-600">{{ $equipment->stock->date_sortie->format('d/m/Y') }}</span>
                                </div>
                                @endif
                                @if($equipment->stock->type_stock == 'celer' && $equipment->stock->celer)
                                <div class="mt-4 pt-4 border-t">
                                    <h4 class="font-bold text-gray-700 mb-2">Détails Celer</h4>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <span class="w-32 text-gray-600">Facture:</span>
                                            <span>{{ $equipment->stock->celer->numero_facture ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-32 text-gray-600">Garantie:</span>
                                            <span>{{ $equipment->stock->celer->certificat_garantie ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @else
                            <p class="text-gray-600 italic">Aucune information de stock disponible</p>
                            @endif
                            @break
                            
                        @case('parc')
                            @if($equipment->parc)
                            <div class="space-y-3">
                                <!-- Utiliser d'abord les colonnes directes de la table parc -->
                                @if($equipment->parc->utilisateur_nom || $equipment->parc->utilisateur_prenom)
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Utilisateur:</span>
                                    <span class="font-bold">
                                        {{ trim($equipment->parc->utilisateur_nom ?? '') }} {{ trim($equipment->parc->utilisateur_prenom ?? '') }}
                                    </span>
                                </div>
                                @elseif($equipment->parc->utilisateur)
                                <!-- Fallback: utiliser la relation utilisateur si les colonnes directes sont vides -->
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Utilisateur:</span>
                                    <span class="font-bold">{{ $equipment->parc->utilisateur->name ?? 'N/A' }}</span>
                                </div>
                                @endif
                                
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Département:</span>
                                    <span>{{ $equipment->parc->departement }}</span>
                                </div>
                                
                                <!-- Utiliser la colonne position si elle existe -->
                                @if($equipment->parc->position)
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Position:</span>
                                    <span>{{ $equipment->parc->position }}</span>
                                </div>
                                @else
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Poste:</span>
                                    <span>{{ $equipment->parc->poste_affecte }}</span>
                                </div>
                                @endif
                                
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Date Affectation:</span>
                                    <span>{{ $equipment->parc->date_affectation->format('d/m/Y') }}</span>
                                </div>
                                
                                @if($equipment->parc->date_retour_prevue)
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Retour prévu:</span>
                                    <span>{{ $equipment->parc->date_retour_prevue->format('d/m/Y') }}</span>
                                </div>
                                @endif
                                
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Statut Usage:</span>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        {{ $equipment->parc->statut_usage }}
                                    </span>
                                </div>
                                
                                <!-- Afficher les nouvelles colonnes si elles existent -->
                                @if($equipment->parc->affectation_reason)
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Motif affectation:</span>
                                    <span>{{ $equipment->parc->affectation_reason }}</span>
                                </div>
                                @endif
                                
                                @if($equipment->parc->localisation)
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Localisation:</span>
                                    <span>{{ $equipment->parc->localisation }}</span>
                                </div>
                                @endif
                                
                                @if($equipment->parc->telephone)
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Téléphone:</span>
                                    <span>{{ $equipment->parc->telephone }}</span>
                                </div>
                                @endif
                                
                                @if($equipment->parc->email)
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Email:</span>
                                    <span>{{ $equipment->parc->email }}</span>
                                </div>
                                @endif
                                
                                @if($equipment->parc->affectation_reason_detail)
                                <div class="mt-2 pt-2 border-t border-gray-200">
                                    <div class="text-sm text-gray-600 mb-1">Détails motif:</div>
                                    <p class="text-sm text-gray-700">{{ $equipment->parc->affectation_reason_detail }}</p>
                                </div>
                                @endif
                            </div>
                            @else
                            <p class="text-gray-600 italic">Aucune information de parc disponible</p>
                            @endif
                            @break
                            
                        @case('maintenance')
                            @if($equipment->maintenance->isNotEmpty())
                                @php
                                    $currentMaintenance = $equipment->maintenance->first();
                                @endphp
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <span class="w-40 text-gray-600">Type:</span>
                                        <span class="font-bold">{{ $currentMaintenance->type_maintenance ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-40 text-gray-600">Prestataire:</span>
                                        <span>{{ $currentMaintenance->prestataire ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-40 text-gray-600">Date Départ:</span>
                                        <span>{{ $currentMaintenance->date_depart->format('d/m/Y') ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-40 text-gray-600">Retour Prévu:</span>
                                        <span>{{ $currentMaintenance->date_retour_prevue->format('d/m/Y') ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="w-40 text-gray-600">Statut:</span>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if(($currentMaintenance->statut ?? '') == 'en_cours') bg-yellow-100 text-yellow-800
                                            @elseif(($currentMaintenance->statut ?? '') == 'terminee') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $currentMaintenance->statut ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-600 italic">Aucune information de maintenance disponible</p>
                            @endif
                            @break
                            
                        @default
                            <p class="text-gray-600 italic">Aucune information supplémentaire pour ce statut</p>
                    @endswitch
                </div>

                <!-- Carte Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-cofina-red mb-4 pb-2 border-b">⚡ Actions Rapides</h3>
                    <div class="space-y-3">
                        <a href="{{ route('equipment.audit', $equipment) }}" 
                           class="flex items-center justify-center w-full p-3 text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition">
                            <span class="mr-2">📋</span> Historique
                        </a>
                        <form action="{{ route('equipment.destroy', $equipment) }}" method="POST" 
                              onsubmit="return confirm('Supprimer définitivement cet équipement ? Cette action est irréversible !')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="flex items-center justify-center w-full p-3 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition">
                                <span class="mr-2">🗑️</span> Supprimer l'équipement
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Carte Dates -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-cofina-red mb-4 pb-2 border-b">📅 Dates Importantes</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Créé le:</span>
                            <span class="text-sm">{{ $equipment->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Modifié le:</span>
                            <span class="text-sm">{{ $equipment->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($equipment->detail && $equipment->detail->date_debut_contrat)
                        <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                            <span class="text-gray-600">Début contrat:</span>
                            <span class="text-sm">{{ $equipment->detail->date_debut_contrat->format('d/m/Y') }}</span>
                        </div>
                        @endif
                        @if($equipment->detail && $equipment->detail->date_fin_contrat)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Fin contrat:</span>
                            <span class="text-sm">{{ $equipment->detail->date_fin_contrat->format('d/m/Y') }}</span>
                        </div>
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
        @apply bg-cofina-red text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition;
    }
    .btn-cofina-outline {
        @apply border border-gray-300 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 transition;
    }
    .text-cofina-red {
        color: #dc2626;
    }
</style>
@endpush