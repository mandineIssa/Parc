@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('agencies.index') }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour à la liste
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">{{ $agency->nom }}</h1>
                <div class="space-x-2">
                    <a href="{{ route('agencies.edit', $agency) }}" class="text-green-600 hover:text-green-900">
                        Modifier
                    </a>
                </div>
            </div>
            <p class="text-gray-600">Code: <span class="font-bold">{{ $agency->code }}</span></p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Localisation</h3>
                        <p class="mt-1 text-gray-900">
                            <span class="block">{{ $agency->adresse }}</span>
                            <span class="block">{{ $agency->ville }}</span>
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Contact</h3>
                        <div class="mt-2 space-y-2">
                            @if($agency->telephone)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span>{{ $agency->telephone }}</span>
                            </div>
                            @endif

                            @if($agency->email)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <a href="mailto:{{ $agency->email }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $agency->email }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Statistiques</h3>
                    
                    @php
                        $equipmentsCount = $agency->equipments()->count();
                        $parcCount = $agency->equipments()->where('statut', 'parc')->count();
                        $stockCount = $agency->equipments()->where('statut', 'stock')->count();
                    @endphp

                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                            <span class="text-gray-700">Total équipements</span>
                            <span class="font-bold text-blue-600">{{ $equipmentsCount }}</span>
                        </div>

                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <span class="text-gray-700">Équipements en parc</span>
                            <span class="font-bold text-green-600">{{ $parcCount }}</span>
                        </div>

                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-700">Équipements en stock</span>
                            <span class="font-bold text-gray-600">{{ $stockCount }}</span>
                        </div>
                    </div>

                    @if($equipmentsCount > 0)
                    <div class="mt-6">
                        <a href="{{ route('equipment.index', ['agency_id' => $agency->id]) }}" 
                           class="inline-flex items-center text-blue-600 hover:text-blue-900">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Voir les équipements de cette agence
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($equipmentsCount > 0)
        <div class="px-6 py-4 bg-gray-50 border-t">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Équipements récents</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($agency->equipments()->latest()->take(3)->get() as $equipment)
                <div class="bg-white border rounded p-4">
                    <div class="font-medium text-gray-900">{{ $equipment->nom ?? $equipment->type }}</div>
                    <div class="text-sm text-gray-600">{{ $equipment->marque }} {{ $equipment->modele }}</div>
                    <div class="mt-2">
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $equipment->statut == 'parc' ? 'bg-green-100 text-green-800' : 
                               ($equipment->statut == 'stock' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($equipment->statut) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection