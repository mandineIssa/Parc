@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <p class="text-muted">Importez directement des équipements dans le parc avec leurs affectations</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Importer un fichier CSV</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                            @if(session('import_errors'))
                                <ul class="mb-0 mt-2">
                                    @foreach(session('import_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('parc.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Fichier CSV</label>
                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                            <div class="form-text">
                                Format accepté: CSV (séparateur tabulation ou point-virgule). Taille max: 10MB
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading">📋 Instructions :</h6>
                            <ul class="mb-0">
                                <li>Téléchargez le <a href="{{ route('parc.import.template') }}" class="alert-link">template CSV</a> pour voir le format attendu</li>
                                <li>Les équipements seront importés directement avec le statut "parc"</li>
                                <li>Si un utilisateur_id est fourni, l'équipement sera affecté à cet utilisateur</li>
                                <li>Les colonnes optionnelles peuvent être laissées vides</li>
                            </ul>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i> Importer
                        </button>
                        <a href="{{ route('parc.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Retour au parc
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Informations importantes</h5>
                </div>
                <div class="card-body">
                    <h6>Colonnes obligatoires :</h6>
                    <ul>
                        <li><strong>type</strong> : Type d'équipement</li>
                        <li><strong>numero_serie</strong> : Numéro de série (unique)</li>
                    </ul>

                    <h6>Colonnes d'affectation :</h6>
                    <ul>
                        <li><strong>utilisateur_id</strong> : ID de l'utilisateur</li>
                        <li><strong>departement</strong> : Département d'affectation</li>
                        <li><strong>poste_affecte</strong> : Poste concerné</li>
                        <li><strong>date_affectation</strong> : Date d'affectation</li>
                    </ul>

                    <div class="mt-3">
                        <a href="{{ route('parc.import.template') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-1"></i> Télécharger le template
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection