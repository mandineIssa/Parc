<div id="stock-to-hors-service-form" class="transition-form">
    <h4 class="text-xl font-bold text-cofina-red mb-6">📦 → ❌ Mettre Hors Service (Stock)</h4>
    
    <form action="{{ route('equipment.transitions.stock-to-hors-service', $equipment) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Raison -->
            <div>
                <label for="raison" class="block text-sm font-bold text-cofina-red mb-2">Raison *</label>
                <select name="raison" id="raison" class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="panne_irreparable">Panne irréparable (neuf défectueux)</option>
                    <option value="obsolescence">Obsolescence</option>
                    <option value="accident">Accident pendant stockage</option>
                    <option value="autre">Autre</option>
                </select>
            </div>
            
            <!-- Destinataire -->
            <div>
                <label for="destinataire" class="block text-sm font-bold text-cofina-red mb-2">Destinataire *</label>
                <select name="destinataire" id="destinataire" class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="reforme">Retour fournisseur</option>
                    <option value="destruction">Destruction</option>
                    <option value="don">Don</option>
                    <option value="vente">Vente comme pièces</option>
                </select>
            </div>
            
            <!-- Valeur résiduelle -->
            <div>
                <label for="valeur_residuelle" class="block text-sm font-bold text-cofina-red mb-2">Valeur résiduelle (FCFA)</label>
                <input type="number" name="valeur_residuelle" id="valeur_residuelle" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       min="0" step="0.01" placeholder="0.00">
            </div>
            
            <!-- Description incident -->
            <div class="md:col-span-2">
                <label for="description_incident" class="block text-sm font-bold text-cofina-red mb-2">Description *</label>
                <textarea name="description_incident" id="description_incident" rows="3" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Pourquoi cet équipement neuf est hors service ?" required></textarea>
            </div>
            
            <!-- Justificatif -->
            <div class="md:col-span-2">
                <label for="justificatif" class="block text-sm font-bold text-cofina-red mb-2">Justificatif</label>
                <input type="text" name="justificatif" id="justificatif" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="N° retour fournisseur, rapport expertise...">
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
                ✅ Confirmer la mise hors service
            </button>
            <button type="button" class="btn-cofina-outline" onclick="resetForm()">
                ↩️ Annuler
            </button>
        </div>
    </form>
</div>