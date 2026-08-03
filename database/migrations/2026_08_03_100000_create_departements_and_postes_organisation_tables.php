<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departements', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->boolean('actif')->default(true);
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();
        });

        Schema::create('postes_organisation', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->boolean('actif')->default(true);
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();
        });

        $departements = [
            'IT', 'Ressources Humaines', 'Comptabilité', 'Finance', 'Marketing',
            'Ventes', 'Direction', 'Opérations', 'Commercial', 'Administratif', 'Autre',
        ];
        foreach ($departements as $i => $nom) {
            DB::table('departements')->insert([
                'nom' => $nom,
                'actif' => true,
                'ordre' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $postes = [
            'Directeur', 'Manager', 'Chef de Projet', 'Technicien', 'Développeur',
            'Analyste', 'Consultant', 'Administrateur', 'Assistant', 'Agent',
            'Stagiaire', 'CC', 'RH', 'Finance', 'Caissier', 'Recouvrement',
            'Juridique', 'CAF', 'Logistique', 'Marketing', 'Autre',
        ];
        foreach ($postes as $i => $nom) {
            DB::table('postes_organisation')->insert([
                'nom' => $nom,
                'actif' => true,
                'ordre' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('postes_organisation');
        Schema::dropIfExists('departements');
    }
};
