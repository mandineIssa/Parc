<?php

namespace App\Services;

/**
 * Cahier des charges — Gestion du parc informatique (COFINA).
 * Base métier fournie par le maître d’ouvrage + compléments des parties manquantes.
 */
class CahierDesChargesModuleParcBuilder
{
    /**
     * @return list<array{num: int, title: string, html: string}>
     */
    public function chapters(): array
    {
        return [
            [
                'num' => 1,
                'title' => 'Contexte et justification',
                'html' => <<<'HTML'
<p>La gestion du parc informatique vise à mettre en place une solution permettant d’assurer une
<strong>traçabilité complète</strong> des équipements informatiques, depuis leur acquisition jusqu’à
leur réforme ou sortie du parc.</p>
<p>La solution devra permettre au Département IT de maîtriser :</p>
<ul>
<li>les équipements affectés aux collaborateurs ;</li>
<li>les équipements disponibles en stock ;</li>
<li>les mouvements d’équipements entre collaborateurs, services et agences ;</li>
<li>les installations et interventions techniques ;</li>
<li>les maintenances préventives et correctives ;</li>
<li>les licences et logiciels installés ;</li>
<li>les garanties et contrats de maintenance ;</li>
<li>l’historique des équipements ;</li>
<li>les restitutions et sorties de parc ;</li>
<li>les responsabilités des utilisateurs concernant le matériel informatique.</li>
</ul>
<p>L’objectif est également de renforcer la <strong>sécurité</strong>, la <strong>traçabilité</strong>,
la <strong>conformité</strong> et la <strong>disponibilité</strong> des ressources informatiques.</p>
<p><em>Complément :</em> dans un environnement bancaire, cette traçabilité constitue un prérequis
aux contrôles d’audit internes et externes (existence, affectation, mouvements, sortie du parc).</p>
HTML
            ],
            [
                'num' => 2,
                'title' => 'Objectifs du projet',
                'html' => <<<'HTML'
<p><strong>Objectif général</strong></p>
<p>Mettre en place un système centralisé de gestion du parc informatique permettant d’avoir une
vision fiable et en temps réel de l’ensemble des actifs IT.</p>
<p><strong>Objectifs spécifiques</strong></p>
<p>La solution devra permettre de :</p>
<ol>
<li>Référencer tous les équipements informatiques.</li>
<li>Attribuer chaque équipement à un utilisateur ou à un emplacement.</li>
<li>Assurer la traçabilité des mouvements.</li>
<li>Suivre les installations et configurations.</li>
<li>Historiser les interventions techniques.</li>
<li>Suivre les garanties et contrats de maintenance.</li>
<li>Identifier les équipements disponibles, affectés, en réparation ou réformés.</li>
<li>Gérer les stocks IT.</li>
<li>Produire des rapports et tableaux de bord.</li>
<li>Améliorer la responsabilité des utilisateurs.</li>
<li>Faciliter les audits internes et externes.</li>
<li>Réduire les pertes et les équipements non tracés.</li>
</ol>
HTML
            ],
            [
                'num' => 3,
                'title' => 'Périmètre du parc',
                'html' => <<<'HTML'
<p>Le système devra permettre de gérer notamment :</p>
<table>
<tr><th>Catégorie</th><th>Exemples</th></tr>
<tr><td>Ordinateurs</td><td>Desktop, Laptop</td></tr>
<tr><td>Serveurs</td><td>Physiques, virtuels</td></tr>
<tr><td>Imprimantes</td><td>Multifonctions, imprimantes réseau</td></tr>
<tr><td>Réseau</td><td>Switch, routeur, firewall, borne Wi-Fi</td></tr>
<tr><td>Téléphonie</td><td>Smartphones, téléphones IP</td></tr>
<tr><td>Sécurité</td><td>Token, équipements MFA</td></tr>
<tr><td>Périphériques</td><td>Écrans, claviers, souris, scanners</td></tr>
<tr><td>Stockage</td><td>Disques, NAS, supports de sauvegarde</td></tr>
<tr><td>Équipements spécifiques</td><td>TPE, équipements agences</td></tr>
<tr><td>Logiciels</td><td>OS, antivirus, applications, licences</td></tr>
<tr><td>Accessoires</td><td>Docking station, chargeurs, câbles</td></tr>
</table>
<p><strong>Complément — Périmètre fonctionnel :</strong> inventaire, stock, mouvements / affectations,
installations, interventions, licences, garanties, inventaire physique, tableaux de bord, documents PDF.</p>
<p><strong>Complément — Hors périmètre (sauf besoin explicite ultérieur) :</strong>
MDM complet, remédiation automatique des postes, gestion RH complète, comptabilité analytique avancée,
outils de supervision réseau temps réel (hors inventaire).</p>
HTML
            ],
            [
                'num' => 4,
                'title' => 'Fiche d’identification d’un équipement',
                'html' => <<<'HTML'
<p>Chaque équipement devra disposer d’une <strong>fiche unique</strong>.</p>
<p><strong>Informations générales</strong></p>
<ul>
<li>Numéro d’inventaire</li>
<li>Catégorie</li>
<li>Type d’équipement</li>
<li>Marque</li>
<li>Modèle</li>
<li>Numéro de série</li>
<li>Numéro de référence</li>
<li>Adresse MAC</li>
<li>Adresse IP</li>
<li>Nom machine</li>
<li>Date d’acquisition</li>
<li>Fournisseur</li>
<li>Numéro de facture</li>
<li>Prix d’acquisition</li>
<li>Date de mise en service</li>
<li>Date de fin de garantie</li>
<li>Contrat de maintenance</li>
<li>Statut</li>
</ul>
<p><strong>Statuts possibles</strong></p>
<ul>
<li>En stock</li>
<li>Affecté</li>
<li>En installation</li>
<li>En maintenance</li>
<li>En réparation</li>
<li>En attente d’affectation</li>
<li>Hors service</li>
<li>Réformé</li>
<li>Perdu</li>
<li>Volé</li>
</ul>
<p><strong>Complément — Règles :</strong></p>
<ul>
<li>Le numéro de série (et/ou le numéro d’inventaire) doit être unique.</li>
<li>Tout changement de statut doit être historisé (qui, quand, motif).</li>
<li>Un équipement « Affecté » doit être rattaché à un utilisateur et/ou une localisation.</li>
</ul>
HTML
            ],
            [
                'num' => 5,
                'title' => 'Fiche de mouvement du matériel',
                'html' => <<<'HTML'
<p>La fiche de mouvement constitue un élément essentiel du dispositif.
Elle doit être générée à <strong>chaque changement de responsabilité ou de localisation</strong>.</p>
<p><strong>Informations</strong></p>
<ul>
<li>Référence mouvement</li>
<li>Date</li>
<li>Type de mouvement :
<ul>
<li>Affectation</li>
<li>Transfert</li>
<li>Retour</li>
<li>Prêt</li>
<li>Remplacement</li>
<li>Réparation</li>
<li>Retour de réparation</li>
<li>Mise en stock</li>
<li>Sortie définitive</li>
<li>Réforme</li>
</ul>
</li>
</ul>
<p><strong>Équipement</strong> : numéro d’inventaire, type, marque, modèle, numéro de série.</p>
<p><strong>Situation avant mouvement</strong> : utilisateur précédent, matricule, département, agence, localisation.</p>
<p><strong>Situation après mouvement</strong> : nouvel utilisateur, matricule, département, agence, localisation.</p>
<p><strong>Validation</strong> : demandeur, responsable hiérarchique, responsable IT, utilisateur réceptionnaire,
date de remise, signature / validation électronique.</p>
<p><strong>État du matériel</strong> : bon état, état moyen, endommagé, accessoires remis.</p>
<p><strong>Complément :</strong> la fiche doit être exportable en PDF et conservée dans l’historique
de l’équipement. Aucun mouvement critique ne doit être clos sans validation des acteurs requis.</p>
HTML
            ],
            [
                'num' => 6,
                'title' => 'Fiche de suivi d’installation',
                'html' => <<<'HTML'
<p>Cette fiche permettra de suivre l’installation et la configuration d’un équipement.</p>
<p><strong>Identification</strong> : numéro de ticket, date d’installation, numéro d’inventaire,
utilisateur, département, agence, technicien.</p>
<p><strong>Installation matérielle</strong> : PC installé, écran, docking station, imprimante, téléphone, accessoires.</p>
<p><strong>Configuration système</strong> : système d’exploitation, version, nom du poste, adresse IP,
adresse MAC, domaine, antivirus/EDR, agent de supervision, outils de sécurité.</p>
<p><strong>Applications</strong> : Microsoft Office, navigateur, applications métiers, applications bancaires,
outils de communication, outils de sécurité, applications spécifiques.</p>
<p><strong>Sécurité</strong> : antivirus installé, EDR installé, chiffrement activé, pare-feu activé,
MFA configuré, politique de mot de passe appliquée, correctifs à jour.</p>
<p><strong>Tests</strong> : connexion réseau, Internet, impression, applications métiers, messagerie,
accès aux ressources internes, VPN, tests de sécurité.</p>
<p><strong>Validation</strong> : technicien, utilisateur, responsable IT, date de validation.</p>
<p><strong>Complément :</strong> la fiche d’installation doit être liée à l’équipement et au mouvement
d’affectation correspondant ; elle doit être générable en PDF.</p>
HTML
            ],
            [
                'num' => 7,
                'title' => 'Gestion des interventions',
                'html' => <<<'HTML'
<p>Chaque intervention devra être historisée.</p>
<p><strong>Types d’intervention</strong> : incident, maintenance préventive, maintenance corrective,
installation, configuration, dépannage, mise à jour, remplacement, réinstallation, nettoyage, réforme.</p>
<p><strong>Pour chaque intervention :</strong></p>
<ul>
<li>Numéro de ticket</li>
<li>Date</li>
<li>Équipement</li>
<li>Utilisateur</li>
<li>Technicien</li>
<li>Description du problème</li>
<li>Diagnostic</li>
<li>Action réalisée</li>
<li>Pièces remplacées</li>
<li>Durée d’intervention</li>
<li>Résultat</li>
<li>Statut</li>
<li>Date de clôture</li>
</ul>
<p><strong>Complément :</strong> les interventions doivent apparaître dans l’historique unique de l’équipement
et, le cas échéant, mettre à jour automatiquement le statut (ex. passage en maintenance / réparation).</p>
HTML
            ],
            [
                'num' => 8,
                'title' => 'Gestion du stock informatique',
                'html' => <<<'HTML'
<p>Le système devra permettre de connaître à tout moment :</p>
<p><strong>Stock initial + Entrées − Sorties = Stock disponible</strong></p>
<p>Les mouvements devront être automatiquement historisés.</p>
<p><strong>Exemple</strong></p>
<table>
<tr><th>Équipement</th><th>Stock</th><th>Affecté</th><th>Maintenance</th><th>Disponible</th></tr>
<tr><td>Laptop</td><td>50</td><td>40</td><td>3</td><td>7</td></tr>
<tr><td>Desktop</td><td>30</td><td>25</td><td>2</td><td>3</td></tr>
<tr><td>Écran</td><td>70</td><td>55</td><td>5</td><td>10</td></tr>
</table>
<p><strong>Complément :</strong> le stock doit pouvoir être consulté par catégorie, agence et type ;
toute affectation, restitution ou réforme doit mettre à jour le stock sans saisie manuelle redondante.</p>
HTML
            ],
            [
                'num' => 9,
                'title' => 'Gestion des licences',
                'html' => <<<'HTML'
<p>Le système devra également permettre de suivre :</p>
<ul>
<li>Nom du logiciel</li>
<li>Version</li>
<li>Éditeur</li>
<li>Type de licence</li>
<li>Nombre de licences acquises</li>
<li>Nombre de licences utilisées</li>
<li>Nombre disponible</li>
<li>Date d’achat</li>
<li>Date d’expiration</li>
<li>Coût</li>
<li>Fournisseur</li>
<li>Contrat associé</li>
</ul>
<p>Une <strong>alerte</strong> devra être générée avant l’expiration d’une licence.</p>
<p><strong>Complément :</strong> alertes recommandées à 90, 60 et 30 jours ; reporting du taux
d’utilisation (acquises vs utilisées) pour éviter la sur/sous-licence.</p>
HTML
            ],
            [
                'num' => 10,
                'title' => 'Gestion des garanties et contrats',
                'html' => <<<'HTML'
<p>Pour chaque équipement :</p>
<ul>
<li>Fournisseur</li>
<li>Date d’achat</li>
<li>Date de début de garantie</li>
<li>Date de fin de garantie</li>
<li>Type de garantie</li>
<li>Contrat de maintenance</li>
<li>Numéro du contrat</li>
<li>Contact fournisseur</li>
<li>Date d’expiration</li>
</ul>
<p>Le système devra générer des alertes :</p>
<ul>
<li>90 jours avant expiration ;</li>
<li>60 jours avant expiration ;</li>
<li>30 jours avant expiration.</li>
</ul>
HTML
            ],
            [
                'num' => 11,
                'title' => 'Inventaire physique',
                'html' => <<<'HTML'
<p>Le système devra permettre de réaliser périodiquement un inventaire physique.</p>
<p>Pour chaque équipement : <strong>Équipement attendu → Équipement trouvé → Écart</strong>.</p>
<p><strong>Exemple</strong></p>
<table>
<tr><th>Inventaire</th><th>Attendu</th><th>Trouvé</th><th>Écart</th><th>Observation</th></tr>
<tr><td>Laptop</td><td>120</td><td>118</td><td>2</td><td>À rechercher</td></tr>
<tr><td>Desktop</td><td>80</td><td>80</td><td>0</td><td>Conforme</td></tr>
<tr><td>Écran</td><td>150</td><td>147</td><td>3</td><td>À vérifier</td></tr>
</table>
<p>Chaque écart devra faire l’objet d’une <strong>justification</strong>.</p>
<p><strong>Complément :</strong> l’inventaire doit être cadré par campagne (période, agence, périmètre),
clos par un rapport de conformité, et alimenter le KPI « taux de conformité inventaire ».</p>
HTML
            ],
            [
                'num' => 12,
                'title' => 'Tableau de bord IT',
                'html' => <<<'HTML'
<p>Le système devra proposer un dashboard avec notamment :</p>
<ul>
<li>Nombre total d’équipements</li>
<li>Équipements affectés</li>
<li>Équipements disponibles</li>
<li>Équipements en maintenance</li>
<li>Équipements hors service</li>
<li>Équipements en fin de garantie</li>
<li>Équipements par agence</li>
<li>Équipements par département</li>
<li>Équipements par utilisateur</li>
<li>Nombre de mouvements</li>
<li>Nombre d’incidents</li>
<li>Nombre d’interventions</li>
<li>Taux de disponibilité</li>
<li>Taux de conformité de l’inventaire</li>
<li>Valeur du parc informatique</li>
</ul>
<p><strong>Complément :</strong> les indicateurs doivent être filtrables (période, agence, catégorie)
et exportables (Excel / PDF) pour reporting de direction.</p>
HTML
            ],
            [
                'num' => 13,
                'title' => 'Gestion des utilisateurs et habilitations',
                'html' => <<<'HTML'
<p>La solution devra prévoir plusieurs profils.</p>
<p><strong>Administrateur IT</strong> — Accès complet.</p>
<p><strong>Responsable IT</strong></p>
<ul>
<li>Consultation</li>
<li>Validation</li>
<li>Reporting</li>
<li>Gestion des affectations</li>
</ul>
<p><strong>Technicien</strong></p>
<ul>
<li>Installation</li>
<li>Intervention</li>
<li>Maintenance</li>
<li>Mise à jour des fiches techniques</li>
</ul>
<p><strong>Responsable métier</strong></p>
<ul>
<li>Consultation des équipements de son périmètre</li>
<li>Validation des demandes</li>
</ul>
<p><strong>Utilisateur</strong></p>
<ul>
<li>Consultation de son matériel</li>
<li>Demande d’assistance</li>
<li>Déclaration d’incident</li>
<li>Validation de réception / restitution</li>
</ul>
<p><strong>Complément :</strong> le principe du moindre privilège s’applique ; toute action sensible
(approbation, réforme, suppression) doit être journalisée.</p>
HTML
            ],
            [
                'num' => 14,
                'title' => 'Workflow d’affectation',
                'html' => <<<'HTML'
<p>Le processus pourrait être :</p>
<p><strong>Demande → Validation responsable → Validation IT → Préparation matériel → Installation
→ Affectation → Signature utilisateur → Mise à jour du parc</strong></p>
<p><strong>Complément — Règles :</strong></p>
<ul>
<li>Pas d’affectation sans validation IT.</li>
<li>La signature utilisateur matérialise la prise en charge du matériel.</li>
<li>La mise à jour du parc (statut « Affecté ») est automatique à la fin du workflow.</li>
<li>Les documents (mouvement, installation, PV de remise) sont générés à la clôture.</li>
</ul>
HTML
            ],
            [
                'num' => 15,
                'title' => 'Workflow de restitution',
                'html' => <<<'HTML'
<p><strong>Demande de restitution → Réception matériel → Contrôle état → Vérification accessoires
→ Mise à jour fiche équipement → Mise en stock / Maintenance / Réforme</strong></p>
<p><strong>Complément — Orientation selon l’état :</strong></p>
<ul>
<li>Bon état → remise en stock (disponible)</li>
<li>Panne / anomalie → maintenance ou réparation</li>
<li>Fin de vie / hors usage → réforme (avec procédure §18)</li>
</ul>
HTML
            ],
            [
                'num' => 16,
                'title' => 'Charte informatique',
                'html' => <<<'HTML'
<p>La charte informatique devra définir les règles d’utilisation des ressources informatiques de l’entreprise.
Elle devra notamment couvrir :</p>
<p><strong>Utilisation du matériel</strong></p>
<p>L’utilisateur est responsable du matériel qui lui est confié et doit :</p>
<ul>
<li>en prendre soin ;</li>
<li>signaler rapidement toute panne ;</li>
<li>signaler toute perte ou vol ;</li>
<li>ne pas modifier la configuration sans autorisation IT ;</li>
<li>ne pas installer de logiciel non autorisé ;</li>
<li>ne pas prêter son équipement sans autorisation.</li>
</ul>
<p><strong>Accès au système d’information</strong></p>
<p>L’utilisateur doit :</p>
<ul>
<li>protéger ses identifiants ;</li>
<li>ne jamais communiquer son mot de passe ;</li>
<li>utiliser l’authentification forte lorsqu’elle est disponible ;</li>
<li>verrouiller son poste lorsqu’il quitte son bureau ;</li>
<li>respecter les politiques de sécurité.</li>
</ul>
<p><strong>Internet et messagerie</strong></p>
<p>Il est interdit notamment de :</p>
<ul>
<li>télécharger des logiciels non autorisés ;</li>
<li>accéder à des sites présentant un risque pour la sécurité ;</li>
<li>transmettre des informations confidentielles sans autorisation ;</li>
<li>utiliser la messagerie professionnelle à des fins susceptibles de compromettre l’entreprise.</li>
</ul>
<p><strong>Données</strong></p>
<p>Les utilisateurs doivent respecter :</p>
<ul>
<li>la confidentialité ;</li>
<li>l’intégrité des données ;</li>
<li>les règles de sauvegarde ;</li>
<li>les règles de classification de l’information ;</li>
<li>les procédures de protection des données.</li>
</ul>
<p><strong>Complément :</strong> la charte doit être acceptée électroniquement lors de l’affectation
(ou périodiquement) ; la preuve (utilisateur, date, version) est conservée.</p>
HTML
            ],
            [
                'num' => 17,
                'title' => 'Procédure en cas de perte ou de vol',
                'html' => <<<'HTML'
<p>Tout utilisateur doit signaler immédiatement :</p>
<ul>
<li>perte d’un ordinateur ;</li>
<li>vol d’un ordinateur ;</li>
<li>perte d’un téléphone ;</li>
<li>perte d’un support de stockage ;</li>
<li>compromission potentielle d’un compte.</li>
</ul>
<p>L’IT devra alors pouvoir :</p>
<ol>
<li>Désactiver les accès.</li>
<li>Réinitialiser les comptes concernés.</li>
<li>Bloquer l’équipement lorsque la technologie le permet.</li>
<li>Vérifier les journaux de sécurité.</li>
<li>Informer les responsables concernés.</li>
<li>Documenter l’incident.</li>
<li>Procéder à l’inventaire et à la mise à jour du statut de l’équipement.</li>
</ol>
<p><strong>Complément :</strong> le statut de l’équipement passe à « Perdu » ou « Volé » ;
la déclaration (plainte, références) est archivée dans le dossier de l’équipement.</p>
HTML
            ],
            [
                'num' => 18,
                'title' => 'Sortie et réforme du matériel',
                'html' => <<<'HTML'
<p>Avant toute réforme :</p>
<ul>
<li>identification du matériel ;</li>
<li>justification de la réforme ;</li>
<li>validation IT ;</li>
<li>validation administrative / financière ;</li>
<li>effacement sécurisé des données ;</li>
<li>retrait des actifs ;</li>
<li>mise à jour du statut ;</li>
<li>conservation de la preuve de réforme.</li>
</ul>
<p>Pour les ordinateurs et supports de stockage, l’<strong>effacement sécurisé des données</strong>
doit être obligatoire avant leur sortie du parc.</p>
<p><strong>Complément :</strong> aucune réforme ne peut être clôturée sans preuve d’effacement
(certificat / PV) jointe au dossier.</p>
HTML
            ],
            [
                'num' => 19,
                'title' => 'Indicateurs KPI',
                'html' => <<<'HTML'
<p>Prévoir au minimum :</p>
<table>
<tr><th>KPI</th><th>Objectif</th></tr>
<tr><td>Taux de couverture de l’inventaire</td><td>100 %</td></tr>
<tr><td>Taux d’équipements correctement affectés</td><td>≥ 98 %</td></tr>
<tr><td>Taux d’équipements non identifiés</td><td>&lt; 2 %</td></tr>
<tr><td>Taux de conformité inventaire</td><td>≥ 98 %</td></tr>
<tr><td>Taux de disponibilité du parc</td><td>≥ 95 %</td></tr>
<tr><td>Nombre d’équipements hors garantie</td><td>Suivi</td></tr>
<tr><td>Nombre d’équipements sans fiche</td><td>0</td></tr>
<tr><td>Nombre de mouvements non validés</td><td>0</td></tr>
<tr><td>Délai moyen d’installation</td><td>Suivi</td></tr>
<tr><td>Délai moyen de résolution</td><td>Suivi</td></tr>
<tr><td>Taux de renouvellement du parc</td><td>Suivi</td></tr>
</table>
HTML
            ],
            [
                'num' => 20,
                'title' => 'Documents à intégrer dans la solution',
                'html' => <<<'HTML'
<p>Le système devra idéalement permettre de générer automatiquement :</p>
<ol>
<li>Fiche d’inventaire équipement</li>
<li>Fiche de mouvement</li>
<li>Fiche d’affectation</li>
<li>Fiche de restitution</li>
<li>Fiche d’installation</li>
<li>Fiche d’intervention</li>
<li>Fiche de maintenance</li>
<li>Fiche de prêt</li>
<li>Fiche de réforme</li>
<li>Fiche d’inventaire physique</li>
<li>Charte informatique</li>
<li>Procès-verbal de remise du matériel</li>
</ol>
HTML
            ],
            [
                'num' => 21,
                'title' => 'Architecture fonctionnelle recommandée',
                'html' => <<<'HTML'
<pre>
                    GESTION DU PARC IT
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
     INVENTAIRE         MOUVEMENTS        MAINTENANCE
        │                  │                  │
   ┌────┴────┐        ┌────┴────┐        ┌────┴────┐
   │Matériel │        │Affectation│      │Incident │
   │Logiciel │        │Transfert │       │Réparation│
   │Licence  │        │Retour    │       │Préventif │
   └─────────┘        └──────────┘        └─────────┘
        │                  │                  │
        └──────────────────┼──────────────────┘
                           │
                     TABLEAU DE BORD
                           │
                    ┌──────┴──────┐
                    │   REPORTING │
                    │    KPI      │
                    └─────────────┘
</pre>
<p><strong>Recommandation importante</strong></p>
<p>Structurer le projet comme un outil <strong>IT Asset Management (ITAM)</strong> couplé à un outil
de gestion des tickets / incidents. Ainsi, un équipement aura un historique unique :</p>
<p><em>Acquisition → Stock → Installation → Affectation → Mouvements → Incidents → Maintenance
→ Restitution → Réaffectation → Réforme.</em></p>
<p>Cela permettra une traçabilité complète de chaque équipement, particulièrement importante
dans un environnement bancaire et pour les contrôles d’audit.</p>
HTML
            ],
            [
                'num' => 22,
                'title' => 'Exigences non fonctionnelles (complément)',
                'html' => <<<'HTML'
<p>Section ajoutée pour compléter le cahier des charges.</p>
<table>
<tr><th>Thème</th><th>Exigence</th></tr>
<tr><td>Disponibilité</td><td>Accès aux heures ouvrées ; sauvegarde quotidienne des données</td></tr>
<tr><td>Sécurité</td><td>Authentification, habilitations par profil, journalisation des actions sensibles, HTTPS</td></tr>
<tr><td>Traçabilité</td><td>Historique non altérable des mouvements, validations et changements de statut</td></tr>
<tr><td>Performance</td><td>Listes paginées ; temps de réponse raisonnable sur inventaires volumineux</td></tr>
<tr><td>Ergonomie</td><td>Interfaces claires pour techniciens et validateurs ; documents PDF lisibles</td></tr>
<tr><td>Interopérabilité</td><td>Export Excel / PDF ; possibilité d’échanges API ultérieurs</td></tr>
<tr><td>Conformité</td><td>Conservation des preuves (signatures, charte, réforme, inventaire)</td></tr>
</table>
HTML
            ],
            [
                'num' => 23,
                'title' => 'Critères d’acceptation (complément)',
                'html' => <<<'HTML'
<p>Section ajoutée pour la recette.</p>
<ol>
<li>Tout équipement possède une fiche unique avec statut à jour.</li>
<li>Tout mouvement génère une fiche et met à jour l’historique.</li>
<li>Le workflow d’affectation aboutit à une signature utilisateur et un statut « Affecté ».</li>
<li>Le workflow de restitution oriente correctement vers stock, maintenance ou réforme.</li>
<li>Les alertes garantie et licences se déclenchent à 90 / 60 / 30 jours.</li>
<li>Une campagne d’inventaire physique produit un rapport attendu / trouvé / écart justifié.</li>
<li>Aucune réforme d’équipement de stockage sans preuve d’effacement sécurisé.</li>
<li>Les profils d’habilitation respectent les droits définis au §13.</li>
<li>Le dashboard affiche les indicateurs du §12 ; les KPI du §19 sont calculables.</li>
<li>Les 12 documents du §20 sont générables (ou planifiés explicitement en lot de livraison).</li>
</ol>
HTML
            ],
            [
                'num' => 24,
                'title' => 'Glossaire (complément)',
                'html' => <<<'HTML'
<table>
<tr><th>Terme</th><th>Définition</th></tr>
<tr><td>ITAM</td><td>IT Asset Management — gestion des actifs informatiques</td></tr>
<tr><td>Affectation</td><td>Mise à disposition d’un équipement à un utilisateur / emplacement</td></tr>
<tr><td>Restitution</td><td>Retour du matériel par l’utilisateur</td></tr>
<tr><td>Réforme</td><td>Sortie définitive du parc (fin de vie, destruction, cession…)</td></tr>
<tr><td>Inventaire physique</td><td>Contrôle terrain attendu / trouvé / écart</td></tr>
<tr><td>Mouvement</td><td>Changement de responsabilité ou de localisation</td></tr>
<tr><td>Effacement sécurisé</td><td>Suppression irréversible des données avant sortie du parc</td></tr>
</table>
<p class="meta">Document de référence — Cahier des charges Gestion du parc informatique — Version 1.1</p>
HTML
            ],
        ];
    }
}
