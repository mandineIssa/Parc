<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;

class AgencySeeder extends Seeder
{
    public function run(): void
    {
        $agencies = [
            [
                'nom' => 'Siège Social', 
                'ville' => 'Dakar', 
                'adresse' => 'Plateau, Immeuble Alpha',
                'telephone' => '+221 33 821 00 00',
                'email' => 'siege@entreprise.sn'
            ],
            [
                'nom' => 'Agence Mermoz', 
                'ville' => 'Dakar', 
                'adresse' => 'Rue de Mermoz, Villa 12',
                'telephone' => '+221 33 821 01 01',
                'email' => 'mermoz@entreprise.sn'
            ],
            [
                'nom' => 'Agence Grand Dakar', 
                'ville' => 'Dakar', 
                'adresse' => 'Grand Dakar, Cité Keur Gorgui',
                'telephone' => '+221 33 821 02 02',
                'email' => 'granddakar@entreprise.sn'
            ],
            [
                'nom' => 'Agence Thiès', 
                'ville' => 'Thiès', 
                'adresse' => 'Route de Khombole',
                'telephone' => '+221 33 821 03 03',
                'email' => 'thies@entreprise.sn'
            ],
            [
                'nom' => 'Agence Saint-Louis', 
                'ville' => 'Saint-Louis', 
                'adresse' => 'Avenue Jean Mermoz',
                'telephone' => '+221 33 821 04 04',
                'email' => 'saintlouis@entreprise.sn'
            ],
        ];
        
        foreach ($agencies as $agency) {
            Agency::create($agency);
        }
        
        $this->command->info('✅ ' . count($agencies) . ' agences créées avec succès!');
    }
}