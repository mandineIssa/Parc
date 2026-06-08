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
                    @include('documentation.sections.utilisateur')
                    @break

                @case('agent-it')
                    @include('documentation.sections.agent-it')
                    @break

                @case('manuel-complet')
                    @include('documentation.sections.manuel-complet')
                    @break

                @case('admin')
                    @include('documentation.sections.admin')
                    @break

                @case('api')
                    @include('documentation.sections.api')
                    @break

                @case('installation')
                    @include('documentation.sections.installation')
                    @break

                @default
                    <div class="doc-box-warn">
                        <p class="mb-0">Section inconnue. <a href="{{ route('documentation.index') }}" class="text-[#A61B29] underline">Retour à l'accueil documentation</a>.</p>
                    </div>
            @endswitch
        </div>
    </div>
</div>
@endsection
