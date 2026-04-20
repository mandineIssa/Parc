<?php
// database/migrations/2024_01_01_000001_create_change_tickets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('change_tickets', function (Blueprint $table) {
            $table->id();
            
            // Relations avec les utilisateurs
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->string('ticket_id')->unique();
            $table->string('status')->default('DRAFT');
            $table->string('titre');
            $table->string('type')->default('Standard');
            $table->string('prenom');
            $table->string('nom');
            $table->string('departement');
            $table->date('date_execution')->nullable();
            $table->string('environnement');
            $table->text('problematique');
            $table->string('impact_ops')->nullable();
            $table->string('impact_users')->nullable();
            $table->string('impact_prod')->nullable();
            $table->text('risques')->nullable();
            $table->text('rollback')->nullable();
            
            // N+2 fields
            $table->text('recommandation')->nullable();
            $table->text('requete')->nullable();
            $table->datetime('date_exec_reelle')->nullable();
            $table->string('operateur')->nullable();
            $table->text('resultat')->nullable();
            $table->text('ecarts')->nullable();
            
            // N+3 fields
            $table->text('close_note')->nullable();
            $table->datetime('closed_at')->nullable();
            
            // Incident
            $table->string('incident_num')->nullable();
            $table->datetime('incident_opened_at')->nullable();
            $table->text('rejet_note')->nullable();
            
            $table->json('history')->nullable();
            $table->json('files')->nullable();
            $table->json('recomm_files')->nullable();
            $table->json('exec_files')->nullable();
            
            $table->timestamps();
            
            // Ajouter les clés étrangères
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('change_tickets');
    }
}