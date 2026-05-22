<div class="doc-prose">
    <h2 id="ch12">12. Contrôles IT</h2>
    <ol>
        <li><strong>Dashboard contrôles</strong> — vue campagnes</li>
        <li><strong>Nouveau contrôle</strong> — planifier, assigner responsables</li>
        <li><strong>Mes tâches</strong> — exécuter, joindre preuves, valider</li>
        <li><strong>Templates</strong> (Super Admin uniquement) — modèles de rapport</li>
    </ol>
    @include('documentation.partials.figure', ['id' => '37-controls-dashboard', 'caption' => 'Figure 37 — Dashboard contrôles IT'])
    @include('documentation.partials.figure', ['id' => '38-controls-tasks', 'caption' => 'Figure 38 — Mes tâches de contrôle'])

    <h2 id="ch13">13. Infrastructure IT</h2>

    <h3>13.1 Coffre mots de passe</h3>
    <ul>
        <li>Liste, création, OTP, partage sécurisé, pièces jointes</li>
    </ul>
    @include('documentation.partials.figure', ['id' => '39-passwords-index', 'caption' => 'Figure 39 — Liste mots de passe'])

    <h3>13.2 Réseau</h3>
    <ul>
        <li>Vue d'ensemble, plans VLAN, branchements locaux</li>
        <li>Filtre <code>?type=plan_adressage</code> ou <code>branchement_local</code></li>
    </ul>
    @include('documentation.partials.figure', ['id' => '40-network-index', 'caption' => 'Figure 40 — Réseau — vue d\'ensemble'])

    <h3>13.3 Licences</h3>
    <ul>
        <li>Suivi Fortinet, FAI, certificats, Office 365, etc.</li>
        <li>Alertes dates d'expiration</li>
    </ul>
    @include('documentation.partials.figure', ['id' => '41-licences-index', 'caption' => 'Figure 41 — Gestion des licences'])

    <h2 id="ch14">14. Configuration</h2>
    <table>
        <thead><tr><th>Référentiel</th><th>URL</th><th>Droits</th></tr></thead>
        <tbody>
            <tr><td>Agences</td><td><code>/agencies</code></td><td>IT / Admin</td></tr>
            <tr><td>Catégories</td><td><code>/categories</code></td><td>IT / Admin</td></tr>
            <tr><td>Fournisseurs</td><td><code>/suppliers</code></td><td>IT / Admin</td></tr>
            <tr><td>Utilisateurs</td><td><code>/admin/users</code></td><td>Super Admin</td></tr>
            <tr><td>Journal d'activité</td><td><code>/audits</code></td><td>IT / Admin</td></tr>
        </tbody>
    </table>
    @include('documentation.partials.figure', ['id' => '42-admin-users', 'caption' => 'Figure 42 — Administration utilisateurs'])
    @include('documentation.partials.figure', ['id' => '43-agencies', 'caption' => 'Figure 43 — Référentiel agences'])

    <h2 id="ch15">15. FAQ et dépannage</h2>
    <table>
        <thead><tr><th>Problème</th><th>Solution</th></tr></thead>
        <tbody>
            <tr><td>« Accès refusé » à l'enregistrement</td><td>Vérifier le rôle ; Agent IT : profil autorisé ; autres formulaires selon <code>roleManager.js</code></td></tr>
            <tr><td>Transition bloquée</td><td>Approbation en attente ; vérifier <strong>admin/approvals</strong></td></tr>
            <tr><td>Export Excel erreur 500</td><td>Vider cache navigateur ; contacter support si persistant</td></tr>
            <tr><td>Signature EOD manquante</td><td>Enregistrer signature dans <strong>Profil → Ma signature</strong></td></tr>
            <tr><td>Menu Change vide</td><td>Sélectionner <strong>role_change</strong> (N1/N2/N3)</td></tr>
            <tr><td>N° série déjà affecté</td><td>Vérifier parc existant ou terminer l'affectation précédente</td></tr>
        </tbody>
    </table>

    <h2 id="annexe-captures">Annexe A — Liste des captures (43 fichiers)</h2>
    <p>Enregistrez chaque capture en PNG dans <code>public/doc-captures/</code> avec le nom exact indiqué. Rechargez la page documentation pour affichage automatique.</p>
    <p>Voir aussi <code>docs/MANUEL_UTILISATION_COMPLET.md</code> et le script <code>scripts/liste-captures-manuel.ps1</code>.</p>

    <h2 id="annexe-urls">Annexe B — URLs utiles (local)</h2>
    <pre>Login          /login
Dashboard      /dashboard
Parc           /parc
Export masse   /equipment/parc/export
Équipements    /equipment
Transitions    /equipment/{id}/transitions
Approbations   /admin/approvals
Change N1      /change/n1
EOD N3         /eod/n3/pending
Documentation  /documentation/manuel-complet</pre>
</div>
