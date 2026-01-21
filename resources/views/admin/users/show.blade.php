@extends('layouts.app')

@section('title', 'Détails de l\'utilisateur')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ __('Détails de l\'utilisateur') }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('users.edit', $user) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded transition-colors">
                    <i class="fas fa-edit mr-2"></i>{{ __('Modifier') }}
                </a>
                <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Retour') }}
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informations principales -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-indigo-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-user-circle mr-2"></i>Informations Personnelles
                        </h3>
                    </div>
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="border-b pb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Nom</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                            </div>

                            <div class="border-b pb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Prénom</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $user->prenom }}</p>
                            </div>

                            <div class="border-b pb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                                <p class="text-gray-900">
                                    <i class="fas fa-envelope text-indigo-600 mr-2"></i>
                                    <a href="mailto:{{ $user->email }}" class="text-indigo-600 hover:text-indigo-800">
                                        {{ $user->email }}
                                    </a>
                                </p>
                            </div>

                            <div class="border-b pb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Rôle</label>
                                <p>
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                        @if($user->role === 'super_admin') bg-purple-100 text-purple-800
                                        @elseif($user->role === 'agent_it') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        @if($user->role === 'super_admin')
                                            <i class="fas fa-crown mr-2"></i>Super Admin
                                        @elseif($user->role === 'agent_it')
                                            <i class="fas fa-laptop-code mr-2"></i>Agent IT
                                        @else
                                            <i class="fas fa-user mr-2"></i>Utilisateur
                                        @endif
                                    </span>
                                </p>
                            </div>

                            <div class="border-b pb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Département</label>
                                <p class="text-gray-900">
                                    <i class="fas fa-building text-gray-600 mr-2"></i>
                                    {{ $user->departement ?? '-' }}
                                </p>
                            </div>

                            <div class="border-b pb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Fonction</label>
                                <p class="text-gray-900">
                                    <i class="fas fa-briefcase text-gray-600 mr-2"></i>
                                    {{ $user->fonction ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activités récentes (si applicable) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="bg-green-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-history mr-2"></i>Activités Récentes
                        </h3>
                    </div>
                    <div class="p-6 bg-white border-b border-gray-200">
                        @if(isset($user->equipment) && $user->equipment->count() > 0)
                            <div class="space-y-3">
                                @foreach($user->equipment->take(5) as $equipment)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <div class="bg-indigo-100 p-2 rounded-full mr-3">
                                                <i class="fas fa-desktop text-indigo-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $equipment->nom }}</p>
                                                <p class="text-sm text-gray-500">{{ $equipment->numero_serie ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            @if($equipment->status == 'disponible') bg-green-100 text-green-800
                                            @elseif($equipment->status == 'en_service') bg-blue-100 text-blue-800
                                            @elseif($equipment->status == 'en_maintenance') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $equipment->status)) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                <p>Aucune activité récente</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panneau latéral -->
            <div class="lg:col-span-1">
                <!-- Statistiques -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="bg-blue-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-chart-pie mr-2"></i>Statistiques
                        </h3>
                    </div>
                    <div class="p-6 bg-white">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-3 rounded-full mr-3">
                                        <i class="fas fa-desktop fa-lg text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Équipements</p>
                                        <p class="text-2xl font-bold text-gray-900">
                                            {{ $user->equipment_count ?? 0 }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if(isset($user->tickets_count))
                            <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-3 rounded-full mr-3">
                                        <i class="fas fa-ticket-alt fa-lg text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Tickets</p>
                                        <p class="text-2xl font-bold text-gray-900">
                                            {{ $user->tickets_count }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="bg-yellow-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-bolt mr-2"></i>Actions Rapides
                        </h3>
                    </div>
                    <div class="p-6 bg-white">
                        <div class="space-y-3">
                            <a href="{{ route('users.edit', $user) }}" 
                               class="flex items-center justify-center w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded transition-colors">
                                <i class="fas fa-edit mr-2"></i>Modifier
                            </a>

                            <a href="mailto:{{ $user->email }}" 
                               class="flex items-center justify-center w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded transition-colors">
                                <i class="fas fa-envelope mr-2"></i>Envoyer un Email
                            </a>

                            @if(!$user->isSuperAdmin() || auth()->user()->isSuperAdmin())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="w-full"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="flex items-center justify-center w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded transition-colors">
                                    <i class="fas fa-trash mr-2"></i>Supprimer
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informations système -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-gray-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-info-circle mr-2"></i>Informations Système
                        </h3>
                    </div>
                    <div class="p-6 bg-white">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    <i class="fas fa-calendar-plus text-gray-400 mr-2"></i>Créé le
                                </label>
                                <p class="text-gray-900 font-semibold">
                                    {{ $user->created_at->format('d/m/Y à H:i') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $user->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <div class="border-t pt-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    <i class="fas fa-calendar-check text-gray-400 mr-2"></i>Dernière modification
                                </label>
                                <p class="text-gray-900 font-semibold">
                                    {{ $user->updated_at->format('d/m/Y à H:i') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $user->updated_at->diffForHumans() }}
                                </p>
                            </div>

                            @if($user->email_verified_at)
                            <div class="border-t pt-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>Email vérifié
                                </label>
                                <p class="text-gray-900 font-semibold">
                                    {{ $user->email_verified_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>
                            @else
                            <div class="border-t pt-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">
                                    <i class="fas fa-exclamation-circle text-yellow-500 mr-2"></i>Email
                                </label>
                                <p class="text-yellow-600 font-semibold">
                                    Non vérifié
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection