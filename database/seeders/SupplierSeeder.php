<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Vider la table d'abord
        Supplier::query()->delete();
        
        $suppliers = [
            [
                'nom' => 'Cisco Systems Sénégal',
                'contact' => 'M. Ahmed Diallo',
                'email' => 'senegal@cisco.com',
                'telephone' => '+221 33 849 10 00',
                'adresse' => 'Immeuble Karamokho, Almadies',
                'ville' => 'Dakar',
                'website' => 'https://www.cisco.com',
                'status' => 'active',
                'notes' => 'Fournisseur officiel Cisco pour le Sénégal'
            ],
            [
                'nom' => 'Dell Technologies Sénégal',
                'contact' => 'Mme Aïssatou Ndiaye',
                'email' => 'senegal@dell.com',
                'telephone' => '+221 33 849 20 00',
                'adresse' => 'Route de l\'Aéroport',
                'ville' => 'Dakar',
                'website' => 'https://www.dell.com',
                'status' => 'active',
                'notes' => 'Distributeur certifié Dell'
            ],
            [
                'nom' => 'HP Africa',
                'contact' => 'M. Souleymane Sow',
                'email' => 'senegal@hp.com',
                'telephone' => '+221 33 849 30 00',
                'adresse' => 'Zone B, Sicap Liberté',
                'ville' => 'Dakar',
                'website' => 'https://www.hp.com',
                'status' => 'active',
                'notes' => 'Représentant HP pour l\'Afrique de l\'Ouest'
            ],
            [
                'nom' => 'Hikvision Sénégal',
                'contact' => 'M. Ibrahim Ba',
                'email' => 'senegal@hikvision.com',
                'telephone' => '+221 33 849 40 00',
                'adresse' => 'Sacré Coeur 3',
                'ville' => 'Dakar',
                'website' => 'https://www.hikvision.com',
                'status' => 'active',
                'notes' => 'Solutions de vidéosurveillance'
            ],
            [
                'nom' => 'Axis Communications',
                'contact' => 'M. Mamadou Fall',
                'email' => 'africa@axis.com',
                'telephone' => '+221 33 849 50 00',
                'adresse' => 'Point E',
                'ville' => 'Dakar',
                'website' => 'https://www.axis.com',
                'status' => 'active',
                'notes' => 'Caméras réseau IP'
            ],
            [
                'nom' => 'Bosch Security Systems',
                'contact' => 'Mme Fatou Diop',
                'email' => 'senegal@bosch.com',
                'telephone' => '+221 33 849 60 00',
                'adresse' => 'Mermoz',
                'ville' => 'Dakar',
                'website' => 'https://www.bosch.com',
                'status' => 'active',
                'notes' => 'Systèmes de sécurité et d\'alarme'
            ],
            [
                'nom' => 'Ubiquiti Networks',
                'contact' => 'M. Cheikh Tidiane',
                'email' => 'sales@ubnt-senegal.com',
                'telephone' => '+221 77 123 45 67',
                'adresse' => 'Ouakam',
                'ville' => 'Dakar',
                'website' => 'https://www.ui.com',
                'status' => 'active',
                'notes' => 'Équipements réseau sans fil'
            ],
            [
                'nom' => 'Sonatel Pro',
                'contact' => 'M. Babacar Ndiaye',
                'email' => 'pro@sonatel.sn',
                'telephone' => '+221 33 839 00 00',
                'adresse' => 'Siège Sonatel',
                'ville' => 'Dakar',
                'website' => 'https://www.sonatel.sn',
                'status' => 'active',
                'notes' => 'Services professionnels et équipements réseau'
            ],
            [
                'nom' => 'Orange Business Services',
                'contact' => 'Mme Khady Diouf',
                'email' => 'business@orange.sn',
                'telephone' => '+221 33 839 11 11',
                'adresse' => 'Plateau',
                'ville' => 'Dakar',
                'website' => 'https://www.orange.sn',
                'status' => 'active',
                'notes' => 'Services aux entreprises'
            ],
            [
                'nom' => 'Sagemcom Sénégal',
                'contact' => 'M. Abdoulaye Sy',
                'email' => 'senegal@sagemcom.com',
                'telephone' => '+221 33 849 70 00',
                'adresse' => 'Liberté 6',
                'ville' => 'Dakar',
                'website' => 'https://www.sagemcom.com',
                'status' => 'active',
                'notes' => 'Équipements de communication'
            ],
            [
                'nom' => 'Microsoft Sénégal',
                'contact' => 'M. Omar Gueye',
                'email' => 'senegal@microsoft.com',
                'telephone' => '+221 33 849 80 00',
                'adresse' => 'Diamniadio',
                'ville' => 'Dakar',
                'website' => 'https://www.microsoft.com',
                'status' => 'active',
                'notes' => 'Licences et solutions logicielles'
            ],
            [
                'nom' => 'Fortinet Sénégal',
                'contact' => 'M. Alioune Diop',
                'email' => 'senegal@fortinet.com',
                'telephone' => '+221 33 849 90 00',
                'adresse' => 'Almadies',
                'ville' => 'Dakar',
                'website' => 'https://www.fortinet.com',
                'status' => 'active',
                'notes' => 'Solutions de cybersécurité'
            ],
            [
                'nom' => 'Jumia Business',
                'contact' => 'Mme Aminata Mbaye',
                'email' => 'business@jumia.sn',
                'telephone' => '+221 33 869 00 00',
                'adresse' => 'SICAP Baobab',
                'ville' => 'Dakar',
                'website' => 'https://www.jumia.sn',
                'status' => 'active',
                'notes' => 'Marketplace pour équipements professionnels'
            ],
            [
                'nom' => 'Canon Sénégal',
                'contact' => 'M. Ibrahima Sarr',
                'email' => 'senegal@canon.com',
                'telephone' => '+221 33 849 25 00',
                'adresse' => 'Fann Résidence',
                'ville' => 'Dakar',
                'website' => 'https://www.canon-senegal.com',
                'status' => 'active',
                'notes' => 'Imprimantes et matériel d\'impression'
            ],
            [
                'nom' => 'Epson Sénégal',
                'contact' => 'M. Modou Diouf',
                'email' => 'senegal@epson.com',
                'telephone' => '+221 33 849 35 00',
                'adresse' => 'Mermoz',
                'ville' => 'Dakar',
                'website' => 'https://www.epson-senegal.com',
                'status' => 'pending',
                'notes' => 'Fournisseur en cours d\'agrément'
            ],
        ];
        
        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('✅ ' . count($suppliers) . ' fournisseurs créés avec succès!');
        $this->command->info('- Actifs: ' . collect($suppliers)->where('status', 'active')->count());
        $this->command->info('- En attente: ' . collect($suppliers)->where('status', 'pending')->count());
    }
}