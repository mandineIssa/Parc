{{--
    resources/views/transitions/partials/parc-to-stock-decele.blade.php
    IMPORTANT : Les <form> sont DIRECTEMENT à la racine (pas de div wrapper hidden)
    car ils sont inclus dans un <div class="hidden"> dans create.blade.php.
    getElementById() doit pouvoir les trouver directement.
--}}

{{-- ── ÉTAPE 1 : Fiche de Retour ── --}}
<form id="retour-step-form-decele" class="step-form-decele hidden" data-step="1">
        <input type="hidden" name="form_type" value="retour">

        <div class="card-cofina bg-white border-2 border-cofina-red">
            {{-- En-tête --}}
            <div class="bg-cofina-red text-white p-6 -mx-6 -mt-6 mb-6 rounded-t-lg">
                <h1 class="text-2xl font-bold text-center">COFINA SENEGAL - IT</h1>
                <h2 class="text-xl font-bold text-center mt-2">FICHE DE RETOUR MATÉRIEL</h2>
                <div class="mt-4 text-center">
                    <label class="inline-block">
                        Date d'application :
                        <input type="date" name="date_application_retour" value="{{ date('Y-m-d') }}"
                            class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold" required>
                    </label>
                </div>
            </div>

            {{-- Informations équipement (lecture seule) --}}
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
                <h4 class="font-bold text-cofina-red mb-3">📦 Équipement concerné</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Nom :</label>
                        <input type="text" value="{{ $equipment->nom }}"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Modèle :</label>
                        <input type="text" value="{{ $equipment->modele ?? 'N/A' }}"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">N° Série :</label>
                        <input type="text" value="{{ $equipment->numero_serie }}"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-gray-100" readonly>
                        <input type="hidden" name="numero_serie" value="{{ $equipment->numero_serie }}">
                    </div>
                </div>
            </div>

            {{-- Informations du détenteur actuel --}}
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border-2 border-blue-200">
                <h4 class="font-bold text-blue-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Détenteur actuel (Expéditeur)
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">
                            <span class="text-red-500">*</span> Nom
                        </label>
                        <input type="text" name="detenteur_nom" placeholder="Nom de l'utilisateur actuel"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-cofina-red" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">
                            <span class="text-red-500">*</span> Prénom
                        </label>
                        <input type="text" name="detenteur_prenom" placeholder="Prénom de l'utilisateur actuel"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-cofina-red" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Département</label>
                        <input type="text" name="detenteur_departement" placeholder="Ex: Finance, IT..."
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-cofina-red">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Poste</label>
                        <input type="text" name="detenteur_poste" placeholder="Ex: Directeur, Agent..."
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-cofina-red">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Localisation actuelle</label>
                        <input type="text" name="localisation_actuelle" placeholder="Ex: Bureau 201, Agence Dakar..."
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-cofina-red">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">
                            <span class="text-red-500">*</span> Date de retour
                        </label>
                        <input type="date" name="date_retour" value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-cofina-red" required>
                    </div>
                </div>
            </div>

            {{-- Raison du retour --}}
            <div class="mb-6 p-4 bg-yellow-50 rounded-lg border-2 border-yellow-200">
                <h4 class="font-bold text-yellow-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Raison du retour
                </h4>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">
                        <span class="text-red-500">*</span> Raison principale
                    </label>
                    <select name="raison_retour"
                        class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-cofina-red" required>
                        <option value="">-- Sélectionner une raison --</option>
                        <option value="renouvellement">Renouvellement d'équipement</option>
                        <option value="panne">Panne / Dysfonctionnement</option>
                        <option value="optimisation">Optimisation du parc</option>
                        <option value="depart_employe">Départ / Mutation d'employé</option>
                        <option value="fin_contrat">Fin de contrat</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Détails / Observations</label>
                    <textarea name="raison_retour_detail" rows="3"
                        class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-cofina-red"
                        placeholder="Précisez les circonstances du retour..."></textarea>
                </div>
            </div>

            {{-- Signature agent IT (récupérateur) --}}
            <div class="bg-white p-4 rounded-lg border-2 border-blue-300">
                <h4 class="font-bold mb-3 text-blue-800">✍️ Signature Agent IT (Récupérateur)</h4>
                <div class="mb-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nom :</label>
                        <input type="text" name="agent_it_nom" value="{{ auth()->user()->name ?? '' }}"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Prénom :</label>
                        <input type="text" name="agent_it_prenom" value="{{ auth()->user()->prenom ?? '' }}"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Fonction :</label>
                        <input type="text" name="agent_it_fonction" value="AGENT IT"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded">
                    </div>
                </div>
                <div class="signature-pad-container mb-3">
                    <canvas class="signature-pad border-2 border-gray-300 rounded bg-white w-full h-32"
                        id="signatureCanvasAgentRetour"></canvas>
                </div>
                <div class="flex gap-2 mb-2">
                    <button type="button" class="btn-cofina-outline text-xs py-1 px-2 flex-1"
                        onclick="clearSignatureDecele('agent_retour')">Effacer</button>
                    <button type="button" class="btn-cofina text-xs py-1 px-2 flex-1"
                        onclick="saveSignatureDecele('agent_retour')">Sauvegarder</button>
                </div>
                <input type="hidden" name="signature_agent_retour" id="signatureAgentRetour">
            </div>
        </div>
    </form>

    {{-- ── ÉTAPE 2 : Fiche Décélération --}}
    <form id="deceleration-step-form" class="step-form-decele hidden" data-step="2">
        <input type="hidden" name="form_type" value="deceleration">

        <div class="card-cofina bg-white border-2 border-orange-400">
            <div class="bg-orange-500 text-white p-6 -mx-6 -mt-6 mb-6 rounded-t-lg">
                <h1 class="text-2xl font-bold text-center">COFINA SENEGAL - IT</h1>
                <h2 class="text-xl font-bold text-center mt-2">FICHE DE DÉCÉLÉRATION MATÉRIEL</h2>
                <div class="mt-4 text-center">
                    <label class="inline-block">
                        Date :
                        <input type="date" name="date_deceleration" value="{{ date('Y-m-d') }}"
                            class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold" required>
                    </label>
                </div>
            </div>

            {{-- Diagnostic technique --}}
            <div class="mb-6 p-4 bg-orange-50 rounded-lg border-2 border-orange-200">
                <h4 class="font-bold text-orange-800 mb-4">🔍 Diagnostic technique</h4>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">
                        <span class="text-red-500">*</span> État à la réception
                    </label>
                    <select name="etat_retour"
                        class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-orange-500" required
                        onchange="toggleDecelerationFields(this.value)">
                        <option value="">-- Évaluer l'état --</option>
                        <option value="bon">✅ Bon état — Réintégration directe au stock</option>
                        <option value="reparable">🔧 Réparable — Nécessite maintenance avant réintégration</option>
                        <option value="irreparable">❌ Irréparable — Mise hors service recommandée</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Diagnostic détaillé</label>
                    <textarea name="diagnostic" rows="4"
                        class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-orange-500"
                        placeholder="Décrivez l'état technique de l'équipement, les problèmes constatés..."></textarea>
                </div>

                {{-- Valeur résiduelle --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Valeur résiduelle estimée (FCFA)</label>
                    <input type="number" name="valeur_residuelle" min="0" step="1000"
                        placeholder="Ex: 250000"
                        class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-orange-500">
                </div>
            </div>

            {{-- Stock décélé : localisation physique --}}
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border-2 border-gray-300">
                <h4 class="font-bold text-gray-700 mb-4">📍 Intégration Stock Décélé</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">
                            <span class="text-red-500">*</span> Localisation physique (stock)
                        </label>
                        <input type="text" name="localisation_physique" placeholder="Ex: Magasin IT - Étagère B3"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-orange-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">
                            <span class="text-red-500">*</span> Date d'entrée au stock
                        </label>
                        <input type="date" name="date_entree_stock" value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-orange-500" required>
                    </div>
                </div>

                {{-- Message conditionnel selon état --}}
                <div id="msg-reparable" class="hidden mt-4 p-3 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                    <p class="text-sm font-semibold text-yellow-800">
                        ⚠️ Équipement réparable : il sera enregistré en stock décélé avec statut <strong>réservé</strong>
                        en attente de passage en maintenance.
                    </p>
                </div>
                <div id="msg-irreparable" class="hidden mt-4 p-3 bg-red-50 border-l-4 border-red-500 rounded">
                    <p class="text-sm font-semibold text-red-800">
                        ❌ Équipement irréparable : il sera enregistré en stock décélé mais une mise hors service
                        sera recommandée. Vous pourrez effectuer la transition "Hors Service" après validation.
                    </p>
                </div>
                <div id="msg-bon" class="hidden mt-4 p-3 bg-green-50 border-l-4 border-green-500 rounded">
                    <p class="text-sm font-semibold text-green-800">
                        ✅ Équipement en bon état : il sera directement disponible dans le stock décélé pour
                        une future réaffectation.
                    </p>
                </div>
            </div>

            {{-- Observations finales --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-cofina-red mb-2">
                    💬 Observations complémentaires
                </label>
                <textarea name="observations_retour" rows="3"
                    class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:border-cofina-red"
                    placeholder="Accessoires manquants, dommages visibles, notes particulières..."></textarea>
            </div>
        </div>
    </form>

    {{-- ── ÉTAPE 3 : Fiche de Mouvement --}}
    <form id="mouvement-decele-step-form" class="step-form-decele hidden" data-step="3">
        <input type="hidden" name="form_type" value="mouvement_decele">
        <input type="hidden" name="transition_type" value="parc_to_stock_decele">

        <div class="card-cofina bg-white border-2 border-cofina-red">
            <div class="bg-cofina-red text-white p-6 -mx-6 -mt-6 mb-6 rounded-t-lg">
                <h1 class="text-2xl font-bold text-center">COFINA SENEGAL - IT</h1>
                <h2 class="text-xl font-bold text-center mt-2">FICHE DE MOUVEMENT DE MATÉRIEL INFORMATIQUE</h2>
                <div class="mt-4 text-center">
                    <label class="inline-block">
                        Date d'application :
                        <input type="date" name="date_application_mouvement_decele" value="{{ date('Y-m-d') }}"
                            class="ml-2 px-2 py-1 rounded text-gray-900 font-semibold" required>
                    </label>
                </div>
            </div>

            {{-- Expéditeur / Réceptionnaire --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">

                {{-- EXPÉDITEUR : l'utilisateur qui rend l'équipement --}}
                <div class="border-2 border-blue-300 rounded-lg p-6 bg-blue-50">
                    <h3 class="text-lg font-bold text-blue-800 mb-4 text-center border-b-2 border-blue-300 pb-2">
                        EXPÉDITEUR
                    </h3>
                    <p class="text-xs text-blue-600 mb-3 text-center">
                        (Utilisateur qui restitue l'équipement)
                    </p>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1">Nom :</label>
                                <input type="text" name="expediteur_decele_nom" placeholder="Auto-rempli à l'étape 1"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">Prénom :</label>
                                <input type="text" name="expediteur_decele_prenom" placeholder="Auto-rempli à l'étape 1"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Fonction :</label>
                            <input type="text" name="expediteur_decele_fonction" placeholder="Poste de l'utilisateur"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" required>
                        </div>
                    </div>
                </div>

                {{-- RÉCEPTIONNAIRE : agent IT --}}
                <div class="border-2 border-green-300 rounded-lg p-6 bg-green-50">
                    <h3 class="text-lg font-bold text-green-800 mb-4 text-center border-b-2 border-green-300 pb-2">
                        RÉCEPTIONNAIRE
                    </h3>
                    <p class="text-xs text-green-600 mb-3 text-center">
                        (Agent IT qui réceptionne au stock)
                    </p>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1">Nom :</label>
                                <input type="text" name="receptionnaire_decele_nom"
                                    value="{{ auth()->user()->name ?? '' }}"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">Prénom :</label>
                                <input type="text" name="receptionnaire_decele_prenom"
                                    value="{{ auth()->user()->prenom ?? '' }}"
                                    class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Fonction :</label>
                            <input type="text" name="receptionnaire_decele_fonction" value="AGENT IT"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Détails du mouvement --}}
            <div class="border-2 border-gray-300 rounded-lg p-6 bg-gray-50 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">TYPE DE MATÉRIEL</label>
                        <input type="text" value="{{ $equipment->type ?? 'N/A' }}"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">RÉFÉRENCE</label>
                        <input type="text" value="{{ $equipment->numero_serie ?? 'N/A' }}"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded bg-white" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">LIEU DE DÉPART *</label>
                        <input type="text" name="lieu_depart_decele"
                            placeholder="Localisation actuelle de l'équipement"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">DESTINATION *</label>
                        <input type="text" name="destination_decele" placeholder="Ex: Magasin IT - Stock Décélé"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">MOTIF</label>
                        <input type="text" name="motif_decele" value="RETOUR STOCK DÉCÉLÉ"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                    </div>
                </div>
            </div>

            {{-- Signatures --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">

                {{-- Signature expéditeur (utilisateur) --}}
                <div class="border-2 border-blue-300 rounded-lg p-6 bg-blue-50">
                    <h4 class="font-bold text-blue-800 mb-3 text-center">
                        Signature de l'expéditeur
                    </h4>
                    <p class="text-xs text-blue-600 mb-3 text-center">Utilisateur restituant l'équipement</p>
                    <div class="mb-3">
                        <label class="block text-sm font-semibold mb-1">Date :</label>
                        <input type="date" name="date_signature_expediteur_decele" value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                    </div>
                    <div class="signature-pad-container mb-3">
                        <canvas class="signature-pad border-2 border-gray-300 rounded bg-white w-full h-32"
                            id="signatureCanvasExpediteurDecele"></canvas>
                    </div>
                    <div class="flex gap-2 mb-2">
                        <button type="button" class="btn-cofina-outline text-xs py-1 px-2 flex-1"
                            onclick="clearSignatureDecele('expediteur_decele')">Effacer</button>
                        <button type="button" class="btn-cofina text-xs py-1 px-2 flex-1"
                            onclick="saveSignatureDecele('expediteur_decele')">Sauvegarder</button>
                    </div>
                    <input type="hidden" name="signature_expediteur_decele" id="signatureExpediteurDecele">
                    <div class="text-center">
                        <span class="text-sm text-gray-500">Signature Utilisateur / Expéditeur</span>
                    </div>
                </div>

                {{-- Signature réceptionnaire (Agent IT) --}}
                @if(auth()->check() && strtolower(auth()->user()->role) === 'super_admin')
                <div class="border-2 border-green-300 rounded-lg p-6 bg-green-50">
                    <h4 class="font-bold text-green-800 mb-3 text-center">Signature du réceptionnaire</h4>
                    <p class="text-xs text-green-600 mb-3 text-center">Agent IT réceptionnant au stock</p>
                    <div class="mb-3">
                        <label class="block text-sm font-semibold mb-1">Date :</label>
                        <input type="date" name="date_signature_receptionnaire_decele" value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border-2 border-gray-300 rounded" required>
                    </div>
                    <div class="signature-pad-container mb-3">
                        <canvas class="signature-pad border-2 border-gray-300 rounded bg-white w-full h-32"
                            id="signatureCanvasReceptionnaireDecele"></canvas>
                    </div>
                    <div class="flex gap-2 mb-2">
                        <button type="button" class="btn-cofina-outline text-xs py-1 px-2 flex-1"
                            onclick="clearSignatureDecele('receptionnaire_decele')">Effacer</button>
                        <button type="button" class="btn-cofina text-xs py-1 px-2 flex-1"
                            onclick="saveSignatureDecele('receptionnaire_decele')">Sauvegarder</button>
                    </div>
                    <input type="hidden" name="signature_receptionnaire_decele" id="signatureReceptionnaireDecele">
                    <div class="text-center">
                        <span class="text-sm text-gray-500">Signature Agent IT / Réceptionnaire</span>
                    </div>
                </div>
                @else
                <div class="border-2 border-gray-200 rounded-lg p-6 bg-gray-50">
                    <h4 class="font-bold text-gray-600 mb-3 text-center">Signature du réceptionnaire</h4>
                    <div class="text-center py-8">
                        <div class="text-yellow-500 mb-3">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600 font-semibold">Réservé aux Super Administrateurs</p>
                        <p class="text-xs text-gray-500 mt-1">
                            Seul un Super Admin peut compléter la signature de réception
                        </p>
                    </div>
                </div>
                @endif
            </div>

            {{-- NOTA --}}
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                <p class="text-sm font-semibold text-yellow-800">
                    <strong>NOTA :</strong> Tout mouvement de matériel informatique nécessite le remplissage
                    de cette fiche par l'expéditeur et le réceptionnaire qui doivent en garder une copie.
                </p>
            </div>
        </div>
    </form>

{{-- FIN DU PARTIAL — 3 formulaires : #retour-step-form-decele, #deceleration-step-form, #mouvement-decele-step-form --}}