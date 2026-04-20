@extends('layouts.app')

@section('title', 'Nouveau Contrôle IT')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Créer un nouveau contrôle</h1>
            <p class="text-gray-500 mt-1">Définissez les paramètres du contrôle à planifier</p>
        </div>
        <a href="{{ route('controls.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('controls.store') }}" method="POST" id="controlForm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nom du contrôle <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Ex: Contrôle des accès mensuel"
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Type de contrôle <span class="text-red-500">*</span>
                    </label>
                    <select name="type" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        <option value="">Sélectionner un type</option>
                        <option value="securite" {{ old('type') == 'securite' ? 'selected' : '' }}>🔒 Sécurité informatique</option>
                        <option value="exploitation" {{ old('type') == 'exploitation' ? 'selected' : '' }}>⚙️ Exploitation IT</option>
                        <option value="conformite" {{ old('type') == 'conformite' ? 'selected' : '' }}>📋 Conformité</option>
                        <option value="audit" {{ old('type') == 'audit' ? 'selected' : '' }}>📊 Audit interne</option>
                    </select>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Fréquence -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fréquence <span class="text-red-500">*</span>
                    </label>
                    <select name="frequency" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        <option value="">Sélectionner une fréquence</option>
                        <option value="quotidienne" {{ old('frequency') == 'quotidienne' ? 'selected' : '' }}>📅 Quotidienne</option>
                        <option value="hebdomadaire" {{ old('frequency') == 'hebdomadaire' ? 'selected' : '' }}>📆 Hebdomadaire</option>
                        <option value="mensuelle" {{ old('frequency') == 'mensuelle' ? 'selected' : '' }}>📆 Mensuelle</option>
                        <option value="ponctuelle" {{ old('frequency') == 'ponctuelle' ? 'selected' : '' }}>🎯 Ponctuelle</option>
                    </select>
                    @error('frequency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Template -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Template (optionnel)</label>
                    <select name="template_id" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        <option value="">Aucun template</option>
                        @foreach($templates as $template)
                        <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                            {{ $template->name }} - {{ $template->review_type }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Utiliser un template pré-défini pour ce contrôle</p>
                    @error('template_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Application associée -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Application associée</label>
                    <select name="associated_application" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        <option value="">Aucune</option>
                        @foreach($applications as $id => $name)
                        <option value="{{ $id }}" {{ old('associated_application') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Associer ce contrôle à une application de la cartographie</p>
                    @error('associated_application') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Responsable -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Rôle responsable <span class="text-red-500">*</span>
                    </label>
                    <select name="responsible_role" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        <option value="">Sélectionner un rôle</option>
                        <option value="N1" {{ old('responsible_role') == 'N1' ? 'selected' : '' }}>👤 N+1 (Contrôleur - exécutant)</option>
                        <option value="N2" {{ old('responsible_role') == 'N2' ? 'selected' : '' }}>👥 N+2 (Superviseur - validation)</option>
                        <option value="N3" {{ old('responsible_role') == 'N3' ? 'selected' : '' }}>👔 N+3 (Direction - validation finale)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Le responsable N+1 sera automatiquement assigné aux tâches</p>
                    @error('responsible_role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description détaillée</label>
                    <textarea name="description" rows="4" 
                              placeholder="Décrivez les objectifs et le périmètre de ce contrôle..."
                              class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Paramètres avancés (si template sélectionné) -->
            <div id="templateFields" class="hidden mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-medium text-gray-800 mb-3">Paramètres du template</h3>
                <div id="templateContent"></div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('controls.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Créer le contrôle
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('template_id')?.addEventListener('change', function() {
    const templateId = this.value;
    const templateFields = document.getElementById('templateFields');
    
    if (templateId) {
        // Charger les détails du template via AJAX
        fetch(`/controls/templates/${templateId}/details`)
            .then(response => response.json())
            .then(data => {
                templateFields.classList.remove('hidden');
                document.getElementById('templateContent').innerHTML = `
                    <p class="text-sm text-gray-600 mb-2">${data.description || 'Aucune description'}</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <div>
                            <label class="text-xs font-medium text-gray-500">Checklist</label>
                            <ul class="mt-1 text-sm text-gray-600">
                                ${data.checklist?.map(item => `<li>✓ ${item}</li>`).join('') || '<li>Aucune checklist</li>'}
                            </ul>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500">Questions</label>
                            <ul class="mt-1 text-sm text-gray-600">
                                ${data.questions?.map(q => `<li>• ${q}</li>`).join('') || '<li>Aucune question</li>'}
                            </ul>
                        </div>
                    </div>
                `;
            });
    } else {
        templateFields.classList.add('hidden');
    }
});
</script>
@endpush
@endsection