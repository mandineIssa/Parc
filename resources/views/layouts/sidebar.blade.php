{{-- resources/views/layouts/sidebar.blade.php --}}
<aside id="sidebar" class="fixed top-16 left-0 h-[calc(100vh-64px)] w-64 bg-white border-r border-gray-200 shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-40 overflow-y-auto">
    

    <nav class="p-4 sm:p-6 space-y-1">
        
        <!-- ================= DASHBOARD ================= -->
        <!-- <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>
        </div> -->

        
        <!-- ================= RAPPORTS ================= -->
        <div class="mb-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Rapports</h3>
            
            <!-- Menu déroulant Rapports -->
            <div class="flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer hover:bg-red-50 transition-all duration-200" data-toggle="rapports">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="font-medium">Tableau de Bord Rapports</span>
                </div>
                <svg id="rapports-arrow" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            
            <div id="rapports-menu" class="ml-8 mt-1 space-y-1 hidden">
                <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 {{ request()->routeIs('reports.index') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="3" fill="currentColor"/>
                    </svg>
                    Vue d'ensemble
                </a>
                
                <a href="{{ route('reports.equipment') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 {{ request()->routeIs('reports.equipment') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="3" fill="currentColor"/>
                    </svg>
                    Équipements
                </a>
                
                <a href="{{ route('reports.financial') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 {{ request()->routeIs('reports.financial') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="3" fill="currentColor"/>
                    </svg>
                    Financier
                </a>
                
                @if(class_exists('App\Models\Maintenance'))
                <a href="{{ route('reports.maintenance') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 {{ request()->routeIs('reports.maintenance') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="3" fill="currentColor"/>
                    </svg>
                    Maintenance
                </a>
                @endif
                
                <hr class="my-2 border-gray-200">
                
                <a href="{{ route('reports.import.equipment') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 {{ request()->routeIs('reports.import.*') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="3" fill="currentColor"/>
                    </svg>
                    Importer
                </a>
                
                <a href="{{ route('reports.export.equipment') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 {{ request()->routeIs('reports.export.*') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                    <svg class="w-3 h-3 mr-2" viewBox="0 0 16 16">
                        <circle cx="8" cy="8" r="3" fill="currentColor"/>
                    </svg>
                    Exporter
                </a>
            </div>
        </div>

       
        <hr class="my-4 border-gray-200">

        <!-- ================= ÉQUIPEMENTS ================= -->
        <div class="mb-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Équipements</h3>
            
            <div class="space-y-1">
                <a href="{{ route('equipment.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('equipment.index') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="font-medium">Tous les Équipements</span>
                </a>
                <a href="{{ route('equipment.import.form') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('equipment.import.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    <span>Import</span>
                </a>
                
                <a href="{{ route('equipment.export') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('equipment.export') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Export</span>
                </a>
                
            </div>
        </div>

        <hr class="my-4 border-gray-200">
         <hr class="my-2 border-gray-200">

        <!-- ================= STOCKS ================= -->
        <div class="mb-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Stocks</h3>
            
            <!-- CELER -->
            <div class="mb-2">
                <div class="flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer hover:bg-red-50 transition-all duration-200" data-toggle="celer">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        <span class="font-medium">CELER</span>
                    </div>
                    <svg id="celer-arrow" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                
                <div id="celer-menu" class="ml-8 mt-1 space-y-1 hidden">
                    <a href="{{ route('dashboard.celer-informatique') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 {{ request()->routeIs('dashboard.celer-informatique') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2" viewBox="0 0 16 16">
                            <circle cx="8" cy="8" r="3" fill="currentColor"/>
                        </svg>
                        Informatique
                    </a>
                    <a href="{{ route('dashboard.celer-reseau') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 {{ request()->routeIs('dashboard.celer-reseau') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2" viewBox="0 0 16 16">
                            <circle cx="8" cy="8" r="3" fill="currentColor"/>
                        </svg>
                        Réseau
                    </a>
                    <a href="{{ route('dashboard.celer-electronique') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 {{ request()->routeIs('dashboard.celer-electronique') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2" viewBox="0 0 16 16">
                            <circle cx="8" cy="8" r="3" fill="currentColor"/>
                        </svg>
                        Électronique
                    </a>
                </div>
            </div>
            
            <!-- DECELER -->
            <div>
                <div class="flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer hover:bg-red-50 transition-all duration-200" data-toggle="deceler">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span class="font-medium">DECELER</span>
                    </div>
                    <svg id="deceler-arrow" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                
                <div id="deceler-menu" class="ml-8 mt-1 space-y-1 hidden">
                    <a href="{{ route('dashboard.deceler-informatique') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 {{ request()->routeIs('dashboard.deceler-informatique') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2" viewBox="0 0 16 16">
                            <circle cx="8" cy="8" r="3" fill="currentColor"/>
                        </svg>
                        Informatique
                    </a>
                    <a href="{{ route('dashboard.deceler-reseau') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 {{ request()->routeIs('dashboard.deceler-reseau') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2" viewBox="0 0 16 16">
                            <circle cx="8" cy="8" r="3" fill="currentColor"/>
                        </svg>
                        Réseau
                    </a>
                    <a href="{{ route('dashboard.deceler-electronique') }}" class="flex items-center px-4 py-2 rounded hover:bg-red-50 transition-all duration-200 {{ request()->routeIs('dashboard.deceler-electronique') ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        <svg class="w-3 h-3 mr-2" viewBox="0 0 16 16">
                            <circle cx="8" cy="8" r="3" fill="currentColor"/>
                        </svg>
                        Électronique
                    </a>
                </div>
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        <!-- ================= GESTION ================= -->
        <div class="mb-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Gestion</h3>
            
            <div class="space-y-1">
                <a href="{{ route('parc.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('parc.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span>Parc</span>
                </a>
                
                <a href="{{ route('maintenance.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('maintenance.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Maintenances</span>
                </a>
                
                <!-- NOUVEAU BOUTON AJOUTÉ ICI -->
                <a href="http://127.0.0.1:8000/approvals/27/attachments" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 text-gray-700 hover:bg-red-50 {{ request()->is('approvals/*/attachments') ? 'sidebar-active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <span>Pièces jointes Approbation</span>
                </a>
                
                <a href="{{ route('hors-service.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('hors-service.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.698-.833-2.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <span>Hors Service</span>
                </a>
                
                <a href="{{ route('perdu.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('perdu.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Perdu</span>
                </a>
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        <!-- ================= CONFIGURATION ================= -->
        <div class="mb-4">
            <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Configuration</h3>
            
            <div class="space-y-1">
                <a href="{{ route('agencies.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('agencies.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Agences</span>
                </a>
                
                <a href="{{ route('categories.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('categories.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span>Catégories</span>
                </a>
                
                <a href="{{ route('suppliers.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('suppliers.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span>Fournisseurs</span>
                </a>
                
                <!-- Bouton Admin -->
                <a href="http://127.0.0.1:8000/admin/users" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->is('admin/users*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}" target="_blank">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Administration</span>
                </a>
            </div>
        </div>

        <hr class="my-4 border-gray-200">

        <!-- ================= JOURNAL ================= -->
        <div class="mb-4">
            <a href="{{ route('audits.index') }}" class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('audits.*') ? 'sidebar-active' : 'text-gray-700 hover:bg-red-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Journal d'activité</span>
            </a>
        </div>

        <!-- ================= VERSION ================= -->
        <div class="mt-8 pt-4 border-t border-gray-200">
            <div class="px-4 text-xs text-gray-400">
                <p class="font-medium">Gestion Parc Informatique</p>
                <p class="mt-1">Version 1.0.0</p>
                <p class="mt-1">© {{ date('Y') }} COFINA</p>
            </div>
        </div>

    </nav>
</aside>

<!-- Sidebar overlay for mobile -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden lg:hidden z-30 transition-opacity duration-300"></div>

<script>
    // Toggle sidebar menus
    document.querySelectorAll('[data-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            const menu = document.getElementById(btn.dataset.toggle + '-menu');
            const arrow = document.getElementById(btn.dataset.toggle + '-arrow');
            
            // Close other open menus
            document.querySelectorAll('[id$="-menu"]').forEach(otherMenu => {
                if (otherMenu !== menu && !otherMenu.classList.contains('hidden')) {
                    otherMenu.classList.add('hidden');
                    const otherArrow = document.getElementById(otherMenu.id.replace('-menu', '-arrow'));
                    if (otherArrow) otherArrow.classList.remove('rotate-180');
                }
            });
            
            // Toggle current menu
            menu?.classList.toggle('hidden');
            arrow?.classList.toggle('rotate-180');
        });
    });

    // Set active menu based on current route
    document.addEventListener('DOMContentLoaded', function() {
        const currentPath = window.location.pathname;
        
        // Open CELER menu if on CELER page
        if (currentPath.includes('celer')) {
            const celerMenu = document.getElementById('celer-menu');
            const celerArrow = document.getElementById('celer-arrow');
            if (celerMenu) celerMenu.classList.remove('hidden');
            if (celerArrow) celerArrow.classList.add('rotate-180');
        }
        
        // Open DECELER menu if on DECELER page
        if (currentPath.includes('deceler')) {
            const decelerMenu = document.getElementById('deceler-menu');
            const decelerArrow = document.getElementById('deceler-arrow');
            if (decelerMenu) decelerMenu.classList.remove('hidden');
            if (decelerArrow) decelerArrow.classList.add('rotate-180');
        }
        
        // Open Rapports menu if on any reports page
        if (currentPath.includes('reports') || currentPath.includes('rapports')) {
            const rapportsMenu = document.getElementById('rapports-menu');
            const rapportsArrow = document.getElementById('rapports-arrow');
            if (rapportsMenu) rapportsMenu.classList.remove('hidden');
            if (rapportsArrow) rapportsArrow.classList.add('rotate-180');
        }
    });
</script>