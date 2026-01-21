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
    
    /* Pour les boutons outline, conserver la couleur d'origine */
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
    
    /* Bouton light spécial */
    .btn-light i {
        color: #212529 !important;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Gestion des Fournisseurs</h4>
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouveau Fournisseur
                </a>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total</h5>
                            <p class="h2">{{ $stats['total'] ?? 0 }}</p>
                            <small>Fournisseurs</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Actifs</h5>
                            <p class="h2">{{ $stats['active'] ?? 0 }}</p>
                            <small>Fournisseurs actifs</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">En attente</h5>
                            <p class="h2">{{ $stats['pending'] ?? 0 }}</p>
                            <small>Fournisseurs en attente</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Avec Équipements</h5>
                            <p class="h2">{{ $stats['with_equipment'] ?? 0 }}</p>
                            <small>Fournisseurs avec équipements</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Liste des Fournisseurs</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">ID</th>
                                    <th>Nom</th>
                                    <th>Contact</th>
                                    <th>Téléphone/Email</th>
                                    <th>Ville/Adresse</th>
                                    <th width="100">Statut</th>
                                    <th width="100">Équipements</th>
                                    <th width="150" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suppliers as $supplier)
                                <tr>
                                    <td class="fw-bold">{{ $supplier->id }}</td>
                                    <td>
                                        <strong>{{ $supplier->nom }}</strong>
                                        @if($supplier->website)
                                            <br>
                                            <small>
                                                <a href="{{ $supplier->website }}" target="_blank" class="text-decoration-none">
                                                    <i class="fas fa-external-link-alt me-1"></i>
                                                    Site web
                                                </a>
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $supplier->contact ?? '-' }}</td>
                                    <td>
                                        @if($supplier->telephone)
                                            <div class="mb-1">
                                                <i class="fas fa-phone text-primary me-1"></i>
                                                <a href="tel:{{ $supplier->telephone }}" class="text-decoration-none">
                                                    {{ $supplier->telephone }}
                                                </a>
                                            </div>
                                        @endif
                                        @if($supplier->email)
                                            <div>
                                                <i class="fas fa-envelope text-primary me-1"></i>
                                                <a href="mailto:{{ $supplier->email }}" class="text-decoration-none">
                                                    {{ Str::limit($supplier->email, 20) }}
                                                </a>
                                            </div>
                                        @endif
                                        @if(!$supplier->telephone && !$supplier->email)
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($supplier->ville)
                                            <div class="mb-1">
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                {{ $supplier->ville }}
                                            </div>
                                        @endif
                                        @if($supplier->adresse)
                                            <div>
                                                <small class="text-muted">
                                                    {{ Str::limit($supplier->adresse, 30) }}
                                                </small>
                                            </div>
                                        @endif
                                        @if(!$supplier->ville && !$supplier->adresse)
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
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
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            {{ $supplier->equipment_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('suppliers.show', $supplier) }}" 
                                               class="btn btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('suppliers.edit', $supplier) }}" 
                                               class="btn btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('suppliers.destroy', $supplier) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-truck fa-2x mb-2"></i>
                                            <p>Aucun fournisseur trouvé.</p>
                                            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Créer un fournisseur
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Affichage de {{ $suppliers->firstItem() }} à {{ $suppliers->lastItem() }} 
                            sur {{ $suppliers->total() }} fournisseurs
                        </div>
                        <div>
                            {{ $suppliers->links() }}
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

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: "{{ session('error') }}",
            confirmButtonText: 'OK'
        });
    });
</script>
@endif
@endsection