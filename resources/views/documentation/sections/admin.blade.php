<div class="doc-prose">
    <p class="text-lg text-gray-600 mb-6">
        Guide réservé aux administrateurs et validateurs : utilisateurs, rôles, approbations,
        configuration référentielle et modules avancés (Change, EOD, Contrôles, Incidents).
    </p>

    <h2>1. Rôles et permissions</h2>
    <p>L’application s’appuie sur <strong>Spatie Laravel Permission</strong> (rôles + permissions).</p>
    <ul>
        <li><strong>Super Admin</strong> : accès étendu (emails listés dans <code>SUPER_ADMIN_EMAILS</code> / <code>config/cofina.php</code>).</li>
        <li><strong>Validateurs Change</strong> : N+1, N+2, N+3 pour les tickets de changement.</li>
        <li><strong>Agents IT</strong> : gestion opérationnelle du parc.</li>
        <li>Profils personnalisés selon déploiement COFINA.</li>
    </ul>

    <h2>2. Gestion des utilisateurs</h2>
    <ol>
        <li>Menu <strong>Configuration → Utilisateurs</strong> (selon droits).</li>
        <li>Créer / modifier un compte (nom, email, mot de passe).</li>
        <li>Attribuer un ou plusieurs rôles.</li>
        <li>Désactiver un compte en cas de départ (ne pas supprimer l’historique).</li>
    </ol>

    <h2>3. Circuit d’approbations (transitions)</h2>
    <p>Les transitions critiques (ex. parc → hors service, maintenance → stock) peuvent nécessiter :</p>
    <ul>
        <li>Soumission avec formulaire et checklist</li>
        <li>Validation N+1 puis N+2 / N+3</li>
        <li>Pièces jointes (PDF, photos, bons)</li>
        <li>Génération de fiches mouvement / installation</li>
    </ul>
    <p>Accès : menu <strong>Gestion → Pièces jointes Approbation</strong> (si une demande est en cours) ou listes d’approbations dédiées.</p>

    <h2>4. Configuration référentielle</h2>
    <table>
        <thead>
            <tr><th>Entité</th><th>Usage</th></tr>
        </thead>
        <tbody>
            <tr><td>Agences</td><td>Sites et localisations</td></tr>
            <tr><td>Fournisseurs</td><td>Liens achats / garanties</td></tr>
            <tr><td>Catégories</td><td>Typologie équipements</td></tr>
            <tr><td>Utilisateurs</td><td>Comptes et rôles</td></tr>
            <tr><td>Audits</td><td>Journal des actions sensibles</td></tr>
        </tbody>
    </table>

    <h2>5. Change Management</h2>
    <p>Module <strong>Change</strong> avec niveaux :</p>
    <ul>
        <li><strong>N+1</strong> : création et suivi des demandes de changement</li>
        <li><strong>N+2</strong> : validation intermédiaire</li>
        <li><strong>N+3</strong> : validation finale (badge visible sur le dashboard)</li>
    </ul>
    <p>Chaque niveau dispose de formulaires dédiés, listes et workflows de validation.</p>

    <h2>6. EOD Suivi</h2>
    <p>Suivi de fin de journée (End of Day) : signatures et contrôles spécifiques selon profil utilisateur.</p>

    <h2>7. Infrastructure IT</h2>
    <ul>
        <li><strong>Mots de passe</strong> : coffre applicatif interne</li>
        <li><strong>Adresses réseau</strong> : plan d’adressage</li>
        <li><strong>Licences</strong> : suivi éditeurs, volumes, expirations</li>
    </ul>

    <h2>8. Contrôles IT</h2>
    <ul>
        <li>Tableau de bord contrôles</li>
        <li>Campagnes et tâches de contrôle</li>
        <li>Modèles (templates) pour checklists récurrentes</li>
    </ul>

    <h2>9. Incidents</h2>
    <p>Fiches incidents : création, suivi, typologie (logiciel, matériel, réseau, application, infrastructure).</p>

    <h2>10. Audits et traçabilité</h2>
    <p>Le module <strong>Audits</strong> enregistre les modifications sur les entités sensibles. En production, les logs applicatifs ne doivent pas contenir de données personnelles en clair (<code>SecureLog</code>).</p>

    <h2>11. Sécurité — recommandations production</h2>
    <div class="doc-box-warn">
        <ul class="mb-0">
            <li><code>APP_DEBUG=false</code>, HTTPS obligatoire</li>
            <li>Réduire la liste <code>SUPER_ADMIN_EMAILS</code></li>
            <li>Sauvegardes MySQL régulières</li>
            <li>Désactiver <code>routes/debug.php</code> (déjà limité à <code>local</code>)</li>
        </ul>
    </div>
</div>
