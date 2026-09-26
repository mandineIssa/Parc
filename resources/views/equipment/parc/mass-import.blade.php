{{-- resources/views/equipment/parc/mass-import.blade.php --}}
@extends('layouts.app')

@section('title', 'Import en masse Parc')
@section('header', 'Import en masse — Parc informatique')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-end mb-6">
        <a href="{{ route('parc.index') }}" class="text-gray-600 hover:text-gray-900 inline-flex items-center">
            ← Retour au parc
        </a>
    </div>

    @if(session('mass_import_stats'))
        @php
            $stats = session('mass_import_stats');
            $perfect = (bool) session('mass_import_perfect');
        @endphp
        <div class="{{ $perfect ? 'bg-green-50 border-green-300' : 'bg-amber-50 border-amber-200' }} border rounded-xl p-6 mb-6">
            @if($perfect)
                <p class="text-2xl font-bold text-green-800 mb-1">Import réussi à 100 %</p>
                <p class="text-sm text-green-700 mb-4">Tous les équipements avec un numéro de série ont été importés.</p>
            @else
                <p class="font-semibold text-amber-900 mb-3">Résultat de l'import</p>
            @endif
            <ul class="text-sm {{ $perfect ? 'text-green-800' : 'text-amber-900' }} space-y-1">
                <li>Créés : <strong>{{ $stats['created'] }}</strong></li>
                <li>Mis à jour : <strong>{{ $stats['updated'] }}</strong></li>
                <li>Ignorés : <strong>{{ $stats['ignored'] }}</strong></li>
            </ul>
            @if(($stats['ignored'] ?? 0) > 0)
                <a href="{{ route('parc.mass-import.ignored') }}"
                   class="mt-4 inline-flex items-center px-4 py-2 bg-[#C8102E] hover:bg-[#a00d24] text-white text-sm font-semibold rounded-lg">
                    Télécharger le fichier des équipements ignorés
                </a>
            @endif
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-[#C8102E] px-6 py-4">
            <h2 class="text-lg font-semibold text-white">Importer le fichier Excel Parc</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('parc.mass-import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-1">Fichier Excel (.xlsx)</label>
                    <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls" required
                           class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg p-2">
                    @error('excel_file')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="bg-[#C8102E] hover:bg-[#a00d24] text-white font-semibold py-2 px-5 rounded-lg">
                        Lancer l'import
                    </button>
                    <a href="{{ route('parc.export', ['filtre_rapide' => 'informatique']) }}"
                       class="bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2 px-5 rounded-lg">
                        Télécharger le modèle (export)
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 text-sm text-gray-800 space-y-2">
        <p class="font-semibold text-blue-900">Même format que « Export en masse »</p>
        <ul class="list-disc pl-5 space-y-1">
            <li>Feuille <strong>Parc</strong> avec les colonnes : NOM, PRENOM, AGENCE, Departeme, POSTE, Dotation, NOM DE L'EQUIPEMENT, serial number, Marque/Modele, Model PC, dates, prix, Fournisseur, État.</li>
            <li>Import réservé aux équipements de type <strong>Informatique</strong>.</li>
            <li>Les lignes sans numéro de série sont <strong>ignorées</strong> (rapport Excel généré avec le motif).</li>
            <li>Si le numéro de série existe, l'équipement et l'affectation parc sont <strong>mis à jour</strong> ; sinon ils sont <strong>créés</strong> en statut parc.</li>
            <li>Une cellule vide <strong>remplace</strong> la valeur déjà enregistrée (nom, prénom, agence, etc.).</li>
        </ul>
        <p class="text-gray-600">Cette fonction est distincte de « Importation d'Équipements » (stock / multi-onglets).</p>
    </div>
</div>
@endsection

@push('scripts')
@if(session('mass_import_perfect'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swal === 'undefined') {
            return;
        }
        Swal.fire({
            icon: 'success',
            title: 'Import réussi à 100 %',
            text: {!! json_encode((string) session('mass_import_message')) !!},
            confirmButtonText: 'OK',
            confirmButtonColor: '#16a34a'
        });
    });
</script>
@endif
@endpush
