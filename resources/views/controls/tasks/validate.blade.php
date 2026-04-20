@extends('layouts.app')

@section('title', 'Validation - ' . $task->title)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Validation du contrôle</h1>
            <p class="text-gray-500 mt-1">{{ $task->control->name }}</p>
        </div>
        <a href="{{ route('controls.tasks.show', $task) }}" class="text-gray-600 hover:text-gray-800">
            ← Retour à la tâche
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Détails du contrôle -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Résultat du contrôle -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Résultat du contrôle</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut de conformité</label>
                    <div class="mt-1">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($task->conformity == 'conforme') bg-green-100 text-green-800
                            @elseif($task->conformity == 'non_conforme') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            @if($task->conformity == 'conforme') ✅ Conforme
                            @elseif($task->conformity == 'non_conforme') ❌ Non conforme
                            @else ⏳ En attente @endif
                        </span>
                    </div>
                </div>

                @if($task->criticality)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Niveau de criticité</label>
                    <div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($task->criticality == 'mineur') bg-yellow-100 text-yellow-800
                            @elseif($task->criticality == 'majeur') bg-orange-100 text-orange-800
                            @else bg-red-100 text-red-800 @endif">
                            @if($task->criticality == 'mineur') 🟡 Mineur
                            @elseif($task->criticality == 'majeur') 🟠 Majeur
                            @else 🔴 Critique @endif
                        </span>
                    </div>
                </div>
                @endif

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire du contrôleur</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-700">{{ $task->comment ?? 'Aucun commentaire' }}</p>
                    </div>
                </div>
            </div>

            <!-- Pièces jointes -->
            @if($task->attachments->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-gray-800 mb-3">Justificatifs fournis</h3>
                <div class="grid grid-cols-1 gap-2">
                    @foreach($task->attachments as $attachment)
                    <a href="{{ $attachment->url }}" target="_blank" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $attachment->original_name }}</p>
                            <p class="text-xs text-gray-400">Version {{ $attachment->version }} - {{ $attachment->file_size_formatted }}</p>
                        </div>
                        <svg class="w-5 h-5 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Formulaire de validation -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                <h3 class="font-semibold text-gray-800 mb-4">Décision de validation</h3>
                
                <form action="{{ route('controls.tasks.validate', $task) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Action <span class="text-red-500">*</span>
                        </label>
                        <select name="action" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" id="validationAction">
                            <option value="">Sélectionner</option>
                            <option value="approve" class="text-green-600">✅ Approuver - Le contrôle est conforme</option>
                            <option value="reject" class="text-red-600">❌ Rejeter - Le contrôle n'est pas acceptable</option>
                            <option value="need_complement" class="text-orange-600">📝 Demander des compléments</option>
                        </select>
                    </div>

                    <div class="mb-4 hidden" id="validationCommentField">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Commentaire <span class="text-red-500">*</span>
                        </label>
                        <textarea name="comment" rows="4" 
                                  placeholder="Expliquez votre décision..."
                                  class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Ce commentaire sera visible par le contrôleur</p>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <p class="text-xs text-yellow-800">
                            <strong>⚠️ Attention</strong><br>
                            Cette action est irréversible. En cas de rejet, le contrôleur devra refaire le contrôle.
                        </p>
                    </div>

                    <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 rounded-lg transition font-medium">
                        Valider la décision
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('validationAction')?.addEventListener('change', function() {
    const commentField = document.getElementById('validationCommentField');
    if (this.value === 'reject' || this.value === 'need_complement') {
        commentField.classList.remove('hidden');
        document.querySelector('textarea[name="comment"]').required = true;
    } else {
        commentField.classList.add('hidden');
        document.querySelector('textarea[name="comment"]').required = false;
    }
});
</script>
@endpush
@endsection