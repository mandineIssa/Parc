@extends('layouts.app')

@section('title', 'Rapports et Statistiques')
@section('header', '📊 Tableau de Bord des Rapports')

@section('content')
<div class="mb-6">

    <!-- Cartes statistiques (toujours visibles) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-600">Total Équipements</h3>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($totalEquipment, 0, ',', ' ') }}</p>
            <p class="text-sm text-gray-500">enregistrés</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-600">Valeur Totale</h3>
            <p class="text-3xl font-bold text-green-600">{{ number_format($totalValue, 0, ',', ' ') }} FCFA</p>
            <p class="text-sm text-gray-500">d'investissement</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <h3 class="text-lg font-semibold text-gray-600">Sans Garantie</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ number_format($equipmentWithoutWarranty, 0, ',', ' ') }}</p>
            <p class="text-sm text-gray-500">équipements</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <h3 class="text-lg font-semibold text-gray-600">Catégories</h3>
            <p class="text-3xl font-bold text-purple-600">{{ $equipmentByCategoryTable->count() ?? 0 }}</p>
            <p class="text-sm text-gray-500">types différents</p>
        </div>
    </div>

    <!-- Boutons d'action rapides (toujours visibles) -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('reports.equipment') }}" class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:border-blue-500 transition duration-300 text-center">
            <div class="text-blue-500 mb-2">
                <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <h4 class="font-semibold text-gray-700">Rapport Équipements</h4>
        </a>
        <a href="{{ route('reports.financial') }}" class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:border-green-500 transition duration-300 text-center">
            <div class="text-green-500 mb-2">
                <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h4 class="font-semibold text-gray-700">Rapport Financier</h4>
        </a>
        <a href="{{ route('reports.categories') }}" class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:border-red-500 transition duration-300 text-center">
            <div class="text-red-500 mb-2">
                <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
            </div>
            <h4 class="font-semibold text-gray-700">Rapport Catégories</h4>
        </a>
        <a href="{{ route('reports.import.equipment') }}" class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:border-purple-500 transition duration-300 text-center">
            <div class="text-purple-500 mb-2">
                <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" /></svg>
            </div>
            <h4 class="font-semibold text-gray-700">Importer</h4>
        </a>
    </div>

    <!-- Boutons d'export (toujours visibles) -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('reports.export.equipment') }}" class="btn-cofina-primary text-center flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            Exporter Équipements
        </a>
        <a href="{{ route('reports.export.agencies') }}" class="btn-cofina-primary text-center flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            Exporter Agences
        </a>
        <a href="{{ route('reports.export.suppliers') }}" class="btn-cofina-primary text-center flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            Exporter Fournisseurs
        </a>
        <a href="{{ route('reports.export.categories') }}" class="btn-cofina-primary text-center flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            Exporter Catégories
        </a>
    </div>

    <!-- ===== SECTIONS ACCORDÉON ===== -->

    <!-- Accordéon : Graphiques -->
    <div class="card-cofina mb-4 overflow-hidden">
        <button onclick="toggleSection('section-charts')" class="w-full flex items-center justify-between px-4 py-4 text-left focus:outline-none">
            <h2 class="text-xl font-bold text-cofina-red">📊 Graphiques des Équipements</h2>
            <svg id="icon-section-charts" class="w-5 h-5 text-cofina-red transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="section-charts" class="hidden px-4 pb-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">📊 Équipements par Type</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="typeChart"></canvas>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">📈 Distribution par État</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="etatChart"></canvas>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">🏢 Équipements par Agence</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="agencyChart"></canvas>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">💰 Valeur Totale par Agence</h3>
                    <div style="position: relative; height: 300px;">
                        <canvas id="valueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accordéon : Statistiques par Type -->
    <div class="card-cofina mb-4 overflow-hidden">
        <button onclick="toggleSection('section-types')" class="w-full flex items-center justify-between px-4 py-4 text-left focus:outline-none">
            <h2 class="text-xl font-bold text-cofina-red">📋 Statistiques par Type d'Équipement</h2>
            <svg id="icon-section-types" class="w-5 h-5 text-cofina-red transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="section-types" class="hidden px-4 pb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pourcentage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur Totale</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur Moyenne</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($equipmentByType as $type)
                        @php
                            $value = \App\Models\Equipment::where('type', $type->type)->sum('prix');
                            $avg = \App\Models\Equipment::where('type', $type->type)->avg('prix');
                            $percentage = $totalEquipment > 0 ? round(($type->count / $totalEquipment) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $type->type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $type->count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                        <div class="bg-cofina-red h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span>{{ $percentage }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($value, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($avg, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Accordéon : Statistiques par Agence -->
    <div class="card-cofina mb-4 overflow-hidden">
        <button onclick="toggleSection('section-agencies')" class="w-full flex items-center justify-between px-4 py-4 text-left focus:outline-none">
            <h2 class="text-xl font-bold text-cofina-red">🏢 Statistiques par Agence</h2>
            <svg id="icon-section-agencies" class="w-5 h-5 text-cofina-red transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="section-agencies" class="hidden px-4 pb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre d'Équipements</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur Totale</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pourcentage du Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($equipmentByAgency as $agency)
                        @php
                            $percentage = $totalEquipment > 0 ? round(($agency['equipment_count'] / $totalEquipment) * 100, 1) : 0;
                            $agencyValue = $valueByAgency->firstWhere('agency_id', $agency['agency_id'])['total_value'] ?? 0;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $agency['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $agency['equipment_count'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($agencyValue, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                        <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
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
    </div>

    <!-- Accordéon : Top 5 les plus chers -->
    @if($topExpensive->count() > 0)
    <div class="card-cofina mb-4 overflow-hidden">
        <button onclick="toggleSection('section-top5')" class="w-full flex items-center justify-between px-4 py-4 text-left focus:outline-none">
            <h2 class="text-xl font-bold text-cofina-red">🏆 Top 5 Équipements les Plus Chers</h2>
            <svg id="icon-section-top5" class="w-5 h-5 text-cofina-red transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="section-top5" class="hidden px-4 pb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro Série</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marque/Modèle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Livraison</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($topExpensive as $index => $equipment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $equipment->type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono">{{ $equipment->numero_serie }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $equipment->marque }} {{ $equipment->modele }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $equipment->agence ? $equipment->agence->nom : 'Non attribué' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-green-600">{{ number_format($equipment->prix, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $equipment->date_livraison ? $equipment->date_livraison->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Accordéon : Statistiques par Fournisseur -->
    @if($equipmentBySupplier->count() > 0)
    <div class="card-cofina mb-4 overflow-hidden">
        <button onclick="toggleSection('section-suppliers')" class="w-full flex items-center justify-between px-4 py-4 text-left focus:outline-none">
            <h2 class="text-xl font-bold text-cofina-red">🏭 Statistiques par Fournisseur</h2>
            <svg id="icon-section-suppliers" class="w-5 h-5 text-cofina-red transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        <div id="section-suppliers" class="hidden px-4 pb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre d'Équipements</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur Totale</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($equipmentBySupplier as $supplier)
                        @php
                            $supplierModel = \App\Models\Supplier::find($supplier['fournisseur_id']);
                            $supplierValue = \App\Models\Equipment::where('fournisseur_id', $supplier['fournisseur_id'])->sum('prix');
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $supplier['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $supplier['equipment_count'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($supplierValue, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($supplierModel)
                                    <span class="px-2 py-1 text-xs rounded-full {{ 
                                        $supplierModel->status == 'active' ? 'bg-green-100 text-green-800' : 
                                        ($supplierModel->status == 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') 
                                    }}">
                                        {{ ucfirst($supplierModel->status) }}
                                    </span>
                                @else
                                    <span class="text-gray-500">Inconnu</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ---- Accordéon ----
    let chartsInitialized = false;

    function toggleSection(id) {
        const section = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        const isHidden = section.classList.contains('hidden');

        section.classList.toggle('hidden');
        icon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';

        // Initialiser les graphiques seulement quand la section s'ouvre
        if (id === 'section-charts' && isHidden && !chartsInitialized) {
            initCharts();
            chartsInitialized = true;
        }
    }

    // ---- Graphiques ----
    function initCharts() {
        // Graphique 1 : Équipements par Type
        new Chart(document.getElementById('typeChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($equipmentByType->pluck('type')) !!},
                datasets: [{
                    label: 'Nombre d\'équipements',
                    data: {!! json_encode($equipmentByType->pluck('count')) !!},
                    backgroundColor: '#D32F2F',
                    borderColor: '#B71C1C',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => 'Équipements: ' + ctx.parsed.y } }
                }
            }
        });

        // Graphique 2 : Équipements par État
        new Chart(document.getElementById('etatChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($equipmentByEtat->pluck('etat')) !!},
                datasets: [{
                    data: {!! json_encode($equipmentByEtat->pluck('count')) !!},
                    backgroundColor: ['#4CAF50', '#2196F3', '#FFC107', '#F44336'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 15 } },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                return `${ctx.label}: ${ctx.parsed} (${Math.round((ctx.parsed / total) * 100)}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Graphique 3 : Équipements par Agence
        new Chart(document.getElementById('agencyChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($equipmentByAgency->pluck('name')) !!},
                datasets: [{
                    label: 'Nombre d\'équipements',
                    data: {!! json_encode($equipmentByAgency->pluck('equipment_count')) !!},
                    backgroundColor: '#757575',
                    borderColor: '#424242',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => 'Équipements: ' + ctx.parsed.y } }
                }
            }
        });

        // Graphique 4 : Valeur par Agence
        new Chart(document.getElementById('valueChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($valueByAgency->pluck('name')) !!},
                datasets: [{
                    label: 'Valeur totale (FCFA)',
                    data: {!! json_encode($valueByAgency->pluck('total_value')) !!},
                    backgroundColor: '#4CAF50',
                    borderColor: '#388E3C',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => v >= 1000000 ? (v/1000000).toFixed(1)+'M' : v >= 1000 ? (v/1000).toFixed(0)+'K' : v
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => 'Valeur: ' + new Intl.NumberFormat('fr-FR', {
                                style: 'currency', currency: 'XOF', minimumFractionDigits: 0
                            }).format(ctx.parsed.y)
                        }
                    }
                }
            }
        });
    }
</script>
@endsection