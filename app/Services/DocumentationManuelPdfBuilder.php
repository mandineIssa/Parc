<?php

namespace App\Services;

class DocumentationManuelPdfBuilder
{
    public function chapters(): array
    {
        $fig = fn (string $id, string $caption) => [
            'path' => public_path('doc-captures/' . $id . '.png'),
            'caption' => $caption,
        ];

        return [
            [
                'num' => 1,
                'title' => 'Présentation',
                'html' => '<p>Application centralisée : inventaire, stocks CELER/DECELER, parc, transitions, approbations, maintenance, Change, EOD, incidents, contrôles, infrastructure IT.</p>
                <table><tr><th>Rôle</th><th>Usage</th></tr>
                <tr><td>Super Admin</td><td>Tout, utilisateurs, approbations</td></tr>
                <tr><td>Agent IT</td><td>Équipements, parc, stocks, exports</td></tr>
                <tr><td>Utilisateur</td><td>Consultation, profil, workflows N1/N2/N3</td></tr></table>',
                'figures' => [$fig('01-accueil-dashboard', 'Figure 1 — Tableau de bord')],
            ],
            [
                'num' => 2,
                'title' => 'Connexion et navigation',
                'html' => '<ol><li>Accéder à <code>/login</code></li><li>Saisir email et mot de passe</li><li>Menu latéral : PARC, Infrastructure, Change, Contrôles, Incidents, EOD, Configuration</li><li>Profil : signature pour EOD/Change</li></ol>',
                'figures' => [
                    $fig('02-login', 'Figure 2 — Connexion'),
                    $fig('03-profil', 'Figure 3 — Profil'),
                    $fig('04-sidebar-complete', 'Figure 4 — Menu'),
                ],
            ],
            [
                'num' => 3,
                'title' => 'Équipements',
                'html' => '<p><strong>/equipment</strong> — Création, import CSV (template), export, renouvellement. Le n° série est unique.</p>',
                'figures' => [
                    $fig('06-equipment-list', 'Figure 6 — Liste'),
                    $fig('07-equipment-create', 'Figure 7 — Création'),
                    $fig('08-equipment-import', 'Figure 8 — Import'),
                ],
            ],
            [
                'num' => 4,
                'title' => 'Stocks CELER / DECELER',
                'html' => '<p>CELER = entrées stock. DECELER = sorties. Familles : Informatique, Réseau, Électronique.</p>',
                'figures' => [$fig('10-stock-celer', 'Figure 10 — CELER'), $fig('11-stock-deceler', 'Figure 11 — DECELER')],
            ],
            [
                'num' => 5,
                'title' => 'Parc et export Excel',
                'html' => '<p><strong>/parc</strong> — Affectations. Bouton <strong>Export en masse</strong> : fichier Excel colonnes COFINA (NOM, PRENOM, AGENCE, n° série, marque, modèle, dates, état BON/MOYEN/MAUVAIS).</p>',
                'figures' => [
                    $fig('12-parc-index', 'Figure 12 — Parc'),
                    $fig('13-parc-create', 'Figure 13 — Affectation'),
                    $fig('14-parc-export-masse', 'Figure 14 — Export masse'),
                ],
            ],
            [
                'num' => 6,
                'title' => 'Transitions (scénarios)',
                'html' => '<div class="scenario"><strong>A — Stock → Parc</strong> : affectation utilisateur, approbation.</div>
                <div class="scenario"><strong>B — Parc → Maintenance</strong></div>
                <div class="scenario"><strong>C — Maintenance → Stock</strong></div>
                <div class="scenario"><strong>D — Parc → Hors service</strong></div>
                <div class="scenario"><strong>E — Parc → Perdu</strong></div>
                <p>Validateurs : <code>/admin/approvals</code></p>',
                'figures' => [
                    $fig('15-transitions-menu', 'Figure 15 — Menu transitions'),
                    $fig('16-transition-stock-parc', 'Figure 16 — Stock → Parc'),
                    $fig('21-approbation-detail', 'Figure 21 — Approbation'),
                ],
            ],
            [
                'num' => 7,
                'title' => 'Maintenance, hors service, perdu',
                'html' => '<p>Modules Gestion : maintenance SAV, retrait hors service, déclaration perte.</p>',
                'figures' => [$fig('23-maintenance-index', 'Maintenance'), $fig('24-hors-service-index', 'Hors service')],
            ],
            [
                'num' => 8,
                'title' => 'Rapports',
                'html' => '<p><strong>/reports</strong> — Vue ensemble, équipements, financier, import/export.</p>',
                'figures' => [$fig('26-reports-overview', 'Rapports')],
            ],
            [
                'num' => 9,
                'title' => 'Change Management',
                'html' => '<p>Sélectionner rôle N1/N2/N3 via <code>/change/role</code>. Workflow demande → traitement → validation.</p>',
                'figures' => [$fig('27-change-role-select', 'Rôle Change'), $fig('28-change-n1-create', 'N1')],
            ],
            [
                'num' => 10,
                'title' => 'EOD Suivi',
                'html' => '<p>Fiches fin de journée. Signature N3 et validation Controller. Enregistrer signature au profil.</p>',
                'figures' => [$fig('33-eod-n3-pending', 'EOD N3'), $fig('34-eod-controller', 'Controller')],
            ],
            [
                'num' => 11,
                'title' => 'Incidents',
                'html' => '<p>Création N1, traitement N2, clôture N3.</p>',
                'figures' => [$fig('35-incidents-create', 'Incident'), $fig('36-incidents-list', 'Liste')],
            ],
            [
                'num' => 12,
                'title' => 'Contrôles IT',
                'html' => '<p>Campagnes, tâches assignées, templates (Super Admin).</p>',
                'figures' => [$fig('37-controls-dashboard', 'Contrôles')],
            ],
            [
                'num' => 13,
                'title' => 'Infrastructure IT',
                'html' => '<p>Mots de passe, réseau (VLAN, branchements), licences.</p>',
                'figures' => [$fig('39-passwords-index', 'Mots de passe'), $fig('40-network-index', 'Réseau')],
            ],
            [
                'num' => 14,
                'title' => 'Configuration et FAQ',
                'html' => '<p>Agences, catégories, fournisseurs, utilisateurs, audits.</p>
                <table><tr><th>Problème</th><th>Solution</th></tr>
                <tr><td>Accès refusé profil</td><td>Vérifier rôle Agent IT, Ctrl+F5</td></tr>
                <tr><td>Menu Change vide</td><td>Sélectionner role_change N1/N2/N3</td></tr>
                <tr><td>Signature EOD</td><td>Profil → Ma signature</td></tr></table>',
                'figures' => [$fig('42-admin-users', 'Utilisateurs'), $fig('43-agencies', 'Agences')],
            ],
        ];
    }
}
