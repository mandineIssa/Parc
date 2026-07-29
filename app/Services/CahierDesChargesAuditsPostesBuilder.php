<?php

namespace App\Services;

/**
 * Cahier des charges — Application de collecte / gestion audits postes (module Parc).
 */
class CahierDesChargesAuditsPostesBuilder
{
    /**
     * @return list<array{num: int, title: string, html: string}>
     */
    public function chapters(): array
    {
        return [
            [
                'num' => 1,
                'title' => 'Contexte et objectifs',
                'html' => <<<'HTML'
<p><strong>Maître d’ouvrage :</strong> COFINA — Direction des Systèmes d’Information</p>
<p><strong>Projet :</strong> Application de collecte et de gestion des audits des postes de travail
(parc informatique Windows), avec remontée centralisée et export Excel.</p>
<p><strong>Contexte :</strong> Aujourd’hui, le suivi de conformité des postes (antivirus, BitLocker,
firewall, utilisateur de session, inventaire matériel) est partiel ou manuel. L’application
doit automatiser la collecte via un agent PowerShell déployé sur le parc et exposer une
interface de consultation / export pour les équipes IT.</p>
<p><strong>Objectifs :</strong></p>
<ul>
<li>Collecter automatiquement les données techniques de chaque poste Windows 10/11</li>
<li>Centraliser l’historique des audits et des utilisateurs de session</li>
<li>Détecter les non-conformités (Defender off, BitLocker C: inactif, etc.)</li>
<li>Exporter les données en Excel pour reporting / audit</li>
<li>Déployer la collecte sans installer un client lourd sur chaque machine (script + GPO)</li>
</ul>
<p><strong>Hors périmètre (v1) :</strong> MDM complet, inventaire logiciels détaillé, remédiation
automatique, application mobile, synchronisation AD avancée.</p>
HTML
            ],
            [
                'num' => 2,
                'title' => 'Acteurs et cas d’usage',
                'html' => <<<'HTML'
<table>
<tr><th>Acteur</th><th>Besoins</th></tr>
<tr><td>Agent IT</td><td>Consulter les postes, filtrer, exporter Excel, voir l’historique</td></tr>
<tr><td>Responsable / Super Admin</td><td>Configurer la clé API, piloter la conformité, accès complet</td></tr>
<tr><td>Poste Windows (SYSTEM / utilisateur)</td><td>Exécuter le script et envoyer un audit HTTP</td></tr>
<tr><td>Administrateur AD / GPO</td><td>Déployer le script via partage + tâche planifiée</td></tr>
</table>
<p><strong>Cas d’usage principaux :</strong></p>
<ol>
<li>CU1 — Un poste envoie un audit quotidien (tâche planifiée)</li>
<li>CU2 — Un agent consulte la liste des postes audités et filtre (OS, antivirus, BitLocker)</li>
<li>CU3 — Un agent ouvre le détail d’un poste et voit l’historique des utilisateurs</li>
<li>CU4 — Export Excel filtré pour un comité / audit</li>
<li>CU5 — Rotation de la clé API sans interruption de service</li>
<li>CU6 — Test manuel via double-clic sur un lanceur (.bat) depuis le partage</li>
</ol>
HTML
            ],
            [
                'num' => 3,
                'title' => 'Exigences fonctionnelles',
                'html' => <<<'HTML'
<table>
<tr><th>ID</th><th>Exigence</th><th>Priorité</th></tr>
<tr><td>EF-01</td><td>Réception POST JSON d’un audit poste</td><td>Must</td></tr>
<tr><td>EF-02</td><td>Validation stricte du payload (champs requis + types)</td><td>Must</td></tr>
<tr><td>EF-03</td><td>Upsert poste (hostname + numéro de série)</td><td>Must</td></tr>
<tr><td>EF-04</td><td>Historique des audits dans le temps</td><td>Must</td></tr>
<tr><td>EF-05</td><td>Conservation de l’historique des utilisateurs de session</td><td>Must</td></tr>
<tr><td>EF-06</td><td>Liste paginée + filtres (fabricant, OS, utilisateur, antivirus, BitLocker, recherche)</td><td>Must</td></tr>
<tr><td>EF-07</td><td>Fiche détail poste + historique complet</td><td>Must</td></tr>
<tr><td>EF-08</td><td>Export Excel (.xlsx) avec en-têtes, auto-size, mise en évidence des alertes</td><td>Must</td></tr>
<tr><td>EF-09</td><td>Script PowerShell compatible session utilisateur et compte SYSTEM</td><td>Must</td></tr>
<tr><td>EF-10</td><td>Lanceur .bat double-clic (partage réseau)</td><td>Should</td></tr>
<tr><td>EF-11</td><td>Documentation déploiement GPO / tâche planifiée</td><td>Should</td></tr>
<tr><td>EF-12</td><td>Tableau de bord : compteurs antivirus off / BitLocker off / audités 24h</td><td>Should</td></tr>
<tr><td>EF-13</td><td>Lien optionnel avec inventaire équipements (n° série)</td><td>Could</td></tr>
</table>
<p><strong>Données collectées (payload JSON) :</strong>
hostname, utilisateurSession (DOMAINE\user), fabricant, modele, numeroSerie, os, versionOS,
antivirusDefender (bool), firewall, bitlocker, usbStockageBloque (bool), adresseMAC,
adresseIP, dateAudit (ISO 8601).</p>
HTML
            ],
            [
                'num' => 4,
                'title' => 'Exigences non fonctionnelles',
                'html' => <<<'HTML'
<table>
<tr><th>ID</th><th>Exigence</th></tr>
<tr><td>ENF-01</td><td>Disponibilité API collecte : heures ouvrées + batch nocturne/matinal</td></tr>
<tr><td>ENF-02</td><td>HTTPS obligatoire en production</td></tr>
<tr><td>ENF-03</td><td>Authentification API par clé (header X-API-Key ou Bearer)</td></tr>
<tr><td>ENF-04</td><td>Rate limiting sur POST (ex. 60 req/min/IP, configurable)</td></tr>
<tr><td>ENF-05</td><td>Rotation de clé sans coupure (clé courante + clé précédente)</td></tr>
<tr><td>ENF-06</td><td>Données nominatives (utilisateurSession) : logs sans clair, hash traçable</td></tr>
<tr><td>ENF-07</td><td>Compatibilité Windows 10/11, PowerShell 5.1+</td></tr>
<tr><td>ENF-08</td><td>Temps de réponse UI liste &lt; 3 s pour 10 000 postes (pagination)</td></tr>
<tr><td>ENF-09</td><td>Journalisation des ingestions (succès / échec) côté serveur</td></tr>
<tr><td>ENF-10</td><td>Aucune copie obligatoire du script sur chaque PC (partage + GPO)</td></tr>
</table>
HTML
            ],
            [
                'num' => 5,
                'title' => 'Architecture cible',
                'html' => <<<'HTML'
<pre>
[Poste Windows]
   Script PS1 / BAT  ──HTTP POST JSON──►  [API Backend]
                                              │
                                         upsert + historique
                                              │
                                         [Base MySQL]
                                         postes / poste_audits
                                              │
                              ┌───────────────┴───────────────┐
                              ▼                               ▼
                     [UI Web Agents IT]              [Export Excel]
                     /audits-postes
</pre>
<p><strong>Choix techniques recommandés (alignés stack COFINA) :</strong></p>
<ul>
<li>Backend : PHP Laravel 12</li>
<li>SGBD : MySQL</li>
<li>UI : Blade + Tailwind (intégrable au GPI existant ou app dédiée)</li>
<li>Export : PhpSpreadsheet / Maatwebsite Excel</li>
<li>Agent : PowerShell + config.json sur partage</li>
</ul>
<p><strong>Deux modes de livraison :</strong></p>
<ol>
<li><strong>Module intégré</strong> dans GPI (déjà prototypé) — réutilise auth, menu, déploiement serveur existant</li>
<li><strong>Application autonome</strong> — même cahier des charges, dépôt et URL dédiés, SSO/LDAP optionnel en v2</li>
</ol>
HTML
            ],
            [
                'num' => 6,
                'title' => 'Modèle de données',
                'html' => <<<'HTML'
<p><strong>Table postes</strong> (état courant) :</p>
<ul>
<li>id, hostname, numero_serie (unique composé), utilisateur_session</li>
<li>fabricant, modele, os, version_os</li>
<li>antivirus_defender, firewall, bitlocker, usb_stockage_bloque</li>
<li>adresse_mac, adresse_ip, date_audit, timestamps</li>
</ul>
<p><strong>Table poste_audits</strong> (historique) :</p>
<ul>
<li>id, poste_id (FK cascade), mêmes champs métier + date_audit, timestamps</li>
</ul>
<p>Règles :</p>
<ul>
<li>1 poste = 1 couple (hostname, numero_serie)</li>
<li>Chaque réception crée une ligne d’historique</li>
<li>L’utilisateur courant est mis à jour sur le poste ; l’historique conserve les valeurs passées</li>
</ul>
HTML
            ],
            [
                'num' => 7,
                'title' => 'Spécification API',
                'html' => <<<'HTML'
<table>
<tr><th>Méthode</th><th>URI</th><th>Auth</th><th>Description</th></tr>
<tr><td>POST</td><td>/api/audit</td><td>API Key</td><td>Ingestion audit</td></tr>
<tr><td>GET</td><td>/api/audit</td><td>Session / token agent</td><td>Liste + filtres + pagination</td></tr>
<tr><td>GET</td><td>/api/audit/{id}</td><td>Session / token agent</td><td>Détail + historique</td></tr>
<tr><td>GET</td><td>/api/audit/export</td><td>Session / token agent</td><td>Fichier .xlsx</td></tr>
</table>
<p><strong>Réponses POST :</strong> 201 (création), 200 (mise à jour), 400 (payload), 401 (clé), 429 (throttle), 500 (serveur).</p>
<p><strong>Corps POST (exemple) :</strong></p>
<pre>{
  "hostname": "PC-COMPTA-01",
  "utilisateurSession": "COFINA\\jdupont",
  "fabricant": "Dell Inc.",
  "modele": "Latitude 5540",
  "numeroSerie": "SN-ABC-123",
  "os": "Microsoft Windows 11 Pro",
  "versionOS": "10.0.22631",
  "antivirusDefender": true,
  "firewall": "Domain:True;Private:True;Public:False",
  "bitlocker": "C::On;D::Off",
  "usbStockageBloque": true,
  "adresseMAC": "AA:BB:CC:DD:EE:FF",
  "adresseIP": "10.10.1.42",
  "dateAudit": "2026-07-23T10:00:00+00:00"
}</pre>
HTML
            ],
            [
                'num' => 8,
                'title' => 'Interface utilisateur',
                'html' => <<<'HTML'
<p><strong>Écrans v1 :</strong></p>
<ol>
<li><strong>Liste des postes</strong> — cartes stats, filtres, tableau, pagination, bouton Export Excel, lien Détail.
Lignes en alerte si antivirus off ou BitLocker C: inactif.</li>
<li><strong>Détail poste</strong> — état courant, historique utilisateurs, historique des audits.</li>
<li><strong>(Option) Paramètres clé API</strong> — réservé Super Admin (ou CLI artisan en v1 intégrée).</li>
</ol>
<p>Charte : identité COFINA (rouge institutionnel, contraste lisible). Pas de surcharge visuelle type dashboard marketing.</p>
HTML
            ],
            [
                'num' => 9,
                'title' => 'Agent de collecte et déploiement',
                'html' => <<<'HTML'
<p><strong>Composants agent :</strong></p>
<ul>
<li>Collecte-AuditPoste.ps1 — logique de collecte + envoi</li>
<li>Collecte-AuditPoste.bat — lanceur double-clic</li>
<li>config.json — ApiUrl + ApiKey (ACL restreintes)</li>
</ul>
<p><strong>Déploiement :</strong></p>
<ol>
<li>Créer un partage réseau unique (ex. \\Fichiers\IT$\audit-poste\)</li>
<li>Y déposer script + bat + config</li>
<li>GPO : tâche planifiée quotidienne (SYSTEM), délai aléatoire, exécution si réseau disponible</li>
<li>Test manuel : double-clic .bat depuis un poste du domaine</li>
</ol>
<p><strong>Contraintes techniques agent :</strong></p>
<ul>
<li>Utilisateur session via Win32_ComputerSystem si exécution SYSTEM</li>
<li>Tolérance si BitLocker / Defender non lisibles (valeurs Unknown / false)</li>
<li>Compatible PowerShell 5.1 (Windows 10/11)</li>
</ul>
HTML
            ],
            [
                'num' => 10,
                'title' => 'Sécurité et conformité',
                'html' => <<<'HTML'
<ul>
<li>Clé API stockée uniquement dans .env serveur et config.json du partage (pas dans Git)</li>
<li>Rotation : AUDIT_API_KEY + AUDIT_API_KEY_PREVIOUS</li>
<li>Rate limiting POST</li>
<li>Logs d’accès sans login en clair (hash SHA-256)</li>
<li>HTTPS / certificat valide en production</li>
<li>Droits partage : Domain Computers = Lecture ; Admins IT = Contrôle total</li>
<li>Accès UI réservé aux comptes authentifiés (rôles Agent IT / Admin)</li>
</ul>
HTML
            ],
            [
                'num' => 11,
                'title' => 'Livrables et critères d’acceptation',
                'html' => <<<'HTML'
<p><strong>Livrables :</strong></p>
<ol>
<li>Code source backend + UI + migrations + tests</li>
<li>Script PowerShell + BAT + exemples de config + guide GPO</li>
<li>Documentation technique + ce cahier des charges (PDF)</li>
<li>Procédure de déploiement serveur (migrate, config:cache, clé API)</li>
</ol>
<p><strong>Critères d’acceptation (v1) :</strong></p>
<table>
<tr><th>ID</th><th>Critère</th></tr>
<tr><td>CA-01</td><td>POST /api/audit avec clé valide crée ou met à jour un poste + historique</td></tr>
<tr><td>CA-02</td><td>POST sans clé → 401 ; payload invalide → 400</td></tr>
<tr><td>CA-03</td><td>Changement d’utilisateur session conservé dans l’historique</td></tr>
<tr><td>CA-04</td><td>UI liste + détail accessibles après authentification</td></tr>
<tr><td>CA-05</td><td>Export Excel téléchargeable avec colonne Utilisateur</td></tr>
<tr><td>CA-06</td><td>Script SYSTEM remonte un utilisateur de session réel (si session ouverte)</td></tr>
<tr><td>CA-07</td><td>Rotation de clé : ancienne clé acceptée pendant la fenêtre PREVIOUS</td></tr>
<tr><td>CA-08</td><td>Tests automatisés POST (nominal, erreur, upsert, changement user) verts</td></tr>
</table>
HTML
            ],
            [
                'num' => 12,
                'title' => 'Planning indicatif et risques',
                'html' => <<<'HTML'
<table>
<tr><th>Phase</th><th>Contenu</th><th>Durée indicative</th></tr>
<tr><td>P1</td><td>Modèle données + API POST + tests</td><td>3–5 j</td></tr>
<tr><td>P2</td><td>UI liste/détail + export Excel</td><td>3–4 j</td></tr>
<tr><td>P3</td><td>Script PS + BAT + doc GPO</td><td>2–3 j</td></tr>
<tr><td>P4</td><td>Sécurité (clé, throttle, logs) + recette</td><td>2 j</td></tr>
<tr><td>P5</td><td>Déploiement prod + pilote 10 postes + GPO</td><td>2–4 j</td></tr>
</table>
<p><strong>Risques :</strong></p>
<ul>
<li>DNS / réseau : postes hors VPN ne joignent pas l’API</li>
<li>Droits insuffisants pour lire BitLocker sans admin</li>
<li>Partage UNC inaccessible → échec GPO</li>
<li>Clé API exposée si ACL partage trop permissives</li>
</ul>
HTML
            ],
            [
                'num' => 13,
                'title' => 'Décision d’architecture (intégrée vs autonome)',
                'html' => <<<'HTML'
<table>
<tr><th>Option</th><th>Avantages</th><th>Inconvénients</th></tr>
<tr><td>A — Module dans GPI existant</td><td>Auth, hébergement, menu déjà là ; délai court</td><td>Couplage au monolithe</td></tr>
<tr><td>B — Application dédiée</td><td>Indépendance, cycle de release propre</td><td>Auth/SSO, hébergement, ops à refaire</td></tr>
</table>
<p><strong>Recommandation v1 :</strong> Option A (module intégré à
https://gpi.cofinaonline.com) pour industrialiser rapidement, avec interfaces
(API + script) déjà conçues pour une éventuelle extraction Option B en v2.</p>
<p>Le présent cahier des charges reste valable pour A et B.</p>
HTML
            ],
        ];
    }
}
