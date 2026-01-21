{{-- resources/views/attachments/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="fas fa-paperclip me-2"></i>
                    Fichiers attachés - Approbation #{{ $approval->id }}
                </h1>
                <div>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                </div>
            </div>
            <div class="text-muted mt-2">
                Équipement: {{ $approval->equipment->name ?? 'N/A' }} | 
                Statut: 
                <span class="badge bg-{{ $approval->status === 'approved' ? 'success' : ($approval->status === 'pending' ? 'warning' : 'danger') }}">
                    {{ $approval->status === 'approved' ? 'Approuvé' : ($approval->status === 'pending' ? 'En attente' : 'Rejeté') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Messages d'alerte --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Section des fichiers existants --}}
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-files me-2"></i>
                Fichiers attachés ({{ count($attachments) }})
            </h5>
            @if(count($attachments) > 0)
                <span class="badge bg-primary">
                    {{ calculateTotalSize($attachments) }}
                </span>
            @endif
        </div>
        <div class="card-body p-0">
            @if(count($attachments) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Nom du fichier</th>
                                <th>Fichier original</th>
                                <th width="120">Taille</th>
                                <th width="150">Date d'ajout</th>
                                <th width="180">Ajouté par</th>
                                <th width="150" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attachments as $index => $attachment)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $icon = 'fa-file';
                                            $extension = strtolower($attachment['extension'] ?? pathinfo($attachment['original_name'], PATHINFO_EXTENSION));
                                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'])) {
                                                $icon = 'fa-file-image';
                                            } elseif (in_array($extension, ['pdf'])) {
                                                $icon = 'fa-file-pdf';
                                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                                $icon = 'fa-file-word';
                                            } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                                $icon = 'fa-file-excel';
                                            } elseif (in_array($extension, ['ppt', 'pptx'])) {
                                                $icon = 'fa-file-powerpoint';
                                            } elseif (in_array($extension, ['txt'])) {
                                                $icon = 'fa-file-alt';
                                            } elseif (in_array($extension, ['zip', 'rar', '7z'])) {
                                                $icon = 'fa-file-archive';
                                            }
                                        @endphp
                                        <i class="fas {{ $icon }} text-primary me-2"></i>
                                        <div>
                                            <strong>{{ $attachment['name'] }}</strong>
                                            @if($attachment['is_replacement'] ?? false)
                                                <span class="badge bg-warning text-dark ms-2" title="Fichier remplacé">
                                                    <i class="fas fa-exchange-alt"></i> Remplacé
                                                </span>
                                            @endif
                                            @if(isset($attachment['replaced_count']) && $attachment['replaced_count'] > 0)
                                                <small class="text-muted d-block">
                                                    A remplacé {{ $attachment['replaced_count'] }} version(s) précédente(s)
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">
                                    <small>{{ $attachment['original_name'] }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $attachment['size_formatted'] ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $attachment['date'] }}
                                        @if(isset($attachment['replaced_at']))
                                            <br>
                                            <span class="text-warning" title="Date de remplacement">
                                                <i class="fas fa-history"></i> {{ date('d/m/Y H:i', strtotime($attachment['replaced_at'])) }}
                                            </span>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        <i class="fas fa-user text-muted me-1"></i>
                                        {{ $attachment['uploaded_by_name'] ?? 'Système' }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ $attachment['file'] }}" target="_blank" 
                                           class="btn btn-outline-primary" 
                                           title="Voir le fichier">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('transitions.attachments.download', [
                                            'approval' => $approval->id,
                                            'file_url' => urlencode($attachment['file'])
                                        ]) }}" 
                                           class="btn btn-outline-success"
                                           title="Télécharger">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger delete-attachment"
                                                data-file-url="{{ $attachment['file'] }}"
                                                data-attachment-id="{{ $attachment['id'] }}"
                                                data-attachment-name="{{ $attachment['name'] }}"
                                                data-approval-id="{{ $approval->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-folder-open fa-3x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Aucun fichier attaché</h5>
                    <p class="text-muted mb-0">Ajoutez votre premier fichier en utilisant le formulaire ci-dessous.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Section d'ajout de fichier --}}
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-upload me-2"></i>
                Ajouter un nouveau fichier
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('transitions.attachments.store', $approval->id) }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  id="uploadForm"
                  onsubmit="return validateFile()">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="attachment_name" class="form-label required">
                            <i class="fas fa-tag me-1"></i>Nom d'affichage
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="attachment_name" 
                               name="attachment_name" 
                               placeholder="Ex: Fiche d'installation" 
                               required>
                        <div class="form-text">Nom qui sera affiché dans la liste</div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="attachment_file" class="form-label required">
                            <i class="fas fa-file me-1"></i>Fichier à uploader
                        </label>
                        <input type="file" 
                               class="form-control" 
                               id="attachment_file" 
                               name="attachment_file" 
                               accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" 
                               required
                               onchange="updateFileName(this)">
                        <div class="form-text">
                            Formats acceptés: JPG, PNG, GIF, PDF, DOC, XLS, PPT, TXT (max 10MB)
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-info-circle fa-lg mt-1"></i>
                        </div>
                        <div>
                            <h6 class="alert-heading mb-2">Information importante</h6>
                           <ul class="mb-0">
                                <li>Si un fichier avec le même nom original existe déjà, il sera automatiquement remplacé.</li>
                                <li>Les anciennes versions seront supprimées définitivement.</li>
                                <li>La taille maximale autorisée est de 10 Mo par fichier.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="form-text">
                        <i class="fas fa-shield-alt me-1"></i>
                        Fichiers stockés de manière sécurisée
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary me-2" onclick="resetForm()">
                            <i class="fas fa-redo me-1"></i> Réinitialiser
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-upload me-1"></i> Uploader le fichier
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal de confirmation pour suppression --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le fichier <strong id="fileNameToDelete"></strong> ?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    Cette action est irréversible. Le fichier sera définitivement supprimé.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Annuler
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash me-1"></i> Supprimer définitivement
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Variables globales pour la suppression
let currentFileUrl = null;
let currentAttachmentId = null;
let currentApprovalId = null;

// Mettre à jour automatiquement le nom d'affichage
function updateFileName(input) {
    const nameInput = document.getElementById('attachment_name');
    if (!nameInput.value && input.files.length > 0) {
        const fileName = input.files[0].name;
        const baseName = fileName.replace(/\.[^/.]+$/, "");
        nameInput.value = baseName;
    }
}

// Valider le fichier avant soumission
function validateFile() {
    const fileInput = document.getElementById('attachment_file');
    const submitBtn = document.getElementById('submitBtn');
    const maxSize = 10 * 1024 * 1024; // 10MB
    
    if (fileInput.files.length === 0) {
        alert('Veuillez sélectionner un fichier');
        return false;
    }
    
    const file = fileInput.files[0];
    
    // Vérifier la taille
    if (file.size > maxSize) {
        alert('Le fichier est trop volumineux. Taille maximale: 10MB');
        return false;
    }
    
    // Vérifier l'extension
    const allowedExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.txt'];
    const fileName = file.name.toLowerCase();
    const isValidExtension = allowedExtensions.some(ext => fileName.endsWith(ext));
    
    if (!isValidExtension) {
        alert('Format de fichier non supporté. Formats acceptés: ' + allowedExtensions.join(', '));
        return false;
    }
    
    // Afficher l'indicateur de chargement
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Upload en cours...';
    submitBtn.disabled = true;
    
    return true;
}

// Réinitialiser le formulaire
function resetForm() {
    document.getElementById('uploadForm').reset();
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-upload me-1"></i> Uploader le fichier';
    document.getElementById('submitBtn').disabled = false;
}

// Gérer la suppression de fichiers
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le modal de suppression
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    // Écouter les clics sur les boutons de suppression
    document.querySelectorAll('.delete-attachment').forEach(button => {
        button.addEventListener('click', function() {
            currentFileUrl = this.getAttribute('data-file-url');
            currentAttachmentId = this.getAttribute('data-attachment-id');
            currentApprovalId = this.getAttribute('data-approval-id');
            const fileName = this.getAttribute('data-attachment-name');
            
            document.getElementById('fileNameToDelete').textContent = fileName;
            deleteModal.show();
        });
    });
    
    // Confirmer la suppression
    document.getElementById('confirmDelete').addEventListener('click', function() {
        deleteModal.hide();
        
        // Afficher l'indicateur de chargement
        const deleteBtn = document.querySelector(`[data-file-url="${currentFileUrl}"]`);
        if (deleteBtn) {
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            deleteBtn.disabled = true;
        }
        
        // Envoyer la requête de suppression
        fetch(`/transitions/${currentApprovalId}/attachments`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                file_url: currentFileUrl,
                attachment_id: currentAttachmentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher le message de succès et recharger
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success alert-dismissible fade show';
                successAlert.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.container-fluid').prepend(successAlert);
                
                // Recharger la page après un court délai
                setTimeout(() => location.reload(), 1500);
            } else {
                alert('Erreur: ' + data.message);
                if (deleteBtn) {
                    deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
                    deleteBtn.disabled = false;
                }
            }
        })
        .catch(error => {
            alert('Erreur lors de la suppression');
            console.error(error);
            if (deleteBtn) {
                deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
                deleteBtn.disabled = false;
            }
        });
    });
    
    // Gérer la fermeture du modal
    document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function() {
        currentFileUrl = null;
        currentAttachmentId = null;
        currentApprovalId = null;
    });
});
</script>
@endpush

@push('styles')
<style>
.required:after {
    content: " *";
    color: #dc3545;
}

.card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}

.card-header {
    border-bottom: 1px solid #e0e0e0;
    padding: 1rem 1.25rem;
}

.table th {
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.badge {
    font-weight: 500;
}

.alert {
    border: none;
    border-radius: 6px;
}

.form-text {
    font-size: 0.85rem;
    color: #6c757d;
}

#submitBtn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.fa-folder-open, .fa-file {
    color: #6c757d;
}
</style>
@endpush

@php
// Fonction helper pour calculer la taille totale
function calculateTotalSize($attachments) {
    $totalBytes = 0;
    foreach ($attachments as $attachment) {
        $totalBytes += $attachment['size'] ?? 0;
    }
    
    if ($totalBytes === 0) {
        return '0 B';
    }
    
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($totalBytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}
@endphp