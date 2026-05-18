<div class="doc-prose">
    <p class="text-lg text-gray-600 mb-6">
        Documentation technique de l’application Laravel : architecture, routes modulaires,
        authentification web et points d’extension.
    </p>

    <div class="doc-box-info">
        Cette application est principalement <strong>web (Blade + sessions)</strong>.
        Il n’existe pas d’API REST publique complète ; les routes ci-dessous sont les entrées HTTP métier.
    </div>

    <h2>1. Stack technique</h2>
    <ul>
        <li><strong>Laravel 12</strong> — PHP 8.2+</li>
        <li><strong>MySQL</strong> — persistance</li>
        <li><strong>Blade + Tailwind</strong> — interface</li>
        <li><strong>Vite</strong> — assets front</li>
        <li><strong>Spatie Permission</strong> — RBAC</li>
        <li><strong>PHPUnit / PHPStan</strong> — qualité</li>
    </ul>

    <h2>2. Structure des routes</h2>
    <p>Point d’entrée : <code>routes/web.php</code>, puis modules :</p>
    <pre>routes/web/v01_verified_equipment_parc.php   — Équipements, parc, transitions
routes/web/v02a…v02f_*.php                   — Approbations, admin users
routes/web/v03_reports_documentation.php   — Rapports, documentation
routes/web/v04_workflow_transitions.php    — Workflows
routes/web/v05_dashboards.php              — Tableaux de bord
routes/web/v06_change_eod.php                — Change & EOD
routes/web/v07_passwords_network_licences.php
routes/web/v08_controls.php                  — Contrôles IT
routes/web/v09_incidents.php                 — Incidents
routes/debug.php                             — Local uniquement</pre>

    <h2>3. Routes documentation</h2>
    <table>
        <thead>
            <tr><th>Méthode</th><th>URI</th><th>Nom</th></tr>
        </thead>
        <tbody>
            <tr><td>GET</td><td>/documentation</td><td>documentation.index</td></tr>
            <tr><td>GET</td><td>/documentation/{section}</td><td>documentation.show</td></tr>
            <tr><td>GET</td><td>/documentation/download/{format}</td><td>documentation.download</td></tr>
        </tbody>
    </table>
    <p>Sections : <code>utilisateur</code>, <code>agent-it</code>, <code>admin</code>, <code>api</code>, <code>installation</code>.</p>

    <h2>4. Équipements & parc (extraits)</h2>
    <table>
        <thead>
            <tr><th>Nom de route</th><th>Description</th></tr>
        </thead>
        <tbody>
            <tr><td>equipment.index</td><td>Liste inventaire</td></tr>
            <tr><td>equipment.store</td><td>Création équipement</td></tr>
            <tr><td>equipment.renewal</td><td>Plan renouvellement</td></tr>
            <tr><td>equipment.import.form / equipment.import</td><td>Import CSV</td></tr>
            <tr><td>parc.index</td><td>Liste parc</td></tr>
            <tr><td>parc.create / parc.store</td><td>Nouvelle affectation</td></tr>
            <tr><td>parc.import.template</td><td>Template CSV parc</td></tr>
        </tbody>
    </table>

    <h2>5. Authentification</h2>
    <ul>
        <li><strong>Laravel Breeze</strong> : session cookie, middleware <code>auth</code> + <code>verified</code></li>
        <li>Gates dans <code>app/Providers/AuthServiceProvider.php</code></li>
        <li>Config COFINA : <code>config/cofina.php</code> (<code>SUPER_ADMIN_EMAILS</code>)</li>
    </ul>

    <h2>6. Modèle Equipment — renouvellement</h2>
    <p>Config : <code>config/equipment_renewal.php</code></p>
    <ul>
        <li><code>EQUIPMENT_RENEWAL_ORANGE_YEARS</code> (défaut 2)</li>
        <li><code>EQUIPMENT_RENEWAL_RED_YEARS</code> (défaut 3)</li>
    </ul>
    <p>Méthodes : <code>lifecycleReferenceDate()</code>, <code>niveauRenouvellement()</code>, scopes SQL associés.</p>

    <h2>7. Contrôleurs principaux</h2>
    <table>
        <thead>
            <tr><th>Contrôleur</th><th>Domaine</th></tr>
        </thead>
        <tbody>
            <tr><td>EquipmentController</td><td>CRUD équipements, export, renouvellement</td></tr>
            <tr><td>ParcController</td><td>Affectations, import parc</td></tr>
            <tr><td>TransitionController</td><td>Workflows de transition (volumineux)</td></tr>
            <tr><td>ApprovalController</td><td>Approbations</td></tr>
            <tr><td>DocumentationController</td><td>Cette documentation</td></tr>
        </tbody>
    </table>

    <h2>8. Tests & CI</h2>
    <pre>php artisan test
./vendor/bin/phpstan analyse</pre>
    <p>PHPUnit cible MySQL (base <code>testing</code>). Voir <code>.env.testing.example</code>.</p>

    <h2>9. Logs sécurisés</h2>
    <p>Utiliser <code>App\Support\SecureLog::requestPayload()</code> pour journaliser les requêtes sans exposer de secrets.</p>
</div>
