@extends('layouts.app')

@section('title', 'Détail du poste')
@section('header', 'Détail du poste')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-2xl bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex justify-between items-start mb-6">
            <h1 class="text-xl font-semibold text-gray-800 uppercase">{{ $poste->nom }}</h1>
            <a href="{{ route('parametres.postes.edit', $poste) }}" class="px-4 py-2 bg-[#C8102E] text-white rounded-md font-semibold">Modifier</a>
        </div>
        <dl class="space-y-4">
            <div>
                <dt class="text-sm text-gray-500">Description</dt>
                <dd class="text-gray-900">{{ $poste->description ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Statut</dt>
                <dd>
                    @if($poste->actif)
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Actif</span>
                    @else
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Inactif</span>
                    @endif
                </dd>
            </div>
        </dl>
        <div class="mt-6">
            <a href="{{ route('parametres.postes.index') }}" class="text-sm text-indigo-600 hover:underline">Retour à la liste</a>
        </div>
    </div>
</div>
@endsection
