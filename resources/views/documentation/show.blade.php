@extends('layouts.app')

@section('title', 'Documentation - ' . ($title ?? 'Section'))

@section('content')
@include('documentation.partials.styles')

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap gap-4 items-center justify-between">
            <a href="{{ route('documentation.index') }}"
               class="inline-flex items-center text-[#A61B29] hover:text-[#7A0C1A] font-medium">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour à la documentation
            </a>
            <a href="{{ route('documentation.manuel.pdf') }}"
               class="inline-flex items-center bg-[#A61B29] hover:bg-[#7A0C1A] text-white text-sm font-semibold px-4 py-2 rounded-lg">
                <i class="fas fa-file-pdf mr-2"></i>
                Télécharger le manuel PDF
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            @switch($section)
                @case('utilisateur')
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-user text-blue-600 mr-3"></i>Guide Utilisateur
                    </h1>
                    @include('documentation.sections.utilisateur')
                    @break

                @case('agent-it')
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-laptop-code text-green-600 mr-3"></i>Guide Agent IT
                    </h1>
                    @include('documentation.sections.agent-it')
                    @break

                @case('manuel-complet')
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-book-open text-[#A61B29] mr-3"></i>Manuel d'utilisation complet
                    </h1>
                    @include('documentation.sections.manuel-complet')
                    @break

                @case('admin')
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-user-shield text-purple-600 mr-3"></i>Guide Administrateur
                    </h1>
                    @include('documentation.sections.admin')
                    @break

                @case('api')
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-code text-orange-600 mr-3"></i>Documentation technique
                    </h1>
                    @include('documentation.sections.api')
                    @break

                @case('installation')
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-download text-red-600 mr-3"></i>Guide d'installation
                    </h1>
                    @include('documentation.sections.installation')
                    @break

                @default
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                        <i class="fas fa-book text-gray-600 mr-3"></i>
                        Documentation — {{ ucfirst(str_replace('-', ' ', $section)) }}
                    </h1>
                    <div class="doc-box-warn">
                        <p class="mb-0">Section inconnue. <a href="{{ route('documentation.index') }}" class="text-[#A61B29] underline">Retour à l'accueil documentation</a>.</p>
                    </div>
            @endswitch
        </div>
    </div>
</div>
@endsection
