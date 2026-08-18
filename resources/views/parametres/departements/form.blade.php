<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-2xl bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h1 class="text-xl font-semibold text-gray-800 mb-6">{{ isset($departement) ? 'Modifier le département' : 'Nouveau département' }}</h1>
        <form method="POST" action="{{ isset($departement) ? route('parametres.departements.update', $departement) : route('parametres.departements.store') }}" class="space-y-4">
            @csrf
            @if(isset($departement))
                @method('PUT')
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                <input type="text" name="nom" required maxlength="100"
                       value="{{ old('nom', $departement->nom ?? '') }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-[#C8102E] focus:border-[#C8102E]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <input type="text" name="description" maxlength="255"
                       value="{{ old('description', $departement->description ?? '') }}"
                       class="w-full rounded-lg border-gray-300 focus:ring-[#C8102E] focus:border-[#C8102E]">
            </div>
            <div>
                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="actif" value="0">
                    <input type="checkbox" name="actif" value="1" class="rounded border-gray-300 text-[#C8102E]"
                           {{ old('actif', $departement->actif ?? true) ? 'checked' : '' }}>
                    Actif
                </label>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('parametres.departements.index') }}" class="px-4 py-2 rounded-md bg-gray-100 text-gray-700 font-semibold">Annuler</a>
                <button type="submit" class="px-4 py-2 rounded-md bg-[#C8102E] hover:bg-[#a00d24] text-white font-semibold">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
