<?php
// database/seeders/ApprovalsTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Approval;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ApprovalsTableSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les données existantes
        $equipments = Equipment::all();
        $users = User::all();
        
        if ($equipments->isEmpty() || $users->isEmpty()) {
            $this->command->info('⚠️  Aucun équipement ou utilisateur trouvé. Exécutez d\'abord les seeders Users et Equipments.');
            return;
        }
        
        // Équipements disponibles
        $availableEquipments = $equipments->where('status', 'available')->take(5);
        $inStockEquipments = $equipments->where('status', 'in_stock')->take(5);
        
        $statusTransitions = [
            ['from' => 'in_stock', 'to' => 'in_use'],
            ['from' => 'available', 'to' => 'in_use'],
            ['from' => 'maintenance', 'to' => 'in_use'],
            ['from' => 'in_use', 'to' => 'maintenance'],
            ['from' => 'available', 'to' => 'maintenance'],
        ];
        
        $agences = ['SIEGE', 'AGENCE NORD', 'AGENCE SUD', 'AGENCE EST', 'AGENCE OUEST'];
        $postes = ['Agent Commercial', 'Responsable Agence', 'Caissier', 'Conseiller Clientèle', 'Superviseur'];
        $departements = ['Commercial', 'Finance', 'Ressources Humaines', 'IT', 'Marketing'];
        
        $approvalStatuses = ['pending', 'approved', 'rejected'];
        
        // Créer des approvals pour les équipements disponibles
        $i = 0;
        foreach ($availableEquipments as $equipment) {
            $user = $users->random();
            $requestedBy = $users->where('role', 'agent')->first() ?? $user;
            
            $transition = $statusTransitions[$i % count($statusTransitions)];
            
            Approval::create([
                'equipment_id' => $equipment->id,
                'user_id' => $user->id,
                'requested_by' => $requestedBy->id,
                'from_status' => $transition['from'],
                'to_status' => $transition['to'],
                'request_data' => [
                    'user_name' => $user->name,
                    'departement' => $departements[$i % count($departements)],
                    'poste_affecte' => $postes[$i % count($postes)],
                    'date_affectation' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                    'agent_nom' => $requestedBy->name,
                    'agent_prenom' => '',
                    'agent_fonction' => 'Agent IT',
                    'agence' => $agences[$i % count($agences)],
                    'raison' => 'Nouveau poste / Remplacement',
                    'notes' => 'Demande standard pour nouvel affectation',
                ],
                'status' => $approvalStatuses[$i % count($approvalStatuses)],
                'approver_id' => $approvalStatuses[$i % count($approvalStatuses)] != 'pending' 
                    ? $users->whereIn('role', ['admin', 'super_admin'])->random()->id 
                    : null,
                'approved_at' => $approvalStatuses[$i % count($approvalStatuses)] == 'approved' 
                    ? now()->subDays(rand(1, 10)) 
                    : null,
                'rejected_at' => $approvalStatuses[$i % count($approvalStatuses)] == 'rejected' 
                    ? now()->subDays(rand(1, 5)) 
                    : null,
                'rejection_reason' => $approvalStatuses[$i % count($approvalStatuses)] == 'rejected' 
                    ? ['Matériel non disponible', 'Demande incomplète', 'Budget non approuvé', 'Priorité basse'][rand(0, 3)] 
                    : null,
                'validation_notes' => $approvalStatuses[$i % count($approvalStatuses)] == 'approved' 
                    ? 'Demande approuvée après vérification des stocks.' 
                    : null,
            ]);
            
            $i++;
        }
        
        // Créer des approvals pour les équipements en stock
        foreach ($inStockEquipments as $equipment) {
            $user = $users->random();
            $requestedBy = $users->where('role', 'agent')->first() ?? $user;
            
            $transition = $statusTransitions[$i % count($statusTransitions)];
            
            Approval::create([
                'equipment_id' => $equipment->id,
                'user_id' => $user->id,
                'requested_by' => $requestedBy->id,
                'from_status' => $transition['from'],
                'to_status' => $transition['to'],
                'request_data' => [
                    'user_name' => $user->name,
                    'departement' => $departements[$i % count($departements)],
                    'poste_affecte' => $postes[$i % count($postes)],
                    'date_affectation' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                    'agent_nom' => $requestedBy->name,
                    'agent_prenom' => '',
                    'agent_fonction' => 'Technicien IT',
                    'agence' => $agences[$i % count($agences)],
                    'raison' => 'Mise à niveau matérielle',
                    'notes' => 'Besoin d\'équipement plus performant',
                ],
                'status' => $approvalStatuses[$i % count($approvalStatuses)],
                'approver_id' => $approvalStatuses[$i % count($approvalStatuses)] != 'pending' 
                    ? $users->whereIn('role', ['admin', 'super_admin'])->random()->id 
                    : null,
                'approved_at' => $approvalStatuses[$i % count($approvalStatuses)] == 'approved' 
                    ? now()->subDays(rand(1, 15)) 
                    : null,
                'rejected_at' => $approvalStatuses[$i % count($approvalStatuses)] == 'rejected' 
                    ? now()->subDays(rand(1, 8)) 
                    : null,
                'rejection_reason' => $approvalStatuses[$i % count($approvalStatuses)] == 'rejected' 
                    ? ['Équipement réservé', 'Validation manager requise', 'Formation manquante'][rand(0, 2)] 
                    : null,
                'validation_notes' => $approvalStatuses[$i % count($approvalStatuses)] == 'approved' 
                    ? 'Équipement libéré pour affectation.' 
                    : null,
            ]);
            
            $i++;
        }
        
        $this->command->info('✅ ' . Approval::count() . ' approbations créées avec succès.');
    }
}