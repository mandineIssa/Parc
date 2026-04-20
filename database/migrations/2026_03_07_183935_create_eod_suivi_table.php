<?php
// database/migrations/2026_03_07_000001_create_eod_suivi_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('eod_suivi', function (Blueprint $table) {
            $table->id();
            
            // Relations avec les utilisateurs
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('validated_by')->nullable();
            
            $table->string('reference')->unique();
            $table->string('status')->default('DRAFT'); // DRAFT, PENDING_N2, VALIDATED, REJECTED
            
            // En-tête
            $table->date('date_traitement');
            $table->string('institution')->default('COFINA');
            $table->string('systeme')->default('Oracle FLEXCUBE Core Banking');
            $table->string('heure_lancement')->default('22h00');
            $table->string('heure_fin')->nullable();
            $table->string('statut_global')->nullable();
            $table->string('responsable_suivi')->nullable();
            
            // 1. Sauvegarde - Avant traitement
            $table->string('sauvegarde_avant_incremental')->nullable();
            $table->string('sauvegarde_avant_differentiel')->nullable();
            $table->string('sauvegarde_avant_complet')->nullable();
            $table->string('sauvegarde_avant_heure')->nullable();
            $table->text('sauvegarde_avant_observation')->nullable();
            
            // 1. Sauvegarde - Après traitement
            $table->string('sauvegarde_apres_incremental')->nullable();
            $table->string('sauvegarde_apres_differentiel')->nullable();
            $table->string('sauvegarde_apres_complet')->nullable();
            $table->string('sauvegarde_apres_heure')->nullable();
            $table->text('sauvegarde_apres_observation')->nullable();
            
            // NAFA-BD
            $table->string('nafa_bd_avant_incremental')->nullable();
            $table->string('nafa_bd_avant_differentiel')->nullable();
            $table->string('nafa_bd_avant_complet')->nullable();
            $table->string('nafa_bd_apres_incremental')->nullable();
            $table->string('nafa_bd_apres_differentiel')->nullable();
            $table->string('nafa_bd_apres_complet')->nullable();
            $table->string('nafa_bd_heure')->nullable();
            $table->text('nafa_bd_observation')->nullable();
            
            // 2. Traitement - Batch (JSON pour stocker plusieurs lignes)
            $table->json('batch_data')->nullable();
            
            // 3. Émargement
            $table->text('emargement')->nullable();
            $table->string('responsable_batch')->nullable();
            
            // 4. Incidents (JSON pour stocker plusieurs incidents)
            $table->json('incidents_data')->nullable();
            
            // 5. Validation
            $table->datetime('validated_at')->nullable();
            $table->text('validation_note')->nullable();
            $table->string('validation_head_it_date')->nullable();
            $table->string('validation_head_it_visa')->nullable();
            $table->string('validation_audit_date')->nullable();
            $table->string('validation_audit_visa')->nullable();
            
            $table->json('history')->nullable();
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('eod_suivi');
    }
};