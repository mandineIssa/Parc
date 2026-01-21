<?php
// database/migrations/xxxx_xx_xx_create_approvals_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Informations de transition
            $table->string('from_status');
            $table->string('to_status');
            
            // Données de la demande
            $table->json('request_data'); // Stocke les informations de la demande
            
            // Gestion de l'approbation
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            // Raisons et notes
            $table->text('rejection_reason')->nullable();
            $table->text('validation_notes')->nullable();
            
            // Métadonnées
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('status');
            $table->index('approved_at');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('approvals');
    }
};