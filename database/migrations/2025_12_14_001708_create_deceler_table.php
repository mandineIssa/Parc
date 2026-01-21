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
        Schema::create('deceler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('stock')->onDelete('cascade');
            $table->enum('origine', ['parc', 'maintenance']);
            $table->string('numero_serie_origine')->nullable(); // Ajoutez ->nullable()
            $table->date('date_retour');
            $table->enum('raison_retour', ['renouvellement', 'panne', 'optimisation', 'autre']);
            $table->text('diagnostic')->nullable();
            $table->enum('etat_retour', ['bon', 'reparable', 'irreparable']);
            $table->decimal('valeur_residuelle', 10, 2)->nullable();
            $table->text('observations_retour')->nullable();
            $table->timestamps();
            
            $table->foreign('numero_serie_origine')->references('numero_serie')->on('equipment')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deceler');
    }
};