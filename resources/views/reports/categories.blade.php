@extends('layouts.app')

@section('title', 'Rapport des Catégories')
@section('header', '📊 Rapport des Catégories')

@section('content')
<div class="mb-6">
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-600">Total Catégories</h3>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total_categories'], 0, ',', ' ') }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-600">Avec Équipements</h3>
            <p class="text-3xl font-bold text-green-600">{{ number_format($stats['total_with_equipment'], 0, ',', ' ') }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <h3 class="text-lg font-semibold text-gray-600">Vides</h3>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['total_categories'] - $stats['total_with_equipment'], 0, ',', ' ') }}</p>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="mb-6">
        <a href="{{ route('reports.export.categories') }}" class="btn-cofina-primary inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Exporter les Catégories
        </a>
    </div>

    <!-- Tableau des catégories -->
    <div class="card-cofina">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📋 Liste des Catégories</h2>
        
        <!-- Filtres -->
        <div class="mb-4 flex flex-col md:flex-row gap-4">
            <div class="w-full md:w-1/3">
                <input type="text" id="search" placeholder="Rechercher..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
            </div>
            <div class="w-full md:w-1/4">
                <select id="typeFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
                    <option value="">Tous les types</option>
                    <option value="réseaux">Réseaux</option>
                    <option value="électronique">Électronique</option>
                    <option value="informatiques">Informatiques</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white" id="categoriesTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre d'Équipements</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($categories as $category)
                    <tr data-type="{{ $category->type }}" data-search="{{ strtolower($category->nom . ' ' . $category->description) }}">
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-cofina-red">
                            {{ $category->nom }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                {{ $category->type ?? 'Non spécifié' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $category->parent ? $category->parent->nom : 'Aucun' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-sm rounded-full 
                                {{ $category->equipment_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $category->equipment_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs truncate" title="{{ $category->description }}">
                                {{ $category->description ?? 'Aucune description' }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Top 5 catégories -->
    @if($stats['top_categories']->count() > 0)
    <div class="card-cofina mt-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">🏆 Top 5 Catégories avec le Plus d'Équipements</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre d'Équipements</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pourcentage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($stats['top_categories'] as $index => $category)
                    @php
                        $percentage = $stats['total_with_equipment'] > 0 ? 
                            round(($category->equipment_count / $stats['total_with_equipment']) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $category->nom }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $category->type ?? 'Non spécifié' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $category->equipment_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                    <div class="bg-cofina-red h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span>{{ $percentage }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Répartition par type -->
    @if(!empty($stats['total_by_type']))
    <div class="card-cofina mt-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📊 Répartition par Type</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de Catégories</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pourcentage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($stats['total_by_type'] as $type => $count)
                    @php
                        $percentage = $stats['total_categories'] > 0 ? 
                            round(($count / $stats['total_categories']) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ ucfirst($type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                    <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span>{{ $percentage }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const typeFilter = document.getElementById('typeFilter');
        const rows = document.querySelectorAll('#categoriesTable tbody tr');
        
        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedType = typeFilter.value;
            
            rows.forEach(row => {
                const searchText = row.getAttribute('data-search');
                const rowType = row.getAttribute('data-type');
                
                const matchesSearch = searchText.includes(searchTerm);
                const matchesType = !selectedType || rowType === selectedType;
                
                if (matchesSearch && matchesType) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        searchInput.addEventListener('input', filterTable);
        typeFilter.addEventListener('change', filterTable);
    });
</script>
@endsection