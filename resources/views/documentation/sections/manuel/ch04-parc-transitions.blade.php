<div class="doc-prose">
    <h2 id="ch5">5. Parc informatique et affectations</h2>

    <h3>5.1 Liste du parc</h3>
    <p>Menu <strong>PARC → Gestion → Parc</strong> (<code>/parc</code>) : équipements au statut <strong>parc</strong> avec affectation.</p>
    @include('documentation.partials.figure', [
        'id' => '12-parc-index',
        'caption' => 'Figure 12 — Parc d\'équipements (statistiques, filtres, liste)',
    ])

    <h3>5.2 Nouvelle affectation simple</h3>
    <ol>
        <li><strong>Nouvelle affectation</strong></li>
        <li>Saisir le <strong>numéro de série</strong></li>
        <li>Nom, prénom, département, poste, position, dates</li>
        <li>Valider → bon d'affectation généré (<code>AFF-…</code>)</li>
    </ol>
    @include('documentation.partials.figure', [
        'id' => '13-parc-create',
        'caption' => 'Figure 13 — Formulaire nouvelle affectation',
    ])

    <h3>5.3 Export en masse (Excel COFINA)</h3>
    <ol>
        <li>Sur la page Parc, cliquez <strong>Export en masse</strong></li>
        <li>Fichier <code>.xlsx</code> : colonnes NOM, PRENOM, AGENCE, n° série, marque, modèle, dates, fournisseur, état (BON/MOYEN/MAUVAIS)</li>
        <li>Les filtres actifs (recherche, type, état) sont repris dans l'export</li>
    </ol>
    @include('documentation.partials.figure', [
        'id' => '14-parc-export-masse',
        'caption' => 'Figure 14 — Fichier Excel exporté (feuille Parc, en-tête rouge)',
    ])

    <h3>5.4 Import parc et réaffectations</h3>
    <ul>
        <li><strong>Import Équipements</strong> sur la page parc : import CSV direct vers parc</li>
        <li><strong>Historique réaffectations</strong> : traçabilité des changements d'utilisateur</li>
    </ul>

    <h2 id="ch6">6. Transitions — tous les scénarios</h2>
    <p>Accès : fiche équipement → <strong>Changer statut</strong> ou URL <code>/equipment/{id}/transitions</code>.</p>
    @include('documentation.partials.figure', [
        'id' => '15-transitions-menu',
        'caption' => 'Figure 15 — Choix du type de transition',
    ])

    <div class="doc-scenario">
        <h4>Scénario A — Stock → Parc (affectation)</h4>
        <ol>
            <li>Équipement en <strong>stock</strong> → transition <strong>Stock vers Parc</strong></li>
            <li>Renseigner utilisateur, agence, département, poste, dates d'affectation</li>
            <li>Signatures installation / vérification si formulaire complet</li>
            <li>Soumission → approbation si requise → équipement en <strong>parc</strong></li>
        </ol>
        @include('documentation.partials.figure', ['id' => '16-transition-stock-parc', 'caption' => 'Figure 16 — Formulaire Stock → Parc'])
    </div>

    <div class="doc-scenario">
        <h4>Scénario B — Parc → Maintenance</h4>
        <ol>
            <li>Depuis le parc, envoyer en <strong>maintenance</strong> (panne, SAV)</li>
            <li>Renseigner prestataire, dates, coût estimé</li>
            <li>Approbation puis statut maintenance</li>
        </ol>
        @include('documentation.partials.figure', ['id' => '17-transition-parc-maintenance', 'caption' => 'Figure 17 — Parc → Maintenance'])
    </div>

    <div class="doc-scenario">
        <h4>Scénario C — Maintenance → Stock</h4>
        <p>Retour en stock après réparation validée.</p>
        @include('documentation.partials.figure', ['id' => '18-transition-maintenance-stock', 'caption' => 'Figure 18 — Maintenance → Stock'])
    </div>

    <div class="doc-scenario">
        <h4>Scénario D — Parc → Hors service</h4>
        <p>Retrait définitif ou temporaire (obsolescence, panne irréparable).</p>
        @include('documentation.partials.figure', ['id' => '19-transition-parc-hors-service', 'caption' => 'Figure 19 — Parc → Hors service'])
    </div>

    <div class="doc-scenario">
        <h4>Scénario E — Parc → Perdu</h4>
        <p>Déclaration perte ou vol avec motif et pièces si nécessaire.</p>
        @include('documentation.partials.figure', ['id' => '20-transition-parc-perdu', 'caption' => 'Figure 20 — Parc → Perdu'])
    </div>

    <div class="doc-scenario">
        <h4>Scénario F — Stock → Hors service</h4>
        <p>Équipement jamais mis en service mais retiré du stock actif.</p>
    </div>

    <div class="doc-scenario">
        <h4>Scénario G — Affectation simple (formulaire parc)</h4>
        <p>Alternative rapide sans workflow multi-étapes : <strong>Parc → Nouvelle affectation</strong>.</p>
    </div>

    <h3>6.1 Circuit d'approbation</h3>
    <ol>
        <li>Validateur : menu admin ou notification → <strong>Approbations</strong></li>
        <li>Consulter checklist, pièces jointes, signatures</li>
        <li><strong>Approuver</strong> ou <strong>Rejeter</strong> avec motif</li>
    </ol>
    @include('documentation.partials.figure', [
        'id' => '21-approbation-detail',
        'caption' => 'Figure 21 — Détail d\'une demande d\'approbation',
    ])
    @include('documentation.partials.figure', [
        'id' => '22-approbations-liste',
        'caption' => 'Figure 22 — Liste des approbations en attente',
    ])

    <h2 id="ch7">7. Maintenance, hors service, perdu</h2>
    <table>
        <thead><tr><th>Module</th><th>Menu</th><th>Actions</th></tr></thead>
        <tbody>
            <tr><td>Maintenance</td><td>Gestion → Maintenances</td><td>Créer, suivre, terminer, annuler</td></tr>
            <tr><td>Hors service</td><td>Gestion → Hors service</td><td>Consulter, traiter, approuver</td></tr>
            <tr><td>Perdu</td><td>Gestion → Perdu</td><td>Déclarer, suivre recherche</td></tr>
        </tbody>
    </table>
    @include('documentation.partials.figure', ['id' => '23-maintenance-index', 'caption' => 'Figure 23 — Liste maintenances'])
    @include('documentation.partials.figure', ['id' => '24-hors-service-index', 'caption' => 'Figure 24 — Liste hors service'])
    @include('documentation.partials.figure', ['id' => '25-perdu-index', 'caption' => 'Figure 25 — Liste équipements perdus'])
</div>
