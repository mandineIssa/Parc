<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transition_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
            $table->string('from_status', 50);
            $table->string('to_status', 50);
            $table->string('type', 100);
            $table->foreignId('submitted_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('rejected_by')->nullable()->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->json('data')->nullable();
            $table->json('generated_files')->nullable();
            $table->json('checklist_data')->nullable();
            $table->string('super_admin_signature')->nullable();
            $table->text('validation_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['equipment_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transition_approvals');
    }
};