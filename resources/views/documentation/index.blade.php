@extends('layouts.app')

@section('title', 'Documentation')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-book mr-3 text-blue-600"></i>
                Documentation Complète
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Guide d'utilisation et documentation technique du système de gestion du parc informatique
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Guide Utilisateur -->
            <a href="{{ route('documentation.show', 'utilisateur') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition duration-300">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user text-blue-600 text-3xl mr-4"></i>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Guide Utilisateur</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400">
                    Documentation pour les utilisateurs standard du système
                </p>
            </a>

            <!-- Guide Agent IT -->
            <a href="{{ route('documentation.show', 'agent-it') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition duration-300">
                <div class="flex items-center mb-4">
                    <i class="fas fa-laptop-code text-green-600 text-3xl mr-4"></i>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Guide Agent IT</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400">
                    Gestion des équipements, transitions et imports
                </p>
            </a>

            <!-- Guide Administrateur -->
            <a href="{{ route('documentation.show', 'admin') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition duration-300">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user-shield text-purple-600 text-3xl mr-4"></i>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Guide Admin</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400">
                    Administration du système et approbations
                </p>
            </a>

            <!-- Documentation API -->
            <a href="{{ route('documentation.show', 'api') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition duration-300">
                <div class="flex items-center mb-4">
                    <i class="fas fa-code text-orange-600 text-3xl mr-4"></i>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Documentation API</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400">
                    Documentation technique des routes et API
                </p>
            </a>

            <!-- Guide Installation -->
            <a href="{{ route('documentation.show', 'installation') }}" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow hover:shadow-lg transition duration-300">
                <div class="flex items-center mb-4">
                    <i class="fas fa-download text-red-600 text-3xl mr-4"></i>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Installation</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400">
                    Guide d'installation et de configuration
                </p>
            </a>

            <!-- Téléchargements -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <div class="flex items-center mb-4">
                    <i class="fas fa-file-pdf text-gray-600 text-3xl mr-4"></i>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Téléchargements</h2>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('documentation.download', 'pdf') }}" class="block text-blue-600 hover:underline">
                        <i class="fas fa-file-pdf mr-2"></i>Documentation PDF
                    </a>
                    <a href="{{ route('equipment.export.template') }}" class="block text-blue-600 hover:underline">
                        <i class="fas fa-file-csv mr-2"></i>Template Import CSV
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection