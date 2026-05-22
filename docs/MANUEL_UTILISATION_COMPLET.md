# Manuel d'utilisation complet — Gestion Parc Informatique COFINA

**Version** : 1.0 — Mai 2026  
**Application** : Laravel 12 — Parc IT COFINA  
**Manuel interactif (avec captures)** : `/documentation/manuel-complet`

---

## Comment utiliser ce manuel

1. **En ligne** : menu *Documentation* → *Manuel d'utilisation complet*.
2. **Captures** : déposez les PNG dans `public/documentation/captures/` (voir script `scripts/liste-captures-manuel.ps1`).
3. **PDF** : exportez la page web (Imprimer → PDF) une fois les captures ajoutées.

---

## 1. Présentation

Application de gestion du parc IT : inventaire, stocks CELER/DECELER, affectations, transitions, approbations, maintenance, Change, EOD, incidents, contrôles, infrastructure (mots de passe, réseau, licences).

### Rôles principaux

| Rôle | Capacités |
|------|-----------|
| Super Admin | Tout, utilisateurs, approbations, templates |
| Agent IT | Équipements, parc, stocks, transitions, exports |
| Utilisateur | Lecture, profil ; workflows si `role_change` défini |
| EOD N3 / Controller | Signatures et validation EOD |

### Rôle workflow (`role_change`)

| Valeur | Modules |
|--------|---------|
| N1 | Création Change, EOD, incidents |
| N2 | Traitement Change / EOD |
| N3 | Validation Change ; signature EOD |
| CONTROLLER | Validation batch EOD |

---

## 2. Connexion et navigation

1. URL : `/login` — email + mot de passe.
2. **Profil** : `/profile` — informations, signature, mot de passe.
3. **Menu latéral** : PARC, Infrastructure IT, Change, Contrôles, Incidents, EOD, Configuration.
4. **Tableau de bord** : `/dashboard`.

*Captures : `01-accueil-dashboard.png` … `05-dashboard-agent.png`*

---

## 3. Équipements

| Action | Chemin |
|--------|--------|
| Liste | `/equipment` |
| Créer | `/equipment/create` |
| Import | `/equipment/import` + template CSV |
| Export | `/equipment/export` |
| Renouvellement | `/equipment/renewal` |

**Règle** : le numéro de série est unique.

*Captures : `06` à `09`*

---

## 4. Stocks CELER / DECELER

- **CELER** (entrée) : `/dashboard/celer-informatique`, `celer-reseau`, `celer-electronique`
- **DECELER** (sortie) : `/dashboard/deceler-informatique`, etc.

*Captures : `10`, `11`*

---

## 5. Parc informatique

| Action | Chemin |
|--------|--------|
| Liste parc | `/parc` |
| Nouvelle affectation | `/parc/create` |
| Export en masse Excel COFINA | `/equipment/parc/export` |
| Import parc | `/equipment/parc/import` |
| Réaffectations | `/parc/reaffectations` |

L'export génère les colonnes : NOM, PRENOM, AGENCE, Département, POSTE, Dotation, **serial number**, Marque, Modèle, dates, Fournisseur, État (BON/MOYEN/MAUVAIS).

*Captures : `12` à `14`*

---

## 6. Transitions — scénarios

Accès : fiche équipement → **Changer statut** → `/equipment/{id}/transitions`

| Scénario | Description |
|----------|-------------|
| **A — Stock → Parc** | Affectation utilisateur, agence, signatures |
| **B — Parc → Maintenance** | Envoi SAV |
| **C — Maintenance → Stock** | Retour après réparation |
| **D — Parc → Hors service** | Retrait |
| **E — Parc → Perdu** | Perte / vol |
| **F — Stock → Hors service** | Retrait sans mise en service |
| **G — Affectation simple** | Formulaire direct `/parc/create` |

### Approbations

Validateurs : Super Admin, responsable approbation.  
Liste : `/admin/approvals` — Approuver / Rejeter + pièces jointes.

*Captures : `15` à `22`*

---

## 7. Maintenance, hors service, perdu

| Module | URL |
|--------|-----|
| Maintenance | `/maintenance` |
| Hors service | `/hors-service` |
| Perdu | `/perdu` |

*Captures : `23` à `25`*

---

## 8. Rapports

`/reports` — Vue d'ensemble, équipements, financier, import/export.

*Capture : `26`*

---

## 9. Change Management

1. Choisir rôle : `/change/role` (N1, N2, N3).
2. **N1** : `/change/n1/create` — créer demande.
3. **N2** : `/change/n2` — traiter.
4. **N3** : `/change/n3` — valider.

*Captures : `27` à `30`*

---

## 10. EOD Suivi

| Rôle | Pages |
|------|-------|
| N1 | `/eod/n1`, création fiche |
| N2 | `/eod/n2` |
| N3 | `/eod/n3/pending` (signatures) |
| Controller | `/eod/controller` |

Signature : enregistrer au préalable dans **Profil → Ma signature**.

*Captures : `31` à `34`*

---

## 11. Incidents

- Création : N1 ou Super Admin — `/incidents/create`
- Traitement N2 / N3 — `/incidents`

*Captures : `35`, `36`*

---

## 12. Contrôles IT

- `/controls/dashboard`, `/controls`, `/controls/tasks`
- Templates : Super Admin — `/controls/templates`

*Captures : `37`, `38`*

---

## 13. Infrastructure IT

| Module | URL |
|--------|-----|
| Mots de passe | `/passwords` |
| Réseau | `/network` |
| Licences | `/licences` |

*Captures : `39` à `41`*

---

## 14. Configuration

| Référentiel | URL |
|-------------|-----|
| Agences | `/agencies` |
| Catégories | `/categories` |
| Fournisseurs | `/suppliers` |
| Utilisateurs | `/admin/users` |
| Audits | `/audits` |

*Captures : `42`, `43`*

---

## 15. FAQ

| Problème | Solution |
|----------|----------|
| Accès refusé (profil) | Rôle Agent IT : recharger Ctrl+F5 |
| Transition bloquée | Vérifier approbations en attente |
| Menu Change vide | Sélectionner `role_change` |
| Signature EOD | Profil → Ma signature |
| N° série déjà affecté | Vérifier entrée parc existante |

---

## Annexe — Index des 43 captures

| Fichier | Sujet |
|---------|--------|
| 01-accueil-dashboard | Dashboard |
| 02-login | Connexion |
| 03-profil | Profil |
| 04-sidebar-complete | Menu |
| 05-dashboard-agent | Dashboard agent |
| 06-equipment-list | Liste équipements |
| 07-equipment-create | Création |
| 08-equipment-import | Import |
| 09-equipment-renewal | Renouvellement |
| 10-stock-celer | CELER |
| 11-stock-deceler | DECELER |
| 12-parc-index | Parc |
| 13-parc-create | Affectation |
| 14-parc-export-masse | Export Excel |
| 15-20 | Transitions |
| 21-22 | Approbations |
| 23-25 | Maintenance / HS / Perdu |
| 26 | Rapports |
| 27-30 | Change |
| 31-34 | EOD |
| 35-36 | Incidents |
| 37-38 | Contrôles |
| 39-41 | Infra IT |
| 42-43 | Config |

---

*COFINA — Gestion Parc Informatique v1.0.0*
