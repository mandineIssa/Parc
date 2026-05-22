<div class="doc-prose">
    <h2 id="ch8">8. Rapports</h2>
    <p>Menu <strong>PARC → Rapports</strong> :</p>
    <ul>
        <li><strong>Vue d'ensemble</strong> — KPI globaux</li>
        <li><strong>Équipements</strong> — inventaire détaillé</li>
        <li><strong>Financier</strong> — valorisation</li>
        <li><strong>Maintenance</strong> — si module actif</li>
        <li><strong>Importer / Exporter</strong> — échanges CSV/Excel</li>
    </ul>
    @include('documentation.partials.figure', ['id' => '26-reports-overview', 'caption' => 'Figure 26 — Rapports vue d\'ensemble'])

    <h2 id="ch9">9. Change Management</h2>
    <div class="doc-box-warn">
        Prérequis : sélectionner un rôle Change (<strong>N1</strong>, <strong>N2</strong> ou <strong>N3</strong>) via
        <strong>Change Management → Sélectionner un rôle</strong> (stocké en session).
    </div>

    <h3>9.1 Choisir le rôle Change</h3>
    @include('documentation.partials.figure', ['id' => '27-change-role-select', 'caption' => 'Figure 27 — Sélection du rôle Change'])

    <div class="doc-scenario">
        <h4>Scénario Change — N1 (demandeur)</h4>
        <ol>
            <li><strong>N+1 → Nouveau formulaire</strong></li>
            <li>Décrire le changement, impact, planning</li>
            <li>Soumettre vers N2</li>
        </ol>
        @include('documentation.partials.figure', ['id' => '28-change-n1-create', 'caption' => 'Figure 28 — Création ticket Change N1'])
    </div>

    <div class="doc-scenario">
        <h4>Scénario Change — N2 (technicien)</h4>
        <ol>
            <li><strong>N+2 → À traiter</strong></li>
            <li>Analyser, compléter, joindre preuves</li>
            <li>Transmettre à N3 ou renvoyer à N1</li>
        </ol>
        @include('documentation.partials.figure', ['id' => '29-change-n2-index', 'caption' => 'Figure 29 — File N2'])
    </div>

    <div class="doc-scenario">
        <h4>Scénario Change — N3 (validation)</h4>
        <ol>
            <li><strong>N+3 → Validation finale</strong></li>
            <li>Approuver, rejeter ou demander compléments</li>
        </ol>
        @include('documentation.partials.figure', ['id' => '30-change-n3-index', 'caption' => 'Figure 30 — Validation N3'])
    </div>

    <h2 id="ch10">10. EOD Suivi (fin de journée)</h2>
    <p>Même logique de rôles (<code>role_change</code> N1 / N2 / N3 / CONTROLLER).</p>

    <div class="doc-scenario">
        <h4>Scénario EOD — N1</h4>
        <p>Créer la fiche journalière, activités, incidents du jour.</p>
        @include('documentation.partials.figure', ['id' => '31-eod-n1-create', 'caption' => 'Figure 31 — Nouvelle fiche EOD N1'])
    </div>

    <div class="doc-scenario">
        <h4>Scénario EOD — N2</h4>
        <p>Compléter et transmettre ; export PDF possible.</p>
        @include('documentation.partials.figure', ['id' => '32-eod-n2-index', 'caption' => 'Figure 32 — Fiches EOD N2'])
    </div>

    <div class="doc-scenario">
        <h4>Scénario EOD — N3 (signature)</h4>
        <p><strong>Fiches à signer</strong> : signature électronique (profil préalablement enregistré).</p>
        @include('documentation.partials.figure', ['id' => '33-eod-n3-pending', 'caption' => 'Figure 33 — File de signature N3'])
    </div>

    <div class="doc-scenario">
        <h4>Scénario EOD — Contrôleur (batch)</h4>
        <p>Menu <strong>EOD Suivi → Validation Controller</strong> pour valider plusieurs fiches.</p>
        @include('documentation.partials.figure', ['id' => '34-eod-controller', 'caption' => 'Figure 34 — Validation batch Controller'])
    </div>

    <h2 id="ch11">11. Incidents</h2>
    <ol>
        <li><strong>N1 ou Super Admin</strong> : <strong>Nouvelle fiche</strong></li>
        <li>Description, priorité, catégorie</li>
        <li><strong>N2</strong> : prise en charge technique</li>
        <li><strong>N3</strong> : clôture et validation</li>
    </ol>
    @include('documentation.partials.figure', ['id' => '35-incidents-create', 'caption' => 'Figure 35 — Création incident'])
    @include('documentation.partials.figure', ['id' => '36-incidents-list', 'caption' => 'Figure 36 — Liste des incidents'])
</div>
