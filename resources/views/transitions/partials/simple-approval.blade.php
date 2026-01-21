<!-- Formulaire SIMPLE pour seulement fiche de mouvement -->
<form method="POST" action="{{ route('transitions.approve', $approval) }}" class="space-y-8">
    @csrf

    <div class="card-cofina">
        <h2 class="text-2xl font-bold text-cofina-red mb-6 border-b-2 border-cofina-red pb-3">
            📋 Validation simple
        </h2>

        <div class="mb-6">
            <label class="block font-bold text-cofina-red mb-3">
                ☑️ Checklist de validation
            </label>
            <div class="space-y-3 ml-4">
                <label class="flex items-start">
                    <input type="checkbox" name="checklist[mouvement_rempli]" value="1" required
                           class="h-5 w-5 text-cofina-red rounded mt-1 flex-shrink-0">
                    <span class="ml-3 font-semibold">Fiche de mouvement remplie et signée</span>
                </label>
                <label class="flex items-start">
                    <input type="checkbox" name="checklist[materiel_verifie]" value="1" required
                           class="h-5 w-5 text-cofina-red rounded mt-1 flex-shrink-0">
                    <span class="ml-3 font-semibold">Matériel vérifié et en bon état</span>
                </label>
                <label class="flex items-start">
                    <input type="checkbox" name="checklist[signatures_ok]" value="1" required
                           class="h-5 w-5 text-cofina-red rounded mt-1 flex-shrink-0">
                    <span class="ml-3 font-semibold">Signatures complètes</span>
                </label>
            </div>
        </div>

        <div class="mb-6">
            <label for="signature_super_admin" class="block font-bold text-cofina-red mb-2">
                ✍️ Signature Super Admin *
            </label>
            <input type="text" name="signature_super_admin" required
                   class="w-full px-4 py-3 border-2 border-cofina-gray rounded-lg focus:border-cofina-red"
                   placeholder="Votre nom complet pour validation">
        </div>

        <div class="mb-6">
            <label for="date_validation" class="block font-bold text-cofina-red mb-2">
                📅 Date de validation *
            </label>
            <input type="date" name="date_validation" value="{{ date('Y-m-d') }}" required
                   class="w-full px-4 py-3 border-2 border-cofina-gray rounded-lg focus:border-cofina-red">
        </div>

        <div class="mb-6">
            <label for="observations" class="block font-bold text-cofina-red mb-2">
                💬 Observations
            </label>
            <textarea name="observations" id="observations" rows="4"
                      class="w-full px-4 py-3 border-2 border-cofina-gray rounded-lg focus:border-cofina-red"
                      placeholder="Remarques sur la validation..."></textarea>
        </div>

        <div class="flex gap-4 pt-6 border-t border-cofina-gray">
            <button type="submit" class="btn-cofina-success flex-1 py-4 text-lg font-bold">
                ✅ APPROUVER LA TRANSITION
            </button>
            <button type="button" onclick="openRejectModal()" 
                    class="btn-cofina-danger flex-1 py-4 text-lg font-bold">
                ❌ REJETER
            </button>
        </div>
    </div>
</form>