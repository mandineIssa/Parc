<div id="parc-to-hors-service-form" class="transition-form">
    <h4 class="text-xl font-bold text-cofina-red mb-6">👨‍💼 → ❌ Mettre Hors Service</h4>
    
    <form id="parc-hors-service-form" onsubmit="submitParcHorsService(event)">
        @csrf
        <input type="hidden" name="transition_type" value="parc_to_hors_service">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Raison -->
            <div>
                <label for="raison" class="block text-sm font-bold text-cofina-red mb-2">Raison *</label>
                <select name="raison" id="raison" class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="panne_irreparable">Panne irréparable</option>
                    <option value="obsolescence">Obsolescence</option>
                    <option value="accident">Accident</option>
                    <option value="vol">Vol</option>
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
                    <option value="don">Don</option>
                    <option value="vente">Vente</option>
                </select>
            </div>
            
            <!-- Valeur résiduelle -->
            <div>
                <label for="valeur_residuelle" class="block text-sm font-bold text-cofina-red mb-2">Valeur résiduelle (FCFA)</label>
                <input type="number" name="valeur_residuelle" id="valeur_residuelle" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       min="0" step="0.01" placeholder="0.00">
            </div>
            
            <!-- Justificatif -->
            <div>
                <label for="justificatif" class="block text-sm font-bold text-cofina-red mb-2">Justificatif</label>
                <input type="text" name="justificatif" id="justificatif" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="N° bon de sortie, référence...">
            </div>
            
            <!-- Description incident -->
            <div class="md:col-span-2">
                <label for="description_incident" class="block text-sm font-bold text-cofina-red mb-2">Description de l'incident *</label>
                <textarea name="description_incident" id="description_incident" rows="3" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Décrivez ce qui s'est passé..." required></textarea>
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
            <button type="submit" class="btn-cofina-primary" id="submit-btn-parc">
                ✅ Confirmer la mise hors service
            </button>
            <button type="button" class="btn-cofina-outline" onclick="closeSimpleForm()">
                ↩️ Annuler
            </button>
        </div>
    </form>
</div>
<script>
function submitParcHorsService(event) {
    event.preventDefault();
    
    const form = document.getElementById('parc-hors-service-form');
    const submitBtn = document.getElementById('submit-btn-parc');
    const originalText = submitBtn.innerHTML;
    
    // Désactiver le bouton pendant l'envoi
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ Envoi en cours...';
    
    // Créer FormData
    const formData = new FormData(form);
    formData.append('equipment_id', {{ $equipment->id }});
    
    console.log('Données envoyées:');
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    // Envoyer les données au serveur
    fetch('{{ route("transitions.submit-parc-hors-service") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json' // Important
        }
    })
    .then(response => {
        console.log('Réponse reçue:', response);
        
        // Vérifier d'abord le statut HTTP
        if (!response.ok) {
            // Si ce n'est pas du JSON, lire comme texte
            return response.text().then(text => {
                console.error('Réponse non-JSON:', text.substring(0, 200));
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            });
        }
        
        // Essayer de parser comme JSON
        return response.json().then(data => {
            console.log('Données JSON:', data);
            return data;
        });
    })
    .then(result => {
        console.log('Résultat:', result);
        
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
        console.error('Erreur complète:', error);
        
        // Afficher un message d'erreur clair
        let errorMessage = 'Erreur lors de la soumission: ';
        
        if (error.message.includes('HTTP 500')) {
            errorMessage += 'Erreur interne du serveur. Vérifiez les logs Laravel.';
        } else if (error.message.includes('HTTP 404')) {
            errorMessage += 'Route non trouvée.';
        } else if (error.message.includes('HTTP 419')) {
            errorMessage += 'Session expirée. Veuillez vous reconnecter.';
        } else if (error.message.includes('Unexpected token')) {
            errorMessage += 'Le serveur a retourné une réponse HTML au lieu de JSON. Vérifiez le contrôleur.';
        } else {
            errorMessage += error.message;
        }
        
        alert(errorMessage);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
  </script>