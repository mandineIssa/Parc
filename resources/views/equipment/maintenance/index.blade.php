@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestion des Maintenances</h1>
        <div class="space-x-2">
            <a href="{{ route('maintenance.retard') }}" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                Retards ({{ $stats['retard'] }})
            </a>
            <a href="{{ route('maintenance.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nouvelle Maintenance
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total</div>
            <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow p-4">
            <div class="text-sm text-yellow-500">En cours</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['en_cours'] }}</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4">
            <div class="text-sm text-green-500">Terminées</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['terminee'] }}</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-4">
            <div class="text-sm text-blue-500">Coût total</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['cout_total'], 2, ',', ' ') }} €</div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Série</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prestataire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Départ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Retour prévu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Coût</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($maintenances as $maint)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $maint->numero_serie }}</div>
                        @if($maint->equipment)
                            <div class="text-sm text-gray-500">{{ $maint->equipment->type }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $types = [
                                'preventive' => 'Préventive',
                                'corrective' => 'Corrective',
                                'contractuelle' => 'Contractuelle',
                                'autre' => 'Autre'
                            ];
                        @endphp
                        {{ $types[$maint->type_maintenance] ?? $maint->type_maintenance }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ Str::limit($maint->prestataire, 20) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $maint->date_depart->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($maint->date_retour_prevue < now() && $maint->statut == 'en_cours')
                            <span class="text-red-600 font-semibold">{{ $maint->date_retour_prevue->format('d/m/Y') }}</span>
                        @else
                            {{ $maint->date_retour_prevue->format('d/m/Y') }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($maint->cout)
                            {{ number_format($maint->cout, 2, ',', ' ') }} €
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $badgeClasses = [
                                'en_cours' => 'bg-yellow-100 text-yellow-800',
                                'terminee' => 'bg-green-100 text-green-800',
                                'annulee' => 'bg-gray-100 text-gray-800'
                            ];
                            $statutLabels = [
                                'en_cours' => 'En cours',
                                'terminee' => 'Terminée',
                                'annulee' => 'Annulée'
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $badgeClasses[$maint->statut] ?? 'bg-gray-100' }}">
                            {{ $statutLabels[$maint->statut] ?? $maint->statut }}
                        </span>
                        @if($maint->date_retour_reelle)
                            <div class="text-xs text-gray-500">Retour: {{ $maint->date_retour_reelle->format('d/m/Y') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('maintenance.show', $maint->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                        @if($maint->statut == 'en_cours')
                            <button type="button" onclick="openTerminerModal({{ $maint->id }})" 
                                class="text-green-600 hover:text-green-900 mr-3">
                                Terminer
                            </button>
                            <button type="button" onclick="openAnnulerModal({{ $maint->id }})" 
                                class="text-red-600 hover:text-red-900 mr-3">
                                Annuler
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        Aucune maintenance enregistrée
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

<!-- Modal pour terminer -->
<div id="terminerModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Terminer la maintenance</h3>
        <form id="terminerForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_retour_reelle">
                    Date de retour réelle *
                </label>
                <input type="date" name="date_retour_reelle" id="date_retour_reelle" required
                    value="{{ date('Y-m-d') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="travaux_realises">
                    Travaux réalisés *
                </label>
                <textarea name="travaux_realises" id="travaux_realises" rows="3" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Décrivez les travaux réalisés..."></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="cout">
                    Coût (€) *
                </label>
                <input type="number" step="0.01" name="cout" id="cout" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="observations_fin">
                    Observations
                </label>
                <textarea name="observations_fin" id="observations_fin" rows="2"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeTerminerModal()"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Annuler
                </button>
                <button type="submit"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Confirmer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour annuler -->
<div id="annulerModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Annuler la maintenance</h3>
        <form id="annulerForm" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="raison_annulation">
                    Raison de l'annulation *
                </label>
                <textarea name="raison_annulation" id="raison_annulation" rows="3" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Pourquoi annulez-vous cette maintenance ?"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeAnnulerModal()"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Annuler
                </button>
                <button type="submit"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Confirmer l'annulation
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openTerminerModal(maintenanceId) {
    const form = document.getElementById('terminerForm');
    form.action = `/maintenance/${maintenanceId}/terminer`;
    document.getElementById('terminerModal').classList.remove('hidden');
}

function closeTerminerModal() {
    document.getElementById('terminerModal').classList.add('hidden');
}

function openAnnulerModal(maintenanceId) {
    const form = document.getElementById('annulerForm');
    form.action = `/maintenance/${maintenanceId}/annuler`;
    document.getElementById('annulerModal').classList.remove('hidden');
}

function closeAnnulerModal() {
    document.getElementById('annulerModal').classList.add('hidden');
}
</script>
@endsection