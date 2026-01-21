<div id="stock-to-parc-form" class="transition-form">
    <h4 class="text-xl font-bold text-cofina-red mb-6">📦 → 👨‍💼 Affecter au Parc</h4>
    
    <form action="{{ route('equipment.transitions.stock-to-parc', $equipment) }}" method="POST">
        @csrf
        
        <!-- SECTION 1: INFORMATIONS DE BASE -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Utilisateur -->
            <div>
                <label for="utilisateur_id" class="block text-sm font-bold text-cofina-red mb-2">
                    Utilisateur *
                </label>
                <select name="utilisateur_id" id="utilisateur_id" 
                        class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red" 
                        required onchange="fillUserInfo(this)">
                    <option value="">-- Sélectionner un utilisateur --</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" 
                            data-name="{{ $user->name }}" 
                            data-prenom="{{ $user->prenom ?? $user->name }}"
                            data-email="{{ $user->email }}">
                        {{ $user->name }} {{ $user->prenom ?? '' }} - {{ $user->email }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Département -->
            <div>
                <label for="departement" class="block text-sm font-bold text-cofina-red mb-2">
                    Département *
                </label>
                <input type="text" name="departement" id="departement" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="Informatique, RH..." required>
            </div>
            
            <!-- Poste -->
            <div>
                <label for="poste_affecte" class="block text-sm font-bold text-cofina-red mb-2">
                    Poste *
                </label>
                <input type="text" name="poste_affecte" id="poste_affecte" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       placeholder="Chef de Projet, Technicien..." required>
            </div>
            
            <!-- Date d'affectation -->
            <div>
                <label for="date_affectation" class="block text-sm font-bold text-cofina-red mb-2">
                    Date d'affectation *
                </label>
                <input type="date" name="date_affectation" id="date_affectation" 
                       class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                       value="{{ date('Y-m-d') }}" required>
            </div>
        </div>

        <!-- CHOIX DES FICHES -->
        <div class="mb-8">
            <label class="block text-sm font-bold text-cofina-red mb-2">Fiches à générer *</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center p-4 border-2 border-cofina-gray rounded hover:bg-red-50 cursor-pointer">
                    <input type="checkbox" name="choix_fiches[]" id="fiche_mouvement" value="mouvement" 
                           class="h-5 w-5 text-cofina-red rounded focus:ring-cofina-red"
                           onchange="toggleSection('section-mouvement', this.checked)">
                    <label for="fiche_mouvement" class="ml-3 block cursor-pointer">
                        <span class="font-bold">Fiche de Mouvement</span><br>
                        <span class="text-sm text-gray-600">Document de transfert de matériel</span>
                    </label>
                </div>
                <div class="flex items-center p-4 border-2 border-cofina-gray rounded hover:bg-red-50 cursor-pointer">
                    <input type="checkbox" name="choix_fiches[]" id="fiche_installation" value="installation" 
                           class="h-5 w-5 text-cofina-red rounded focus:ring-cofina-red"
                           onchange="toggleSection('section-installation', this.checked)">
                    <label for="fiche_installation" class="ml-3 block cursor-pointer">
                        <span class="font-bold">Fiche d'Installation</span><br>
                        <span class="text-sm text-gray-600">Checklist d'installation complète</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- SECTION FICHE DE MOUVEMENT (cachée par défaut) -->
        <div id="section-mouvement" class="hidden mb-8 p-6 border-2 border-blue-300 rounded-lg bg-blue-50">
            <h5 class="text-lg font-bold text-blue-800 mb-4 border-b-2 border-blue-300 pb-2">
                📄 Fiche de Mouvement
            </h5>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Expéditeur (Agent IT - Auto-rempli) -->
                <div class="bg-white p-4 rounded-lg border border-blue-200">
                    <h6 class="font-bold text-blue-800 mb-3">Expéditeur (Agent IT)</h6>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Nom *</label>
                            <input type="text" name="expediteur_nom" 
                                   value="{{ auth()->user()->name }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded" 
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Prénom *</label>
                            <input type="text" name="expediteur_prenom" 
                                   value="{{ auth()->user()->prenom ?? auth()->user()->name }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded" 
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Fonction *</label>
                            <input type="text" name="expediteur_fonction" 
                                   value="AGENT IT"
                                   class="w-full px-3 py-2 border border-gray-300 rounded" 
                                   required>
                        </div>
                    </div>
                </div>

                <!-- Réceptionnaire (Pré-rempli automatiquement) -->
                <div class="bg-white p-4 rounded-lg border border-green-200">
                    <h6 class="font-bold text-green-800 mb-3">Réceptionnaire (Auto-rempli)</h6>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Nom</label>
                            <input type="text" id="receptionnaire_nom" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100" 
                                   readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Prénom</label>
                            <input type="text" id="receptionnaire_prenom" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100" 
                                   readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Fonction</label>
                            <input type="text" id="receptionnaire_fonction" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100" 
                                   readonly>
                        </div>
                    </div>
                </div>

                <!-- Détails du mouvement -->
                <div class="md:col-span-2 bg-white p-4 rounded-lg border border-gray-200">
                    <h6 class="font-bold text-gray-800 mb-3">Détails du mouvement</h6>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Lieu de départ *</label>
                            <input type="text" name="lieu_depart" 
                                   value="SIEGE COFINA"
                                   class="w-full px-3 py-2 border border-gray-300 rounded" 
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Destination *</label>
                            <input type="text" name="destination" id="destination"
                                   class="w-full px-3 py-2 border border-gray-300 rounded" 
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Motif *</label>
                            <input type="text" name="motif" 
                                   value="DOTATION"
                                   class="w-full px-3 py-2 border border-gray-300 rounded" 
                                   required>
                        </div>
                    </div>
                </div>

                <!-- ===================== SIGNATURES MOUVEMENT ===================== -->
                <!-- Signature Expéditeur -->
                <div class="md:col-span-2 p-4 border-t-2 border-blue-300 bg-blue-50 rounded-lg">
                    <h6 class="font-bold text-blue-800 mb-3">✍️ Signature Expéditeur (Agent IT)</h6>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <input type="text" name="signature_expediteur_nom"
                               placeholder="Nom" class="border px-2 py-1 rounded" 
                               value="{{ auth()->user()->name }}" required>
                        <input type="text" name="signature_expediteur_prenom"
                               placeholder="Prénom" class="border px-2 py-1 rounded"
                               value="{{ auth()->user()->prenom ?? auth()->user()->name }}" required>
                        <input type="text" name="signature_expediteur_fonction"
                               value="AGENT IT" class="border px-2 py-1 rounded" readonly>
                    </div>

                    <canvas id="signature_expediteur" width="400" height="150"
                            class="border-2 border-blue-400 bg-white rounded w-full max-w-md"></canvas>

                    <input type="hidden" name="signature_expediteur" id="signature_expediteur_input">

                    <button type="button"
                            onclick="clearSignature('expediteur')"
                            class="mt-2 text-sm px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">
                        Effacer la signature
                    </button>
                </div>

                <!-- Signature Réceptionnaire -->
                <div class="md:col-span-2 p-4 border-t-2 border-green-300 bg-green-50 rounded-lg">
                    <h6 class="font-bold text-green-800 mb-3">✍️ Signature Réceptionnaire</h6>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <input type="text" name="signature_receptionnaire_nom"
                               placeholder="Nom" class="border px-2 py-1 rounded" required>
                        <input type="text" name="signature_receptionnaire_prenom"
                               placeholder="Prénom" class="border px-2 py-1 rounded" required>
                        <input type="text" name="signature_receptionnaire_fonction"
                               placeholder="Fonction" class="border px-2 py-1 rounded" required>
                    </div>

                    <canvas id="signature_receptionnaire" width="400" height="150"
                            class="border-2 border-green-400 bg-white rounded w-full max-w-md"></canvas>

                    <input type="hidden" name="signature_receptionnaire" id="signature_receptionnaire_input">

                    <button type="button"
                            onclick="clearSignature('receptionnaire')"
                            class="mt-2 text-sm px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">
                        Effacer la signature
                    </button>
                </div>
            </div>
        </div>

        <!-- SECTION FICHE D'INSTALLATION (cachée par défaut) -->
        <div id="section-installation" class="hidden mb-8 p-6 border-2 border-cofina-red rounded-lg">
            <!-- ===================== FICHE INSTALLATION ===================== -->
            <div class="card-cofina border-2 border-cofina-red mb-6">

                <!-- HEADER -->
                <div class="bg-cofina-red text-white p-6 rounded-t-lg">
                    <h2 class="text-2xl font-bold text-center">COFINA SENEGAL - IT</h2>
                    <p class="text-center font-semibold mt-1">PROCÉDURE D'INSTALLATION DE MACHINES</p>
                </div>

                <!-- AGENCE -->
                <div class="p-6">
                    <label class="font-bold">Nom de l'agence *</label>
                    <input type="text" name="agence_nom"
                           class="w-full border-2 rounded px-3 py-2"
                           id="agence_nom"
                           placeholder="Nom de l'agence ou département" required>
                </div>

                <!-- ===================== CHECKLIST ===================== -->
                <div class="p-6 border-t">
                    <h3 class="font-bold text-xl mb-4">☑️ Checklist d'installation</h3>

                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="checklist[sauvegarde_donnees]" value="1"
                                   class="h-4 w-4">
                            Sauvegarde des données
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="checklist[sauvegarde_outlook]" value="1"
                                   class="h-4 w-4">
                            Sauvegarde Outlook (.pst)
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="checklist[reinstallation_os]" value="1"
                                   class="h-4 w-4">
                            Réinstallation du système
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="checklist[logiciels_ms_office]" value="1"
                                   class="h-4 w-4">
                            MS Office
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="checklist[logiciels_kaspersky]" value="1"
                                   class="h-4 w-4">
                            Kaspersky
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="checklist[logiciels_chrome]" value="1"
                                   class="h-4 w-4">
                            Google Chrome
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="checklist[integration_domaine]" value="1"
                                   class="h-4 w-4">
                            Intégration au domaine
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="checklist[parametrage_messagerie]" value="1"
                                   class="h-4 w-4">
                            Messagerie configurée
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="checklist[connexion_dossier_partage]" value="1"
                                   class="h-4 w-4">
                            Connexion dossier partagé
                        </label>
                    </div>
                </div>

                <!-- ===================== SIGNATURE INSTALLATEUR ===================== -->
                <div class="p-6 border-t bg-blue-50">
                    <h3 class="font-bold text-blue-800 mb-3">✍️ Signature Installateur</h3>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <input type="text" name="installateur_nom"
                               placeholder="Nom" class="border px-2 py-1" required
                               value="{{ auth()->user()->name }}">
                        <input type="text" name="installateur_prenom"
                               placeholder="Prénom" class="border px-2 py-1" required
                               value="{{ auth()->user()->prenom ?? auth()->user()->name }}">
                        <input type="text" name="installateur_fonction"
                               value="IT" class="border px-2 py-1">
                    </div>

                    <canvas id="signature_installateur" width="400" height="150"
                            class="border-2 border-blue-400 bg-white rounded w-full max-w-md"></canvas>

                    <input type="hidden" name="signature_installateur" id="signature_installateur_input">

                    <button type="button"
                            onclick="clearSignature('installateur')"
                            class="mt-2 text-sm px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">
                        Effacer
                    </button>
                </div>

                <!-- ===================== SIGNATURE VÉRIFICATEUR ===================== -->
                <div class="p-6 border-t bg-green-50">
                    <h3 class="font-bold text-green-800 mb-3">✍️ Signature Vérificateur (Super Admin)</h3>
                    <p class="text-sm text-gray-600 mb-3">À compléter par le Super Admin lors de la validation</p>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <input type="text" name="verificateur_nom"
                               placeholder="Nom" class="border px-2 py-1 bg-gray-100" 
                               readonly>
                        <input type="text" name="verificateur_prenom"
                               placeholder="Prénom" class="border px-2 py-1 bg-gray-100" 
                               readonly>
                        <input type="text" name="verificateur_fonction"
                               value="Super Admin" class="border px-2 py-1 bg-gray-100" 
                               readonly>
                    </div>

                    <div class="bg-yellow-50 p-4 rounded border border-yellow-200">
                        <canvas id="signature_verificateur" width="400" height="150"
                                class="border-2 border-yellow-400 bg-white rounded w-full max-w-md"></canvas>
                        <p class="text-sm text-yellow-700 mt-2 text-center">
                            Cette zone sera signée par le Super Admin lors de la validation finale
                        </p>
                    </div>

                    <input type="hidden" name="signature_verificateur" id="signature_verificateur_input">
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="mb-8">
            <label for="notes" class="block text-sm font-bold text-cofina-red mb-2">Notes</label>
            <textarea name="notes" id="notes" rows="3" 
                      class="w-full px-4 py-2 border-2 border-cofina-gray rounded focus:outline-none focus:border-cofina-red"
                      placeholder="Remarques sur l'affectation..."></textarea>
        </div>
        
        <div class="flex gap-4 pt-6 border-t border-cofina-gray">
            <button type="submit" class="btn-cofina-primary flex-1">
                📋 Soumettre pour validation
            </button>
            <button type="button" class="btn-cofina-outline flex-1" onclick="resetForm()">
                ↩️ Annuler
            </button>
        </div>
    </form>
</div>

<!-- ===================== SCRIPT SIGNATURE ===================== -->
<script>
// Fonction générique pour setup signature
function setupSignature(canvasId, inputId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    let drawing = false;

    ctx.lineWidth = 2;
    ctx.strokeStyle = "#000";

    // Mouse events
    canvas.addEventListener('mousedown', e => {
        drawing = true;
        ctx.beginPath();
        const rect = canvas.getBoundingClientRect();
        ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    });

    canvas.addEventListener('mouseup', () => {
        drawing = false;
        document.getElementById(inputId).value = canvas.toDataURL();
    });

    canvas.addEventListener('mousemove', e => {
        if (!drawing) return;
        const rect = canvas.getBoundingClientRect();
        ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        ctx.stroke();
    });

    // Touch events for mobile
    canvas.addEventListener('touchstart', e => {
        e.preventDefault();
        drawing = true;
        ctx.beginPath();
        const rect = canvas.getBoundingClientRect();
        const touch = e.touches[0];
        ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
    });

    canvas.addEventListener('touchend', () => {
        drawing = false;
        document.getElementById(inputId).value = canvas.toDataURL();
    });

    canvas.addEventListener('touchmove', e => {
        e.preventDefault();
        if (!drawing) return;
        const rect = canvas.getBoundingClientRect();
        const touch = e.touches[0];
        ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
        ctx.stroke();
    });
}

function clearSignature(type) {
    const canvas = document.getElementById(`signature_${type}`);
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    const input = document.getElementById(`signature_${type}_input`);
    if (input) input.value = '';
}

// Initialize signatures when section is shown
function toggleSection(sectionId, show) {
    const section = document.getElementById(sectionId);
    if (show) {
        section.classList.remove('hidden');
        section.classList.add('animate-fadeIn');
        
        // Initialize signatures based on section
        setTimeout(() => {
            if (sectionId === 'section-mouvement') {
                setupSignature('signature_expediteur', 'signature_expediteur_input');
                setupSignature('signature_receptionnaire', 'signature_receptionnaire_input');
                
                // Auto-fill receptionnaire signature fields
                const nom = document.getElementById('receptionnaire_nom').value;
                const prenom = document.getElementById('receptionnaire_prenom').value;
                const fonction = document.getElementById('receptionnaire_fonction').value;
                
                if (nom) document.querySelector('input[name="signature_receptionnaire_nom"]').value = nom;
                if (prenom) document.querySelector('input[name="signature_receptionnaire_prenom"]').value = prenom;
                if (fonction) document.querySelector('input[name="signature_receptionnaire_fonction"]').value = fonction;
            }
            
            if (sectionId === 'section-installation') {
                setupSignature('signature_installateur', 'signature_installateur_input');
                // Verificateur signature is read-only in this form
                // setupSignature('signature_verificateur', 'signature_verificateur_input');
                
                // Auto-fill installateur fields
                document.querySelector('input[name="installateur_nom"]').value = '{{ auth()->user()->name }}';
                document.querySelector('input[name="installateur_prenom"]').value = '{{ auth()->user()->prenom ?? auth()->user()->name }}';
                
                // Auto-fill agence from departement
                const departement = document.getElementById('departement').value;
                if (departement) {
                    document.getElementById('agence_nom').value = departement;
                }
            }
        }, 100);
    } else {
        section.classList.add('hidden');
        section.classList.remove('animate-fadeIn');
    }
}

// Remplir automatiquement les infos du réceptionnaire
function fillUserInfo(select) {
    const option = select.options[select.selectedIndex];
    if (option.value) {
        document.getElementById('receptionnaire_nom').value = option.dataset.name || '';
        document.getElementById('receptionnaire_prenom').value = option.dataset.prenom || '';
        document.getElementById('receptionnaire_fonction').value = document.getElementById('poste_affecte').value || '';
        document.getElementById('destination').value = document.getElementById('departement').value || 'SIEGE COFINA';
        
        // Pré-remplir les champs de signature du réceptionnaire
        document.querySelector('input[name="signature_receptionnaire_nom"]').value = option.dataset.name || '';
        document.querySelector('input[name="signature_receptionnaire_prenom"]').value = option.dataset.prenom || '';
        document.querySelector('input[name="signature_receptionnaire_fonction"]').value = document.getElementById('poste_affecte').value || '';
    } else {
        document.getElementById('receptionnaire_nom').value = '';
        document.getElementById('receptionnaire_prenom').value = '';
        document.getElementById('receptionnaire_fonction').value = '';
        
        // Effacer les champs de signature
        document.querySelector('input[name="signature_receptionnaire_nom"]').value = '';
        document.querySelector('input[name="signature_receptionnaire_prenom"]').value = '';
        document.querySelector('input[name="signature_receptionnaire_fonction"]').value = '';
    }
}

// Synchroniser les champs
document.getElementById('poste_affecte')?.addEventListener('input', function() {
    document.getElementById('receptionnaire_fonction').value = this.value;
    document.querySelector('input[name="signature_receptionnaire_fonction"]').value = this.value;
});

document.getElementById('departement')?.addEventListener('input', function() {
    document.getElementById('destination').value = this.value || 'SIEGE COFINA';
    document.getElementById('agence_nom').value = this.value || '';
});

// Reset du formulaire
function resetForm() {
    if (confirm('Êtes-vous sûr de vouloir annuler ? Toutes les données seront perdues.')) {
        const form = document.getElementById('stock-to-parc-form').querySelector('form');
        form.reset();
        
        document.getElementById('receptionnaire_nom').value = '';
        document.getElementById('receptionnaire_prenom').value = '';
        document.getElementById('receptionnaire_fonction').value = '';
        
        // Réinitialiser toutes les signatures
        ['expediteur', 'receptionnaire', 'installateur', 'verificateur'].forEach(type => {
            clearSignature(type);
        });
        
        // Réinitialiser les champs de signature par défaut
        document.querySelector('input[name="signature_expediteur_nom"]').value = '{{ auth()->user()->name }}';
        document.querySelector('input[name="signature_expediteur_prenom"]').value = '{{ auth()->user()->prenom ?? auth()->user()->name }}';
        document.querySelector('input[name="installateur_nom"]').value = '{{ auth()->user()->name }}';
        document.querySelector('input[name="installateur_prenom"]').value = '{{ auth()->user()->prenom ?? auth()->user()->name }}';
        
        document.querySelector('input[name="signature_receptionnaire_nom"]').value = '';
        document.querySelector('input[name="signature_receptionnaire_prenom"]').value = '';
        document.querySelector('input[name="signature_receptionnaire_fonction"]').value = '';
        
        document.querySelector('input[name="verificateur_nom"]').value = '';
        document.querySelector('input[name="verificateur_prenom"]').value = '';
        
        // Masquer toutes les sections
        document.getElementById('section-mouvement').classList.add('hidden');
        document.getElementById('section-installation').classList.add('hidden');
    }
}

// Initial setup for date field
document.addEventListener('DOMContentLoaded', function() {
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    const dateField = document.getElementById('date_affectation');
    if (dateField && !dateField.value) {
        dateField.value = today;
    }
});
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.3s ease-in-out;
}

/* Amélioration du canvas de signature */
canvas {
    cursor: crosshair;
    touch-action: none; /* Empêche le défilement sur mobile */
}

/* Style pour les champs en lecture seule */
input[readonly] {
    background-color: #f3f4f6;
    cursor: not-allowed;
}
</style>