@extends('layouts.app')

@section('title', 'Importation d\'Équipements')
@section('header', 'Importation CSV des Équipements')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📥 Importer des Équipements</h2>
        
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Format requis :</strong> Assurez-vous que votre fichier CSV respecte la structure exacte du modèle.
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('reports.import.process') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        Fichier CSV
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-cofina-red hover:text-red-700 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-cofina-red">
                                    <span>Télécharger un fichier</span>
                                    <input id="file" name="file" type="file" class="sr-only" accept=".csv,.txt" required>
                                </label>
                                <p class="pl-1">ou glissez-déposez</p>
                            </div>
                            <p class="text-xs text-gray-500">CSV, TXT jusqu'à 10MB</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Options d'import
                    </label>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input id="skip_duplicates" name="skip_duplicates" type="checkbox" class="h-4 w-4 text-cofina-red focus:ring-cofina-red border-gray-300 rounded" checked>
                            <label for="skip_duplicates" class="ml-2 block text-sm text-gray-700">
                                Ignorer les doublons (numéro de série)
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="create_suppliers" name="create_suppliers" type="checkbox" class="h-4 w-4 text-cofina-red focus:ring-cofina-red border-gray-300 rounded">
                            <label for="create_suppliers" class="ml-2 block text-sm text-gray-700">
                                Créer les fournisseurs manquants
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="create_agencies" name="create_agencies" type="checkbox" class="h-4 w-4 text-cofina-red focus:ring-cofina-red border-gray-300 rounded">
                            <label for="create_agencies" class="ml-2 block text-sm text-gray-700">
                                Créer les agences manquantes
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <a href="{{ route('reports.index') }}" class="btn-cofina-secondary">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour aux rapports
                </a>
                <div class="space-x-3">
                    <a href="{{ route('equipment.import') }}" class="btn-cofina-secondary">
                        Utiliser l'import standard
                    </a>
                    <button type="submit" class="btn-cofina-primary">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                        </svg>
                        Importer le fichier
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Section modèle CSV -->
    <div class="card-cofina">
        <h3 class="text-lg font-bold text-cofina-red mb-4">📋 Modèle de Fichier CSV Requis</h3>
        
        <div class="mb-4">
            <p class="text-sm text-gray-600 mb-2">Les colonnes suivantes doivent être présentes (séparées par tabulation):</p>
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <code class="text-sm">
                    type | numero_serie | marque | modele | garantie | date_livraison | prix | reference_facture | etat | statut | fournisseur_id | agence_id | localisation | departement | adresse_mac | adresse_ip | numero_codification | date_mise_service | notes
                </code>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase">type</th>
                        <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase">numero_serie</th>
                        <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase">marque</th>
                        <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase">prix</th>
                        <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase">etat</th>
                        <th class="px-4 py-2 border text-left text-xs font-medium text-gray-500 uppercase">localisation</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-4 py-2 border text-sm">Informatique</td>
                        <td class="px-4 py-2 border text-sm font-mono">SN-123456</td>
                        <td class="px-4 py-2 border text-sm">Dell Latitude</td>
                        <td class="px-4 py-2 border text-sm">500000</td>
                        <td class="px-4 py-2 border text-sm">neuf</td>
                        <td class="px-4 py-2 border text-sm">Siège</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-2 border text-sm">Réseau</td>
                        <td class="px-4 py-2 border text-sm font-mono">SW-789012</td>
                        <td class="px-4 py-2 border text-sm">Cisco Catalyst</td>
                        <td class="px-4 py-2 border text-sm">750000</td>
                        <td class="px-4 py-2 border text-sm">bon</td>
                        <td class="px-4 py-2 border text-sm">Agence Centre</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 flex justify-between items-center">
            <a href="{{ route('equipment.export.template') }}" class="text-cofina-red hover:underline inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Télécharger le modèle complet
            </a>
            
            <a href="{{ route('reports.export.equipment') }}" class="btn-cofina-primary text-sm">
                <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exporter les données actuelles
            </a>
        </div>
    </div>
</div>
@endsection