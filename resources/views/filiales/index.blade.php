@extends('layouts.app')

@section('title', 'Filiales')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Filiales</h1>
        <a href="{{ route('filiales.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition">
            Nouvelle filiale
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">{{ session('error') }}</div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agences</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateurs</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($filiales as $filiale)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $filiale->nom }}</td>
                        <td class="px-6 py-4">
                            @if($filiale->actif)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $filiale->agencies_count }}</td>
                        <td class="px-6 py-4">{{ $filiale->users_count }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('filiales.edit', $filiale) }}" class="text-green-600 hover:text-green-800 mr-3">Modifier</a>
                            <form action="{{ route('filiales.destroy', $filiale) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette filiale ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Aucune filiale.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $filiales->links() }}</div>
</div>
@endsection
