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
// database/migrations/xxxx_xx_xx_create_maintenance_table.php
Schema::create('maintenance', function (Blueprint $table) {
    $table->id();
    $table->string('numero_serie');
    $table->date('date_depart');
    $table->date('date_retour_prevue');
    $table->date('date_retour_reelle')->nullable();
    $table->enum('type_maintenance', ['preventive', 'corrective', 'curative']);
    $table->string('prestataire');
    $table->decimal('cout', 10, 2)->nullable();
    $table->enum('statut', ['en_cours', 'termine', 'en_attente']);
    $table->text('description_panne');
    $table->text('travaux_realises')->nullable();
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
        Schema::dropIfExists('maintenance');
    }
};
