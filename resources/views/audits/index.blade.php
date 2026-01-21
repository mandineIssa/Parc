@extends('layouts.app')

@section('title', 'Journal d\'Audit')
@section('header', 'Journal des Activités')

@section('content')
<div class="card-cofina">
    <!-- Filtres -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <form action="{{ route('audits.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-bold text-cofina-red mb-2">Action</label>
                <select name="action" class="w-full px-3 py-2 border border-gray-300 rounded">
                    <option value="">Toutes les actions</option>
                    <option value="create" @selected(request('action') == 'create')>Création</option>
                    <option value="update" @selected(request('action') == 'update')>Modification</option>
                    <option value="delete" @selected(request('action') == 'delete')>Suppression</option>
                    <option value="transition" @selected(request('action') == 'transition')>Transition</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-cofina-red mb-2">Modèle</label>
                <select name="model_type" class="w-full px-3 py-2 border border-gray-300 rounded">
                    <option value="">Tous les modèles</option>
                    <option value="Equipment" @selected(request('model_type') == 'Equipment')>Équipement</option>
                    <option value="Parc" @selected(request('model_type') == 'Parc')>Parc</option>
                    <option value="Maintenance" @selected(request('model_type') == 'Maintenance')>Maintenance</option>
                    <option value="Stock" @selected(request('model_type') == 'Stock')>Stock</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-cofina-red mb-2">Période</label>
                <select name="period" class="w-full px-3 py-2 border border-gray-300 rounded">
                    <option value="7" @selected(request('period') == '7')>7 derniers jours</option>
                    <option value="30" @selected(request('period') == '30' || !request('period'))>30 derniers jours</option>
                    <option value="90" @selected(request('period') == '90')>3 derniers mois</option>
                    <option value="365" @selected(request('period') == '365')>1 an</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="btn-cofina-outline w-full">
                    🔍 Filtrer
                </button>
                @if(request()->anyFilled(['action', 'model_type', 'period']))
                <a href="{{ route('audits.index') }}" class="ml-2 btn-cofina-outline">
                    ↩️
                </a>
                @endif
            </div>
        </form>
    </div>
    
    <!-- Tableau des audits -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-cofina-red uppercase tracking-wider">
                        Date & Heure
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-cofina-red uppercase tracking-wider">
                        Action
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-cofina-red uppercase tracking-wider">
                        Modèle
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-cofina-red uppercase tracking-wider">
                        Utilisateur
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-cofina-red uppercase tracking-wider">
                        Détails
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-cofina-red uppercase tracking-wider">
                        IP
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($audits as $audit)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $audit->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $audit->created_at->format('H:i:s') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $actionColors = [
                                'create' => 'bg-green-100 text-green-800',
                                'update' => 'bg-blue-100 text-blue-800',
                                'delete' => 'bg-red-100 text-red-800',
                                'transition' => 'bg-purple-100 text-purple-800'
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $actionColors[$audit->action] ?? 'bg-gray-100' }}">
                            @if($audit->action == 'transition')
                                🔄 {{ $audit->transition_type }}
                            @elseif($audit->action == 'create')
                                ➕ Création
                            @elseif($audit->action == 'update')
                                ✏️ Modification
                            @elseif($audit->action == 'delete')
                                🗑️ Suppression
                            @else
                                {{ $audit->action }}
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">
                            @switch($audit->model_type)
                                @case('App\Models\Equipment')
                                    📦 Équipement
                                    @break
                                @case('App\Models\Parc')
                                    👨‍💼 Parc
                                    @break
                                @case('App\Models\Maintenance')
                                    🔧 Maintenance
                                    @break
                                @case('App\Models\Stock')
                                    📊 Stock
                                    @break
                                @default
                                    {{ class_basename($audit->model_type) }}
                            @endswitch
                        </div>
                        <div class="text-xs text-gray-500">ID: {{ $audit->model_id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $audit->user->name ?? 'Système' }}</div>
                        <div class="text-xs text-gray-500">{{ $audit->user->email ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $audit->notes }}</div>
                        @if($audit->action == 'update' && !empty($audit->formatted_changes))
                        <div class="mt-1">
                            <button type="button" 
                                    onclick="toggleChanges({{ $audit->id }})"
                                    class="text-xs text-blue-600 hover:text-blue-900">
                                📋 Voir les changements
                            </button>
                            <div id="changes-{{ $audit->id }}" class="hidden mt-2 p-2 bg-gray-50 rounded text-xs">
                                @foreach($audit->formatted_changes as $change)
                                <div class="mb-1">
                                    <span class="font-semibold">{{ $change['field'] }}:</span>
                                    <span class="text-red-600">{{ $change['old'] }}</span> → 
                                    <span class="text-green-600">{{ $change['new'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $audit->ip_address }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        📭 Aucune activité enregistrée
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($audits->hasPages())
    <div class="mt-6">
        {{ $audits->links() }}
    </div>
    @endif
</div>

<!-- Statistiques -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
    <div class="bg-white p-4 rounded-lg shadow border">
        <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
        <div class="text-sm text-gray-600">Activités totales</div>
    </div>
    <div class="bg-white p-4 rounded-lg shadow border">
        <div class="text-2xl font-bold text-green-600">{{ $stats['by_action']['create'] ?? 0 }}</div>
        <div class="text-sm text-gray-600">Créations</div>
    </div>
    <div class="bg-white p-4 rounded-lg shadow border">
        <div class="text-2xl font-bold text-blue-600">{{ $stats['by_action']['update'] ?? 0 }}</div>
        <div class="text-sm text-gray-600">Modifications</div>
    </div>
    <div class="bg-white p-4 rounded-lg shadow border">
        <div class="text-2xl font-bold text-purple-600">{{ $stats['by_action']['transition'] ?? 0 }}</div>
        <div class="text-sm text-gray-600">Transitions</div>
    </div>
</div>

<script>
function toggleChanges(auditId) {
    const element = document.getElementById('changes-' + auditId);
    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
    } else {
        element.classList.add('hidden');
    }
}
</script>
@endsection