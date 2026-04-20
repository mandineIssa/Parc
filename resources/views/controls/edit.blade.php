@extends('layouts.app')

@section('title', 'Modifier le Contrôle')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Modifier le contrôle</h1>
            <p class="text-gray-500 mt-1">{{ $control->name }}</p>
        </div>
        <a href="{{ route('controls.show', $control) }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('controls.update', $control) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nom du contrôle <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $control->name) }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Type de contrôle <span class="text-red-500">*</span>
                    </label>
                    <select name="type" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        <option value="securite" {{ old('type', $control->type) == 'securite' ? 'selected' : '' }}>🔒 Sécurité informatique</option>
                        <option value="exploitation" {{ old('type', $control->type) == 'exploitation' ? 'selected' : '' }}>⚙️ Exploitation IT</option>
                        <option value="conformite" {{ old('type', $control->type) == 'conformite' ? 'selected' : '' }}>📋 Conformité</option>
                        <option value="audit" {{ old('type', $control->type) == 'audit' ? 'selected' : '' }}>📊 Audit interne</option>
                    </select>
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Fréquence -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fréquence <span class="text-red-500">*</span>
                    </label>
                    <select name="frequency" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        <option value="quotidienne" {{ old('frequency', $control->frequency) == 'quotidienne' ? 'selected' : '' }}>📅 Quotidienne</option>
                        <option value="hebdomadaire" {{ old('frequency', $control->frequency) == 'hebdomadaire' ? 'selected' : '' }}>📆 Hebdomadaire</option>
                        <option value="mensuelle" {{ old('frequency', $control->frequency) == 'mensuelle' ? 'selected' : '' }}>📆 Mensuelle</option>
                        <option value="ponctuelle" {{ old('frequency', $control->frequency) == 'ponctuelle' ? 'selected' : '' }}>🎯 Ponctuelle</option>
                    </select>
                    @error('frequency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Statut -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Statut <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        <option value="actif" {{ old('status', $control->status) == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ old('status', $control->status) == 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Template -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                    <select name="template_id" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        <option value="">Aucun template</option>
                        @foreach($templates as $template)
                        <option value="{{ $template->id }}" {{ old('template_id', $control->template_id) == $template->id ? 'selected' : '' }}>
                            {{ $template->name }} - {{ $template->review_type }}
                        </option>
                        @endforeach
                    </select>
                    @error('template_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Application associée -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Application associée</label>
                    <select name="associated_application" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        <option value="">Aucune</option>
                        @foreach($applications as $id => $name)
                        <option value="{{ $id }}" {{ old('associated_application', $control->associated_application) == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('associated_application') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Responsable -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Rôle responsable <span class="text-red-500">*</span>
                    </label>
                    <select name="responsible_role" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        <option value="N1" {{ old('responsible_role', $control->responsible_role) == 'N1' ? 'selected' : '' }}>👤 N+1 (Contrôleur)</option>
                        <option value="N2" {{ old('responsible_role', $control->responsible_role) == 'N2' ? 'selected' : '' }}>👥 N+2 (Superviseur)</option>
                        <option value="N3" {{ old('responsible_role', $control->responsible_role) == 'N3' ? 'selected' : '' }}>👔 N+3 (Direction)</option>
                    </select>
                    @error('responsible_role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description détaillée</label>
                    <textarea name="description" rows="4" 
                              class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">{{ old('description', $control->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('controls.show', $control) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Annuler
                </a>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection