<?php

// database/migrations/2025_12_16_000003_create_equipment_electronique_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('equipment_electronique', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->unique()->constrained('equipment')->onDelete('cascade');
            $table->enum('type_electronique', ['camera', 'dvr_nvr', 'controle_acces', 'detecteur_incendie', 'detecteur_presence', 'extincteur', 'autre']);
            $table->enum('etat_electronique', ['en_stock', 'dote', 'mise_en_rebus']);
            
            // Vérification technique
            $table->dateTime('derniere_verification_technique')->nullable();
            
            // Contrat maintenance
            $table->boolean('contrat_maintenance')->default(false);
            $table->enum('type_contrat', ['maintenance', 'licence', 'garantie', 'support'])->nullable();
            $table->date('date_debut_contrat')->nullable();
            $table->date('date_fin_contrat')->nullable();
            $table->enum('periodicite_maintenance', ['mensuelle', 'trimestrielle', 'semestrielle', 'annuelle'])->nullable();
            $table->dateTime('derniere_maintenance')->nullable();
            $table->dateTime('prochaine_maintenance')->nullable();
            
            // Conformité
            $table->boolean('conforme_normes_securite')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('equipment_electronique');
    }
};
