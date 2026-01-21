<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@cofina.sn'], // clé unique
            [
                
                'name' => 'Super',
                'prenom' => 'Admin',
                'password' => Hash::make('Admin123!'),
                'role' => 'super_admin',
                'departement' => 'IT',
                'fonction' => 'Super Administrateur',
                'email_verified_at' => now(),
            ]
        );

        // Agent IT
        User::updateOrCreate(
            ['email' => 'agent.it@cofina.sn'],
            [
                'name' => 'Agent',
                'prenom' => 'IT',
                'password' => Hash::make('Agent123!'),
                'role' => 'agent_it',
                'departement' => 'IT',
                'fonction' => 'Agent Informatique',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super admin OK : superadmin@cofina.sn / Admin123!');
        $this->command->info('Agent IT OK : agent.it@cofina.sn / Agent123!');
    }
}
