<div id="maintenance-to-hors-service-form" class="transition-form">
    <h4 class="text-xl font-bold text-cofina-red mb-6">🔧 → ❌ Déclarer Irréparable</h4>
    
    <form action="{{ route('equipment.transitions.maintenance-to-hors-service', $equipment) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Raison -->
            <div>
                <label for="raison" class="block text-sm font-bold text-cofina-red mb-2">Raison *</label>
                <select name="raison" id="raison" class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="panne_irreparable">Coût réparation trop élevé</option>
                    <option value="obsolescence">Pièces non disponibles</option>
                    <option value="accident">Dégâts irréparables</option>
                    <option value="autre">Autre</option>
                </select>
            </div>
            
            <!-- Destinataire -->
            <div>
                <label for="destinataire" class="block text-sm font-bold text-cofina-red mb-2">Destinataire *</label>
                <select name="destinataire" id="destinataire" class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="reforme">Réforme</option>
                    <option value="destruction">Destruction</option>
                    <option value="don">Don comme pièces</option>
                    <option value="vente">Vente pour pièces</option>
                </select>
            </div>
            
            <!-- Coût diagnostic -->
            <div>
                <label for="cout_diagnostic" class="block text-sm font-bold text-cofina-red mb-2">Coût diagnostic (FCFA)</label>
                <input type="number" name="cout_diagnostic" id="cout_diagnostic" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       min="0" step="0.01" placeholder="0.00">
            </div>
            
            <!-- Valeur résiduelle -->
            <div>
                <label for="valeur_residuelle" class="block text-sm font-bold text-cofina-red mb-2">Valeur résiduelle (FCFA)</label>
                <input type="number" name="valeur_residuelle" id="valeur_residuelle" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       min="0" step="0.01" placeholder="0.00">
            </div>
            
            <!-- Diagnostic détaillé -->
            <div class="md:col-span-2">
                <label for="diagnostic_detaille" class="block text-sm font-bold text-cofina-red mb-2">Diagnostic détaillé *</label>
                <textarea name="diagnostic_detaille" id="diagnostic_detaille" rows="3" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Pourquoi l'équipement est irréparable ?" required></textarea>
            </div>
            
            <!-- Recommandation -->
            <div class="md:col-span-2">
                <label for="recommandation" class="block text-sm font-bold text-cofina-red mb-2">Recommandation</label>
                <textarea name="recommandation" id="recommandation" rows="2" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Que faire ensuite ? Achat neuf ?..."></textarea>
            </div>
            
            <!-- Justificatif -->
            <div class="md:col-span-2">
                <label for="justificatif" class="block text-sm font-bold text-cofina-red mb-2">Justificatif</label>
                <input type="text" name="justificatif" id="justificatif" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="Rapport expert, devis trop élevé...">
            </div>
        </div>
        
        <div class="flex gap-4 mt-8 pt-6 border-t border-cofina-gray">
            <button type="submit" class="btn-cofina-primary">
                ✅ Confirmer l'irréparabilité
            </button>
            <button type="button" class="btn-cofina-outline" onclick="resetForm()">
                ↩️ Annuler
            </button>
        </div>
    </form>
</div>