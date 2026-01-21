
<div id="parc-to-maintenance-form" class="transition-form">
    <h4 class="text-xl font-bold text-cofina-red mb-6">👨‍💼 → 🔧 Envoyer en Maintenance</h4>
    
    <form action="{{ route('equipment.transitions.parc-to-maintenance', $equipment) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Type de maintenance -->
            <div>
                <label for="type_maintenance" class="block text-sm font-bold text-cofina-red mb-2">Type *</label>
                <select name="type_maintenance" id="type_maintenance" class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="preventive">Préventive</option>
                    <option value="corrective" selected>Corrective</option>
                    <option value="curative">Curative</option>
                </select>
            </div>
            
            <!-- Prestataire -->
            <div>
                <label for="prestataire" class="block text-sm font-bold text-cofina-red mb-2">Prestataire *</label>
                <input type="text" name="prestataire" id="prestataire" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="Nom du prestataire..." required>
            </div>
            
            <!-- Date retour prévue -->
            <div>
                <label for="date_retour_prevue" class="block text-sm font-bold text-cofina-red mb-2">Date retour prévue *</label>
                <input type="date" name="date_retour_prevue" id="date_retour_prevue" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
            </div>
            
            <!-- Description panne -->
            <div class="md:col-span-2">
                <label for="description_panne" class="block text-sm font-bold text-cofina-red mb-2">Description de la panne *</label>
                <textarea name="description_panne" id="description_panne" rows="3" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Décrivez le problème..." required></textarea>
            </div>
            
            <!-- Notes -->
            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-bold text-cofina-red mb-2">Notes supplémentaires</label>
                <textarea name="notes" id="notes" rows="2" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Informations complémentaires..."></textarea>
            </div>
        </div>
        
        <div class="flex gap-4 mt-8 pt-6 border-t border-cofina-gray">
            <button type="submit" class="btn-cofina-primary">
                ✅ Confirmer l'envoi en maintenance
            </button>
            <button type="button" class="btn-cofina-outline" onclick="resetForm()">
                ↩️ Annuler
            </button>
        </div>
    </form>
</div>