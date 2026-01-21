@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Nouvelle Déclaration Hors Service</h1>
    
    <form action="{{ route('hors-service.store') }}" method="POST" enctype="multipart/form-data" 
          class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="numero_serie">
                Équipement *
            </label>
            <select name="numero_serie" id="numero_serie" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">Sélectionner un équipement</option>
                @foreach($equipments as $equipment)
                    <option value="{{ $equipment->numero_serie }}" {{ old('numero_serie') == $equipment->numero_serie ? 'selected' : '' }}>
                        {{ $equipment->numero_serie }} - {{ $equipment->type }} ({{ $equipment->marque }} {{ $equipment->modele }})
                    </option>
                @endforeach
            </select>
            @error('numero_serie')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_hors_service">
                    Date hors service *
                </label>
                <input type="date" name="date_hors_service" id="date_hors_service" 
                    value="{{ old('date_hors_service', date('Y-m-d')) }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('date_hors_service')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="raison">
                    Raison *
                </label>
                <select name="raison" id="raison" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Sélectionner une raison</option>
                    <option value="panne" {{ old('raison') == 'panne' ? 'selected' : '' }}>Panne</option>
                    <option value="obsolescence" {{ old('raison') == 'obsolescence' ? 'selected' : '' }}>Obsolescence</option>
                    <option value="accident" {{ old('raison') == 'accident' ? 'selected' : '' }}>Accident</option>
                    <option value="autre" {{ old('raison') == 'autre' ? 'selected' : '' }}>Autre</option>
                </select>
                @error('raison')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description_incident">
                Description de l'incident *
            </label>
            <textarea name="description_incident" id="description_incident" rows="3" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Décrivez en détail l'incident ou la panne...">{{ old('description_incident') }}</textarea>
            @error('description_incident')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="destinataire">
                    Destinataire (optionnel)
                </label>
                <input type="text" name="destinataire" id="destinataire" 
                    value="{{ old('destinataire') }}"
                    placeholder="Ex: Recyclage, Don..."
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('destinataire')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_traitement">
                    Date de traitement
                </label>
                <input type="date" name="date_traitement" id="date_traitement" 
                    value="{{ old('date_traitement') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('date_traitement')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="valeur_residuelle">
                    Valeur résiduelle (€)
                </label>
                <input type="number" step="0.01" name="valeur_residuelle" id="valeur_residuelle" 
                    value="{{ old('valeur_residuelle') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('valeur_residuelle')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="justificatif">
                    Justificatif (optionnel)
                </label>
                <input type="file" name="justificatif" id="justificatif"
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (max 2MB)</p>
                @error('justificatif')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="observations">
                Observations supplémentaires
            </label>
            <textarea name="observations" id="observations" rows="3"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('observations') }}</textarea>
            @error('observations')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="flex items-center justify-between">
            <a href="{{ route('hors-service.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Annuler
            </a>
            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Déclarer hors service
            </button>
        </div>
    </form>
</div>
@endsection