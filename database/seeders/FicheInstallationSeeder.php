<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FicheInstallation;
use App\Models\Approval;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FicheInstallationSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer quelques données existantes
        $approval = Approval::first();
        $equipment = Equipment::first();
        $user = User::where('role', 'admin')->first();

        if ($approval && $equipment && $user) {
            // Créer une fiche d'installation principale
            FicheInstallation::create([
                'approval_id' => $approval->id,
                'equipment_id' => $equipment->id,
                'user_id' => $user->id,
                'date_application' => now(),
                'numero_fiche' => FicheInstallation::generateNumeroFiche(),
                'agence_nom' => 'AGENCE NORD',
                'date_installation' => now(),
                'prerequis' => [
                    'sauvegarde_donnees' => true,
                    'sauvegarde_outlook' => true,
                    'sauvegarde_tous_utilisateurs' => true,
                    'reinstallation_os' => true,
                ],
                'logiciels_installes' => [
                    'logiciels_adobe' => true,
                    'logiciels_ms_office' => true,
                    'logiciels_kaspersky' => true,
                    'logiciels_anydesk' => true,
                    'logiciels_jre' => true,
                    'logiciels_pilotes' => true,
                    'logiciels_chrome' => true,
                    'logiciels_firefox' => true,
                    'logiciels_imprimante' => false,
                    'logiciels_zoom' => true,
                    'logiciels_vpn' => true,
                    'logiciels_winrar' => true,
                    'logiciels_scanner_naps2' => false,
                ],
                'raccourcis' => [
                    'raccourcis_nafa' => true,
                    'raccourcis_flexcube' => true,
                    'copie_logiciels_local' => true,
                    'applications_transfert' => true,
                    'applications_cc' => true,
                ],
                'autres_configurations' => [
                    'creation_compte_admin' => true,
                    'integration_domaine' => true,
                    'parametrage_messagerie' => true,
                    'partition_disque' => false,
                    'desactivation_ports_usb' => true,
                    'connexion_dossier_partage' => true,
                ],
                'installateur_nom' => 'DIOP',
                'installateur_prenom' => 'Ahmadou',
                'installateur_fonction' => 'IT',
                'date_verification' => now()->addDay(),
                'verifications' => [
                    'verif_logiciels_installes' => true,
                    'verif_messagerie' => true,
                    'verif_sauvegarde' => true,
                    'verif_integration_ad' => true,
                    'verif_systeme_licence' => true,
                    'verif_restauration' => true,
                ],
                'autres_verifications' => [
                    'verif_fiche_mouvement' => true,
                    'verif_restriction_web' => true,
                    'verif_validation_installation' => true,
                ],
                'verificateur_nom' => 'SALL',
                'verificateur_prenom' => 'Moussa',
                'verificateur_fonction' => 'Super Admin',
                'status' => 'complet',
                'observations' => 'Installation réalisée avec succès, tous les tests passés.',
                'checklist_complete' => [
                    'prerequis_complet' => 4,
                    'logiciels_complet' => 13,
                    'raccourcis_complet' => 5,
                    'configurations_complet' => 6,
                    'verifications_complet' => 6,
                    'autres_verifications_complet' => 3,
                ],
            ]);

            // Créer quelques autres fiches d'installation
            $agences = ['AGENCE SUD', 'AGENCE EST', 'AGENCE OUEST', 'AGENCE CENTRE', 'SIEGE'];
            $statuses = ['draft', 'en_cours', 'installe', 'verifie', 'complet'];

            for ($i = 0; $i < 5; $i++) {
                FicheInstallation::create([
                    'approval_id' => $approval->id,
                    'equipment_id' => $equipment->id,
                    'user_id' => $user->id,
                    'date_application' => now()->subDays($i + 2),
                    'numero_fiche' => 'FI-' . now()->subDays($i + 2)->format('Ymd') . '-' . str_pad($i + 2, 4, '0', STR_PAD_LEFT),
                    'agence_nom' => $agences[$i],
                    'date_installation' => now()->subDays($i + 1),
                    'prerequis' => [
                        'sauvegarde_donnees' => $i > 0,
                        'reinstallation_os' => $i > 1,
                    ],
                    'logiciels_installes' => [
                        'logiciels_ms_office' => true,
                        'logiciels_kaspersky' => $i > 0,
                        'logiciels_chrome' => true,
                    ],
                    'raccourcis' => [
                        'raccourcis_nafa' => true,
                        'raccourcis_flexcube' => $i > 1,
                    ],
                    'autres_configurations' => [
                        'integration_domaine' => $i > 0,
                        'parametrage_messagerie' => $i > 1,
                    ],
                    'installateur_nom' => 'DIOP',
                    'installateur_prenom' => 'Ahmadou',
                    'installateur_fonction' => 'IT',
                    'date_verification' => $i > 2 ? now()->subDays($i) : null,
                    'verifications' => $i > 2 ? [
                        'verif_logiciels_installes' => true,
                        'verif_messagerie' => $i > 3,
                    ] : null,
                    'autres_verifications' => $i > 3 ? [
                        'verif_fiche_mouvement' => true,
                        'verif_validation_installation' => $i == 4,
                    ] : null,
                    'verificateur_nom' => $i > 2 ? 'SALL' : null,
                    'verificateur_prenom' => $i > 2 ? 'Moussa' : null,
                    'verificateur_fonction' => $i > 2 ? 'Super Admin' : null,
                    'status' => $statuses[$i],
                    'observations' => ['En attente', 'En cours', 'Installé', 'Vérifié', 'Complet'][$i],
                    'checklist_complete' => [
                        'prerequis_complet' => $i > 0 ? 2 : 1,
                        'logiciels_complet' => $i + 2,
                        'configurations_complet' => $i > 1 ? 2 : 1,
                    ],
                ]);
            }
        }
    }
}