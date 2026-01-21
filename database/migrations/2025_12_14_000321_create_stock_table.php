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
        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->string('numero_serie')->unique();
            $table->enum('type_stock', ['celer', 'deceler']);
            $table->string('localisation_physique');
            $table->enum('etat', ['disponible', 'reserve', 'en_transit', 'sorti'])->default('disponible');
            $table->integer('quantite')->default(1);
            $table->date('date_entree');
            $table->date('date_sortie')->nullable();
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
        Schema::dropIfExists('stock');
    }
};