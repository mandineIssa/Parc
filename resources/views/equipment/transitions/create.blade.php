@extends('layouts.app')

@section('title', 'Transition d\'État')
@section('header', 'Changer l\'état de l\'équipement')

@section('content')
<div class="card-cofina max-w-4xl">
    <div class="mb-6">
        <h3 class="text-lg font-bold text-cofina-red mb-2">Équipement: {{ $equipment->nom }}</h3>
        <p class="text-gray-600">
            N° Série: <span class="font-bold">{{ $equipment->numero_serie }}</span> | 
            Statut actuel: <span class="font-bold text-{{ $equipment->statut == 'stock' ? 'green' : 'blue' }}-600">{{ ucfirst($equipment->statut) }}</span>
        </p>
    </div>
    
    <!-- Carte des transitions disponibles -->
    <div class="mb-8">
        <h4 class="text-xl font-bold text-cofina-red mb-4">Transitions disponibles</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($equipment->statut == 'stock')
                <!-- Stock → Parc -->
                <div class="transition-card" data-target="stock-to-parc">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg mr-4">
                            📦→👨‍💼
                        </div>
                        <div>
                            <h5 class="font-bold text-lg">Affecter au Parc</h5>
                            <p class="text-sm text-gray-600">Sortir du stock pour affectation</p>
                        </div>
                    </div>
                </div>
                
                <!-- Stock → Hors Service -->
                <div class="transition-card" data-target="stock-to-hors-service">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-lg mr-4">
                            📦→❌
                        </div>
                        <div>
                            <h5 class="font-bold text-lg">Mettre Hors Service</h5>
                            <p class="text-sm text-gray-600">Équipement neuf défectueux</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($equipment->statut == 'parc')
                <!-- Parc → Maintenance -->
                <div class="transition-card" data-target="parc-to-maintenance">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg mr-4">
                            👨‍💼→🔧
                        </div>
                        <div>
                            <h5 class="font-bold text-lg">Envoyer en Maintenance</h5>
                            <p class="text-sm text-gray-600">Pour réparation ou entretien</p>
                        </div>
                    </div>
                </div>
                
                <!-- Parc → Hors Service -->
                <div class="transition-card" data-target="parc-to-hors-service">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-lg mr-4">
                            👨‍💼→❌
                        </div>
                        <div>
                            <h5 class="font-bold text-lg">Mettre Hors Service</h5>
                            <p class="text-sm text-gray-600">Irréparable ou obsolète</p>
                        </div>
                    </div>
                </div>
                
                <!-- Parc → Perdu -->
                <div class="transition-card" data-target="parc-to-perdu">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-lg mr-4">
                            👨‍💼→🔍
                        </div>
                        <div>
                            <h5 class="font-bold text-lg">Déclarer Perdu</h5>
                            <p class="text-sm text-gray-600">Vol, perte ou disparition</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($equipment->statut == 'maintenance')
                <!-- Maintenance → Stock -->
                <div class="transition-card" data-target="maintenance-to-stock">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg mr-4">
                            🔧→📦
                        </div>
                        <div>
                            <h5 class="font-bold text-lg">Retour au Stock</h5>
                            <p class="text-sm text-gray-600">Maintenance terminée</p>
                        </div>
                    </div>
                </div>
                
                <!-- Maintenance → Hors Service -->
                <div class="transition-card" data-target="maintenance-to-hors-service">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-lg mr-4">
                            🔧→❌
                        </div>
                        <div>
                            <h5 class="font-bold text-lg">Déclarer Irréparable</h5>
                            <p class="text-sm text-gray-600">Coût de réparation trop élevé</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Formulaire dynamique -->
    <div id="transition-form-container">
        <!-- Les formulaires s'affichent ici dynamiquement -->
    </div>
    
    <!-- Bouton retour -->
    <div class="mt-8 pt-6 border-t border-cofina-gray">
        <a href="{{ route('equipment.show', $equipment) }}" class="btn-cofina-outline">
            ↩️ Retour à la fiche
        </a>
    </div>
</div>

<!-- Template pour chaque formulaire de transition -->
@include('transitions.partials.stock-to-parc')
@include('transitions.partials.parc-to-maintenance')
@include('transitions.partials.maintenance-to-stock')
@include('transitions.partials.parc-to-hors-service')
@include('transitions.partials.parc-to-perdu')
@include('transitions.partials.stock-to-hors-service')
@include('transitions.partials.maintenance-to-hors-service')

<style>
.transition-card {
    @apply p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all duration-200;
}
.transition-card:hover {
    @apply border-cofina-red bg-red-50 transform scale-105;
}
.transition-form {
    @apply hidden p-6 border-2 border-cofina-gray rounded-lg bg-gray-50;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.transition-card');
    const container = document.getElementById('transition-form-container');
    
    cards.forEach(card => {
        card.addEventListener('click', function() {
            const target = this.dataset.target;
            
            // Masquer tous les formulaires
            document.querySelectorAll('.transition-form').forEach(form => {
                form.classList.add('hidden');
            });
            
            // Afficher le formulaire correspondant
            const form = document.getElementById(target + '-form');
            if (form) {
                form.classList.remove('hidden');
                container.innerHTML = '';
                container.appendChild(form.cloneNode(true));
                
                // Scroll vers le formulaire
                container.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});
</script>
@endsection
