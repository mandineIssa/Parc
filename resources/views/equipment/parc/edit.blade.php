@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Modifier l'Affectation</h1>
    
    <form action="{{ route('parc.update', $parc->id) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="numero_serie">
                Équipement *
            </label>
            <select name="numero_serie" id="numero_serie" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">Sélectionner un équipement</option>
                @foreach($equipments as $equipment)
                    <option value="{{ $equipment->numero_serie }}" {{ $parc->numero_serie == $equipment->numero_serie ? 'selected' : '' }}>
                        {{ $equipment->numero_serie }} - {{ $equipment->type }} ({{ $equipment->marque }})
                    </option>
                @endforeach
            </select>
            @error('numero_serie')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="utilisateur_id">
                Utilisateur *
            </label>
            <select name="utilisateur_id" id="utilisateur_id" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">Sélectionner un utilisateur</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $parc->utilisateur_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            @error('utilisateur_id')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="departement">
                    Département *
                </label>
                <input type="text" name="departement" id="departement" value="{{ old('departement', $parc->departement) }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('departement')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="poste_affecte">
                    Poste *
                </label>
                <input type="text" name="poste_affecte" id="poste_affecte" value="{{ old('poste_affecte', $parc->poste_affecte) }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('poste_affecte')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_affectation">
                    Date d'affectation *
                </label>
                <input type="date" name="date_affectation" id="date_affectation" value="{{ old('date_affectation', $parc->date_affectation->format('Y-m-d')) }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('date_affectation')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_retour_prevue">
                    Date de retour prévue
                </label>
                <input type="date" name="date_retour_prevue" id="date_retour_prevue" value="{{ old('date_retour_prevue', $parc->date_retour_prevue ? $parc->date_retour_prevue->format('Y-m-d') : '') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('date_retour_prevue')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="statut_usage">
                Statut d'usage *
            </label>
            <select name="statut_usage" id="statut_usage" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="en_service" {{ $parc->statut_usage == 'en_service' ? 'selected' : '' }}>En service</option>
                <option value="reserve" {{ $parc->statut_usage == 'reserve' ? 'selected' : '' }}>Réservé</option>
                <option value="maintenance" {{ $parc->statut_usage == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
            @error('statut_usage')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="notes_affectation">
                Notes
            </label>
            <textarea name="notes_affectation" id="notes_affectation" rows="3"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('notes_affectation', $parc->notes_affectation) }}</textarea>
            @error('notes_affectation')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="flex items-center justify-between">
            <a href="{{ route('parc.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Annuler
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection