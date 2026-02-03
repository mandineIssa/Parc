<nav class="fixed top-0 left-0 right-0 bg-white shadow-md border-b z-50 h-16">
    <div class="h-full w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-full">
            <!-- Logo -->
            <div class="flex items-center">
                <button id="toggle-sidebar" 
                        class="mr-4 lg:hidden text-gray-600 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-600 rounded p-1"
                        aria-label="Toggle sidebar">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <img src="{{ asset('images/Cofina1.jpeg') }}"
                         alt="Cofina Logo"
                         class="h-14 sm:h-18 w-auto mr-2 sm:mr-3">
                    <span class="font-bold text-lg sm:text-xl text-gray-800 hidden xs:inline">Gestion Parc</span>
                </a>
            </div>

            <!-- User -->
            <div class="flex items-center space-x-2 sm:space-x-4">
                @auth
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            @click.away="open = false"
                            class="flex items-center space-x-1 sm:space-x-2 text-gray-700 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-600 rounded p-1"
                            aria-expanded="false"
                            aria-haspopup="true">
                        <div class="relative group/avatar">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 bg-red-600 text-white rounded-full flex items-center justify-center font-semibold cursor-pointer text-xs sm:text-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <!-- Tooltip avec l'email (desktop seulement) -->
                            <div class="hidden lg:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover/avatar:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                                {{ Auth::user()->email }}
                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                                    <div class="border-4 border-transparent border-t-gray-800"></div>
                                </div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform hidden sm:inline" 
                           :class="{ 'rotate-180': open }"></i>
                    </button>

                    <!-- Dropdown menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 sm:w-56 bg-white border rounded-lg shadow-lg z-50"
                         style="display: none;">
                        
                        <!-- Email visible sur mobile -->
                        <div class="lg:hidden px-4 py-2 text-xs text-gray-500 border-b break-words">
                            {{ Auth::user()->email }}
                        </div>

                        <a href="{{ route('profile.edit') }}" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-user mr-2"></i>Profil
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}" class="logout-form">
    @csrf
    <button type="submit" 
            class="logout-btn w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors"
            data-action="logout">
        <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
    </button>
</form>
                        
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </div>
</nav>