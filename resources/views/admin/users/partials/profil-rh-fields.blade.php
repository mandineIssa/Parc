{{-- resources/views/admin/users/partials/profil-rh-fields.blade.php --}}
@php
    $selectedFiliale = old('filiale_id', isset($user) ? $user->filiale_id : null);
    $selectedAgency = old('agency_id', isset($user) ? $user->agency_id : null);
    $selectedN1 = old('n_plus_1_id', isset($user) ? $user->n_plus_1_id : null);
    $selectedN2 = old('n_plus_2_id', isset($user) ? $user->n_plus_2_id : null);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="matricule" class="block font-medium text-gray-700">Matricule</label>
        <input id="matricule" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="matricule" value="{{ old('matricule', $user->matricule ?? '') }}" />
        <p class="mt-1 text-xs text-gray-500">Laissé vide, il est généré automatiquement.</p>
        @error('matricule')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="telephone" class="block font-medium text-gray-700">Téléphone</label>
        <input id="telephone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="telephone" value="{{ old('telephone', $user->telephone ?? '') }}" />
        @error('telephone')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="type_contrat" class="block font-medium text-gray-700">Type de contrat</label>
        <select id="type_contrat" name="type_contrat" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @foreach(['CDI', 'CDD', 'Stagiaire', 'Autre'] as $contrat)
                <option value="{{ $contrat }}" {{ old('type_contrat', $user->type_contrat ?? 'CDI') === $contrat ? 'selected' : '' }}>{{ $contrat }}</option>
            @endforeach
        </select>
        @error('type_contrat')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="statut" class="block font-medium text-gray-700">Statut</label>
        <select id="statut" name="statut" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="actif" {{ old('statut', $user->statut ?? 'actif') === 'actif' ? 'selected' : '' }}>actif</option>
            <option value="inactif" {{ old('statut', $user->statut ?? 'actif') === 'inactif' ? 'selected' : '' }}>inactif</option>
        </select>
        @error('statut')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="filiale_id" class="block font-medium text-gray-700">Filiale</label>
        <select id="filiale_id" name="filiale_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">—</option>
            @foreach($filiales ?? [] as $filiale)
                <option value="{{ $filiale->id }}" {{ (string) $selectedFiliale === (string) $filiale->id ? 'selected' : '' }}>{{ $filiale->nom }}</option>
            @endforeach
        </select>
        @error('filiale_id')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="agency_id" class="block font-medium text-gray-700">Site / Agence</label>
        <select id="agency_id" name="agency_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">—</option>
            @foreach($agencies ?? [] as $agency)
                <option value="{{ $agency->id }}" {{ (string) $selectedAgency === (string) $agency->id ? 'selected' : '' }}>{{ $agency->nom }}</option>
            @endforeach
        </select>
        @error('agency_id')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="n_plus_1_id" class="block font-medium text-gray-700">N+1</label>
        <select id="n_plus_1_id" name="n_plus_1_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">—</option>
            @foreach($managers ?? [] as $manager)
                <option value="{{ $manager->id }}" {{ (string) $selectedN1 === (string) $manager->id ? 'selected' : '' }}>
                    {{ $manager->name }} {{ $manager->prenom }} @if($manager->matricule)({{ $manager->matricule }})@endif
                </option>
            @endforeach
        </select>
        @error('n_plus_1_id')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="n_plus_2_id" class="block font-medium text-gray-700">N+2</label>
        <select id="n_plus_2_id" name="n_plus_2_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">—</option>
            @foreach($managers ?? [] as $manager)
                <option value="{{ $manager->id }}" {{ (string) $selectedN2 === (string) $manager->id ? 'selected' : '' }}>
                    {{ $manager->name }} {{ $manager->prenom }} @if($manager->matricule)({{ $manager->matricule }})@endif
                </option>
            @endforeach
        </select>
        @error('n_plus_2_id')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
