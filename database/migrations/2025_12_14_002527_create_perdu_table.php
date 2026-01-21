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
// database/migrations/xxxx_xx_xx_create_perdu_table.php
Schema::create('perdu', function (Blueprint $table) {
    $table->id();
    $table->string('numero_serie')->unique();
    $table->date('date_disparition');
    $table->string('lieu_disparition');
    $table->enum('type_disparition', ['vol', 'perte', 'non_localise']);
    $table->text('circonstances');
    $table->boolean('plainte_deposee')->default(false);
    $table->string('numero_plainte')->nullable();
    $table->decimal('valeur_assuree', 10, 2)->nullable();
    $table->enum('statut_recherche', ['en_cours', 'cloture', 'retrouve']);
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
        Schema::dropIfExists('perdu');
    }
};
