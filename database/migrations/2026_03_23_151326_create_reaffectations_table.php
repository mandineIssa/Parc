<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reaffectations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');

            // Ancien utilisateur (snapshot avant réaffectation)
            $table->string('ancien_utilisateur_nom')->nullable();
            $table->string('ancien_utilisateur_prenom')->nullable();
            $table->string('ancien_departement')->nullable();
            $table->string('ancienne_localisation')->nullable();

            // Nouvel utilisateur
            $table->string('nouveau_utilisateur_nom');
            $table->string('nouveau_utilisateur_prenom')->nullable();
            $table->string('nouveau_departement')->nullable();
            $table->string('nouvelle_localisation')->nullable();

            // Métadonnées
            $table->date('date_reaffectation');
            $table->text('motif')->nullable();
            $table->foreignId('fait_par')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reaffectations');
    }
};