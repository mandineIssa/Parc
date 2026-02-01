<div id="parc-to-maintenance-form" class="transition-form">
    <h4 class="text-xl font-bold text-cofina-red mb-6">👨‍💼 → 🔧 Envoyer en Maintenance</h4>
    
    <form id="parc-maintenance-form" onsubmit="submitParcMaintenance(event)">
        @csrf
        <input type="hidden" name="transition_type" value="parc_to_maintenance">
        
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
            
            <!-- Priorité -->
            <div>
                <label for="priorite" class="block text-sm font-bold text-cofina-red mb-2">Priorité</label>
                <select name="priorite" id="priorite" class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red">
                    <option value="normal" selected>Normale</option>
                    <option value="urgent">Urgente</option>
                    <option value="critique">Critique</option>
                </select>
            </div>
            
            <!-- Description panne -->
            <div class="md:col-span-2">
                <label for="description_panne" class="block text-sm font-bold text-cofina-red mb-2">Description de la panne *</label>
                <textarea name="description_panne" id="description_panne" rows="3" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Décrivez le problème..." required></textarea>
            </div>
            
            <!-- Coût estimé -->
            <div>
                <label for="cout_estime" class="block text-sm font-bold text-cofina-red mb-2">Coût estimé (FCFA)</label>
                <input type="number" name="cout_estime" id="cout_estime" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       min="0" step="0.01" placeholder="0.00">
            </div>
            
            <!-- Localisation actuelle -->
            <div>
                <label for="localisation" class="block text-sm font-bold text-cofina-red mb-2">Localisation actuelle</label>
                <input type="text" name="localisation" id="localisation" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="Ex: Bureau 201, Agence Dakar...">
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
            <button type="submit" class="btn-cofina-primary" id="submit-maintenance-btn">
                ✅ Confirmer l'envoi en maintenance
            </button>
            <button type="button" class="btn-cofina-outline" onclick="closeSimpleForm()">
                ↩️ Annuler
            </button>
        </div>
    </form>
</div>
<script>
    function submitParcMaintenance(event) {
    event.preventDefault();
    
    const form = document.getElementById('parc-maintenance-form');
    const submitBtn = document.getElementById('submit-maintenance-btn');
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
    fetch('{{ route("transitions.submit-maintenance") }}', {
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