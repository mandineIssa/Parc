<div class="doc-prose">
    <h2 id="ch1">1. Présentation de l'application</h2>
    <p>
        <strong>Gestion Parc Informatique COFINA</strong> centralise l'inventaire IT, les stocks (CELER / DECELER),
        les affectations au parc, les transitions d'état, les approbations, la maintenance, le Change Management,
        le suivi EOD, les incidents, les contrôles IT et l'infrastructure (mots de passe, réseau, licences).
    </p>

    @include('documentation.partials.figure', [
        'id' => '01-accueil-dashboard',
        'caption' => 'Figure 1 — Tableau de bord après connexion (logo COFINA, menu latéral, indicateurs)',
    ])

    <h3>1.1 Objectifs métier</h3>
    <ul>
        <li>Tracer chaque équipement par <strong>numéro de série</strong> de l'entrée stock jusqu'à la sortie (hors service, perdu, réforme).</li>
        <li>Documenter les <strong>affectations</strong> (utilisateur, agence, département, poste).</li>
        <li>Valider les mouvements sensibles via un <strong>circuit d'approbation</strong>.</li>
        <li>Produire des <strong>rapports et exports Excel</strong> alignés sur le modèle COFINA.</li>
    </ul>

    <h3>1.2 Profils utilisateurs</h3>
    <table>
        <thead><tr><th>Rôle (<code>users.role</code>)</th><th>Usage principal</th></tr></thead>
        <tbody>
            <tr><td><strong>Super Admin</strong></td><td>Tout le système, utilisateurs, approbations, templates contrôles</td></tr>
            <tr><td><strong>Agent IT</strong></td><td>Équipements, stocks, parc, transitions, imports/exports, maintenance</td></tr>
            <tr><td><strong>Utilisateur</strong></td><td>Consultation, profil ; Change/EOD/Incidents si rôle workflow activé</td></tr>
            <tr><td><strong>Signataire EOD N+3 / Contrôleur</strong></td><td>Signatures et validation batch EOD</td></tr>
        </tbody>
    </table>

    <h3>1.3 Rôle workflow Change / EOD / Incidents (<code>role_change</code>)</h3>
    <table>
        <thead><tr><th>Valeur</th><th>Modules</th></tr></thead>
        <tbody>
            <tr><td><strong>N1</strong></td><td>Création demandes Change, fiches EOD, création incidents</td></tr>
            <tr><td><strong>N2</strong></td><td>Traitement technique Change et EOD</td></tr>
            <tr><td><strong>N3</strong></td><td>Validation finale Change ; signature EOD</td></tr>
            <tr><td><strong>CONTROLLER</strong></td><td>Validation batch EOD (menu dédié)</td></tr>
        </tbody>
    </table>

    <div class="doc-box-info">
        Un même compte peut cumuler <em>Agent IT</em> + <em>N2</em> : le menu affiche alors les modules PARC et Change/EOD selon le contexte.
    </div>
</div>
