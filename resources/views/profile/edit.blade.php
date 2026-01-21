@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">
            {{ __('Profile') }}
        </h2>

        <div class="space-y-6">
            <!-- Informations du profil -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Changer le mot de passe -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Supprimer le compte -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection