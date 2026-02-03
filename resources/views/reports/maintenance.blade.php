@extends('layouts.app')

@section('title', 'Rapport de Maintenance')
@section('header', '🔧 Rapport de Maintenance')

@section('content')
<div class="mb-6">
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-600">Total</h3>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['total'], 0, ',', ' ') }}</p>
            <p class="text-sm text-gray-500">interventions</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-600">Terminées</h3>
            <p class="text-3xl font-bold text-green-600">{{ number_format($stats['termines'], 0, ',', ' ') }}</p>
            <p class="text-sm text-gray-500">interventions</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <h3 class="text-lg font-semibold text-gray-600">En Cours</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['en_cours'], 0, ',', ' ') }}</p>
            <p class="text-sm text-gray-500">interventions</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <h3 class="text-lg font-semibold text-gray-600">Coût Total</h3>
            <p class="text-3xl font-bold text-red-600">{{ number_format($stats['cout_total'], 0, ',', ' ') }} FCFA</p>
            <p class="text-sm text-gray-500">dépensés</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <h3 class="text-lg font-semibold text-gray-600">Coût Moyen</h3>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['cout_moyen'], 0, ',', ' ') }} FCFA</p>
            <p class="text-sm text-gray-500">par intervention</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">🔍 Filtres de Recherche</h2>
        <form method="GET" action="{{ route('reports.maintenance') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="type_maintenance" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type_maintenance" id="type_maintenance" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
                    <option value="">Tous les types</option>
                    @foreach($types as $typeOption)
                        <option value="{{ $typeOption }}" {{ request('type_maintenance') == $typeOption ? 'selected' : '' }}>{{ $typeOption }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="statut" id="statut" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
                    <option value="">Tous les statuts</option>
                    <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En Cours</option>
                    <option value="termine" {{ request('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                    <option value="planifie" {{ request('statut') == 'planifie' ? 'selected' : '' }}>Planifié</option>
                    <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date Début</label>
                <input type="date" name="date_from" id="date_from" 
                       value="{{ request('date_from') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
            </div>
            
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date Fin</label>
                <input type="date" name="date_to" id="date_to" 
                       value="{{ request('date_to') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
            </div>
            
            <div class="md:col-span-4 flex items-end space-x-2">
                <button type="submit" class="btn-cofina-primary flex-1">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filtrer
                </button>
                <a href="{{ route('reports.maintenance') }}" class="btn-cofina-secondary px-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </a>
            </div>
        </form>
    </div>

    <!-- Graphiques de maintenance -->
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📈 Statistiques de Maintenance</h2>
        
        <!-- Sélecteur de graphique -->
        <div class="mb-6">
            <div class="flex flex-wrap gap-2 mb-4">
                <button onclick="showChart('costEvolution')" class="chart-btn active" id="btn-costEvolution">
                    Évolution des Coûts
                </button>
                <button onclick="showChart('costByType')" class="chart-btn" id="btn-costByType">
                    Coût par Type
                </button>
                <button onclick="showChart('costByStatus')" class="chart-btn" id="btn-costByStatus">
                    Coût par Statut
                </button>
                <button onclick="showChart('interventionsByMonth')" class="chart-btn" id="btn-interventionsByMonth">
                    Interventions par Mois
                </button>
                @if($hasPrestataireColumn && count($costsByPrestataire) > 0)
                <button onclick="showChart('costByPrestataire')" class="chart-btn" id="btn-costByPrestataire">
                    Coût par Prestataire
                </button>
                @endif
            </div>
        </div>

        <!-- Conteneur des graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Graphique 1: Évolution des coûts -->
            <div class="chart-container" id="chart-costEvolution">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">📈 Évolution des Coûts (12 derniers mois)</h3>
                    <canvas id="costEvolutionChart" height="250"></canvas>
                    <div class="mt-4 text-sm text-gray-600">
                        <p>Coût total sur 12 mois: <strong>{{ number_format(array_sum($monthlyCosts->toArray()), 0, ',', ' ') }} FCFA</strong></p>
                    </div>
                </div>
            </div>

            <!-- Graphique 2: Coût par type -->
            <div class="chart-container hidden" id="chart-costByType">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">🔧 Coût par Type de Maintenance</h3>
                    <canvas id="costByTypeChart" height="250"></canvas>
                    <div class="mt-4 text-sm text-gray-600">
                        <p>Coût moyen par intervention: <strong>{{ number_format($stats['cout_moyen'], 0, ',', ' ') }} FCFA</strong></p>
                    </div>
                </div>
            </div>

            <!-- Graphique 3: Coût par statut -->
            <div class="chart-container hidden" id="chart-costByStatus">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">📊 Coût par Statut d'Intervention</h3>
                    <canvas id="costByStatusChart" height="250"></canvas>
                    <div class="mt-4 text-sm text-gray-600">
                        <p>Taux de complétion: <strong>{{ $stats['total'] > 0 ? round(($stats['termines'] / $stats['total']) * 100, 1) : 0 }}%</strong></p>
                    </div>
                </div>
            </div>

            <!-- Graphique 4: Interventions par mois -->
            <div class="chart-container hidden" id="chart-interventionsByMonth">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">📅 Interventions par Mois</h3>
                    <canvas id="interventionsByMonthChart" height="250"></canvas>
                    <div class="mt-4 text-sm text-gray-600">
                        <p>Total d'interventions sur 12 mois: <strong>{{ array_sum($monthlyCounts->toArray()) }}</strong></p>
                    </div>
                </div>
            </div>

            <!-- Graphique 5: Coût par prestataire -->
            @if($hasPrestataireColumn && count($costsByPrestataire) > 0)
            <div class="chart-container hidden" id="chart-costByPrestataire">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">👥 Coût par Prestataire</h3>
                    <canvas id="costByPrestataireChart" height="250"></canvas>
                    <div class="mt-4 text-sm text-gray-600">
                        <p>Nombre de prestataires: <strong>{{ count($costsByPrestataire) }}</strong></p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Tableau des maintenances -->
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📋 Liste des Interventions de Maintenance</h2>
        
        <div class="mb-4">
            <p class="text-sm text-gray-600">
                Affichage de {{ $maintenances->firstItem() }} à {{ $maintenances->lastItem() }} sur {{ $maintenances->total() }} interventions
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technicien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coût</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($maintenances as $maintenance)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($maintenance->equipment)
                                <div class="font-semibold">{{ $maintenance->equipment->type }}</div>
                                <div class="text-sm text-gray-500">{{ $maintenance->equipment->numero_serie }}</div>
                            @else
                                <span class="text-gray-400">Équipement supprimé</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                {{ $maintenance->type_maintenance ?? 'Non spécifié' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">{{ $maintenance->date_depart ? \Carbon\Carbon::parse($maintenance->date_depart)->format('d/m/Y') : 'N/A' }}</div>
                            @if($maintenance->date_retour_prevue)
                                <div class="text-xs text-gray-500">Fin: {{ \Carbon\Carbon::parse($maintenance->date_retour_prevue)->format('d/m/Y') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $maintenance->prestataire ?? 'Non spécifié' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statutColors = [
                                    'en_cours' => 'bg-yellow-100 text-yellow-800',
                                    'termine' => 'bg-green-100 text-green-800',
                                    'terminee' => 'bg-green-100 text-green-800',
                                    'planifie' => 'bg-blue-100 text-blue-800',
                                    'annule' => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statutColors[$maintenance->statut] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$maintenance->statut] ?? ucfirst(str_replace('_', ' ', $maintenance->statut)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold {{ $maintenance->cout > 0 ? 'text-red-600' : 'text-gray-500' }}">
                            {{ number_format($maintenance->cout, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-xs truncate" title="{{ $maintenance->description_panne ?? $maintenance->description }}">
                                {{ Str::limit($maintenance->description_panne ?? $maintenance->description, 50) }}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $maintenances->links() }}
        </div>
    </div>

    <!-- Équipements les plus défaillants -->
    @if($mostFailing->count() > 0)
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">⚠️ Top 10 Équipements les Plus Défaillants</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Équipement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre d'Interventions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux de Défaillance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière Intervention</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($mostFailing as $index => $item)
                    @php
                        $rate = $stats['total'] > 0 ? round(($item['count'] / $stats['total']) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-sm rounded-full {{ $index < 3 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                #{{ $index + 1 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">
                            {{ $item['equipment'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-sm rounded-full {{ $item['count'] > 5 ? 'bg-red-100 text-red-800' : ($item['count'] > 2 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ $item['count'] }} intervention(s)
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                    <div class="bg-red-500 h-2.5 rounded-full" style="width: {{ min($rate, 100) }}%"></div>
                                </div>
                                <span>{{ $rate }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @php
                                // Solution simplifiée : chercher par numéro de série
                                $serial = $item['numero_serie'] ?? '';
                                if (empty($serial) && str_contains($item['equipment'] ?? '', ' - ')) {
                                    $parts = explode(' - ', $item['equipment']);
                                    $serial = end($parts);
                                }
                                
                                $lastMaintenance = \App\Models\Maintenance::where('numero_serie', $serial)
                                    ->latest('date_depart')
                                    ->first();
                            @endphp
                            {{ $lastMaintenance && $lastMaintenance->date_depart ? 
                                \Carbon\Carbon::parse($lastMaintenance->date_depart)->format('d/m/Y') : 'N/A' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Inclure Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables PHP converties en JavaScript
        const months = @json($months);
        const monthlyCosts = @json($monthlyCosts);
        const monthlyCounts = @json($monthlyCounts);
        
        const types = @json($types);
        const costsByType = @json($costsByType);
        const countsByType = @json($countsByType);
        
        const statuses = @json($statuses);
        const statusLabels = @json($statusLabels);
        const costsByStatus = @json($costsByStatus);
        const countsByStatus = @json($countsByStatus);
        
        const costsByPrestataire = @json($costsByPrestataire);
        const countsByPrestataire = @json($countsByPrestataire);
        
        // Couleurs pour les graphiques
        const colors = {
            primary: '#3B82F6',    // Blue
            success: '#10B981',    // Green
            warning: '#F59E0B',    // Yellow
            danger: '#EF4444',     // Red
            info: '#8B5CF6',       // Purple
            secondary: '#6B7280'   // Gray
        };

        // ===========================================
        // 1. Graphique d'évolution des coûts
        // ===========================================
        const costEvolutionCtx = document.getElementById('costEvolutionChart')?.getContext('2d');
        if (costEvolutionCtx) {
            const costEvolutionChart = new Chart(costEvolutionCtx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Coût mensuel (FCFA)',
                        data: monthlyCosts,
                        borderColor: colors.danger,
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Coût: ${context.raw.toLocaleString()} FCFA`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' FCFA';
                                }
                            }
                        }
                    }
                }
            });
        }

        // ===========================================
        // 2. Graphique des coûts par type
        // ===========================================
        const costByTypeCtx = document.getElementById('costByTypeChart')?.getContext('2d');
        if (costByTypeCtx) {
            const costByTypeChart = new Chart(costByTypeCtx, {
                type: 'bar',
                data: {
                    labels: types,
                    datasets: [{
                        label: 'Coût total (FCFA)',
                        data: types.map(type => costsByType[type] || 0),
                        backgroundColor: [
                            colors.primary,
                            colors.success,
                            colors.warning,
                            colors.info,
                            colors.secondary
                        ],
                        borderColor: [
                            colors.primary,
                            colors.success,
                            colors.warning,
                            colors.info,
                            colors.secondary
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Coût: ${context.raw.toLocaleString()} FCFA`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' FCFA';
                                }
                            }
                        }
                    }
                }
            });
        }

        // ===========================================
        // 3. Graphique des coûts par statut
        // ===========================================
        const costByStatusCtx = document.getElementById('costByStatusChart')?.getContext('2d');
        if (costByStatusCtx) {
            const costByStatusChart = new Chart(costByStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: statuses.map(status => statusLabels[status]),
                    datasets: [{
                        data: statuses.map(status => costsByStatus[status] || 0),
                        backgroundColor: [
                            colors.warning,   // en_cours
                            colors.success,   // termine/terminee
                            colors.primary,   // planifie
                            colors.danger,    // annule
                            colors.info       // autres
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${context.label}: ${value.toLocaleString()} FCFA (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // ===========================================
        // 4. Graphique des interventions par mois
        // ===========================================
        const interventionsByMonthCtx = document.getElementById('interventionsByMonthChart')?.getContext('2d');
        if (interventionsByMonthCtx) {
            const interventionsByMonthChart = new Chart(interventionsByMonthCtx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Nombre d\'interventions',
                        data: monthlyCounts,
                        backgroundColor: colors.info,
                        borderColor: colors.info,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // ===========================================
        // 5. Graphique des coûts par prestataire
        // ===========================================
        const costByPrestataireCtx = document.getElementById('costByPrestataireChart')?.getContext('2d');
        if (costByPrestataireCtx && Object.keys(costsByPrestataire).length > 0) {
            const prestataireLabels = Object.keys(costsByPrestataire);
            const prestataireCosts = Object.values(costsByPrestataire);
            const prestataireCounts = Object.values(countsByPrestataire);
            
            const costByPrestataireChart = new Chart(costByPrestataireCtx, {
                type: 'bar',
                data: {
                    labels: prestataireLabels,
                    datasets: [{
                        label: 'Coût total (FCFA)',
                        data: prestataireCosts,
                        backgroundColor: colors.primary,
                        borderColor: colors.primary,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const prestataire = prestataireLabels[context.dataIndex];
                                    const cost = context.raw;
                                    const count = prestataireCounts[context.dataIndex];
                                    return `${prestataire}: ${cost.toLocaleString()} FCFA (${count} interventions)`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' FCFA';
                                }
                            }
                        }
                    }
                }
            });
        }

        // ===========================================
        // Fonctions pour changer de graphique
        // ===========================================
        function showChart(chartName) {
            // Cacher tous les conteneurs de graphiques
            document.querySelectorAll('.chart-container').forEach(container => {
                container.classList.add('hidden');
            });
            
            // Désactiver tous les boutons
            document.querySelectorAll('.chart-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Afficher le graphique sélectionné
            const chartElement = document.getElementById(`chart-${chartName}`);
            const btnElement = document.getElementById(`btn-${chartName}`);
            
            if (chartElement) {
                chartElement.classList.remove('hidden');
            }
            if (btnElement) {
                btnElement.classList.add('active');
            }
        }
        
        // Initialiser le premier graphique comme visible
        showChart('costEvolution');
    });
    // AJAX pour filtrer sans rechargement
document.querySelector('form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const params = new URLSearchParams(formData);
    
    // Afficher un indicateur de chargement
    const loader = document.createElement('div');
    loader.className = 'loading-indicator';
    loader.innerHTML = 'Chargement des données...';
    document.querySelector('.chart-container').prepend(loader);
    
    try {
        const response = await fetch(`{{ route('reports.maintenance') }}?${params}`);
        const html = await response.text();
        
        // Parser la réponse HTML
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Extraire les nouvelles données
        const newMonths = JSON.parse(doc.querySelector('#months-data')?.textContent || '[]');
        const newMonthlyCosts = JSON.parse(doc.querySelector('#monthly-costs-data')?.textContent || '[]');
        
        // Mettre à jour les graphiques
        updateCharts(newMonths, newMonthlyCosts);
        
        // Mettre à jour le tableau aussi
        updateTable(doc.querySelector('table tbody').innerHTML);
        
        loader.remove();
    } catch (error) {
        console.error('Erreur:', error);
        loader.innerHTML = 'Erreur de chargement';
    }
});

function updateCharts(months, monthlyCosts) {
    if (window.costEvolutionChart) {
        window.costEvolutionChart.data.labels = months;
        window.costEvolutionChart.data.datasets[0].data = monthlyCosts;
        window.costEvolutionChart.update();
    }
}
</script>

<style>
    .chart-btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
        background-color: #f3f4f6;
        color: #4b5563;
        border: none;
        cursor: pointer;
    }
    
    .chart-btn:hover {
        background-color: #e5e7eb;
    }
    
    .chart-btn.active {
        background-color: #dc2626;
        color: white;
    }
    
    .chart-container {
        transition: opacity 0.3s ease;
    }
    
    .chart-container.hidden {
        display: none;
    }
</style>
@endsection