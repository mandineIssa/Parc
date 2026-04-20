{{-- resources/views/layouts/sidebar.blade.php --}}
<aside id="sidebar" class="fixed top-16 left-0 h-[calc(100vh-64px)] w-64 bg-white border-r border-gray-200 shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-40 flex flex-col">

    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">

        {{-- RAPPORTS --}}
        <div class="mb-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Rapports</h3>
            <div class="flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer hover:bg-red-50 transition-all duration-200" data-toggle="rapports">
                <div class="flex items-center min-w-0">
                    <svg class="w-5 h-5 mr-3 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="font-medium text-sm truncate">Tableau de Bord Rapports</span>
                </div>
                <svg id="rapports-arrow" class="w-4 h-4 flex-shrink-0 ml-2 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            <div id="rapports-menu" class="ml-8 mt-1 space-y-1 hidden">
                <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('reports.index') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                    <span class="truncate">Vue d'ensemble</span>
                </a>
                <a href="{{ route('reports.equipment') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('reports.equipment') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                    <span class="truncate">Équipements</span>
                </a>
                <a href="{{ route('reports.financial') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('reports.financial') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                    <span class="truncate">Financier</span>
                </a>
                @if(class_exists('App\Models\Maintenance'))
                <a href="{{ route('reports.maintenance') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('reports.maintenance') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                    <span class="truncate">Maintenance</span>
                </a>
                @endif
                <hr class="my-2 border-gray-200">
                <a href="{{ route('reports.import.equipment') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('reports.import.*') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                    <span class="truncate">Importer</span>
                </a>
                <a href="{{ route('reports.export.equipment') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('reports.export.*') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                    <span class="truncate">Exporter</span>
                </a>
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        {{-- ÉQUIPEMENTS --}}
        <div class="mb-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Équipements</h3>
            <div class="space-y-1">
                <a href="{{ route('equipment.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('equipment.index') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="font-medium text-sm truncate">Tous les Équipements</span>
                </a>
                <a href="{{ route('equipment.import.form') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('equipment.import.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    <span class="text-sm">Import</span>
                </a>
                <a href="{{ route('equipment.export') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('equipment.export') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span class="text-sm">Export</span>
                </a>
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        {{-- STOCKS --}}
        <div class="mb-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Stocks</h3>

            {{-- CELER --}}
            <div class="mb-2">
                <div class="flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer hover:bg-red-50 transition-all duration-200" data-toggle="celer">
                    <div class="flex items-center min-w-0">
                        <svg class="w-5 h-5 mr-3 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        <span class="font-medium text-sm">CELER</span>
                    </div>
                    <svg id="celer-arrow" class="w-4 h-4 flex-shrink-0 ml-2 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="celer-menu" class="ml-8 mt-1 space-y-1 hidden">
                    <a href="{{ route('dashboard.celer-informatique') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('dashboard.celer-informatique') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                        Informatique
                    </a>
                    <a href="{{ route('dashboard.celer-reseau') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('dashboard.celer-reseau') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                        Réseau
                    </a>
                    <a href="{{ route('dashboard.celer-electronique') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('dashboard.celer-electronique') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                        Électronique
                    </a>
                </div>
            </div>

            {{-- DECELER --}}
            <div>
                <div class="flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer hover:bg-red-50 transition-all duration-200" data-toggle="deceler">
                    <div class="flex items-center min-w-0">
                        <svg class="w-5 h-5 mr-3 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span class="font-medium text-sm">DECELER</span>
                    </div>
                    <svg id="deceler-arrow" class="w-4 h-4 flex-shrink-0 ml-2 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="deceler-menu" class="ml-8 mt-1 space-y-1 hidden">
                    <a href="{{ route('dashboard.deceler-informatique') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('dashboard.deceler-informatique') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                        Informatique
                    </a>
                    <a href="{{ route('dashboard.deceler-reseau') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('dashboard.deceler-reseau') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                        Réseau
                    </a>
                    <a href="{{ route('dashboard.deceler-electronique') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('dashboard.deceler-electronique') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                        Électronique
                    </a>
                </div>
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        {{-- GESTION --}}
        <div class="mb-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Gestion</h3>
            <div class="space-y-1">

                {{-- Parc --}}
                <a href="{{ route('parc.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('parc.index') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span class="text-sm">Parc</span>
                </a>

                {{-- ✅ Historique Réaffectations --}}
                <a href="{{ route('parc.reaffectations.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('parc.reaffectations.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm">Historique Réaffectations</span>
                </a>

                {{-- Maintenances --}}
                <a href="{{ route('maintenance.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('maintenance.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm">Maintenances</span>
                </a>

                @if(isset($approval) && $approval)
                    <a href="{{ route('approvals.attachments.show', $approval->id) }}"
                       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 text-gray-700 hover:bg-red-50 {{ request()->routeIs('approvals.attachments.show') ? 'sidebar-active' : '' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span class="text-sm truncate">Pièces jointes Approbation</span>
                    </a>
                @else
                    <div class="flex items-center px-4 py-3 rounded-lg text-gray-400 bg-gray-100 cursor-not-allowed">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span class="text-sm truncate">Pièces jointes Approbation</span>
                    </div>
                @endif

                <a href="{{ route('hors-service.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('hors-service.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.698-.833-2.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <span class="text-sm">Hors Service</span>
                </a>

                <a href="{{ route('perdu.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('perdu.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm">Perdu</span>
                </a>
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        {{-- EOD SUIVI --}}
        <div class="mb-4">
            <div class="flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer hover:bg-red-50 transition-all duration-200" data-toggle="eod">
                <div class="flex items-center min-w-0">
                    <svg class="w-5 h-5 mr-3 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium text-sm truncate">EOD Suivi</span>
                </div>
                <svg id="eod-arrow" class="w-4 h-4 flex-shrink-0 ml-2 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            <div id="eod-menu" class="ml-8 mt-1 space-y-1 hidden">
                @php
                    $user = auth()->user();
                    $hasChangeRole = $user && $user->role_change;
                @endphp

                @if($hasChangeRole)
                    @if($user->role_change === 'N1')
                        <a href="{{ route('eod.n1.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('eod.n1.*') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                            <span class="truncate">Mes fiches EOD</span>
                        </a>
                        <a href="{{ route('eod.n1.create') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('eod.n1.create') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                            <span class="truncate">Nouvelle fiche EOD</span>
                        </a>
                    @elseif($user->role_change === 'N2')
                        <a href="{{ route('eod.n2.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('eod.n2.*') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                            <span class="truncate">Fiches à valider</span>
                        </a>
                    @elseif($user->role_change === 'N3')
                        <a href="{{ route('eod.n3.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('eod.n3.*') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                            <span class="truncate">Supervision EOD</span>
                        </a>
                    @endif
                @else
                    <div class="px-4 py-2 text-xs text-gray-400 italic">
                        Sélectionnez un rôle Change Management
                    </div>
                @endif
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        
<hr class="my-4 border-gray-200">

{{-- INFRASTRUCTURE IT --}}
<div class="mb-4">
    <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Infrastructure IT</h3>

    {{-- Mots de passe --}}
    <div class="mb-1">
        <div class="flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer hover:bg-red-50 transition-all duration-200" data-toggle="passwords">
            <div class="flex items-center min-w-0">
                <svg class="w-5 h-5 mr-3 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                <span class="font-medium text-sm truncate">Mots de Passe IT</span>
            </div>
            <svg id="passwords-arrow" class="w-4 h-4 flex-shrink-0 ml-2 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div id="passwords-menu" class="ml-8 mt-1 space-y-1 hidden">
            <a href="{{ route('passwords.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('passwords.index') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                <span class="truncate">Tous les mots de passe</span>
            </a>
            <a href="{{ route('passwords.create') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('passwords.create') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                <span class="truncate">Nouvelle fiche</span>
            </a>
        </div>
    </div>

    {{-- Plan d'adressage --}}
    <div class="mb-1">
        <div class="flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer hover:bg-red-50 transition-all duration-200" data-toggle="network">
            <div class="flex items-center min-w-0">
                <svg class="w-5 h-5 mr-3 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                </svg>
                <span class="font-medium text-sm truncate">Plan d'Adressage</span>
            </div>
            <svg id="network-arrow" class="w-4 h-4 flex-shrink-0 ml-2 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div id="network-menu" class="ml-8 mt-1 space-y-1 hidden">
            <a href="{{ route('network.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('network.index') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                <span class="truncate">Vue d'ensemble</span>
            </a>
            <a href="{{ route('network.index', ['type' => 'plan_adressage']) }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request('type') === 'plan_adressage' ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                <span class="truncate">Plans VLAN</span>
            </a>
            <a href="{{ route('network.index', ['type' => 'branchement_local']) }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request('type') === 'branchement_local' ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                <span class="truncate">Branchements locaux</span>
            </a>
            <a href="{{ route('network.create') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('network.create') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                <span class="truncate">Ajouter une entrée</span>
            </a>
        </div>
    </div>

    {{-- Licences --}}
    <div class="mb-1">
        <div class="flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer hover:bg-red-50 transition-all duration-200" data-toggle="licences">
            <div class="flex items-center min-w-0">
                <svg class="w-5 h-5 mr-3 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                <span class="font-medium text-sm truncate">Suivi des Licences</span>
            </div>
            <svg id="licences-arrow" class="w-4 h-4 flex-shrink-0 ml-2 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div id="licences-menu" class="ml-8 mt-1 space-y-1 hidden">
            <a href="{{ route('licences.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('licences.index') && !request('type') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                <span class="truncate">Toutes les licences</span>
            </a>
            @foreach(['Fortinet' => '🛡', 'FAI' => '🌐', 'Certificat' => '🔐', 'Office365' => '📧'] as $type => $icon)
            <a href="{{ route('licences.index', ['type' => $type]) }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request('type') === $type ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                <span class="mr-2 text-xs">{{ $icon }}</span>
                <span class="truncate">{{ $type }}</span>
            </a>
            @endforeach
            <hr class="my-1 border-gray-200">
            <a href="{{ route('licences.create') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('licences.create') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                <span class="truncate">Nouvelle licence</span>
            </a>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     À AJOUTER dans le JS du sidebar (dans la fonction autoOpen)
     ═══════════════════════════════════════════════════════════════════════════ --}}
{{--
    if (path.includes('passwords'))  autoOpen('passwords');
    if (path.includes('network'))    autoOpen('network');
    if (path.includes('licences'))   autoOpen('licences');
--}}

        {{-- CHANGE MANAGEMENT --}}
        <div class="mb-4">
            <div class="flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer hover:bg-red-50 transition-all duration-200" data-toggle="change">
                <div class="flex items-center min-w-0">
                    <svg class="w-5 h-5 mr-3 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span class="font-medium text-sm truncate">Change Management</span>
                </div>
                <svg id="change-arrow" class="w-4 h-4 flex-shrink-0 ml-2 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            <div id="change-menu" class="ml-8 mt-1 space-y-1 hidden">
                @php
                    $user = auth()->user();
                    $hasChangeRole = $user && $user->role_change;
                @endphp

                @if($hasChangeRole)
                    @if($user->role_change === 'N1')
                        <a href="{{ route('change.n1.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('change.n1.*') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                            <span class="truncate">N+1 - Mes formulaires</span>
                        </a>
                        <a href="{{ route('change.n1.create') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('change.n1.create') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                            <span class="truncate">Nouveau formulaire</span>
                        </a>
                    @elseif($user->role_change === 'N2')
                        <a href="{{ route('change.n2.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('change.n2.*') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                            <span class="truncate">N+2 - Formulaires à traiter</span>
                        </a>
                    @elseif($user->role_change === 'N3')
                        <a href="{{ route('change.n3.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('change.n3.*') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                            <span class="truncate">N+3 - Validation finale</span>
                        </a>
                    @endif
                    
                    <hr class="my-2 border-gray-200">
                    
                    <form method="POST" action="{{ route('change.role.clear') }}" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm text-gray-600 w-full text-left">
                            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                            <span class="truncate">Changer de rôle (session)</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('change.role') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('change.role') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
                        <span class="truncate">Sélectionner un rôle</span>
                    </a>
                @endif
                
                <hr class="my-2 border-gray-200">
                
                <div class="px-4 py-2">
                    <div class="text-xs text-gray-400">
                        @if($hasChangeRole)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                  style="background: {{ $user->role_change === 'N1' ? 'rgba(59,130,246,0.1)' : ($user->role_change === 'N2' ? 'rgba(16,185,129,0.1)' : 'rgba(139,92,246,0.1)') }}; 
                                         color: {{ $user->role_change === 'N1' ? '#3b82f6' : ($user->role_change === 'N2' ? '#10b981' : '#8b5cf6') }};">
                                Rôle: {{ $user->role_change === 'N1' ? 'N+1' : ($user->role_change === 'N2' ? 'N+2' : 'N+3') }}
                            </span>
                            @if(session('change_role') && session('change_role') !== $user->role_change)
                                <br>
                                <span class="text-xs text-yellow-600 mt-1 inline-block">
                                    Session: {{ session('change_role') }} (différent)
                                </span>
                            @endif
                        @else
                            <span class="text-gray-400">Aucun rôle Change Management</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>



        <hr class="my-4 border-gray-200">

{{-- CONTRÔLES IT --}}
<div class="mb-4">
    <div class="flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer hover:bg-red-50 transition-all duration-200" data-toggle="controls">
        <div class="flex items-center min-w-0">
            <svg class="w-5 h-5 mr-3 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span class="font-medium text-sm truncate">Contrôles IT</span>
        </div>
        <svg id="controls-arrow" class="w-4 h-4 flex-shrink-0 ml-2 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
    <div id="controls-menu" class="ml-8 mt-1 space-y-1 hidden">
        <a href="{{ route('controls.dashboard') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('controls.dashboard') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
            <span class="truncate">Tableau de bord</span>
        </a>
        <a href="{{ route('controls.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('controls.index') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
            <span class="truncate">Tous les contrôles</span>
        </a>
        <a href="{{ route('controls.create') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('controls.create') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
            <span class="truncate">Nouveau contrôle</span>
        </a>
        <a href="{{ route('controls.tasks.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('controls.tasks.index') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
            <span class="truncate">Mes tâches</span>
        </a>
        @if(auth()->user() && auth()->user()->is_admin)
        <hr class="my-2 border-gray-200">
        <a href="{{ route('controls.templates.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 text-sm {{ request()->routeIs('controls.templates.*') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
            <svg class="w-3 h-3 mr-2 flex-shrink-0" viewBox="0 0 16 16"><circle cx="8" cy="8" r="3" fill="currentColor"/></svg>
            <span class="truncate">Templates</span>
        </a>
        @endif
    </div>
</div>
        <hr class="my-4 border-gray-200">


        
        @auth
        <div class="mb-4">
            <a href="{{ route('dashboard') }}"
               class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                <i class="fas fa-tachometer-alt mr-3 w-5 text-center flex-shrink-0"></i>
                <span class="text-sm">Dashboard</span>
            </a>
        </div>
        <hr class="my-4 border-gray-200">
        @endauth

        {{-- CONFIGURATION --}}
        <div class="mb-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Configuration</h3>
            <div class="space-y-1">
                <a href="{{ route('agencies.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('agencies.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="text-sm">Agences</span>
                </a>
                <a href="{{ route('categories.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('categories.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span class="text-sm">Catégories</span>
                </a>
                <a href="{{ route('suppliers.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('suppliers.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span class="text-sm">Fournisseurs</span>
                </a>
                <a href="{{ route('users.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="text-sm">Administration</span>
                </a>
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        {{-- JOURNAL --}}
        <div class="mb-4">
            <a href="{{ route('audits.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('audits.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm">Journal d'activité</span>
            </a>
        </div>

    </nav>

    {{-- VERSION --}}
    <div class="p-4 border-t border-gray-200 bg-white flex-shrink-0">
        <div class="text-xs text-gray-400">
            <p class="font-medium">Gestion Parc Informatique</p>
            <p class="mt-1">Version 1.0.0</p>
            <p class="mt-1">© {{ date('Y') }} COFINA</p>
        </div>
    </div>

</aside>

{{-- Overlay mobile --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden lg:hidden z-30 transition-opacity duration-300"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
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

    sidebar.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 1024) closeSidebar();
        });
    });

    document.querySelectorAll('[data-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var key   = btn.dataset.toggle;
            var menu  = document.getElementById(key + '-menu');
            var arrow = document.getElementById(key + '-arrow');

            document.querySelectorAll('[id$="-menu"]').forEach(function (m) {
                if (m !== menu && !m.classList.contains('hidden')) {
                    m.classList.add('hidden');
                    var a = document.getElementById(m.id.replace('-menu', '-arrow'));
                    if (a) a.classList.remove('rotate-180');
                }
            });

            if (menu)  menu.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        });
    });

    var path = window.location.pathname;

    function autoOpen(key) {
        var m = document.getElementById(key + '-menu');
        var a = document.getElementById(key + '-arrow');
        if (m) m.classList.remove('hidden');
        if (a) a.classList.add('rotate-180');
    }

    if (path.includes('celer') && !path.includes('deceler')) autoOpen('celer');
    if (path.includes('deceler'))                              autoOpen('deceler');
    if (path.includes('reports') || path.includes('rapports')) autoOpen('rapports');
    if (path.includes('change') || path.includes('change/'))    autoOpen('change');
    if (path.includes('eod') || path.includes('eod/'))          autoOpen('eod');
});
    if (path.includes('passwords'))  autoOpen('passwords');
    if (path.includes('network'))    autoOpen('network');
    if (path.includes('licences'))   autoOpen('licences');
    if (path.includes('controls')) autoOpen('controls');
</script>

<style>
.sidebar-active {
    background-color: #fee2e2;
    color: #dc2626;
    font-weight: 500;
}
.sidebar-active svg {
    color: #dc2626;
}
.rotate-180 {
    transform: rotate(180deg);
}
</style>