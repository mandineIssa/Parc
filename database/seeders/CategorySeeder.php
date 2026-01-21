<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // ============ TYPE : RÉSEAUX ============
            [
                'type' => 'réseaux',
                'nom' => 'Connectivité & Transmission',
                'description' => 'Équipements de connectivité et transmission réseau',
                'equipment_list' => [
                    'Switches (L2/L3)',
                    'Routeurs',
                    'Points d\'accès Wi-Fi / Contrôleurs Wi-Fi',
                    'Modems',
                    'Convertisseurs Fibre (SFP, GBIC, Media converter)'
                ]
            ],
            [
                'type' => 'réseaux',
                'nom' => 'Sécurité Réseau',
                'description' => 'Équipements de sécurité réseau et protection',
                'equipment_list' => [
                    'Pare-feu (Firewall)',
                    'UTM / Appliances de sécurité',
                    'Passerelles VPN',
                    'IPS/IDS (Systèmes de prévention/détection d\'intrusion)'
                ]
            ],
            [
                'type' => 'réseaux',
                'nom' => 'Infrastructure & Support',
                'description' => 'Infrastructure physique et support réseau',
                'equipment_list' => [
                    'Baies et armoires réseau',
                    'Panneaux de brassage',
                    'Câblage RJ45 / Fibre optique',
                    'Onduleurs (UPS)',
                    'PDU (Multiprises intelligentes)'
                ]
            ],
            
            // ============ TYPE : ÉLECTRONIQUE ============
            [
                'type' => 'électronique',
                'nom' => 'Vidéosurveillance (CCTV)',
                'description' => 'Systèmes de vidéosurveillance et sécurité vidéo',
                'equipment_list' => [
                    'Caméras IP (fixes, PTZ, dôme)',
                    'NVR / DVR (Enregistreurs vidéo)',
                    'Serveurs d\'archivage vidéo',
                    'Moniteurs de contrôle',
                    'Switchs PoE pour caméras'
                ]
            ],
            [
                'type' => 'électronique',
                'nom' => 'Contrôle d\'accès',
                'description' => 'Systèmes de contrôle et gestion des accès',
                'equipment_list' => [
                    'Badges / Lecteurs RFID',
                    'Serrures électroniques',
                    'Tourniquets / Portillons',
                    'Unités de contrôle et software',
                    'Contrôleurs d\'accès'
                ]
            ],
            [
                'type' => 'électronique',
                'nom' => 'Systèmes d\'alarme',
                'description' => 'Systèmes d\'alarme et détection d\'intrusion',
                'equipment_list' => [
                    'Alarmes anti-intrusion',
                    'Détecteurs de mouvement',
                    'Détecteurs d\'ouverture (porte/fenêtre)',
                    'Centrale d\'alarme',
                    'Sirènes et flashs'
                ]
            ],
            
            // ============ TYPE : INFORMATIQUES ============
            [
                'type' => 'informatiques',
                'nom' => 'Postes Utilisateurs',
                'description' => 'Équipements de postes de travail utilisateurs',
                'equipment_list' => [
                    'Ordinateurs de bureau (Tours, All-in-One)',
                    'Ordinateurs portables (Laptops, Notebooks)',
                    'Écrans (Moniteurs)',
                    'Claviers / Souris',
                    'Station d\'accueil pour portable'
                ]
            ],
            [
                'type' => 'informatiques',
                'nom' => 'Périphériques',
                'description' => 'Périphériques informatiques et bureautique',
                'equipment_list' => [
                    'Imprimantes (Laser, Jet d\'encre, Multifonctions)',
                    'Scanners (Bureautique, Production)',
                    'Onduleurs individuels (UPS bureau)',
                    'Projecteurs / Écrans interactifs',
                    'Copieurs numériques'
                ]
            ],
            [
                'type' => 'informatiques',
                'nom' => 'Serveurs & Stockage',
                'description' => 'Serveurs et solutions de stockage de données',
                'equipment_list' => [
                    'Serveurs physiques (rack, tour)',
                    'NAS / SAN (Stockage en réseau)',
                    'Baies de stockage (Storage arrays)',
                    'Solutions de backup (Tape library, disque dur externe)',
                    'Serveurs de virtualisation'
                ]
            ],
            [
                'type' => 'informatiques',
                'nom' => 'Matériel d\'Administration & Support',
                'description' => 'Matériel technique pour administration et support IT',
                'equipment_list' => [
                    'Outils de diagnostic réseau',
                    'KVM (Keyboard Video Mouse switch)',
                    'Barras de test (câblage RJ45, fibre)',
                    'Logiciels systèmes et outils métiers',
                    'Consoles de gestion et supervision'
                ]
            ],
        ];
        
        // Créer ou mettre à jour les catégories principales
        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['nom' => $categoryData['nom']],
                $categoryData
            );
        }
        
        $this->command->info('✅ Catégories créées/mises à jour avec succès!');
        $this->command->info('- Réseaux: 3 catégories');
        $this->command->info('- Électronique: 3 catégories');
        $this->command->info('- Informatiques: 4 catégories');
    }
}