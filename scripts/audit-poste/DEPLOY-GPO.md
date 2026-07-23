# Déploiement GPO — Collecte audits postes

## Prérequis

1. Backend Laravel en prod (HTTPS) avec dans `.env` :
   ```env
   AUDIT_API_KEY=...cle_longue...
   AUDIT_API_RATE_LIMIT=60
   ```
2. Partage réseau accessible en **lecture** par les postes du domaine, ex. :
   `\\SERVEUR\IT$\audit-poste\`

## Contenu du partage

| Fichier | Rôle |
|---------|------|
| `Collecte-AuditPoste.ps1` | Script de collecte |
| `config.json` | `ApiUrl` + `ApiKey` (copier depuis `config.prod.example.json`) |

### ACL recommandées sur le dossier

- **Domain Computers** : Lecture & exécution  
- **Admins IT** : Contrôle total  
- Pas d’écriture pour les utilisateurs standards  

## Créer la GPO (tâche planifiée)

1. Ouvrir **Gestion des stratégies de groupe** (`gpmc.msc`)
2. Créer une GPO, ex. `Parc - Collecte Audit Postes`
3. Lier la GPO à l’OU des postes (ou du domaine)
4. Éditer la GPO :

**Configuration ordinateur**  
→ **Préférences**  
→ **Paramètres du Panneau de configuration**  
→ **Tâches planifiées**  
→ clic droit → **Nouveau** → **Tâche planifiée (au moins Windows 7)**

### Onglet Général

| Champ | Valeur |
|-------|--------|
| Action | Mettre à jour |
| Nom | `Parc-CollecteAuditPoste` |
| Exécuter en tant que | `NT AUTHORITY\SYSTEM` |
| Exécuter avec les autorisations maximales | Oui |
| Configurer pour | Windows 7 / Windows Server 2008 R2 |

### Onglet Déclencheurs

- Nouveau → **Quotidien**
- Heure : `08:15`
- Cocher **Délai aléatoire** (ex. 30 min) pour éviter que tout le parc frappe l’API en même temps
- Activé : Oui

### Onglet Actions

| Champ | Valeur |
|-------|--------|
| Action | Démarrer un programme |
| Programme / script | `powershell.exe` |
| Ajouter des arguments | `-NoProfile -NonInteractive -ExecutionPolicy Bypass -File "\\SERVEUR\IT$\audit-poste\Collecte-AuditPoste.ps1"` |

> Remplace `\\SERVEUR\IT$\audit-poste\` par le chemin réel de ton partage.

### Onglet Conditions

- Cocher **Démarrer uniquement si la connexion réseau suivante est disponible** (si proposé)
- Ne pas arrêter sur batterie (postes portables) 

### Onglet Paramètres

- Cocher **Exécuter la tâche dès que possible après un démarrage manqué**
- Si la tâche est déjà en cours : **Ne pas démarrer une nouvelle instance**
- Arrêter la tâche si elle s’exécute plus de : **15 minutes**

## Appliquer / tester

Sur un PC cible :

```cmd
gpupdate /force
schtasks /Query /TN "Parc-CollecteAuditPoste" /V /FO LIST
schtasks /Run /TN "Parc-CollecteAuditPoste"
```

Puis vérifier dans l’app : **Contrôles IT → Audits postes**.

## Test sans GPO (1 machine)

1. Adapter le chemin UNC dans `Collecte-AuditPoste-Task.xml`
2. En admin :

```cmd
schtasks /Create /TN "Parc\CollecteAuditPoste" /XML "C:\chemin\Collecte-AuditPoste-Task.xml" /F
schtasks /Run /TN "Parc\CollecteAuditPoste"
```

## Rotation de clé API (sans coupure)

Commandes Laravel (sur le serveur) :

```bash
# État (clé masquée)
php artisan audit:api-key status

# Première installation — génère une clé
php artisan audit:api-key generate

# Rotation sans coupure — affiche les 2 lignes .env à coller
php artisan audit:api-key rotate

# Quand tout le parc a la nouvelle clé — affiche comment retirer PREVIOUS
php artisan audit:api-key finalize
```

Procédure `rotate` :

1. `php artisan audit:api-key rotate` → coller `AUDIT_API_KEY` + `AUDIT_API_KEY_PREVIOUS` dans `.env` prod  
2. `php artisan config:clear && php artisan config:cache`  
3. Mettre à jour `config.json` sur le partage (`ApiUrl` = `https://gpi.cofinaonline.com`, `ApiKey` = nouvelle)  
4. Attendre 1–2 cycles de tâche planifiée  
5. `php artisan audit:api-key finalize` → retirer `AUDIT_API_KEY_PREVIOUS`

Pendant l’étape 1–4, **les deux clés sont acceptées** (pas de coupure).

## Dépannage

| Symptôme | Piste |
|----------|--------|
| Rien dans `/audits-postes` | Tâche non créée / GPO non appliquée (`gpresult /r`) |
| 401 | `config.json` : clé ≠ `AUDIT_API_KEY` prod |
| Timeout / échec réseau | Pare-feu, DNS, HTTPS, proxy |
| Utilisateur `UNKNOWN\NO_SESSION` | PC sans session interactive au moment de la collecte (normal la nuit) — lancer aussi au logon si besoin |
