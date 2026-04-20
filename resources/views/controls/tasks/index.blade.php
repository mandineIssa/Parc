@extends('layouts.app')

@section('title', 'Mes Tâches de Contrôle')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Mes Tâches de Contrôle</h1>
            <p class="text-gray-500 mt-1">Gérez et réalisez vos contrôles assignés</p>
        </div>
        <a href="{{ route('controls.dashboard') }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Tableau de bord
        </a>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">En attente</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">En retard</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['overdue'] }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Complétées</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Rejetées</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['rejected'] }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    <option value="">Tous les statuts</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Complétées</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejetées</option>
                    <option value="need_complement" {{ request('status') == 'need_complement' ? 'selected' : '' }}>Compléments requis</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition">
                    Filtrer
                </button>
                <a href="{{ route('controls.tasks.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition inline-block">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Liste des tâches -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tâche</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contrôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date limite</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Conformité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($tasks as $task)
                    <tr class="hover:bg-gray-50 {{ $task->isOverdue() && $task->status == 'pending' ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4">
                            <a href="{{ route('controls.tasks.show', $task) }}" class="text-red-600 hover:text-red-800 font-medium">
                                {{ Str::limit($task->title, 40) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $task->control->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($task->control->type == 'securite') bg-red-100 text-red-800
                                @elseif($task->control->type == 'exploitation') bg-blue-100 text-blue-800
                                @elseif($task->control->type == 'conformite') bg-green-100 text-green-800
                                @else bg-purple-100 text-purple-800 @endif">
                                {{ ucfirst($task->control->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($task->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                                @elseif($task->status == 'completed') bg-green-100 text-green-800
                                @elseif($task->status == 'rejected') bg-red-100 text-red-800
                                @else bg-orange-100 text-orange-800 @endif">
                                @if($task->status == 'pending') ⏳ En attente
                                @elseif($task->status == 'in_progress') 🔄 En cours
                                @elseif($task->status == 'completed') ✅ Complétée
                                @elseif($task->status == 'rejected') ❌ Rejetée
                                @else 📝 Compléments requis @endif
                            </span>
                         </td>
                        <td class="px-6 py-4 text-sm {{ $task->isOverdue() && $task->status == 'pending' ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                            {{ $task->due_date->format('d/m/Y H:i') }}
                            @if($task->isOverdue() && $task->status == 'pending')
                                <span class="ml-1 text-xs">(Retard)</span>
                            @endif
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
                            <span class="text-gray-400 text-sm">-</span>
                            @endif
                         </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('controls.tasks.show', $task) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800">
                                Traiter
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                         </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p>Aucune tâche trouvée</p>
                            <p class="text-sm mt-1">Toutes vos tâches sont à jour</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">
            {{ $tasks->links() }}
        </div>
    </div>
</div>
@endsection