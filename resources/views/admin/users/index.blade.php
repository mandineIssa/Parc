{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('content')
@php
    $filterQuery = request()->only(['search', 'role', 'role_change', 'departement', 'statut']);
    $hasFilters = collect($filterQuery)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty();
    $roleOptions = [
        'super_admin' => 'Super Admin',
        'agent_it' => 'Agent IT',
        'user' => 'Utilisateur',
        'eod_n3' => 'Signataire EOD N+3',
        'eod_controller' => 'Contrôleur EOD',
    ];
    $roleChangeOptions = [
        'N1' => 'N+1 - Demandeur',
        'N2' => 'N+2 - Technicien',
        'N3' => 'N+3 - Validateur',
        'CONTROLLER' => 'Controller',
    ];
@endphp
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Liste des utilisateurs</h2>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('users.import.template') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-md transition-colors">
                            Télécharger un modèle
                        </a>
                        <a href="{{ route('users.import.form') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md transition-colors">
                            Importer
                        </a>
                        <a href="{{ route('users.export', $filterQuery) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-md transition-colors">
                            Exporter Excel
                        </a>
                        <a href="{{ route('users.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md transition-colors">
                            + Nouvel utilisateur
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('users.index') }}" id="usersFiltersForm" class="mb-6 bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="flex flex-col lg:flex-row gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text"
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Matricule, nom, prénom, e-mail…"
                                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#A61B29] focus:border-[#A61B29] outline-none bg-white">
                            </div>
                        </div>
                        <div class="w-full lg:w-48">
                            <select name="role" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#A61B29] focus:border-[#A61B29] outline-none" onchange="this.form.submit()">
                                <option value="">Tous les rôles</option>
                                @foreach($roleOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full lg:w-52">
                            <select name="role_change" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#A61B29] focus:border-[#A61B29] outline-none" onchange="this.form.submit()">
                                <option value="">Tous les rôles Change</option>
                                @foreach($roleChangeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(request('role_change') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full lg:w-52">
                            <select name="departement" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#A61B29] focus:border-[#A61B29] outline-none" onchange="this.form.submit()">
                                <option value="">Tous les départements</option>
                                @foreach(($departementOptions ?? []) as $departement)
                                    <option value="{{ $departement }}" @selected(request('departement') === $departement)>{{ $departement }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full lg:w-40">
                            <select name="statut" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-[#A61B29] focus:border-[#A61B29] outline-none" onchange="this.form.submit()">
                                <option value="">Tous les statuts</option>
                                <option value="actif" @selected(request('statut') === 'actif')>Actif</option>
                                <option value="inactif" @selected(request('statut') === 'inactif')>Inactif</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-[#A61B29] hover:bg-[#7A0C1A] text-white font-semibold py-2.5 px-5 rounded-lg transition">
                            Filtrer
                        </button>
                        <a href="{{ route('users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2.5 px-5 rounded-lg transition text-center">
                            Réinitialiser
                        </a>
                    </div>
                </form>

                @if($hasFilters)
                    <div class="mb-4 px-4 py-3 rounded-lg bg-blue-50 border border-blue-200 flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm text-blue-800">
                            <span class="font-semibold">{{ $users->total() }} résultat{{ $users->total() > 1 ? 's' : '' }}</span>
                            @if(request('search')) • Recherche : « {{ request('search') }} » @endif
                            @if(request('role')) • Rôle : {{ $roleOptions[request('role')] ?? request('role') }} @endif
                            @if(request('role_change')) • Change : {{ $roleChangeOptions[request('role_change')] ?? request('role_change') }} @endif
                            @if(request('departement')) • Département : {{ request('departement') }} @endif
                            @if(request('statut')) • Statut : {{ request('statut') }} @endif
                        </p>
                        <a href="{{ route('users.index') }}" class="text-sm font-medium text-blue-700 hover:text-blue-900">Tout effacer</a>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matricule</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prénom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle Principal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle Change</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Département</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->matricule ?: '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->prenom }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->role === 'eod_n3')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-violet-100 text-violet-900">
                                            {{ $user->principal_role_label }}
                                        </span>
                                    @elseif($user->role === 'eod_controller')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-900">
                                            {{ $user->principal_role_label }}
                                        </span>
                                    @elseif($user->isSuperAdmin())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            Super Admin
                                        </span>
                                    @elseif($user->isAgentIT())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Agent IT
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Utilisateur
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->role_change)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($user->role_change === 'N1') bg-blue-100 text-blue-800
                                            @elseif($user->role_change === 'N2') bg-green-100 text-green-800
                                            @elseif($user->role_change === 'N3') bg-purple-100 text-purple-800
                                            @elseif($user->role_change === 'CONTROLLER') bg-indigo-100 text-indigo-800
                                            @endif">
                                            {{ $user->change_role_label }}
                                        </span>
                                        @if($user->eod_signature_only_ui ?? false)
                                            <span class="ml-1 text-[10px] text-gray-500" title="Menu EOD uniquement">EOD↓</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $user->departement ?: '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Voir</a>
                                    <a href="{{ route('users.edit', $user) }}" class="text-green-600 hover:text-green-900 mr-3">Modifier</a>
                                    @if(!$user->isSuperAdmin() || auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    Aucun utilisateur ne correspond aux filtres.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection