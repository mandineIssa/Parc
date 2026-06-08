{{-- resources/views/incidents/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">

    <div class="mb-6">
        <a href="{{ route('incidents.show', $incident) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-red-600 transition-colors mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour à la fiche
        </a>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center shadow">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-mono">{{ $incident->reference }}</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('incidents.update', $incident) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-red-600 px-6 py-3">
                <h2 class="text-white font-semibold text-sm uppercase tracking-wider">Déclaration</h2>
            </div>
            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Type d'incident <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['logiciel'=>'Logiciel','materiel'=>'Matériel','reseau_telecom'=>'Réseaux & Télécom'] as $val=>$label)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="type" value="{{ $val }}"
                                {{ old('type', $incident->type)===$val ? 'checked' : '' }}
                                class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                            <span class="text-sm text-gray-700 group-hover:text-red-600 transition-colors">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('type')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Utilisateur <span class="text-red-500">*</span></label>
                        <input type="text" name="utilisateur" value="{{ old('utilisateur', $incident->utilisateur) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        @error('utilisateur')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Entité <span class="text-red-500">*</span></label>
                        <input type="text" name="entite" value="{{ old('entite', $incident->entite) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        @error('entite')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fonction <span class="text-red-500">*</span></label>
                        <input type="text" name="fonction" value="{{ old('fonction', $incident->fonction) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        @error('fonction')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Point d'entrée <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-3">
                            @foreach(['telephone'=>'Téléphone','mail'=>'Mail','application'=>'Application'] as $val=>$label)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="point_entree" value="{{ $val }}"
                                    {{ old('point_entree', $incident->point_entree)===$val ? 'checked' : '' }}
                                    class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                                <span class="text-sm text-gray-700">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date_incident" value="{{ old('date_incident', $incident->date_incident->format('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Heure</label>
                        <input type="time" name="heure_incident" value="{{ old('heure_incident', $incident->heure_incident) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sujet <span class="text-red-500">*</span></label>
                    <input type="text" name="sujet" value="{{ old('sujet', $incident->sujet) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    @error('sujet')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="bloquant" value="1" {{ old('bloquant', $incident->bloquant) ? 'checked' : '' }}
                            class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <span class="text-sm font-medium text-gray-700">Bloquant</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="reproductible" value="1" {{ old('reproductible', $incident->reproductible) ? 'checked' : '' }}
                            class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <span class="text-sm font-medium text-gray-700">Reproductible</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="6"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('description') border-red-400 @enderror">{{ old('description', $incident->description) }}</textarea>
                    @error('description')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('incidents.show', $incident) }}"
               class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                Annuler
            </a>
            <button type="submit"
                class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection