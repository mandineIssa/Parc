@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Nouvelle Déclaration de Perte</h1>
    
    <form action="{{ route('perdu.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
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
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_disparition">
                    Date de disparition *
                </label>
                <input type="date" name="date_disparition" id="date_disparition" 
                    value="{{ old('date_disparition', date('Y-m-d')) }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('date_disparition')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="type_disparition">
                    Type de disparition *
                </label>
                <select name="type_disparition" id="type_disparition" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Sélectionner un type</option>
                    <option value="vol" {{ old('type_disparition') == 'vol' ? 'selected' : '' }}>Vol</option>
                    <option value="perte" {{ old('type_disparition') == 'perte' ? 'selected' : '' }}>Perte</option>
                    <option value="oubli" {{ old('type_disparition') == 'oubli' ? 'selected' : '' }}>Oubli</option>
                    <option value="destruction" {{ old('type_disparition') == 'destruction' ? 'selected' : '' }}>Destruction</option>
                </select>
                @error('type_disparition')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="lieu_disparition">
                Lieu de disparition *
            </label>
            <input type="text" name="lieu_disparition" id="lieu_disparition" 
                value="{{ old('lieu_disparition') }}" required
                placeholder="Ex: Bureau 203, Parking entreprise..."
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('lieu_disparition')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="circonstances">
                Circonstances *
            </label>
            <textarea name="circonstances" id="circonstances" rows="3" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Décrivez les circonstances de la disparition...">{{ old('circonstances') }}</textarea>
            @error('circonstances')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <div class="flex items-center mb-2">
                <input type="checkbox" name="plainte_deposee" id="plainte_deposee" value="1" 
                    {{ old('plainte_deposee') ? 'checked' : '' }}
                    class="mr-2">
                <label class="text-gray-700 text-sm font-bold" for="plainte_deposee">
                    Plainte déposée
                </label>
            </div>
            
            <div id="plainte_fields" class="mt-2 {{ old('plainte_deposee') ? '' : 'hidden' }}">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="numero_plainte">
                    Numéro de plainte *
                </label>
                <input type="text" name="numero_plainte" id="numero_plainte" 
                    value="{{ old('numero_plainte') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('numero_plainte')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="valeur_assuree">
                    Valeur assurée (€)
                </label>
                <input type="number" step="0.01" name="valeur_assuree" id="valeur_assuree" 
                    value="{{ old('valeur_assuree') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('valeur_assuree')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="statut_recherche">
                    Statut de recherche *
                </label>
                <select name="statut_recherche" id="statut_recherche" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="en_cours" {{ old('statut_recherche') == 'en_cours' ? 'selected' : '' }}>En recherche</option>
                    <option value="definitif" {{ old('statut_recherche') == 'definitif' ? 'selected' : '' }}>Définitif</option>
                </select>
                @error('statut_recherche')
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
            <a href="{{ route('perdu.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Annuler
            </a>
            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Déclarer la perte
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('plainte_deposee').addEventListener('change', function() {
    const plainteFields = document.getElementById('plainte_fields');
    const plainteInput = document.getElementById('numero_plainte');
    
    if (this.checked) {
        plainteFields.classList.remove('hidden');
        plainteInput.required = true;
    } else {
        plainteFields.classList.add('hidden');
        plainteInput.required = false;
        plainteInput.value = '';
    }
});
</script>
@endsection