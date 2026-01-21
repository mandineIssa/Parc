<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;
use App\Models\Agency;
use App\Models\Category;
use App\Models\Supplier;
use Carbon\Carbon;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les IDs
        $siegeSocial = Agency::where('nom', 'Siège Social')->first();
        $agenceMermoz = Agency::where('nom', 'Agence Mermoz')->first();
        $agenceGrandDakar = Agency::where('nom', 'Agence Grand Dakar')->first();
        
        // Récupérer les catégories
        $postesUtilisateurs = Category::where('nom', 'Postes Utilisateurs')->first();
        $peripheriques = Category::where('nom', 'Périphériques')->first();
        $serveursStockage = Category::where('nom', 'Serveurs & Stockage')->first();
        $connectivite = Category::where('nom', 'Connectivité & Transmission')->first();
        $securiteReseau = Category::where('nom', 'Sécurité Réseau')->first();
        $infrastructure = Category::where('nom', 'Infrastructure & Support')->first();
        $videoSurveillance = Category::where('nom', 'Vidéosurveillance (CCTV)')->first();
        $controleAcces = Category::where('nom', 'Contrôle d\'accès')->first();
        $systemesAlarme = Category::where('nom', 'Systèmes d\'alarme')->first();
        
        // Récupérer les fournisseurs
        $cisco = Supplier::where('nom', 'like', '%Cisco%')->first();
        $dell = Supplier::where('nom', 'like', '%Dell%')->first();
        $hp = Supplier::where('nom', 'like', '%HP%')->first();
        $hikvision = Supplier::where('nom', 'like', '%Hikvision%')->first();
        $axis = Supplier::where('nom', 'like', '%Axis%')->first();
        $bosch = Supplier::where('nom', 'like', '%Bosch%')->first();
        $ubiquiti = Supplier::where('nom', 'like', '%Ubiquiti%')->first();
        $sonatel = Supplier::where('nom', 'like', '%Sonatel%')->first();
        $microsoft = Supplier::where('nom', 'like', '%Microsoft%')->first();
        $fortinet = Supplier::where('nom', 'like', '%Fortinet%')->first();
        
        $equipments = [
            // ============ informatique ============
            // Postes Utilisateurs
            [
                'numero_serie' => 'SN-DELL-LT-001',
                'agency_id' => $siegeSocial->id,
                'localisation' => 'Bureau 101 - Direction',
                'type' => 'informatique',
                'categorie_id' => $postesUtilisateurs->id,
                'nom' => 'Latitude 7420',
                'modele' => 'Latitude 7420',
                'marque' => 'Dell',
                'date_livraison' => Carbon::parse('2024-01-15'),
                'prix' => 1250000,
                'etat' => 'neuf',
                'statut' => 'parc',
                'departement' => 'Direction',
                'poste_staff' => 'Directeur Général',
                'date_mise_service' => Carbon::parse('2024-01-20'),
                'fournisseur_id' => $dell->id,
            ],
            [
                'numero_serie' => 'SN-HP-ELITE-001',
                'agency_id' => $siegeSocial->id,
                'localisation' => 'Bureau 201 - Comptabilité',
                'type' => 'informatique',
                'categorie_id' => $postesUtilisateurs->id,
                'nom' => 'EliteBook 840 G8',
                'modele' => 'EliteBook 840 G8',
                'marque' => 'HP',
                'date_livraison' => Carbon::parse('2024-02-10'),
                'prix' => 950000,
                'etat' => 'neuf',
                'statut' => 'parc',
                'departement' => 'Comptabilité',
                'poste_staff' => 'Chef Comptable',
                'date_mise_service' => Carbon::parse('2024-02-15'),
                'fournisseur_id' => $hp->id,
            ],
            
            // Périphériques
            [
                'numero_serie' => 'SN-HP-PRINT-001',
                'agency_id' => $siegeSocial->id,
                'localisation' => 'Salle reprographie',
                'type' => 'informatique',
                'categorie_id' => $peripheriques->id,
                'nom' => 'LaserJet Pro MFP',
                'modele' => 'MFP M428fdw',
                'marque' => 'HP',
                'date_livraison' => Carbon::parse('2024-03-05'),
                'prix' => 650000,
                'etat' => 'neuf',
                'statut' => 'parc',
                'departement' => 'Administration',
                'date_mise_service' => Carbon::parse('2024-03-10'),
                'fournisseur_id' => $hp->id,
            ],
            
            // Serveurs & Stockage
            [
                'numero_serie' => 'SN-DELL-SRV-001',
                'agency_id' => $siegeSocial->id,
                'localisation' => 'Salle serveurs',
                'type' => 'informatique',
                'categorie_id' => $serveursStockage->id,
                'nom' => 'PowerEdge R740',
                'modele' => 'R740XD',
                'marque' => 'Dell',
                'date_livraison' => Carbon::parse('2023-11-20'),
                'prix' => 8500000,
                'etat' => 'neuf',
                'statut' => 'parc',
                'departement' => 'IT',
                'poste_staff' => 'Administrateur Système',
                'date_mise_service' => Carbon::parse('2023-12-01'),
                'fournisseur_id' => $dell->id,
            ],
            
            // ============ reseau ============
            // Connectivité & Transmission
            [
                'numero_serie' => 'SN-CISCO-SW-001',
                'agency_id' => $siegeSocial->id,
                'localisation' => 'Rack réseau principal',
                'type' => 'reseau',
                'categorie_id' => $connectivite->id,
                'nom' => 'Catalyst 9300',
                'modele' => 'C9300-48P',
                'marque' => 'Cisco',
                'date_livraison' => Carbon::parse('2023-10-15'),
                'prix' => 3200000,
                'etat' => 'neuf',
                'statut' => 'parc',
                'departement' => 'IT',
                'date_mise_service' => Carbon::parse('2023-10-20'),
                'fournisseur_id' => $cisco->id,
            ],
            [
                'numero_serie' => 'SN-UBIQUITI-AP-001',
                'agency_id' => $agenceMermoz->id,
                'localisation' => 'Plafond réception',
                'type' => 'reseau',
                'categorie_id' => $connectivite->id,
                'nom' => 'UniFi AP AC Pro',
                'modele' => 'UAP-AC-PRO',
                'marque' => 'Ubiquiti',
                'date_livraison' => Carbon::parse('2024-04-10'),
                'prix' => 250000,
                'etat' => 'neuf',
                'statut' => 'parc',
                'departement' => 'IT',
                'date_mise_service' => Carbon::parse('2024-04-15'),
                'fournisseur_id' => $ubiquiti->id,
            ],
            
            // Sécurité Réseau
            [
                'numero_serie' => 'SN-FORTINET-FW-001',
                'agency_id' => $siegeSocial->id,
                'localisation' => 'Rack DMZ',
                'type' => 'reseau',
                'categorie_id' => $securiteReseau->id,
                'nom' => 'FortiGate 100F',
                'modele' => 'FG-100F',
                'marque' => 'Fortinet',
                'date_livraison' => Carbon::parse('2023-12-05'),
                'prix' => 4500000,
                'etat' => 'neuf',
                'statut' => 'parc',
                'departement' => 'IT',
                'date_mise_service' => Carbon::parse('2023-12-10'),
                'fournisseur_id' => $fortinet->id,
            ],
            
            // Infrastructure & Support
            [
                'numero_serie' => 'SN-SONATEL-RACK-001',
                'agency_id' => $siegeSocial->id,
                'localisation' => 'Salle réseau',
                'type' => 'reseau',
                'categorie_id' => $infrastructure->id,
                'nom' => 'Baie réseau 42U',
                'modele' => 'Rack-42U-600',
                'marque' => 'Sonatel',
                'date_livraison' => Carbon::parse('2023-09-10'),
                'prix' => 1200000,
                'etat' => 'neuf',
                'statut' => 'parc',
                'departement' => 'IT',
                'date_mise_service' => Carbon::parse('2023-09-15'),
                'fournisseur_id' => $sonatel->id,
            ],
            
            // ============ ÉLECTRONIQUE ============
            // Vidéosurveillance (CCTV)
            [
                'numero_serie' => 'SN-HIKVISION-CAM-001',
                'agency_id' => $siegeSocial->id,
                'localisation' => 'Entrée principale',
                'type' => 'électronique',
                'categorie_id' => $videoSurveillance->id,
                'nom' => 'Caméra IP Dome',
                'modele' => 'DS-2CD2143G0-I',
                'marque' => 'Hikvision',
                'date_livraison' => Carbon::parse('2024-01-25'),
                'prix' => 180000,
                'etat' => 'neuf',
                'statut' => 'parc',
                'departement' => 'Sécurité',
                'date_mise_service' => Carbon::parse('2024-02-01'),
                'fournisseur_id' => $hikvision->id,
            ],
            [
                'numero_serie' => 'SN-AXIS-CAM-001',
                'agency_id' => $agenceMermoz->id,
                'localisation' => 'Parking',
                'type' => 'électronique',
                'categorie_id' => $videoSurveillance->id,
                'nom' => 'Caméra PTZ',
                'modele' => 'AXIS Q6128-LE',
                'marque' => 'Axis',
                'date_livraison' => Carbon::parse('2024-03-15'),
                'prix' => 420000,
                'etat' => 'neuf',
                'statut' => 'parc',
                'departement' => 'Sécurité',
                'date_mise_service' => Carbon::parse('2024-03-20'),
                'fournisseur_id' => $axis->id,
            ],
            
            // Contrôle d'accès
            [
                'numero_serie' => 'SN-BOSCH-ACC-001',
                'agency_id' => $siegeSocial->id,
                'localisation' => 'Porte entrée staff',
                'type' => 'électronique',
                'categorie_id' => $controleAcces->id,
                'nom' => 'Contrôleur d\'accès',
                'modele' => 'B426',
                'marque' => 'Bosch',
                'date_livraison' => Carbon::parse('2024-02-20'),
                'prix' => 350000,
                'etat' => 'neuf',
                'statut' => 'parc',
                'departement' => 'Sécurité',
                'date_mise_service' => Carbon::parse('2024-02-25'),
                'fournisseur_id' => $bosch->id,
            ],
            
            // Systèmes d'alarme
            [
                'numero_serie' => 'SN-BOSCH-ALM-001',
                'agency_id' => $agenceGrandDakar->id,
                'localisation' => 'Local archives',
                'type' => 'électronique',
                'categorie_id' => $systemesAlarme->id,
                'nom' => 'Système d\'alarme intrusion',
                'modele' => 'B9512G',
                'marque' => 'Bosch',
                'date_livraison' => Carbon::parse('2024-04-05'),
                'prix' => 280000,
                'etat' => 'neuf',
                'statut' => 'parc',
                'departement' => 'Sécurité',
                'date_mise_service' => Carbon::parse('2024-04-10'),
                'fournisseur_id' => $bosch->id,
            ],
        ];
        
        foreach ($equipments as $equipment) {
            Equipment::create($equipment);
        }
        
        $this->command->info('✅ ' . count($equipments) . ' équipements créés avec succès!');
        $this->command->info('- Informatique: 4 équipements');
        $this->command->info('- reseau: 4 équipements');
        $this->command->info('- Électronique: 4 équipements');
    }
}