<div id="maintenance-to-hors-service-form" class="transition-form">
    <h4 class="text-xl font-bold text-cofina-red mb-6">🔧 → ❌ Déclarer Irréparable</h4>
    
    <form id="maintenance-hors-service-form" onsubmit="submitMaintenanceHorsService(event)">
        @csrf
        <input type="hidden" name="transition_type" value="maintenance_to_hors_service">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Raison -->
            <div>
                <label for="raison" class="block text-sm font-bold text-cofina-red mb-2">Raison *</label>
                <select name="raison" id="raison" class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="cout_reparation">Coût réparation trop élevé</option>
                    <option value="pieces_indisponibles">Pièces non disponibles</option>
                    <option value="degats_irreparables">Dégâts irréparables</option>
                    <option value="obsolescence_technique">Obsolescence technique</option>
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
                    <option value="don_pieces">Don comme pièces</option>
                    <option value="vente_pieces">Vente pour pièces</option>
                    <option value="recyclage">Recyclage</option>
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
                          placeholder="Détails du diagnostic technique, problèmes identifiés, pourquoi l'équipement est irréparable..." required></textarea>
            </div>
            
            <!-- Recommandation -->
            <div class="md:col-span-2">
                <label for="recommandation" class="block text-sm font-bold text-cofina-red mb-2">Recommandation</label>
                <textarea name="recommandation" id="recommandation" rows="2" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Que faire ensuite ? Achat neuf ? Remplacer par quel modèle ?..."></textarea>
            </div>
            
            <!-- Justificatif -->
            <div class="md:col-span-2">
                <label for="justificatif" class="block text-sm font-bold text-cofina-red mb-2">Justificatif</label>
                <input type="text" name="justificatif" id="justificatif" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="Rapport expert, devis trop élevé, références des pièces indisponibles...">
            </div>
            
            <!-- Nom du technicien -->
            <div>
                <label for="technicien_nom" class="block text-sm font-bold text-cofina-red mb-2">Technicien *</label>
                <input type="text" name="technicien_nom" id="technicien_nom" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="Nom du technicien" required>
            </div>
            
            <!-- Date diagnostic -->
            <div>
                <label for="date_diagnostic" class="block text-sm font-bold text-cofina-red mb-2">Date diagnostic *</label>
                <input type="date" name="date_diagnostic" id="date_diagnostic" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       value="{{ date('Y-m-d') }}" required>
            </div>
        </div>
        
        <div class="flex gap-4 mt-8 pt-6 border-t border-cofina-gray">
            <button type="submit" class="btn-cofina-primary" id="submit-btn-maintenance">
                ✅ Confirmer l'irréparabilité
            </button>
            <button type="button" class="btn-cofina-outline" onclick="closeSimpleForm()">
                ↩️ Annuler
            </button>
        </div>
    </form>
</div>
<script>
    function submitMaintenanceHorsService(event) {
    event.preventDefault();
    
    const form = document.getElementById('maintenance-hors-service-form');
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
    
    // Nettoyer les valeurs numériques
    if (data.cout_diagnostic === '') data.cout_diagnostic = null;
    if (data.valeur_residuelle === '') data.valeur_residuelle = null;
    
    // Envoyer les données au serveur
    fetch('{{ route("transitions.submit-maintenance-hors-service") }}', {
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