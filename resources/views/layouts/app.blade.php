<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
)
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - @yield('title', 'Gestion Parc Informatique')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/cofina.png') }}">
     <!-- #region -->
       @stack('styles')
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/agencies.css') }}" rel="stylesheet">
    <!-- Dans le <head> -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('css/suppliers.css') }}">

@auth
    @if(auth()->user()->role === 'super_admin')
        <li class="relative">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-700 rounded-lg transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804l-1.121-1.121a3 3 0 01-1.121-2.122V6a3 3 0 013-3h5a3 3 0 013 3v1.5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.378 21H18a3 3 0 003-3v-5a3 3 0 00-3-3h-1.5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.378 15l-3.086 3.086a2 2 0 01-2.828 0L11 15"/>
                </svg>
                Super Admin
                @php
                    $pendingCount = \App\Models\TransitionApproval::where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="ml-auto px-2 py-0.5 text-xs bg-red-500 text-white rounded-full">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>
        </li>
    @endif
@endauth
    <style>
        :root {
            --cofina-red: #e60012;
            --cofina-gray: #f0f0f0;
            --sidebar-width: 16rem;
        }

        .text-cofina-red { color: var(--cofina-red); }
        .bg-cofina-red { background-color: var(--cofina-red); }
        .border-cofina-gray { border-color: var(--cofina-gray); }
        
        .sidebar-active {
            background-color: #e60012;
            color: white;
        }
        
        .sidebar-active:hover {
            background-color: #cc0010;
        }
    </style>
    
    @stack('styles')
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

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

    <!-- Scripts -->
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

        // Close sidebar when clicking overlay
        overlay?.addEventListener('click', function() {
            sidebar?.classList.add('-translate-x-full');
            this.classList.add('hidden');
        });
        
        // Close sidebar when clicking outside on mobile
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

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Handle flash messages with SweetAlert
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