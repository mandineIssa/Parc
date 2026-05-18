@extends('layouts.app')

@section('title', 'Renouvellement des équipements')
@section('header', 'Plan de renouvellement')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row justify-end items-center mb-8 gap-4">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('equipment.renewal.export', request()->only(['search', 'statut'])) }}"
               class="inline-flex items-center px-4 py-2 rounded-lg bg-[#A61B29] hover:bg-[#7A0C1A] text-white font-semibold text-sm shadow-sm transition-colors">
                Exporter à remplacer ({{ $counts['a_remplacer'] }})
            </a>
            <a href="{{ route('equipment.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 font-medium text-sm">
                ← Inventaire équipements
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <a href="{{ route('equipment.renewal') }}" class="rounded-xl border p-4 {{ !$niveau ? 'ring-2 ring-blue-500 border-blue-200 bg-blue-50/40' : 'border-gray-200 bg-white hover:bg-gray-50' }}">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Avec dates</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $counts['avec_dates'] }}</p>
        </a>
        <a href="{{ route('equipment.renewal', array_merge(request()->except('page'), ['niveau' => 'recent'])) }}" class="rounded-xl border p-4 {{ $niveau === 'recent' ? 'ring-2 ring-emerald-500 border-emerald-200 bg-emerald-50/40' : 'border-gray-200 bg-white hover:bg-gray-50' }}">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Récent</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $counts['recent'] }}</p>
        </a>
        <a href="{{ route('equipment.renewal', array_merge(request()->except('page'), ['niveau' => 'seuil_reference'])) }}" class="rounded-xl border p-4 {{ $niveau === 'seuil_reference' ? 'ring-2 ring-orange-500 border-orange-200 bg-orange-50/40' : 'border-gray-200 bg-white hover:bg-gray-50' }}">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-orange-500"></span>Seuil de référence</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $counts['seuil_reference'] }}</p>
        </a>
        <a href="{{ route('equipment.renewal', array_merge(request()->except('page'), ['niveau' => 'a_remplacer'])) }}" class="rounded-xl border p-4 {{ $niveau === 'a_remplacer' ? 'ring-2 ring-red-500 border-red-200 bg-red-50/40' : 'border-gray-200 bg-white hover:bg-gray-50' }}">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-600"></span>À remplacer</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $counts['a_remplacer'] }}</p>
        </a>
        <a href="{{ route('equipment.renewal', array_merge(request()->except('page'), ['niveau' => 'inconnu'])) }}" class="rounded-xl border p-4 {{ $niveau === 'inconnu' ? 'ring-2 ring-gray-400 border-gray-300 bg-gray-50' : 'border-gray-200 bg-white hover:bg-gray-50' }}">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Sans date</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $counts['inconnu'] }}</p>
        </a>
    </div>

    <form method="get" action="{{ route('equipment.renewal') }}" class="bg-white rounded-xl shadow-md p-6 mb-6 flex flex-col md:flex-row gap-4 flex-wrap items-end">
        @if($niveau)
            <input type="hidden" name="niveau" value="{{ $niveau }}">
        @endif
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="N° série, marque, modèle…"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="w-full md:w-48">
            <label class="block text-sm font-medium text-gray-700 mb-1">Statut métier</label>
            <select name="statut" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500">
                <option value="">Tous</option>
                <option value="stock" {{ $statut === 'stock' ? 'selected' : '' }}>Stock</option>
                <option value="parc" {{ $statut === 'parc' ? 'selected' : '' }}>Parc</option>
                <option value="maintenance" {{ $statut === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="hors_service" {{ $statut === 'hors_service' ? 'selected' : '' }}>Hors service</option>
                <option value="perdu" {{ $statut === 'perdu' ? 'selected' : '' }}>Perdu</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
            Filtrer
        </button>
        @if($search !== '' || $statut)
            <a href="{{ route('equipment.renewal', $niveau ? ['niveau' => $niveau] : []) }}" class="text-sm text-blue-600 hover:underline py-2">Réinitialiser recherche</a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorité</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipement</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Âge</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Référence</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Renouvellement</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Statut</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($equipments as $equipment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-700">
                                #{{ $equipments->firstItem() + $loop->index }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $equipment->nom ?? ($equipment->marque.' '.$equipment->modele) }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $equipment->numero_serie }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">
                                @if($equipment->age_equipement_annees !== null)
                                    {{ number_format($equipment->age_equipement_annees, 2, ',', ' ') }} ans
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">
                                @php $ref = $equipment->lifecycleReferenceDate(); @endphp
                                @if($ref)
                                    <div>{{ $ref->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $equipment->date_mise_service ? 'Mise en service' : 'Livraison (repli)' }}
                                    </div>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @include('equipment.partials.renewal-badge', ['equipment' => $equipment])
                                <div class="text-xs text-gray-500 mt-1 max-w-xs">{{ $equipment->libelleRenouvellementLong() }}</div>
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                <span class="text-sm capitalize">{{ str_replace('_', ' ', $equipment->statut) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('equipment.show', $equipment) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Fiche</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                Aucun équipement ne correspond aux critères.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            {{ $equipments->links() }}
        </div>
    </div>
</div>
@endsection
