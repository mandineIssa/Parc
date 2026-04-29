{{-- Menu réduit : comptes dédiés signature EOD (N+3 ou Controller) --}}
@php $u = auth()->user(); @endphp

<div class="sidebar-section open" data-section="eod-only">
    <div class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">EOD — Signature</div>
    <div class="sidebar-section-body">
        @if($u->eodSidebarShowsN3Section())
            <a href="{{ route('eod.n3.pending') }}" class="sidebar-item {{ request()->routeIs('eod.n3.pending') ? 'sidebar-active' : '' }}">
                <span class="sidebar-dot"></span>
                <span>Fiches à signer (N+3)</span>
            </a>
            <a href="{{ route('eod.n3.index') }}" class="sidebar-item {{ request()->routeIs('eod.n3.index') ? 'sidebar-active' : '' }}">
                <span class="sidebar-dot"></span>
                <span>Supervision EOD</span>
            </a>
            <a href="{{ route('eod.n3.statistiques') }}" class="sidebar-item {{ request()->routeIs('eod.n3.statistiques') ? 'sidebar-active' : '' }}">
                <span class="sidebar-dot"></span>
                <span>Statistiques</span>
            </a>
        @endif
        @if($u->eodSidebarShowsControllerSection())
            <a href="{{ route('eod.n3.pending') }}" class="sidebar-item {{ request()->routeIs('eod.n3.pending') ? 'sidebar-active' : '' }}">
                <span class="sidebar-dot"></span>
                <span>Fiches en attente (Controller)</span>
            </a>
            <a href="{{ route('eod.controller.index') }}" class="sidebar-item {{ request()->routeIs('eod.controller.index') ? 'sidebar-active' : '' }}">
                <span class="sidebar-dot"></span>
                <span>Toutes les fiches batch</span>
            </a>
        @endif
    </div>
</div>

<div class="sidebar-divider"></div>

<form method="POST" action="{{ route('logout') }}" class="mt-4 px-2">
    @csrf
    <button type="submit" class="sidebar-item w-full text-left text-red-600 hover:bg-red-50 rounded-lg">
        <span class="sidebar-dot"></span>
        <span>Déconnexion</span>
    </button>
</form>
