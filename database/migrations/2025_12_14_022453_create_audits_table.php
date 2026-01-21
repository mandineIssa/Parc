<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // create, update, delete, transition
            $table->string('model_type'); // Equipment, Stock, Parc, etc.
            $table->unsignedBigInteger('model_id');
            $table->json('old_data')->nullable(); // Données avant modification
            $table->json('new_data')->nullable(); // Données après modification
            $table->json('changes')->nullable(); // Différences seulement
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('transition_type')->nullable(); // Pour les transitions: stock→parc, etc.
            $table->text('notes')->nullable(); // Notes supplémentaires
            $table->timestamps();
            
            // Index pour les recherches
            $table->index(['model_type', 'model_id']);
            $table->index('action');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};