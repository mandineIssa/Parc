@extends('layouts.app')

@section('title', 'Audits postes')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header title="Audits postes" subtitle="Données collectées automatiquement par les scripts PowerShell du parc">
        <a href="{{ route('audits-postes.export', request()->query()) }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-5 rounded-lg shadow-md transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Exporter Excel
        </a>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm">Postes connus</p>
            <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-emerald-500">
            <p class="text-gray-500 text-sm">Audités (24 h)</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $stats['audites_24h'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
            <p class="text-gray-500 text-sm">Antivirus off</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['antivirus_off'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-amber-500">
            <p class="text-gray-500 text-sm">BitLocker C: inactif</p>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['bitlocker_off'] }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md mb-6 p-4">
        <form method="GET" action="{{ route('audits-postes.index') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="search">Recherche</label>
                <input id="search" type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Hostname, série, utilisateur, IP…"
                       class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="fabricant">Fabricant</label>
                <input id="fabricant" type="text" name="fabricant" value="{{ $filters['fabricant'] ?? '' }}"
                       class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="os">OS</label>
                <input id="os" type="text" name="os" value="{{ $filters['os'] ?? '' }}"
                       class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="utilisateur">Utilisateur</label>
                <input id="utilisateur" type="text" name="utilisateur" value="{{ $filters['utilisateur'] ?? '' }}"
                       class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="antivirus_defender">Antivirus</label>
                <select id="antivirus_defender" name="antivirus_defender" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    <option value="">Tous</option>
                    <option value="1" @selected(($filters['antivirus_defender'] ?? '') === '1')>Actif</option>
                    <option value="0" @selected(($filters['antivirus_defender'] ?? '') === '0')>Inactif</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="bitlocker_actif">BitLocker C:</label>
                <select id="bitlocker_actif" name="bitlocker_actif" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    <option value="">Tous</option>
                    <option value="1" @selected(($filters['bitlocker_actif'] ?? '') === '1')>Actif</option>
                    <option value="0" @selected(($filters['bitlocker_actif'] ?? '') === '0')>Inactif</option>
                </select>
            </div>
            <div class="flex gap-2 md:col-span-2">
                <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded-lg transition">
                    Filtrer
                </button>
                <a href="{{ route('audits-postes.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded-lg transition">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Hostname</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Utilisateur</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Fabricant / modèle</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">OS</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Antivirus</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">BitLocker</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Dernier audit</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($postes as $poste)
                        @php
                            $alerte = ! $poste->antivirus_defender || ! $poste->isBitlockerActif();
                        @endphp
                        <tr class="{{ $alerte ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $poste->hostname }}</div>
                                <div class="text-xs text-gray-500">{{ $poste->numero_serie }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-800">{{ $poste->utilisateur_session ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div>{{ $poste->fabricant }}</div>
                                <div class="text-xs text-gray-500">{{ $poste->modele }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div>{{ $poste->os }}</div>
                                <div class="text-xs text-gray-500">{{ $poste->version_os }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($poste->antivirus_defender)
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Oui</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Non</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($poste->isBitlockerActif())
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">C: On</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">C: Off</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ optional($poste->date_audit)->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('audits-postes.show', $poste) }}"
                                   class="text-red-700 hover:text-red-900 font-medium">
                                    Détail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-gray-500">
                                Aucun audit reçu pour le moment. Les postes apparaîtront ici après le premier envoi du script PowerShell vers
                                <code class="text-xs bg-gray-100 px-1 rounded">POST /api/audit</code>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($postes->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $postes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
