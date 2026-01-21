<?php
// ============================================
// 4. SEEDER - database/seeders/SousCategorieSeeder.php
// ============================================

namespace Database\Seeders;

use App\Models\SousCategorie;
use App\Models\Category;
use Illuminate\Database\Seeder;

class SousCategorieSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Équipements réseau
            'Équipements réseau' => [
                'Switches (L2/L3)',
                'Routeurs',
                'Points d\'accès Wi-Fi / Contrôleurs Wi-Fi',
                'Modems',
                'Convertisseurs Fibre (SFP, GBIC, Media converter)',
            ],
            // Sécurité réseau
            'Sécurité réseau' => [
                'Pare-feu (Firewall)',
                'UTM / Appliances de sécurité',
                'Passerelles VPN',
                'IPS/IDS',
            ],
            // Infrastructure réseau
            'Infrastructure réseau' => [
                'Baies et armoires réseau',
                'Panneaux de brassage',
                'Câblage RJ45 / Fibre optique',
                'Onduleurs (UPS)',
                'PDU (Multiprises intelligentes)',
            ],
            // Vidéosurveillance
            'Vidéosurveillance' => [
                'Caméras IP (fixes, PTZ, dôme)',
                'NVR / DVR',
                'Serveurs d\'archivage vidéos',
                'Moniteurs de contrôle',
            ],
            // Contrôle d'accès
            'Contrôle d\'accès' => [
                'Badges / Lecteurs RFID',
                'Serrures électroniques',
                'Tournquets / Portillons',
                'Unités de contrôle et software',
            ],
            // Systèmes d'alarme
            'Systèmes d\'alarme' => [
                'Alarmes anti-intrusion',
                'Détecteurs de mouvement',
                'Détecteurs d\'ouverture',
                'Centrale d\'alarme',
            ],
            // Postes de travail
            'Postes de travail' => [
                'Ordinateurs de bureau',
                'Ordinateurs portables',
                'Écrans',
                'Claviers / Souris',
            ],
            // Périphériques
            'Périphériques' => [
                'Imprimantes',
                'Scanners',
                'Onduleurs individuels',
                'Projecteurs / Écrans interactifs',
            ],
            // Stockage et serveurs
            'Stockage et serveurs' => [
                'Serveurs physiques (rack, tour)',
                'NAS / SAN',
                'Baies de stockage',
                'Solutions de backup (Tape library, disque dur externe)',
            ],
            // Outils et logiciels
            'Outils et logiciels' => [
                'Outils de diagnostic',
                'KVM (Keyboard Video Mouse)',
                'Barras de test (câblage)',
                'Logiciels systèmes et outils métiers',
            ],
        ];

        foreach ($data as $categorieName => $sousCategories) {
            // Récupérer ou créer la catégorie
            $categorie = Category::where('nom', $categorieName)->first();
            
            if (!$categorie) {
                $categorie = Category::create([
                    'nom' => $categorieName,
                    'description' => 'Catégorie: ' . $categorieName,
                ]);
            }

            // Créer les sous-catégories
            foreach ($sousCategories as $sousCategoryName) {
                SousCategorie::firstOrCreate(
                    ['nom' => $sousCategoryName, 'categorie_id' => $categorie->id],
                    [
                        'description' => 'Sous-catégorie: ' . $sousCategoryName,
                    ]
                );
            }
        }
    }
}