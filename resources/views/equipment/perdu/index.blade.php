@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Équipements Perdus/Sous Doublure</h1>
        <a href="{{ route('perdu.create') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            Nouvelle Déclaration
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Perdus</div>
            <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-4">
            <div class="text-sm text-blue-500">En recherche</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['en_recherche'] }}</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4">
            <div class="text-sm text-green-500">Retrouvés</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['trouves'] }}</div>
        </div>
        <div class="bg-gray-100 rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Définitifs</div>
            <div class="text-2xl font-bold text-gray-700">{{ $stats['definitif'] }}</div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Série</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Disparition</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lieu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plainte</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut Recherche</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($perdus as $perdu)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $perdu->numero_serie }}</div>
                        @if($perdu->equipment)
                            <div class="text-sm text-gray-500">{{ $perdu->equipment->type }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $perdu->date_disparition->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ Str::limit($perdu->lieu_disparition, 30) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $types = [
                                'vol' => 'Vol',
                                'perte' => 'Perte',
                                'oubli' => 'Oubli',
                                'destruction' => 'Destruction'
                            ];
                        @endphp
                        {{ $types[$perdu->type_disparition] ?? $perdu->type_disparition }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($perdu->plainte_deposee)
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Oui</span>
                            @if($perdu->numero_plainte)
                                <div class="text-xs text-gray-500">N°: {{ $perdu->numero_plainte }}</div>
                            @endif
                        @else
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">Non</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $badgeClasses = [
                                'en_cours' => 'bg-yellow-100 text-yellow-800',
                                'trouve' => 'bg-green-100 text-green-800',
                                'definitif' => 'bg-red-100 text-red-800'
                            ];
                            $statutLabels = [
                                'en_cours' => 'En recherche',
                                'trouve' => 'Retrouvé',
                                'definitif' => 'Définitif'
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $badgeClasses[$perdu->statut_recherche] ?? 'bg-gray-100' }}">
                            {{ $statutLabels[$perdu->statut_recherche] ?? $perdu->statut_recherche }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('perdu.show', $perdu->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                        <a href="{{ route('perdu.edit', $perdu->id) }}" class="text-green-600 hover:text-green-900 mr-3">Modifier</a>
                        @if($perdu->statut_recherche == 'en_cours')
                            <button type="button" onclick="openRetrouverModal({{ $perdu->id }})" 
                                class="text-purple-600 hover:text-purple-900 mr-3">
                                Retrouvé
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        Aucun équipement perdu déclaré
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4">
            {{ $perdus->links() }}
        </div>
    </div>
</div>

<!-- Modal pour marquer comme retrouvé -->
<div id="retrouverModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Marquer comme retrouvé</h3>
        <form id="retrouverForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date_retrouvaille">
                    Date de retrouvaille *
                </label>
                <input type="date" name="date_retrouvaille" id="date_retrouvaille" required
                    value="{{ date('Y-m-d') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="lieu_retrouvaille">
                    Lieu de retrouvaille *
                </label>
                <input type="text" name="lieu_retrouvaille" id="lieu_retrouvaille" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="etat_retrouvaille">
                    État de l'équipement *
                </label>
                <textarea name="etat_retrouvaille" id="etat_retrouvaille" rows="3" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Décrivez l'état dans lequel l'équipement a été retrouvé..."></textarea>
            </div>
            <div class="flex justify-end">
                <button type="button" onclick="closeRetrouverModal()"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Annuler
                </button>
                <button type="submit"
                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    Confirmer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRetrouverModal(perduId) {
    const form = document.getElementById('retrouverForm');
    form.action = `/perdu/${perduId}/retrouver`;
    document.getElementById('retrouverModal').classList.remove('hidden');
}

function closeRetrouverModal() {
    document.getElementById('retrouverModal').classList.add('hidden');
}
</script>
@endsection