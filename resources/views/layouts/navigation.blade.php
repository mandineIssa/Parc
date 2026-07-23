<nav class="fixed top-0 left-0 right-0 bg-white shadow-md border-b z-50 h-16">
    <div class="h-full w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-full">
            <!-- Logo -->
            <div class="flex items-center">
                <button id="sidebar-toggle"
                        class="mr-4 lg:hidden text-gray-600 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-600 rounded p-1"
                        aria-label="Toggle sidebar">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center"
                   title="Tableau de bord"
                   aria-label="Tableau de bord">
                    <img src="{{ asset('images/Cofina1.jpeg') }}"
                         alt="Cofina — accueil"
                         class="h-14 sm:h-18 w-auto mr-2 sm:mr-3">
                    <span class="sr-only">Tableau de bord</span>
                    <span class="font-bold text-lg sm:text-xl text-gray-800 hidden xs:inline">Gestion Parc</span>
                </a>
            </div>

            <!-- Recherche, notifications, thème -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                @auth
                <button type="button" id="global-search-open" class="p-2 text-gray-600 hover:text-[#C8102E] rounded-lg" title="Recherche (Ctrl+K)">
                    <i class="fas fa-search"></i>
                </button>
                @php $unreadNotifs = auth()->user()->gpiNotifications()->whereNull('read_at')->count(); @endphp
                <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-600 hover:text-[#C8102E] rounded-lg" title="Notifications">
                    <i class="fas fa-bell"></i>
                    @if($unreadNotifs > 0)
                        <span class="absolute -top-0.5 -right-0.5 bg-[#C8102E] text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">{{ $unreadNotifs > 9 ? '9+' : $unreadNotifs }}</span>
                    @endif
                </a>
                <button type="button" id="theme-toggle" class="p-2 text-gray-600 hover:text-[#C8102E] rounded-lg" title="Mode sombre">
                    <i class="fas fa-moon"></i>
                </button>
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
                            <!-- Tooltip email (desktop uniquement) -->
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

                        <button type="button"
                                class="logout-btn w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors"
                                data-action="logout"
                                onclick="window.submitLogout && window.submitLogout(event)">
                            <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                        </button>

                    </div>
                </div>
                @endauth
            </div>
        </div>
    </div>
</nav>