<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['réseaux', 'électronique', 'informatiques'])->nullable();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable(); // Pour les sous-catégories
            $table->json('equipment_list')->nullable(); // Liste des équipements typiques
            $table->timestamps();
            $table->softDeletes();
            
            // Clé étrangère pour les sous-catégories
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            
            // Index pour améliorer les performances
            $table->index('type');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};