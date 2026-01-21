@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')

<div class="p-4 sm:p-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">

        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-xs sm:text-sm text-gray-500 mb-2">Total équipements</div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $totalEquipments }}</div>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-xs sm:text-sm text-gray-500 mb-2">En stock</div>
            <div class="text-2xl sm:text-3xl font-bold text-blue-600">{{ $stockEquipments }}</div>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-xs sm:text-sm text-gray-500 mb-2">En parc</div>
            <div class="text-2xl sm:text-3xl font-bold text-green-600">{{ $parcEquipments }}</div>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-xs sm:text-sm text-gray-500 mb-2">En maintenance</div>
            <div class="text-2xl sm:text-3xl font-bold text-orange-600">{{ $maintenanceEquipments }}</div>
        </div>

    </div>


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">

        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-xs sm:text-sm text-gray-500 mb-2">Stock CELER</div>
            <div class="text-2xl sm:text-3xl font-bold text-green-700">{{ $celerStock }}</div>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-xs sm:text-sm text-gray-500 mb-2">Stock DECELER</div>
            <div class="text-2xl sm:text-3xl font-bold text-yellow-600">{{ $decelerStock }}</div>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-xs sm:text-sm text-gray-500 mb-2">Journal d'audit</div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $auditCount }}</div>
        </div>
        
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-xs sm:text-sm text-gray-500 mb-2">Perdu</div>
            <div class="text-2xl sm:text-3xl font-bold text-orange-600">{{ $perduEquipments }}</div>
        </div>

    </div>
</div>

@endsection
