@extends('layouts.app')

@section('title', $control->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-2xl font-bold text-gray-800">{{ $control->name }}</h1>
                <span class="px-2 py-1 text-xs rounded-full {{ $control->status == 'actif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $control->status == 'actif' ? 'Actif' : 'Inactif' }}
                </span>
            </div>
            <div class="flex items-center gap-4 text-sm text-gray-500">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Type: {{ ucfirst($control->type) }}
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Fréquence: {{ ucfirst($control->frequency) }}
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Responsable: {{ $control->responsible_role }}
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('controls.edit', $control) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
            @if($control->status == 'actif')
            <form action="{{ route('controls.generate-tasks', $control) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Générer tâche
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm">Taux de conformité</p>
            <div class="flex items-center gap-2 mt-1">
                <div class="flex-1 bg-gray-200 rounded-full h-3">
                    <div class="bg-green-500 rounded-full h-3 transition-all" style="width: {{ $control->conformity_rate }}%"></div>
                </div>
                <span class="text-xl font-bold">{{ $control->conformity_rate }}%</span>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm">Tâches totales</p>
            <p class="text-2xl font-bold">{{ $control->tasks->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm">Tâches en attente</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $control->pending_tasks_count }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-sm">Tâches complétées</p>
            <p class="text-2xl font-bold text-green-600">{{ $control->completed_tasks_count }}</p>
        </div>
    </div>

    <!-- Graphique de conformité -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4">Répartition de la conformité</h3>
        <div class="flex items-center gap-6">
            <div class="flex-1">
                <div class="h-4 bg-gray-200 rounded-full overflow-hidden flex">
                    <div class="bg-green-500 h-full" style="width: {{ $conformityData['conforme'] }}%"></div>
                    <div class="bg-red-500 h-full" style="width: {{ $conformityData['non_conforme'] }}%"></div>
                    <div class="bg-gray-400 h-full" style="width: {{ $conformityData['en_attente'] }}%"></div>
                </div>
                <div class="flex justify-between mt-3 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-green-500 rounded"></div>
                        <span>Conforme ({{ $conformityData['conforme'] }}%)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-red-500 rounded"></div>
                        <span>Non conforme ({{ $conformityData['non_conforme'] }}%)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-gray-400 rounded"></div>
                        <span>En attente ({{ $conformityData['en_attente'] }}%)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Description -->
    @if($control->description)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-2">Description</h3>
        <p class="text-gray-600">{{ $control->description }}</p>
    </div>
    @endif

    <!-- Liste des tâches -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-semibold text-gray-800">Historique des tâches</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tâche</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigné à</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Conformité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date limite</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($control->tasks()->latest()->get() as $task)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('controls.tasks.show', $task) }}" class="text-red-600 hover:text-red-800">
                                {{ $task->title }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $task->assignedTo?->name ?? 'Non assigné' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($task->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                                @elseif($task->status == 'completed') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($task->conformity)
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($task->conformity == 'conforme') bg-green-100 text-green-800
                                @elseif($task->conformity == 'non_conforme') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($task->conformity) }}
                            </span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm {{ $task->isOverdue() ? 'text-red-600 font-bold' : '' }}">
                            {{ $task->due_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('controls.tasks.show', $task) }}" class="text-blue-600 hover:text-blue-800">
                                Voir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            Aucune tâche générée pour le moment
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection