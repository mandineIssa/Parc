{{-- resources/views/passwords/_partials/expiration_description.blade.php --}}
{{-- Bloc Expiration + Description commun à tous les formulaires --}}

<div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Expiration & Renouvellement</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
            <input type="date" name="date_expiration"
                   value="{{ old('date_expiration', isset($password) ? $password->date_expiration?->format('Y-m-d') : '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Durée de renouvellement (jours)</label>
            <input type="number" name="duree_renouvellement" min="1" placeholder="ex: 90"
                   value="{{ old('duree_renouvellement', isset($password) ? $password->duree_renouvellement : '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Description complémentaire</h3>
    <textarea name="description" rows="3" placeholder="Informations complémentaires, notes d'exploitation…"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">{{ old('description', isset($password) ? $password->description : '') }}</textarea>
</div>
