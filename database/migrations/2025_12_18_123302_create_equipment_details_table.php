<?php
// database/migrations/xxxx_xx_xx_create_equipment_details_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            
            // Classification dynamique (texte libre)
            $table->string('categorie')->nullable(); // ex: "Postes Utilisateurs"
            $table->string('sous_categorie')->nullable(); // ex: "Ordinateurs portables"
            
            // Champs spécifiques optionnels (selon le type)
            $table->string('etat_specifique')->nullable(); // Pour etat_reseau, etat_stock, etc.
            $table->string('adresse_ip_specifique')->nullable();
            $table->string('adresse_mac_specifique')->nullable();
            $table->string('departement_specifique')->nullable();
            $table->string('poste_staff_specifique')->nullable();
            $table->string('numero_codification_specifique')->nullable();
            
            // Contrat maintenance
            $table->boolean('contrat_maintenance')->default(false);
            $table->string('type_contrat')->nullable();
            $table->date('date_debut_contrat')->nullable();
            $table->date('date_fin_contrat')->nullable();
            $table->string('periodicite_maintenance')->nullable();
            
            // Données spécifiques en JSON (pour tous les champs dynamiques)
            $table->json('specific_data')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index('equipment_id');
            $table->index(['categorie', 'sous_categorie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_details');
    }
};