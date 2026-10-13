@extends('layouts.app')

@section('title', 'Modifier la filiale')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <a href="{{ route('filiales.index') }}" class="text-blue-600 hover:text-blue-900">← Retour à la liste</a>
    <div class="bg-white shadow-md rounded-lg p-6 mt-4">
        <h1 class="text-xl font-bold mb-4">Modifier {{ $filiale->nom }}</h1>
        <form action="{{ route('filiales.update', $filiale) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="nom" class="block text-gray-700 text-sm font-bold mb-2">Nom *</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom', $filiale->nom) }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                @error('nom')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <label class="inline-flex items-center mb-6">
                <input type="hidden" name="actif" value="0">
                <input type="checkbox" name="actif" value="1" {{ old('actif', $filiale->actif) ? 'checked' : '' }} class="rounded border-gray-300">
                <span class="ml-2 text-sm text-gray-700">Filiale active</span>
            </label>
            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection
