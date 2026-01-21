@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Modifier la catégorie</h1>
            <div class="flex items-center mt-2">
                @if($category->type == 'réseaux')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mr-3">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                        </svg>
                        Réseaux
                    </span>
                @elseif($category->type == 'électronique')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mr-3">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                        </svg>
                        Électronique
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 mr-3">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Informatiques
                    </span>
                @endif
                <span class="text-sm text-gray-500">
                    ID: <span class="font-mono text-gray-700">#{{ $category->id }}</span>
                </span>
            </div>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <a href="{{ route('categories.show', $category) }}" 
               class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-orange-50">
            <h2 class="text-lg font-semibold text-gray-800">Modifier les informations</h2>
            <p class="text-sm text-gray-600 mt-1">Modifiez les champs ci-dessous pour mettre à jour cette catégorie</p>
        </div>

        <form action="{{ route('categories.update', $category) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-8">
                <!-- Type et Nom -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type de catégorie *
                            <span class="text-red-500">*</span>
                        </label>
                        <select id="type" name="type" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('type') border-red-500 @enderror"
                                required>
                            <option value="">Sélectionnez un type</option>
                            <option value="réseaux" {{ old('type', $category->type) == 'réseaux' ? 'selected' : '' }}>Réseaux</option>
                            <option value="électronique" {{ old('type', $category->type) == 'électronique' ? 'selected' : '' }}>Électronique</option>
                            <option value="informatiques" {{ old('type', $category->type) == 'informatiques' ? 'selected' : '' }}>Informatiques</option>
                        </select>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nom -->
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom de la catégorie *
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nom" name="nom" value="{{ old('nom', $category->nom) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('nom') border-red-500 @enderror"
                               placeholder="Ex: Connectivité & Transmission"
                               required>
                        @error('nom')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Catégorie parente -->
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Catégorie parente
                    </label>
                    <select id="parent_id" name="parent_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <option value="">Aucune (catégorie principale)</option>
                        @foreach($parentCategories as $parent)
                            @if($parent->id != $category->id) {{-- Éviter de se sélectionner soi-même --}}
                            <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->nom }} ({{ $parent->type_label }})
                            </option>
                            @endif
                        @endforeach
                    </select>
                    <div class="mt-2 text-sm text-gray-500">
                        Si cette catégorie est une sous-catégorie, sélectionnez sa catégorie parente
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('description') border-red-500 @enderror"
                              placeholder="Décrivez cette catégorie...">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Équipements typiques -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Équipements typiques
                        </label>
                        <button type="button" 
                                onclick="addEquipmentField()"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ajouter un équipement
                        </button>
                    </div>
                    
                    <div id="equipment-container" class="space-y-3">
                        @php
                            $equipmentList = old('equipment_list', $category->equipment_list ?? []);
                        @endphp
                        
                        @if(count($equipmentList) > 0)
                            @foreach($equipmentList as $equipment)
                            <div class="flex items-center">
                                <input type="text" 
                                       name="equipment_list[]" 
                                       value="{{ $equipment }}"
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                       placeholder="Ex: Switches (L2/L3)">
                                <button type="button" 
                                        onclick="removeEquipmentField(this)"
                                        class="ml-3 text-red-600 hover:text-red-800 p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            @endforeach
                        @else
                            <div class="flex items-center">
                                <input type="text" 
                                       name="equipment_list[]" 
                                       class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                       placeholder="Ex: Switches (L2/L3)">
                                <button type="button" 
                                        onclick="removeEquipmentField(this)"
                                        class="ml-3 text-red-600 hover:text-red-800 p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-3 text-sm text-gray-500">
                        Liste des équipements typiques de cette catégorie (un par ligne)
                    </div>
                </div>

                <!-- Informations importantes -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-yellow-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-800 mb-2">Information importante</h3>
                            <p class="text-yellow-700 mb-3">La modification de cette catégorie affectera tous les équipements qui y sont associés. Assurez-vous que les modifications sont correctes avant de sauvegarder.</p>
                            <div class="text-sm text-yellow-700 bg-yellow-100 p-3 rounded-lg">
                                <p class="font-medium">Cette catégorie contient actuellement :</p>
                                <ul class="mt-1 space-y-1">
                                    <li>• <span class="font-semibold">{{ $category->equipment_count ?? 0 }}</span> équipement(s)</li>
                                    @if($category->subcategories_count > 0)
                                    <li>• <span class="font-semibold">{{ $category->subcategories_count }}</span> sous-catégorie(s)</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex flex-col-reverse md:flex-row gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('categories.show', $category) }}" 
                       class="w-full md:w-auto px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-center">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Mettre à jour
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Gestion des champs d'équipements
function addEquipmentField() {
    const container = document.getElementById('equipment-container');
    const div = document.createElement('div');
    div.className = 'flex items-center';
    div.innerHTML = `
        <input type="text" 
               name="equipment_list[]" 
               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
               placeholder="Ex: Routeurs, Points d'accès Wi-Fi, etc.">
        <button type="button" 
                onclick="removeEquipmentField(this)"
                class="ml-3 text-red-600 hover:text-red-800 p-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    container.appendChild(div);
    
    // Focus sur le nouveau champ
    const input = div.querySelector('input');
    input.focus();
}

function removeEquipmentField(button) {
    const container = document.getElementById('equipment-container');
    const fields = container.querySelectorAll('div');
    
    // Ne pas supprimer s'il ne reste qu'un champ
    if (fields.length > 1) {
        button.parentElement.remove();
    } else {
        // Réinitialiser le champ restant
        const input = fields[0].querySelector('input');
        input.value = '';
    }
}
</script>

<style>
/* Styles pour les champs du formulaire */
input:focus, textarea:focus, select:focus {
    outline: none;
    ring-width: 2px;
}

/* Animation pour les nouveaux champs */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#equipment-container div {
    animation: slideIn 0.3s ease-out;
}

/* Style pour les messages d'erreur */
.text-red-600 {
    color: #dc2626;
}

.border-red-500 {
    border-color: #dc2626;
}

/* Responsive */
@media (max-width: 768px) {
    .grid-cols-2 {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection