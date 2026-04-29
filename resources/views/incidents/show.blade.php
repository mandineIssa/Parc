{{-- resources/views/incidents/show.blade.php --}}
@extends('layouts.app')

@section('content')
@php
    $statut = $incident->statut;
    $isN1 = $user->isN1();
    $isN2 = $user->isN2();
    $isN3 = $user->isN3();
    $isAdmin = $user->isSuperAdmin();
    $isAuteur = $incident->created_by === $user->id;
    $criticiteColor = $incident->getCriticiteColorAttribute();
@endphp

<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">

    {{-- EN-TÊTE --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <a href="{{ route('incidents.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-red-600 transition-colors mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Tous les incidents
            </a>
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-mono text-xl font-bold text-red-600">{{ $incident->reference }}</span>
                @php
                    $statusBadge = [
                        'soumis' => 'bg-blue-50 text-blue-700',
                        'en_cours_n2' => 'bg-orange-50 text-orange-700',
                        'en_cours_n3' => 'bg-purple-50 text-purple-700',
                        'cloture' => 'bg-green-50 text-green-700',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $statusBadge[$statut] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ $incident->statut_label }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-{{ $criticiteColor }}-100 text-{{ $criticiteColor }}-700">
                    {{ $incident->criticite_label }}
                </span>
                @if($incident->bloquant)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-600 text-white">
                    🚨 BLOQUANT
                </span>
                @endif
            </div>
            <p class="text-sm text-gray-600 mt-1 font-medium">{{ $incident->sujet }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if(($isAuteur || $isAdmin) && in_array($statut, ['brouillon','soumis']))
            <a href="{{ route('incidents.edit', $incident) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                ✏️ Modifier
            </a>
            @endif

            @if($isAdmin || $isN3 || $isAuteur)
            <a href="{{ route('incidents.generer-pdf', $incident) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                📄 Télécharger Rapport ITIL
            </a>
            @endif

            @if($incident->pdf_fiche_path)
            <a href="{{ Storage::url($incident->pdf_fiche_path) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-2 border border-green-300 text-green-700 text-xs font-semibold rounded-lg hover:bg-green-50 transition-colors">
                📑 Voir dernier PDF
            </a>
            @endif
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
    <div class="mb-5 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-5 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 flex items-center gap-2 text-sm">
        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- WORKFLOW PROGRESSION --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
        <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">📊 Progression du workflow ITIL</h3>
        @php
            $steps = [
                ['label'=>'N+1 Helpdesk', 'key'=>'soumis', 'color'=>'blue', 'icon'=>'📋'],
                ['label'=>'N+2 Support', 'key'=>'en_cours_n2', 'color'=>'orange', 'icon'=>'🔧'],
                ['label'=>'N+3 Validateur', 'key'=>'en_cours_n3', 'color'=>'purple', 'icon'=>'✅'],
                ['label'=>'Clôturé', 'key'=>'cloture', 'color'=>'green', 'icon'=>'🏁'],
            ];
            $order = ['soumis'=>0, 'en_cours_n2'=>1, 'en_cours_n3'=>2, 'cloture'=>3];
            $current = $order[$statut] ?? 0;
        @endphp
        <div class="flex items-center">
            @foreach($steps as $i => $step)
            @php $done = $current > $i; $active = $current === $i; @endphp
            <div class="flex flex-col items-center {{ $i < count($steps)-1 ? 'flex-1' : '' }}">
                <div class="flex items-center w-full">
                    <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all
                        {{ $done ? 'bg-green-500 border-green-500' : ($active ? 'bg-red-600 border-red-600' : 'bg-white border-gray-300') }}">
                        @if($done)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <span class="text-sm font-bold {{ $active ? 'text-white' : 'text-gray-400' }}">{{ $step['icon'] }}</span>
                        @endif
                    </div>
                    @if($i < count($steps)-1)
                    <div class="flex-1 h-0.5 mx-1 {{ $current > $i ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                    @endif
                </div>
                <span class="text-[10px] font-semibold mt-1.5 {{ $done ? 'text-green-600' : ($active ? 'text-red-600' : 'text-gray-400') }} text-center">
                    {{ $step['label'] }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">

            {{-- SECTION DÉCLARATION COMPLÈTE --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-red-600 px-5 py-3 flex justify-between items-center">
                    <h2 class="text-white font-bold text-sm tracking-wide">📄 RAPPORT D'INCIDENT ITIL — DÉCLARATION</h2>
                    @if($incident->application_concernee)
                    <span class="text-white/80 text-xs">App: {{ $incident->application_concernee }}</span>
                    @endif
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4 pb-4 border-b border-gray-100">
                        <div>
                            <dt class="text-[10px] text-gray-400 font-bold uppercase">Date de survenue</dt>
                            <dd class="font-semibold text-gray-800">{{ $incident->date_incident->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] text-gray-400 font-bold uppercase">Heure de début</dt>
                            <dd class="font-semibold text-gray-800">{{ $incident->heure_debut ?: $incident->heure_incident ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] text-gray-400 font-bold uppercase">Environnement</dt>
                            <dd class="font-semibold text-gray-800">{{ $incident->environnement ?: 'Production' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] text-gray-400 font-bold uppercase">Type</dt>
                            <dd class="font-semibold text-gray-800">{{ $incident->type_label }}</dd>
                        </div>
                    </div>

                    <dl class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-3 text-sm">
                        <div><dt class="text-[10px] text-gray-400 font-bold uppercase">Déclarant</dt><dd class="text-gray-700">{{ $incident->utilisateur }}</dd></div>
                        <div><dt class="text-[10px] text-gray-400 font-bold uppercase">Entité</dt><dd class="text-gray-700">{{ $incident->entite }}</dd></div>
                        <div><dt class="text-[10px] text-gray-400 font-bold uppercase">Fonction</dt><dd class="text-gray-700">{{ $incident->fonction }}</dd></div>
                        <div><dt class="text-[10px] text-gray-400 font-bold uppercase">Canal de remontée</dt><dd class="text-gray-700">{{ $incident->point_entree_label }}</dd></div>
                        <div><dt class="text-[10px] text-gray-400 font-bold uppercase">Service impacté</dt><dd class="text-gray-700">{{ $incident->service_impacte ?: '—' }}</dd></div>
                        <div>
                            <dt class="text-[10px] text-gray-400 font-bold uppercase">Clients impactés</dt>
                            <dd class="text-gray-700">{{ $incident->nb_clients_impactes ?: '—' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-2">📝 Description détaillée</p>
                        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-line">{{ $incident->description }}</div>
                    </div>

                    @if($incident->impact_metier)
                    <div class="mt-4">
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-2">⚠️ Impact métier</p>
                        <div class="bg-orange-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-line">{{ $incident->impact_metier }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- BLOC N+1 --}}
            @include('incidents._bloc_niveau', [
                'niveau' => 'n1',
                'label' => '🔧 TRAITEMENT NIVEAU HELPDESK (N+1)',
                'color' => 'blue',
                'bgColor' => 'bg-blue-600',
                'ringColor' => 'focus:ring-blue-500',
                'inputBg' => 'bg-blue-50',
                'canTrait' => $isN1 || $isAdmin,
                'canUpload' => $isN1 || $isAdmin || $isAuteur,
                'isActive' => $statut === 'soumis',
                'alreadyDone' => !is_null($incident->n1_description_traitement),
                'formRoute' => 'incidents.traiter-n1',
                'statutOptions' => ['cloture' => '✅ Clôturer', 'transfere' => '⬆️ Transférer au N+2'],
                'statutField' => 'n1_statut',
                'fieldPrefix' => 'n1',
                'user' => $incident->n1User,
                'date' => $incident->n1_date_traitement,
                'intervenants' => $incident->n1_autres_intervenants,
                'description' => $incident->n1_description_traitement,
                'solutions' => $incident->n1_solutions_envisagees,
                'pdfPath' => $incident->n1_pdf_path,
                'inactive' => false,
                'incident' => $incident,
            ])

            {{-- BLOC N+2 --}}
            @include('incidents._bloc_niveau', [
                'niveau' => 'n2',
                'label' => '🛠️ TRAITEMENT SUPPORT NIVEAU 2 (N+2)',
                'color' => 'orange',
                'bgColor' => 'bg-orange-500',
                'ringColor' => 'focus:ring-orange-500',
                'inputBg' => 'bg-orange-50',
                'canTrait' => $isN2 || $isAdmin,
                'canUpload' => $isN2 || $isAdmin,
                'isActive' => $statut === 'en_cours_n2',
                'alreadyDone' => !is_null($incident->n2_description_traitement),
                'formRoute' => 'incidents.traiter-n2',
                'statutOptions' => ['cloture' => '✅ Clôturer', 'ouverture_ticket' => '🎫 Ouvrir ticket → N+3'],
                'statutField' => 'n2_statut',
                'fieldPrefix' => 'n2',
                'user' => $incident->n2User,
                'date' => $incident->n2_date_traitement,
                'intervenants' => $incident->n2_autres_intervenants,
                'description' => $incident->n2_description_traitement,
                'solutions' => $incident->n2_solutions_envisagees,
                'pdfPath' => $incident->n2_pdf_path,
                'inactive' => !in_array($statut, ['en_cours_n2','en_cours_n3','cloture']),
                'incident' => $incident,
            ])

            {{-- BLOC N+3 --}}
            @include('incidents._bloc_niveau', [
                'niveau' => 'n3',
                'label' => '✅ TRAITEMENT VALIDATEUR (N+3)',
                'color' => 'purple',
                'bgColor' => 'bg-purple-600',
                'ringColor' => 'focus:ring-purple-500',
                'inputBg' => 'bg-purple-50',
                'canTrait' => $isN3 || $isAdmin,
                'canUpload' => $isN3 || $isAdmin,
                'isActive' => $statut === 'en_cours_n3',
                'alreadyDone' => !is_null($incident->n3_description_traitement),
                'formRoute' => 'incidents.traiter-n3',
                'statutOptions' => ['cloture' => '✅ Clôturer définitivement', 'escalade' => '📈 Escalade'],
                'statutField' => 'n3_statut',
                'fieldPrefix' => 'n3',
                'user' => $incident->n3User,
                'date' => $incident->n3_date_traitement,
                'intervenants' => $incident->n3_autres_intervenants,
                'description' => $incident->n3_description_traitement,
                'solutions' => $incident->n3_solutions_envisagees,
                'pdfPath' => $incident->n3_pdf_path,
                'inactive' => !in_array($statut, ['en_cours_n3','cloture']),
                'incident' => $incident,
            ])

            {{-- ANALYSE ET CAUSE RACINE (si disponible) --}}
            @if($incident->cause_racine || $incident->actions_correctives || $incident->actions_preventives)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gray-800 px-5 py-3">
                    <h2 class="text-white font-bold text-sm tracking-wide">🔍 ANALYSE ET ACTIONS</h2>
                </div>
                <div class="p-5 space-y-4">
                    @if($incident->cause_racine)
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Cause racine (RCA)</p>
                        <div class="bg-red-50 rounded-lg p-3 text-sm text-gray-700">{{ $incident->cause_racine }}</div>
                    </div>
                    @endif
                    @if($incident->actions_correctives)
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Actions correctives immédiates</p>
                        <div class="bg-green-50 rounded-lg p-3 text-sm text-gray-700">
                            <ul class="list-disc list-inside">
                                @foreach($incident->actions_correctives as $action)
                                <li>{{ $action }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                    @if($incident->actions_preventives)
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Actions préventives (CAPA)</p>
                        <div class="bg-blue-50 rounded-lg p-3 text-sm text-gray-700">
                            <ul class="list-disc list-inside">
                                @foreach($incident->actions_preventives as $action)
                                <li>{{ $action }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- COLONNE DROITE --}}
        <div class="space-y-4">

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">📊 Informations SLA</h3>
                <dl class="space-y-2.5 text-xs">
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-400 font-medium">Heure résolution</dt>
                        <dd class="font-semibold text-gray-800">{{ $incident->heure_resolution ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-400 font-medium">Durée</dt>
                        <dd class="font-semibold text-gray-800">{{ $incident->duree_incident ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-400 font-medium">Temps résolution</dt>
                        <dd class="font-semibold text-gray-800">{{ $incident->temps_resolution ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-400 font-medium">SLA respecté</dt>
                        <dd class="font-semibold {{ $incident->sla_respecte ? 'text-green-600' : 'text-red-600' }}">
                            {{ $incident->sla_respecte ? '✅ Oui' : '❌ Non' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">👥 Informations</h3>
                <dl class="space-y-2.5 text-xs">
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-400 font-medium">Créé par</dt>
                        <dd class="font-semibold text-gray-800">{{ $incident->createdBy?->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-400 font-medium">Créé le</dt>
                        <dd class="font-medium text-gray-700">{{ $incident->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-400 font-medium">Clôturé le</dt>
                        <dd class="font-medium text-gray-700">{{ $incident->date_cloture?->format('d/m/Y H:i') ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-400 font-medium">Validé par</dt>
                        <dd class="font-medium text-gray-700">{{ $incident->validePar?->name ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-gray-400 font-medium">Votre rôle</dt>
                        <dd>
                            @if($user->role_change)
                            <span class="font-bold px-2 py-0.5 rounded
                                {{ $isN1 ? 'bg-blue-100 text-blue-700' : ($isN2 ? 'bg-orange-100 text-orange-700' : 'bg-purple-100 text-purple-700') }}">
                                {{ $user->change_role_label }}
                            </span>
                            @else
                            <span class="text-gray-400">{{ $user->role }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            @if($incident->commentaires_cloture)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">💬 Commentaires de clôture</h3>
                <p class="text-sm text-gray-700">{{ $incident->commentaires_cloture }}</p>
            </div>
            @endif

            @if($incident->n1_pdf_path || $incident->n2_pdf_path || $incident->n3_pdf_path)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">📎 Documents joints</h3>
                <div class="space-y-2">
                    @foreach(['n1'=>['label'=>'Rapport N+1','color'=>'blue'],'n2'=>['label'=>'Rapport N+2','color'=>'orange'],'n3'=>['label'=>'Rapport N+3','color'=>'purple']] as $n=>$info)
                    @if($incident->{"{$n}_pdf_path"})
                    <a href="{{ Storage::url($incident->{"{$n}_pdf_path"}) }}" target="_blank"
                       class="flex items-center gap-2 p-2.5 rounded-lg border border-{{ $info['color'] }}-200 bg-{{ $info['color'] }}-50 hover:bg-{{ $info['color'] }}-100 transition-colors text-xs font-semibold text-{{ $info['color'] }}-700">
                        📄 {{ $info['label'] }}
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- HISTORIQUE --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">📜 Journal d'activité</h3>
                </div>
                <div class="p-4 max-h-96 overflow-y-auto">
                    @forelse($incident->historiques as $histo)
                    <div class="flex gap-2.5 mb-4 last:mb-0">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center mt-0.5
                            {{ $histo->niveau==='N1' ? 'bg-blue-100' : ($histo->niveau==='N2' ? 'bg-orange-100' : 'bg-purple-100') }}">
                            <span class="text-[9px] font-bold {{ $histo->niveau==='N1' ? 'text-blue-700' : ($histo->niveau==='N2' ? 'text-orange-700' : 'text-purple-700') }}">
                                {{ $histo->niveau ?? '—' }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-800">{{ $histo->action_label }}</p>
                            @if($histo->commentaire)
                            <p class="text-[11px] text-gray-500 mt-0.5">{{ $histo->commentaire }}</p>
                            @endif
                            <p class="text-[10px] text-gray-400 mt-1">{{ $histo->user?->name ?? 'Système' }} · {{ $histo->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 italic">Aucune activité enregistrée.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection