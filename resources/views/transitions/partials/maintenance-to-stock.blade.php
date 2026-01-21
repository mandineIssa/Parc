<div id="maintenance-to-stock-form" class="transition-form">
    <h4 class="text-xl font-bold text-cofina-red mb-6">🔧 → 📦 Retour au Stock</h4>
    
    <form action="{{ route('equipment.transitions.maintenance-to-stock', $equipment) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- État retour -->
            <div>
                <label for="etat_retour" class="block text-sm font-bold text-cofina-red mb-2">État retour *</label>
                <select name="etat_retour" id="etat_retour" class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="bon">Bon (réparé)</option>
                    <option value="reparable">Réparable (besoin travaux)</option>
                    <option value="irreparable">Irréparable</option>
                </select>
            </div>
            
            <!-- Coût -->
            <div>
                <label for="cout" class="block text-sm font-bold text-cofina-red mb-2">Coût (FCFA)</label>
                <input type="number" name="cout" id="cout" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       min="0" step="0.01" placeholder="0.00">
            </div>
            
            <!-- Diagnostic -->
            <div class="md:col-span-2">
                <label for="diagnostic" class="block text-sm font-bold text-cofina-red mb-2">Diagnostic</label>
                <textarea name="diagnostic" id="diagnostic" rows="3" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Résultat du diagnostic..."></textarea>
            </div>
            
            <!-- Travaux réalisés -->
            <div class="md:col-span-2">
                <label for="travaux_realises" class="block text-sm font-bold text-cofina-red mb-2">Travaux réalisés</label>
                <textarea name="travaux_realises" id="travaux_realises" rows="3" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Détail des travaux effectués..."></textarea>
            </div>
            
            <!-- Observations -->
            <div class="md:col-span-2">
                <label for="observations_retour" class="block text-sm font-bold text-cofina-red mb-2">Observations</label>
                <textarea name="observations_retour" id="observations_retour" rows="2" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Remarques sur le retour..."></textarea>
            </div>
        </div>
        
        <div class="flex gap-4 mt-8 pt-6 border-t border-cofina-gray">
            <button type="submit" class="btn-cofina-primary">
                ✅ Confirmer le retour au stock
            </button>
            <button type="button" class="btn-cofina-outline" onclick="resetForm()">
                ↩️ Annuler
            </button>
        </div>
    </form>
</div>