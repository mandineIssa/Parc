<div class="doc-prose">
    <h2 id="ch2">2. Connexion et navigation</h2>

    <h3>2.1 Connexion</h3>
    <ol>
        <li>Ouvrez l'URL de l'application (ex. <code>http://127.0.0.1:8000/login</code> en local).</li>
        <li>Saisissez <strong>email</strong> et <strong>mot de passe</strong> fournis par l'administrateur.</li>
        <li>Cliquez <strong>Se connecter</strong>.</li>
    </ol>
    @include('documentation.partials.figure', [
        'id' => '02-login',
        'caption' => 'Figure 2 — Écran de connexion',
    ])

    <h3>2.2 Déconnexion et profil</h3>
    <ul>
        <li><strong>Profil</strong> : icône utilisateur en haut à droite → modifier nom, email, département, signature.</li>
        <li><strong>Déconnexion</strong> : bas du menu latéral.</li>
    </ul>
    @include('documentation.partials.figure', [
        'id' => '03-profil',
        'caption' => 'Figure 3 — Page profil (informations, signature, mot de passe)',
    ])

    <h3>2.3 Menu latéral</h3>
    <p>Le menu est organisé en sections repliables :</p>
    <ul>
        <li><strong>PARC</strong> — Rapports, Gestion (parc, maintenance…), Équipements, Stocks CELER/DECELER, Documentation</li>
        <li><strong>Infrastructure IT</strong> — Mots de passe, Réseau, Licences</li>
        <li><strong>Change Management</strong> — selon <code>role_change</code></li>
        <li><strong>Contrôles IT</strong></li>
        <li><strong>Incidents</strong></li>
        <li><strong>EOD Suivi</strong></li>
        <li><strong>Configuration</strong> — Agences, catégories, fournisseurs, admin, audits</li>
    </ul>
    @include('documentation.partials.figure', [
        'id' => '04-sidebar-complete',
        'caption' => 'Figure 4 — Menu latéral complet (Agent IT)',
    ])

    <h3>2.4 Tableau de bord</h3>
    <p>Cliquez le logo ou <strong>Tableau de bord</strong> : synthèse KPI selon votre rôle (équipements en stock, en parc, approbations en attente, etc.).</p>
    @include('documentation.partials.figure', [
        'id' => '05-dashboard-agent',
        'caption' => 'Figure 5 — Tableau de bord Agent IT ou Super Admin',
    ])
</div>
