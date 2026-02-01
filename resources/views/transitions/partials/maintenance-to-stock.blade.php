<div id="maintenance-to-stock-form" class="transition-form">
    <h4 class="text-xl font-bold text-cofina-red mb-6">🔧 → 📦 Retour au Stock</h4>
    
    <form id="maintenance-stock-form" onsubmit="submitMaintenanceToStock(event)">
        @csrf
        <input type="hidden" name="transition_type" value="maintenance_to_stock">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- État retour -->
            <div>
                <label for="etat_retour" class="block text-sm font-bold text-cofina-red mb-2">État retour *</label>
                <select name="etat_retour" id="etat_retour" 
                        class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" required>
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
            
            <!-- Origine (Maintenance) -->
            <div>
                <label for="origine" class="block text-sm font-bold text-cofina-red mb-2">Origine *</label>
                <input type="text" name="origine" id="origine" value="Maintenance"
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded bg-gray-50" readonly>
            </div>
            
            <!-- Date retour -->
            <div>
                <label for="date_retour" class="block text-sm font-bold text-cofina-red mb-2">Date retour *</label>
                <input type="date" name="date_retour" id="date_retour" 
                       value="{{ date('Y-m-d') }}"
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" required>
            </div>
            
            <!-- Diagnostic -->
            <div class="md:col-span-2">
                <label for="diagnostic" class="block text-sm font-bold text-cofina-red mb-2">Diagnostic *</label>
                <textarea name="diagnostic" id="diagnostic" rows="3" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Résultat du diagnostic..." required></textarea>
            </div>
            
            <!-- Travaux réalisés -->
            <div class="md:col-span-2">
                <label for="travaux_realises" class="block text-sm font-bold text-cofina-red mb-2">Travaux réalisés</label>
                <textarea name="travaux_realises" id="travaux_realises" rows="3" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Détail des travaux effectués..."></textarea>
            </div>
            
            <!-- Raison retour -->
            <div class="md:col-span-2">
                <label for="raison_retour" class="block text-sm font-bold text-cofina-red mb-2">Raison du retour *</label>
                <textarea name="raison_retour" id="raison_retour" rows="2" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Pourquoi l'équipement revient en stock..." required></textarea>
            </div>
            
            <!-- Valeur résiduelle -->
            <div>
                <label for="valeur_residuelle" class="block text-sm font-bold text-cofina-red mb-2">Valeur résiduelle (FCFA)</label>
                <input type="number" name="valeur_residuelle" id="valeur_residuelle" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       min="0" step="0.01" placeholder="0.00">
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
            <button type="submit" class="btn-cofina-primary" id="submit-btn-maintenance">
                ✅ Confirmer le retour au stock
            </button>
            <button type="button" class="btn-cofina-outline" onclick="closeSimpleForm()">
                ↩️ Annuler
            </button>
        </div>
    </form>
</div>
<script>
    function submitMaintenanceToStock(event) {
    event.preventDefault();
    
    const form = document.getElementById('maintenance-stock-form');
    const submitBtn = document.getElementById('submit-btn-maintenance');
    const originalText = submitBtn.innerHTML;
    
    // Désactiver le bouton pendant l'envoi
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ Envoi en cours...';
    
    // Récupérer les données du formulaire
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    // Ajouter l'ID de l'équipement
    data.equipment_id = {{ $equipment->id }};
    data._token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    // Envoyer les données au serveur
    fetch('{{ route("transitions.submit-maintenance-to-stock") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': data._token
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(JSON.stringify(err));
            });
        }
        return response.json();
    })
    .then(result => {
        if (result.success) {
            if (result.redirect_url) {
                window.location.href = result.redirect_url;
            } else {
                alert(result.message);
                window.location.reload();
            }
        } else {
            let errorMessage = result.message || 'Erreur lors de la soumission';
            
            // Afficher les erreurs de validation
            if (result.errors) {
                errorMessage += '\n\nErreurs :\n';
                for (const field in result.errors) {
                    errorMessage += `• ${field}: ${result.errors[field].join(', ')}\n`;
                }
            }
            
            alert(errorMessage);
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        try {
            const errorData = JSON.parse(error.message);
            if (errorData.errors) {
                let errorMessage = 'Erreurs de validation :\n';
                for (const field in errorData.errors) {
                    errorMessage += `• ${field}: ${errorData.errors[field].join(', ')}\n`;
                }
                alert(errorMessage);
            } else {
                alert('Erreur de connexion au serveur: ' + error.message);
            }
        } catch (e) {
            alert('Erreur de connexion au serveur: ' + error.message);
        }
        
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
</script>