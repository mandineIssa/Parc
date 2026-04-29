{{-- resources/views/change/n3/form.blade.php --}}
@extends('layouts.app')

@section('title', $ticket->titre)
@section('header', 'Change Management - N+3')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête avec navigation -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-[#C8102E]">{{ $ticket->titre }}</h1>
            <p class="text-gray-600 mt-2">
                {{ $ticket->ticket_id }} · 
                @if($ticket->ticket_number)
                    <span class="text-gray-500 mr-2">N°: {{ $ticket->ticket_number }}</span>
                @endif
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                    @if($ticket->status === 'PENDING_N3') bg-yellow-100 text-yellow-800
                    @elseif($ticket->status === 'CLOSED') bg-red-50 text-[#C8102E] ring-1 ring-red-200
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $ticket->status_label }}
                </span>
            </p>
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
        <div class="bg-gradient-to-r from-[#C8102E] to-[#4a4a4a] px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-white">1. Informations générales</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">ID Système</label>
                    <div class="bg-white p-3 rounded-lg border border-gray-200 font-mono text-sm text-[#C8102E]">{{ $ticket->ticket_id }}</div>
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
        <div class="bg-gradient-to-r from-[#C8102E] to-[#4a4a4a] px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-white">2. Problématique</h2>
        </div>
        <div class="p-6">
            <label class="block text-sm font-medium text-gray-600 mb-2">Description</label>
            <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800">{{ $ticket->problematique }}</div>
        </div>
    </div>

    <!-- SECTION 3 - Analyse de l'impact -->
    <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200 mb-6">
        <div class="bg-gradient-to-r from-[#C8102E] to-[#4a4a4a] px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-white">3. Analyse de l'impact</h2>
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
        <div class="bg-gradient-to-r from-[#C8102E] to-[#4a4a4a] px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-white">Fichiers joints (N+1)</h2>
            <span class="ml-2 px-2 py-1 bg-gray-200 text-gray-700 rounded-full text-xs">{{ count($ticket->files) }} fichier(s)</span>
        </div>
        <div class="p-6">
            <div class="space-y-2">
                @foreach($ticket->files as $index => $file)
                    <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200 hover:border-red-200 transition-colors">
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
                           class="text-[#C8102E] hover:text-[#a00d24] p-2 transition-colors"
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
        <div class="bg-gradient-to-r from-[#C8102E] to-[#4a4a4a] px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-white">4. Recommandation (N+2)</h2>
        </div>
        <div class="p-6">
            <label class="block text-sm font-medium text-gray-600 mb-2">Recommandation</label>
            <div class="bg-white p-3 rounded-lg border border-gray-200 text-gray-800 mb-4">{{ $ticket->recommandation ?: '—' }}</div>
            
            @if($ticket->recomm_files && count($ticket->recomm_files) > 0)
                <label class="block text-sm font-medium text-gray-600 mb-2">Fiches de test</label>
                <div class="space-y-2">
                    @foreach($ticket->recomm_files as $index => $file)
                        <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200 hover:border-red-200 transition-colors">
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
                               class="text-[#C8102E] hover:text-[#a00d24] p-2 transition-colors"
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
        <div class="bg-gradient-to-r from-[#C8102E] to-[#4a4a4a] px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-white">5. Requête à exécuter (N+2)</h2>
        </div>
        <div class="p-6">
            <label class="block text-sm font-medium text-gray-600 mb-2">Script / SQL</label>
            <div class="bg-white p-3 rounded-lg border border-gray-200 font-mono text-sm text-gray-800">{{ $ticket->requete ?: '—' }}</div>
        </div>
    </div>

    <!-- SECTION 7 - Exécution & Résultat (N+2) -->
    <div class="form-card-readonly bg-gray-50 rounded-xl shadow-sm overflow-hidden border border-gray-200 mb-6">
        <div class="bg-gradient-to-r from-[#C8102E] to-[#4a4a4a] px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-white">7. Exécution & Résultat (N+2)</h2>
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
                                <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200 hover:border-red-200 transition-colors">
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
                                       class="text-[#C8102E] hover:text-[#a00d24] p-2 transition-colors"
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

    @if($ticket->n2_progress_entries && count($ticket->n2_progress_entries) > 0)
    <div class="form-card-readonly bg-slate-50 rounded-xl shadow-sm overflow-hidden border border-gray-200 mb-6">
        <div class="bg-slate-100 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-white">Notes complémentaires (N+2)</h2>
        </div>
        <div class="p-6 space-y-2">
            @foreach($ticket->n2_progress_entries as $entry)
                @if(is_array($entry) && !empty($entry['text']))
                    <div class="bg-white p-3 rounded-lg border border-gray-200 text-sm">
                        <span class="text-xs text-gray-500">{{ $entry['at'] ?? '' }}</span>
                        <p class="mt-1 whitespace-pre-wrap text-gray-800">{{ $entry['text'] }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    @if($ticket->n3_progress_entries && count($ticket->n3_progress_entries) > 0)
    <div class="form-card-readonly bg-red-50/50 rounded-xl shadow-sm overflow-hidden mb-6 border border-red-100">
        <div class="bg-gradient-to-r from-[#C8102E] to-[#4a4a4a] px-6 py-4 border-b border-red-200">
            <h2 class="text-lg font-semibold text-white">Commentaires de contrôle (N+3)</h2>
        </div>
        <div class="p-6 space-y-3">
            @foreach($ticket->n3_progress_entries as $entry)
                @if(is_array($entry) && !empty($entry['text']))
                    <div class="border border-gray-200 rounded-lg p-3 bg-white text-sm text-gray-800">
                        <span class="text-xs text-gray-500">{{ $entry['at'] ?? '' }} — {{ $entry['role'] ?? 'N3' }}</span>
                        <p class="mt-1 whitespace-pre-wrap">{{ $entry['text'] }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Approbation → retour N+2 -->
    @if($ticket->status === 'PENDING_N3')
        <div class="form-card-cloture bg-white rounded-xl shadow-md overflow-hidden mb-6 border-2 border-[#C8102E]/40">
            <div class="bg-gradient-to-r from-[#C8102E] to-[#4a4a4a] px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Approuver et renvoyer au N+2</h2>
            </div>
            <div class="p-6 space-y-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Commentaire final (optionnel, ajouté aux notes N+3)</label>
                <textarea id="approveNote" rows="2" 
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#C8102E] focus:ring-[#C8102E]"
                          placeholder="Synthèse du contrôle, conditions…"></textarea>
                <p class="text-xs text-gray-500">La demande repart chez le N+2 pour exécution / finalisation, puis envoi au N+1.</p>
            </div>
        </div>
    @endif

    @if($ticket->status === 'CLOSED')
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex">
                <svg class="w-5 h-5 text-[#C8102E] mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <div>
                    <p class="text-gray-900 font-medium">Ticket clôturé</p>
                    <p class="text-sm text-[#4a4a4a]">le {{ $ticket->closed_at ? $ticket->closed_at->format('d/m/Y H:i') : '' }}</p>
                    @if($ticket->close_note)
                        <p class="text-sm text-gray-700 mt-2"><strong>Note :</strong> {{ $ticket->close_note }}</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('change.ticket.pdf', $ticket) }}" target="_blank" rel="noopener"
               class="inline-flex items-center justify-center px-4 py-2 bg-[#C8102E] hover:bg-[#a00d24] text-white text-sm font-semibold rounded-lg shrink-0">
                Télécharger le PDF
            </a>
        </div>
    @endif

    <!-- HISTORIQUE COMPLET -->
    @if($ticket->history && count($ticket->history) > 0)
        <div class="form-card bg-white rounded-xl shadow-md overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-[#C8102E] to-[#4a4a4a] px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-white">HISTORIQUE COMPLET</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($ticket->history as $h)
                        <div class="flex">
                            <div class="flex-shrink-0 mr-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2"
                                     style="background: {{ $h['role'] === 'N1' ? 'rgba(200,16,46,0.08)' : ($h['role'] === 'N2' ? 'rgba(74,74,74,0.08)' : 'rgba(200,16,46,0.12)') }}; 
                                            border-color: {{ $h['role'] === 'N1' ? '#C8102E' : ($h['role'] === 'N2' ? '#4a4a4a' : '#a00d24') }};
                                            color: {{ $h['role'] === 'N1' ? '#C8102E' : ($h['role'] === 'N2' ? '#4a4a4a' : '#a00d24') }};">
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
            <button type="button" class="px-6 py-2 bg-[#C8102E] hover:bg-[#a00d24] text-white font-semibold rounded-lg transition-colors inline-flex items-center" onclick="approveReturnToN2()">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Approuver — retour N+2
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
function approveReturnToN2() {
    const note = document.getElementById('approveNote')?.value || '';
    if (!confirm('Approuver le contrôle et renvoyer la demande au N+2 pour finalisation ?')) {
        return;
    }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('change.n3.approve-n2', $ticket) }}";
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    const n = document.createElement('input');
    n.type = 'hidden';
    n.name = 'note';
    n.value = note;
    form.appendChild(n);
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
@endsection