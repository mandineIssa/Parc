{{-- resources/views/passwords/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des Mots de Passe IT')

@section('content')
<div class="p-6 space-y-6">

    {{-- ── EN-TÊTE ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                Gestion des Mots de Passe IT
            </h1>
            <p class="text-sm text-gray-500 mt-1">Coffre-fort sécurisé — COFINA Sénégal</p>
        </div>
        <a href="{{ route('passwords.create') }}"
           class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle fiche
        </a>
    </div>

    {{-- ── STATS PAR CATÉGORIE (st) ─────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">

        {{-- Toutes les catégories --}}
        @foreach($statsByCateg as $cat => $total)
        <a href="{{ route('passwords.index', ['categorie' => $cat]) }}"
           class="bg-white rounded-xl border border-gray-200 p-3 text-center shadow-sm hover:border-red-300 hover:shadow transition
                  {{ request('categorie') === $cat ? 'border-red-500 bg-red-50' : '' }}">
            <p class="text-xs text-gray-500 truncate">{{ $cat }}</p>
            <p class="text-xl font-bold text-gray-800 mt-0.5">{{ $total }}</p>
        </a>
        @endforeach

        {{-- Card spéciale : expirent bientôt --}}
        @if($expiresSoon > 0)
        <a href="{{ route('passwords.index', ['expire' => 'bientot']) }}"
           class="bg-amber-50 rounded-xl border border-amber-200 p-3 text-center shadow-sm hover:border-amber-400 hover:shadow transition
                  {{ request('expire') === 'bientot' ? 'border-amber-500' : '' }}">
            <p class="text-xs text-amber-600 font-semibold truncate">Expirent bientôt</p>
            <p class="text-xl font-bold text-amber-600 mt-0.5">{{ $expiresSoon }}</p>
        </a>
        @endif

    </div>

    {{-- ── FILTRES ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 mb-1">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nom, IP, compte…"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div class="min-w-36">
                <label class="block text-xs font-medium text-gray-600 mb-1">Catégorie</label>
                <select name="categorie" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Toutes</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" @selected(request('categorie') === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-32">
                <label class="block text-xs font-medium text-gray-600 mb-1">Site</label>
                <select name="site" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Tous</option>
                    @foreach($sites as $site)
                        <option value="{{ $site }}" @selected(request('site') === $site)>{{ $site }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                Filtrer
            </button>
            @if(request()->hasAny(['search', 'categorie', 'site', 'expire']))
            <a href="{{ route('passwords.index') }}"
               class="text-sm text-gray-500 hover:text-red-600 underline py-2">Réinitialiser</a>
            @endif
        </form>
    </div>

    {{-- ── TABLEAU ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Catégorie</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Nom / Équipement</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Adresse IP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Compte</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Protocole</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Site</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Expiration</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Créé par</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($passwords as $pwd)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                @switch($pwd->categorie)
                                    @case('Serveur')               bg-blue-100 text-blue-700   @break
                                    @case('Réseau')                bg-purple-100 text-purple-700 @break
                                    @case('Base de données')       bg-green-100 text-green-700  @break
                                    @case('Sécurité électronique') bg-orange-100 text-orange-700 @break
                                    @default                       bg-gray-100 text-gray-700
                                @endswitch
                            ">{{ $pwd->categorie }}</span>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $pwd->nom }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $pwd->adresse_ip ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $pwd->compte }}</td>
                        <td class="px-4 py-3">
                            @if($pwd->protocole)
                                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-mono">{{ $pwd->protocole }}</span>
                            @else —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $pwd->site ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($pwd->date_expiration)
                                @php $statut = $pwd->statut_expiration; @endphp
                                <span class="inline-flex items-center gap-1 text-xs font-medium
                                    {{ $statut === 'Expiré'  ? 'text-red-600'   :
                                      ($statut === 'Bientôt' ? 'text-amber-600' : 'text-green-600') }}">
                                    <span class="w-1.5 h-1.5 rounded-full
                                        {{ $statut === 'Expiré'  ? 'bg-red-500'   :
                                          ($statut === 'Bientôt' ? 'bg-amber-500' : 'bg-green-500') }}"></span>
                                    {{ $pwd->date_expiration->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $pwd->creator?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('passwords.show', $pwd) }}"
                                   class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition" title="Voir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('passwords.edit', $pwd) }}"
                                   class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded transition" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('passwords.destroy', $pwd) }}"
                                      onsubmit="return confirm('Supprimer cette fiche ?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition" title="Supprimer">
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
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <p class="font-medium">Aucune fiche trouvée</p>
                            <p class="text-sm mt-1">
                                <a href="{{ route('passwords.create') }}" class="text-red-600 hover:underline">Créer la première fiche</a>
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($passwords->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $passwords->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ── TOAST ────────────────────────────────────────────────────────────── --}}
@if(session('success'))
<div id="toast" class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2 z-50">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('toast')?.remove(), 3500);</script>
@endif

@endsection