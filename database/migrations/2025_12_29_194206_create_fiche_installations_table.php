<?php
// database/migrations/2024_01_01_create_fiche_installations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fiche_installations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_id')->constrained('approvals')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Informations de base
            $table->date('date_application');
            $table->string('numero_fiche')->unique();
            $table->string('agence_nom');
            
            // Section INSTALLATION
            $table->date('date_installation');
            
            // Prérequis (stockés en JSON)
            $table->json('prerequis')->nullable();
            
            // Logiciels installés (stockés en JSON)
            $table->json('logiciels_installes')->nullable();
            
            // Raccourcis (stockés en JSON)
            $table->json('raccourcis')->nullable();
            
            // Autres configurations (stockés en JSON)
            $table->json('autres_configurations')->nullable();
            
            // Installateur
            $table->string('installateur_nom');
            $table->string('installateur_prenom');
            $table->string('installateur_fonction');
            
            // Section VÉRIFICATION
            $table->date('date_verification');
            
            // Vérifications (stockées en JSON)
            $table->json('verifications')->nullable();
            
            // Autres vérifications (stockées en JSON)
            $table->json('autres_verifications')->nullable();
            
            // Vérificateur
            $table->string('verificateur_nom')->nullable();
            $table->string('verificateur_prenom')->nullable();
            $table->string('verificateur_fonction')->nullable();
            
            // Signatures (chemins des fichiers)
            $table->string('signature_installateur_path')->nullable();
            $table->string('signature_utilisateur_path')->nullable();
            $table->string('signature_verificateur_path')->nullable();
            
            // Statut
            $table->enum('status', [
                'draft', 
                'en_cours', 
                'installe', 
                'verifie', 
                'complet', 
                'archived'
            ])->default('draft');
            
            // Métadonnées
            $table->text('observations')->nullable();
            $table->json('checklist_complete')->nullable(); // Pour suivre les cases cochées
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('numero_fiche');
            $table->index('date_application');
            $table->index('agence_nom');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fiche_installations');
    }
};