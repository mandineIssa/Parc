@extends('layouts.app')

@section('content')
    @php
        // Ancien fichier conservé : redirection vers la documentation complète
    @endphp
    <script>window.location.href = @json(route('documentation.index'));</script>
    <meta http-equiv="refresh" content="0;url={{ route('documentation.index') }}">
    <p class="p-8 text-center">
        <a href="{{ route('documentation.index') }}" class="text-[#A61B29] underline font-medium">
            Accéder à la documentation complète
        </a>
    </p>
@endsection
