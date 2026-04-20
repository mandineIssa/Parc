<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('control_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('control_id')->constrained('controls')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected', 'need_complement'])->default('pending');
            $table->enum('conformity', ['conforme', 'non_conforme', 'en_attente'])->nullable();
            $table->enum('criticality', ['mineur', 'majeur', 'critique'])->nullable();
            $table->text('comment')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('due_date');
            $table->datetime('completed_at')->nullable();
            $table->datetime('validated_at')->nullable();
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['assigned_to', 'status']);
            $table->index('due_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_tasks');
    }
};