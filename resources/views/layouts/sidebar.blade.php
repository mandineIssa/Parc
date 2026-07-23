{{-- Sidebar — structure PARC + modules (sans icônes) --}}
<aside id="sidebar" class="fixed top-16 left-0 h-[calc(100vh-64px)] w-64 bg-gradient-to-b from-white to-gray-50 border-r border-gray-200 shadow-xl transform -translate-x-full lg:translate-x-0 transition-all duration-300 ease-in-out z-40 flex flex-col">

        @php
            $auditsPostesOpen = request()->routeIs('audits-postes.*');
            $rapportsMenuOpen = request()->routeIs('reports.*');
            $gestionMenuOpen = request()->routeIs('parc.*', 'maintenance.*', 'hors-service.*', 'perdu.*', 'approvals.*');
            $equipementsMenuOpen = request()->routeIs('equipment.*');
            $stocksMenuOpen = request()->routeIs('dashboard.celer-*') || request()->routeIs('dashboard.deceler-*');
            $stockBranchCelerOpen = request()->routeIs('dashboard.celer-*');
            $stockBranchDecelerOpen = request()->routeIs('dashboard.deceler-*');
            $parcSectionOpen = $rapportsMenuOpen || $gestionMenuOpen || $equipementsMenuOpen || $stocksMenuOpen
                || request()->routeIs('documentation.*');
            $configurationSectionOpen = $auditsPostesOpen
                || request()->routeIs('agencies.*', 'categories.*', 'suppliers.*', 'admin.users.*', 'audits.*');
            $activeParcSubsection = match (true) {
                $rapportsMenuOpen => 'rapports',
                $gestionMenuOpen => 'gestion',
                $equipementsMenuOpen => 'equipements',
                $stocksMenuOpen => 'stocks',
                default => '',
            };
            $activeParcStockBranch = $stockBranchDecelerOpen ? 'deceler' : ($stockBranchCelerOpen ? 'celer' : '');
        @endphp

    <nav class="flex-1 overflow-y-auto py-4 sidebar-custom-scrollbar" id="sidebar-nav"
         data-parc-subsection="{{ $activeParcSubsection }}"
         data-parc-stock-branch="{{ $activeParcStockBranch }}">

        @if(auth()->check() && auth()->user()->usesEodSignatureOnlySidebar())
            @include('layouts.partials.sidebar-eod-signature-only')
        @else

        {{-- ═══ PARC (menu principal) ═══ --}}
        <div class="sidebar-section" data-section="parc">
            <button type="button" class="sidebar-section-header sidebar-section-header--root {{ $parcSectionOpen ? 'is-active' : '' }}" data-section="parc">
                <span class="font-semibold">PARC</span>
                <span class="sidebar-chevron {{ $parcSectionOpen ? 'open' : '' }}" aria-hidden="true"></span>
            </button>
            <div class="sidebar-section-body {{ $parcSectionOpen ? 'open' : '' }}">

                {{-- Rapports --}}
                <div class="sidebar-subsection" data-subsection="rapports">
                    <button type="button" class="sidebar-subsection-header {{ $rapportsMenuOpen ? 'is-active' : '' }}" data-subsection="rapports">
                        <span>Rapports</span>
                        <span class="sidebar-chevron sidebar-chevron--sm {{ $rapportsMenuOpen ? 'open' : '' }}" aria-hidden="true"></span>
                    </button>
                    <div class="sidebar-subsection-body {{ $rapportsMenuOpen ? 'open' : '' }}">
                        <a href="{{ route('reports.index') }}" class="sidebar-item {{ request()->routeIs('reports.index') ? 'sidebar-active' : '' }}">Vue d'ensemble</a>
                        <a href="{{ route('reports.equipment') }}" class="sidebar-item {{ request()->routeIs('reports.equipment') ? 'sidebar-active' : '' }}">Équipements</a>
                        <a href="{{ route('reports.financial') }}" class="sidebar-item {{ request()->routeIs('reports.financial') ? 'sidebar-active' : '' }}">Financier</a>
                @if(class_exists('App\Models\Maintenance'))
                        <a href="{{ route('reports.maintenance') }}" class="sidebar-item {{ request()->routeIs('reports.maintenance') ? 'sidebar-active' : '' }}">Maintenance</a>
                @endif
                <div class="sidebar-divider"></div>
                        <a href="{{ route('reports.import.equipment') }}" class="sidebar-item {{ request()->routeIs('reports.import.*') ? 'sidebar-active' : '' }}">Importer</a>
                        <a href="{{ route('reports.export.equipment') }}" class="sidebar-item {{ request()->routeIs('reports.export.*') ? 'sidebar-active' : '' }}">Exporter</a>
            </div>
        </div>

                {{-- Gestion --}}
                <div class="sidebar-subsection" data-subsection="gestion">
                    <button type="button" class="sidebar-subsection-header {{ $gestionMenuOpen ? 'is-active' : '' }}" data-subsection="gestion">
                        <span>Gestion</span>
                        <span class="sidebar-chevron sidebar-chevron--sm {{ $gestionMenuOpen ? 'open' : '' }}" aria-hidden="true"></span>
                    </button>
                    <div class="sidebar-subsection-body {{ $gestionMenuOpen ? 'open' : '' }}">
                        <a href="{{ route('parc.index') }}" class="sidebar-item {{ request()->routeIs('parc.index') ? 'sidebar-active' : '' }}">Parc</a>
                        <a href="{{ route('parc.reaffectations.index') }}" class="sidebar-item {{ request()->routeIs('parc.reaffectations.*') ? 'sidebar-active' : '' }}">Historique réaffectations</a>
                        <a href="{{ route('maintenance.index') }}" class="sidebar-item {{ request()->routeIs('maintenance.*') ? 'sidebar-active' : '' }}">Maintenances</a>
                        @if(isset($approval) && $approval)
                            <a href="{{ route('approvals.attachments.show', $approval->id) }}" class="sidebar-item {{ request()->routeIs('approvals.attachments.show') ? 'sidebar-active' : '' }}">Pièces jointes approbation</a>
                        @else
                            <span class="sidebar-item sidebar-item--disabled">Pièces jointes approbation</span>
                        @endif
                        <a href="{{ route('hors-service.index') }}" class="sidebar-item {{ request()->routeIs('hors-service.*') ? 'sidebar-active' : '' }}">Hors service</a>
                        <a href="{{ route('perdu.index') }}" class="sidebar-item {{ request()->routeIs('perdu.*') ? 'sidebar-active' : '' }}">Perdu</a>
            </div>
        </div>

                {{-- Équipements --}}
                <div class="sidebar-subsection" data-subsection="equipements">
                    <button type="button" class="sidebar-subsection-header {{ $equipementsMenuOpen ? 'is-active' : '' }}" data-subsection="equipements">
                        <span>Équipements</span>
                        <span class="sidebar-chevron sidebar-chevron--sm {{ $equipementsMenuOpen ? 'open' : '' }}" aria-hidden="true"></span>
                    </button>
                    <div class="sidebar-subsection-body {{ $equipementsMenuOpen ? 'open' : '' }}">
                        <a href="{{ route('equipment.index') }}" class="sidebar-item {{ request()->routeIs('equipment.index') ? 'sidebar-active' : '' }}">Tous les équipements</a>
                        <a href="{{ route('equipment.renewal') }}" class="sidebar-item {{ request()->routeIs('equipment.renewal') ? 'sidebar-active' : '' }}">Renouvellement</a>
                        <a href="{{ route('equipment.import.form') }}" class="sidebar-item {{ request()->routeIs('equipment.import.*') ? 'sidebar-active' : '' }}">Import</a>
                        <a href="{{ route('equipment.export') }}" class="sidebar-item {{ request()->routeIs('equipment.export') ? 'sidebar-active' : '' }}">Export</a>
            </div>
        </div>

                {{-- Documentation --}}
                <a href="{{ route('documentation.index') }}"
                   class="sidebar-subsection-link {{ request()->routeIs('documentation.*') ? 'sidebar-active' : '' }}">
                    Documentation
                </a>

                {{-- Stocks : CELER / DECELER --}}
                <div class="sidebar-subsection" data-subsection="stocks">
                    <button type="button" class="sidebar-subsection-header {{ $stocksMenuOpen ? 'is-active' : '' }}" data-subsection="stocks">
                        <span>Stocks</span>
                        <span class="sidebar-chevron sidebar-chevron--sm {{ $stocksMenuOpen ? 'open' : '' }}" aria-hidden="true"></span>
                    </button>
                    <div class="sidebar-subsection-body sidebar-subsection-body--stocks {{ $stocksMenuOpen ? 'open' : '' }}">

                        <div class="sidebar-stock-branch" data-stock-branch="celer">
                            <button type="button" class="sidebar-stock-branch-header {{ $stockBranchCelerOpen ? 'is-active' : '' }}" data-stock-branch="celer">
                                <span class="sidebar-stock-branch-title">CELER</span>
                                <span class="sidebar-chevron sidebar-chevron--xs {{ $stockBranchCelerOpen ? 'open' : '' }}" aria-hidden="true"></span>
                            </button>
                            <div class="sidebar-stock-branch-body {{ $stockBranchCelerOpen ? 'open' : '' }}">
                                <a href="{{ route('dashboard.celer-informatique') }}" class="sidebar-item sidebar-item--stock {{ request()->routeIs('dashboard.celer-informatique*') ? 'sidebar-active' : '' }}">Informatique</a>
                                <a href="{{ route('dashboard.celer-reseau') }}" class="sidebar-item sidebar-item--stock {{ request()->routeIs('dashboard.celer-reseau*') ? 'sidebar-active' : '' }}">Réseau</a>
                                <a href="{{ route('dashboard.celer-electronique') }}" class="sidebar-item sidebar-item--stock {{ request()->routeIs('dashboard.celer-electronique*') ? 'sidebar-active' : '' }}">Électronique</a>
                            </div>
                        </div>

                        <div class="sidebar-stock-branch" data-stock-branch="deceler">
                            <button type="button" class="sidebar-stock-branch-header {{ $stockBranchDecelerOpen ? 'is-active' : '' }}" data-stock-branch="deceler">
                                <span class="sidebar-stock-branch-title">DECELER</span>
                                <span class="sidebar-chevron sidebar-chevron--xs {{ $stockBranchDecelerOpen ? 'open' : '' }}" aria-hidden="true"></span>
                            </button>
                            <div class="sidebar-stock-branch-body {{ $stockBranchDecelerOpen ? 'open' : '' }}">
                                <a href="{{ route('dashboard.deceler-informatique') }}" class="sidebar-item sidebar-item--stock {{ request()->routeIs('dashboard.deceler-informatique*') ? 'sidebar-active' : '' }}">Informatique</a>
                                <a href="{{ route('dashboard.deceler-reseau') }}" class="sidebar-item sidebar-item--stock {{ request()->routeIs('dashboard.deceler-reseau*') ? 'sidebar-active' : '' }}">Réseau</a>
                                <a href="{{ route('dashboard.deceler-electronique') }}" class="sidebar-item sidebar-item--stock {{ request()->routeIs('dashboard.deceler-electronique*') ? 'sidebar-active' : '' }}">Électronique</a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        {{-- Infrastructure IT --}}
        <div class="sidebar-section" data-section="infrastructure">
            <button type="button" class="sidebar-section-header" data-section="infrastructure">
                    <span class="font-medium">Infrastructure IT</span>
                <span class="sidebar-chevron" aria-hidden="true"></span>
            </button>
            <div class="sidebar-section-body">
                <p class="sidebar-group-label">Mots de passe</p>
                <a href="{{ route('passwords.index') }}" class="sidebar-item {{ request()->routeIs('passwords.index') ? 'sidebar-active' : '' }}">Tous les mots de passe</a>
                <a href="{{ route('passwords.create') }}" class="sidebar-item {{ request()->routeIs('passwords.create') ? 'sidebar-active' : '' }}">Nouvelle fiche</a>
                <div class="sidebar-divider"></div>
                <p class="sidebar-group-label">Plan d'adressage</p>
                <a href="{{ route('network.index') }}" class="sidebar-item {{ request()->routeIs('network.index') && !request('type') ? 'sidebar-active' : '' }}">Vue d'ensemble</a>
                <a href="{{ route('network.index', ['type' => 'plan_adressage']) }}" class="sidebar-item {{ request('type') === 'plan_adressage' ? 'sidebar-active' : '' }}">Plans VLAN</a>
                <a href="{{ route('network.index', ['type' => 'branchement_local']) }}" class="sidebar-item {{ request('type') === 'branchement_local' ? 'sidebar-active' : '' }}">Branchements locaux</a>
                <a href="{{ route('network.create') }}" class="sidebar-item {{ request()->routeIs('network.create') ? 'sidebar-active' : '' }}">Ajouter une entrée</a>
                <div class="sidebar-divider"></div>
                <p class="sidebar-group-label">Licences</p>
                <a href="{{ route('licences.index') }}" class="sidebar-item {{ request()->routeIs('licences.index') && !request('type') ? 'sidebar-active' : '' }}">Toutes les licences</a>
                @foreach(['Fortinet', 'FAI', 'Certificat', 'Office365'] as $type)
                <a href="{{ route('licences.index', ['type' => $type]) }}" class="sidebar-item {{ request('type') === $type ? 'sidebar-active' : '' }}">{{ $type }}</a>
                @endforeach
                <a href="{{ route('licences.create') }}" class="sidebar-item {{ request()->routeIs('licences.create') ? 'sidebar-active' : '' }}">Nouvelle licence</a>
            </div>
        </div>

        {{-- Change Management --}}
        <div class="sidebar-section" data-section="change">
            <button type="button" class="sidebar-section-header" data-section="change">
                    <span class="font-medium">Change Management</span>
                <span class="sidebar-chevron" aria-hidden="true"></span>
            </button>
            <div class="sidebar-section-body">
                @php $user = auth()->user(); $hasChangeRole = $user && $user->role_change; @endphp
                @if($hasChangeRole)
                    @if($user->role_change === 'N1')
                        <a href="{{ route('change.n1.index') }}" class="sidebar-item {{ request()->routeIs('change.n1.*') ? 'sidebar-active' : '' }}">N+1 — Mes formulaires</a>
                        <a href="{{ route('change.n1.create') }}" class="sidebar-item {{ request()->routeIs('change.n1.create') ? 'sidebar-active' : '' }}">Nouveau formulaire</a>
                    @elseif($user->role_change === 'N2')
                        <a href="{{ route('change.n2.index') }}" class="sidebar-item {{ request()->routeIs('change.n2.*') ? 'sidebar-active' : '' }}">N+2 — Formulaires à traiter</a>
                    @elseif($user->role_change === 'N3')
                        <a href="{{ route('change.n3.index') }}" class="sidebar-item {{ request()->routeIs('change.n3.*') ? 'sidebar-active' : '' }}">N+3 — Validation finale</a>
                    @elseif($user->role_change === 'CONTROLLER')
                        <p class="sidebar-hint">Profil Controller EOD : utilisez la section « EOD Suivi ».</p>
                    @endif
                    <div class="sidebar-divider"></div>
                    <form method="POST" action="{{ route('change.role.clear') }}" class="w-full">
                        @csrf
                        <button type="submit" class="sidebar-item w-full text-left">Changer de rôle (session)</button>
                    </form>
                    <div class="px-4 py-2 mt-1">
                        <span class="sidebar-role-pill">
                            Rôle actuel :
                            {{ $user->role_change === 'N1' ? 'N+1' : ($user->role_change === 'N2' ? 'N+2' : ($user->role_change === 'N3' ? 'N+3' : 'CONTROLLER')) }}
                        </span>
                    </div>
                @else
                    <a href="{{ route('change.role') }}" class="sidebar-item {{ request()->routeIs('change.role') ? 'sidebar-active' : '' }}">Sélectionner un rôle</a>
                @endif
            </div>
        </div>

        {{-- Contrôles IT --}}
        <div class="sidebar-section" data-section="controls">
            <button type="button" class="sidebar-section-header" data-section="controls">
                    <span class="font-medium">Contrôles IT</span>
                <span class="sidebar-chevron" aria-hidden="true"></span>
            </button>
            <div class="sidebar-section-body">
                <a href="{{ route('controls.dashboard') }}" class="sidebar-item {{ request()->routeIs('controls.dashboard') ? 'sidebar-active' : '' }}">Dashboard contrôles</a>
                <a href="{{ route('controls.index') }}" class="sidebar-item {{ request()->routeIs('controls.index') ? 'sidebar-active' : '' }}">Tous les contrôles</a>
                <a href="{{ route('controls.tasks.index') }}" class="sidebar-item {{ request()->routeIs('controls.tasks.*') ? 'sidebar-active' : '' }}">
                    Mes tâches
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
                        <span class="sidebar-badge">{{ $nbTaches }}</span>
                    @endif
                </a>
                <div class="sidebar-divider"></div>
                <p class="sidebar-group-label">Planification</p>
                <a href="{{ route('controls.create') }}" class="sidebar-item {{ request()->routeIs('controls.create') ? 'sidebar-active' : '' }}">Nouveau contrôle</a>
                @if(auth()->user() && auth()->user()->role === 'super_admin')
                    <div class="sidebar-divider"></div>
                    <p class="sidebar-group-label">Modèles</p>
                    <a href="{{ route('controls.templates.index') }}" class="sidebar-item {{ request()->routeIs('controls.templates.*') ? 'sidebar-active' : '' }}">Templates de rapport</a>
                @endif
            </div>
        </div>

        {{-- Incidents --}}
<div class="sidebar-section" data-section="incidents">
            <button type="button" class="sidebar-section-header" data-section="incidents">
            <span class="font-medium">Incidents</span>
            @php
                $nbIncidentsSidebar = 0;
                try {
                    $userSidebar = auth()->user();
                    if ($userSidebar && $userSidebar->isN2()) {
                            $nbIncidentsSidebar = \App\Models\IncidentFiche::where('statut', 'en_cours_n2')->count();
                    } elseif ($userSidebar && $userSidebar->isN3()) {
                            $nbIncidentsSidebar = \App\Models\IncidentFiche::where('statut', 'en_cours_n3')->count();
                    }
                } catch (\Exception $e) {}
            @endphp
            @if($nbIncidentsSidebar > 0)
                    <span class="sidebar-badge">{{ $nbIncidentsSidebar }}</span>
            @endif
                <span class="sidebar-chevron" aria-hidden="true"></span>
    </button>
    <div class="sidebar-section-body">
                <a href="{{ route('incidents.index') }}" class="sidebar-item {{ request()->routeIs('incidents.index') ? 'sidebar-active' : '' }}">Toutes les fiches</a>
        @if(auth()->user()?->isN1() || auth()->user()?->isSuperAdmin())
                <a href="{{ route('incidents.create') }}" class="sidebar-item {{ request()->routeIs('incidents.create') ? 'sidebar-active' : '' }}">Nouvelle fiche incident</a>
        @endif
        @php $userSb = auth()->user(); @endphp
        @if($userSb?->isN1())
                    <p class="sidebar-hint">Rôle : N+1 Helpdesk</p>
        @elseif($userSb?->isN2())
                    <p class="sidebar-hint">Rôle : N+2 Support@if($nbIncidentsSidebar > 0) — {{ $nbIncidentsSidebar }} en attente@endif</p>
        @elseif($userSb?->isN3())
                    <p class="sidebar-hint">Rôle : N+3 Validateur@if($nbIncidentsSidebar > 0) — {{ $nbIncidentsSidebar }} en attente@endif</p>
                    @endif
            </div>
        </div>

        {{-- EOD Suivi --}}
        <div class="sidebar-section" data-section="eod">
            <button type="button" class="sidebar-section-header" data-section="eod">
                <span class="font-medium">EOD Suivi</span>
                <span class="sidebar-chevron" aria-hidden="true"></span>
            </button>
            <div class="sidebar-section-body">
                @php
                    $user = auth()->user();
                    $eodN3 = $user && $user->canAccessEodAsN3();
                    $eodCtrl = $user && $user->canSignEodControllerSlot();
                    $eodN1 = $user && $user->role_change === 'N1';
                    $eodN2 = $user && $user->role_change === 'N2';
                    $eodAny = $eodN1 || $eodN2 || $eodN3 || $eodCtrl;
                @endphp
                @if($eodAny)
                    @if($eodN1)
                        <a href="{{ route('eod.n1.index') }}" class="sidebar-item {{ request()->routeIs('eod.n1.index') ? 'sidebar-active' : '' }}">Mes fiches EOD</a>
                        <a href="{{ route('eod.n1.create') }}" class="sidebar-item {{ request()->routeIs('eod.n1.create') ? 'sidebar-active' : '' }}">Nouvelle fiche EOD</a>
                    @endif
                    @if($eodN2)
                        <a href="{{ route('eod.n2.index') }}" class="sidebar-item {{ request()->routeIs('eod.n2.index') ? 'sidebar-active' : '' }}">Mes fiches EOD</a>
                        <a href="{{ route('eod.n2.create') }}" class="sidebar-item {{ request()->routeIs('eod.n2.create') ? 'sidebar-active' : '' }}">Nouvelle fiche EOD</a>
                    @endif
                    @if($eodN3)
                        <a href="{{ route('eod.n3.pending') }}" class="sidebar-item {{ request()->routeIs('eod.n3.pending') ? 'sidebar-active' : '' }}">Fiches à signer (N+3)</a>
                        <a href="{{ route('eod.n3.index') }}" class="sidebar-item {{ request()->routeIs('eod.n3.index') ? 'sidebar-active' : '' }}">Supervision EOD</a>
                    @endif
                    @if($eodN3 || $eodCtrl || auth()->user()?->role === 'super_admin')
                        <a href="{{ route('eod.planning.index') }}" class="sidebar-item {{ request()->routeIs('eod.planning.*') ? 'sidebar-active' : '' }}">Planification batch</a>
                    @elseif($eodN1 || $eodN2)
                        <a href="{{ route('eod.planning.index') }}" class="sidebar-item {{ request()->routeIs('eod.planning.*') ? 'sidebar-active' : '' }}">Mon planning batch</a>
                    @endif
                    @if($eodCtrl)
                        <a href="{{ route('eod.controller.index') }}" class="sidebar-item {{ request()->routeIs('eod.controller.*') ? 'sidebar-active' : '' }}">Validation Controller</a>
                    @endif
                @else
                    <p class="sidebar-hint">Profil sans accès EOD</p>
                @endif
    </div>
</div>

        {{-- Configuration --}}
        <div class="sidebar-section" data-section="configuration">
            <button type="button" class="sidebar-section-header {{ $configurationSectionOpen ? 'is-active' : '' }}" data-section="configuration">
                    <span class="font-medium">Configuration</span>
                <span class="sidebar-chevron {{ $configurationSectionOpen ? 'open' : '' }}" aria-hidden="true"></span>
            </button>
            <div class="sidebar-section-body {{ $configurationSectionOpen ? 'open' : '' }}">
                <a href="{{ route('agencies.index') }}" class="sidebar-item {{ request()->routeIs('agencies.*') ? 'sidebar-active' : '' }}">Agences</a>
                <a href="{{ route('categories.index') }}" class="sidebar-item {{ request()->routeIs('categories.*') ? 'sidebar-active' : '' }}">Catégories</a>
                <a href="{{ route('suppliers.index') }}" class="sidebar-item {{ request()->routeIs('suppliers.*') ? 'sidebar-active' : '' }}">Fournisseurs</a>
                <a href="{{ route('users.index') }}" class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'sidebar-active' : '' }}">Administration</a>
                <div class="sidebar-divider"></div>
                <a href="{{ route('audits-postes.index') }}" class="sidebar-item {{ request()->routeIs('audits-postes.*') ? 'sidebar-active' : '' }}">Audits postes</a>
                <a href="{{ route('audits.index') }}" class="sidebar-item {{ request()->routeIs('audits.*') ? 'sidebar-active' : '' }}">Journal d'activité</a>
            </div>
        </div>

        @endif

    </nav>

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

<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden lg:hidden z-30 transition-all duration-300"></div>

<style>
.sidebar-custom-scrollbar::-webkit-scrollbar { width: 4px; }
.sidebar-custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
.sidebar-custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.sidebar-custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #C8102E; }

.sidebar-section { border-bottom: 1px solid #f3f4f6; }

.sidebar-section-header,
.sidebar-subsection-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 12px 18px;
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 13px;
    color: #1f2937;
    text-align: left;
    transition: background 0.2s ease, color 0.2s ease;
    gap: 8px;
}
.sidebar-section-header--root {
    font-size: 14px;
    color: #7A0C1A;
    background: linear-gradient(90deg, #fef2f2 0%, #fff 100%);
}
.sidebar-subsection-header {
    padding: 10px 18px 10px 28px;
    font-size: 12.5px;
    font-weight: 600;
}
.sidebar-section-header:hover,
.sidebar-subsection-header:hover {
    background: #fef2f2;
    color: #C8102E;
}
.sidebar-section-header.is-active,
.sidebar-subsection-header.is-active {
    color: #C8102E;
    background: #fef2f2;
    border-left: 3px solid #C8102E;
}

.sidebar-chevron {
    flex-shrink: 0;
    font-size: 10px;
    color: #9ca3af;
    transition: transform 0.25s ease;
    line-height: 1;
}
.sidebar-chevron::before { content: '▾'; }
.sidebar-chevron--sm::before { content: '▸'; }
.sidebar-chevron.open::before,
.sidebar-chevron--sm.open::before { content: '▾'; }

.sidebar-section-body,
.sidebar-subsection-body {
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: #fefefe;
}
.sidebar-section-body.open {
    max-height: 5000px;
    overflow: visible;
}
.sidebar-subsection-body.open {
    max-height: 2000px;
    overflow: visible;
}

.sidebar-section.hidden-section { display: none; }

.sidebar-subsection-link {
    display: block;
    padding: 10px 18px 10px 28px;
    font-size: 12.5px;
    font-weight: 600;
    color: #374151;
    text-decoration: none;
    transition: all 0.2s ease;
}
.sidebar-subsection-link:hover,
.sidebar-subsection-link.sidebar-active {
    background: #fef2f2;
    color: #C8102E;
}

.sidebar-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 8px 18px 8px 42px;
    font-size: 12.5px;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    background: transparent;
    cursor: pointer;
    width: 100%;
    text-align: left;
    position: relative;
}
.sidebar-item--nested { padding-left: 52px; font-size: 12px; }
.sidebar-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 0;
    background: #C8102E;
    transition: width 0.2s ease;
}
.sidebar-item:hover::before,
.sidebar-active::before { width: 3px; }
.sidebar-item:hover,
.sidebar-active {
    background: #fef2f2;
    color: #C8102E;
}
.sidebar-active { font-weight: 600; }
.sidebar-item--disabled {
    opacity: 0.45;
    cursor: not-allowed;
    padding: 8px 18px 8px 42px;
    font-size: 12.5px;
    color: #9ca3af;
}

.sidebar-nested-label {
    font-size: 10px;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 10px 18px 4px 48px;
    margin: 0;
}

/* Stocks : branches CELER / DECELER */
.sidebar-subsection-body--stocks {
    padding-top: 4px;
    padding-bottom: 6px;
}
.sidebar-stock-branch {
    margin: 0;
}
.sidebar-stock-branch-header {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 8px 18px 8px 40px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #374151;
    background: transparent;
    border: none;
    cursor: pointer;
    text-align: left;
    transition: color 0.2s ease, background 0.2s ease;
}
.sidebar-stock-branch-header:hover,
.sidebar-stock-branch-header.is-active {
    color: #C8102E;
    background: #fef2f2;
}
.sidebar-stock-branch-title {
    flex: 1;
}
.sidebar-chevron--xs {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
}
.sidebar-stock-branch-body {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}
.sidebar-stock-branch-body.open {
    max-height: 500px;
    overflow: visible;
}
.sidebar-subsection-body--stocks.open {
    max-height: 2000px;
    overflow: visible;
}
.sidebar-item--stock {
    padding-left: 54px;
    font-size: 12px;
}
.sidebar-group-label {
    font-size: 9.5px;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 10px 18px 4px 42px;
    margin: 0;
}
.sidebar-divider {
    height: 1px;
    background: linear-gradient(90deg, #e5e7eb 0%, transparent 100%);
    margin: 6px 18px 6px 42px;
}
.sidebar-badge {
    background: #C8102E;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 12px;
    margin-left: auto;
}
.sidebar-hint {
    font-size: 11px;
    color: #9ca3af;
    font-style: italic;
    padding: 8px 18px 8px 42px;
    margin: 0;
}
.sidebar-role-pill {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    color: #7A0C1A;
    background: #fef2f2;
    padding: 4px 10px;
    border-radius: 999px;
}
</style>

@verbatim
<script>
document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebar-overlay');
    var toggleBtn = document.getElementById('sidebar-toggle');

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                closeSidebar();
            }
        });
    }
    if (overlay) overlay.addEventListener('click', closeSidebar);
    sidebar.querySelectorAll('a').forEach(function (l) {
        l.addEventListener('click', function () {
            if (window.innerWidth < 1024) closeSidebar();
        });
    });

    var path = window.location.pathname;

    var sectionMap = [
        { section: 'parc', patterns: ['reports', 'equipment', 'documentation', 'celer', 'deceler', 'parc', 'maintenance', 'hors-service', 'perdu', 'approvals'] },
        { section: 'infrastructure', patterns: ['passwords', 'network', 'licences'] },
        { section: 'change', patterns: ['change'] },
        { section: 'controls', patterns: ['controls'] },
        { section: 'incidents', patterns: ['incidents'] },
        { section: 'eod', patterns: ['eod'] },
        { section: 'configuration', patterns: ['audits-postes', 'agencies', 'categories', 'suppliers', 'users', '/audits'] },
    ];

    var subsectionMap = [
        { subsection: 'rapports', patterns: ['reports'] },
        { subsection: 'stocks', patterns: ['celer', 'deceler'] },
        { subsection: 'gestion', patterns: ['/parc', 'maintenance', 'hors-service', 'perdu', 'approvals', 'reaffectations'] },
        { subsection: 'equipements', patterns: ['equipment'] },
    ];

    function openPanel(body, chevron) {
        if (!body) return;
        body.classList.add('open');
        body.style.removeProperty('max-height');
        if (chevron) chevron.classList.add('open');
    }

    function closeAllStockBranches(exceptKey) {
        document.querySelectorAll('#sidebar .sidebar-stock-branch').forEach(function (branch) {
            if (exceptKey && branch.dataset.stockBranch === exceptKey) return;
            var body = branch.querySelector('.sidebar-stock-branch-body');
            var header = branch.querySelector('.sidebar-stock-branch-header');
            var chevron = header ? header.querySelector('.sidebar-chevron') : null;
            closePanel(body, chevron, header);
        });
    }

    function openStockBranch(branchKey) {
        if (!branchKey) return;
        var branch = document.querySelector('#sidebar .sidebar-stock-branch[data-stock-branch="' + branchKey + '"]');
        if (!branch) return;
        var body = branch.querySelector('.sidebar-stock-branch-body');
        var header = branch.querySelector('.sidebar-stock-branch-header');
        var chevron = header ? header.querySelector('.sidebar-chevron') : null;
        openPanel(body, chevron);
        if (header) header.classList.add('is-active');
    }

    function closeAllParcSubsections(exceptSub) {
        document.querySelectorAll('#sidebar .sidebar-section[data-section="parc"] .sidebar-subsection').forEach(function (sub) {
            if (exceptSub && sub === exceptSub) return;
            var body = sub.querySelector(':scope > .sidebar-subsection-body');
            var header = sub.querySelector('.sidebar-subsection-header');
            var chevron = header ? header.querySelector('.sidebar-chevron') : null;
            closePanel(body, chevron, header);
            if (sub.dataset.subsection === 'stocks') {
                closeAllStockBranches();
            }
        });
    }

    /** Ouvre uniquement le sous-menu PARC demandé (+ branche Stocks si applicable). */
    function activateParcSubsection(subKey, opts) {
        opts = opts || {};
        ensureParcMenuOpen();

        var sub = document.querySelector('#sidebar .sidebar-section[data-section="parc"] .sidebar-subsection[data-subsection="' + subKey + '"]');
        if (!sub) return;

        var body = sub.querySelector(':scope > .sidebar-subsection-body');
        var header = sub.querySelector('.sidebar-subsection-header');
        var chevron = header ? header.querySelector('.sidebar-chevron') : null;
        var isOpen = body && body.classList.contains('open');

        if (opts.toggle && isOpen) {
            if (subKey === 'stocks') {
                closeAllStockBranches();
            }
            closePanel(body, chevron, header);
            return;
        }

        closeAllParcSubsections(sub);
        openPanel(body, chevron);
        if (header) header.classList.add('is-active');

        if (subKey === 'stocks') {
            var branch = opts.stockBranch || resolveActiveStockBranch() || 'celer';
            closeAllStockBranches(branch);
            if (opts.toggle) {
                var branchEl = document.querySelector('#sidebar .sidebar-stock-branch[data-stock-branch="' + branch + '"]');
                var branchBody = branchEl ? branchEl.querySelector('.sidebar-stock-branch-body') : null;
                if (branchBody && branchBody.classList.contains('open')) {
                    closeAllStockBranches();
                    return;
                }
            }
            openStockBranch(branch);
        }
    }

    function ensureParcMenuOpen() {
        var parcSection = document.querySelector('#sidebar .sidebar-section[data-section="parc"]');
        if (!parcSection) return;
        parcSection.classList.remove('hidden-section');
        var parcBody = parcSection.querySelector(':scope > .sidebar-section-body');
        var parcHeader = parcSection.querySelector(':scope > .sidebar-section-header');
        var parcChevron = parcHeader ? parcHeader.querySelector('.sidebar-chevron') : null;
        if (parcBody && !parcBody.classList.contains('open')) {
            openPanel(parcBody, parcChevron);
        }
        if (parcHeader) parcHeader.classList.add('is-active');
    }

    function resolveActiveSection() {
    for (var i = 0; i < sectionMap.length; i++) {
        var entry = sectionMap[i];
        for (var j = 0; j < entry.patterns.length; j++) {
                if (path.includes(entry.patterns[j])) return entry.section;
            }
        }
        return null;
    }

    function resolveActiveSubsection() {
        if (resolveActiveSection() !== 'parc') return null;
        for (var i = 0; i < subsectionMap.length; i++) {
            var entry = subsectionMap[i];
            for (var j = 0; j < entry.patterns.length; j++) {
                if (path.includes(entry.patterns[j])) return entry.subsection;
            }
        }
        if (path.includes('documentation')) return null;
        return null;
    }

    var activeSection = resolveActiveSection();
    var activeSubsection = resolveActiveSubsection();

    document.querySelectorAll('#sidebar .sidebar-section').forEach(function (section) {
        var key = section.dataset.section;
        var body = section.querySelector(':scope > .sidebar-section-body');
        var header = section.querySelector(':scope > .sidebar-section-header');
        var chevron = header ? header.querySelector('.sidebar-chevron') : null;

        if (activeSection && key !== activeSection) {
            section.classList.add('hidden-section');
        } else if (activeSection && key === activeSection) {
            openPanel(body, chevron);
            if (header) header.classList.add('is-active');
        }
    });

    function resolveActiveStockBranch() {
        if (/\/deceler(?:\/|$|-)/.test(path)) return 'deceler';
        if (/\/celer(?:\/|$|-)/.test(path)) return 'celer';
        return null;
    }

    var sidebarNav = document.getElementById('sidebar-nav');
    var bladeParcSub = sidebarNav ? (sidebarNav.dataset.parcSubsection || '') : '';
    var bladeParcBranch = sidebarNav ? (sidebarNav.dataset.parcStockBranch || '') : '';

    if (activeSection === 'parc') {
        var subToOpen = activeSubsection || bladeParcSub;
        if (subToOpen) {
            activateParcSubsection(subToOpen, {
                stockBranch: subToOpen === 'stocks'
                    ? (resolveActiveStockBranch() || bladeParcBranch || 'celer')
                    : null
            });
        }
    }

    function closePanel(body, chevron, header) {
        if (body) {
                body.classList.remove('open');
                body.style.removeProperty('max-height');
        }
        if (chevron) chevron.classList.remove('open');
        if (header) header.classList.remove('is-active');
    }

    function getMainSections() {
        return document.querySelectorAll('#sidebar-nav > .sidebar-section[data-section]');
    }

    function isMainSectionFocused(currentSection) {
        var mainSections = getMainSections();
        var visibleCount = 0;
        mainSections.forEach(function (s) {
            if (!s.classList.contains('hidden-section')) visibleCount++;
        });
        return visibleCount === 1 && !currentSection.classList.contains('hidden-section');
    }

    function showAllMainSections() {
        getMainSections().forEach(function (s) {
                    s.classList.remove('hidden-section');
                });
    }

    function focusMainSection(currentSection) {
        getMainSections().forEach(function (s) {
            if (s === currentSection) {
                s.classList.remove('hidden-section');
            } else {
                s.classList.add('hidden-section');
                var otherBody = s.querySelector(':scope > .sidebar-section-body');
                var otherHeader = s.querySelector(':scope > .sidebar-section-header');
                var otherChevron = otherHeader ? otherHeader.querySelector('.sidebar-chevron') : null;
                closePanel(otherBody, otherChevron, otherHeader);
            }
        });
    }

    /* Menus principaux : un seul visible à la fois */
    document.querySelectorAll('#sidebar-nav > .sidebar-section > .sidebar-section-header').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var currentSection = btn.closest('.sidebar-section');
            if (!currentSection) return;

            var body = currentSection.querySelector(':scope > .sidebar-section-body');
            var chevron = btn.querySelector('.sidebar-chevron');
            var isOpen = body && body.classList.contains('open');
            var focused = isMainSectionFocused(currentSection);

            if (isOpen && focused) {
                closePanel(body, chevron, btn);
                showAllMainSections();
                return;
            }

            focusMainSection(currentSection);

            if (!isOpen) {
                openPanel(body, chevron);
                btn.classList.add('is-active');
            } else {
                btn.classList.add('is-active');
            }
        });
    });

    /* Sous-menus PARC : un seul ouvert à la fois, adapté au clic */
    document.querySelectorAll('#sidebar .sidebar-section[data-section="parc"] .sidebar-subsection-header').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var sub = btn.closest('.sidebar-subsection');
            if (!sub || !sub.dataset.subsection) return;

            var subKey = sub.dataset.subsection;
            var body = sub.querySelector(':scope > .sidebar-subsection-body');
            var isOpen = body && body.classList.contains('open');

            if (isOpen) {
                if (subKey === 'stocks') {
                    closeAllStockBranches();
                }
                closePanel(body, btn.querySelector('.sidebar-chevron'), btn);
                return;
            }

            activateParcSubsection(subKey, {
                stockBranch: subKey === 'stocks' ? (resolveActiveStockBranch() || 'celer') : null
            });
        });
    });

    /* CELER / DECELER : une seule branche ouverte */
    document.querySelectorAll('#sidebar .sidebar-stock-branch-header').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var branch = btn.closest('.sidebar-stock-branch');
            if (!branch || !branch.dataset.stockBranch) return;

            var branchKey = branch.dataset.stockBranch;
            var body = branch.querySelector('.sidebar-stock-branch-body');
            var isOpen = body && body.classList.contains('open');

            if (isOpen) {
                closePanel(body, btn.querySelector('.sidebar-chevron'), btn);
                return;
            }

            activateParcSubsection('stocks', { stockBranch: branchKey });
        });
    });

    /* Clic sur un lien : mémoriser le sous-menu actif pour la prochaine visite */
    document.querySelectorAll('#sidebar .sidebar-section[data-section="parc"] a.sidebar-item, #sidebar .sidebar-section[data-section="parc"] a.sidebar-subsection-link').forEach(function (link) {
        link.addEventListener('click', function () {
            var sub = link.closest('.sidebar-subsection');
            if (!sub || !sub.dataset.subsection) return;
            try {
                sessionStorage.setItem('parcActiveSubsection', sub.dataset.subsection);
                if (sub.dataset.subsection === 'stocks') {
                    var stockBranch = link.closest('.sidebar-stock-branch');
                    if (stockBranch && stockBranch.dataset.stockBranch) {
                        sessionStorage.setItem('parcActiveStockBranch', stockBranch.dataset.stockBranch);
                    }
                }
            } catch (err) { /* ignore */ }
        });
    });

    /* Au chargement : si pas de route reconnue, rouvrir le dernier sous-menu visité */
    if (activeSection === 'parc' && !activeSubsection) {
        try {
            var savedSub = sessionStorage.getItem('parcActiveSubsection');
            var savedBranch = sessionStorage.getItem('parcActiveStockBranch');
            if (savedSub && ['rapports', 'gestion', 'equipements', 'stocks'].indexOf(savedSub) !== -1) {
                activateParcSubsection(savedSub, {
                    stockBranch: savedSub === 'stocks' ? (savedBranch || 'celer') : null
                });
            }
        } catch (err) { /* ignore */ }
    }
});
</script>
@endverbatim
