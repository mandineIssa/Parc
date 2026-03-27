@extends('layouts.app')

@section('title', 'Historique des Réaffectations')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Historique des Réaffectations</h1>
                <p class="text-gray-500 mt-0.5">Suivi complet de tous les transferts d'équipements</p>
            </div>
        </div>
        <a href="{{ route('parc.index') }}"
           class="mt-4 md:mt-0 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 px-5 rounded-lg transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au parc
        </a>
    </div>

    {{-- Message de succès --}}
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
        <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <p class="text-green-800 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Statistiques rapides --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $reaffectations->total() }}</p>
                <p class="text-sm text-gray-500">Réaffectations totales</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">
                    {{ \App\Models\Reaffectation::whereMonth('date_reaffectation', now()->month)->count() }}
                </p>
                <p class="text-sm text-gray-500">Ce mois-ci</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">
                    {{ \App\Models\Reaffectation::distinct('equipment_id')->count('equipment_id') }}
                </p>
                <p class="text-sm text-gray-500">Équipements concernés</p>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET" action="{{ route('parc.reaffectations.index') }}"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Rechercher par N° série, nom équipement..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm">
            </div>
            <div>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm"
                       placeholder="Du">
            </div>
            <div>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm"
                       placeholder="Au">
            </div>
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-5 rounded-lg transition text-sm whitespace-nowrap flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filtrer
            </button>
            @if(request()->hasAny(['search', 'date_debut', 'date_fin']))
            <a href="{{ route('parc.reaffectations.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium py-2.5 px-4 rounded-lg transition text-sm flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Reset
            </a>
            @endif
        </div>
    </form>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">
                {{ $reaffectations->total() }} réaffectation{{ $reaffectations->total() > 1 ? 's' : '' }}
                @if(request()->hasAny(['search', 'date_debut', 'date_fin']))
                    <span class="text-indigo-600">(filtrées)</span>
                @endif
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Équipement</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ancien utilisateur</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">→</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nouvel utilisateur</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Motif</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fait par</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($reaffectations as $r)
                    <tr class="hover:bg-indigo-50/30 transition-colors">

                        {{-- Équipement --}}
                        <td class="px-5 py-4">
                            @if($r->equipment)
                            <div class="flex items-center gap-2.5">
                                <div class="h-8 w-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <a href="{{ route('equipment.show', $r->equipment) }}"
                                       class="font-medium text-gray-900 hover:text-indigo-600 transition text-sm">
                                        {{ $r->equipment->nom }}
                                    </a>
                                    <p class="text-xs text-gray-400 font-mono">{{ $r->equipment->numero_serie }}</p>
                                </div>
                            </div>
                            @else
                            <span class="text-gray-400 text-sm italic">Équipement supprimé</span>
                            @endif
                        </td>

                        {{-- Ancien utilisateur --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="h-7 w-7 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-gray-500">
                                        {{ strtoupper(substr($r->ancien_utilisateur_nom ?? 'N', 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ $r->ancien_nom_complet }}</p>
                                    @if($r->ancien_departement)
                                    <p class="text-xs text-gray-400">{{ $r->ancien_departement }}</p>
                                    @endif
                                    @if($r->ancienne_localisation)
                                    <p class="text-xs text-gray-300">{{ $r->ancienne_localisation }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Flèche --}}
                        <td class="px-3 py-4 text-center">
                            <div class="inline-flex items-center justify-center h-7 w-7 bg-indigo-600 rounded-full">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </div>
                        </td>

                        {{-- Nouvel utilisateur --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="h-7 w-7 bg-indigo-200 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-indigo-700">
                                        {{ strtoupper(substr($r->nouveau_utilisateur_nom, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-indigo-700">{{ $r->nouveau_nom_complet }}</p>
                                    @if($r->nouveau_departement)
                                    <p class="text-xs text-gray-400">{{ $r->nouveau_departement }}</p>
                                    @endif
                                    @if($r->nouvelle_localisation)
                                    <p class="text-xs text-gray-300">{{ $r->nouvelle_localisation }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Date --}}
                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="text-sm font-medium text-gray-900">{{ $r->date_reaffectation->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $r->date_reaffectation->diffForHumans() }}</p>
                        </td>

                        {{-- Motif --}}
                        <td class="px-5 py-4 max-w-xs">
                            @if($r->motif)
                            <p class="text-sm text-gray-600 truncate" title="{{ $r->motif }}">{{ $r->motif }}</p>
                            @else
                            <span class="text-gray-300 italic text-sm">—</span>
                            @endif
                        </td>

                        {{-- Fait par --}}
                        <td class="px-5 py-4">
                            @if($r->auteur)
                            <p class="text-sm text-gray-700">{{ $r->auteur->name }}</p>
                            @else
                            <span class="text-gray-300 italic text-sm">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">Aucune réaffectation trouvée</p>
                                <p class="text-gray-400 text-sm">
                                    @if(request()->hasAny(['search', 'date_debut', 'date_fin']))
                                        Modifiez vos critères de recherche
                                    @else
                                        Les réaffectations apparaîtront ici
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($reaffectations->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                <p class="text-sm text-gray-600">
                    Affichage de <span class="font-medium">{{ $reaffectations->firstItem() }}</span>
                    à <span class="font-medium">{{ $reaffectations->lastItem() }}</span>
                    sur <span class="font-medium">{{ $reaffectations->total() }}</span>
                </p>
                {{ $reaffectations->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection