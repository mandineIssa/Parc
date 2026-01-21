<div id="parc-to-perdu-form" class="transition-form">
    <h4 class="text-xl font-bold text-cofina-red mb-6">👨‍💼 → 🔍 Déclarer Perdu</h4>
    
    <form action="{{ route('equipment.transitions.parc-to-perdu', $equipment) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Type disparition -->
            <div>
                <label for="type_disparition" class="block text-sm font-bold text-cofina-red mb-2">Type *</label>
                <select name="type_disparition" id="type_disparition" class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="vol">Vol</option>
                    <option value="perte">Perte</option>
                    <option value="non_localise">Non localisé</option>
                </select>
            </div>
            
            <!-- Lieu disparition -->
            <div>
                <label for="lieu_disparition" class="block text-sm font-bold text-cofina-red mb-2">Lieu de disparition *</label>
                <input type="text" name="lieu_disparition" id="lieu_disparition" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="Localisation exacte..." required>
            </div>
            
            <!-- Plainte déposée -->
            <div>
                <label for="plainte_deposee" class="block text-sm font-bold text-cofina-red mb-2">Plainte déposée</label>
                <select name="plainte_deposee" id="plainte_deposee" class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red">
                    <option value="0">Non</option>
                    <option value="1">Oui</option>
                </select>
            </div>
            
            <!-- Numéro plainte -->
            <div id="numero_plainte_container" class="hidden">
                <label for="numero_plainte" class="block text-sm font-bold text-cofina-red mb-2">N° plainte</label>
                <input type="text" name="numero_plainte" id="numero_plainte" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="N° de dossier police...">
            </div>
            
            <!-- Valeur assurée -->
            <div>
                <label for="valeur_assuree" class="block text-sm font-bold text-cofina-red mb-2">Valeur assurée (FCFA)</label>
                <input type="number" name="valeur_assuree" id="valeur_assuree" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       min="0" step="0.01" placeholder="0.00">
            </div>
            
            <!-- Circonstances -->
            <div class="md:col-span-2">
                <label for="circonstances" class="block text-sm font-bold text-cofina-red mb-2">Circonstances *</label>
                <textarea name="circonstances" id="circonstances" rows="3" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Décrivez les circonstances..." required></textarea>
            </div>
            
            <!-- Observations -->
            <div class="md:col-span-2">
                <label for="observations" class="block text-sm font-bold text-cofina-red mb-2">Observations</label>
                <textarea name="observations" id="observations" rows="2" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Informations complémentaires..."></textarea>
            </div>
        </div>
        
        <div class="flex gap-4 mt-8 pt-6 border-t border-cofina-gray">
            <button type="submit" class="btn-cofina-primary">
                ✅ Confirmer la déclaration
            </button>
            <button type="button" class="btn-cofina-outline" onclick="resetForm()">
                ↩️ Annuler
            </button>
        </div>
    </form>
    
    <script>
    document.getElementById('plainte_deposee').addEventListener('change', function() {
        const plainteContainer = document.getElementById('numero_plainte_container');
        if (this.value === '1') {
            plainteContainer.classList.remove('hidden');
        } else {
            plainteContainer.classList.add('hidden');
        }
    });
    </script>
</div>