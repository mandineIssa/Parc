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