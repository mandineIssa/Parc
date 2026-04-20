@extends('layouts.app')

@section('title', 'Modifier le Template')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Modifier le template</h1>
            <p class="text-gray-500 mt-1">{{ $template->name }}</p>
        </div>
        <a href="{{ route('controls.templates.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('controls.templates.update', $template) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Informations générales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nom du template <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $template->name) }}" required
                               class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Type de revue <span class="text-red-500">*</span>
                        </label>
                        <select name="review_type" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            @foreach($reviewTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('review_type', $template->review_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('review_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Fréquence recommandée <span class="text-red-500">*</span>
                        </label>
                        <select name="frequency" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            @foreach($frequencies as $value => $label)
                            <option value="{{ $value }}" {{ old('frequency', $template->frequency) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('frequency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <select name="is_active" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                            <option value="1" {{ old('is_active', $template->is_active) == '1' ? 'selected' : '' }}>Actif</option>
                            <option value="0" {{ old('is_active', $template->is_active) == '0' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">{{ old('description', $template->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Checklist -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Checklist de vérification</label>
                    <div id="checklistContainer" class="space-y-2">
                        @php $checklist = old('checklist', $template->checklist ?? []); @endphp
                        @foreach($checklist as $index => $item)
                        <div class="flex gap-2">
                            <input type="text" name="checklist[]" value="{{ $item }}" 
                                   class="flex-1 rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                                   placeholder="Point à vérifier...">
                            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" onclick="addChecklistItem()" class="mt-2 text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter un point
                    </button>
                </div>

                <!-- Questions -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Questions du contrôle</label>
                    <div id="questionsContainer" class="space-y-2">
                        @php $questions = old('questions', $template->questions ?? []); @endphp
                        @foreach($questions as $index => $question)
                        <div class="flex gap-2">
                            <input type="text" name="questions[]" value="{{ $question }}" 
                                   class="flex-1 rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                                   placeholder="Question à poser...">
                            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" onclick="addQuestionItem()" class="mt-2 text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter une question
                    </button>
                </div>

                <!-- Justificatifs requis -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Types de justificatifs requis</label>
                    <div id="attachmentsContainer" class="space-y-2">
                        @php $attachments = old('required_attachments', $template->required_attachments ?? []); @endphp
                        @foreach($attachments as $index => $type)
                        <div class="flex gap-2">
                            <input type="text" name="required_attachments[]" value="{{ $type }}" 
                                   class="flex-1 rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                                   placeholder="Type de justificatif...">
                            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" onclick="addAttachmentType()" class="mt-2 text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ajouter un type de justificatif
                    </button>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <a href="{{ route('controls.templates.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Mettre à jour
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function addChecklistItem() {
    const container = document.getElementById('checklistContainer');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" name="checklist[]" class="flex-1 rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="Point à vérifier...">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function addQuestionItem() {
    const container = document.getElementById('questionsContainer');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" name="questions[]" class="flex-1 rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="Question à poser...">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function addAttachmentType() {
    const container = document.getElementById('attachmentsContainer');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" name="required_attachments[]" class="flex-1 rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="Type de justificatif...">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    container.appendChild(div);
}
</script>
@endpush
@endsection