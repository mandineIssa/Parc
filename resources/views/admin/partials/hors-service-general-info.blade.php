<!-- Informations générales communes à tous les types -->
<div class="mb-8 p-6 bg-gray-50 rounded-lg border-2 border-gray-200">
    <h2 class="text-xl font-bold text-gray-800 mb-4">📝 Informations générales</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informations de l'équipement -->
        <div>
            <h3 class="font-bold text-lg mb-3 text-cofina-red">Équipement</h3>
            <div class="space-y-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Nom</label>
                    <p class="font-bold">{{ $approval->equipment->nom ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">N° Série</label>
                    <p class="font-mono">{{ $approval->equipment->numero_serie ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Modèle</label>
                    <p>{{ $approval->equipment->modele ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Type</label>
                    <p>{{ $approval->equipment->type ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Informations de la demande -->
        <div>
            <h3 class="font-bold text-lg mb-3 text-cofina-red">Demande</h3>
            <div class="space-y-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Raison</label>
                    <p class="font-bold text-red-600">{{ $data['raison'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Destinataire</label>
                    <p>{{ $data['destinataire'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Valeur résiduelle</label>
                    <p>{{ isset($data['valeur_residuelle']) ? number_format($data['valeur_residuelle'], 2) . ' FCFA' : 'Non spécifiée' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Justificatif</label>
                    <p>{{ $data['justificatif'] ?? 'Non spécifié' }}</p>
                </div>
            </div>
        </div>
        
        <!-- Description de l'incident -->
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-600 mb-2">Description de l'incident</label>
            <div class="p-3 bg-white rounded border">
                {{ $data['description_incident'] ?? 'Non spécifiée' }}
            </div>
        </div>
        
        <!-- Observations -->
        @if(isset($data['observations']))
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-600 mb-2">Observations</label>
            <div class="p-3 bg-white rounded border">
                {{ $data['observations'] }}
            </div>
        </div>
        @endif
        
        <!-- Informations du demandeur -->
        <div class="md:col-span-2">
            <h3 class="font-bold text-lg mb-3 text-cofina-red">Demandeur</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Nom</label>
                    <p>{{ $data['agent_nom'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Prénom</label>
                    <p>{{ $data['agent_prenom'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Fonction</label>
                    <p>{{ $data['agent_fonction'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Date demande</label>
                    <p>{{ \Carbon\Carbon::parse($data['submitted_at'])->format('d/m/Y H:i') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>