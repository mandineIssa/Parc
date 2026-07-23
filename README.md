# Parc informatique COFINA — Application Laravel

Application métier de gestion du parc IT, des workflows de transition (stock / parc / maintenance / hors service / pertes), des tableaux de bord et des modules associés (contrôle, incidents, etc.).

Stack principale : **Laravel 12**, **PHP 8.2+**, **Blade + Tailwind**, **Spatie Laravel Permission**.

---

## Prérequis

- PHP 8.2+ avec extensions habituelles (`pdo_mysql`, `mbstring`, `openssl`, `intl`, `curl`, `zip`)
- Composer 2.x
- Node.js 20+ (pour Vite / assets front)
- Base MySQL (schéma défini par les migrations du projet)

---

## Installation rapide

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build
php artisan serve
```

### Production (queue, planification, sauvegarde)

Les e-mails GPI passent par la **file d'attente** (`GpiNotificationMail` + `Mail::queue`). Configurez :

```bash
# .env
QUEUE_CONNECTION=database   # ou redis en production
php artisan queue:table && php artisan migrate
php artisan queue:work --tries=3
```

Planification (cron `* * * * * php artisan schedule:run`) :

| Commande | Fréquence |
|----------|-----------|
| `gpi:backup-database` | Quotidien 02:00 |
| `gpi:remind-pending-approvals` | Jours ouvrés 08:30 |
| `audits:purge --days=365` | Mensuel |

Sauvegardes SQLite : `storage/app/backups/`. En MySQL, compléter avec `mysqldump`.

### API REST (lecture seule)

Endpoints authentifiés sous `/api/v1` :

- `GET /api/v1/parc` — liste parc
- `GET /api/v1/parc/{numeroSerie}` — détail
- `GET /api/v1/stock` — liste stock

### Notifications in-app & recherche

- Cloche dans la barre de navigation → `/notifications`
- Recherche globale : **Ctrl+K** ou icône loupe (`/search/global`)

Variables utiles :

| Variable | Rôle |
|----------|------|
| `SUPER_ADMIN_EMAILS` | Liste (virgules) des emails ayant un bypass équivalent `super_admin` dans les Gates (`config/cofina.php`). À réduire en prod au profit des rôles/permissions. |
| `AUDIT_API_KEY` | Clé secrète pour `POST /api/audit` (scripts PowerShell). Rotation via `AUDIT_API_KEY_PREVIOUS`. |
| `AUDIT_API_RATE_LIMIT` | Limite de requêtes/minute/IP sur la collecte (défaut 60). |
| `QUEUE_CONNECTION` | `sync` (dev), `database` ou `redis` (prod) pour les e-mails GPI |
| `MAIL_*` | SMTP — ne jamais committer de secrets réels |

Commandes clés API : `php artisan audit:api-key status|generate|rotate|finalize` (voir `scripts/audit-poste/DEPLOY-GPO.md`).

### Collecte audits postes (PowerShell → API)

- **POST** `/api/audit` — ingestion JSON (header `X-API-Key` ou `Authorization: Bearer`), upsert par `hostname` + `numeroSerie`, historique dans `poste_audits`.
- **GET** `/api/audit` — liste paginée + filtres (session auth).
- **GET** `/api/audit/{id}` — détail + historique utilisateurs.
- **GET** `/api/audit/export` — export Excel (mêmes filtres).
- **UI** : menu **Contrôles IT → Audits postes** (`/audits-postes`).
- **Script** : `scripts/audit-poste/Collecte-AuditPoste.ps1` (voir `scripts/audit-poste/README.md`).
- Config : `config/audit_collecte.php` ; tables : `postes`, `poste_audits` (la table `audits` reste réservée au journal applicatif).

---

## Manuel utilisateur

- **Manuel complet (web)** : après connexion → [Documentation](http://127.0.0.1:8000/documentation) → *Manuel d'utilisation complet* (`/documentation/manuel-complet`).
- **Markdown** : `docs/MANUEL_UTILISATION_COMPLET.md`.
- **Captures d'écran** : déposer les PNG dans `public/doc-captures/` (ne pas utiliser `public/documentation/` — conflit avec l’URL `/documentation`).

---

## Architecture des routes

- **`routes/web.php`** : point d’entrée (accueil, profil, `register`, inclusion `auth.php`, admin utilisateurs partiels), puis inclusion **ordonnée** des fichiers métier ci‑dessous.
- **`routes/web/_partials_header.php`** : en‑tête `use …` répliqué dans chaque partiel (les fichiers requis sont des unités de compilation PHP séparées).
- **`routes/web/v01_verified_equipment_parc.php`** — **`v09_incidents.php`** : modules métier (parc / équipement / transitions dans `v01`, approbations en plusieurs fichiers `v02*`, reporting et documentation `v03`, workflows hors service et assimilés `v04`, dashboards `v05`, change & EOD `v06`, mots de passe / réseau / licences `v07`, contrôles IT `v08`, incidents `v09`).
- **`routes/auth.php`** : authentification Breeze.
- **`routes/debug.php`** : **chargé uniquement si `APP_ENV=local`** depuis `bootstrap/app.php**.

Les **noms de routes dupliqués** (ex. plusieurs fois `dashboard`, blocs d’approbation répétés) restent à corriger dans une passe suivante ; la vague 1 fournit la **structure fichiers** sans tout résoudre d’un coup.

---

## Tests automatisés (MySQL)

PHPUnit (`phpunit.xml`) utilise désormais **MySQL** (base `testing` par défaut). Créez la base locale puis :

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
cp .env.testing.example .env.testing
php artisan test
```

Sous Windows PowerShell, remplacez la deuxième ligne par : `Copy-Item .env.testing.example .env.testing`.

Sans serveur MySQL joignable, les migrations peuvent échouer (ex. `ALTER TABLE … MODIFY`).

---

## Sécurité & conformité applicative

- Gates définies dans `app/Providers/AuthServiceProvider.php` ; emails bootstrap centralisés via `config/cofina.php`.
- Journalisation des requêtes : utiliser `App\Support\SecureLog::requestPayload()` plutôt que `$request->all()` dans les logs.
- Les endpoints CSV / tests d’import équipement sont **exclusivement** dans `routes/debug.php` (local).

Recommandations production : HTTPS obligatoire, `APP_DEBUG=false`, révision des migrations qui émettent du bruit console, durcissement RBAC (Spatie), suppression progressive des bypass email.

---

## Qualité du code & CI

- **PHPUnit** : `composer test` ou `php artisan test`.
- **PHPStan + Larastan** : `./vendor/bin/phpstan analyse` (fichier `phpstan.neon`). Le contrôleur `TransitionController` est temporairement exclu tant que la extraction vers des services n’est pas terminée.
- **GitHub Actions** : `.github/workflows/ci.yml` (tests + PHPStan en mode *continue-on-error* le temps de stabiliser la base).

---

## Stratégie de tests (feuille de route)

1. Activer `tests/Feature/RouteNameUniquenessTest.php` une fois `routes/web.php` découpé sans noms dupliqués.
2. Ajouter des tests Feature par workflow critique : transitions, pièces jointes, approbations.
3. Tests d’autorisation : refus explicite pour rôles non autorisés.

---

## Refactoring enterprise (priorités restantes)

Les chantiers suivants dépassent une seule livraison mais sont indispensables pour viser un niveau « production banque » :

| Priorité | Sujet |
|----------|--------|
| Critique | **Éliminer les noms de routes dupliqués** et fusionner les blocs d’approbation redondants (la modularisation `routes/web/v*.php` est en place). |
| Critique | Extraire `TransitionController` (~5000 lignes) en **services / actions** par cas d’usage + FormRequests dédiés. |
| Élevée | Harmoniser les middlewares `auth` / `verified` sur toutes les routes POST sensibles. |
| Élevée | Factoriser le JavaScript inline Blade vers `resources/js` + Vite. |
| Moyenne | Pipeline CI bloquant PHPStan/Pint une fois la base est propre. |

---

## Documentation métier & charte COFINA

Les maquettes et présentations « executive » doivent respecter la charte COFINA (orange institutionnel, noir, blanc, gris corporate — pas de palette hors marque). Ce dépôt documente surtout l’aspect technique ; les livrables PowerPoint / UI sont à versionner séparément si besoin.

---

## Licence

Le squelette Laravel est sous licence MIT ; le code métier COFINA reste soumis à vos politiques internes.
