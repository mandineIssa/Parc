@extends('layouts.app')

@section('title', 'Tableau de Bord - Contrôles IT')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Tableau de Bord des Contrôles IT</h1>
        <p class="text-gray-500 mt-1">Vue d'ensemble de l'activité de contrôle</p>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Mes tâches en attente</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['my_pending_tasks'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Tâches en retard</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['my_overdue_tasks'] ?? 0 }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">À valider (superviseur)</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['to_validate'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Taux de conformité global</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['total_compliance_rate'] ?? 0 }}%</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Mes tâches récentes -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">Mes tâches récentes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tâche</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date limite</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentTasks ?? [] as $task)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('controls.tasks.show', $task) }}" class="text-red-600 hover:text-red-800 text-sm">
                                    {{ Str::limit($task->title, 40) }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm {{ method_exists($task, 'isOverdue') && $task->isOverdue() && $task->status != 'completed' ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                {{ $task->due_date->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($task->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($task->status == 'completed') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('controls.tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                    Traiter →
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                Aucune tâche assignée
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t bg-gray-50">
                <a href="{{ route('controls.tasks.index') }}" class="text-sm text-red-600 hover:text-red-800">
                    Voir toutes mes tâches →
                </a>
            </div>
        </div>

        <!-- Liens rapides -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">Actions rapides</h3>
            </div>
            <div class="p-4 space-y-3">
                <a href="{{ route('controls.tasks.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                    <div class="bg-blue-100 rounded-full p-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Mes tâches</p>
                        <p class="text-xs text-gray-500">Consulter et traiter mes contrôles assignés</p>
                    </div>
                </a>

                <a href="{{ route('controls.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                    <div class="bg-green-100 rounded-full p-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Tous les contrôles</p>
                        <p class="text-xs text-gray-500">Gérer les contrôles et leurs configurations</p>
                    </div>
                </a>

                @if(auth()->user() && (auth()->user()->is_admin ?? false))
                <a href="{{ route('controls.templates.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                    <div class="bg-purple-100 rounded-full p-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Templates</p>
                        <p class="text-xs text-gray-500">Gérer les modèles de contrôle</p>
                    </div>
                </a>
                @endif

                <a href="{{ route('controls.create') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                    <div class="bg-red-100 rounded-full p-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">Nouveau contrôle</p>
                        <p class="text-xs text-gray-500">Créer un nouveau contrôle IT</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Indicateurs de performance -->
    <div class="mt-6 bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Indicateurs de performance</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Taux de conformité global</p>
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-3">
                        <div class="bg-green-500 rounded-full h-3 transition-all" style="width: {{ $stats['total_compliance_rate'] ?? 0 }}%"></div>
                    </div>
                    <span class="text-lg font-bold">{{ $stats['total_compliance_rate'] ?? 0 }}%</span>
                </div>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Tâches complétées vs total</p>
                @php
                    $totalTasks = App\Models\ControlTask::count();
                    $completedTasks = App\Models\ControlTask::where('status', 'completed')->count();
                    $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;
                @endphp
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-500 rounded-full h-3 transition-all" style="width: {{ $completionRate }}%"></div>
                    </div>
                    <span class="text-lg font-bold">{{ $completionRate }}%</span>
                </div>
                <p class="text-xs text-gray-400 mt-1">{{ $completedTasks }} / {{ $totalTasks }} tâches</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Contrôles actifs</p>
                @php
                    $activeControls = App\Models\Control::where('status', 'actif')->count();
                    $totalControls = App\Models\Control::count();
                @endphp
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-3">
                        <div class="bg-purple-500 rounded-full h-3 transition-all" style="width: {{ $totalControls > 0 ? round(($activeControls / $totalControls) * 100, 2) : 0 }}%"></div>
                    </div>
                    <span class="text-lg font-bold">{{ $activeControls }} / {{ $totalControls }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection