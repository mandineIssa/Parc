<div class="doc-prose">
    <p class="text-lg text-gray-600 mb-6">
        Ce guide s’adresse aux collaborateurs qui consultent le parc, suivent leurs équipements affectés
        ou interagissent avec les demandes IT sans administrer le système.
    </p>

    <div class="doc-box-cofina">
        <strong>Application :</strong> Gestion Parc Informatique COFINA — version 1.0<br>
        <strong>Objectif :</strong> centraliser l’inventaire, les affectations, les mouvements et le suivi du cycle de vie des équipements IT.
    </div>

    <h2>1. Accès et connexion</h2>
    <ol>
        <li>Ouvrez l’URL fournie par le service IT (ex. <code>http://127.0.0.1:8000</code> en local).</li>
        <li>Saisissez votre <strong>adresse e-mail</strong> et votre <strong>mot de passe</strong>.</li>
        <li>Validez avec <strong>Se connecter</strong>.</li>
        <li>En cas d’oubli de mot de passe, utilisez le lien « Mot de passe oublié » (si activé).</li>
    </ol>

    <h2>2. Interface générale</h2>
    <p>Après connexion, vous disposez de :</p>
    <ul>
        <li><strong>En-tête</strong> : logo COFINA, profil utilisateur, badge de rôle.</li>
        <li><strong>Menu latéral</strong> : modules accessibles selon vos droits (Rapports, Équipements, Gestion, etc.).</li>
        <li><strong>Zone centrale</strong> : tableaux de bord, listes et formulaires.</li>
    </ul>

    <h2>3. Tableau de bord</h2>
    <p>Le tableau de bord affiche une synthèse selon votre profil :</p>
    <ul>
        <li>Indicateurs clés (nombre d’équipements, répartitions par statut ou type).</li>
        <li>Accès rapides aux modules autorisés.</li>
        <li>Notifications ou validations en attente (validateurs N+1, N+2, N+3).</li>
    </ul>

    <h2>4. Consulter le parc d’équipements</h2>
    <p>Si vous avez accès au module <strong>Gestion → Parc</strong> :</p>
    <ol>
        <li>Ouvrez <strong>Parc</strong> dans le menu latéral.</li>
        <li>Utilisez la recherche (n° série, nom, modèle, utilisateur, agence).</li>
        <li>Filtrez par type (Réseau, Informatique, Électronique) ou par état.</li>
        <li>Cliquez sur une ligne pour ouvrir la fiche équipement (détails, historique).</li>
    </ol>

    <h3>Statuts courants d’un équipement</h3>
    <table>
        <thead>
            <tr><th>Statut</th><th>Signification</th></tr>
        </thead>
        <tbody>
            <tr><td>Stock</td><td>Équipement disponible, non affecté</td></tr>
            <tr><td>Parc</td><td>Équipement affecté à un collaborateur / site</td></tr>
            <tr><td>Maintenance</td><td>En réparation ou suivi SAV</td></tr>
            <tr><td>Hors service</td><td>Retiré du parc actif</td></tr>
            <tr><td>Perdu</td><td>Déclaration de perte ou vol</td></tr>
        </tbody>
    </table>

    <h2>5. Renouvellement et cycle de vie</h2>
    <p>Le module <strong>Équipements → Renouvellement</strong> calcule automatiquement l’ancienneté à partir de la date de mise en service (ou de livraison) :</p>
    <ul>
        <li><strong>Vert — Récent</strong> : moins de 2 ans</li>
        <li><strong>Orange — Seuil de référence</strong> : entre 2 et 3 ans (planifier)</li>
        <li><strong>Rouge — À remplacer</strong> : 3 ans et plus (priorité renouvellement)</li>
    </ul>

    <h2>6. Rapports (lecture seule)</h2>
    <p>Le menu <strong>Rapports</strong> permet d’exporter ou visualiser :</p>
    <ul>
        <li>Vue d’ensemble du parc</li>
        <li>Rapports équipements et financiers</li>
        <li>Exports CSV selon droits</li>
    </ul>

    <h2>7. Documentation</h2>
    <p>Vous êtes sur la page <strong>Documentation</strong>. Utilisez les cartes pour :</p>
    <ul>
        <li>Guide utilisateur (cette page)</li>
        <li>Guide agent IT (procédures techniques)</li>
        <li>Guide administrateur (approbations, utilisateurs)</li>
    </ul>

    <h2>8. Support</h2>
    <div class="doc-box-info">
        <p class="mb-0">
            <strong>Service IT COFINA</strong><br>
            Pour toute anomalie d’accès, demande d’affectation ou incident matériel, contactez le support IT interne.<br>
            Indiquez toujours le <strong>numéro de série</strong> de l’équipement concerné.
        </p>
    </div>
</div>
