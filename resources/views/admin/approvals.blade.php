@extends('layouts.app')

@section('title', 'Approbations en attente')
@section('header', 'Gestion des Approbations')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-cofina-red mb-2 flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd"></path>
                    </svg>
                    Approbations en attente
                </h1>
                <p class="text-gray-600">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    Total: <span class="font-bold text-cofina-red">{{ $approvals->total() }}</span> demandes
                </p>
            </div>
            <div class="text-right">
                <a href="{{ route('admin.dashboard') }}" class="btn-cofina-outline flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Messages de statut -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded flex items-start">
        <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <p class="text-green-700 font-bold">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded flex items-start">
        <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        <p class="text-red-700 font-bold">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Filtres -->
    <div class="card-cofina mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.approvals', ['status' => 'pending']) }}" 
                   class="px-4 py-2 rounded flex items-center {{ request('status') == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' }}">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    En attente
                </a>
                <a href="{{ route('admin.approvals', ['status' => 'approved']) }}" 
                   class="px-4 py-2 rounded flex items-center {{ request('status') == 'approved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Approuvées
                </a>
                <a href="{{ route('admin.approvals', ['status' => 'rejected']) }}" 
                   class="px-4 py-2 rounded flex items-center {{ request('status') == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600' }}">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Rejetées
                </a>
                <a href="{{ route('admin.approvals') }}" 
                   class="px-4 py-2 rounded flex items-center {{ !request('status') ? 'bg-cofina-red text-white' : 'bg-gray-100 text-gray-600' }}">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                    </svg>
                    Toutes
                </a>
            </div>
            
            <!-- Recherche -->
            <form method="GET" action="{{ route('admin.approvals') }}" class="flex gap-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Rechercher..." 
                           class="pl-10 pr-4 py-2 border-2 border-cofina-gray rounded w-full md:w-64">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <button type="submit" class="btn-cofina-primary flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Chercher
                </button>
            </form>
        </div>
    </div>

    <!-- Tableau des approbations -->
    <div class="card-cofina">
        @forelse($approvals as $approval)
            <!-- Pas la première itération = ajouter un séparateur -->
            @if(!$loop->first)
                <hr class="my-4 border-cofina-gray">
            @endif

            <div class="py-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                    <!-- ID et Statut -->
                    <div>
                        <p class="text-sm text-gray-600 font-semibold flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            N° Demande
                        </p>
                        <p class="text-xl font-bold text-cofina-red">
                            #{{ $approval->formatted_id ?? str_pad($approval->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $approval->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <!-- Équipement -->
                    <div>
                        <p class="text-sm text-gray-600 font-semibold flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Équipement
                        </p>
                        <p class="font-bold">{{ $approval->equipment->nom ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $approval->equipment->numero_serie ?? 'N/A' }}
                        </p>
                    </div>

                    <!-- Demandeur -->
                    <div>
                        <p class="text-sm text-gray-600 font-semibold flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            Demandeur
                        </p>
                        <p class="font-bold">{{ $approval->submitter->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                            {{ $approval->submitter->email ?? 'N/A' }}
                        </p>
                    </div>

                    <!-- Type de transition -->
                    <div>
                        <p class="text-sm text-gray-600 font-semibold flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            Transition
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm font-semibold">
                                {{ strtoupper($approval->from_status) }}
                            </span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm font-semibold">
                                {{ strtoupper($approval->to_status) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            @php
                                $data = json_decode($approval->data, true);
                                $formType = $data['form_type'] ?? 'simple';
                            @endphp
                            @if($formType === 'multi_step')
                                <span class="inline-flex items-center px-2 py-0.5 bg-purple-100 text-purple-800 rounded">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Formulaire 3 étapes
                                </span>
                            @elseif($formType === 'installation')
                                <span class="inline-flex items-center px-2 py-0.5 bg-blue-100 text-blue-800 rounded">
                                    🖥️ Fiche d'installation
                                </span>
                            @elseif($formType === 'mouvement')
                                <span class="inline-flex items-center px-2 py-0.5 bg-green-100 text-green-800 rounded">
                                    📄 Fiche de mouvement
                                </span>
                            @endif
                        </p>
                    </div>

                    <!-- Statut -->
                    <div>
                        <p class="text-sm text-gray-600 font-semibold">Statut</p>
                        @if($approval->status === 'pending')
                            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 font-bold inline-flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                En attente
                            </span>
                            @if($approval->created_at->diffInDays(now()) > 3)
                                <p class="text-xs text-red-600 mt-1 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    En attente depuis {{ $approval->created_at->diffInDays(now()) }} jours
                                </p>
                            @endif
                        @elseif($approval->status === 'approved')
                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 font-bold inline-flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Approuvé
                            </span>
                            <p class="text-xs text-gray-500 mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                Par: {{ $approval->approver->name ?? 'N/A' }}
                            </p>
                        @elseif($approval->status === 'rejected')
                            <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 font-bold inline-flex items-center mt-1">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                Rejeté
                            </span>
                            <p class="text-xs text-gray-500 mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                Par: {{ $approval->approver->name ?? 'N/A' }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Informations supplémentaires -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                Utilisateur final
                            </p>
                            @php
                                $data = json_decode($approval->data, true);
                                // Gestion des différentes structures de données
                                if (isset($data['affectation_data'])) {
                                    // Structure multi_step (3 étapes)
                                    $affectationData = $data['affectation_data'];
                                    $userName = $affectationData['responsable_name'] ?? 'N/A';
                                    $department = $affectationData['department'] ?? 'N/A';
                                    $position = $affectationData['position'] ?? 'N/A';
                                    $affectationDate = $affectationData['affectation_date'] ?? 'N/A';
                                } else {
                                    // Structure simple ou ancienne
                                    $userName = $data['user_name'] ?? ($data['utilisateur_nom'] ?? 'N/A');
                                    $department = $data['departement'] ?? ($data['destination'] ?? 'N/A');
                                    $position = $data['poste_affecte'] ?? ($data['receptionnaire_fonction'] ?? 'N/A');
                                    $affectationDate = $data['date_affectation'] ?? ($data['date_expediteur'] ?? 'N/A');
                                }
                            @endphp
                            <p class="font-bold">
                                {{ $userName }}
                            </p>
                            <p class="text-sm text-gray-600 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $department }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-semibold flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Poste
                            </p>
                            <p class="font-bold">
                                {{ $position }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-semibold flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                Date prévue
                            </p>
                            <p class="font-bold">
                                @if($affectationDate && $affectationDate !== 'N/A')
                                    {{ \Carbon\Carbon::parse($affectationDate)->format('d/m/Y') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <!-- Détails des fiches incluses -->
                    @php
                        $data = json_decode($approval->data, true);
                        $formType = $data['form_type'] ?? 'simple';
                        $choixFiches = $data['choix_fiches'] ?? [];
                    @endphp
                    
                    @if($formType === 'multi_step')
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600 font-semibold mb-2">Fiches incluses :</p>
                            <div class="flex flex-wrap gap-2">
                             <a href="{{ route('transitions.fiche-installation.download', $approval->id) }}" 
                               class="btn-cofina-outline flex items-center gap-2 justify-center hover:bg-red-50 hover:border-red-500 transition-colors">
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Installation (Étape 1)
                                </span>
                                </a>
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    Affectation (Étape 2)
                                </span>
                             <a href="{{ route('transitions.fiche-mouvement.download', $approval->id) }}" 
                                class="btn-cofina-outline flex items-center gap-2 justify-center hover:bg-red-50 hover:border-red-500 transition-colors">
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                    </svg>
                                    Mouvement (Étape 3)
                                </span>
                             </a>
                            </div>
                        </div>
                    @elseif(!empty($choixFiches))
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600 font-semibold mb-2">Fiches incluses :</p>
                            <div class="flex flex-wrap gap-2">
                                @if(in_array('installation', $choixFiches))
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                                    🖥️ Fiche d'installation
                                </span>
                                @endif
                                @if(in_array('mouvement', $choixFiches))
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800">
                                    📄 Fiche de mouvement
                                </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
<!-- Actions -->
<div class="flex flex-col sm:flex-row gap-3 mt-6 pt-4 border-t border-gray-200">
    @if($approval->status === 'pending')
        <!-- Voir détails simples -->
        <a href="{{ route('admin.approvals.show', $approval) }}" 
        class="btn-cofina-primary flex-1 text-center flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Voir détails
        </a>

        <!-- Traiter (aller vers les fiches) -->
        <a href="{{ route('transitions.approval.show', $approval) }}" 
        class="btn-cofina-success flex-1 text-center flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Compléter les fiches
        </a>
        
        <!-- Rejeter avec modal -->
        <button type="button" 
                onclick="showRejectionModal({{ $approval->id }})"
                class="btn-cofina-danger flex-1 py-3 flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
            </svg>
            Rejeter
        </button>
    @elseif($approval->status === 'approved')
        <!-- Pour les demandes approuvées -->
        <div class="flex flex-col sm:flex-row gap-3 w-full">
            <!-- Voir détails -->
            <a href="{{ route('admin.approvals.show', $approval) }}" 
            class="btn-cofina-primary flex-1 text-center flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Voir détails
            </a>
            
            <!-- Télécharger les fiches -->
            <a href="{{ route('transitions.approval.download', $approval) }}" 
            class="btn-cofina-success flex-1 text-center flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Télécharger PDF
            </a>
            
            <!-- Voir l'équipement -->
            <a href="{{ route('equipment.show', $approval->equipment_id) }}" 
            class="btn-cofina flex-1 text-center flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Voir équipement
            </a>
        </div>
    @elseif($approval->status === 'rejected')
        <!-- Pour les demandes rejetées -->
        <div class="flex flex-col sm:flex-row gap-3 w-full">
            <!-- Voir détails -->
            <a href="{{ route('admin.approvals.show', $approval) }}" 
            class="btn-cofina-primary flex-1 text-center flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Voir détails
            </a>
            
            <!-- Raison du rejet -->
            <button type="button" 
                    onclick="showRejectionReason({{ $approval->id }}, '{{ addslashes($approval->rejection_reason) }}')"
                    class="btn-cofina-outline flex-1 text-center flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Voir raison
            </button>
            
            <!-- Voir l'équipement -->
            <a href="{{ route('equipment.show', $approval->equipment_id) }}" 
            class="btn-cofina flex-1 text-center flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Voir équipement
            </a>
        </div>
    @endif
</div>
            </div>
        @empty
            <!-- Aucune approbation -->
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-xl font-bold text-gray-600 mb-2">
                    Aucune approbation trouvée
                </p>
                <p class="text-gray-500">
                    @if(request('status') || request('search'))
                        Aucun résultat pour vos critères de recherche.
                    @else
                        Toutes les demandes ont été traitées !
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($approvals->total() > 0)
    <div class="mt-8">
        {{ $approvals->links() }}
    </div>
    @endif

</div>

<!-- Modal pour saisir la raison du rejet -->
<div id="rejectionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form method="POST" action="" id="rejectForm">
            @csrf
            <div class="mt-3 text-center">
                <h3 class="text-lg font-bold text-cofina-red mb-4 flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    Raison du rejet
                </h3>
                <div class="mt-2 px-7 py-3">
                    <textarea name="raison_rejet" 
                              id="modalRejectionReason" 
                              class="w-full border border-gray-300 rounded p-2" 
                              rows="4" 
                              placeholder="Veuillez indiquer la raison du rejet..."
                              required></textarea>
                    <p class="text-sm text-gray-500 mt-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Ce champ est obligatoire pour rejeter la demande.
                    </p>
                </div>
                <div class="flex gap-2 justify-center mt-4">
                    <button type="submit" 
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 font-bold flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Confirmer le rejet
                    </button>
                    <button type="button" 
                            onclick="closeModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Annuler
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Variables globales
let currentApprovalId = null;

function showRejectionModal(approvalId) {
    currentApprovalId = approvalId;
    
    // Mettre à jour l'action du formulaire
    const form = document.getElementById('rejectForm');
    form.action = `/equipment/${approvalId}/transition/reject`;
    
    // Afficher la modal
    document.getElementById('rejectionModal').classList.remove('hidden');
    document.getElementById('modalRejectionReason').focus();
}

function closeModal() {
    document.getElementById('rejectionModal').classList.add('hidden');
    document.getElementById('modalRejectionReason').value = '';
    currentApprovalId = null;
}

// Validation du formulaire
document.getElementById('rejectForm').addEventListener('submit', function(e) {
    const reason = document.getElementById('modalRejectionReason').value.trim();
    
    if (!reason) {
        e.preventDefault();
        alert('Veuillez saisir une raison pour le rejet.');
        document.getElementById('modalRejectionReason').focus();
        return;
    }
    
    // Confirmation supplémentaire
    if (!confirm('Êtes-vous sûr de vouloir rejeter cette demande ? Cette action est irréversible.')) {
        e.preventDefault();
        return;
    }
    
    // Ajouter un indicateur de chargement
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalHtml = submitBtn.innerHTML;
    submitBtn.innerHTML = '<svg class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Traitement...';
    submitBtn.disabled = true;
    
    // Restaurer en cas d'erreur
    setTimeout(() => {
        submitBtn.innerHTML = originalHtml;
        submitBtn.disabled = false;
    }, 5000);
});

// Fermer la modal avec la touche Échap
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});

// Empêcher la fermeture en cliquant à l'extérieur
document.getElementById('rejectionModal').addEventListener('click', function(event) {
    if (event.target === this) {
        const reason = document.getElementById('modalRejectionReason').value.trim();
        if (reason) {
            if (confirm('Annuler le rejet ? Les données saisies seront perdues.')) {
                closeModal();
            }
        } else {
            closeModal();
        }
    }
});

// Fonction pour afficher la raison du rejet
function showRejectionReason(approvalId, reason) {
    const modalHtml = `
        <div class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="rejectionReasonModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-bold text-cofina-red mb-4 flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Raison du rejet
                    </h3>
                    <div class="mt-2 px-7 py-3">
                        <div class="bg-gray-50 p-4 rounded border border-gray-300 text-left">
                            <p class="text-gray-700 whitespace-pre-wrap">${reason || 'Aucune raison spécifiée'}</p>
                        </div>
                    </div>
                    <div class="flex gap-2 justify-center mt-4">
                        <button type="button" 
                                onclick="closeRejectionReasonModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter la modal au DOM si elle n'existe pas
    if (!document.getElementById('rejectionReasonModal')) {
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    
    // Afficher la modal
    document.getElementById('rejectionReasonModal').classList.remove('hidden');
}

// Fonction pour fermer la modal de raison
function closeRejectionReasonModal() {
    const modal = document.getElementById('rejectionReasonModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Gestion de la touche Échap pour la modal de raison
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeRejectionReasonModal();
    }
});
</script>

<style>
#rejectionModal {
    backdrop-filter: blur(2px);
}

#rejectionModal > div {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Améliorations pour les boutons */
.btn-cofina-danger {
    transition: all 0.2s ease;
}

.btn-cofina-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(220, 38, 38, 0.25);
}

/* Animation de spin */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endsection