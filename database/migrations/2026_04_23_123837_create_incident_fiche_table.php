<?php
// database/migrations/2024_01_01_000001_create_incident_fiches_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_fiches', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            
            // Type et classification
            $table->enum('type', ['logiciel', 'materiel', 'reseau_telecom', 'application', 'infrastructure']);
            $table->string('application_concernee')->nullable();
            $table->string('environnement')->default('Production');
            $table->string('niveau_criticite')->default('P2'); // P1, P2, P3, P4
            
            // Déclaration
            $table->string('utilisateur');
            $table->string('entite');
            $table->string('fonction');
            $table->enum('point_entree', ['telephone', 'mail', 'application', 'itsm', 'hotline']);
            $table->date('date_incident');
            $table->time('heure_incident')->nullable();
            $table->time('heure_debut')->nullable();
            $table->time('heure_resolution')->nullable();
            $table->string('duree_incident')->nullable();
            $table->string('sujet');
            $table->boolean('bloquant')->default(false);
            $table->boolean('reproductible')->default(false);
            $table->text('description');
            
            // Impact métier
            $table->string('service_impacte')->nullable();
            $table->integer('nb_clients_impactes')->nullable();
            $table->integer('nb_utilisateurs_impactes')->nullable();
            $table->text('impact_metier')->nullable();
            
            // Analyse
            $table->text('cause_racine')->nullable();
            $table->text('analyse_initiale')->nullable();
            
            // Chronologie (JSON)
            $table->json('chronologie')->nullable();
            
            // Actions (JSON)
            $table->json('actions_correctives')->nullable();
            $table->json('actions_preventives')->nullable();
            
            // SLA
            $table->boolean('sla_respecte')->default(true);
            $table->string('temps_resolution')->nullable();
            $table->text('commentaires_cloture')->nullable();

            // Workflow
            $table->enum('statut', [
                'brouillon', 'soumis', 'en_cours_n2', 'en_cours_n3', 'cloture', 'rejete'
            ])->default('brouillon');

            // N+1 (Helpdesk)
            $table->unsignedBigInteger('n1_user_id')->nullable();
            $table->text('n1_description_traitement')->nullable();
            $table->text('n1_solutions_envisagees')->nullable();
            $table->enum('n1_statut', ['en_attente', 'cloture', 'transfere'])->nullable();
            $table->text('n1_autres_intervenants')->nullable();
            $table->dateTime('n1_date_traitement')->nullable();
            $table->string('n1_pdf_path')->nullable();

            // N+2 (Support niveau 2)
            $table->unsignedBigInteger('n2_user_id')->nullable();
            $table->text('n2_description_traitement')->nullable();
            $table->text('n2_solutions_envisagees')->nullable();
            $table->enum('n2_statut', ['en_attente', 'cloture', 'ouverture_ticket'])->nullable();
            $table->text('n2_autres_intervenants')->nullable();
            $table->dateTime('n2_date_traitement')->nullable();
            $table->string('n2_pdf_path')->nullable();

            // N+3 (Traitement du problème)
            $table->unsignedBigInteger('n3_user_id')->nullable();
            $table->text('n3_description_traitement')->nullable();
            $table->text('n3_solutions_envisagees')->nullable();
            $table->enum('n3_statut', ['en_attente', 'cloture', 'escalade'])->nullable();
            $table->text('n3_autres_intervenants')->nullable();
            $table->dateTime('n3_date_traitement')->nullable();
            $table->string('n3_pdf_path')->nullable();

            // Validation finale
            $table->unsignedBigInteger('valide_par')->nullable();
            $table->dateTime('date_cloture')->nullable();

            // PDF final généré
            $table->string('pdf_fiche_path')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('n1_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('n2_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('n3_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('valide_par')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('incident_historiques', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incident_fiche_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->text('commentaire')->nullable();
            $table->string('niveau')->nullable();
            $table->timestamps();

            $table->foreign('incident_fiche_id')->references('id')->on('incident_fiches')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_historiques');
        Schema::dropIfExists('incident_fiches');
    }
};