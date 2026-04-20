{{-- resources/views/change/n1/form.blade.php --}}
@extends('layouts.app')

@section('title', isset($ticket) ? $ticket->titre : 'Nouveau formulaire')
@section('header', 'Change Management - N+1')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête avec navigation -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                {{ isset($ticket) ? ($ticket->titre ?: 'Modifier le formulaire') : 'Nouveau formulaire de changement' }}
            </h1>
            @if(isset($ticket))
                <p class="text-gray-600 mt-2">
                    {{ $ticket->ticket_id }} · 
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($ticket->status === 'DRAFT') bg-gray-100 text-gray-800
                        @elseif($ticket->status === 'PENDING_N2') bg-yellow-100 text-yellow-800
                        @elseif($ticket->status === 'VALIDATED_N2') bg-green-100 text-green-800
                        @elseif($ticket->status === 'REJECTED') bg-red-100 text-red-800
                        @else bg-purple-100 text-purple-800
                        @endif">
                        {{ $ticket->status_label }}
                    </span>
                </p>
            @else
                <p class="text-gray-600 mt-2">Créez une nouvelle demande de changement</p>
            @endif
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            @if(isset($ticket) && $ticket->incident_num)
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg px-4 py-2">
                    <div class="text-xs text-indigo-600 font-semibold">RAPPORT INCIDENT</div>
                    <div class="text-lg font-bold text-indigo-700">{{ $ticket->incident_num }}</div>
                </div>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(isset($ticket) && $ticket->status === 'REJECTED')
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            ⚠️ Ce formulaire a été rejeté par N+2. Veuillez le modifier et le soumettre à nouveau.
            @if($ticket->rejet_note)
                <br><strong>Motif : {{ $ticket->rejet_note }}</strong>
            @endif
        </div>
    @endif
    
    @if(isset($ticket) && $ticket->status === 'VALIDATED_N2')
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            ✅ Formulaire validé par N+2. Vous pouvez maintenant compléter le rapport d'incident et le transmettre à N+3.
            <p class="text-sm text-green-600 mt-1">Les informations générales, la problématique et l'analyse d'impact ne sont plus modifiables.</p>
        </div>
    @endif
    
    @if(isset($ticket) && $ticket->status === 'CLOSED')
        <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg">
            🔒 Ce ticket est clôturé. <strong>{{ $ticket->close_note ?? '' }}</strong>
        </div>
    @endif

    <!-- Déterminer les modes d'édition -->
    @php
        $isEditable = !isset($ticket) || $ticket->status === 'DRAFT' || $ticket->status === 'REJECTED';
        $isReadOnly = isset($ticket) && in_array($ticket->status, ['PENDING_N2', 'PENDING_N3', 'CLOSED']);
        $canEditIncident = isset($ticket) && $ticket->status === 'VALIDATED_N2';
        $isMainSectionsReadOnly = isset($ticket) && ($ticket->status === 'VALIDATED_N2' || $ticket->status === 'PENDING_N2' || $ticket->status === 'PENDING_N3' || $ticket->status === 'CLOSED');
    @endphp

    <!-- Formulaire -->
    <form method="POST" action="{{ isset($ticket) ? route('change.n1.update', $ticket) : route('change.n1.store') }}" 
          enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if(isset($ticket))
            @method('PUT')
        @endif

        <!-- SECTION 1 - Informations générales -->
        <div class="form-card {{ $isMainSectionsReadOnly ? 'bg-gray-50' : 'bg-white' }} rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r {{ $isMainSectionsReadOnly ? 'from-gray-100 to-gray-200' : 'from-gray-50 to-gray-100' }} px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold {{ $isMainSectionsReadOnly ? 'text-gray-600' : 'text-gray-800' }}">1. Informations générales</h2>
                    <span class="text-sm text-gray-500">§1</span>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Titre du changement (pleine largeur) -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Titre du changement <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="titre" value="{{ old('titre', $ticket->titre ?? '') }}" 
                               {{ $isMainSectionsReadOnly ? 'disabled' : '' }}
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isMainSectionsReadOnly ? 'bg-gray-100' : '' }}"
                               placeholder="Ex: Correction paramètre taux change FCUB">
                        @error('titre') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- NUMÉRO DE TICKET (NOUVEAU CHAMP) -->
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Numéro de ticket <span class="text-gray-400 text-xs">(optionnel)</span>
                        </label>
                        <input type="text" name="ticket_number" value="{{ old('ticket_number', $ticket->ticket_number ?? '') }}"
                               {{ $isMainSectionsReadOnly ? 'disabled' : '' }}
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isMainSectionsReadOnly ? 'bg-gray-100' : '' }}"
                               placeholder="Ex: TICKET-2024-001">
                        @error('ticket_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-500 mt-1">ID unique système: <span class="font-mono text-indigo-600">{{ $ticket->ticket_id ?? 'Généré automatiquement' }}</span></p>
                    </div>
                    
                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" {{ $isMainSectionsReadOnly ? 'disabled' : '' }}
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isMainSectionsReadOnly ? 'bg-gray-100' : '' }}">
                            @foreach($typeOpts as $opt)
                                <option value="{{ $opt }}" {{ (old('type', $ticket->type ?? 'Standard') == $opt) ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Environnement -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Environnement <span class="text-red-500">*</span>
                        </label>
                        <select name="environnement" {{ $isMainSectionsReadOnly ? 'disabled' : '' }}
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isMainSectionsReadOnly ? 'bg-gray-100' : '' }}">
                            @foreach($envOpts as $opt)
                                <option value="{{ $opt }}" {{ (old('environnement', $ticket->environnement ?? 'Flexcube') == $opt) ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('environnement') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- PRÉNOM - Toujours readonly car vient de l'utilisateur -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        @php
                            $prenomValue = old('prenom', $ticket->prenom ?? (auth()->user()->prenom ?? ''));
                        @endphp
                        <input type="text" 
                               value="{{ $prenomValue }}"
                               readonly
                               class="w-full rounded-lg border-gray-300 bg-gray-100 text-gray-700 cursor-not-allowed"
                               placeholder="Prénom">
                        <!-- Champ caché pour envoyer la valeur -->
                        <input type="hidden" name="prenom" value="{{ $prenomValue }}">
                        @error('prenom') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- NOM - Toujours readonly car vient de l'utilisateur -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        @php
                            $nomValue = old('nom', $ticket->nom ?? (auth()->user()->name ?? ''));
                        @endphp
                        <input type="text" 
                               value="{{ $nomValue }}"
                               readonly
                               class="w-full rounded-lg border-gray-300 bg-gray-100 text-gray-700 cursor-not-allowed"
                               placeholder="Nom">
                        <!-- Champ caché pour envoyer la valeur -->
                        <input type="hidden" name="nom" value="{{ $nomValue }}">
                        @error('nom') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- DÉPARTEMENT - Toujours readonly car vient de l'utilisateur -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Département <span class="text-red-500">*</span>
                        </label>
                        @php
                            $departementValue = old('departement', $ticket->departement ?? (auth()->user()->departement ?? ''));
                        @endphp
                        <input type="text" 
                               value="{{ $departementValue }}"
                               readonly
                               class="w-full rounded-lg border-gray-300 bg-gray-100 text-gray-700 cursor-not-allowed"
                               placeholder="Ex: Informatique Bancaire">
                        <!-- Champ caché pour envoyer la valeur -->
                        <input type="hidden" name="departement" value="{{ $departementValue }}">
                        @error('departement') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <!-- Date prévue d'exécution -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date prévue d'exécution
                        </label>
                        <input type="date" name="date_execution" value="{{ old('date_execution', isset($ticket) && $ticket->date_execution ? $ticket->date_execution->format('Y-m-d') : '') }}"
                               {{ $isMainSectionsReadOnly ? 'disabled' : '' }}
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isMainSectionsReadOnly ? 'bg-gray-100' : '' }}">
                        @error('date_execution') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2 - Problématique -->
        <div class="form-card {{ $isMainSectionsReadOnly ? 'bg-gray-50' : 'bg-white' }} rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r {{ $isMainSectionsReadOnly ? 'from-gray-100 to-gray-200' : 'from-gray-50 to-gray-100' }} px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold {{ $isMainSectionsReadOnly ? 'text-gray-600' : 'text-gray-800' }}">2. Problématique</h2>
                    <span class="text-sm text-gray-500">§2</span>
                </div>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Description de la situation actuelle / dysfonctionnement <span class="text-red-500">*</span>
                </label>
                <textarea name="problematique" rows="4" 
                          {{ $isMainSectionsReadOnly ? 'disabled' : '' }}
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isMainSectionsReadOnly ? 'bg-gray-100' : '' }}"
                          placeholder="Décrire clairement la situation actuelle, le dysfonctionnement ou la nécessité du changement...">{{ old('problematique', $ticket->problematique ?? '') }}</textarea>
                @error('problematique') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- SECTION 3 - Analyse de l'impact -->
        <div class="form-card {{ $isMainSectionsReadOnly ? 'bg-gray-50' : 'bg-white' }} rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r {{ $isMainSectionsReadOnly ? 'from-gray-100 to-gray-200' : 'from-gray-50 to-gray-100' }} px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold {{ $isMainSectionsReadOnly ? 'text-gray-600' : 'text-gray-800' }}">3. Analyse de l'impact</h2>
                    <span class="text-sm text-gray-500">§3</span>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Impact sur les opérations</label>
                        <select name="impact_ops" {{ $isMainSectionsReadOnly ? 'disabled' : '' }}
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isMainSectionsReadOnly ? 'bg-gray-100' : '' }}">
                            <option value="">Sélectionner...</option>
                            @foreach($impactLvls as $lvl)
                                <option value="{{ $lvl }}" {{ (old('impact_ops', $ticket->impact_ops ?? '') == $lvl) ? 'selected' : '' }}>
                                    {{ $lvl }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Impact sur les utilisateurs</label>
                        <select name="impact_users" {{ $isMainSectionsReadOnly ? 'disabled' : '' }}
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isMainSectionsReadOnly ? 'bg-gray-100' : '' }}">
                            <option value="">Sélectionner...</option>
                            @foreach($impactLvls as $lvl)
                                <option value="{{ $lvl }}" {{ (old('impact_users', $ticket->impact_users ?? '') == $lvl) ? 'selected' : '' }}>
                                    {{ $lvl }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Impact sur la production</label>
                        <select name="impact_prod" {{ $isMainSectionsReadOnly ? 'disabled' : '' }}
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isMainSectionsReadOnly ? 'bg-gray-100' : '' }}">
                            <option value="">Sélectionner...</option>
                            @foreach($impactLvls as $lvl)
                                <option value="{{ $lvl }}" {{ (old('impact_prod', $ticket->impact_prod ?? '') == $lvl) ? 'selected' : '' }}>
                                    {{ $lvl }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Risques en cas de non-exécution</label>
                        <textarea name="risques" rows="2" 
                                  {{ $isMainSectionsReadOnly ? 'disabled' : '' }}
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isMainSectionsReadOnly ? 'bg-gray-100' : '' }}"
                                  placeholder="Décrire les risques...">{{ old('risques', $ticket->risques ?? '') }}</textarea>
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Plan de retour arrière (Rollback)</label>
                        <textarea name="rollback" rows="2" 
                                  {{ $isMainSectionsReadOnly ? 'disabled' : '' }}
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isMainSectionsReadOnly ? 'bg-gray-100' : '' }}"
                                  placeholder="Procédure de rollback...">{{ old('rollback', $ticket->rollback ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- RAPPORT INCIDENT - visible uniquement après validation N+2 -->
        @if(isset($ticket) && $ticket->incident_num)
        <div class="form-card {{ $canEditIncident ? 'bg-white' : 'bg-gray-50' }} rounded-xl shadow-md overflow-hidden border-2 {{ $canEditIncident ? 'border-green-300' : 'border-gray-200' }}">
            <div class="bg-gradient-to-r {{ $canEditIncident ? 'from-green-500 to-green-600' : 'from-gray-100 to-gray-200' }} px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold {{ $canEditIncident ? 'text-white' : 'text-gray-700' }}">
                        Rapport d'incident #{{ $ticket->incident_num }}
                    </h2>
                    @if($canEditIncident)
                        <span class="px-3 py-1 bg-white text-green-700 rounded-full text-xs font-semibold">
                            À compléter
                        </span>
                    @endif
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description de l'incident 
                            @if($canEditIncident)<span class="text-red-500">*</span>@endif
                        </label>
                        <textarea name="incident_description" rows="4" 
                                  {{ !$canEditIncident ? 'disabled' : '' }}
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 {{ !$canEditIncident ? 'bg-gray-100' : '' }}"
                                  placeholder="{{ $canEditIncident ? 'Décrire les détails de l\'incident survenu...' : '' }}">{{ old('incident_description', $ticket->incident_description ?? '') }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Actions correctives
                            @if($canEditIncident)<span class="text-red-500">*</span>@endif
                        </label>
                        <textarea name="incident_actions" rows="3" 
                                  {{ !$canEditIncident ? 'disabled' : '' }}
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 {{ !$canEditIncident ? 'bg-gray-100' : '' }}"
                                  placeholder="{{ $canEditIncident ? 'Actions prises pour résoudre l\'incident...' : '' }}">{{ old('incident_actions', $ticket->incident_actions ?? '') }}</textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Date de résolution
                                @if($canEditIncident)<span class="text-red-500">*</span>@endif
                            </label>
                            <input type="datetime-local" name="incident_resolved_at" 
                                   value="{{ old('incident_resolved_at', isset($ticket) && $ticket->incident_resolved_at ? $ticket->incident_resolved_at->format('Y-m-d\TH:i') : '') }}"
                                   {{ !$canEditIncident ? 'disabled' : '' }}
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 {{ !$canEditIncident ? 'bg-gray-100' : '' }}">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Impact résiduel
                            </label>
                            <select name="incident_impact_residuel" {{ !$canEditIncident ? 'disabled' : '' }}
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 {{ !$canEditIncident ? 'bg-gray-100' : '' }}">
                                <option value="">Sélectionner...</option>
                                <option value="Faible" {{ (old('incident_impact_residuel', $ticket->incident_impact_residuel ?? '') == 'Faible') ? 'selected' : '' }}>Faible</option>
                                <option value="Moyen" {{ (old('incident_impact_residuel', $ticket->incident_impact_residuel ?? '') == 'Moyen') ? 'selected' : '' }}>Moyen</option>
                                <option value="Élevé" {{ (old('incident_impact_residuel', $ticket->incident_impact_residuel ?? '') == 'Élevé') ? 'selected' : '' }}>Élevé</option>
                            </select>
                        </div>
                    </div>
                    
                    @if($canEditIncident)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                            <div class="flex">
                                <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="text-sm text-blue-700">
                                        Veuillez documenter l'incident survenu. Ces informations seront essentielles pour le suivi et l'analyse.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- File Upload avec bouton Ajouter -->
        <div class="form-card bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">Fichiers joints</h2>
                    <span class="text-sm text-gray-500">§6</span>
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-medium text-gray-700">
                        Documents justificatifs
                    </label>
                    @if($isEditable || $canEditIncident)
                        <button type="button" 
                                onclick="addFileField()"
                                class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Ajouter un fichier
                        </button>
                    @endif
                </div>
                
                <!-- Conteneur pour les fichiers uploadés (affichés) -->
                @if(isset($ticket) && $ticket->files)
                    <div class="mb-4 space-y-2" id="uploaded-files-container">
                        @foreach($ticket->files as $index => $file)
                            <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3 border border-gray-200 hover:border-indigo-200 transition-colors">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">{{ $file['name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($file['size'] / 1024, 1) }} KB</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <a href="{{ route('change.file.download', ['ticketId' => $ticket->id, 'fileIndex' => $index]) }}" 
                                       target="_blank"
                                       class="text-indigo-600 hover:text-indigo-800 p-2 transition-colors"
                                       title="Ouvrir le fichier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if($isEditable || $canEditIncident)
                                        <button type="button" class="text-red-600 hover:text-red-800 p-1 ml-1"
                                                onclick="if(confirm('Supprimer ce fichier ?')) document.getElementById('delete-file-{{ $index }}').submit();">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                        <form id="delete-file-{{ $index }}" method="POST" 
                                              action="{{ route('change.n1.delete-file', ['ticket' => $ticket->id, 'fileIndex' => $index]) }}" 
                                              style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Conteneur pour les nouveaux fichiers à uploader -->
                <div id="files-container" class="space-y-3">
                    <!-- Les champs de fichier seront ajoutés ici dynamiquement -->
                </div>

                <!-- Message si aucun fichier -->
                <div id="no-files-message" class="text-center py-6 {{ (isset($ticket) && $ticket->files) ? 'hidden' : '' }}">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Aucun fichier sélectionné</p>
                    <p class="text-xs text-gray-400 mt-1">Cliquez sur "Ajouter un fichier" pour commencer</p>
                </div>
            </div>
        </div>

        <!-- SECTIONS 4, 5, 7 — readonly for N+1 -->
        @if(isset($ticket) && ($ticket->recommandation || $ticket->requete || $ticket->resultat))
            <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-700">4. Recommandation (N+2)</h2>
                        <span class="text-sm text-gray-500">§4</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-600 mb-2">Recommandation</label>
                            <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->recommandation ?: '—' }}</div>
                        </div>
                        @if($ticket->recomm_files)
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-600 mb-2">Fiches de test</label>
                                <div class="space-y-2">
                                    @foreach($ticket->recomm_files as $index => $file)
                                        <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-gray-200 hover:border-indigo-200 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span class="text-sm text-gray-700">{{ $file['name'] }}</span>
                                            </div>
                                            <a href="{{ route('change.file.download', ['ticketId' => $ticket->id, 'fileIndex' => $index, 'type' => 'recomm_files']) }}" 
                                               target="_blank"
                                               class="text-indigo-600 hover:text-indigo-800 p-1"
                                               title="Ouvrir le fichier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-700">5. Requête à exécuter (N+2)</h2>
                        <span class="text-sm text-gray-500">§5</span>
                    </div>
                </div>
                <div class="p-6">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Commande / Script / SQL</label>
                    <div class="bg-white p-3 rounded-lg border border-gray-200 font-mono text-sm text-gray-800">{{ $ticket->requete ?: '—' }}</div>
                </div>
            </div>

            <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-700">7. Exécution & Résultat (N+2)</h2>
                        <span class="text-sm text-gray-500">§7</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Date d'exécution</label>
                            <div class="bg-white p-3 rounded-lg border border-gray-200">{{ $ticket->date_exec_reelle ? $ticket->date_exec_reelle->format('d/m/Y H:i') : '—' }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Opérateur</label>
                            <div class="bg-white p-3 rounded-lg border border-gray-200">{{ $ticket->operateur ?: '—' }}</div>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-600 mb-2">Résultat obtenu</label>
                            <div class="bg-white p-3 rounded-lg border border-gray-200">{{ $ticket->resultat ?: '—' }}</div>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-600 mb-2">Écarts / Anomalies</label>
                            <div class="bg-white p-3 rounded-lg border border-gray-200">{{ $ticket->ecarts ?: '—' }}</div>
                        </div>
                        @if($ticket->exec_files)
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-600 mb-2">Screenshots / Logs</label>
                                <div class="space-y-2">
                                    @foreach($ticket->exec_files as $index => $file)
                                        <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-gray-200 hover:border-indigo-200 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span class="text-sm text-gray-700">{{ $file['name'] }}</span>
                                            </div>
                                            <a href="{{ route('change.file.download', ['ticketId' => $ticket->id, 'fileIndex' => $index, 'type' => 'exec_files']) }}" 
                                               target="_blank"
                                               class="text-indigo-600 hover:text-indigo-800 p-1"
                                               title="Ouvrir le fichier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- TIMELINE -->
        @if(isset($ticket) && $ticket->history)
            <div class="form-card bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">HISTORIQUE</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($ticket->history as $h)
                            <div class="flex">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2"
                                         style="background: {{ $h['role'] === 'N1' ? 'rgba(59,130,246,0.1)' : ($h['role'] === 'N2' ? 'rgba(16,185,129,0.1)' : 'rgba(139,92,246,0.1)') }}; 
                                                border-color: {{ $h['role'] === 'N1' ? '#3b82f6' : ($h['role'] === 'N2' ? '#10b981' : '#8b5cf6') }};
                                                color: {{ $h['role'] === 'N1' ? '#3b82f6' : ($h['role'] === 'N2' ? '#10b981' : '#8b5cf6') }};">
                                        {{ $h['role'] }}
                                    </div>
                                </div>
                                <div class="flex-1 pb-4 relative">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-base font-semibold text-gray-900">{{ $h['action'] }}</h4>
                                        <span class="text-sm text-gray-500">{{ $h['at'] }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $h['role'] }}</p>
                                    @if(!empty($h['note']))
                                        <p class="text-sm text-gray-700 mt-2 bg-gray-50 p-2 rounded italic">"{{ $h['note'] }}"</p>
                                    @endif
                                    @if(!$loop->last)
                                        <div class="absolute left-[-24px] top-10 bottom-0 w-0.5 bg-gray-200"></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Boutons d'action -->
        <div class="flex justify-end space-x-3 pt-4">
            <a href="{{ route('change.n1.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
            
            @if($isEditable)
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Sauvegarder
                </button>
            @endif
            
            @if(isset($ticket) && ($ticket->status === 'DRAFT' || $ticket->status === 'REJECTED'))
                @php
                    $canSubmit = $ticket->titre && $ticket->prenom && $ticket->nom && $ticket->departement && $ticket->problematique;
                @endphp
                <button type="button" 
                        onclick="submitToN2()"
                        {{ $canSubmit ? '' : 'disabled' }}
                        class="px-6 py-2 {{ $canSubmit ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed' }} text-white font-semibold rounded-lg transition-colors inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Soumettre à N+2
                </button>
            @endif
            
            @if($canEditIncident)
                <button type="button" 
                        onclick="submitToN3()"
                        class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Transmettre à N+3
                </button>
            @endif
        </div>
    </form>

    <!-- Bouton de retour au dashboard -->
    <div class="mt-8">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Retour au Dashboard
        </a>
    </div>
</div>

<style>
/* ✅ FIX: Cibler uniquement les cartes du formulaire avec .form-card
   et non pas .bg-white / .bg-gray-50 qui affectaient aussi la sidebar */
.form-card,
.form-card-readonly {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.form-card:hover,
.form-card-readonly:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
}

/* Style pour les champs désactivés */
input:disabled, select:disabled, textarea:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    background-color: #f3f4f6;
}

/* Style spécifique pour les champs readonly */
input[readonly], select[readonly], textarea[readonly] {
    background-color: #f3f4f6;
    border-color: #e5e7eb;
    color: #6b7280;
    cursor: default;
}

/* Animation pour le bouton d'upload */
.file-upload-zone {
    transition: all 0.3s ease;
}

.file-upload-zone:hover {
    border-color: #6366f1;
    background-color: #f5f3ff;
}

/* Style pour l'historique */
.timeline-dot {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Animation pour les fichiers */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

/* Style pour les liens de fichiers */
a:hover svg {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .w-10.h-10 {
        width: 2rem;
        height: 2rem;
        font-size: 0.75rem;
    }
}
</style>

@push('scripts')
<script>
// Compteur pour les champs de fichier
let fileFieldCount = 0;

function addFileField() {
    const container = document.getElementById('files-container');
    const messageDiv = document.getElementById('no-files-message');
    
    // Cacher le message "aucun fichier"
    if (messageDiv) {
        messageDiv.classList.add('hidden');
    }
    
    // Créer un nouvel ID unique pour ce champ
    const fieldId = 'file-field-' + fileFieldCount;
    
    const div = document.createElement('div');
    div.id = fieldId;
    div.className = 'flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 animate-fade-in';
    div.innerHTML = `
        <div class="flex-1">
            <div class="relative">
                <input type="file" 
                       name="files[]" 
                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                       onchange="handleFileSelect(this, '${fieldId}')">
                <div class="flex items-center px-4 py-2 border border-gray-300 rounded-lg bg-white">
                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span class="text-sm text-gray-600 file-name">Choisir un fichier</span>
                </div>
            </div>
        </div>
        <button type="button" 
                onclick="removeFileField('${fieldId}')"
                class="text-red-600 hover:text-red-800 p-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    
    container.appendChild(div);
    fileFieldCount++;
}

function handleFileSelect(input, fieldId) {
    const fileName = input.files[0]?.name || 'Choisir un fichier';
    const fileNameSpan = document.querySelector(`#${fieldId} .file-name`);
    if (fileNameSpan) {
        fileNameSpan.textContent = fileName;
        fileNameSpan.classList.add('text-gray-900', 'font-medium');
    }
}

function removeFileField(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.remove();
    }
    
    // Vérifier s'il reste des champs
    const container = document.getElementById('files-container');
    const messageDiv = document.getElementById('no-files-message');
    const uploadedContainer = document.getElementById('uploaded-files-container');
    
    const hasUploadedFiles = uploadedContainer && uploadedContainer.children.length > 0;
    const hasNewFiles = container.children.length > 0;
    
    if (!hasUploadedFiles && !hasNewFiles && messageDiv) {
        messageDiv.classList.remove('hidden');
    }
}

function submitToN2() {
    if(confirm('Soumettre ce formulaire à N+2 ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ isset($ticket) ? route('change.n1.submit-n2', $ticket) : '' }}';
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}

function submitToN3() {
    if(confirm('Transmettre ce formulaire à N+3 pour clôture ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ isset($ticket) ? route('change.n1.submit-n3', $ticket) : '' }}';
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection