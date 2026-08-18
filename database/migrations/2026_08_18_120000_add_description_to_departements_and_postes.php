<?php

use App\Models\Departement;
use App\Models\PosteOrganisation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departements', function (Blueprint $table) {
            if (! Schema::hasColumn('departements', 'description')) {
                $table->string('description')->nullable()->after('nom');
            }
        });

        Schema::table('postes_organisation', function (Blueprint $table) {
            if (! Schema::hasColumn('postes_organisation', 'description')) {
                $table->string('description')->nullable()->after('nom');
            }
        });

        Departement::query()->whereNull('description')->orWhere('description', '')->each(function (Departement $departement) {
            $departement->description = 'Direction '.mb_strtolower($departement->nom);
            $departement->saveQuietly();
        });

        PosteOrganisation::query()->whereNull('description')->orWhere('description', '')->each(function (PosteOrganisation $poste) {
            $poste->description = $poste->nom;
            $poste->saveQuietly();
        });
    }

    public function down(): void
    {
        Schema::table('departements', function (Blueprint $table) {
            if (Schema::hasColumn('departements', 'description')) {
                $table->dropColumn('description');
            }
        });

        Schema::table('postes_organisation', function (Blueprint $table) {
            if (Schema::hasColumn('postes_organisation', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
