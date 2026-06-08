@extends('layouts.app')

@section('title', 'EOD - Controller')
@section('header', 'Validation Controller EOD')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-600">Le PDF final n'est disponible qu'après votre validation.</p>
        </div>
    </div>

    <div class="flex gap-2 mb-4">
        <a href="{{ route('eod.controller.index') }}" class="px-3 py-2 rounded-lg text-sm {{ !request('filter') ? 'bg-red-50 text-[#C8102E]' : 'bg-gray-100 text-gray-700' }}">Tous</a>
        <a href="{{ route('eod.controller.index', ['filter' => 'pending']) }}" class="px-3 py-2 rounded-lg text-sm {{ request('filter') === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700' }}">En attente signature</a>
        <a href="{{ route('eod.controller.index', ['filter' => 'signed']) }}" class="px-3 py-2 rounded-lg text-sm {{ request('filter') === 'signed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">Signées</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Référence</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">N+2</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Statut</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($fiches as $fiche)
                <tr>
                    <td class="px-4 py-3 font-mono text-sm text-[#C8102E]">{{ $fiche->reference }}</td>
                    <td class="px-4 py-3 text-sm">{{ $fiche->date_traitement?->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $fiche->validator?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="px-2 py-1 rounded-full text-xs {{ $fiche->status_class }}">{{ $fiche->status_label }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('eod.controller.edit', $fiche) }}" class="text-[#C8102E] hover:text-[#a00d24] text-sm font-medium">Ouvrir</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Aucune fiche à afficher.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $fiches->links() }}
    </div>
</div>
@endsection
