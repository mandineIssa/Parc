<div id="parc-to-perdu-form" class="transition-form">
    <h4 class="text-xl font-bold text-cofina-red mb-6">👨‍💼 → 🔍 Déclarer Perdu</h4>
    
    <form id="parc-perdu-form" onsubmit="submitParcPerdu(event)">
        @csrf
        <input type="hidden" name="transition_type" value="parc_to_perdu">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Type disparition -->
            <div>
                <label for="type_disparition" class="block text-sm font-bold text-cofina-red mb-2">Type *</label>
                <select name="type_disparition" id="type_disparition" 
                        class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" 
                        required>
                    <option value="">-- Sélectionner --</option>
                    <option value="vol">Vol</option>
                    <option value="perte">Perte</option>
                    <option value="non_localise">Non localisé</option>
                </select>
            </div>
            
            <!-- Date disparition -->
            <div>
                <label for="date_disparition" class="block text-sm font-bold text-cofina-red mb-2">Date disparition *</label>
                <input type="date" name="date_disparition" id="date_disparition"
                       value="{{ date('Y-m-d') }}"
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       required>
            </div>
            
            <!-- Lieu disparition -->
            <div>
                <label for="lieu_disparition" class="block text-sm font-bold text-cofina-red mb-2">Lieu de disparition *</label>
                <input type="text" name="lieu_disparition" id="lieu_disparition" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="Localisation exacte..." required>
            </div>
            
            <!-- Responsable disparition -->
            <div>
                <label for="responsable_disparition" class="block text-sm font-bold text-cofina-red mb-2">Responsable</label>
                <input type="text" name="responsable_disparition" id="responsable_disparition" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="Nom du responsable...">
            </div>
            
            <!-- Plainte déposée -->
            <div>
                <label for="plainte_deposee" class="block text-sm font-bold text-cofina-red mb-2">Plainte déposée</label>
                <select name="plainte_deposee" id="plainte_deposee" 
                        class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red">
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
            
            <!-- Mesures prises -->
            <div class="md:col-span-2">
                <label for="mesures_prises" class="block text-sm font-bold text-cofina-red mb-2">Mesures prises</label>
                <textarea name="mesures_prises" id="mesures_prises" rows="2" 
                          class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                          placeholder="Quelles mesures ont été prises..."></textarea>
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
            <button type="submit" class="btn-cofina-primary" id="submit-perdu-btn">
                ✅ Confirmer la déclaration
            </button>
            <button type="button" class="btn-cofina-outline" onclick="closeSimpleForm()">
                ↩️ Annuler
            </button>
        </div>
    </form>
</div>
<script>
    function submitParcPerdu(event) {
    event.preventDefault();
    
    const form = document.getElementById('parc-perdu-form');
    const submitBtn = document.getElementById('submit-perdu-btn');
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
    
    // Convertir les valeurs booléennes
    data.plainte_deposee = data.plainte_deposee === '1' ? true : false;
    
    // Envoyer les données au serveur
    fetch('{{ route("transitions.submit-perdu") }}', {
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

// Script pour gérer l'affichage du champ plainte
document.addEventListener('DOMContentLoaded', function() {
    const plainteSelect = document.getElementById('plainte_deposee');
    if (plainteSelect) {
        plainteSelect.addEventListener('change', function() {
            const plainteContainer = document.getElementById('numero_plainte_container');
            if (this.value === '1') {
                plainteContainer.classList.remove('hidden');
            } else {
                plainteContainer.classList.add('hidden');
            }
        });
    }
});
  </script>