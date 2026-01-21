<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FicheMouvement;
use App\Models\Approval;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FicheMouvementSeeder extends Seeder
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
            // Créer une fiche de mouvement
            FicheMouvement::create([
                'approval_id' => $approval->id,
                'equipment_id' => $equipment->id,
                'user_id' => $user->id,
                'date_application' => now(),
                'numero_fiche' => FicheMouvement::generateNumeroFiche(),
                'expediteur_nom' => 'DIOP',
                'expediteur_prenom' => 'Ahmadou',
                'expediteur_fonction' => 'Agent IT',
                'receptionnaire_nom' => 'NDIAYE',
                'receptionnaire_prenom' => 'Fatou',
                'receptionnaire_fonction' => 'Agent Commercial',
                'type_materiel' => 'Ordinateur Portable',
                'reference' => $equipment->numero_serie,
                'lieu_depart' => 'SIEGE COFINA',
                'destination' => 'AGENCE NORD',
                'motif' => 'Dotation',
                'date_expediteur' => now(),
                'date_receptionnaire' => now(),
                'status' => 'completed',
                'notes' => 'Livraison effectuée avec succès',
            ]);

            // Créer quelques autres fiches de mouvement
            for ($i = 0; $i < 5; $i++) {
                FicheMouvement::create([
                    'approval_id' => $approval->id,
                    'equipment_id' => $equipment->id,
                    'user_id' => $user->id,
                    'date_application' => now()->subDays($i),
                    'numero_fiche' => 'FM-' . now()->subDays($i)->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'expediteur_nom' => 'DIOP',
                    'expediteur_prenom' => 'Ahmadou',
                    'expediteur_fonction' => 'Agent IT',
                    'receptionnaire_nom' => ['NDIAYE', 'SENE', 'FALL', 'GUEYE', 'TOURE'][$i],
                    'receptionnaire_prenom' => ['Fatou', 'Moussa', 'Aminata', 'Ibrahima', 'Mariama'][$i],
                    'receptionnaire_fonction' => ['Agent Commercial', 'Responsable Agence', 'Caissier', 'Conseiller Clientèle', 'Superviseur'][$i],
                    'type_materiel' => ['Ordinateur Portable', 'Imprimante', 'Scanner', 'Téléphone IP', 'Tablette'][$i],
                    'reference' => $equipment->numero_serie . '-' . $i,
                    'lieu_depart' => 'SIEGE COFINA',
                    'destination' => ['AGENCE NORD', 'AGENCE SUD', 'AGENCE EST', 'AGENCE OUEST', 'AGENCE CENTRE'][$i],
                    'motif' => ['Dotation', 'Remplacement', 'Maintenance', 'Mise à niveau', 'Nouveau poste'][$i],
                    'date_expediteur' => now()->subDays($i),
                    'date_receptionnaire' => now()->subDays($i),
                    'status' => ['draft', 'completed', 'completed', 'draft', 'completed'][$i],
                    'notes' => ['En attente signature', 'Terminé', 'Signé', 'En cours', 'Archivé'][$i],
                ]);
            }
        }
    }
}