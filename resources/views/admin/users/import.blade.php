@extends('layouts.app')

@section('title', 'Importer des profils')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Importer des profils</h2>
                    <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-md">Retour</a>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
                @endif
                @if(session('warning'))
                    <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded">{{ session('warning') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">{{ session('error') }}</div>
                @endif
                @if(session('import_errors'))
                    <div class="mb-4 p-4 bg-white border border-yellow-200 rounded">
                        <p class="font-medium text-gray-800 mb-2">Lignes ignorées :</p>
                        <ul class="text-sm text-gray-700 space-y-1">
                            @foreach(session('import_errors') as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <ol class="mb-6 list-decimal list-inside text-gray-700 space-y-1">
                    <li>Téléchargez le modèle Excel (mêmes colonnes que l’export, sans les dates).</li>
                    <li>Remplissez au minimum Nom et Prénom. L’ordre des colonnes est libre.</li>
                    <li>Importez un fichier .xlsx ou .xls (10 Mo max).</li>
                </ol>

                <div class="mb-6">
                    <a href="{{ route('users.import.template') }}" class="inline-flex px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md">
                        Télécharger un modèle
                    </a>
                </div>

                <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="excel_file" class="block font-medium text-gray-700 mb-1">Fichier Excel *</label>
                        <input id="excel_file" name="excel_file" type="file" accept=".xlsx,.xls" required
                               class="block w-full text-sm text-gray-700 border border-gray-300 rounded-md p-2">
                        @error('excel_file')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md">
                        Importer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
