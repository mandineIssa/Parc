<?php
// database/migrations/2024_01_01_create_fiche_mouvements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fiche_mouvements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_id')->constrained('approvals')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Informations de base
            $table->date('date_application');
            $table->string('numero_fiche')->unique();
            
            // Expéditeur
            $table->string('expediteur_nom');
            $table->string('expediteur_prenom');
            $table->string('expediteur_fonction');
            
            // Réceptionnaire
            $table->string('receptionnaire_nom');
            $table->string('receptionnaire_prenom');
            $table->string('receptionnaire_fonction');
            
            // Détails du mouvement
            $table->string('type_materiel');
            $table->string('reference');
            $table->string('lieu_depart');
            $table->string('destination');
            $table->string('motif');
            
            // Signatures
            $table->date('date_expediteur');
            $table->date('date_receptionnaire');
            
            // Fichiers signatures (si digitalisées)
            $table->string('signature_expediteur_path')->nullable();
            $table->string('signature_receptionnaire_path')->nullable();
            
            // Statut
            $table->enum('status', ['draft', 'completed', 'archived'])->default('draft');
            
            // Métadonnées
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Pour stocker des données supplémentaires
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('numero_fiche');
            $table->index('date_application');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fiche_mouvements');
    }
};