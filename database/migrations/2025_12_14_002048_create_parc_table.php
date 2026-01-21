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
// database/migrations/xxxx_xx_xx_create_parc_table.php
Schema::create('parc', function (Blueprint $table) {
    $table->id();
    $table->string('numero_serie')->unique();
    $table->foreignId('utilisateur_id')->nullable()->constrained('users')->onDelete('set null');
    $table->string('departement');
    $table->string('poste_affecte');
    $table->date('date_affectation');
    $table->date('date_retour_prevue')->nullable();
    $table->enum('statut_usage', ['actif', 'inactif', 'en_pret']);
    $table->text('notes_affectation')->nullable();
    $table->timestamps();
    
    $table->foreign('numero_serie')->references('numero_serie')->on('equipment')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parc');
    }
};
