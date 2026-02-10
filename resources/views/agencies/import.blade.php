@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Importation d'agences</h1>
            <p class="text-gray-600 mt-2">Importez des agences depuis un fichier Excel</p>
        </div>
        <a href="{{ route('agencies.index') }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour à la liste
        </a>
    </div>

    <!-- Messages d'erreur d'importation -->
    @if(session('import_errors'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <h4 class="font-bold mb-2">❌ Erreurs d'importation :</h4>
            <ul class="list-disc pl-5">
                @foreach(session('import_errors') as $error)
                    <li class="text-sm mb-1">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulaire d'importation -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="{{ route('agency.import.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="file" class="block text-gray-700 text-sm font-bold mb-2">
                            Fichier Excel (.xlsx, .xls, .csv) *
                        </label>
                        <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition">
                            <input type="file" name="file" id="file" 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                   accept=".xlsx,.xls,.csv"
                                   required>
                            <div class="space-y-2">
                                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Cliquez pour télécharger</span> ou glissez-déposez
                                </p>
                                <p class="text-xs text-gray-500">Excel ou CSV jusqu'à 10MB</p>
                            </div>
                        </div>
                        <div id="fileName" class="mt-2 text-sm text-gray-600 hidden"></div>
                        @error('file')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Mode d'importation
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="mode" value="skip" checked class="mr-2">
                                <span class="text-sm">Ignorer les doublons</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="mode" value="update" class="mr-2">
                                <span class="text-sm">Mettre à jour les doublons (non disponible)</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline flex items-center"
                                id="submitBtn">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Importer
                        </button>
                        
                        <a href="{{ route('agency.import.template') }}" 
                           class="text-green-600 hover:text-green-900 inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Télécharger le template
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Instructions et format attendu -->
        <div class="lg:col-span-1">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="font-bold text-gray-800 mb-4">📋 Instructions d'importation</h3>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Format du fichier</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Format Excel (.xlsx) ou CSV</li>
                            <li>• Première ligne = en-têtes des colonnes</li>
                            <li>• Taille maximale : 10MB</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-700 mb-2">Colonnes attendues</h4>
                        <div class="bg-white border border-gray-200 rounded p-3">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-1 px-2 text-left font-medium">Colonne</th>
                                        <th class="py-1 px-2 text-left font-medium">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expectedHeaders as $key => $description)
                                    <tr class="border-t">
                                        <td class="py-1 px-2 font-mono">{{ $key }}</td>
                                        <td class="py-1 px-2">{{ $description }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded p-3">
                        <h4 class="font-medium text-blue-700 mb-1">💡 Conseils</h4>
                        <ul class="text-sm text-blue-600 space-y-1">
                            <li>• Utilisez le template fourni</li>
                            <li>• Vérifiez les données avant import</li>
                            <li>• Les doublons seront ignorés</li>
                            <li>• Exportez les erreurs pour correction</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exemple de données -->
    <div class="mt-8 bg-white shadow-md rounded-lg p-6">
        <h3 class="font-bold text-gray-800 mb-4">📊 Exemple de données valides</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        @foreach(array_keys($expectedHeaders) as $header)
                        <th class="py-2 px-3 text-left font-medium border">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-2 px-3 border">AG001</td>
                        <td class="py-2 px-3 border">Siège Social</td>
                        <td class="py-2 px-3 border">Dakar</td>
                        <td class="py-2 px-3 border">Plateau, Immeuble Alpha</td>
                        <td class="py-2 px-3 border">+221 33 821 00 00</td>
                        <td class="py-2 px-3 border">siege@entreprise.sn</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="py-2 px-3 border">AG002</td>
                        <td class="py-2 px-3 border">Agence Dakar Médina</td>
                        <td class="py-2 px-3 border">Dakar</td>
                        <td class="py-2 px-3 border">Médina, Rue 10 x 12</td>
                        <td class="py-2 px-3 border">+221 33 821 01 00</td>
                        <td class="py-2 px-3 border">dakar-medina@entreprise.sn</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const fileName = document.getElementById('fileName');
    const submitBtn = document.getElementById('submitBtn');
    const importForm = document.getElementById('importForm');
    
    // Afficher le nom du fichier sélectionné
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileName.textContent = 'Fichier sélectionné : ' + this.files[0].name;
            fileName.classList.remove('hidden');
        } else {
            fileName.classList.add('hidden');
        }
    });
    
    // Validation avant soumission
    importForm.addEventListener('submit', function(e) {
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Veuillez sélectionner un fichier à importer.');
            return;
        }
        
        // Désactiver le bouton pendant l'import
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Importation en cours...
        `;
    });
    
    // Zone de drop
    const dropZone = fileInput.parentElement;
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-blue-500', 'bg-blue-50');
    });
    
    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-500', 'bg-blue-50');
    });
    
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-500', 'bg-blue-50');
        
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            
            // Déclencher l'événement change
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
    });
});
</script>
@endsection