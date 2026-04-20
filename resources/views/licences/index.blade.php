{{-- resources/views/licences/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Suivi des Licences')

@section('content')
<div class="p-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                Suivi des Licences
            </h1>
            <p class="text-sm text-gray-500 mt-1">Fortinet · FAI · Certificats · Office 365</p>
        </div>
        <a href="{{ route('licences.create') }}"
           class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle licence
        </a>
    </div>

    {{-- Stats par type --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach(['Fortinet' => 'purple', 'FAI' => 'blue', 'Certificat' => 'orange', 'Office365' => 'green'] as $type => $color)
        <a href="{{ route('licences.index', ['type' => $type]) }}"
           class="bg-white rounded-xl border p-4 shadow-sm hover:shadow transition
                  {{ request('type') === $type ? 'border-'.$color.'-400 bg-'.$color.'-50' : 'border-gray-200' }}">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">{{ $type }}</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $statsByType[$type] ?? 0 }}</p>
        </a>
        @endforeach
        @if($expiresSoon > 0)
        <div class="bg-amber-50 rounded-xl border border-amber-300 p-4 shadow-sm md:col-span-2">
            <p class="text-xs text-amber-600 uppercase tracking-wide font-semibold">⚠ Expirent dans 30 jours</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $expiresSoon }}</p>
        </div>
        @endif
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 mb-1">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nom, site, N° série…"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="min-w-36">
                <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Tous</option>
                    @foreach($types as $t)
                        <option value="{{ $t }}" @selected(request('type') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-36">
                <label class="block text-xs font-medium text-gray-600 mb-1">Statut</label>
                <select name="statut" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">Tous</option>
                    @foreach($statuts as $s)
                        <option value="{{ $s }}" @selected(request('statut') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Filtrer</button>
            @if(request()->hasAny(['search','type','statut']))
            <a href="{{ route('licences.index') }}" class="text-sm text-gray-500 hover:text-green-600 underline py-2">Réinitialiser</a>
            @endif
        </form>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Nom</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Site / Agence</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Détail</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Expiration</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Jours restants</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Contact</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($licences as $lic)
                    @php
                        $jours = $lic->jours_restants;
                        $urgence = $jours !== null && $jours <= 30;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors {{ $urgence ? 'bg-amber-50/30' : '' }}">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                @switch($lic->type)
                                    @case('Fortinet')  bg-purple-100 text-purple-700 @break
                                    @case('FAI')       bg-blue-100 text-blue-700 @break
                                    @case('Certificat') bg-orange-100 text-orange-700 @break
                                    @case('Office365') bg-green-100 text-green-700 @break
                                    @default           bg-gray-100 text-gray-700
                                @endswitch
                            ">{{ $lic->type }}</span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $lic->nom }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $lic->site_agence ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            @if($lic->type === 'Fortinet')  {{ $lic->type_licence }} · {{ $lic->numero_serie }}
                            @elseif($lic->type === 'FAI')   {{ $lic->fournisseur }} · {{ $lic->type_ligne }}
                            @elseif($lic->type === 'Certificat') {{ $lic->environnement }} · port {{ $lic->port }}
                            @elseif($lic->type === 'Office365') {{ $lic->utilisateur }} · {{ $lic->type_licence }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $s = $lic->statut;
                                $cls = match(true) {
                                    in_array($s, ['Actif','UP','Active']) => 'bg-green-100 text-green-700',
                                    in_array($s, ['Bientôt expirée','À renouveler','À surveiller']) => 'bg-amber-100 text-amber-700',
                                    in_array($s, ['Expirée','Expiré','DOWN','Résiliée']) => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $cls }}">{{ $s }}</span>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-700">
                            {{ $lic->date_expiration?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs font-semibold
                            {{ $jours !== null && $jours <= 0 ? 'text-red-600' : ($jours !== null && $jours <= 30 ? 'text-amber-600' : 'text-green-600') }}">
                            {{ $jours !== null ? $jours.'j' : '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            @if($lic->contact_nom)
                            <div class="font-medium text-gray-700">{{ $lic->contact_nom }}</div>
                            <div>{{ $lic->contact_email }}</div>
                            @else —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('licences.edit', $lic) }}"
                                   class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('licences.destroy', $lic) }}"
                                      onsubmit="return confirm('Supprimer cette licence ?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                            <p class="font-medium">Aucune licence trouvée</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($licences->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">{{ $licences->links() }}</div>
        @endif
    </div>
</div>
@endsection
