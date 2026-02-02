<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deceler', function (Blueprint $table) {
            // Modifier la colonne raison_retour en TEXT pour plus de capacité
            $table->text('raison_retour')->nullable()->change();
            
            // Optionnel : modifier aussi les autres colonnes de texte
            $table->text('diagnostic')->nullable()->change();
            $table->text('observations_retour')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('deceler', function (Blueprint $table) {
            // Revenir à VARCHAR(255) si nécessaire
            $table->string('raison_retour', 255)->nullable()->change();
            $table->string('diagnostic', 500)->nullable()->change();
            $table->string('observations_retour', 500)->nullable()->change();
        });
    }
};