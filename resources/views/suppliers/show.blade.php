@extends('layouts.app')

@section('content')
<style>
    /* Correction pour les icônes blanches */
    .btn-primary i,
    .btn-secondary i,
    .btn-success i,
    .btn-danger i,
    .btn-warning i,
    .btn-info i,
    .btn-dark i {
        color: white !important;
    }
    
    .btn-outline-primary i,
    .btn-outline-secondary i,
    .btn-outline-success i,
    .btn-outline-danger i,
    .btn-outline-warning i,
    .btn-outline-info i,
    .btn-outline-light i,
    .btn-outline-dark i {
        color: inherit !important;
    }
    
    .btn-light i {
        color: #212529 !important;
    }

    .info-row {
        border-bottom: 1px solid #eee;
        padding: 12px 0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #495057;
    }

    .info-value {
        color: #212529;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Détails du Fournisseur</h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Informations principales -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Informations Générales
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-row row">
                                <div class="col-md-4 info-label">
                                    <i class="fas fa-building text-primary me-2"></i>
                                    Nom du Fournisseur
                                </div>
                                <div class="col-md-8 info-value">
                                    {{ $supplier->nom }}
                                </div>
                            </div>

                            <div class="info-row row">
                                <div class="col-md-4 info-label">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    Personne de Contact
                                </div>
                                <div class="col-md-8 info-value">
                                    {{ $supplier->contact ?? '-' }}
                                </div>
                            </div>

                            <div class="info-row row">
                                <div class="col-md-4 info-label">
                                    <i class="fas fa-toggle-on text-primary me-2"></i>
                                    Statut
                                </div>
                                <div class="col-md-8 info-value">
                                    @if($supplier->status == 'active')
                                        <span class="badge bg-success rounded-pill px-3">
                                            <i class="fas fa-check-circle me-1"></i> Actif
                                        </span>
                                    @elseif($supplier->status == 'inactive')
                                        <span class="badge bg-danger rounded-pill px-3">
                                            <i class="fas fa-times-circle me-1"></i> Inactif
                                        </span>
                                    @else
                                        <span class="badge bg-warning rounded-pill px-3">
                                            <i class="fas fa-clock me-1"></i> En attente
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="info-row row">
                                <div class="col-md-4 info-label">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    Email
                                </div>
                                <div class="col-md-8 info-value">
                                    @if($supplier->email)
                                        <a href="mailto:{{ $supplier->email }}" class="text-decoration-none">
                                            {{ $supplier->email }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="info-row row">
                                <div class="col-md-4 info-label">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    Téléphone
                                </div>
                                <div class="col-md-8 info-value">
                                    @if($supplier->telephone)
                                        <a href="tel:{{ $supplier->telephone }}" class="text-decoration-none">
                                            {{ $supplier->telephone }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="info-row row">
                                <div class="col-md-4 info-label">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    Ville
                                </div>
                                <div class="col-md-8 info-value">
                                    {{ $supplier->ville ?? '-' }}
                                </div>
                            </div>

                            <div class="info-row row">
                                <div class="col-md-4 info-label">
                                    <i class="fas fa-home text-primary me-2"></i>
                                    Adresse
                                </div>
                                <div class="col-md-8 info-value">
                                    {{ $supplier->adresse ?? '-' }}
                                </div>
                            </div>

                            <div class="info-row row">
                                <div class="col-md-4 info-label">
                                    <i class="fas fa-globe text-primary me-2"></i>
                                    Site Web
                                </div>
                                <div class="col-md-8 info-value">
                                    @if($supplier->website)
                                        <a href="{{ $supplier->website }}" target="_blank" class="text-decoration-none">
                                            {{ $supplier->website }}
                                            <i class="fas fa-external-link-alt ms-1"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            @if($supplier->notes)
                            <div class="info-row row">
                                <div class="col-md-4 info-label">
                                    <i class="fas fa-sticky-note text-primary me-2"></i>
                                    Notes
                                </div>
                                <div class="col-md-8 info-value">
                                    {{ $supplier->notes }}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Équipements associés -->
                    <div class="card mt-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-desktop me-2"></i>
                                Équipements Associés
                                <span class="badge bg-white text-info ms-2">{{ $supplier->equipment_count }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($supplier->equipment && $supplier->equipment->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nom</th>
                                                <th>Numéro de Série</th>
                                                <th>Catégorie</th>
                                                <th>Statut</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($supplier->equipment as $equipment)
                                            <tr>
                                                <td>
                                                    <strong>{{ $equipment->nom }}</strong>
                                                </td>
                                                <td>{{ $equipment->numero_serie ?? '-' }}</td>
                                                <td>
                                                    @if($equipment->category)
                                                        <span class="badge bg-secondary">
                                                            {{ $equipment->category->nom }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($equipment->status == 'disponible')
                                                        <span class="badge bg-success">Disponible</span>
                                                    @elseif($equipment->status == 'en_service')
                                                        <span class="badge bg-info">En Service</span>
                                                    @elseif($equipment->status == 'en_maintenance')
                                                        <span class="badge bg-warning">En Maintenance</span>
                                                    @elseif($equipment->status == 'hors_service')
                                                        <span class="badge bg-danger">Hors Service</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $equipment->status }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('equipment.show', $equipment) }}" 
                                                       class="btn btn-sm btn-info" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-desktop fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">Aucun équipement associé à ce fournisseur.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Panneau latéral -->
                <div class="col-lg-4">
                    <!-- Statistiques -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-pie me-2"></i>
                                Statistiques
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                <div>
                                    <i class="fas fa-desktop fa-2x text-primary"></i>
                                </div>
                                <div class="text-end">
                                    <h3 class="mb-0">{{ $supplier->equipment_count }}</h3>
                                    <small class="text-muted">Équipements</small>
                                </div>
                            </div>

                            @if($supplier->equipment && $supplier->equipment->count() > 0)
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-2">Par statut:</small>
                                    @php
                                        $statusCounts = $supplier->equipment->groupBy('status')->map->count();
                                    @endphp
                                    @foreach($statusCounts as $status => $count)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                            <span class="badge bg-secondary">{{ $count }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions rapides -->
                    <div class="card mt-4">
                        <div class="card-header bg-warning text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                Actions Rapides
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i> Modifier
                                </a>
                                
                                @if($supplier->email)
                                <a href="mailto:{{ $supplier->email }}" class="btn btn-outline-primary">
                                    <i class="fas fa-envelope me-2"></i> Envoyer un Email
                                </a>
                                @endif

                                @if($supplier->telephone)
                                <a href="tel:{{ $supplier->telephone }}" class="btn btn-outline-success">
                                    <i class="fas fa-phone me-2"></i> Appeler
                                </a>
                                @endif

                                @if($supplier->website)
                                <a href="{{ $supplier->website }}" target="_blank" class="btn btn-outline-info">
                                    <i class="fas fa-globe me-2"></i> Visiter le Site
                                </a>
                                @endif

                                <hr>

                                <form action="{{ route('suppliers.destroy', $supplier) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur? Cette action est irréversible.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash me-2"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="card mt-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calendar me-2"></i>
                                Informations Système
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted d-block">Date de création</small>
                                <strong>{{ $supplier->created_at->format('d/m/Y H:i') }}</strong>
                            </div>
                            <div>
                                <small class="text-muted d-block">Dernière modification</small>
                                <strong>{{ $supplier->updated_at->format('d/m/Y H:i') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToast('success', "{{ session('success') }}");
    });
    
    function showToast(type, message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        
        Toast.fire({
            icon: type,
            title: message
        });
    }
</script>
@endif
@endsection