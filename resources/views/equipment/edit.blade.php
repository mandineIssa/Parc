@extends('layouts.app')

@section('title', 'Modifier un Équipement')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Modifier l'Équipement #{{ $equipment->numero_serie }}</h1>
        
        <div class="bg-white rounded-lg shadow-lg p-8">
            <form action="{{ route('equipment.update', $equipment->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Informations de base -->
                <div class="mb-8 pb-8 border-b">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Informations de base</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- N° Série (lecture seule) -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">N° Série</label>
                            <input type="text" value="{{ $equipment->numero_serie }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                            <p class="text-xs text-gray-500 mt-1">Le numéro de série ne peut pas être modifié</p>
                        </div>
                        
                        <!-- Type (lecture seule) -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Type</label>
                            <input type="text" value="{{ $equipment->type }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" readonly>
                        </div>
                        
                        <!-- Marque -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Marque *</label>
                            <input type="text" name="marque" value="{{ old('marque', $equipment->marque) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                        </div>
                        
                        <!-- Modèle -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Modèle *</label>
                            <input type="text" name="modele" value="{{ old('modele', $equipment->modele) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                        </div>
                        
                        <!-- Agence -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Agence</label>
                            <select name="agency_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                                <option value="">-- Sélectionner --</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}" {{ $equipment->agency_id == $agency->id ? 'selected' : '' }}>
                                        {{ $agency->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Fournisseur -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Fournisseur</label>
                            <select name="fournisseur_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                                <option value="">-- Sélectionner --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ $equipment->fournisseur_id == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Localisation -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Localisation *</label>
                            <input type="text" name="localisation" value="{{ old('localisation', $equipment->localisation) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                        </div>
                        
                        <!-- Garantie -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Garantie *</label>
                            <input type="text" name="garantie" value="{{ old('garantie', $equipment->garantie) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                        </div>
                        
                        <!-- Date Livraison -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Date Livraison *</label>
                            <input type="date" name="date_livraison" value="{{ old('date_livraison', $equipment->date_livraison ? $equipment->date_livraison->format('Y-m-d') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                        </div>
                        
                        <!-- Prix -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Prix (FCFA) *</label>
                            <input type="number" name="prix" step="0.01" value="{{ old('prix', $equipment->prix) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                        </div>
                        
                        <!-- Référence facture -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">REF Facture</label>
                            <input type="text" name="reference_facture" value="{{ old('reference_facture', $equipment->reference_facture) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                        
                        <!-- État -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">État *</label>
                            <select name="etat" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                                <option value="neuf" {{ $equipment->etat == 'neuf' ? 'selected' : '' }}>Neuf</option>
                                <option value="bon" {{ $equipment->etat == 'bon' ? 'selected' : '' }}>Bon</option>
                                <option value="moyen" {{ $equipment->etat == 'moyen' ? 'selected' : '' }}>Moyen</option>
                                <option value="mauvais" {{ $equipment->etat == 'mauvais' ? 'selected' : '' }}>Mauvais</option>
                            </select>
                        </div>
                        
                        <!-- Adresse MAC -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Adresse MAC</label>
                            <input type="text" name="adresse_mac" value="{{ old('adresse_mac', $equipment->adresse_mac) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="00:11:22:33:44:55">
                        </div>
                        
                        <!-- Date mise en service -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Date mise en service</label>
                            <input type="date" name="date_mise_service" value="{{ old('date_mise_service', $equipment->date_mise_service ? $equipment->date_mise_service->format('Y-m-d') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                        
                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">{{ old('notes', $equipment->notes) }}</textarea>
                        </div>
                    </div>
                    
                    <!-- ================= ENVOI EN MAINTENANCE ================= -->
                    <div class="mt-8 pt-6 border-t">
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="send_to_maintenance"
                                class="mr-3 h-4 w-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500"
                                {{ $equipment->statut === 'maintenance' ? 'checked disabled' : '' }}
                                onchange="redirectToMaintenance(this)"
                            >
                            <label for="send_to_maintenance" class="text-sm font-bold text-gray-700">
                                Envoyer en maintenance
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Boutons -->
                <div class="flex gap-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                        💾 Enregistrer les modifications
                    </button>
                    
                    <a href="{{ route('equipment.show', $equipment->id) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition">
                        ↩️ Annuler
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Afficher les données spécifiques en lecture seule -->
        @if($equipment->detail && !empty($specificData))
        <div class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gray-50 px-6 py-4 border-b">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Données spécifiques (lecture seule)
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Ces informations sont verrouillées et ne peuvent être modifiées que lors de la création.
                        </p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        Lecture seule
                    </span>
                </div>
            </div>
            
            <!-- Contenu -->
            <div class="p-6">
                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-6">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-gray-700">
                            Les données spécifiques sont définies lors de la création de l'équipement et ne peuvent pas être modifiées par la suite.
                        </p>
                    </div>
                </div>
                
                <!-- Grille de données -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Catégorie : Réseau -->
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                        <h4 class="font-semibold text-blue-800 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Configuration Réseau
                        </h4>
                        <div class="space-y-2">
                            @if(isset($specificData['adresse_mac']))
                            <div>
                                <span class="text-xs text-gray-500 block">Adresse MAC</span>
                                <span class="text-sm font-medium text-gray-800">{{ $specificData['adresse_mac'] }}</span>
                            </div>
                            @endif
                            
                            @if(isset($specificData['type_switch']))
                            <div>
                                <span class="text-xs text-gray-500 block">Type de Switch</span>
                                <span class="text-sm font-medium text-gray-800">{{ $specificData['type_switch'] }}</span>
                            </div>
                            @endif
                            
                            @if(isset($specificData['ports_ethernet']))
                            <div>
                                <span class="text-xs text-gray-500 block">Ports Ethernet</span>
                                <span class="text-sm font-medium text-gray-800">{{ $specificData['ports_ethernet'] }}</span>
                            </div>
                            @endif
                            
                            @if(isset($specificData['ports_poe']))
                            <div>
                                <span class="text-xs text-gray-500 block">Ports PoE</span>
                                <span class="text-sm font-medium text-gray-800">{{ $specificData['ports_poe'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Catégorie : État -->
                    <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                        <h4 class="font-semibold text-green-800 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            État & Responsable
                        </h4>
                        <div class="space-y-2">
                            @if(isset($specificData['etat_switch']))
                            <div>
                                <span class="text-xs text-gray-500 block">État Switch</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $specificData['etat_switch'] === 'fonctionnel' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $specificData['etat_switch'] }}
                                </span>
                            </div>
                            @endif
                            
                            @if(isset($specificData['responsable_switch']))
                            <div>
                                <span class="text-xs text-gray-500 block">Responsable</span>
                                <span class="text-sm font-medium text-gray-800">{{ $specificData['responsable_switch'] }}</span>
                            </div>
                            @endif
                            
                            @if(isset($specificData['etat_routeur']))
                            <div>
                                <span class="text-xs text-gray-500 block">État Routeur</span>
                                <span class="text-sm font-medium text-gray-800">{{ $specificData['etat_routeur'] }}</span>
                            </div>
                            @endif
                            
                            @if(isset($specificData['etat_wifi']))
                            <div>
                                <span class="text-xs text-gray-500 block">État WiFi</span>
                                <span class="text-sm font-medium text-gray-800">{{ $specificData['etat_wifi'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Catégorie : Caractéristiques -->
                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                        <h4 class="font-semibold text-purple-800 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Caractéristiques
                        </h4>
                        <div class="space-y-2">
                            @if(isset($specificData['vitesse_ports']))
                            <div>
                                <span class="text-xs text-gray-500 block">Vitesse Ports</span>
                                <span class="text-sm font-medium text-gray-800">{{ $specificData['vitesse_ports'] }}</span>
                            </div>
                            @endif
                            
                            @if(isset($specificData['vlan_supportes']))
                            <div>
                                <span class="text-xs text-gray-500 block">VLAN Supportés</span>
                                <span class="text-sm font-medium text-gray-800">{{ $specificData['vlan_supportes'] }}</span>
                            </div>
                            @endif
                            
                            @if(isset($specificData['indice_protection']))
                            <div>
                                <span class="text-xs text-gray-500 block">Indice Protection</span>
                                <span class="text-sm font-medium text-gray-800">{{ $specificData['indice_protection'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Autres données (affichage dynamique) -->
                    @php
                        $excludeKeys = ['adresse_mac', 'type_switch', 'ports_ethernet', 'ports_poe', 
                                        'etat_switch', 'responsable_switch', 'etat_routeur', 'etat_wifi',
                                        'vitesse_ports', 'vlan_supportes', 'indice_protection'];
                        $remainingData = array_diff_key($specificData, array_flip($excludeKeys));
                    @endphp
                    
                    @if(!empty($remainingData))
                        @foreach(array_chunk($remainingData, 5, true) as $chunk)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="font-semibold text-gray-700 mb-3">Autres informations</h4>
                            <div class="space-y-2">
                                @foreach($chunk as $key => $value)
                                <div>
                                    <span class="text-xs text-gray-500 block">{{ str_replace('_', ' ', ucfirst($key)) }}</span>
                                    <span class="text-sm font-medium text-gray-800">
                                        @if(in_array($value, ['oui', 'non']))
                                            <span class="inline-flex items-center">
                                                @if($value === 'oui')
                                                    <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    Oui
                                                @else
                                                    <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                    Non
                                                @endif
                                            </span>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
                
                <!-- Bouton d'action -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Pour modifier ces données, créez un nouvel équipement
                        </div>
<!--                         <a href="{{ route('equipment.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Créer un nouvel équipement
                        </a> -->
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function toggleContractFields() {
    const checkbox = document.getElementById('contrat_maintenance');
    const fields = document.getElementById('contract-fields');
    fields.classList.toggle('hidden', !checkbox.checked);
}
</script>
<script>
function redirectToMaintenance(checkbox) {
    if (checkbox.checked) {
        window.location.href = "{{ route('equipment.maintenance.create', $equipment) }}";
    }
}
</script>

@endsection