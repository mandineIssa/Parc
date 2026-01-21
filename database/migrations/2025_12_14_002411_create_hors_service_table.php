<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
// database/migrations/xxxx_xx_xx_create_hors_service_table.php
Schema::create('hors_service', function (Blueprint $table) {
    $table->id();
    $table->string('numero_serie')->unique();
    $table->date('date_hors_service');
    $table->enum('raison', ['panne_irreparable', 'obsolescence', 'accident', 'vol', 'autre']);
    $table->text('description_incident');
    $table->enum('destinataire', ['reforme', 'destruction', 'don', 'vente']);
    $table->date('date_traitement')->nullable();
    $table->decimal('valeur_residuelle', 10, 2)->nullable();
    $table->text('justificatif')->nullable();
    $table->text('observations')->nullable();
    $table->timestamps();
    
    $table->foreign('numero_serie')->references('numero_serie')->on('equipment')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hors_service');
    }
};
