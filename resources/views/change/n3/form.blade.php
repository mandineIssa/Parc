{{-- resources/views/change/n3/form.blade.php --}}
@extends('layouts.app')

@section('title', $ticket->titre)
@section('header', 'Change Management - N+3')

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
                    @if($ticket->status === 'PENDING_N3') bg-yellow-100 text-yellow-800
                    @elseif($ticket->status === 'CLOSED') bg-purple-100 text-purple-800
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

    <!-- SECTION 1 - Informations générales améliorée -->
    <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200 mb-6">
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

    <!-- SECTION 2 - Problématique -->
    <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200 mb-6">
        <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">2. Problématique</h2>
        </div>
        <div class="p-6">
            <label class="block text-sm font-medium text-gray-600 mb-2">Description</label>
            <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->problematique }}</div>
        </div>
    </div>

    <!-- SECTION 3 - Analyse de l'impact -->
    <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200 mb-6">
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

    <!-- FICHIERS UPLOADÉS PAR N+1 -->
    @if($ticket->files && count($ticket->files) > 0)
    <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200 mb-6">
        <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Fichiers joints (N+1)</h2>
            <span class="ml-2 px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs">{{ count($ticket->files) }} fichier(s)</span>
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

    <!-- SECTION 4 - Recommandation (N+2) -->
    <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200 mb-6">
        <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">4. Recommandation (N+2)</h2>
        </div>
        <div class="p-6">
            <label class="block text-sm font-medium text-gray-600 mb-2">Recommandation</label>
            <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800 mb-4">{{ $ticket->recommandation ?: '—' }}</div>
            
            @if($ticket->recomm_files && count($ticket->recomm_files) > 0)
                <label class="block text-sm font-medium text-gray-600 mb-2">Fiches de test</label>
                <div class="space-y-2">
                    @foreach($ticket->recomm_files as $index => $file)
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
        </div>
    </div>

    <!-- SECTION 5 - Requête à exécuter (N+2) -->
    <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200 mb-6">
        <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">5. Requête à exécuter (N+2)</h2>
        </div>
        <div class="p-6">
            <label class="block text-sm font-medium text-gray-600 mb-2">Script / SQL</label>
            <div class="bg-white p-3 rounded-lg border border-gray-200 font-mono text-sm text-gray-800">{{ $ticket->requete ?: '—' }}</div>
        </div>
    </div>

    <!-- SECTION 7 - Exécution & Résultat (N+2) -->
    <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200 mb-6">
        <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">7. Exécution & Résultat (N+2)</h2>
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
                    <label class="block text-sm font-medium text-gray-600 mb-2">Résultat</label>
                    <div class="bg-white p-3 rounded-lg border border-gray-200">{{ $ticket->resultat ?: '—' }}</div>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-2">Écarts / Anomalies</label>
                    <div class="bg-white p-3 rounded-lg border border-gray-200">{{ $ticket->ecarts ?: '—' }}</div>
                </div>
                
                @if($ticket->exec_files && count($ticket->exec_files) > 0)
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Screenshots / Logs</label>
                        <div class="space-y-2">
                            @foreach($ticket->exec_files as $index => $file)
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
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- RAPPORT D'INCIDENT (si présent) -->
    @if($ticket->incident_num && ($ticket->incident_description || $ticket->incident_actions))
    <div class="form-card-readonly bg-indigo-50 rounded-xl shadow-sm overflow-hidden border border-indigo-200 mb-6">
        <div class="bg-indigo-100 px-6 py-4 border-b border-indigo-200">
            <h2 class="text-lg font-semibold text-indigo-800">Rapport d'incident #{{ $ticket->incident_num }}</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @if($ticket->incident_description)
                <div>
                    <label class="block text-sm font-medium text-indigo-700 mb-2">Description de l'incident</label>
                    <div class="bg-white p-3 rounded-lg border border-indigo-200 text-gray-800">{{ $ticket->incident_description }}</div>
                </div>
                @endif
                
                @if($ticket->incident_actions)
                <div>
                    <label class="block text-sm font-medium text-indigo-700 mb-2">Actions correctives</label>
                    <div class="bg-white p-3 rounded-lg border border-indigo-200 text-gray-800">{{ $ticket->incident_actions }}</div>
                </div>
                @endif
                
                @if($ticket->incident_resolved_at || $ticket->incident_impact_residuel)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($ticket->incident_resolved_at)
                    <div>
                        <label class="block text-sm font-medium text-indigo-700 mb-2">Date de résolution</label>
                        <div class="bg-white p-3 rounded-lg border border-indigo-200">{{ $ticket->incident_resolved_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                    
                    @if($ticket->incident_impact_residuel)
                    <div>
                        <label class="block text-sm font-medium text-indigo-700 mb-2">Impact résiduel</label>
                        <div class="bg-white p-3 rounded-lg border border-indigo-200">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($ticket->incident_impact_residuel === 'Faible') bg-green-100 text-green-800
                                @elseif($ticket->incident_impact_residuel === 'Moyen') bg-yellow-100 text-yellow-800
                                @elseif($ticket->incident_impact_residuel === 'Élevé') bg-red-100 text-red-800
                                @endif">
                                {{ $ticket->incident_impact_residuel }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                
                @if($ticket->incident_files && count($ticket->incident_files) > 0)
                <div>
                    <label class="block text-sm font-medium text-indigo-700 mb-2">Pièces jointes incident</label>
                    <div class="space-y-2">
                        @foreach($ticket->incident_files as $index => $file)
                            <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-indigo-200">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ $file['name'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- CLOTURE -->
    @if($ticket->status === 'PENDING_N3')
        <div class="form-card-cloture bg-white rounded-xl shadow-md overflow-hidden mb-6 border-2 border-purple-200">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Clôture du ticket</h2>
            </div>
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Note de clôture</label>
                <textarea id="closeNote" rows="3" 
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                          placeholder="Observations, commentaires de clôture..."></textarea>
                <p class="text-xs text-gray-500 mt-2">Cette note sera visible dans l'historique et par tous les intervenants.</p>
            </div>
        </div>
    @endif

    @if($ticket->status === 'CLOSED')
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <svg class="w-5 h-5 text-purple-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <div>
                    <p class="text-purple-800 font-medium">Ticket clôturé</p>
                    <p class="text-sm text-purple-600">le {{ $ticket->closed_at ? $ticket->closed_at->format('d/m/Y H:i') : '' }}</p>
                    @if($ticket->close_note)
                        <p class="text-sm text-purple-700 mt-2"><strong>Note :</strong> {{ $ticket->close_note }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- HISTORIQUE COMPLET -->
    @if($ticket->history && count($ticket->history) > 0)
        <div class="form-card bg-white rounded-xl shadow-md overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">HISTORIQUE COMPLET</h2>
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
        <a href="{{ route('change.n3.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour
        </a>
        
        @if($ticket->status === 'PENDING_N3')
            <button type="button" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors inline-flex items-center" onclick="closeTicket()">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Clôturer le ticket
            </button>
        @endif
    </div>

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
.form-card-readonly,
.form-card-cloture {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.form-card:hover,
.form-card-readonly:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
}

.form-card-cloture:hover {
    border-color: #a78bfa;
    box-shadow: 0 0 15px rgba(139, 92, 246, 0.2);
}

/* Style pour les champs readonly */
.readonly-field {
    background-color: #f9fafb;
}

/* Style pour les liens de fichiers */
a:hover svg {
    transform: scale(1.1);
    transition: transform 0.2s ease;
}

/* Style pour les badges de compteur */
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
function closeTicket() {
    const note = document.getElementById('closeNote')?.value;
    if (!note) {
        alert('Veuillez saisir une note de clôture');
        return;
    }
    
    if(confirm('Clôturer ce ticket ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('change.n3.close', $ticket) }}";
        form.innerHTML = '@csrf<input type="hidden" name="note" value="' + note + '">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection