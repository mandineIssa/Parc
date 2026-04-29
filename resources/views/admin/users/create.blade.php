{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Créer un nouvel utilisateur')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    {{ __('Créer un nouvel utilisateur') }}
                </h2>
                
                <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                    @csrf

                    <!-- Nom -->
                    <div>
                        <label for="name" class="block font-medium text-gray-700">Nom *</label>
                        <input id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="name" value="{{ old('name') }}" required autofocus />
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prénom -->
                    <div>
                        <label for="prenom" class="block font-medium text-gray-700">Prénom *</label>
                        <input id="prenom" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="prenom" value="{{ old('prenom') }}" required />
                        @error('prenom')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block font-medium text-gray-700">Email *</label>
                        <input id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" value="{{ old('email') }}" required />
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Rôle Principal -->
                        <div>
                            <label for="role" class="block font-medium text-gray-700">Rôle Principal *</label>
                            <select id="role" name="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">{{ __('Sélectionnez un rôle') }}</option>
                                <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Utilisateur</option>
                                <option value="agent_it" {{ old('role') === 'agent_it' ? 'selected' : '' }}>Agent IT</option>
                                <option value="eod_n3" {{ old('role') === 'eod_n3' ? 'selected' : '' }}>Signataire EOD N+3 (/eod/n3)</option>
                                <option value="eod_controller" {{ old('role') === 'eod_controller' ? 'selected' : '' }}>Contrôleur EOD batch (/eod/controller)</option>
                                @if(auth()->user()->isSuperAdmin())
                                    <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                @endif
                            </select>
                            @error('role')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rôle Change / EOD (N1–N3 + Controller batch) -->
                        <div>
                            <label for="role_change" class="block font-medium text-gray-700">Rôle Change / EOD</label>
                            <select id="role_change" name="role_change" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Aucun') }}</option>
                                <option value="N1" {{ old('role_change') === 'N1' ? 'selected' : '' }}>N+1 - Demandeur (Change)</option>
                                <option value="N2" {{ old('role_change') === 'N2' ? 'selected' : '' }}>N+2 - Technicien (Change)</option>
                                <option value="N3" {{ old('role_change') === 'N3' ? 'selected' : '' }}>N+3 - Validateur (Change + signature EOD /eod/n3)</option>
                                <option value="CONTROLLER" {{ old('role_change') === 'CONTROLLER' ? 'selected' : '' }}>Controller — validation batch EOD (/eod/controller)</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">La <strong>signature N+3</strong> des fiches EOD se fait avec le rôle <strong>N+3</strong> (pas « Controller »). Le rôle Controller correspond au valideur batch sur <code class="text-xs">/eod/controller</code>.</p>
                            @error('role_change')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="rounded-lg border border-indigo-100 bg-indigo-50/50 p-4">
                        <label class="inline-flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="eod_signature_only_ui" value="1" class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('eod_signature_only_ui') ? 'checked' : '' }}>
                            <span>
                                <span class="font-medium text-gray-800">Interface limitée au module EOD</span>
                                <span class="block text-xs text-gray-600 mt-1">Si coché avec <strong>N+3</strong> : menu réduit aux pages de signature <code class="text-xs">/eod/n3</code>. Avec <strong>Controller</strong> : uniquement <code class="text-xs">/eod/controller</code>. Rôle principal conseillé : Utilisateur.</span>
                            </span>
                        </label>
                    </div>

                    <!-- Département -->
                    <div>
                        <label for="departement" class="block font-medium text-gray-700">Département</label>
                        <input id="departement" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="departement" value="{{ old('departement') }}" />
                        @error('departement')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fonction -->
                    <div>
                        <label for="fonction" class="block font-medium text-gray-700">Fonction</label>
                        <input id="fonction" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="fonction" value="{{ old('fonction') }}" />
                        @error('fonction')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mot de passe -->
                    <div>
                        <label for="password" class="block font-medium text-gray-700">Mot de passe *</label>
                        <input id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password" required />
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmation mot de passe -->
                    <div>
                        <label for="password_confirmation" class="block font-medium text-gray-700">Confirmer le mot de passe *</label>
                        <input id="password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password_confirmation" required />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-md transition-colors">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md transition-colors">
                            {{ __('Créer l\'utilisateur') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection