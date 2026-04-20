<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('control_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('review_type', [
                'controle_premier_niveau',
                'maintenance_preventive',
                'inventaire_parc',
                'revue_pca',
                'schema_directeur',
                'politique_securite'
            ]);
            $table->enum('frequency', [
                'quotidienne', 
                'hebdomadaire', 
                'mensuelle', 
                'trimestrielle', 
                'semestrielle', 
                'annuelle'
            ]);
            $table->text('description')->nullable();
            $table->json('checklist')->nullable();
            $table->json('questions')->nullable();
            $table->json('required_attachments')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_templates');
    }
};