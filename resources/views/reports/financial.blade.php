@extends('layouts.app')

@section('title', 'Rapport Financier')
@section('header', '💰 Rapport Financier')

@section('content')
<div class="mb-6">
    <!-- Filtres -->
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📅 Filtres</h2>
        <form method="GET" action="{{ route('reports.financial') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Année</label>
                <select name="year" id="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cofina-red focus:border-transparent">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-cofina-primary w-full">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        @php
            $totalInvestments = $monthlyInvestments->sum('total');
            $totalAmortization = $amortization->sum('total');
        @endphp
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold text-gray-600">Investissements {{ $year }}</h3>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($totalInvestments, 0, ',', ' ') }} FCFA</p>
            <p class="text-sm text-gray-500">Total annuel</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <h3 class="text-lg font-semibold text-gray-600">Amortissements {{ $year }}</h3>
            <p class="text-3xl font-bold text-green-600">{{ number_format($totalAmortization, 0, ',', ' ') }} FCFA</p>
            <p class="text-sm text-gray-500">Total annuel</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <h3 class="text-lg font-semibold text-gray-600">Différence</h3>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($totalInvestments - $totalAmortization, 0, ',', ' ') }} FCFA</p>
            <p class="text-sm text-gray-500">Investissements - Amortissements</p>
        </div>
    </div>

    <!-- Graphique des investissements par mois -->
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📈 Investissements par Mois ({{ $year }})</h2>
        <div style="position: relative; height: 300px;">
            <canvas id="monthlyInvestmentsChart"></canvas>
        </div>
    </div>

    <!-- Tableau des investissements par mois -->
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📋 Détail des Investissements par Mois</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mois</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pourcentage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Évolution</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                        $previousMonthValue = 0;
                    @endphp
                    
                    @for($i = 1; $i <= 12; $i++)
                        @php
                            $monthData = $monthlyInvestments->firstWhere('month', $i);
                            $monthValue = $monthData->total ?? 0;
                            $percentage = $totalInvestments > 0 ? round(($monthValue / $totalInvestments) * 100, 1) : 0;
                            $evolution = $previousMonthValue > 0 ? round((($monthValue - $previousMonthValue) / $previousMonthValue) * 100, 1) : 0;
                            $previousMonthValue = $monthValue;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $months[$i-1] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($monthValue, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                        <div class="bg-cofina-red h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span>{{ $percentage }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($evolution > 0)
                                    <span class="text-green-600 font-semibold">+{{ $evolution }}%</span>
                                @elseif($evolution < 0)
                                    <span class="text-red-600 font-semibold">{{ $evolution }}%</span>
                                @else
                                    <span class="text-gray-500">0%</span>
                                @endif
                            </td>
                        </tr>
                    @endfor
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-bold">Total {{ $year }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold text-blue-600">{{ number_format($totalInvestments, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold">100%</td>
                        <td class="px-6 py-4 whitespace-nowrap"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Investissements par type -->
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📊 Investissements par Type d'Équipement</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pourcentage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classement</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($investmentsByType as $index => $investment)
                    @php
                        $percentage = $totalInvestments > 0 ? round(($investment->total / $totalInvestments) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $investment->type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($investment->total, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                    <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span>{{ $percentage }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                                #{{ $index + 1 }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Investissements par fournisseur -->
    @if($investmentsBySupplier->count() > 0)
    <div class="card-cofina mb-6">
        <h2 class="text-xl font-bold text-cofina-red mb-4">🏭 Investissements par Fournisseur</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pourcentage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classement</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($investmentsBySupplier as $index => $supplier)
                    @php
                        $percentage = $totalInvestments > 0 ? round(($supplier['total'] / $totalInvestments) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ $supplier['name'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($supplier['total'], 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                    <div class="bg-purple-500 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span>{{ $percentage }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
                                #{{ $index + 1 }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Amortissements -->
    @if($amortization->count() > 0)
    <div class="card-cofina">
        <h2 class="text-xl font-bold text-cofina-red mb-4">📅 Calendrier d'Amortissement</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cumulé</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pourcentage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        $cumulative = 0;
                    @endphp
                    @foreach($amortization as $item)
                    @php
                        $cumulative += $item->total;
                        $percentage = $totalAmortization > 0 ? round(($cumulative / $totalAmortization) * 100, 1) : 0;
                        $monthName = $item->month ? \Carbon\Carbon::create()->month($item->month)->locale('fr')->monthName : '';
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold">
                            {{ $monthName }} {{ $item->year }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($cumulative, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                    <div class="bg-orange-500 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span>{{ $percentage }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @if($totalAmortization > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap font-bold">Total Amortissements</td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold text-green-600">{{ number_format($totalAmortization, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold">{{ number_format($cumulative, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold">100%</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Inclure Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique des investissements par mois
        const monthlyCtx = document.getElementById('monthlyInvestmentsChart').getContext('2d');
        
        @php
            $monthlyData = [];
            $monthlyLabels = [];
            
            for($i = 1; $i <= 12; $i++) {
                $monthData = $monthlyInvestments->firstWhere('month', $i);
                $monthlyData[] = $monthData->total ?? 0;
                $monthlyLabels[] = \Carbon\Carbon::create()->month($i)->locale('fr')->shortMonthName;
            }
        @endphp
        
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyLabels) !!},
                datasets: [{
                    label: 'Investissements (FCFA)',
                    data: {!! json_encode($monthlyData) !!},
                    backgroundColor: 'rgba(66, 153, 225, 0.2)',
                    borderColor: 'rgb(66, 153, 225)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'K';
                                }
                                return value;
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Investissement: ' + new Intl.NumberFormat('fr-FR', {
                                    style: 'currency',
                                    currency: 'XOF',
                                    minimumFractionDigits: 0
                                }).format(context.parsed.y);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection