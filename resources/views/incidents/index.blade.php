{{-- resources/views/incidents/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8">

    {{-- ── En-tête ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500 mt-1">
                Workflow N+1 → N+2 → N+3 — COFINA Mobile
                @if($user->role_change)
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                    {{ $user->isN1() ? 'bg-blue-100 text-blue-700' : ($user->isN2() ? 'bg-orange-100 text-orange-700' : 'bg-purple-100 text-purple-700') }}">
                    Votre rôle : {{ $user->change_role_label }}
                </span>
                @endif
            </p>
        </div>
        @if($user->isN1() || $user->isSuperAdmin())
        <a href="{{ route('incidents.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle fiche incident
        </a>
        @endif
    </div>

    {{-- ── Stats ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label'=>'Total',      'value'=>$stats['total'],    'bg'=>'bg-gray-50',    'icon_color'=>'text-gray-500',   'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label'=>'Soumis',     'value'=>$stats['soumis'],   'bg'=>'bg-blue-50',    'icon_color'=>'text-blue-500',   'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['label'=>'En cours',   'value'=>$stats['en_cours'], 'bg'=>'bg-orange-50',  'icon_color'=>'text-orange-500', 'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label'=>'Clôturés',   'value'=>$stats['clotures'], 'bg'=>'bg-green-50',   'icon_color'=>'text-green-600',  'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as $s)
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-lg {{ $s['bg'] }} flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 {{ $s['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $s['icon'] }}"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $s['value'] }}</p>
                <p class="text-xs text-gray-500">{{ $s['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Filtres ── --}}
    <form method="GET" action="{{ route('incidents.index') }}"
          class="bg-white rounded-xl border border-gray-200 p-4 mb-4 shadow-sm flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Recherche</label>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Référence, sujet, entité..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
        </div>
        <div class="w-44">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Statut</label>
            <select name="statut" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Tous les statuts</option>
                @foreach(['soumis'=>'Soumis (N+1)','en_cours_n2'=>'En cours N+2','en_cours_n3'=>'En cours N+3','cloture'=>'Clôturé','rejete'=>'Rejeté'] as $v=>$l)
                <option value="{{ $v }}" {{ request('statut')===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-44">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Type</label>
            <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Tous les types</option>
                @foreach(['logiciel'=>'Logiciel','materiel'=>'Matériel','reseau_telecom'=>'Réseaux & Télécom'] as $v=>$l)
                <option value="{{ $v }}" {{ request('type')===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
            Filtrer
        </button>
        @if(request()->hasAny(['search','statut','type']))
        <a href="{{ route('incidents.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
            Réinitialiser
        </a>
        @endif
    </form>

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Tableau ── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Référence</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Sujet / Entité</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Type</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Date</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Statut</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Niveau actif</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Bloquant</th>
                        <th class="text-right px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($fiches as $fiche)
                    @php
                        $statusStyles = [
                            'brouillon'   => 'bg-gray-100 text-gray-600',
                            'soumis'      => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
                            'en_cours_n2' => 'bg-orange-50 text-orange-700 ring-1 ring-orange-200',
                            'en_cours_n3' => 'bg-purple-50 text-purple-700 ring-1 ring-purple-200',
                            'cloture'     => 'bg-green-50 text-green-700 ring-1 ring-green-200',
                            'rejete'      => 'bg-red-50 text-red-700 ring-1 ring-red-200',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs font-bold text-red-600">{{ $fiche->reference }}</span>
                        </td>
                        <td class="px-4 py-3 max-w-xs">
                            <p class="font-semibold text-gray-900 truncate">{{ $fiche->sujet }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $fiche->entite }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $fiche->type_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                            {{ $fiche->date_incident->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusStyles[$fiche->statut] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $fiche->statut_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($fiche->statut === 'soumis')
                                <span class="text-xs font-bold text-blue-600">N+1</span>
                            @elseif($fiche->statut === 'en_cours_n2')
                                <span class="text-xs font-bold text-orange-600">N+2</span>
                            @elseif($fiche->statut === 'en_cours_n3')
                                <span class="text-xs font-bold text-purple-600">N+3</span>
                            @elseif($fiche->statut === 'cloture')
                                <span class="text-xs font-bold text-green-600">✓ Clôturé</span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($fiche->bloquant)
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-red-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>Bloquant
                                </span>
                            @else
                                <span class="text-xs text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('incidents.show', $fiche) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Voir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-16 text-center">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="font-semibold text-gray-400">Aucune fiche incident</p>
                            @if($user->isN1() || $user->isSuperAdmin())
                            <a href="{{ route('incidents.create') }}" class="inline-block mt-3 text-sm text-red-600 hover:underline">
                                Créer la première fiche →
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($fiches->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $fiches->links() }}</div>
        @endif
    </div>
</div>
@endsection