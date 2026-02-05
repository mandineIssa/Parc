@extends('layouts.app')

@section('title', 'Importation Équipements')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- En-tête -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-file-import me-2"></i>
                        Importation d'Équipements
                    </h3>
                </div>
            </div>

            <!-- Messages de succès/erreur -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>{{ session('success') }}</strong>
                    @if(session('stats'))
                        <ul class="mt-2 mb-0">
                            <li>Équipements importés : <strong>{{ session('stats')['equipment_imported'] }}</strong></li>
                            <li>Détails équipements importés : <strong>{{ session('stats')['equipment_details_imported'] }}</strong></li>
                            <li>Stocks importés : <strong>{{ session('stats')['stock_imported'] }}</strong></li>
                            <li>Affectations importées : <strong>{{ session('stats')['parc_imported'] }}</strong></li>
                        </ul>
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>{{ session('warning') }}</strong>
                    @if(session('stats'))
                        <ul class="mt-2 mb-0">
                            <li>Équipements importés : <strong>{{ session('stats')['equipment_imported'] }}</strong></li>
                            <li>Détails équipements importés : <strong>{{ session('stats')['equipment_details_imported'] }}</strong></li>
                            <li>Stocks importés : <strong>{{ session('stats')['stock_imported'] }}</strong></li>
                            <li>Affectations importées : <strong>{{ session('stats')['parc_imported'] }}</strong></li>
                        </ul>
                        @if(count(session('stats')['errors']) > 0)
                            <hr>
                            <strong>Erreurs détectées :</strong>
                            <ul class="mt-2">
                                @foreach(session('stats')['errors'] as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-times-circle me-2"></i>
                    <strong>Erreur :</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erreurs de validation :</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Instructions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Instructions
                    </h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">
                            <strong>Téléchargez le template Excel</strong> en cliquant sur le bouton ci-dessous
                        </li>
                        <li class="mb-2">
                            <strong>Remplissez le template</strong> avec vos données :
                            <ul class="mt-1">
                                <li>Onglet <strong>EQUIPMENT</strong> : Informations principales (obligatoire)</li>
                                <li>Onglet <strong>EQUIPMENT_DETAILS</strong> : Détails et classification (optionnel)</li>
                                <li>Onglet <strong>STOCK</strong> : Gestion du stock (optionnel)</li>
                                <li>Onglet <strong>PARC</strong> : Affectations utilisateurs (optionnel)</li>
                            </ul>
                        </li>
                        <li class="mb-2">
                            <strong>Respectez les formats</strong> indiqués dans la ligne de description
                        </li>
                        <li class="mb-2">
                            <strong>Uploadez le fichier</strong> rempli pour l'importation
                        </li>
                    </ol>

                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important :</strong> Les numéros de série doivent être uniques. Les doublons seront ignorés.
                    </div>
                </div>
            </div>

            <!-- Télécharger le template -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-download me-2"></i>
                        Étape 1 : Télécharger le Template
                    </h5>
                </div>
                <div class="card-body text-center py-4">
                    <p class="mb-3">Téléchargez le template Excel pré-formaté avec toutes les colonnes nécessaires</p>
                    <a href="{{ route('equipment.imports.template') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-file-excel me-2"></i>
                        Télécharger le Template
                    </a>
                </div>
            </div>

            <!-- Formulaire d'import -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-upload me-2"></i>
                        Étape 2 : Importer le Fichier
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('equipment.imports.process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="excel_file" class="form-label fw-bold">
                                Sélectionnez votre fichier Excel (.xlsx ou .xls)
                            </label>
                            <input type="file" 
                                   class="form-control @error('excel_file') is-invalid @enderror" 
                                   id="excel_file" 
                                   name="excel_file" 
                                   accept=".xlsx,.xls"
                                   required>
                            @error('excel_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Taille maximale : 10 MB. Formats acceptés : .xlsx, .xls
                            </div>
                        </div>

                        <div id="fileInfo" class="alert alert-info d-none mb-4">
                            <strong>Fichier sélectionné :</strong> <span id="fileName"></span><br>
                            <strong>Taille :</strong> <span id="fileSize"></span>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-redo me-2"></i>
                                Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-cloud-upload-alt me-2"></i>
                                Lancer l'Importation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Loader pendant l'import -->
            <div id="loadingOverlay" class="d-none">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-3 text-white fw-bold">Importation en cours...</p>
                    <p class="text-white">Veuillez patienter, cela peut prendre quelques instants.</p>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('excel_file');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const importForm = document.getElementById('importForm');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Afficher les infos du fichier sélectionné
    fileInput.addEventListener('change', function(e) {
        if (this.files.length > 0) {
            const file = this.files[0];
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024).toFixed(2) + ' KB';
            fileInfo.classList.remove('d-none');
        } else {
            fileInfo.classList.add('d-none');
        }
    });

    // Afficher le loader lors de la soumission
    importForm.addEventListener('submit', function(e) {
        if (fileInput.files.length > 0) {
            loadingOverlay.classList.remove('d-none');
        }
    });

    // Réinitialiser
    importForm.addEventListener('reset', function() {
        fileInfo.classList.add('d-none');
    });
});
</script>
@endsection