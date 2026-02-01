@extends('layouts.app')

@section('title', 'Dashboard Agent IT')

@section('header', 'Mon Espace Agent IT')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-cofina-red mb-2">🛠️ Dashboard Agent IT</h1>
        <p class="text-gray-600">Bienvenue {{ auth()->user()->name }}! Gestion de vos équipements et soumissions</p>
    </div>

    <!-- Statistiques personnelles -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Mes soumissions -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Mes soumissions</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $personalStats['my_total_submissions'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <span class="text-2xl">📝</span>
                </div>
            </div>
        </div>

        <!-- En attente -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">En attente</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $personalStats['my_pending_submissions'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <span class="text-2xl">⏳</span>
                </div>
            </div>
        </div>

        <!-- Approuvées -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Approuvées</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $personalStats['my_approved_submissions'] }}</p>
                    <p class="text-sm text-green-600">{{ $personalStats['approval_rate'] }}% taux</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <span class="text-2xl">✅</span>
                </div>
            </div>
        </div>

        <!-- Équipements gérés -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500">Équipements gérés</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $personalStats['equipments_managed'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <span class="text-2xl">💻</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    @if(count($quickActions) > 0)
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">⚡ Actions rapides</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($quickActions as $action)
            <a href="{{ $action['action'] }}" 
               class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow border-l-4 border-{{ $action['color'] }}-500">
                <div class="flex items-center">
                    <div class="text-3xl mr-4">{{ $action['icon'] }}</div>
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $action['title'] }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $action['description'] }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Mes soumissions récentes -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-gray-800">Mes soumissions récentes</h2>
            <a href="{{ route('transitions.create') }}" class="btn-cofina text-sm">
                + Nouvelle soumission
            </a>
        </div>

        @if($mySubmissions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Équipement
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($mySubmissions as $submission)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">
                                {{ $submission->equipment->nom ?? 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $submission->equipment->numero_serie ?? '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">
                                {{ $submission->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($submission->status)
                                @case('pending')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        ⏳ En attente
                                    </span>
                                    @break
                                @case('approved')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        ✅ Approuvé
                                    </span>
                                    @break
                                @case('rejected')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        ❌ Rejeté
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $submission->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('transitions.approval.show', $submission) }}" 
                               class="text-cofina-blue hover:text-cofina-red">
                                👁️ Voir
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($mySubmissions->hasPages())
        <div class="mt-6">
            {{ $mySubmissions->withQueryString()->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-8 text-gray-500">
            <div class="text-4xl mb-4">📭</div>
            <p class="text-lg">Aucune soumission pour le moment</p>
            <p class="text-sm mt-2">Créez votre première soumission pour commencer</p>
            <a href="{{ route('transitions.create') }}" class="btn-cofina mt-4 inline-block">
                Créer une soumission
            </a>
        </div>
        @endif
    </div>

    <!-- Équipements à gérer -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Équipements en stock -->
        @if($equipmentsToManage['stock_equipments']->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">📦 Équipements en stock</h2>
            <div class="space-y-4">
                @foreach($equipmentsToManage['stock_equipments'] as $equipment)
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div>
                        <div class="font-medium">{{ $equipment->nom }}</div>
                        <div class="text-sm text-gray-500">{{ $equipment->numero_serie }}</div>
                    </div>
                    <a href="{{ route('equipment.show', $equipment) }}" 
                       class="btn-cofina-outline text-xs">
                        Gérer
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Maintenances à suivre -->
        @if($equipmentsToManage['maintenance_equipments']->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">🔧 Maintenances à suivre</h2>
            <div class="space-y-4">
                @foreach($equipmentsToManage['maintenance_equipments'] as $equipment)
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <div>
                        <div class="font-medium">{{ $equipment->nom }}</div>
                        <div class="text-sm text-gray-500">
                            Retour: {{ $equipment->maintenance->date_retour_prevue->format('d/m/Y') }}
                        </div>
                    </div>
                    <a href="{{ route('maintenance.show', $equipment->maintenance) }}" 
                       class="btn-cofina-outline text-xs">
                        Suivre
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection