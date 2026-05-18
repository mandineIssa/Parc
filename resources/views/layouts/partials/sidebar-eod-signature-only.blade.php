{{-- Menu réduit : comptes dédiés signature EOD (N+3 ou Controller) --}}
@php $u = auth()->user(); @endphp

<div class="sidebar-section open" data-section="eod-only">
    <div class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">EOD — Signature</div>
    <div class="sidebar-section-body open" style="max-height: none;">
        @if($u->eodSidebarShowsN3Section())
            <a href="{{ route('eod.n3.pending') }}" class="sidebar-item {{ request()->routeIs('eod.n3.pending') ? 'sidebar-active' : '' }}">Fiches à signer (N+3)</a>
            <a href="{{ route('eod.n3.index') }}" class="sidebar-item {{ request()->routeIs('eod.n3.index') ? 'sidebar-active' : '' }}">Supervision EOD</a>
            <a href="{{ route('eod.n3.statistiques') }}" class="sidebar-item {{ request()->routeIs('eod.n3.statistiques') ? 'sidebar-active' : '' }}">Statistiques</a>
        @endif
        @if($u->eodSidebarShowsControllerSection())
            <a href="{{ route('eod.n3.pending') }}" class="sidebar-item {{ request()->routeIs('eod.n3.pending') ? 'sidebar-active' : '' }}">Fiches en attente (Controller)</a>
            <a href="{{ route('eod.controller.index') }}" class="sidebar-item {{ request()->routeIs('eod.controller.index') ? 'sidebar-active' : '' }}">Toutes les fiches batch</a>
        @endif
    </div>
</div>

<div class="sidebar-divider"></div>

<a href="{{ route('documentation.index') }}"
   class="sidebar-subsection-link {{ request()->routeIs('documentation.*') ? 'sidebar-active' : '' }}">
    Documentation
</a>

<div class="mt-4 px-2">
    <button type="button"
            class="sidebar-item logout-btn w-full text-left text-red-600 hover:bg-red-50 rounded-lg"
            data-action="logout"
            onclick="window.submitLogout && window.submitLogout(event)">
        Déconnexion
    </button>
</div>
