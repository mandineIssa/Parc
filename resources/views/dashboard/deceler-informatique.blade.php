{{-- resources/views/dashboard/deceler-informatique.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
    .search-highlight {
        background-color: #FFEB3B !important;
        padding: 0.1em 0.2em !important;
        border-radius: 0.2em !important;
        font-weight: 600 !important;
        color: #000 !important;
    }
    
    .stock-row {
        transition: all 0.3s ease !important;
    }
    
    .stock-row[style*="display: none"] {
        opacity: 0 !important;
        transform: translateX(-10px) !important;
        height: 0 !important;
        overflow: hidden !important;
    }
    
    .filter-badge {
        transition: all 0.2s ease;
    }
    
    .filter-badge.active {
        box-shadow: 0 0 0 2px #3b82f6;
    }
</style>

<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Stock DECELER - Informatique</h1>
            <p class="text-gray-600 mt-2">Gestion des équipements informatiques retournés (DECELER)</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <a href="{{ route('dashboard.deceler-informatique.export') }}"
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter CSV
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Total équipements DECELER</p>
                    <p class="text-3xl font-bold mt-2">{{ $stats['total'] }}</p>
                    <p class="text-sm opacity-80 mt-1">équipements informatiques</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium opacity-90">Valeur résiduelle totale</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['valeur_totale'] ?? 0, 0, ',', ' ') }} FCFA</p>
                    <p class="text-sm opacity-80 mt-1">estimation totale</p>
                </div>
                <div class="bg-white/20 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           id="searchInput"
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Rechercher par N° série, marque, modèle, utilisateur..."
                           value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="w-full md:w-48">
                <select id="etatFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="">Tous les états</option>
                    <option value="bon" {{ request('etat') == 'bon' ? 'selected' : '' }}>Bon état</option>
                    <option value="reparable" {{ request('etat') == 'reparable' ? 'selected' : '' }}>Réparable</option>
                    <option value="irreparable" {{ request('etat') == 'irreparable' ? 'selected' : '' }}>Irréparable</option>
                </select>
            </div>
            
            <button id="resetFilters" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-lg transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Réinitialiser
            </button>
        </div>
        
        <!-- Filtres rapides -->
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap gap-2">
                <span class="text-sm text-gray-500 flex items-center mr-3">Filtres rapides :</span>
                <button class="filter-badge px-3 py-1.5 text-sm font-medium rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200 transition" data-filter="all">
                    Tous
                </button>
                <button class="filter-badge px-3 py-1.5 text-sm font-medium rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition" data-filter="bon">
                    ✅ Bon état
                </button>
                <button class="filter-badge px-3 py-1.5 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition" data-filter="reparable">
                    ⚠️ Réparable
                </button>
                <button class="filter-badge px-3 py-1.5 text-sm font-medium rounded-full bg-red-100 text-red-800 hover:bg-red-200 transition" data-filter="irreparable">
                    ❌ Irréparable
                </button>
                <button class="filter-badge px-3 py-1.5 text-sm font-medium rounded-full bg-purple-100 text-purple-800 hover:bg-purple-200 transition" data-filter="parc">
                    📋 Parc
                </button>
                <button class="filter-badge px-3 py-1.5 text-sm font-medium rounded-full bg-green-100 text-green-800 hover:bg-green-200 transition" data-filter="recent">
                    Retour récent
                </button>
            </div>
        </div>
    </div>

    <!-- Informations de recherche -->
    <div id="searchResultsInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4 hidden">
        <div class="flex justify-between items-center">
            <div>
                <span id="resultsCount" class="text-blue-800 font-medium">0 résultats</span>
                <span id="searchTerm" class="text-blue-600 text-sm ml-4"></span>
            </div>
            <button id="clearAllFilters" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Tout effacer
            </button>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Liste des Équipements DECELER</h2>
                    <p class="text-sm text-gray-600 mt-1" id="totalCount">
                        {{ $stocks->total() }} équipement{{ $stocks->total() > 1 ? 's' : '' }} DECELER au total
                    </p>
                </div>
                <div class="text-sm text-gray-500">
                    <span id="filteredCount"></span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° de Série</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marque</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">État retour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom équipement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modèle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date retour</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Origine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur résiduelle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="stocksTableBody">
                    @forelse($stocks as $stock)
                    @php
                        // Récupérer les informations du parc si l'origine est "parc"
                        $parcInfo = null;
                        if ($stock->deceler && $stock->deceler->origine === 'parc') {
                            $parcInfo = \App\Models\Parc::where('numero_serie', $stock->numero_serie)->first();
                        }
                        
                        // Initialiser les variables utilisateur
                        $utilisateurNom = '-';
                        $utilisateurPrenom = '-';
                        $utilisateurNomComplet = '-';
                        $position = '-';
                        $departement = '-';
                        $email = '-';
                        $telephone = '-';
                        $dateAffectation = '-';
                        $statutUsage = '-';
                        $notesAffectation = '-';
                        $affectationReason = '-';
                        $affectationReasonDetail = '-';
                        $numeroBonAffectation = '-';
                        
                        if ($parcInfo) {
                            // Récupérer le nom et prénom depuis les colonnes directes
                            $utilisateurNom = $parcInfo->utilisateur_nom ?? '-';
                            $utilisateurPrenom = $parcInfo->utilisateur_prenom ?? '-';
                            
                            // Construire le nom complet
                            if ($utilisateurPrenom !== '-' || $utilisateurNom !== '-') {
                                $utilisateurNomComplet = trim(($utilisateurPrenom !== '-' ? $utilisateurPrenom : '') . ' ' . ($utilisateurNom !== '-' ? $utilisateurNom : ''));
                                if (empty($utilisateurNomComplet)) $utilisateurNomComplet = '-';
                            }
                            
                            // Position : essayer 'position' d'abord, puis 'poste_affecte'
                            $position = !empty($parcInfo->position) ? $parcInfo->position : 
                                       (!empty($parcInfo->poste_affecte) ? $parcInfo->poste_affecte : '-');
                            
                            // Département
                            $departement = $parcInfo->departement ?? '-';
                            
                            // Email et téléphone
                            $email = $parcInfo->email ?? '-';
                            $telephone = $parcInfo->telephone ?? '-';
                            
                            // Date d'affectation
                            $dateAffectation = $parcInfo->date_affectation ? $parcInfo->date_affectation->format('d/m/Y') : '-';
                            
                            // Autres informations
                            $statutUsage = $parcInfo->statut_usage ?? '-';
                            $notesAffectation = $parcInfo->notes_affectation ?? '-';
                            $affectationReason = $parcInfo->affectation_reason ?? '-';
                            $affectationReasonDetail = $parcInfo->affectation_reason_detail ?? '-';
                            $numeroBonAffectation = $parcInfo->numero_bon_affectation ?? '-';
                        }
                        
                        // Déterminer la date de retour pour les filtres
                        $dateRetour = $stock->deceler?->date_retour;
                        $dateRetourFormatted = $dateRetour ? $dateRetour->format('Y-m-d') : '';
                        
                        // Vérifier si le retour est récent (moins de 30 jours)
                        $estRecent = false;
                        if ($dateRetour) {
                            $trenteJours = now()->subDays(30);
                            $estRecent = $dateRetour >= $trenteJours;
                        }
                        
                        // Valeur résiduelle formatée
                        $valeurResiduelle = $stock->deceler?->valeur_residuelle;
                        $valeurResiduelleFormatted = $valeurResiduelle 
                                                    ? number_format($valeurResiduelle, 0, ',', ' ').' FCFA'
                                                    : '-';
                        
                        // Préparer les données pour la modal
                        $donneesModal = [
                            "numero_serie"      => $stock->numero_serie,
                            "nom"               => $stock->equipment?->nom ?? $stock->equipment?->type ?? "N/A",
                            "marque"            => $stock->equipment?->marque ?? "-",
                            "modele"            => $stock->equipment?->modele ?? "-",
                            "date_retour"       => $dateRetour?->format("d/m/Y") ?? "-",
                            "raison_retour"     => $stock->deceler?->raison_retour ?? "-",
                            "origine"           => ucfirst($stock->deceler?->origine ?? "-"),
                            "etat_retour"       => $stock->deceler?->etat_retour ?? "-",
                            "diagnostic"        => $stock->deceler?->diagnostic ?? "-",
                            "localisation"      => $stock->localisation_physique ?? "-",
                            "valeur_residuelle" => $valeurResiduelleFormatted,
                            "observations"      => $stock->deceler?->observations_retour ?? "-",
                            // Informations utilisateur du parc
                            "utilisateur_nom"       => $utilisateurNom,
                            "utilisateur_prenom"    => $utilisateurPrenom,
                            "utilisateur_nom_complet" => $utilisateurNomComplet,
                            "position"              => $position,
                            "departement"           => $departement,
                            "email"                 => $email,
                            "telephone"             => $telephone,
                            "date_affectation"      => $dateAffectation,
                            "statut_usage"          => $statutUsage,
                            "notes_affectation"     => $notesAffectation,
                            "affectation_reason"    => $affectationReason,
                            "affectation_reason_detail" => $affectationReasonDetail,
                            "numero_bon_affectation" => $numeroBonAffectation,
                        ];
                    @endphp
                    <tr class="stock-row hover:bg-gray-50 transition-colors"
                        data-id="{{ $stock->id }}"
                        data-numero="{{ strtolower($stock->numero_serie) }}"
                        data-marque="{{ strtolower($stock->equipment?->marque ?? '') }}"
                        data-modele="{{ strtolower($stock->equipment?->modele ?? '') }}"
                        data-etat="{{ strtolower($stock->deceler?->etat_retour ?? '') }}"
                        data-categorie="{{ strtolower($stock->equipment?->type ?? '') }}"
                        data-origine="{{ strtolower($stock->deceler?->origine ?? '') }}"
                        data-localisation="{{ strtolower($stock->equipment?->localisation ?? '') }}"
                        data-date="{{ $dateRetourFormatted }}"
                        data-recent="{{ $estRecent ? '1' : '0' }}"
                        data-utilisateur="{{ strtolower($utilisateurNomComplet) }}"
                        data-prix="{{ $valeurResiduelle ?? 0 }}">

                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-gray-900 stock-numero">{{ $stock->numero_serie }}</span>
                            <div class="text-sm text-gray-500">Qté: {{ $stock->quantite }}</div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-gray-900 stock-marque">{{ $stock->equipment?->marque ?? '-' }}</span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $etat = $stock->deceler?->etat_retour ?? '';
                                $etatConfig = [
                                    'bon'         => ['class' => 'bg-green-100 text-green-800 border-green-200',   'label' => '✅ Bon état'],
                                    'reparable'   => ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-200','label' => '⚠️ Réparable'],
                                    'irreparable' => ['class' => 'bg-red-100 text-red-800 border-red-200',          'label' => '❌ Irréparable'],
                                ];
                                $cfg = $etatConfig[$etat] ?? ['class' => 'bg-gray-100 text-gray-600 border-gray-200', 'label' => ucfirst($etat) ?: '-'];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $cfg['class'] }} stock-etat">
                                {{ $cfg['label'] }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($stock->equipment)
                                <span class="font-medium text-gray-900 stock-nom">{{ $stock->equipment->nom ?? $stock->equipment->type ?? '-' }}</span>
                            @else
                                <span class="text-red-500 text-sm">Non trouvé</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-gray-900 stock-modele">{{ $stock->equipment?->modele ?? '-' }}</span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 stock-date">
                            {{ $dateRetour?->format('d/m/Y') ?? '-' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 stock-origine">
                            @if($stock->deceler && $stock->deceler->origine === 'parc' && $parcInfo)
                                <span class="font-medium text-blue-600">📋 Parc</span>
                                @if($utilisateurNomComplet !== '-')
                                <div class="text-xs text-gray-500 mt-1 stock-utilisateur">
                                    {{ $utilisateurNomComplet }}
                                    @if($position !== '-')
                                        <br>{{ $position }}
                                    @endif
                                </div>
                                @endif
                            @else
                                {{ ucfirst($stock->deceler?->origine ?? '-') }}
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-gray-900 stock-prix">{{ $valeurResiduelleFormatted }}</span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex justify-center space-x-2">
                                @if($stock->equipment)
                                <a href="{{ route('equipment.show', $stock->equipment->id) }}"
                                   class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 p-2 rounded-lg transition"
                                   title="Détail équipement">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                    </svg>
                                </a>
                                @endif

                                @if($stock->deceler)
                                <button type="button"
                                    class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition"
                                    title="Voir retour"
                                    onclick='openRetourModal(@json($donneesModal))'>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </button>
                                @endif

                                @if($parcInfo)
                                <a href="{{ route('parc.show', $parcInfo->id) }}" 
                                   class="text-purple-600 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 p-2 rounded-lg transition"
                                   title="Voir affectation parc">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </a>
                                @endif

                                @if($stock->equipment)
                                <a href="{{ route('equipment.transitions.', $stock->equipment->id) }}"
                                   class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition"
                                   title="Historique des mouvements">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="noResultsRow" style="display: none;">
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun équipement DECELER trouvé</h3>
                                <p class="text-gray-500">Aucun équipement ne correspond à vos critères de recherche</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <p class="text-sm text-gray-700">
                        Affichage de 
                        <span class="font-medium">{{ $stocks->firstItem() }}</span>
                        à 
                        <span class="font-medium">{{ $stocks->lastItem() }}</span>
                        sur 
                        <span class="font-medium">{{ $stocks->total() }}</span>
                        équipements
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    {{ $stocks->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Informations -->
    <div class="mt-8 p-6 bg-blue-50 rounded-xl border border-blue-200">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Gestion du stock DECELER</h3>
                <p class="text-blue-700 mb-3">Cette section permet de gérer les équipements informatiques retournés (DECELER). Vous pouvez suivre les états de retour, les diagnostics et les valeurs résiduelles.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">États de retour</h4>
                        <p class="text-sm text-blue-700">✅ Bon • ⚠️ Réparable • ❌ Irréparable</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Valeur résiduelle</h4>
                        <p class="text-sm text-blue-700">Estimation de la valeur après retour</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h4 class="font-medium text-blue-900 mb-2">Origines</h4>
                        <p class="text-sm text-blue-700">📋 Parc • 🔧 Maintenance</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Fiche de Retour --}}
<div id="retourModal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     style="background:rgba(0,0,0,0.55);">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-screen overflow-y-auto">

        <!-- En-tête -->
        <div class="bg-orange-500 text-white p-6 rounded-t-2xl flex items-center justify-between sticky top-0 z-10">
            <div>
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Fiche de Retour DECELER
                </h2>
                <p class="text-orange-100 text-sm mt-1" id="modal-header-serie"></p>
            </div>
            <button onclick="closeRetourModal()"
                class="text-white hover:text-orange-200 p-2 rounded-full hover:bg-white/20 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Corps -->
        <div class="p-6 space-y-5">
            <!-- Équipement -->
            <div class="bg-orange-50 rounded-xl p-4 border border-orange-200">
                <h3 class="font-bold text-orange-800 mb-3 text-sm uppercase tracking-wide">🖥 Équipement</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 text-xs uppercase">Désignation</span>
                        <p class="font-semibold text-gray-800 mt-0.5" id="modal-nom">—</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs uppercase">N° Série</span>
                        <p class="font-semibold text-gray-800 mt-0.5 font-mono" id="modal-serie">—</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs uppercase">Marque</span>
                        <p class="font-semibold text-gray-800 mt-0.5" id="modal-marque">—</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs uppercase">Modèle</span>
                        <p class="font-semibold text-gray-800 mt-0.5" id="modal-modele">—</p>
                    </div>
                </div>
            </div>

            <!-- Retour -->
            <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                <h3 class="font-bold text-blue-800 mb-3 text-sm uppercase tracking-wide">↩ Informations de retour</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 text-xs uppercase">Date de retour</span>
                        <p class="font-semibold text-gray-800 mt-0.5" id="modal-date-retour">—</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs uppercase">Origine</span>
                        <p class="font-semibold text-gray-800 mt-0.5" id="modal-origine">—</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-500 text-xs uppercase">Raison du retour</span>
                        <p class="font-semibold text-gray-800 mt-0.5" id="modal-raison">—</p>
                    </div>
                </div>
            </div>

            <!-- Informations Utilisateur -->
            <div id="userInfoSection" class="bg-green-50 rounded-xl p-4 border border-green-200 hidden">
                <h3 class="font-bold text-green-800 mb-3 text-sm uppercase tracking-wide">👤 Informations Utilisateur (Parc)</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 text-xs uppercase">Nom complet</span>
                        <p class="font-semibold text-gray-800 mt-0.5" id="modal-user-nomcomplet">—</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs uppercase">Position / Poste</span>
                        <p class="font-semibold text-gray-800 mt-0.5" id="modal-user-position">—</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs uppercase">Département</span>
                        <p class="font-semibold text-gray-800 mt-0.5" id="modal-user-departement">—</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs uppercase">Email</span>
                        <p class="font-semibold text-gray-800 mt-0.5" id="modal-user-email">—</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs uppercase">Téléphone</span>
                        <p class="font-semibold text-gray-800 mt-0.5" id="modal-user-telephone">—</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs uppercase">Date affectation</span>
                        <p class="font-semibold text-gray-800 mt-0.5" id="modal-user-dateaffectation">—</p>
                    </div>
                </div>
                
                <div class="mt-3 pt-3 border-t border-green-200">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 text-xs uppercase">Statut d'usage</span>
                            <p class="font-semibold text-gray-800 mt-0.5" id="modal-user-statut">—</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-xs uppercase">Raison d'affectation</span>
                            <p class="font-semibold text-gray-800 mt-0.5" id="modal-user-raison">—</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500 text-xs uppercase">Détail raison</span>
                            <p class="font-semibold text-gray-800 mt-0.5" id="modal-user-raison-detail">—</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500 text-xs uppercase">N° Bon d'affectation</span>
                            <p class="font-semibold text-gray-800 mt-0.5" id="modal-user-bon">—</p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500 text-xs uppercase">Notes</span>
                            <p class="font-semibold text-gray-800 mt-0.5" id="modal-user-notes">—</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagnostic -->
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <h3 class="font-bold text-gray-700 mb-3 text-sm uppercase tracking-wide">🔧 Diagnostic technique</h3>
                <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                    <div>
                        <span class="text-gray-500 text-xs uppercase">État de retour</span>
                        <div class="mt-1">
                            <span id="modal-etat-badge"
                                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700">—</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs uppercase">Localisation stock</span>
                        <p class="font-semibold text-gray-800 mt-0.5" id="modal-localisation">—</p>
                    </div>
                </div>
                <div class="text-sm">
                    <span class="text-gray-500 text-xs uppercase">Diagnostic</span>
                    <p class="text-gray-800 mt-1 p-3 bg-white rounded-lg border border-gray-200 leading-relaxed"
                       id="modal-diagnostic">—</p>
                </div>
            </div>

            <!-- Valeur & Observations -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-purple-50 rounded-xl p-4 border border-purple-200">
                    <span class="text-purple-600 text-xs uppercase font-bold">💰 Valeur résiduelle</span>
                    <p class="text-2xl font-bold text-purple-900 mt-2" id="modal-valeur">—</p>
                </div>
                <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                    <span class="text-yellow-700 text-xs uppercase font-bold">💬 Observations</span>
                    <p class="text-sm text-yellow-900 mt-2 leading-relaxed" id="modal-observations">—</p>
                </div>
            </div>
        </div>

        <!-- Pied modal -->
        <div class="px-6 pb-6 flex justify-end border-t border-gray-100 pt-4">
            <button onclick="closeRetourModal()"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-8 rounded-lg transition">
                Fermer
            </button>
        </div>
    </div>
</div>

<script>
// Fonction de recherche dynamique
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les éléments
    const searchInput = document.getElementById('searchInput');
    const etatFilter = document.getElementById('etatFilter');
    const resetButton = document.getElementById('resetFilters');
    const clearAllFilters = document.getElementById('clearAllFilters');
    const filterBadges = document.querySelectorAll('.filter-badge');
    const searchResultsInfo = document.getElementById('searchResultsInfo');
    const resultsCount = document.getElementById('resultsCount');
    const searchTerm = document.getElementById('searchTerm');
    const totalCount = document.getElementById('totalCount');
    const filteredCount = document.getElementById('filteredCount');
    const noResultsRow = document.querySelector('#noResultsRow');
    
    const stockRows = document.querySelectorAll('.stock-row');
    const totalStocks = stockRows.length;
    let currentFilter = '';
    
    // Fonction pour normaliser le texte (supprime les accents)
    function normalizeText(text) {
        return (text || '').normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
    }
    
    // Fonction pour mettre en surbrillance le texte correspondant
    function highlightText(element, searchTerm) {
        if (!searchTerm || !element) return;
        
        const text = element.textContent;
        const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        element.innerHTML = text.replace(regex, '<span class="search-highlight">$1</span>');
    }
    
    // Fonction pour enlever les surbrillances
    function removeHighlights() {
        document.querySelectorAll('.search-highlight').forEach(el => {
            const parent = el.parentNode;
            parent.replaceChild(document.createTextNode(el.textContent), el);
            parent.normalize();
        });
    }
    
    // Fonction de filtrage
    function filterStocks() {
        const searchTermValue = normalizeText(searchInput.value.trim());
        const selectedEtat = etatFilter.value;
        let visibleCount = 0;
        
        // Supprimer les anciennes surbrillances
        removeHighlights();
        
        // Masquer la ligne "aucun résultat" par défaut
        if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
        
        // Masquer les informations de recherche si pas de recherche
        if (!searchTermValue && !selectedEtat && !currentFilter) {
            searchResultsInfo.classList.add('hidden');
        } else {
            searchResultsInfo.classList.remove('hidden');
        }
        
        // Filtrer les lignes
        stockRows.forEach(row => {
            const numero = row.getAttribute('data-numero') || '';
            const marque = row.getAttribute('data-marque') || '';
            const modele = row.getAttribute('data-modele') || '';
            const etat = row.getAttribute('data-etat') || '';
            const origine = row.getAttribute('data-origine') || '';
            const utilisateur = row.getAttribute('data-utilisateur') || '';
            const recent = row.getAttribute('data-recent') || '0';
            
            // Vérifier la correspondance avec la recherche
            const searchMatch = !searchTermValue || 
                numero.includes(searchTermValue) ||
                marque.includes(searchTermValue) ||
                modele.includes(searchTermValue) ||
                utilisateur.includes(searchTermValue);
            
            // Vérifier la correspondance avec l'état
            const etatMatch = !selectedEtat || etat === selectedEtat;
            
            // Vérifier la correspondance avec le filtre rapide
            let filterMatch = true;
            if (currentFilter === 'bon') {
                filterMatch = etat === 'bon';
            } else if (currentFilter === 'reparable') {
                filterMatch = etat === 'reparable';
            } else if (currentFilter === 'irreparable') {
                filterMatch = etat === 'irreparable';
            } else if (currentFilter === 'parc') {
                filterMatch = origine === 'parc';
            } else if (currentFilter === 'recent') {
                filterMatch = recent === '1';
            } else if (currentFilter === 'all') {
                filterMatch = true;
            }
            
            if (searchMatch && etatMatch && filterMatch) {
                row.style.display = '';
                visibleCount++;
                
                // Mettre en surbrillance le texte recherché
                if (searchTermValue) {
                    const numeroElement = row.querySelector('.stock-numero');
                    const marqueElement = row.querySelector('.stock-marque');
                    const modeleElement = row.querySelector('.stock-modele');
                    const utilisateurElement = row.querySelector('.stock-utilisateur');
                    
                    if (numeroElement) highlightText(numeroElement, searchInput.value.trim());
                    if (marqueElement) highlightText(marqueElement, searchInput.value.trim());
                    if (modeleElement) highlightText(modeleElement, searchInput.value.trim());
                    if (utilisateurElement) highlightText(utilisateurElement, searchInput.value.trim());
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mettre à jour les informations de recherche
        updateSearchInfo(searchTermValue, selectedEtat, visibleCount);
        
        // Afficher le message "aucun résultat" si besoin
        if (visibleCount === 0) {
            if (noResultsRow) {
                noResultsRow.style.display = '';
            }
        }
    }
    
    // Mettre à jour les informations de recherche
    function updateSearchInfo(searchTermValue, selectedEtat, visibleCount) {
        if (resultsCount) {
            resultsCount.textContent = `${visibleCount} résultat${visibleCount > 1 ? 's' : ''}`;
        }
        
        if (filteredCount) {
            filteredCount.textContent = visibleCount === totalStocks ? '' : `${visibleCount} sur ${totalStocks}`;
        }
        
        if (searchTerm) {
            let infoText = '';
            if (searchTermValue) {
                infoText += `Recherche : "${searchInput.value}"`;
            }
            if (selectedEtat) {
                if (infoText) infoText += ' • ';
                infoText += `État : ${getEtatName(selectedEtat)}`;
            }
            if (currentFilter && currentFilter !== 'all') {
                if (infoText) infoText += ' • ';
                infoText += `Filtre : ${getFilterName(currentFilter)}`;
            }
            searchTerm.textContent = infoText;
        }
    }
    
    // Obtenir le nom de l'état
    function getEtatName(etat) {
        switch(etat) {
            case 'bon': return 'Bon état';
            case 'reparable': return 'Réparable';
            case 'irreparable': return 'Irréparable';
            default: return etat;
        }
    }
    
    // Obtenir le nom du filtre
    function getFilterName(filter) {
        switch(filter) {
            case 'all': return 'Tous';
            case 'bon': return 'Bon état';
            case 'reparable': return 'Réparable';
            case 'irreparable': return 'Irréparable';
            case 'parc': return 'Parc';
            case 'recent': return 'Retour récent';
            default: return '';
        }
    }
    
    // Mettre à jour l'état des badges de filtre
    function updateFilterBadges() {
        filterBadges.forEach(badge => {
            if (badge.dataset.filter === currentFilter) {
                badge.classList.add('active');
            } else {
                badge.classList.remove('active');
            }
        });
    }
    
    // Événements
    searchInput.addEventListener('input', filterStocks);
    etatFilter.addEventListener('change', filterStocks);
    
    resetButton.addEventListener('click', function() {
        searchInput.value = '';
        etatFilter.value = '';
        currentFilter = '';
        updateFilterBadges();
        filterStocks();
        searchInput.focus();
    });
    
    clearAllFilters.addEventListener('click', function() {
        searchInput.value = '';
        etatFilter.value = '';
        currentFilter = '';
        updateFilterBadges();
        filterStocks();
        searchInput.focus();
    });
    
    filterBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            currentFilter = this.dataset.filter;
            
            if (currentFilter === 'bon' || currentFilter === 'reparable' || currentFilter === 'irreparable') {
                etatFilter.value = currentFilter;
            } else {
                etatFilter.value = '';
            }
            
            updateFilterBadges();
            filterStocks();
        });
    });
    
    // Debouncing pour les performances
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterStocks, 300);
    });
    
    // Recherche avec Entrée
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            filterStocks();
        }
    });
    
    // Initialiser avec les valeurs de l'URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search')) {
        searchInput.value = urlParams.get('search');
    }
    if (urlParams.has('etat')) {
        etatFilter.value = urlParams.get('etat');
    }
    
    // Initialiser le filtrage
    filterStocks();
});

// ── Modal Fiche de Retour ───────────────────────────────────────────
function openRetourModal(data) {
    document.getElementById('modal-header-serie').textContent = 'N° de série : ' + (data.numero_serie || '—');
    document.getElementById('modal-nom').textContent          = data.nom           || '—';
    document.getElementById('modal-serie').textContent        = data.numero_serie  || '—';
    document.getElementById('modal-marque').textContent       = data.marque        || '—';
    document.getElementById('modal-modele').textContent       = data.modele        || '—';
    document.getElementById('modal-date-retour').textContent  = data.date_retour   || '—';
    document.getElementById('modal-origine').textContent      = data.origine       || '—';
    document.getElementById('modal-raison').textContent       = data.raison_retour || '—';
    document.getElementById('modal-localisation').textContent = data.localisation  || '—';
    document.getElementById('modal-diagnostic').textContent   = data.diagnostic    || '—';
    document.getElementById('modal-valeur').textContent       = data.valeur_residuelle || '—';
    document.getElementById('modal-observations').textContent = data.observations  || '—';

    // Badge état coloré
    var badge   = document.getElementById('modal-etat-badge');
    var etatMap = {
        bon:         { cls: 'bg-green-100 text-green-800',   label: '✅ Bon état' },
        reparable:   { cls: 'bg-yellow-100 text-yellow-800', label: '⚠️ Réparable' },
        irreparable: { cls: 'bg-red-100 text-red-800',       label: '❌ Irréparable' }
    };
    var cfg = etatMap[data.etat_retour] || { cls: 'bg-gray-100 text-gray-700', label: data.etat_retour || '—' };
    badge.className   = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ' + cfg.cls;
    badge.textContent = cfg.label;

    // Gestion des informations utilisateur
    var userSection = document.getElementById('userInfoSection');
    if (data.origine && data.origine.toLowerCase() === 'parc' && 
        (data.utilisateur_nom !== '-' || data.utilisateur_prenom !== '-' || data.utilisateur_nom_complet !== '-')) {
        
        if (data.utilisateur_nom_complet && data.utilisateur_nom_complet !== '-') {
            document.getElementById('modal-user-nomcomplet').textContent = data.utilisateur_nom_complet;
        } else {
            var nomComplet = '';
            if (data.utilisateur_prenom && data.utilisateur_prenom !== '-') nomComplet += data.utilisateur_prenom + ' ';
            if (data.utilisateur_nom && data.utilisateur_nom !== '-') nomComplet += data.utilisateur_nom;
            document.getElementById('modal-user-nomcomplet').textContent = nomComplet.trim() || '—';
        }
        
        document.getElementById('modal-user-position').textContent = data.position || '—';
        document.getElementById('modal-user-departement').textContent = data.departement || '—';
        document.getElementById('modal-user-email').textContent = data.email || '—';
        document.getElementById('modal-user-telephone').textContent = data.telephone || '—';
        document.getElementById('modal-user-dateaffectation').textContent = data.date_affectation || '—';
        document.getElementById('modal-user-statut').textContent = data.statut_usage || '—';
        document.getElementById('modal-user-raison').textContent = data.affectation_reason || '—';
        document.getElementById('modal-user-raison-detail').textContent = data.affectation_reason_detail || '—';
        document.getElementById('modal-user-bon').textContent = data.numero_bon_affectation || '—';
        document.getElementById('modal-user-notes').textContent = data.notes_affectation || '—';
        
        userSection.classList.remove('hidden');
    } else {
        userSection.classList.add('hidden');
    }

    // Afficher la modal
    var modal = document.getElementById('retourModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeRetourModal() {
    var modal = document.getElementById('retourModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

// Fermer en cliquant sur le fond noir
document.getElementById('retourModal').addEventListener('click', function(e) {
    if (e.target === this) closeRetourModal();
});

// Fermer avec Echap
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeRetourModal();
});
</script>
@endsection