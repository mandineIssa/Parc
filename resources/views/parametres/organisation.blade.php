@extends('layouts.app')

@section('title', 'Paramètres — Départements & Postes')
@section('header', 'Départements & Postes')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <p class="text-sm text-gray-600">Gérez les listes utilisées dans les formulaires d’affectation (transitions, parc).</p>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-red-800 text-sm">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-red-800 text-sm">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Départements --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Départements</h2>
                <span class="text-xs text-gray-500">{{ $departements->where('actif', true)->count() }} actifs</span>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('parametres.departements.store') }}" class="flex gap-2 mb-5">
                    @csrf
                    <input type="text" name="nom" value="{{ old('nom') }}" required maxlength="100"
                           placeholder="Nouveau département…"
                           class="flex-1 rounded-lg border-gray-300 text-sm focus:ring-[#C8102E] focus:border-[#C8102E]">
                    <button type="submit" class="px-4 py-2 bg-[#C8102E] hover:bg-[#a00d24] text-white text-sm font-semibold rounded-lg">Ajouter</button>
                </form>

                <div class="space-y-2 max-h-[60vh] overflow-y-auto">
                    @forelse($departements as $dept)
                        <div class="flex items-center gap-2 p-2 rounded-lg border border-gray-100 {{ $dept->actif ? 'bg-white' : 'bg-gray-50 opacity-70' }}">
                            <form method="POST" action="{{ route('parametres.departements.update', $dept) }}" class="flex-1 flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="nom" value="{{ $dept->nom }}" required
                                       class="flex-1 rounded border-gray-300 text-sm py-1.5">
                                <input type="number" name="ordre" value="{{ $dept->ordre }}" min="0"
                                       class="w-16 rounded border-gray-300 text-sm py-1.5" title="Ordre">
                                <button type="submit" class="px-2 py-1.5 text-xs font-semibold rounded bg-gray-100 hover:bg-gray-200 text-gray-700">OK</button>
                            </form>
                            <form method="POST" action="{{ route('parametres.departements.toggle', $dept) }}">
                                @csrf
                                <button type="submit" class="px-2 py-1.5 text-xs font-semibold rounded {{ $dept->actif ? 'bg-amber-50 text-amber-700' : 'bg-green-50 text-green-700' }}">
                                    {{ $dept->actif ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('parametres.departements.destroy', $dept) }}"
                                  onsubmit="return confirm('Supprimer ce département ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1.5 text-xs font-semibold rounded bg-red-50 text-red-700 hover:bg-red-100">Suppr.</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-6">Aucun département.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Postes --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <h2 class="font-semibold text-gray-800">Postes</h2>
                <span class="text-xs text-gray-500">{{ $postes->where('actif', true)->count() }} actifs</span>
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('parametres.postes.store') }}" class="flex gap-2 mb-5">
                    @csrf
                    <input type="text" name="nom" required maxlength="100"
                           placeholder="Nouveau poste…"
                           class="flex-1 rounded-lg border-gray-300 text-sm focus:ring-[#C8102E] focus:border-[#C8102E]">
                    <button type="submit" class="px-4 py-2 bg-[#C8102E] hover:bg-[#a00d24] text-white text-sm font-semibold rounded-lg">Ajouter</button>
                </form>

                <div class="space-y-2 max-h-[60vh] overflow-y-auto">
                    @forelse($postes as $poste)
                        <div class="flex items-center gap-2 p-2 rounded-lg border border-gray-100 {{ $poste->actif ? 'bg-white' : 'bg-gray-50 opacity-70' }}">
                            <form method="POST" action="{{ route('parametres.postes.update', $poste) }}" class="flex-1 flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="nom" value="{{ $poste->nom }}" required
                                       class="flex-1 rounded border-gray-300 text-sm py-1.5">
                                <input type="number" name="ordre" value="{{ $poste->ordre }}" min="0"
                                       class="w-16 rounded border-gray-300 text-sm py-1.5" title="Ordre">
                                <button type="submit" class="px-2 py-1.5 text-xs font-semibold rounded bg-gray-100 hover:bg-gray-200 text-gray-700">OK</button>
                            </form>
                            <form method="POST" action="{{ route('parametres.postes.toggle', $poste) }}">
                                @csrf
                                <button type="submit" class="px-2 py-1.5 text-xs font-semibold rounded {{ $poste->actif ? 'bg-amber-50 text-amber-700' : 'bg-green-50 text-green-700' }}">
                                    {{ $poste->actif ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('parametres.postes.destroy', $poste) }}"
                                  onsubmit="return confirm('Supprimer ce poste ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1.5 text-xs font-semibold rounded bg-red-50 text-red-700 hover:bg-red-100">Suppr.</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-6">Aucun poste.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
