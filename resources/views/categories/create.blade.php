@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Créer une nouvelle catégorie</h1>
            <p class="text-gray-600 mt-2">Ajoutez une nouvelle catégorie pour organiser vos équipements</p>
        </div>
        <a href="{{ route('categories.index') }}" 
           class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center mt-4 md:mt-0">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour à la liste
        </a>
    </div>

    <!-- Formulaire -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <h2 class="text-lg font-semibold text-gray-800">Informations de la catégorie</h2>
            <p class="text-sm text-gray-600 mt-1">Remplissez les champs ci-dessous pour créer une nouvelle catégorie</p>
        </div>

        <form action="{{ route('categories.store') }}" method="POST" class="p-6">
            @csrf

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
                            <option value="réseaux" {{ old('type') == 'réseaux' ? 'selected' : '' }}>Réseaux</option>
                            <option value="électronique" {{ old('type') == 'électronique' ? 'selected' : '' }}>Électronique</option>
                            <option value="informatiques" {{ old('type') == 'informatiques' ? 'selected' : '' }}>Informatiques</option>
                        </select>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 text-sm text-gray-500">
                            Sélectionnez le type principal de la catégorie
                        </div>
                    </div>

                    <!-- Nom -->
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom de la catégorie *
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nom" name="nom" value="{{ old('nom') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('nom') border-red-500 @enderror"
                               placeholder="Ex: Connectivité & Transmission"
                               required>
                        @error('nom')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Catégorie parente (si création de sous-catégorie) -->
                @if(request()->has('parent_id'))
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Catégorie parente
                    </label>
                    <select id="parent_id" name="parent_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <option value="">Aucune (catégorie principale)</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', request('parent_id')) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->nom }} ({{ $parent->type_label }})
                            </option>
                        @endforeach
                    </select>
                    <div class="mt-2 text-sm text-gray-500">
                        Si cette catégorie est une sous-catégorie, sélectionnez sa catégorie parente
                    </div>
                </div>
                @endif

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('description') border-red-500 @enderror"
                              placeholder="Décrivez cette catégorie (ex: Équipements de connectivité et transmission réseau)...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-2 text-sm text-gray-500">
                        Décrivez le type d'équipements que cette catégorie contient
                    </div>
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
                        @if(old('equipment_list'))
                            @foreach(old('equipment_list') as $index => $equipment)
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

                <!-- Exemples par type -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">Exemples de catégories</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <h4 class="font-medium text-blue-900 mb-2">Réseaux</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Connectivité & Transmission</li>
                                <li>• Sécurité Réseau</li>
                                <li>• Infrastructure & Support</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-blue-900 mb-2">Électronique</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Vidéosurveillance (CCTV)</li>
                                <li>• Contrôle d'accès</li>
                                <li>• Systèmes d'alarme</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-blue-900 mb-2">Informatiques</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Postes Utilisateurs</li>
                                <li>• Périphériques</li>
                                <li>• Serveurs & Stockage</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex flex-col-reverse md:flex-row gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('categories.index') }}" 
                       class="w-full md:w-auto px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-center">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Créer la catégorie
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

// Auto-suggestion basée sur le type sélectionné
document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    const nameInput = document.getElementById('nom');
    const descriptionInput = document.getElementById('description');
    
    const suggestions = {
        'réseaux': {
            nom: 'Connectivité & Transmission',
            description: 'Équipements de connectivité et transmission réseau'
        },
        'électronique': {
            nom: 'Vidéosurveillance (CCTV)',
            description: 'Systèmes de vidéosurveillance et sécurité vidéo'
        },
        'informatiques': {
            nom: 'Postes Utilisateurs',
            description: 'Équipements de postes de travail utilisateurs'
        }
    };
    
    if (suggestions[type] && !nameInput.value) {
        nameInput.value = suggestions[type].nom;
        descriptionInput.value = suggestions[type].description;
    }
});
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
    
    .grid-cols-3 {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection