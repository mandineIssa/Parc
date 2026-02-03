<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- META TAGS POUR ROLE MANAGER -->
    <meta name="user-role" content="{{ auth()->check() ? auth()->user()->role : 'guest' }}">
    <meta name="user-data" content="{{ auth()->check() ? json_encode([
        'id' => auth()->user()->id,
        'name' => auth()->user()->name,
        'email' => auth()->user()->email,
        'departement' => auth()->user()->departement,
        'fonction' => auth()->user()->fonction
    ]) : '{}' }}">
    
    <title>{{ config('app.name') }} - @yield('title', 'Gestion Parc Informatique')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/cofina.png') }}">
    
    <!-- CSS pour les rôles -->
    <link href="{{ asset('css/role-styles.css') }}" rel="stylesheet">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/agencies.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/suppliers.css') }}" rel="stylesheet">
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>

<body class="bg-gray-50 min-h-screen flex flex-col role-{{ auth()->check() ? auth()->user()->role : 'guest' }}">

    <!-- Top Navigation -->
    @include('layouts.navigation')

    <div class="flex flex-1 pt-16">

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main content -->
        <main class="flex-1 px-4 py-4 sm:px-6 sm:py-6 lg:ml-64 transition-all duration-300">
            <!-- Page header -->
            <div class="mb-6 sm:mb-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 break-words">
                            @yield('header', 'Dashboard')
                        </h1>
                        
                        @hasSection('subheader')
                            <p class="mt-2 text-sm sm:text-base text-gray-600 max-w-3xl break-words">
                                @yield('subheader')
                            </p>
                        @endif
                    </div>
                    
                    <!-- Badge de rôle -->
                    <div id="role-badge-container" class="hidden sm:block">
                        @if(auth()->check())
                            @php
                                $roleColors = [
                                    'super_admin' => 'danger',
                                    'agent_it' => 'warning',
                                    'user' => 'primary'
                                ];
                                $roleNames = [
                                    'super_admin' => 'Super Admin',
                                    'agent_it' => 'Agent IT',
                                    'user' => 'Utilisateur'
                                ];
                                $color = $roleColors[auth()->user()->role] ?? 'secondary';
                                $name = $roleNames[auth()->user()->role] ?? auth()->user()->role;
                            @endphp
                            <span class="badge bg-{{ $color }} role-badge">
                                {{ $name }}
                            </span>
                        @endif
                    </div>
                    
                    @hasSection('header-actions')
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                            @yield('header-actions')
                        </div>
                    @endif
                </div>
            </div>

            <!-- Flash messages -->
            @if(session('success'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg animate-fade-in">
                    <div class="flex items-start sm:items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2 sm:mr-3 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm sm:text-base text-green-700 font-medium break-words">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg animate-fade-in">
                    <div class="flex items-start sm:items-center">
                        <svg class="w-5 h-5 text-red-500 mr-2 sm:mr-3 flex-shrink-0 mt-0.5 sm:mt-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm sm:text-base text-red-700 font-medium break-words">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                @yield('content')
            </div>

        </main>
    </div>

    <!-- Footer -->
    @include('layouts.footer')

    <!-- SCRIPTS ROLE MANAGER -->
    <script src="{{ asset('js/roleConfig.js') }}"></script>
    <script src="{{ asset('js/roleManager.js') }}"></script>
    
    <script>
        // Initialiser le RoleManager après le chargement du DOM
        document.addEventListener('DOMContentLoaded', function() {
            // Vérifier les routes
            if (!window.RoleManager.checkCurrentRoute()) {
                console.log('Redirection en cours...');
                return;
            }
            
            // Configurer les gestionnaires de clic protégés
            window.RoleManager.setupProtectedClickHandlers();
            
            // Helper global pour vérifier les rôles
            window.requireRole = function(role, callback) {
                if (window.RoleManager.hasRole(role)) {
                    callback();
                } else {
                    window.RoleManager.showAccessDeniedAlert();
                }
            };
            
            // Helper global pour vérifier les permissions
            window.requirePermission = function(permission, callback) {
                if (window.RoleManager.hasPermission(permission)) {
                    callback();
                } else {
                    window.RoleManager.showAccessDeniedAlert();
                }
            };
            
            // Appliquer les règles d'interface
            window.RoleManager.applyUIRules();
        });
        
        // Bootstrap
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        
        <script>
            // Toggle sidebar on mobile
            const toggleSidebar = document.getElementById('toggle-sidebar');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            toggleSidebar?.addEventListener('click', function() {
                sidebar?.classList.toggle('-translate-x-full');
                overlay?.classList.toggle('hidden');
            });

            overlay?.addEventListener('click', function() {
                sidebar?.classList.add('-translate-x-full');
                this.classList.add('hidden');
            });
            
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 1024) {
                    const isClickInsideSidebar = sidebar?.contains(event.target);
                    const isClickOnToggle = toggleSidebar?.contains(event.target);
                    
                    if (!isClickInsideSidebar && !isClickOnToggle && sidebar && !sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.add('-translate-x-full');
                        overlay?.classList.add('hidden');
                    }
                }
            });

            // Tooltips
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });

            // SweetAlert pour les flash messages
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Succès',
                    text: "{{ session('success') }}",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: "{{ session('error') }}",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                });
            @endif
        </script>
        
        @stack('scripts')

</body>
</html>