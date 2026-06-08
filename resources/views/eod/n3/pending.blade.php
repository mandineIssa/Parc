@extends('layouts.app')

@php $isCtrl = !empty($pendingAsController); @endphp

@section('title', $isCtrl ? 'EOD — Fiches en attente Controller' : 'EOD — Fiches en attente N+3')
@section('header', $isCtrl ? 'EOD — Signatures Controller' : 'EOD — Signatures N+3')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-end items-start md:items-center mb-8 gap-4">
        @if(!$isCtrl)
            <a href="{{ route('eod.n3.index') }}" class="text-sm font-medium text-[#C8102E] hover:text-[#a00d24]">← Supervision EOD</a>
        @else
            <a href="{{ route('eod.controller.index') }}" class="text-sm font-medium text-[#C8102E] hover:text-[#a00d24]">← Toutes les fiches batch</a>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3 font-semibold">Référence</th>
                    <th class="px-4 py-3 font-semibold">Date traitement</th>
                    @if($isCtrl)
                        <th class="px-4 py-3 font-semibold">N+2</th>
                    @else
                        <th class="px-4 py-3 font-semibold">Créateur</th>
                    @endif
                    <th class="px-4 py-3 font-semibold">Statut</th>
                    <th class="px-4 py-3 font-semibold"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($fiches as $fiche)
                <tr>
                    <td class="px-4 py-3 font-mono">{{ $fiche->reference }}</td>
                    <td class="px-4 py-3">{{ $fiche->date_traitement?->format('d/m/Y') ?? '—' }}</td>
                    @if($isCtrl)
                        <td class="px-4 py-3">{{ $fiche->validator?->name ?? '—' }}</td>
                    @else
                        <td class="px-4 py-3">{{ trim(($fiche->creator?->prenom ?? '') . ' ' . ($fiche->creator?->name ?? '')) ?: '—' }}</td>
                    @endif
                    <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $fiche->status_class }}">{{ $fiche->status_label }}</span></td>
                    <td class="px-4 py-3 text-right">
                        @if($isCtrl)
                            <a href="{{ route('eod.controller.edit', $fiche) }}" class="inline-flex items-center px-3 py-1.5 bg-[#C8102E] text-white rounded-lg text-xs font-semibold hover:bg-[#a00d24]">Ouvrir</a>
                        @else
                            <a href="{{ route('eod.n3.show', $fiche) }}" class="inline-flex items-center px-3 py-1.5 bg-[#C8102E] text-white rounded-lg text-xs font-semibold hover:bg-[#a00d24]">Signer</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">
                    @if($isCtrl)
                        Aucune fiche en attente de signature Controller.
                    @else
                        Aucune fiche en attente de signature N+3.
                    @endif
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $fiches->links() }}</div>
</div>
@endsection
