@extends('layouts.app')
@section('title', 'Détails Équipement')
 

@section('content')
<div class="bg-gray-50 min-h-screen py-6">
    <div class="container mx-auto px-4">
        <!-- En-tête avec actions -->
        <div class="mb-6">
            <!--<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $equipment->nom }}</h1>
                    <p class="text-gray-600">{{ $equipment->marque }} {{ $equipment->modele }}</p>
                </div> -->
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.celer-informatique') }}" class="btn-cofina-outline">
                        ↩️ Retour
                    </a>
                    <a href="{{ route('equipment.edit', $equipment) }}" class="btn-cofina-outline bg-blue-50 border-blue-200 hover:bg-blue-100">
                        ✏️ Modifier
                    </a>
                    <a href="{{ route('equipment.transition.show', $equipment) }}" class="btn-cofina-primary">
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
               <!-- <span class="px-4 py-2 rounded-full text-sm font-bold {{ $statusColors[$equipment->statut] ?? 'bg-gray-100' }}">
                    Statut: {{ ucfirst(str_replace('_', ' ', $equipment->statut)) }}
                </span>
                <span class="ml-2 px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm">
                    N° Série: <span class="font-bold">{{ $equipment->numero_serie }}</span>
                </span>-->
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
                                <span class="font-medium">{{ $equipment->categorie->nom ?? 'N/A' }}</span>
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
                        </div>
                    </div>
                </div>

                <!-- Carte Caractéristiques -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-cofina-red mb-4 pb-2 border-b">⚙️ Caractéristiques</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="w-40 text-gray-600">Garantie:</span>
                                <span class="font-medium">{{ $equipment->garantie ?? 'Aucune' }}</span>
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
                            @endif
                            @break
                            
                        @case('parc')
                            @if($equipment->parc)
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Utilisateur:</span>
                                    <span class="font-bold">{{ $equipment->parc->utilisateur->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Département:</span>
                                    <span>{{ $equipment->parc->departement }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Poste:</span>
                                    <span>{{ $equipment->parc->poste_affecte }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Date Affectation:</span>
                                    <span>{{ $equipment->parc->date_affectation->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Statut Usage:</span>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        {{ $equipment->parc->statut_usage }}
                                    </span>
                                </div>
                            </div>
                            @endif
                            @break
                            
                        @case('maintenance')
                            @if($equipment->maintenance)
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Type:</span>
                                    <span class="font-bold">{{ $equipment->maintenance->type_maintenance }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Prestataire:</span>
                                    <span>{{ $equipment->maintenance->prestataire }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Date Départ:</span>
                                    <span>{{ $equipment->maintenance->date_depart->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Retour Prévu:</span>
                                    <span>{{ $equipment->maintenance->date_retour_prevue->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-40 text-gray-600">Statut:</span>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($equipment->maintenance->statut == 'en_cours') bg-yellow-100 text-yellow-800
                                        @elseif($equipment->maintenance->statut == 'terminee') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $equipment->maintenance->statut }}
                                    </span>
                                </div>
                            </div>
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