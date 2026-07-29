<?php

namespace App\Services;

/**
 * Contenu du PDF « Documentation technique — Module Parc ».
 */
class DocumentationParcTechniquePdfBuilder
{
    /**
     * @return list<array{num: int, title: string, html: string, figures?: list<array{path: string, caption: string}>}>
     */
    public function chapters(): array
    {
        $fig = function (string $id, string $caption): array {
            return [
                'path' => public_path('doc-captures/'.$id.'.png'),
                'caption' => $caption,
            ];
        };

        return [
            [
                'num' => 1,
                'title' => 'Objet et périmètre',
                'html' => <<<'HTML'
<p>Ce document décrit l’architecture technique du <strong>module Parc</strong> de l’application
<strong>Gestion Parc Informatique COFINA</strong> (Laravel 12 / PHP 8.2+ / MySQL).</p>
<p><strong>Périmètre :</strong></p>
<ul>
<li>Inventaire équipements et cycle de vie (<code>stock</code> → <code>parc</code> → maintenance / hors service / perdu)</li>
<li>Affectations parc, réaffectations, import/export Excel</li>
<li>Transitions et approbations</li>
<li>Collecte automatique des audits postes (PowerShell → API → UI)</li>
</ul>
<p><strong>Hors périmètre :</strong> Change Management, EOD, Incidents, Contrôles IT, Infrastructure (mots de passe, réseau, licences) — modules connexes documentés séparément.</p>
HTML
                ,
                'figures' => [
                    $fig('12-parc-index', 'Figure 1 — Liste du parc (/parc)'),
                ],
            ],
            [
                'num' => 2,
                'title' => 'Stack et arborescence',
                'html' => <<<'HTML'
<table>
<tr><th>Composant</th><th>Technologie</th></tr>
<tr><td>Backend</td><td>Laravel 12, PHP 8.2+</td></tr>
<tr><td>Base de données</td><td>MySQL</td></tr>
<tr><td>UI</td><td>Blade, Tailwind CSS, Vite</td></tr>
<tr><td>Export Excel</td><td>Maatwebsite Excel / PhpSpreadsheet</td></tr>
<tr><td>PDF</td><td>barryvdh/laravel-dompdf</td></tr>
<tr><td>RBAC</td><td>Spatie Permission + Gates COFINA</td></tr>
<tr><td>Collecte postes</td><td>PowerShell + POST JSON /api/audit</td></tr>
</table>
<p><strong>Fichiers clés :</strong></p>
<pre>app/Models/Equipment.php, Parc.php, Stock.php, …
app/Http/Controllers/ParcController.php
app/Http/Controllers/TransitionController.php
app/Http/Controllers/PosteAuditController.php
app/Http/Controllers/Api/AuditCollecteController.php
routes/web/v01_verified_equipment_parc.php
routes/api.php
config/audit_collecte.php
scripts/audit-poste/
resources/views/equipment/parc/
resources/views/audits-postes/</pre>
HTML
            ],
            [
                'num' => 3,
                'title' => 'Modèle de données',
                'html' => <<<'HTML'
<p>Le hub métier est <code>equipment</code> (clé métier <code>numero_serie</code>). Le champ
<code>statut</code> pilote le cycle de vie : <code>stock</code> | <code>parc</code> |
<code>maintenance</code> | <code>hors_service</code> | <code>perdu</code>.</p>
<table>
<tr><th>Table</th><th>Modèle</th><th>Rôle</th></tr>
<tr><td>equipment</td><td>Equipment</td><td>Fiche inventaire (marque, modèle, IP, MAC…)</td></tr>
<tr><td>parc</td><td>Parc</td><td>Affectation utilisateur / département / poste</td></tr>
<tr><td>stock</td><td>Stock</td><td>État stock, CELER / DECELER</td></tr>
<tr><td>maintenance</td><td>Maintenance</td><td>SAV / panne</td></tr>
<tr><td>hors_service</td><td>HorsService</td><td>Retrait</td></tr>
<tr><td>perdu</td><td>Perdu</td><td>Perte / vol</td></tr>
<tr><td>reaffectations</td><td>Reaffectation</td><td>Historique changement d’utilisateur</td></tr>
<tr><td>transition_approvals</td><td>TransitionApproval</td><td>Demandes from→to + statut</td></tr>
<tr><td>postes</td><td>Poste</td><td>État courant audit (hostname + n° série)</td></tr>
<tr><td>poste_audits</td><td>PosteAudit</td><td>Historique des audits (dont utilisateur session)</td></tr>
</table>
<p><em>Note :</em> la table <code>audits</code> (modèle <code>Audit</code>) est le <strong>journal applicatif</strong>
(trait Auditable). Elle est distincte de <code>poste_audits</code>.</p>
<p><strong>Clé unique postes :</strong> <code>(hostname, numero_serie)</code>.</p>
HTML
            ],
            [
                'num' => 4,
                'title' => 'Cycle de vie et transitions',
                'html' => <<<'HTML'
<pre>
stock ──► parc ──► maintenance ──► stock
  │         │            └──► hors_service
  │         ├──► hors_service
  │         └──► perdu
  └──► hors_service
</pre>
<p>Flux typique avec approbation :</p>
<ol>
<li>Agent IT ouvre le formulaire de transition sur l’équipement</li>
<li>Création d’une ligne <code>transition_approvals</code> (pending)</li>
<li>Validateur traite dans <code>/admin/approvals</code> (ou listes spécialisées)</li>
<li>Sur approve : mise à jour <code>equipment.statut</code> + enregistrement dans la table cible</li>
</ol>
<table>
<tr><th>Transition</th><th>Route / méthode indicative</th></tr>
<tr><td>Stock → Parc</td><td>equipment.transitions / stockToParc, simpleAffectation</td></tr>
<tr><td>Parc → Maintenance</td><td>parcToMaintenance + submit/approve Maintenance</td></tr>
<tr><td>Maintenance → Stock</td><td>maintenanceToStock</td></tr>
<tr><td>Parc → Hors service</td><td>parcToHorsService</td></tr>
<tr><td>Parc → Perdu</td><td>parcToPerdu</td></tr>
<tr><td>Réaffectation</td><td>parc.reaffecter (reste en parc)</td></tr>
</table>
<p>Contrôleur principal : <code>TransitionController</code> (volumineux — extraction progressive en services recommandée).</p>
HTML
                ,
                'figures' => [
                    $fig('15-transitions-menu', 'Figure 2 — Menu transitions'),
                    $fig('16-transition-stock-parc', 'Figure 3 — Stock → Parc'),
                    $fig('21-approbation-detail', 'Figure 4 — Détail approbation'),
                ],
            ],
            [
                'num' => 5,
                'title' => 'Module affectation Parc (UI)',
                'html' => <<<'HTML'
<p><strong>Contrôleur :</strong> <code>ParcController</code></p>
<table>
<tr><th>URI</th><th>Nom de route</th><th>Action</th></tr>
<tr><td>GET /parc</td><td>parc.index</td><td>Liste équipements en parc</td></tr>
<tr><td>GET /parc/create</td><td>parc.create</td><td>Nouvelle affectation</td></tr>
<tr><td>POST /parc</td><td>parc.store</td><td>Enregistrement</td></tr>
<tr><td>GET /parc/{id}</td><td>parc.show</td><td>Fiche</td></tr>
<tr><td>GET|PUT /parc/{id}/edit</td><td>parc.edit / update</td><td>Modification</td></tr>
<tr><td>GET /equipment/parc/import</td><td>parc.import.form</td><td>Import CSV</td></tr>
<tr><td>POST /equipment/parc/import</td><td>parc.import</td><td>Traitement import</td></tr>
<tr><td>GET /equipment/parc/export</td><td>parc.export</td><td>Export Excel masse</td></tr>
<tr><td>GET /parc/reaffectations</td><td>parc.reaffectations.index</td><td>Historique</td></tr>
</table>
<p>Vues : <code>resources/views/equipment/parc/*.blade.php</code></p>
<p>Export masse : service <code>ParcMassExcelExport</code> (colonnes type COFINA : NOM, PRENOM, AGENCE, n° série, marque, modèle, état…).</p>
HTML
                ,
                'figures' => [
                    $fig('13-parc-create', 'Figure 5 — Nouvelle affectation'),
                    $fig('14-parc-export-masse', 'Figure 6 — Export en masse'),
                ],
            ],
            [
                'num' => 6,
                'title' => 'Collecte audits postes',
                'html' => <<<'HTML'
<p>Objectif : remonter automatiquement depuis les postes Windows (hostname, utilisateur session,
fabricant, OS, Defender, firewall, BitLocker, USB, MAC, IP) vers le backend, avec historique
et export Excel.</p>
<h3>6.1 API</h3>
<table>
<tr><th>Méthode</th><th>URI</th><th>Auth</th><th>Rôle</th></tr>
<tr><td>POST</td><td>/api/audit</td><td>X-API-Key ou Bearer</td><td>Ingestion script</td></tr>
<tr><td>GET</td><td>/api/audit</td><td>session auth</td><td>Liste filtrée</td></tr>
<tr><td>GET</td><td>/api/audit/{id}</td><td>session auth</td><td>Détail + historique</td></tr>
<tr><td>GET</td><td>/api/audit/export</td><td>session auth</td><td>Excel</td></tr>
</table>
<p>Middleware : <code>audit.api_key</code> (<code>VerifyAuditApiKey</code>) + throttle <code>audit-collecte</code>.</p>
<p>Upsert : clé <code>hostname</code> + <code>numeroSerie</code> → mise à jour <code>postes</code> + insertion <code>poste_audits</code>.</p>
<p>Validation : <code>StorePosteAuditRequest</code> (réponses JSON 400 / 401 / 500).</p>
<h3>6.2 UI web</h3>
<ul>
<li><code>GET /audits-postes</code> — liste, filtres, stats, export (<code>audits-postes.index</code>)</li>
<li><code>GET /audits-postes/{poste}</code> — détail + historique utilisateurs</li>
<li>Menu : <strong>Configuration → Audits postes</strong></li>
</ul>
<h3>6.3 Configuration</h3>
<pre>AUDIT_API_KEY=
AUDIT_API_KEY_PREVIOUS=   # rotation sans coupure
AUDIT_API_HEADER=X-API-Key
AUDIT_API_RATE_LIMIT=60</pre>
<p>Fichier : <code>config/audit_collecte.php</code><br>
CLI : <code>php artisan audit:api-key status|generate|rotate|finalize</code></p>
<p>RGPD : <code>utilisateurSession</code> masqué dans <code>SecureLog</code> ; hash SHA-256 dans les logs d’ingest.</p>
HTML
            ],
            [
                'num' => 7,
                'title' => 'Script PowerShell et déploiement',
                'html' => <<<'HTML'
<p>Dossier : <code>scripts/audit-poste/</code></p>
<table>
<tr><th>Fichier</th><th>Rôle</th></tr>
<tr><td>Collecte-AuditPoste.ps1</td><td>Collecte WMI/sécurité + POST JSON</td></tr>
<tr><td>Collecte-AuditPoste.bat</td><td>Double-clic (lanceur)</td></tr>
<tr><td>config.json</td><td>ApiUrl + ApiKey (non versionné)</td></tr>
<tr><td>config.prod.example.json</td><td>Modèle prod (https://gpi.cofinaonline.com)</td></tr>
<tr><td>Collecte-AuditPoste-Task.xml</td><td>Modèle tâche planifiée</td></tr>
<tr><td>DEPLOY-GPO.md</td><td>Procédure GPO / partage</td></tr>
</table>
<p><strong>Déploiement entreprise :</strong></p>
<ol>
<li>Partage UNC (ex. <code>\\SERVEUR\IT$\audit-poste\</code>) avec .ps1, .bat, config.json</li>
<li>Test : double-clic sur le .bat depuis un poste du domaine</li>
<li>Prod : GPO → tâche planifiée SYSTEM quotidienne →
<code>powershell.exe -NoProfile -NonInteractive -ExecutionPolicy Bypass -File "\\…\Collecte-AuditPoste.ps1"</code></li>
</ol>
<p>Sous compte SYSTEM, l’utilisateur de session est lu via <code>Win32_ComputerSystem.UserName</code>
(pas <code>$env:USERNAME</code>).</p>
<p>Prérequis réseau : résolution DNS de l’URL API (VPN / réseau entreprise).</p>
HTML
            ],
            [
                'num' => 8,
                'title' => 'API Parc / Stock (v1)',
                'html' => <<<'HTML'
<table>
<tr><th>Méthode</th><th>URI</th><th>Nom</th></tr>
<tr><td>GET</td><td>/api/v1/parc</td><td>api.v1.parc.index</td></tr>
<tr><td>GET</td><td>/api/v1/parc/{numeroSerie}</td><td>api.v1.parc.show</td></tr>
<tr><td>GET</td><td>/api/v1/stock</td><td>api.v1.stock.index</td></tr>
</table>
<p>Middleware : <code>auth</code> (session). Contrôleurs :
<code>Api\V1\ParcApiController</code>, <code>Api\V1\StockApiController</code>.</p>
HTML
            ],
            [
                'num' => 9,
                'title' => 'Sécurité',
                'html' => <<<'HTML'
<ul>
<li>Routes web : <code>auth</code> + <code>verified</code> (sauf documentation partielle)</li>
<li>Collecte : clé API rotative, rate limiting IP, HTTPS en prod</li>
<li>Logs : <code>SecureLog::requestPayload()</code> — pas de secrets ni login en clair</li>
<li>Gates : <code>AuthServiceProvider</code>, bootstrap emails <code>SUPER_ADMIN_EMAILS</code> / <code>config/cofina.php</code></li>
<li><code>route:cache</code> exige des noms de routes uniques (corrections v01/v02/v04)</li>
</ul>
HTML
            ],
            [
                'num' => 10,
                'title' => 'Déploiement serveur et checklist',
                'html' => <<<'HTML'
<pre>cd /var/www/html/ParcInformatique/Parc
git fetch origin && git reset --hard origin/main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
# .env : APP_URL, AUDIT_API_KEY, …
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache</pre>
<p><code>migrate --force</code> n’écrase pas les données : il applique uniquement les migrations
en attente (ex. tables <code>postes</code> / <code>poste_audits</code>).</p>
<p><strong>Ne jamais utiliser en prod :</strong>
<code>migrate:fresh</code>, <code>migrate:refresh</code>, <code>db:wipe</code>.</p>
<table>
<tr><th>Contrôle</th><th>Attendu</th></tr>
<tr><td>GET /parc</td><td>200, liste</td></tr>
<tr><td>GET /audits-postes</td><td>200</td></tr>
<tr><td>POST /api/audit + X-API-Key</td><td>201/200 JSON</td></tr>
<tr><td>Double-clic .bat sur partage</td><td>Message OK</td></tr>
</table>
HTML
            ],
            [
                'num' => 11,
                'title' => 'Tests automatisés',
                'html' => <<<'HTML'
<table>
<tr><th>Fichier</th><th>Couverture</th></tr>
<tr><td>tests/Feature/PosteAuditApiTest.php</td><td>POST /api/audit (401, 400, create, upsert, changement utilisateur)</td></tr>
<tr><td>tests/Feature/RouteNameUniquenessTest.php</td><td>Unicité des noms (à activer pleinement)</td></tr>
</table>
<pre>php artisan test --filter=PosteAuditApiTest</pre>
HTML
            ],
            [
                'num' => 12,
                'title' => 'Glossaire et références',
                'html' => <<<'HTML'
<table>
<tr><th>Terme</th><th>Définition</th></tr>
<tr><td>Parc</td><td>Équipement affecté à un utilisateur / site</td></tr>
<tr><td>CELER / DECELER</td><td>Entrées / sorties de stock</td></tr>
<tr><td>Transition</td><td>Changement de statut avec éventuelle approbation</td></tr>
<tr><td>Audit poste</td><td>Snapshot technique remonté par script Windows</td></tr>
<tr><td>Journal d’activité</td><td>Table audits (Auditable) — distincte des audits postes</td></tr>
</table>
<p><strong>Références code :</strong> README.md, scripts/audit-poste/DEPLOY-GPO.md,
resources/views/documentation/sections/api.blade.php</p>
<p><strong>URL prod indicative :</strong> https://gpi.cofinaonline.com</p>
HTML
            ],
        ];
    }
}
