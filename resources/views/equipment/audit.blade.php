@extends('layouts.app')

@section('title', 'Historique des Modifications')
@section('header', 'Historique: ' . $equipment->nom)

@section('content')
<div class="card-cofina">
    <!-- Infos équipement -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <div class="flex items-center">
            <div class="mr-4">
                <span class="text-2xl">
                    @if($equipment->statut == 'stock') 📦
                    @elseif($equipment->statut == 'parc') 👨‍💼
                    @elseif($equipment->statut == 'maintenance') 🔧
                    @elseif($equipment->statut == 'hors_service') ❌
                    @elseif($equipment->statut == 'perdu') 🔍
                    @endif
                </span>
            </div>
            <div>
                <h4 class="font-bold text-lg">{{ $equipment->nom }}</h4>
                <p class="text-gray-600">
                    N° Série: <span class="font-bold">{{ $equipment->numero_serie }}</span> | 
                    Statut: <span class="font-bold">{{ ucfirst($equipment->statut) }}</span>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Historique -->
    <div class="space-y-4">
        @forelse($audits as $audit)
        <div class="border-l-4 pl-4 
            @if($audit->action == 'create') border-green-500 bg-green-50
            @elseif($audit->action == 'update') border-blue-500 bg-blue-50
            @elseif($audit->action == 'delete') border-red-500 bg-red-50
            @elseif($audit->action == 'transition') border-purple-500 bg-purple-50
            @else border-gray-500 bg-gray-50 @endif">
            
            <div class="flex justify-between items-start">
                <div>
                    <span class="font-bold">
                        @if($audit->action == 'create')
                            ➕ Création
                        @elseif($audit->action == 'update')
                            ✏️ Modification
                        @elseif($audit->action == 'delete')
                            🗑️ Suppression
                        @elseif($audit->action == 'transition')
                            🔄 Transition: {{ $audit->transition_type }}
                        @endif
                    </span>
                    
                    @if($audit->user)
                    <span class="text-gray-600 ml-2">par {{ $audit->user->name }}</span>
                    @endif
                </div>
                
                <div class="text-sm text-gray-500">
                    {{ $audit->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
            
            @if($audit->notes)
            <div class="mt-1 text-gray-700">
                {{ $audit->notes }}
            </div>
            @endif
            
            @if($audit->action == 'update' && !empty($audit->formatted_changes))
            <div class="mt-2">
                <button type="button" 
                        onclick="toggleDetails('{{ $audit->id }}')"
                        class="text-sm text-blue-600 hover:text-blue-900">
                    📋 Détails des modifications
                </button>
                
                <div id="details-{{ $audit->id }}" class="hidden mt-2 space-y-1">
                    @foreach($audit->formatted_changes as $change)
                    <div class="text-sm">
                        <span class="font-semibold">{{ $change['field'] }}:</span>
                        <span class="text-red-600 line-through">{{ $change['old'] }}</span> → 
                        <span class="text-green-600">{{ $change['new'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            @if($audit->ip_address)
            <div class="mt-1 text-xs text-gray-500">
                IP: {{ $audit->ip_address }}
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-8 text-gray-500">
            📭 Aucun historique disponible pour cet équipement
        </div>
        @endforelse
    </div>
    
    <!-- Bouton retour -->
    <div class="mt-8 pt-6 border-t border-cofina-gray">
        <a href="{{ route('equipment.show', $equipment) }}" class="btn-cofina-outline">
            ↩️ Retour à la fiche
        </a>
    </div>
</div>

<script>
function toggleDetails(auditId) {
    const element = document.getElementById('details-' + auditId);
    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
    } else {
        element.classList.add('hidden');
    }
}
</script>
@endsection