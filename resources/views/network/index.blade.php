{{-- resources/views/network/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Plan d\'Adressage Réseau')

@section('content')
<div class="p-6 space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                </svg>
                Plan d'Adressage Réseau
            </h1>
            <p class="text-sm text-gray-500 mt-1">COFINA Sénégal — Toutes agences</p>
        </div>
        <a href="{{ route('network.create') }}"
           class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter une entrée
        </a>
    </div>

    {{-- Stats par site --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
        @foreach($statsBySite as $site => $total)
        <a href="{{ route('network.index', ['site' => $site]) }}"
           class="bg-white rounded-xl border border-gray-200 p-3 text-center shadow-sm hover:border-blue-300 hover:shadow transition
                  {{ request('site') === $site ? 'border-blue-500 bg-blue-50' : '' }}">
            <p class="text-xs text-gray-500">{{ $site }}</p>
            <p class="text-xl font-bold text-gray-800 mt-0.5">{{ $total }}</p>
        </a>
        @endforeach
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 mb-1">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="VLAN, IP, équipement…"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="min-w-36">
                <label class="block text-xs font-medium text-gray-600 mb-1">Site</label>
                <select name="site" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous</option>
                    @foreach($sites as $site)
                        <option value="{{ $site }}" @selected(request('site') === $site)>{{ $site }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-40">
                <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous</option>
                    <option value="plan_adressage"  @selected(request('type') === 'plan_adressage')>Plan d'adressage</option>
                    <option value="branchement_local" @selected(request('type') === 'branchement_local')>Branchement local</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Filtrer</button>
            @if(request()->hasAny(['search','site','type']))
            <a href="{{ route('network.index') }}" class="text-sm text-gray-500 hover:text-blue-600 underline py-2">Réinitialiser</a>
            @endif
        </form>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Site</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">VLAN</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Réseau / IP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Masque</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Équipement</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Connecté à</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Emplacement</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Commentaires</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($addresses as $addr)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded">{{ $addr->site }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $addr->type === 'plan_adressage' ? 'Adressage' : 'Branchement' }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $addr->vlan ?? '—' }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-700">
                            {{ $addr->adresse_reseau ?? $addr->adresse_ip ?? '—' }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $addr->masque ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">
                            @if($addr->equipement_reseau)
                            <div>{{ $addr->equipement_reseau }}</div>
                            @if($addr->type_equipement)
                            <div class="text-xs text-gray-400">{{ $addr->type_equipement }}</div>
                            @endif
                            @else —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $addr->equipement_connecte ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $addr->emplacement ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $addr->commentaires ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('network.edit', $addr) }}"
                                   class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('network.destroy', $addr) }}"
                                      onsubmit="return confirm('Supprimer cette entrée ?')">
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
                        <td colspan="10" class="px-4 py-12 text-center text-gray-400">
                            <p class="font-medium">Aucune entrée trouvée</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($addresses->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">{{ $addresses->links() }}</div>
        @endif
    </div>
</div>
@endsection
