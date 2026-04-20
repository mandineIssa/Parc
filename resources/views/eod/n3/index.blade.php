{{-- resources/views/eod/n3/index.blade.php --}}
@extends('layouts.app')

@section('title', 'N+3 - Supervision EOD')
@section('header', 'Supervision EOD - Tableau de bord')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête avec actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Supervision des traitements EOD</h1>
            <p class="text-gray-600 mt-2">Vue d'ensemble de l'activité et des performances</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <a href="{{ route('eod.n3.statistiques') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Statistiques détaillées
            </a>
            <a href="{{ route('eod.n3.export', 'csv') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter CSV
            </a>
        </div>
    </div>

    <!-- KPIs principaux -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-gray-500">
            <p class="text-sm text-gray-500 uppercase">Total fiches</p>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500 uppercase">En attente</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['en_attente'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-500 uppercase">Validées</p>
            <p class="text-3xl font-bold text-green-600">{{ $stats['valides'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500">
            <p class="text-sm text-gray-500 uppercase">Rejetées</p>
            <p class="text-3xl font-bold text-red-600">{{ $stats['rejetes'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-500 uppercase">Brouillons</p>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['brouillons'] }}</p>
        </div>
    </div>

    <!-- Graphiques et tableaux -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Répartition par jour -->
        <div class="bg-white rounded-xl shadow-md p-6 col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Répartition par jour</h3>
            <div class="space-y-3">
                @foreach($repartitionSemaine as $jour => $count)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $jour }}</span>
                        <span class="font-medium">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $stats['total'] > 0 ? ($count / $stats['total'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top contributeurs -->
        <div class="bg-white rounded-xl shadow-md p-6 col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top contributeurs</h3>
            <div class="space-y-4">
                @forelse($topContributeurs as $contrib)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold mr-3">
                            {{ strtoupper(substr($contrib->creator?->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium">{{ $contrib->creator?->name ?? 'Utilisateur inconnu' }}</p>
                            <p class="text-xs text-gray-500">{{ $contrib->creator?->departement ?? '—' }}</p>
                        </div>
                    </div>
                    <span class="text-lg font-bold text-indigo-600">{{ $contrib->total }}</span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Aucune donnée</p>
                @endforelse
            </div>
        </div>

        <!-- Évolution 30 jours (simplifiée) -->
        <div class="bg-white rounded-xl shadow-md p-6 col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Derniers 30 jours</h3>
            <div class="h-48 flex items-end justify-between gap-1">
                @php
                    $max = $evolution->max('total') ?: 1;
                @endphp
                @foreach($evolution->take(15) as $jour)
                <div class="flex flex-col items-center flex-1">
                    <div class="w-full bg-indigo-100 rounded-t" style="height: {{ ($jour->total / $max) * 100 }}%"></div>
                    <span class="text-xs text-gray-500 mt-1 transform -rotate-45 origin-top-left">{{ \Carbon\Carbon::parse($jour->date)->format('d/m') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Dernières fiches -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Dernières fiches de suivi</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Incidents</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dernieresFiches as $fiche)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-indigo-600">{{ $fiche->reference }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $fiche->date_traitement->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $fiche->creator?->name ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $fiche->status_class }}">
                                {{ $fiche->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $fiche->validator?->name ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @php
                                $incidents = json_decode($fiche->incidents_data ?? '[]', true);
                                $nbIncidents = count($incidents);
                            @endphp
                            @if($nbIncidents > 0)
                                <span class="text-red-600 font-medium">{{ $nbIncidents }}</span>
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('eod.n3.show', $fiche) }}" class="text-indigo-600 hover:text-indigo-900">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            Aucune fiche de suivi trouvée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bouton de retour au dashboard -->
    <div class="mt-8">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Retour au Dashboard
        </a>
    </div>
</div>

<style>
.bg-gradient-to-r {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.bg-white.rounded-xl.shadow-md:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
@endsection