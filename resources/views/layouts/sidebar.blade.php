{{-- resources/views/layouts/sidebar.blade.php --}}
<aside id="sidebar" class="fixed top-16 left-0 h-[calc(100vh-64px)] w-64 bg-gradient-to-b from-white to-gray-50 border-r border-gray-200 shadow-xl transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out z-40 flex flex-col">

    <nav class="flex-1 overflow-y-auto py-4 custom-scrollbar" id="sidebar-nav">

        {{-- ════════════════════════════════════════
             SECTION : RAPPORTS
        ════════════════════════════════════════ --}}
        <div class="sidebar-section" data-section="rapports">
            <button class="section-header group" data-section="rapports">
                <div class="flex items-center gap-3">
                    <svg class="s-icon text-purple-500 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="font-medium">Rapports</span>
                </div>
                <svg class="s-arrow transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="section-body">
                <a href="{{ route('reports.index') }}" class="s-item {{ request()->routeIs('reports.index') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Vue d'ensemble</span>
                </a>
                <a href="{{ route('reports.equipment') }}" class="s-item {{ request()->routeIs('reports.equipment') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Équipements</span>
                </a>
                <a href="{{ route('reports.financial') }}" class="s-item {{ request()->routeIs('reports.financial') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Financier</span>
                </a>
                @if(class_exists('App\Models\Maintenance'))
                <a href="{{ route('reports.maintenance') }}" class="s-item {{ request()->routeIs('reports.maintenance') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Maintenance</span>
                </a>
                @endif
                <div class="s-divider"></div>
                <a href="{{ route('reports.import.equipment') }}" class="s-item {{ request()->routeIs('reports.import.*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Importer</span>
                </a>
                <a href="{{ route('reports.export.equipment') }}" class="s-item {{ request()->routeIs('reports.export.*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Exporter</span>
                </a>
            </div>
        </div>

        {{-- ════════════════════════════════════════
             SECTION : ÉQUIPEMENTS
        ════════════════════════════════════════ --}}
        <div class="sidebar-section" data-section="equipements">
            <button class="section-header group" data-section="equipements">
                <div class="flex items-center gap-3">
                    <svg class="s-icon text-blue-500 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="font-medium">Équipements</span>
                </div>
                <svg class="s-arrow transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="section-body">
                <a href="{{ route('equipment.index') }}" class="s-item {{ request()->routeIs('equipment.index') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Tous les équipements</span>
                </a>
                <a href="{{ route('equipment.import.form') }}" class="s-item {{ request()->routeIs('equipment.import.*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Import</span>
                </a>
                <a href="{{ route('equipment.export') }}" class="s-item {{ request()->routeIs('equipment.export') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Export</span>
                </a>
            </div>
        </div>

        {{-- ════════════════════════════════════════
             SECTION : STOCKS — CELER
        ════════════════════════════════════════ --}}
        <div class="sidebar-section" data-section="celer">
            <button class="section-header group" data-section="celer">
                <div class="flex items-center gap-3">
                    <svg class="s-icon text-cyan-500 group-hover:text-cyan-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <span class="font-medium">Stocks — CELER</span>
                </div>
                <svg class="s-arrow transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="section-body">
                <a href="{{ route('dashboard.celer-informatique') }}" class="s-item {{ request()->routeIs('dashboard.celer-informatique') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Informatique</span>
                </a>
                <a href="{{ route('dashboard.celer-reseau') }}" class="s-item {{ request()->routeIs('dashboard.celer-reseau') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Réseau</span>
                </a>
                <a href="{{ route('dashboard.celer-electronique') }}" class="s-item {{ request()->routeIs('dashboard.celer-electronique') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Électronique</span>
                </a>
            </div>
        </div>

        {{-- ════════════════════════════════════════
             SECTION : STOCKS — DECELER
        ════════════════════════════════════════ --}}
        <div class="sidebar-section" data-section="deceler">
            <button class="section-header group" data-section="deceler">
                <div class="flex items-center gap-3">
                    <svg class="s-icon text-emerald-500 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span class="font-medium">Stocks — DECELER</span>
                </div>
                <svg class="s-arrow transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="section-body">
                <a href="{{ route('dashboard.deceler-informatique') }}" class="s-item {{ request()->routeIs('dashboard.deceler-informatique') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Informatique</span>
                </a>
                <a href="{{ route('dashboard.deceler-reseau') }}" class="s-item {{ request()->routeIs('dashboard.deceler-reseau') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Réseau</span>
                </a>
                <a href="{{ route('dashboard.deceler-electronique') }}" class="s-item {{ request()->routeIs('dashboard.deceler-electronique') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Électronique</span>
                </a>
            </div>
        </div>

        {{-- ════════════════════════════════════════
             SECTION : GESTION
        ════════════════════════════════════════ --}}
        <div class="sidebar-section" data-section="gestion">
            <button class="section-header group" data-section="gestion">
                <div class="flex items-center gap-3">
                    <svg class="s-icon text-amber-500 group-hover:text-amber-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span class="font-medium">Gestion</span>
                </div>
                <svg class="s-arrow transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="section-body">
                <a href="{{ route('parc.index') }}" class="s-item {{ request()->routeIs('parc.index') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Parc</span>
                </a>
                <a href="{{ route('parc.reaffectations.index') }}" class="s-item {{ request()->routeIs('parc.reaffectations.*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Historique Réaffectations</span>
                </a>
                <a href="{{ route('maintenance.index') }}" class="s-item {{ request()->routeIs('maintenance.*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Maintenances</span>
                </a>
                @if(isset($approval) && $approval)
                    <a href="{{ route('approvals.attachments.show', $approval->id) }}" class="s-item {{ request()->routeIs('approvals.attachments.show') ? 's-active' : '' }}">
                        <span class="s-dot"></span>
                        <span>Pièces jointes Approbation</span>
                    </a>
                @else
                    <div class="s-item opacity-40 cursor-not-allowed">
                        <span class="s-dot"></span>
                        <span>Pièces jointes Approbation</span>
                    </div>
                @endif
                <a href="{{ route('hors-service.index') }}" class="s-item {{ request()->routeIs('hors-service.*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Hors Service</span>
                </a>
                <a href="{{ route('perdu.index') }}" class="s-item {{ request()->routeIs('perdu.*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Perdu</span>
                </a>
            </div>
        </div>

        {{-- ════════════════════════════════════════
             SECTION : EOD SUIVI
        ════════════════════════════════════════ --}}
        <div class="sidebar-section" data-section="eod">
            <button class="section-header group" data-section="eod">
                <div class="flex items-center gap-3">
                    <svg class="s-icon text-orange-500 group-hover:text-orange-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">EOD Suivi</span>
                </div>
                <svg class="s-arrow transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="section-body">
                @php $user = auth()->user(); $hasChangeRole = $user && $user->role_change; @endphp
                @if($hasChangeRole)
                    @if($user->role_change === 'N1')
                        <a href="{{ route('eod.n1.index') }}" class="s-item {{ request()->routeIs('eod.n1.index') ? 's-active' : '' }}">
                            <span class="s-dot"></span>
                            <span>Mes fiches EOD</span>
                        </a>
                        <a href="{{ route('eod.n1.create') }}" class="s-item {{ request()->routeIs('eod.n1.create') ? 's-active' : '' }}">
                            <span class="s-dot"></span>
                            <span>Nouvelle fiche EOD</span>
                        </a>
                    @elseif($user->role_change === 'N2')
                        <a href="{{ route('eod.n2.index') }}" class="s-item {{ request()->routeIs('eod.n2.*') ? 's-active' : '' }}">
                            <span class="s-dot"></span>
                            <span>Fiches à valider</span>
                        </a>
                    @elseif($user->role_change === 'N3')
                        <a href="{{ route('eod.n3.index') }}" class="s-item {{ request()->routeIs('eod.n3.*') ? 's-active' : '' }}">
                            <span class="s-dot"></span>
                            <span>Supervision EOD</span>
                        </a>
                    @endif
                @else
                    <div class="px-4 py-2 text-xs text-gray-400 italic">Sélectionnez un rôle Change Management</div>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════
             SECTION : INFRASTRUCTURE IT
        ════════════════════════════════════════ --}}
        <div class="sidebar-section" data-section="infrastructure">
            <button class="section-header group" data-section="infrastructure">
                <div class="flex items-center gap-3">
                    <svg class="s-icon text-rose-500 group-hover:text-rose-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    <span class="font-medium">Infrastructure IT</span>
                </div>
                <svg class="s-arrow transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="section-body">
                <div class="s-group-label">Mots de Passe</div>
                <a href="{{ route('passwords.index') }}" class="s-item {{ request()->routeIs('passwords.index') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Tous les mots de passe</span>
                </a>
                <a href="{{ route('passwords.create') }}" class="s-item {{ request()->routeIs('passwords.create') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Nouvelle fiche</span>
                </a>
                <div class="s-divider"></div>
                <div class="s-group-label">Plan d'Adressage</div>
                <a href="{{ route('network.index') }}" class="s-item {{ request()->routeIs('network.index') && !request('type') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Vue d'ensemble</span>
                </a>
                <a href="{{ route('network.index', ['type' => 'plan_adressage']) }}" class="s-item {{ request('type') === 'plan_adressage' ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Plans VLAN</span>
                </a>
                <a href="{{ route('network.index', ['type' => 'branchement_local']) }}" class="s-item {{ request('type') === 'branchement_local' ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Branchements locaux</span>
                </a>
                <a href="{{ route('network.create') }}" class="s-item {{ request()->routeIs('network.create') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Ajouter une entrée</span>
                </a>
                <div class="s-divider"></div>
                <div class="s-group-label">Licences</div>
                <a href="{{ route('licences.index') }}" class="s-item {{ request()->routeIs('licences.index') && !request('type') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Toutes les licences</span>
                </a>
                @foreach(['Fortinet' => '🛡', 'FAI' => '🌐', 'Certificat' => '🔐', 'Office365' => '📧'] as $type => $icon)
                <a href="{{ route('licences.index', ['type' => $type]) }}" class="s-item {{ request('type') === $type ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>{{ $icon }} {{ $type }}</span>
                </a>
                @endforeach
                <a href="{{ route('licences.create') }}" class="s-item {{ request()->routeIs('licences.create') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Nouvelle licence</span>
                </a>
            </div>
        </div>

        {{-- ════════════════════════════════════════
             SECTION : CHANGE MANAGEMENT
        ════════════════════════════════════════ --}}
        <div class="sidebar-section" data-section="change">
            <button class="section-header group" data-section="change">
                <div class="flex items-center gap-3">
                    <svg class="s-icon text-indigo-500 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span class="font-medium">Change Management</span>
                </div>
                <svg class="s-arrow transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="section-body">
                @php $user = auth()->user(); $hasChangeRole = $user && $user->role_change; @endphp
                @if($hasChangeRole)
                    @if($user->role_change === 'N1')
                        <a href="{{ route('change.n1.index') }}" class="s-item {{ request()->routeIs('change.n1.*') ? 's-active' : '' }}">
                            <span class="s-dot"></span>
                            <span>N+1 — Mes formulaires</span>
                        </a>
                        <a href="{{ route('change.n1.create') }}" class="s-item {{ request()->routeIs('change.n1.create') ? 's-active' : '' }}">
                            <span class="s-dot"></span>
                            <span>Nouveau formulaire</span>
                        </a>
                    @elseif($user->role_change === 'N2')
                        <a href="{{ route('change.n2.index') }}" class="s-item {{ request()->routeIs('change.n2.*') ? 's-active' : '' }}">
                            <span class="s-dot"></span>
                            <span>N+2 — Formulaires à traiter</span>
                        </a>
                    @elseif($user->role_change === 'N3')
                        <a href="{{ route('change.n3.index') }}" class="s-item {{ request()->routeIs('change.n3.*') ? 's-active' : '' }}">
                            <span class="s-dot"></span>
                            <span>N+3 — Validation finale</span>
                        </a>
                    @endif
                    <div class="s-divider"></div>
                    <form method="POST" action="{{ route('change.role.clear') }}" class="w-full">
                        @csrf
                        <button type="submit" class="s-item w-full">
                            <span class="s-dot"></span>
                            <span>Changer de rôle (session)</span>
                        </button>
                    </form>
                    <div class="px-4 py-2 mt-1">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                              style="background:{{ $user->role_change === 'N1' ? 'rgba(59,130,246,0.1)' : ($user->role_change === 'N2' ? 'rgba(16,185,129,0.1)' : 'rgba(139,92,246,0.1)') }};color:{{ $user->role_change === 'N1' ? '#3b82f6' : ($user->role_change === 'N2' ? '#10b981' : '#8b5cf6') }}">
                            Rôle actuel : {{ $user->role_change === 'N1' ? 'N+1' : ($user->role_change === 'N2' ? 'N+2' : 'N+3') }}
                        </span>
                    </div>
                @else
                    <a href="{{ route('change.role') }}" class="s-item {{ request()->routeIs('change.role') ? 's-active' : '' }}">
                        <span class="s-dot"></span>
                        <span>Sélectionner un rôle</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════
             SECTION : CONTRÔLES IT
        ════════════════════════════════════════ --}}
        <div class="sidebar-section" data-section="controls">
            <button class="section-header group" data-section="controls">
                <div class="flex items-center gap-3">
                    <svg class="s-icon text-red-500 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="font-medium">Contrôles IT</span>
                </div>
                <svg class="s-arrow transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="section-body">
                <a href="{{ route('controls.dashboard') }}" class="s-item {{ request()->routeIs('controls.dashboard') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Dashboard Contrôles</span>
                </a>
                <a href="{{ route('controls.index') }}" class="s-item {{ request()->routeIs('controls.index') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Tous les contrôles</span>
                </a>
                <a href="{{ route('controls.tasks.index') }}" class="s-item {{ request()->routeIs('controls.tasks.*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span class="flex-1">Mes tâches</span>
                    @php
                        $nbTaches = 0;
                        try {
                            if (class_exists('\App\Models\ControlTask')) {
                                $nbTaches = \App\Models\ControlTask::where('assigned_to', auth()->id())
                                    ->whereIn('status', ['pending', 'in_progress'])
                                    ->count();
                            }
                        } catch (\Exception $e) {}
                    @endphp
                    @if($nbTaches > 0)
                        <span class="s-badge">{{ $nbTaches }}</span>
                    @endif
                </a>
                <div class="s-divider"></div>
                <div class="s-group-label">Planification</div>
                <a href="{{ route('controls.index') }}" class="s-item {{ request()->routeIs('controls.index') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Toutes les planifications</span>
                </a>
                <a href="{{ route('controls.create') }}" class="s-item {{ request()->routeIs('controls.create') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Nouveau contrôle</span>
                </a>
                @if(auth()->user() && auth()->user()->role === 'super_admin')
                    <div class="s-divider"></div>
                    <div class="s-group-label">Configuration</div>
                    <a href="{{ route('controls.templates.index') }}" class="s-item {{ request()->routeIs('controls.templates.*') ? 's-active' : '' }}">
                        <span class="s-dot"></span>
                        <span>Templates de rapport</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════
             SECTION : CONFIGURATION
        ════════════════════════════════════════ --}}
        <div class="sidebar-section" data-section="configuration">
            <button class="section-header group" data-section="configuration">
                <div class="flex items-center gap-3">
                    <svg class="s-icon text-gray-500 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="font-medium">Configuration</span>
                </div>
                <svg class="s-arrow transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="section-body">
                @auth
                <a href="{{ route('dashboard') }}" class="s-item {{ request()->routeIs('dashboard*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Dashboard</span>
                </a>
                <div class="s-divider"></div>
                @endauth
                <a href="{{ route('agencies.index') }}" class="s-item {{ request()->routeIs('agencies.*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Agences</span>
                </a>
                <a href="{{ route('categories.index') }}" class="s-item {{ request()->routeIs('categories.*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Catégories</span>
                </a>
                <a href="{{ route('suppliers.index') }}" class="s-item {{ request()->routeIs('suppliers.*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Fournisseurs</span>
                </a>
                <a href="{{ route('users.index') }}" class="s-item {{ request()->routeIs('admin.users.*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Administration</span>
                </a>
                <div class="s-divider"></div>
                <a href="{{ route('audits.index') }}" class="s-item {{ request()->routeIs('audits.*') ? 's-active' : '' }}">
                    <span class="s-dot"></span>
                    <span>Journal d'activité</span>
                </a>
            </div>
        </div>

    </nav>

    {{-- VERSION --}}
    <div class="flex-shrink-0 px-4 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-medium">Gestion Parc Informatique</p>
                <p class="text-[11px] text-gray-400 mt-1">v1.0.0 · © {{ date('Y') }}</p>
            </div>
            <div class="text-right">
                <span class="text-[11px] font-semibold text-red-600">COFINA</span>
            </div>
        </div>
    </div>

</aside>

{{-- Overlay mobile --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden lg:hidden z-30 transition-all duration-300"></div>

<style>
/* ── Custom Scrollbar ── */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #C8102E;
}

/* ── Section entière ── */
.sidebar-section {
    border-bottom: 1px solid #f3f4f6;
    margin-bottom: 2px;
}

/* ── Header cliquable de la section ── */
.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 12px 18px;
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: #1f2937;
    text-align: left;
    transition: all 0.2s ease;
}
.section-header:hover {
    background: linear-gradient(90deg, #fef2f2 0%, #fff 100%);
}
.section-header.is-active {
    background: linear-gradient(90deg, #fee2e2 0%, #fff 100%);
    color: #C8102E;
    border-left: 3px solid #C8102E;
}
.section-header.is-active .s-icon {
    color: #C8102E !important;
}

/* ── Corps de la section (items) ── */
.section-body {
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: #fefefe;
}
.section-body.open {
    max-height: 800px;
}

/* ── Sections cachées ── */
.sidebar-section.hidden-section {
    display: none;
}

/* ── Items ── */
.s-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 18px 8px 42px;
    font-size: 12.5px;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    background: transparent;
    cursor: pointer;
    width: 100%;
    position: relative;
}
.s-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 0;
    background: #C8102E;
    transition: width 0.2s ease;
}
.s-item:hover::before,
.s-active::before {
    width: 3px;
}
.s-item:hover {
    background: #fef2f2;
    color: #C8102E;
    padding-left: 42px;
}
.s-active {
    color: #C8102E !important;
    font-weight: 600;
    background: #fef2f2;
}

.s-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
    opacity: 0.4;
    transition: opacity 0.2s ease;
}
.s-item:hover .s-dot,
.s-active .s-dot {
    opacity: 1;
    transform: scale(1.2);
}

.s-group-label {
    font-size: 9.5px;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 10px 18px 4px 42px;
}

.s-divider {
    height: 1px;
    background: linear-gradient(90deg, #e5e7eb 0%, #e5e7eb 50%, transparent 100%);
    margin: 6px 18px;
    border: none;
}

.s-badge {
    background: #C8102E;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 12px;
    line-height: 1.4;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.s-icon {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    color: #9ca3af;
    transition: all 0.2s ease;
}
.section-header:hover .s-icon {
    transform: scale(1.05);
}

.s-arrow {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
    color: #9ca3af;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.s-arrow.open {
    transform: rotate(180deg);
    color: #C8102E;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Mobile ── */
    var sidebar   = document.getElementById('sidebar');
    var overlay   = document.getElementById('sidebar-overlay');
    var toggleBtn = document.getElementById('sidebar-toggle');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar();
        });
    }
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }
    sidebar.querySelectorAll('a').forEach(function (l) {
        l.addEventListener('click', function () {
            if (window.innerWidth < 1024) closeSidebar();
        });
    });

    /* ══════════════════════════════════════════════════════
       LOGIQUE PRINCIPALE
       – Déterminer la section active selon l'URL courante
       – Masquer toutes les autres sections
       – Ouvrir le corps de la section active
    ══════════════════════════════════════════════════════ */

    var path = window.location.pathname;

    var sectionMap = [
        { section: 'rapports',       patterns: ['reports', 'rapports'] },
        { section: 'equipements',    patterns: ['equipment'] },
        { section: 'celer',          patterns: ['celer-informatique', 'celer-reseau', 'celer-electronique'] },
        { section: 'deceler',        patterns: ['deceler-informatique', 'deceler-reseau', 'deceler-electronique'] },
        { section: 'gestion',        patterns: ['parc', 'maintenance', 'hors-service', 'perdu', 'approvals'] },
        { section: 'eod',            patterns: ['eod'] },
        { section: 'infrastructure', patterns: ['passwords', 'network', 'licences'] },
        { section: 'change',         patterns: ['change'] },
        { section: 'controls',       patterns: ['controls'] },
        { section: 'configuration',  patterns: ['dashboard', 'agencies', 'categories', 'suppliers', 'users', 'audits'] },
    ];

    var activeSection = null;
    for (var i = 0; i < sectionMap.length; i++) {
        var entry = sectionMap[i];
        for (var j = 0; j < entry.patterns.length; j++) {
            if (path.includes(entry.patterns[j])) {
                activeSection = entry.section;
                break;
            }
        }
        if (activeSection) break;
    }

    document.querySelectorAll('.sidebar-section').forEach(function (section) {
        var key    = section.dataset.section;
        var body   = section.querySelector('.section-body');
        var arrow  = section.querySelector('.s-arrow');
        var header = section.querySelector('.section-header');

        if (activeSection && key !== activeSection) {
            section.classList.add('hidden-section');
        } else if (activeSection && key === activeSection) {
            if (body) {
                body.classList.add('open');
                body.style.maxHeight = body.scrollHeight + 'px';
            }
            if (arrow) arrow.classList.add('open');
            if (header) header.classList.add('is-active');
        }
    });

    /* ── Clic sur le header : afficher toutes les sections + replier ── */
    document.querySelectorAll('.section-header').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var key     = btn.dataset.section;
            var section = document.querySelector('.sidebar-section[data-section="' + key + '"]');
            var body    = section ? section.querySelector('.section-body') : null;
            var arrow   = btn.querySelector('.s-arrow');

            var allSections = document.querySelectorAll('.sidebar-section');
            var allBodies = document.querySelectorAll('.section-body');
            var allArrows = document.querySelectorAll('.s-arrow');
            var allHeaders = document.querySelectorAll('.section-header');

            var isAlreadyOpen = body && body.classList.contains('open');

            if (isAlreadyOpen) {
                // Fermer cette section si déjà ouverte
                body.classList.remove('open');
                body.style.maxHeight = '0';
                if (arrow) arrow.classList.remove('open');
                btn.classList.remove('is-active');
            } else {
                // Révéler toutes les sections
                allSections.forEach(function (s) {
                    s.classList.remove('hidden-section');
                });

                // Fermer tous les autres corps
                allBodies.forEach(function (b) {
                    b.classList.remove('open');
                    b.style.maxHeight = '0';
                });
                allArrows.forEach(function (a) {
                    a.classList.remove('open');
                });
                allHeaders.forEach(function (h) {
                    h.classList.remove('is-active');
                });

                // Ouvrir celui cliqué
                if (body) {
                    body.classList.add('open');
                    body.style.maxHeight = body.scrollHeight + 'px';
                }
                if (arrow) arrow.classList.add('open');
                btn.classList.add('is-active');
            }
        });
    });

}); // fin DOMContentLoaded
</script>