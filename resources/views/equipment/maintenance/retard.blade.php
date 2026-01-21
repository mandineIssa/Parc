@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Maintenances en Retard de Retour</h1>
        <a href="{{ route('maintenance.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Retour à la liste
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-red-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase">N° Série</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase">Prestataire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase">Départ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase">Retour prévu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase">Jours de retard</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-red-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($maintenances as $maint)
                @php
                    $joursRetard = now()->diffInDays($maint->date_retour_prevue);
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $maint->numero_serie }}</div>
                        @if($maint->equipment)
                            <div class="text-sm text-gray-500">{{ $maint->equipment->type }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $maint->prestataire }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $maint->date_depart->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-red-600 font-semibold">{{ $maint->date_retour_prevue->format('d/m/Y') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                            {{ $joursRetard }} jour{{ $joursRetard > 1 ? 's' : '' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('maintenance.show', $maint->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                        <button type="button" onclick="openTerminerModal({{ $maint->id }})" 
                            class="text-green-600 hover:text-green-900 mr-3">
                            Terminer
                        </button>
                        <button type="button" onclick="openAnnulerModal({{ $maint->id }})" 
                            class="text-red-600 hover:text-red-900">
                            Annuler
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Aucune maintenance en retard
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4">
            {{ $maintenances->links() }}
        </div>
    </div>
</div>

<!-- Modals (identique à la page index) -->
<!-- ... -->
@endsection