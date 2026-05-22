<div class="doc-prose">
    <h2 id="ch3">3. Équipements et inventaire</h2>

    <h3>3.1 Liste des équipements</h3>
    <p>Menu <strong>PARC → Équipements → Tous les équipements</strong> (<code>/equipment</code>).</p>
    <ul>
        <li>Recherche par n° série, marque, modèle, codification</li>
        <li>Filtres type, statut, état</li>
        <li>Actions : voir, modifier, <strong>Changer statut</strong> (transitions)</li>
    </ul>
    @include('documentation.partials.figure', [
        'id' => '06-equipment-list',
        'caption' => 'Figure 6 — Liste des équipements avec filtres',
    ])

    <h3>3.2 Créer un équipement</h3>
    <ol>
        <li><strong>Nouvel Équipement</strong></li>
        <li>Étape 1 : type (Informatique, Réseau, Électronique, Logiciel…), catégorie, n° série <em>unique</em></li>
        <li>Étape 2 : marque, modèle, nom, fournisseur, prix, dates</li>
        <li>Champs spécifiques selon le type (processeur, ports switch, etc.)</li>
        <li>Enregistrer → statut initial <strong>stock</strong></li>
    </ol>
    @include('documentation.partials.figure', [
        'id' => '07-equipment-create',
        'caption' => 'Figure 7 — Formulaire création équipement (étapes)',
    ])

    <h3>3.3 Import / export équipements</h3>
    <table>
        <thead><tr><th>Action</th><th>Chemin</th></tr></thead>
        <tbody>
            <tr><td>Template CSV</td><td>Équipements → Import → Télécharger le modèle</td></tr>
            <tr><td>Import</td><td>Fichier <code>;</code> UTF-8, une ligne = un équipement</td></tr>
            <tr><td>Export</td><td>Équipements → Export</td></tr>
        </tbody>
    </table>
    @include('documentation.partials.figure', [
        'id' => '08-equipment-import',
        'caption' => 'Figure 8 — Écran import CSV équipements',
    ])

    <h3>3.4 Plan de renouvellement</h3>
    <p><strong>Équipements → Renouvellement</strong> : priorisation par âge (seuils orange/rouge configurables).</p>
    @include('documentation.partials.figure', [
        'id' => '09-equipment-renewal',
        'caption' => 'Figure 9 — Plan de renouvellement',
    ])

    <h2 id="ch4">4. Stocks CELER et DECELER</h2>
    <p><strong>CELER</strong> = entrées en stock ; <strong>DECELER</strong> = sorties. Trois familles : Informatique, Réseau, Électronique.</p>

    <h3>4.1 Dashboard CELER</h3>
    <ol>
        <li>Menu <strong>Stocks CELER → Informatique</strong> (ou Réseau / Électronique)</li>
        <li>Consultez les équipements au statut <strong>stock</strong></li>
        <li>Export possible depuis le tableau de bord stock</li>
    </ol>
    @include('documentation.partials.figure', [
        'id' => '10-stock-celer',
        'caption' => 'Figure 10 — Dashboard stock CELER informatique',
    ])

    <h3>4.2 Dashboard DECELER</h3>
    <p>Même principe pour visualiser les sorties et mouvements de déstockage.</p>
    @include('documentation.partials.figure', [
        'id' => '11-stock-deceler',
        'caption' => 'Figure 11 — Dashboard stock DECELER',
    ])
</div>
