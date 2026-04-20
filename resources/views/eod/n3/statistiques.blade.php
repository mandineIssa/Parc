{{-- resources/views/eod/n3/statistiques.blade.php --}}
@extends('layouts.app')

@section('title', 'N+3 - Statistiques EOD')
@section('header', 'Supervision EOD - Statistiques détaillées')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête avec navigation -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Statistiques détaillées</h1>
            <p class="text-gray-600 mt-2">Analyse des traitements EOD sur la période</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <a href="{{ route('eod.n3.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour au tableau de bord
            </a>
        </div>
    </div>

    <!-- Filtres de période -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('eod.n3.statistiques') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input type="date" name="date_debut" value="{{ $dateDebut }}" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input type="date" name="date_fin" value="{{ $dateFin }}" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Statistiques par mois -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Évolution mensuelle</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mois</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validés</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rejetés</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux de validation</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($parMois as $mois)
                        @php
                            $taux = $mois->total > 0 ? round(($mois->valides / $mois->total) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium">{{ \Carbon\Carbon::createFromFormat('Y-m', $mois->mois)->format('F Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $mois->total }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-green-600">{{ $mois->valides }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-red-600">{{ $mois->rejetes }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="mr-2">{{ $taux }}%</span>
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $taux }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Aucune donnée pour cette période
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistiques par utilisateur -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Performance par utilisateur</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Département</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total fiches</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validées</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rejetées</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux succès</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée moy. (h)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($parUtilisateur as $util)
                        @php
                            $taux = $util->total > 0 ? round(($util->valides / $util->total) * 100, 1) : 0;
                            $user = App\Models\User::find($util->created_by);
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $user?->name ?? 'Utilisateur inconnu' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user?->departement ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $util->total }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-green-600">{{ $util->valides }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-red-600">{{ $util->rejetes }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($taux >= 80) bg-green-100 text-green-800
                                    @elseif($taux >= 50) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $taux }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $util->duree_moyenne ? number_format($util->duree_moyenne, 1) : '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Aucune donnée pour cette période
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Incidents les plus fréquents -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Incidents les plus fréquents</h2>
        </div>
        <div class="p-6">
            @if(count($incidentsFrequents) > 0)
                <div class="space-y-4">
                    @foreach($incidentsFrequents as $incident => $count)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">{{ $incident }}</span>
                            <span class="font-medium">{{ $count }} occurrence(s)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $max = max($incidentsFrequents);
                                $pourcentage = $max > 0 ? round(($count / $max) * 100) : 0;
                            @endphp
                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ $pourcentage }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Aucun incident signalé sur la période</p>
            @endif
        </div>
    </div>

    <!-- Graphique synthétique -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Synthèse de la période</h2>
        </div>
        <div class="p-6">
            @php
                $totalPeriod = array_sum($parMois->pluck('total')->toArray());
                $totalValides = array_sum($parMois->pluck('valides')->toArray());
                $totalRejetes = array_sum($parMois->pluck('rejetes')->toArray());
                $tauxGlobal = $totalPeriod > 0 ? round(($totalValides / $totalPeriod) * 100, 1) : 0;
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500 uppercase">Total fiches</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalPeriod }}</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500 uppercase">Taux de validation</p>
                    <p class="text-3xl font-bold text-green-600">{{ $tauxGlobal }}%</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500 uppercase">Moyenne mensuelle</p>
                    <p class="text-3xl font-bold text-indigo-600">{{ $parMois->count() > 0 ? round($totalPeriod / $parMois->count(), 1) : 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bouton d'export -->
    <div class="flex justify-end">
        <a href="{{ route('eod.n3.export', 'csv') }}?date_debut={{ $dateDebut }}&date_fin={{ $dateFin }}" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Exporter les statistiques
        </a>
    </div>
</div>
@endsection