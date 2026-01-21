@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Détails de l'Affectation</h1>
        <div>
            <a href="{{ route('parc.edit', $parc->id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">
                Modifier
            </a>
            <a href="{{ route('parc.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Retour
            </a>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-700">Informations de l'affectation</h2>
        </div>
        
        <div class="px-6 py-4">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Numéro de série</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $parc->numero_serie }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Utilisateur</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $parc->utilisateur->name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-500">{{ $parc->utilisateur->email ?? '' }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Département</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $parc->departement }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Poste affecté</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $parc->poste_affecte }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Date d'affectation</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $parc->date_affectation->format('d/m/Y') }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Date de retour prévue</h3>
                    <p class="mt-1 text-lg text-gray-900">
                        {{ $parc->date_retour_prevue ? $parc->date_retour_prevue->format('d/m/Y') : 'Non définie' }}
                    </p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Statut d'usage</h3>
                    <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        {{ $parc->statut_usage == 'en_service' ? 'bg-green-100 text-green-800' : 
                           ($parc->statut_usage == 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ ucfirst(str_replace('_', ' ', $parc->statut_usage)) }}
                    </span>
                </div>
                
                @if($parc->equipment)
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Informations équipement</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $parc->equipment->type }}</p>
                    <p class="text-sm text-gray-500">{{ $parc->equipment->marque }} - {{ $parc->equipment->modele }}</p>
                </div>
                @endif
            </div>
            
            @if($parc->notes_affectation)
            <div class="mt-8">
                <h3 class="text-sm font-medium text-gray-500">Notes</h3>
                <div class="mt-2 p-4 bg-gray-50 rounded">
                    <p class="text-gray-700">{{ $parc->notes_affectation }}</p>
                </div>
            </div>
            @endif
        </div>
        
        <div class="px-6 py-4 bg-gray-50 border-t">
            <div class="flex justify-between text-sm text-gray-500">
                <div>Créé le: {{ $parc->created_at->format('d/m/Y H:i') }}</div>
                <div>Modifié le: {{ $parc->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection