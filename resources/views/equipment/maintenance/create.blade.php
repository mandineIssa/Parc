@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Nouvelle Maintenance</h1>
    
    <form action="{{ route('maintenance.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
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
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_depart">
                    Date de départ *
                </label>
                <input type="date" name="date_depart" id="date_depart" 
                    value="{{ old('date_depart', date('Y-m-d')) }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('date_depart')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_retour_prevue">
                    Retour prévu *
                </label>
                <input type="date" name="date_retour_prevue" id="date_retour_prevue" 
                    value="{{ old('date_retour_prevue', date('Y-m-d', strtotime('+7 days'))) }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('date_retour_prevue')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="type_maintenance">
                    Type de maintenance *
                </label>
                <select name="type_maintenance" id="type_maintenance" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Sélectionner un type</option>
                    <option value="preventive" {{ old('type_maintenance') == 'preventive' ? 'selected' : '' }}>Préventive</option>
                    <option value="corrective" {{ old('type_maintenance') == 'corrective' ? 'selected' : '' }}>Corrective</option>
                    <option value="contractuelle" {{ old('type_maintenance') == 'contractuelle' ? 'selected' : '' }}>Contractuelle</option>
                    <option value="autre" {{ old('type_maintenance') == 'autre' ? 'selected' : '' }}>Autre</option>
                </select>
                @error('type_maintenance')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="statut">
                    Statut *
                </label>
                <select name="statut" id="statut" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="terminee" {{ old('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                    <option value="annulee" {{ old('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                </select>
                @error('statut')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="prestataire">
                Prestataire *
            </label>
            <input type="text" name="prestataire" id="prestataire" 
                value="{{ old('prestataire') }}" required
                placeholder="Nom du prestataire..."
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('prestataire')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="cout">
                    Coût estimé (€)
                </label>
                <input type="number" step="0.01" name="cout" id="cout" 
                    value="{{ old('cout') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('cout')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            
            <div id="date_retour_reelle_div" class="{{ old('statut') == 'terminee' ? '' : 'hidden' }}">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_retour_reelle">
                    Date de retour réelle
                </label>
                <input type="date" name="date_retour_reelle" id="date_retour_reelle" 
                    value="{{ old('date_retour_reelle') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('date_retour_reelle')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description_panne">
                Description de la panne / besoin *
            </label>
            <textarea name="description_panne" id="description_panne" rows="3" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Décrivez la panne ou le besoin de maintenance...">{{ old('description_panne') }}</textarea>
            @error('description_panne')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="travaux_realises">
                Travaux réalisés (si terminée)
            </label>
            <textarea name="travaux_realises" id="travaux_realises" rows="3"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('travaux_realises') }}</textarea>
            @error('travaux_realises')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="observations">
                Observations
            </label>
            <textarea name="observations" id="observations" rows="3"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('observations') }}</textarea>
            @error('observations')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="flex items-center justify-between">
            <a href="{{ route('maintenance.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Annuler
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Enregistrer
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('statut').addEventListener('change', function() {
    const dateRetourDiv = document.getElementById('date_retour_reelle_div');
    const dateRetourInput = document.getElementById('date_retour_reelle');
    
    if (this.value === 'terminee') {
        dateRetourDiv.classList.remove('hidden');
        if (!dateRetourInput.value) {
            dateRetourInput.value = '{{ date("Y-m-d") }}';
        }
    } else {
        dateRetourDiv.classList.add('hidden');
        dateRetourInput.value = '';
    }
});
</script>
@endsection