{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier un utilisateur')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    {{ __('Modifier l\'utilisateur') }} : {{ $user->name }} {{ $user->prenom }}
                </h2>
                
                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nom -->
                    <div>
                        <label for="name" class="block font-medium text-gray-700">Nom *</label>
                        <input id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus />
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prénom -->
                    <div>
                        <label for="prenom" class="block font-medium text-gray-700">Prénom *</label>
                        <input id="prenom" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" required />
                        @error('prenom')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block font-medium text-gray-700">Email *</label>
                        <input id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" value="{{ old('email', $user->email) }}" required />
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Rôle Principal -->
                        <div>
                            <label for="role" class="block font-medium text-gray-700">Rôle Principal *</label>
                            <select id="role" name="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Utilisateur</option>
                                <option value="agent_it" {{ old('role', $user->role) === 'agent_it' ? 'selected' : '' }}>Agent IT</option>
                                @if(auth()->user()->isSuperAdmin())
                                    <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                @endif
                            </select>
                            @error('role')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rôle Change Management -->
                        <div>
                            <label for="role_change" class="block font-medium text-gray-700">Rôle Change Management</label>
                            <select id="role_change" name="role_change" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Aucun') }}</option>
                                <option value="N1" {{ old('role_change', $user->role_change) === 'N1' ? 'selected' : '' }}>N+1 - Demandeur</option>
                                <option value="N2" {{ old('role_change', $user->role_change) === 'N2' ? 'selected' : '' }}>N+2 - Technicien</option>
                                <option value="N3" {{ old('role_change', $user->role_change) === 'N3' ? 'selected' : '' }}>N+3 - Validateur</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Rôle pour l'application Change Management</p>
                            @error('role_change')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Département -->
                    <div>
                        <label for="departement" class="block font-medium text-gray-700">Département</label>
                        <input id="departement" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="departement" value="{{ old('departement', $user->departement) }}" />
                        @error('departement')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fonction -->
                    <div>
                        <label for="fonction" class="block font-medium text-gray-700">Fonction</label>
                        <input id="fonction" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="fonction" value="{{ old('fonction', $user->fonction) }}" />
                        @error('fonction')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mot de passe (optionnel) -->
                    <div>
                        <label for="password" class="block font-medium text-gray-700">Nouveau mot de passe</label>
                        <input id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password" />
                        <p class="mt-1 text-xs text-gray-500">Laissez vide pour conserver le mot de passe actuel</p>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmation mot de passe -->
                    <div>
                        <label for="password_confirmation" class="block font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                        <input id="password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password_confirmation" />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-md transition-colors">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md transition-colors">
                            {{ __('Mettre à jour') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection