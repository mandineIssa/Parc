{{-- resources/views/equipment/import.blade.php --}}
@extends('layouts.app')
@section('title', 'Importer des Équipements')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        {{-- En-tête --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">📥 Importer des Équipements</h1>
            <p class="text-gray-600">Importez plusieurs équipements en une seule fois via un fichier CSV</p>
        </div>

        {{-- Messages --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                <div class="flex items-center">
                    <span class="text-green-700 font-medium">✅ {{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg">
                <div class="flex items-center mb-2">
                    <span class="text-yellow-700 font-medium">⚠️ {{ session('warning') }}</span>
                </div>
                @if(session('import_errors'))
                    <div class="mt-3 p-3 bg-white rounded border border-yellow-200">
                        <p class="text-sm font-medium text-gray-700 mb-2">Détails des erreurs :</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            @foreach(session('import_errors') as $error)
                                <li class="flex items-start">
                                    <span class="text-red-500 mr-2">•</span>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                <div class="flex items-center">
                    <span class="text-red-700 font-medium">❌ {{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Instructions --}}
        <div class="mb-8 bg-blue-50 rounded-lg p-6">
            <h2 class="text-lg font-semibold text-blue-900 mb-3">📋 Instructions</h2>
            <ol class="space-y-2 text-blue-800">
                <li class="flex items-start">
                    <span class="font-bold mr-2">1.</span>
                    <span>Téléchargez le template CSV ci-dessous</span>
                </li>
                <li class="flex items-start">
                    <span class="font-bold mr-2">2.</span>
                    <span>Remplissez le fichier avec vos données (utilisez les exemples comme référence)</span>
                </li>
                <li class="flex items-start">
                    <span class="font-bold mr-2">3.</span>
                    <span>Sauvegardez le fichier au format CSV (séparateur point-virgule ";")</span>
                </li>
                <li class="flex items-start">
                    <span class="font-bold mr-2">4.</span>
                    <span>Importez le fichier via le formulaire ci-dessous</span>
                </li>
            </ol>

            <div class="mt-4 p-3 bg-white rounded border border-blue-200">
                <p class="text-sm text-blue-700">
                    <strong>💡 Astuce :</strong> Le template contient 4 exemples complets (Switch, Caméra, PC Portable, Logiciel) 
                    pour vous guider. Vous pouvez les modifier ou les supprimer.
                </p>
            </div>
        </div>

        {{-- Télécharger le template --}}
        <div class="mb-8 bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">📄 Télécharger le Template</h2>
            
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">template_import_equipements.csv</h3>
                        <p class="text-sm text-gray-600">Template avec 4 exemples pré-remplis</p>
                    </div>
                </div>
                
                <a href="{{ route('equipment.import.template') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Télécharger
                </a>
            </div>

            {{-- Format du fichier --}}
            <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <h4 class="font-medium text-yellow-900 mb-2">⚙️ Format du fichier CSV :</h4>
                <ul class="text-sm text-yellow-800 space-y-1">
                    <li>• Encodage : <strong>UTF-8</strong></li>
                    <li>• Séparateur : <strong>Point-virgule (;)</strong></li>
                    <li>• Taille max : <strong>10 MB</strong></li>
                    <li>• Extension : <strong>.csv ou .txt</strong></li>
                </ul>
            </div>
        </div>

        {{-- Formulaire d'import --}}
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">📤 Importer le Fichier</h2>
            
            <form action="{{ route('equipment.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Fichier CSV *
                    </label>
                    
                    <div class="flex items-center justify-center w-full">
                        <label for="csv_file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6" id="file-upload-content">
                                <svg class="w-12 h-12 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500">
                                    <span class="font-semibold">Cliquez pour sélectionner</span> ou glissez-déposez
                                </p>
                                <p class="text-xs text-gray-500">CSV ou TXT (max 10 MB)</p>
                            </div>
                            
                            <div class="hidden items-center justify-center" id="file-selected-content">
                                <div class="text-center">
                                    <svg class="w-16 h-16 mx-auto mb-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="font-semibold text-gray-700" id="file-name"></p>
                                    <p class="text-sm text-gray-500" id="file-size"></p>
                                    <button type="button" onclick="resetFileInput()" class="mt-3 text-red-600 hover:text-red-700 text-sm font-medium">
                                        Changer de fichier
                                    </button>
                                </div>
                            </div>
                            
                            <input id="csv_file" name="csv_file" type="file" class="hidden" accept=".csv,.txt" required onchange="handleFileSelect(this)" />
                        </label>
                    </div>
                    
                    @error('csv_file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Champs obligatoires --}}
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-gray-700 mb-3">📌 Champs obligatoires dans le CSV :</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600">
                        <div class="flex items-center">
                            <span class="text-red-500 mr-2">•</span>
                            <span><strong>type</strong> - Type d'équipement</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-red-500 mr-2">•</span>
                            <span><strong>categorie</strong> - Catégorie</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-red-500 mr-2">•</span>
                            <span><strong>sous_categorie</strong> - Sous-catégorie</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-red-500 mr-2">•</span>
                            <span><strong>numero_serie</strong> - Numéro de série (unique)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-red-500 mr-2">•</span>
                            <span><strong>marque</strong> - Marque (sauf Logiciel)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-red-500 mr-2">•</span>
                            <span><strong>modele</strong> - Modèle (sauf Logiciel)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-red-500 mr-2">•</span>
                            <span><strong>date_livraison</strong> - Format: AAAA-MM-JJ</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-red-500 mr-2">•</span>
                            <span><strong>prix</strong> - Prix en FCFA</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-red-500 mr-2">•</span>
                            <span><strong>etat</strong> - État (neuf, bon, moyen, mauvais)</span>
                        </div>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex gap-4">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition flex items-center"
                            id="submitBtn">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Importer
                    </button>
                    
                    <a href="{{ route('equipment.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition">
                        Annuler
                    </a>
                </div>
            </form>
        </div>

        {{-- Aide --}}
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold text-gray-800 mb-3">❓ Besoin d'aide ?</h3>
            <div class="space-y-2 text-sm text-gray-600">
                <p><strong>Q: Que se passe-t-il si un équipement existe déjà ?</strong></p>
                <p class="ml-4">R: L'équipement sera ignoré et signalé dans les erreurs. Le numéro de série doit être unique.</p>
                
                <p class="mt-3"><strong>Q: Puis-je importer des types différents dans le même fichier ?</strong></p>
                <p class="ml-4">R: Oui ! Vous pouvez mélanger Réseau, Électronique, Informatique et Logiciel dans le même CSV.</p>
                
                <p class="mt-3"><strong>Q: Comment gérer les champs avec des virgules ?</strong></p>
                <p class="ml-4">R: Utilisez des guillemets doubles autour du texte : "Intel Core i5, 2.4GHz"</p>
            </div>
        </div>
    </div>
</div>

<script>
function handleFileSelect(input) {
    const uploadContent = document.getElementById('file-upload-content');
    const selectedContent = document.getElementById('file-selected-content');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Afficher les détails du fichier
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        
        // Basculer l'affichage
        uploadContent.classList.add('hidden');
        selectedContent.classList.remove('hidden');
        selectedContent.classList.add('flex');
    }
}

function resetFileInput() {
    const input = document.getElementById('csv_file');
    const uploadContent = document.getElementById('file-upload-content');
    const selectedContent = document.getElementById('file-selected-content');
    
    input.value = '';
    
    uploadContent.classList.remove('hidden');
    selectedContent.classList.add('hidden');
    selectedContent.classList.remove('flex');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Validation avant soumission
document.getElementById('importForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('csv_file');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!fileInput.files || !fileInput.files[0]) {
        e.preventDefault();
        alert('Veuillez sélectionner un fichier CSV à importer');
        return false;
    }
    
    // Désactiver le bouton et afficher un message
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Import en cours...
    `;
});
</script>
@endsection