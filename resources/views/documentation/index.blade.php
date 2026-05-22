@extends('layouts.app')

@section('title', 'Documentation')

@section('content')
@include('documentation.partials.styles')

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-book mr-3 text-[#A61B29]"></i>
                Documentation complète
            </h1>
            <p class="text-gray-600 dark:text-gray-400 max-w-3xl">
                Guide d'utilisation et documentation technique du système de gestion du parc informatique COFINA.
                Sélectionnez une section ci-dessous.
            </p>
        </div>

        <div class="doc-box-cofina mb-8 doc-toc">
            <h2 class="text-lg font-semibold text-[#7A0C1A] mb-2">Sommaire rapide</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 text-sm">
                <a href="{{ route('documentation.show', 'manuel-complet') }}" class="font-semibold text-[#7A0C1A]">★ Manuel complet (tous scénarios + captures)</a>
                <a href="{{ route('documentation.show', 'utilisateur') }}">Guide utilisateur</a>
                <a href="{{ route('documentation.show', 'agent-it') }}">Guide agent IT</a>
                <a href="{{ route('documentation.show', 'admin') }}">Guide administrateur</a>
                <a href="{{ route('documentation.show', 'api') }}">Documentation technique</a>
                <a href="{{ route('documentation.show', 'installation') }}">Installation</a>
                <span class="text-gray-500">Téléchargements (ci-dessous)</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="{{ route('documentation.show', 'manuel-complet') }}"
               class="bg-gradient-to-br from-[#fdf2f3] to-white dark:from-gray-800 dark:to-gray-800 p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300 border-2 border-[#A61B29]/40 md:col-span-2 lg:col-span-3">
                <div class="flex items-center mb-4">
                    <i class="fas fa-book-open text-[#A61B29] text-4xl mr-4"></i>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Manuel d'utilisation complet</h2>
                        <p class="text-sm text-[#7A0C1A] font-medium mt-1">Recommandé — 43 scénarios illustrés</p>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Parc, stocks, transitions, approbations, Change, EOD, incidents, contrôles, infrastructure et configuration.
                    Emplacements pour captures d'écran intégrés.
                </p>
            </a>

            <a href="{{ route('documentation.show', 'utilisateur') }}"
               class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition duration-300 border border-transparent hover:border-[#A61B29]/30">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user text-blue-600 text-3xl mr-4"></i>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Guide Utilisateur</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Connexion, navigation, consultation du parc, renouvellement, rapports et support.
                </p>
            </a>

            <a href="{{ route('documentation.show', 'agent-it') }}"
               class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition duration-300 border border-transparent hover:border-[#A61B29]/30">
                <div class="flex items-center mb-4">
                    <i class="fas fa-laptop-code text-[#A61B29] text-3xl mr-4"></i>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Guide Agent IT</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Équipements, stocks CELER/DECELER, parc, transitions, imports CSV, maintenance.
                </p>
            </a>

            <a href="{{ route('documentation.show', 'admin') }}"
               class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition duration-300 border border-transparent hover:border-[#A61B29]/30">
                <div class="flex items-center mb-4">
                    <i class="fas fa-user-shield text-purple-600 text-3xl mr-4"></i>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Guide Admin</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Utilisateurs, rôles, approbations, Change, EOD, contrôles, incidents, sécurité.
                </p>
            </a>

            <a href="{{ route('documentation.show', 'api') }}"
               class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition duration-300 border border-transparent hover:border-[#A61B29]/30">
                <div class="flex items-center mb-4">
                    <i class="fas fa-code text-orange-600 text-3xl mr-4"></i>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Documentation technique</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Architecture Laravel, routes modulaires, authentification, tests et déploiement.
                </p>
            </a>

            <a href="{{ route('documentation.show', 'installation') }}"
               class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow hover:shadow-lg transition duration-300 border border-transparent hover:border-[#A61B29]/30">
                <div class="flex items-center mb-4">
                    <i class="fas fa-download text-red-600 text-3xl mr-4"></i>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Installation</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Prérequis, configuration MySQL, variables d'environnement, production et dépannage.
                </p>
            </a>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow border border-gray-100">
                <div class="flex items-center mb-4">
                    <i class="fas fa-file-download text-gray-600 text-3xl mr-4"></i>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Téléchargements</h2>
                </div>
                <div class="space-y-3 text-sm">
                    <a href="{{ route('documentation.manuel.pdf') }}" class="flex items-center font-semibold text-[#A61B29] hover:underline">
                        <i class="fas fa-file-pdf mr-2"></i>Manuel complet PDF (avec illustrations)
                    </a>
                    <a href="{{ route('documentation.download', 'pdf') }}" class="flex items-center text-gray-600 hover:underline">
                        <i class="fas fa-file-pdf mr-2"></i>Archive PDF stockée
                        <span class="text-gray-400 ml-1">(si disponible)</span>
                    </a>
                    <a href="{{ route('equipment.import.template') }}" class="flex items-center text-[#A61B29] hover:underline">
                        <i class="fas fa-file-csv mr-2"></i>Template import équipements
                    </a>
                    <a href="{{ route('parc.import.template') }}" class="flex items-center text-[#A61B29] hover:underline">
                        <i class="fas fa-file-csv mr-2"></i>Template import parc
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-10 doc-prose">
            <h2>Modules de l'application</h2>
            <table>
                <thead>
                    <tr><th>Menu</th><th>Fonction</th></tr>
                </thead>
                <tbody>
                    <tr><td>Rapports</td><td>Vue d'ensemble, équipements, financier, import/export</td></tr>
                    <tr><td>Équipements</td><td>Inventaire, renouvellement, import/export</td></tr>
                    <tr><td>Stocks CELER / DECELER</td><td>Entrées et sorties de stock par famille</td></tr>
                    <tr><td>Gestion</td><td>Parc, réaffectations, maintenance, hors service, perdu</td></tr>
                    <tr><td>EOD Suivi</td><td>Suivi fin de journée</td></tr>
                    <tr><td>Infrastructure IT</td><td>Mots de passe, réseau, licences</td></tr>
                    <tr><td>Change Management</td><td>Tickets N+1, N+2, N+3</td></tr>
                    <tr><td>Contrôles IT</td><td>Campagnes et modèles de contrôle</td></tr>
                    <tr><td>Incidents</td><td>Fiches incidents</td></tr>
                    <tr><td>Configuration</td><td>Agences, catégories, fournisseurs, utilisateurs, audits</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
