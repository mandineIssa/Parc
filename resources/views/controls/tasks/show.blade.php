@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-start mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-2xl font-bold text-gray-800">{{ $task->title }}</h1>
                <span class="px-2 py-1 text-xs rounded-full
                    @if($task->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                    @elseif($task->status == 'completed') bg-green-100 text-green-800
                    @elseif($task->status == 'rejected') bg-red-100 text-red-800
                    @else bg-orange-100 text-orange-800 @endif">
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </div>
            <p class="text-gray-500">Contrôle: {{ $task->control->name }}</p>
        </div>
        <a href="{{ route('controls.tasks.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour aux tâches
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche - Informations -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Description
                </h3>
                <p class="text-gray-600">{{ $task->description ?? $task->control->description ?? 'Aucune description' }}</p>
            </div>

            <!-- Formulaire de réalisation du contrôle -->
            @if(in_array($task->status, ['pending', 'in_progress', 'need_complement']) && auth()->id() == $task->assigned_to)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Réaliser le contrôle
                </h3>
                
                <form action="{{ route('controls.tasks.update-status', $task) }}" method="POST" id="taskForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Checklist du template -->
                    @if($task->control->template && $task->control->template->checklist)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Checklist de vérification</label>
                        <div class="space-y-2 bg-gray-50 p-4 rounded-lg">
                            @foreach($task->control->template->checklist as $item)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="checklist[]" value="{{ $item }}" class="rounded border-gray-300">
                                <span class="text-sm text-gray-700">{{ $item }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Conformité -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Résultat du contrôle <span class="text-red-500">*</span>
                        </label>
                        <select name="conformity" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" id="conformitySelect">
                            <option value="">Sélectionner</option>
                            <option value="conforme">✅ Conforme</option>
                            <option value="non_conforme">❌ Non conforme</option>
                            <option value="en_attente">⏳ En attente</option>
                        </select>
                    </div>

                    <!-- Criticité (pour non conforme) -->
                    <div class="mb-4 hidden" id="criticalityField">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Niveau de criticité <span class="text-red-500">*</span>
                        </label>
                        <select name="criticality" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            <option value="">Sélectionner</option>
                            <option value="mineur">🟡 Mineur</option>
                            <option value="majeur">🟠 Majeur</option>
                            <option value="critique">🔴 Critique</option>
                        </select>
                    </div>

                    <!-- Commentaire -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Commentaire @if($task->status == 'need_complement') <span class="text-red-500">*</span> @endif
                        </label>
                        <textarea name="comment" rows="4" 
                                  placeholder="Décrivez les actions réalisées, les anomalies constatées..."
                                  class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">{{ old('comment', $task->comment) }}</textarea>
                    </div>

                    <!-- Upload de fichiers -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pièces jointes (justificatifs)</label>
                        <div id="dropzone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-red-400 transition">
                            <input type="file" id="fileInput" multiple class="hidden" accept=".pdf,.jpg,.png,.xlsx,.xls,.doc,.docx">
                            <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-gray-500">Glissez-déposez vos fichiers ici ou cliquez pour sélectionner</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, images, Excel, Word (max 10MB)</p>
                        </div>
                        <div id="fileList" class="mt-3 space-y-2"></div>
                    </div>

                    <div class="flex justify-end gap-3 mt-4 pt-4 border-t">
                        <button type="button" onclick="saveDraft()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Sauvegarder brouillon
                        </button>
                        <button type="submit" name="status" value="completed" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Soumettre le contrôle
                        </button>
                    </div>
                </form>
            </div>
            @endif

            <!-- Validation par superviseur -->
            @if($task->status == 'completed' && !$task->validated_by && $canValidate)
            <div class="bg-white rounded-lg shadow p-6 border-2 border-yellow-200">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Validation du superviseur
                </h3>
                
                <form action="{{ route('controls.tasks.validate', $task) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Décision de validation</label>
                        <select name="action" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" id="validationAction">
                            <option value="">Sélectionner</option>
                            <option value="approve">✅ Approuver</option>
                            <option value="reject">❌ Rejeter</option>
                            <option value="need_complement">📝 Demander des compléments</option>
                        </select>
                    </div>
                    <div class="mb-4" id="validationCommentField">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Commentaire</label>
                        <textarea name="comment" rows="3" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 rounded-lg transition">
                        Valider la décision
                    </button>
                </form>
            </div>
            @endif

            <!-- Pièces jointes existantes -->
            @if($task->attachments->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    Pièces jointes
                </h3>
                <div class="space-y-2">
                    @foreach($task->attachments as $attachment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <a href="{{ $attachment->url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    {{ $attachment->original_name }}
                                </a>
                                <p class="text-xs text-gray-400">Version {{ $attachment->version }} - {{ $attachment->file_size_formatted }}</p>
                            </div>
                        </div>
                        @if(auth()->id() == $attachment->uploaded_by && in_array($task->status, ['pending', 'in_progress', 'need_complement']))
                        <form action="{{ route('controls.tasks.delete-attachment', $attachment) }}" method="POST" onsubmit="return confirm('Supprimer ce fichier ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Colonne droite - Informations et historique -->
        <div class="space-y-6">
            <!-- Informations -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-3">Informations</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs text-gray-500">Date limite</dt>
                        <dd class="text-sm font-medium {{ $task->isOverdue() && $task->status != 'completed' ? 'text-red-600' : '' }}">
                            {{ $task->due_date->format('d/m/Y H:i') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Assigné à</dt>
                        <dd class="text-sm">{{ $task->assignedTo?->name ?? 'Non assigné' }}</dd>
                    </div>
                    @if($task->completed_at)
                    <div>
                        <dt class="text-xs text-gray-500">Date de réalisation</dt>
                        <dd class="text-sm">{{ $task->completed_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @endif
                    @if($task->validated_at)
                    <div>
                        <dt class="text-xs text-gray-500">Date de validation</dt>
                        <dd class="text-sm">{{ $task->validated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Validé par</dt>
                        <dd class="text-sm">{{ $task->validatedBy?->name ?? '-' }}</dd>
                    </div>
                    @endif
                    @if($task->criticality)
                    <div>
                        <dt class="text-xs text-gray-500">Criticité</dt>
                        <dd class="text-sm">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($task->criticality == 'mineur') bg-yellow-100 text-yellow-800
                                @elseif($task->criticality == 'majeur') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($task->criticality) }}
                            </span>
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Historique des versions des pièces jointes -->
            @php
                $groupedAttachments = $task->attachments->groupBy('original_name');
            @endphp
            @if($groupedAttachments->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-3">Historique des versions</h3>
                @foreach($groupedAttachments as $name => $versions)
                <div class="mb-3">
                    <p class="text-sm font-medium text-gray-700">{{ $name }}</p>
                    <div class="ml-2 mt-1 space-y-1">
                        @foreach($versions->sortByDesc('version') as $version)
                        <div class="text-xs text-gray-500">
                            Version {{ $version->version }} - {{ $version->created_at->format('d/m/Y H:i') }}
                            <a href="{{ $version->url }}" target="_blank" class="text-blue-600 ml-2">Télécharger</a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Commentaires -->
            @if($task->comment)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-3">Commentaires</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-700">{{ $task->comment }}</p>
                    <p class="text-xs text-gray-400 mt-2">Ajouté le {{ $task->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Gestion de la criticité
document.getElementById('conformitySelect')?.addEventListener('change', function() {
    const criticalityField = document.getElementById('criticalityField');
    if (this.value === 'non_conforme') {
        criticalityField.classList.remove('hidden');
    } else {
        criticalityField.classList.add('hidden');
    }
});

// Gestion du commentaire de validation
document.getElementById('validationAction')?.addEventListener('change', function() {
    const commentField = document.getElementById('validationCommentField');
    if (this.value === 'reject' || this.value === 'need_complement') {
        commentField.classList.remove('hidden');
    } else {
        commentField.classList.add('hidden');
    }
});

// Upload de fichiers
const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('fileInput');
const fileList = document.getElementById('fileList');
const uploadedFiles = [];

if (dropzone) {
    dropzone.addEventListener('click', () => fileInput.click());
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-red-400', 'bg-red-50');
    });
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-red-400', 'bg-red-50');
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-red-400', 'bg-red-50');
        handleFiles(e.dataTransfer.files);
    });
    fileInput.addEventListener('change', (e) => handleFiles(e.target.files));
}

function handleFiles(files) {
    for (let file of files) {
        if (file.size > 10 * 1024 * 1024) {
            alert(`Fichier trop volumineux: ${file.name} (max 10MB)`);
            continue;
        }
        uploadFile(file);
    }
}

function uploadFile(file) {
    const formData = new FormData();
    formData.append('attachment', file);
    
    const fileId = Date.now() + '_' + file.name;
    const fileDiv = document.createElement('div');
    fileDiv.id = fileId;
    fileDiv.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
    fileDiv.innerHTML = `
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span class="text-sm">${file.name}</span>
        </div>
        <span class="text-xs text-gray-400">Upload en cours...</span>
    `;
    fileList.appendChild(fileDiv);
    
    fetch(`{{ route('controls.tasks.upload-attachment', $task) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fileDiv.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <a href="${data.url}" target="_blank" class="text-sm text-blue-600 hover:underline">${file.name}</a>
                </div>
                <span class="text-xs text-green-500">Uploadé</span>
            `;
        } else {
            fileDiv.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="text-sm">${file.name}</span>
                </div>
                <span class="text-xs text-red-500">Erreur</span>
            `;
        }
    })
    .catch(() => {
        fileDiv.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span class="text-sm">${file.name}</span>
            </div>
            <span class="text-xs text-red-500">Erreur</span>
        `;
    });
}

function saveDraft() {
    const form = document.getElementById('taskForm');
    const formData = new FormData(form);
    formData.set('status', 'in_progress');
    
    fetch(form.action + '?draft=1', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-HTTP-Method-Override': 'PUT'
        },
        body: formData
    })
    .then(response => response.json())
    .then(() => {
        alert('Brouillon sauvegardé');
    })
    .catch(() => {
        alert('Erreur lors de la sauvegarde');
    });
}
</script>
@endpush
@endsection