// Puis dans resources/views/documentation.blade.php :
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Documentation Complète</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Guide Utilisateur</h2>
            <p>Documentation pour les utilisateurs standard...</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Guide Administrateur</h2>
            <p>Documentation pour les administrateurs...</p>
        </div>
    </div>
</div>
@endsection