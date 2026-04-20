{{-- resources/views/change/n2/form.blade.php --}}
@extends('layouts.app')

@section('title', $ticket->titre)
@section('header', 'Change Management - N+2')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête avec navigation -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ $ticket->titre }}</h1>
            <p class="text-gray-600 mt-2">
                {{ $ticket->ticket_id }} · 
                @if($ticket->ticket_number)
                    <span class="text-gray-500 mr-2">N°: {{ $ticket->ticket_number }}</span>
                @endif
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                    @if($ticket->status === 'PENDING_N2') bg-yellow-100 text-yellow-800
                    @elseif($ticket->status === 'VALIDATED_N2') bg-green-100 text-green-800
                    @elseif($ticket->status === 'REJECTED') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $ticket->status_label }}
                </span>
            </p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            @if($ticket->incident_num)
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

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            ❌ {{ session('error') }}
        </div>
    @endif

    <!-- Formulaire -->
    <form method="POST" action="{{ route('change.n2.update', $ticket) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- READ-ONLY SECTIONS 1, 2, 3 -->
        <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700">1. Informations générales</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">ID Système</label>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 font-mono text-sm text-indigo-600">{{ $ticket->ticket_id }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">N° Ticket</label>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->ticket_number ?: '—' }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Titre</label>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->titre }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Type</label>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->type }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Demandeur</label>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->prenom }} {{ $ticket->nom }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Département</label>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->departement }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Date prévue</label>
                        <div class="bg-white p-3 rounded-lg border border-gray-200">{{ $ticket->date_execution ? $ticket->date_execution->format('d/m/Y') : '—' }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Environnement</label>
                        <div class="bg-white p-3 rounded-lg border border-gray-200">{{ $ticket->environnement }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700">2. Problématique</h2>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-600 mb-2">Description</label>
                <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->problematique }}</div>
            </div>
        </div>

        <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700">3. Analyse de l'impact</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Impact opérations</label>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->impact_ops ?: '—' }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Impact utilisateurs</label>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->impact_users ?: '—' }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Impact production</label>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->impact_prod ?: '—' }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Risques</label>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->risques ?: '—' }}</div>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Rollback</label>
                        <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->rollback ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FICHIERS UPLOADÉS PAR N+1 (avec possibilité de les ouvrir) -->
        @if($ticket->files && count($ticket->files) > 0)
        <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700">Fichiers joints (N+1)</h2>
                <span class="text-xs text-gray-500">{{ count($ticket->files) }} fichier(s)</span>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    @foreach($ticket->files as $index => $file)
                        <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200 hover:border-indigo-200 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ $file['name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ number_format($file['size'] / 1024, 1) }} KB</p>
                                </div>
                            </div>
                            <a href="{{ route('change.file.download', ['ticketId' => $ticket->id, 'fileIndex' => $index]) }}" 
                               target="_blank"
                               class="text-indigo-600 hover:text-indigo-800 p-2 transition-colors"
                               title="Ouvrir le fichier">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- EDITABLE SECTIONS 4, 5, 7 -->
        <div class="form-card bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">4. Recommandation & Test</h2>
                    <span class="text-sm text-gray-500">§4 — N+2</span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Recommandation technique</label>
                        <textarea name="recommandation" rows="4" 
                                  {{ $ticket->status !== 'PENDING_N2' ? 'disabled' : '' }}
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $ticket->status !== 'PENDING_N2' ? 'bg-gray-100' : '' }}"
                                  placeholder="Proposer la solution technique à appliquer...">{{ old('recommandation', $ticket->recommandation) }}</textarea>
                    </div>
                    
                    <!-- Upload fichiers de recommandation avec bouton Ajouter -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Fiches de test
                            </label>
                            @if($ticket->status === 'PENDING_N2')
                                <button type="button" 
                                        onclick="addRecommFileField()"
                                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Ajouter un fichier
                                </button>
                            @endif
                        </div>
                        
                        <!-- Fichiers déjà uploadés (avec possibilité de les ouvrir) -->
                        @if($ticket->recomm_files && count($ticket->recomm_files) > 0)
                            <div class="mb-4 space-y-2" id="uploaded-recomm-files-container">
                                @foreach($ticket->recomm_files as $index => $file)
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
                                        <a href="{{ route('change.file.download', ['ticketId' => $ticket->id, 'fileIndex' => $index, 'type' => 'recomm_files']) }}" 
                                           target="_blank"
                                           class="text-indigo-600 hover:text-indigo-800 p-2 transition-colors"
                                           title="Ouvrir le fichier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Conteneur pour les nouveaux fichiers de recommandation -->
                        <div id="recomm-files-container" class="space-y-3">
                            <!-- Les champs de fichier seront ajoutés ici dynamiquement -->
                        </div>

                        <!-- Message si aucun fichier de recommandation -->
                        <div id="no-recomm-files-message" class="text-center py-6 {{ (isset($ticket) && $ticket->recomm_files && count($ticket->recomm_files) > 0) ? 'hidden' : '' }}">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500">Aucune fiche de test</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">5. Requête à exécuter</h2>
                    <span class="text-sm text-gray-500">§5 — N+2</span>
                </div>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Commande / Script / SQL / Procédure</label>
                <textarea name="requete" rows="5" 
                          {{ $ticket->status !== 'PENDING_N2' ? 'disabled' : '' }}
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm {{ $ticket->status !== 'PENDING_N2' ? 'bg-gray-100' : '' }}"
                          placeholder="Ex: UPDATE PARAM_TABLE SET VALUE = '...' WHERE ID = '...'">{{ old('requete', $ticket->requete) }}</textarea>
            </div>
        </div>

        <div class="form-card bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">7. Exécution & Résultat</h2>
                    <span class="text-sm text-gray-500">§7 — N+2</span>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date d'exécution</label>
                        <input type="datetime-local" name="date_exec_reelle" 
                               value="{{ old('date_exec_reelle', $ticket->date_exec_reelle ? $ticket->date_exec_reelle->format('Y-m-d\TH:i') : '') }}"
                               {{ $ticket->status !== 'PENDING_N2' ? 'disabled' : '' }}
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $ticket->status !== 'PENDING_N2' ? 'bg-gray-100' : '' }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Opérateur</label>
                        <input type="text" name="operateur" 
                               value="{{ old('operateur', $ticket->operateur) }}"
                               {{ $ticket->status !== 'PENDING_N2' ? 'disabled' : '' }}
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $ticket->status !== 'PENDING_N2' ? 'bg-gray-100' : '' }}"
                               placeholder="Nom de l'opérateur">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Résultat obtenu</label>
                        <textarea name="resultat" rows="3" 
                                  {{ $ticket->status !== 'PENDING_N2' ? 'disabled' : '' }}
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $ticket->status !== 'PENDING_N2' ? 'bg-gray-100' : '' }}"
                                  placeholder="Décrire le résultat...">{{ old('resultat', $ticket->resultat) }}</textarea>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Écarts / Anomalies rencontrées</label>
                        <textarea name="ecarts" rows="2" 
                                  {{ $ticket->status !== 'PENDING_N2' ? 'disabled' : '' }}
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $ticket->status !== 'PENDING_N2' ? 'bg-gray-100' : '' }}"
                                  placeholder="Aucun écart / ou détailler les anomalies...">{{ old('ecarts', $ticket->ecarts) }}</textarea>
                    </div>
                    
                    <!-- Upload screenshots/logs avec bouton Ajouter -->
                    <div class="col-span-2">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Screenshots / Logs
                            </label>
                            @if($ticket->status === 'PENDING_N2')
                                <button type="button" 
                                        onclick="addExecFileField()"
                                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Ajouter un fichier
                                </button>
                            @endif
                        </div>
                        
                        <!-- Fichiers déjà uploadés (avec possibilité de les ouvrir) -->
                        @if($ticket->exec_files && count($ticket->exec_files) > 0)
                            <div class="mb-4 space-y-2" id="uploaded-exec-files-container">
                                @foreach($ticket->exec_files as $index => $file)
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
                                        <a href="{{ route('change.file.download', ['ticketId' => $ticket->id, 'fileIndex' => $index, 'type' => 'exec_files']) }}" 
                                           target="_blank"
                                           class="text-indigo-600 hover:text-indigo-800 p-2 transition-colors"
                                           title="Ouvrir le fichier">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Conteneur pour les nouveaux fichiers d'exécution -->
                        <div id="exec-files-container" class="space-y-3">
                            <!-- Les champs de fichier seront ajoutés ici dynamiquement -->
                        </div>

                        <!-- Message si aucun fichier d'exécution -->
                        <div id="no-exec-files-message" class="text-center py-6 {{ (isset($ticket) && $ticket->exec_files && count($ticket->exec_files) > 0) ? 'hidden' : '' }}">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500">Aucun screenshot ou log</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HISTORIQUE -->
        @if($ticket->history && count($ticket->history) > 0)
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
            <a href="{{ route('change.n2.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
            
            @if($ticket->status === 'PENDING_N2')
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Sauvegarder
                </button>
                <button type="button" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center" onclick="showRejectModal()">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Rejeter
                </button>
                <button type="button" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center" onclick="validateTicket()">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Valider & Ouvrir incident
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

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="modal-card bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 rounded-t-xl">
            <h3 class="text-lg font-semibold text-white">⚠️ Confirmer le rejet</h3>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Motif du rejet</label>
                <textarea id="rejectNote" rows="3" 
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                          placeholder="Veuillez préciser le motif du rejet..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors" onclick="hideRejectModal()">
                    Annuler
                </button>
                <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors" onclick="rejectTicket()">
                    Confirmer le rejet
                </button>
            </div>
        </div>
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

/* Animation pour le modal */
#rejectModal {
    backdrop-filter: blur(4px);
    transition: all 0.3s ease;
}

.modal-card {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Animation pour les champs de fichier */
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

/* Style pour le compteur de fichiers */
.badge-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.5rem;
    height: 1.5rem;
    padding: 0 0.25rem;
    background-color: #e5e7eb;
    color: #374151;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 9999px;
}

/* Responsive */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .grid-cols-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

@push('scripts')
<script>
// Compteurs pour les champs de fichier
let recommFileFieldCount = 0;
let execFileFieldCount = 0;

// Fonctions pour les fichiers de recommandation
function addRecommFileField() {
    const container = document.getElementById('recomm-files-container');
    const messageDiv = document.getElementById('no-recomm-files-message');
    
    if (messageDiv) {
        messageDiv.classList.add('hidden');
    }
    
    const fieldId = 'recomm-file-field-' + recommFileFieldCount;
    
    const div = document.createElement('div');
    div.id = fieldId;
    div.className = 'flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 animate-fade-in';
    div.innerHTML = `
        <div class="flex-1">
            <div class="relative">
                <input type="file" 
                       name="recomm_files[]" 
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
                onclick="removeFileField('${fieldId}', 'recomm-files-container', 'no-recomm-files-message')"
                class="text-red-600 hover:text-red-800 p-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    
    container.appendChild(div);
    recommFileFieldCount++;
}

// Fonctions pour les fichiers d'exécution
function addExecFileField() {
    const container = document.getElementById('exec-files-container');
    const messageDiv = document.getElementById('no-exec-files-message');
    
    if (messageDiv) {
        messageDiv.classList.add('hidden');
    }
    
    const fieldId = 'exec-file-field-' + execFileFieldCount;
    
    const div = document.createElement('div');
    div.id = fieldId;
    div.className = 'flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 animate-fade-in';
    div.innerHTML = `
        <div class="flex-1">
            <div class="relative">
                <input type="file" 
                       name="exec_files[]" 
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
                onclick="removeFileField('${fieldId}', 'exec-files-container', 'no-exec-files-message')"
                class="text-red-600 hover:text-red-800 p-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    
    container.appendChild(div);
    execFileFieldCount++;
}

// Fonction générique pour gérer la sélection de fichier
function handleFileSelect(input, fieldId) {
    const fileName = input.files[0]?.name || 'Choisir un fichier';
    const fileNameSpan = document.querySelector(`#${fieldId} .file-name`);
    if (fileNameSpan) {
        fileNameSpan.textContent = fileName;
        fileNameSpan.classList.add('text-gray-900', 'font-medium');
    }
}

// Fonction générique pour supprimer un champ de fichier
function removeFileField(fieldId, containerId, messageId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.remove();
    }
    
    // Vérifier s'il reste des champs
    const container = document.getElementById(containerId);
    const messageDiv = document.getElementById(messageId);
    const uploadedContainer = document.getElementById('uploaded-' + containerId);
    
    const hasUploadedFiles = uploadedContainer && uploadedContainer.children.length > 0;
    const hasNewFiles = container.children.length > 0;
    
    if (!hasUploadedFiles && !hasNewFiles && messageDiv) {
        messageDiv.classList.remove('hidden');
    }
}

// Fonctions existantes
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}

function rejectTicket() {
    const note = document.getElementById('rejectNote').value;
    if (!note) {
        alert('Veuillez saisir un motif de rejet');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('change.n2.reject', $ticket) }}';
    form.innerHTML = '@csrf<input type="hidden" name="note" value="' + note + '">';
    document.body.appendChild(form);
    form.submit();
}

function validateTicket() {
    if(confirm('Valider ce formulaire et ouvrir un rapport d\'incident ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('change.n2.validate', $ticket) }}';
        form.innerHTML = '@csrf';
        document.body.appendChild(form);
        form.submit();
    }
}

// Animation CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection