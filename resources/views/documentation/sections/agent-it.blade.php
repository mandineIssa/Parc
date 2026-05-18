<div class="doc-prose">
    <p class="text-lg text-gray-600 mb-6">
        Guide opérationnel pour les agents IT : inventaire, stocks, affectations, transitions d’état,
        imports CSV et suivi du renouvellement.
    </p>

    <h2>1. Vue d’ensemble des modules</h2>
    <table>
        <thead>
            <tr><th>Module (menu)</th><th>Rôle</th></tr>
        </thead>
        <tbody>
            <tr><td>Équipements</td><td>Inventaire global, import/export, renouvellement</td></tr>
            <tr><td>Stocks — CELER</td><td>Entrées stock (informatique, réseau, électronique)</td></tr>
            <tr><td>Stocks — DECELER</td><td>Sorties / déstockage</td></tr>
            <tr><td>Gestion → Parc</td><td>Affectations collaborateurs</td></tr>
            <tr><td>Gestion → Maintenance</td><td>Suivi SAV et réparations</td></tr>
            <tr><td>Gestion → Hors service / Perdu</td><td>Retraits et déclarations</td></tr>
            <tr><td>Rapports</td><td>Exports et analyses</td></tr>
        </tbody>
    </table>

    <h2>2. Créer un équipement</h2>
    <ol>
        <li>Menu <strong>Équipements → Tous les équipements</strong>.</li>
        <li>Cliquez <strong>Nouvel Équipement</strong>.</li>
        <li>Renseignez : type, n° série (unique), marque, modèle, dates, prix, fournisseur, localisation.</li>
        <li>Complétez les champs spécifiques selon le type (Réseau / Informatique / Électronique / Logiciel).</li>
        <li>Enregistrez : le statut initial est généralement <strong>stock</strong>.</li>
    </ol>

    <h2>3. Import CSV équipements</h2>
    <ol>
        <li><strong>Équipements → Import</strong>.</li>
        <li>Téléchargez le <strong>template CSV</strong> (voir section Téléchargements).</li>
        <li>Remplissez le fichier (séparateur <code>;</code>, encodage UTF-8).</li>
        <li>Importez et vérifiez les erreurs ligne par ligne en cas d’échec.</li>
    </ol>
    <div class="doc-box-warn">
        Le n° série doit être unique. Les doublons sont rejetés à l’import.
    </div>

    <h2>4. Affecter un équipement au parc</h2>
    <ol>
        <li><strong>Gestion → Parc → Nouvelle affectation</strong>.</li>
        <li><strong>Saisir le numéro de série</strong> de l’équipement (champ texte avec suggestions).</li>
        <li>Renseigner l’utilisateur : nom, prénom, département, poste, position.</li>
        <li>Date d’affectation, statut d’usage (<code>actif</code>, <code>inactif</code>, <code>en_pret</code>).</li>
        <li>Valider : l’équipement passe en statut <strong>parc</strong> et un n° de bon d’affectation est généré.</li>
    </ol>

    <h2>5. Transitions d’état (workflow)</h2>
    <p>Depuis la fiche équipement, <strong>Changer statut</strong> permet les mouvements typiques :</p>
    <ul>
        <li><strong>Stock → Parc</strong> : mise en service chez un utilisateur</li>
        <li><strong>Parc → Maintenance</strong> : envoi en réparation</li>
        <li><strong>Parc → Hors service</strong> : retrait définitif ou temporaire</li>
        <li><strong>Parc → Perdu</strong> : déclaration de perte/vol</li>
        <li>Retours vers stock après validation des approbations requises</li>
    </ul>
    <p>Les transitions sensibles peuvent déclencher un <strong>circuit d’approbation</strong> (N+1, N+2, N+3) avec pièces jointes et checklist.</p>

    <h2>6. Plan de renouvellement</h2>
    <p><strong>Équipements → Renouvellement</strong> :</p>
    <ul>
        <li>Calcul automatique de l’âge (date mise en service ou livraison).</li>
        <li>Seuils : orange ≥ 2 ans, rouge ≥ 3 ans (configurables via <code>.env</code>).</li>
        <li>Tri par priorité (plus ancien en premier).</li>
        <li>Filtres par niveau, statut métier, recherche.</li>
    </ul>

    <h2>7. Stocks CELER / DECELER</h2>
    <h3>CELER (entrée stock)</h3>
    <p>Dashboards par famille : Informatique, Réseau, Électronique. Permet de visualiser et exporter le stock entrant.</p>
    <h3>DECELER (sortie stock)</h3>
    <p>Même logique pour les sorties et équipements retirés du stock actif.</p>

    <h2>8. Maintenance, hors service, perdu</h2>
    <ul>
        <li><strong>Maintenance</strong> : dates, prestataire, coût, statut (en cours / terminée / annulée).</li>
        <li><strong>Hors service</strong> : raison (panne, obsolescence…), statut traitement.</li>
        <li><strong>Perdu</strong> : type de disparition, statut de recherche, plainte éventuelle.</li>
    </ul>

    <h2>9. Réaffectations</h2>
    <p><strong>Gestion → Historique Réaffectations</strong> : trace les changements d’utilisateur ou de localisation sur un même équipement.</p>

    <h2>10. Bonnes pratiques</h2>
    <ul>
        <li>Toujours vérifier le n° série avant toute transition.</li>
        <li>Joindre les pièces justificatives aux demandes d’approbation.</li>
        <li>Mettre à jour la date de mise en service à l’affectation réelle.</li>
        <li>Exporter régulièrement l’inventaire pour sauvegarde locale.</li>
    </ul>
</div>
