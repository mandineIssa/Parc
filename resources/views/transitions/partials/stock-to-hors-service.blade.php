<div id="stock-to-hors-service-form" class="transition-form">
    <h4 class="text-xl font-bold text-cofina-red mb-6">📦 → ❌ Mettre Hors Service (Stock)</h4>
    
    <form id="stock-hors-service-form" onsubmit="submitStockHorsService(event)">
        @csrf
        <input type="hidden" name="transition_type" value="stock_to_hors_service">
        
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
        <option value="reforme">Retour fournisseur (Réforme)</option>
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
            <button type="submit" class="btn-cofina-primary" id="submit-btn">
                ✅ Confirmer la mise hors service
            </button>
            <button type="button" class="btn-cofina-outline" onclick="closeSimpleForm()">
                ↩️ Annuler
            </button>
        </div>
    </form>
</div>
<script>
   function submitStockHorsService(event) {
    event.preventDefault();
    
    const form = document.getElementById('stock-hors-service-form');
    const submitBtn = document.getElementById('submit-btn');
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
    fetch('{{ route("transitions.submit-hors-service") }}', {
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
                    errorMessage += `• ${result.errors[field].join(', ')}\n`;
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