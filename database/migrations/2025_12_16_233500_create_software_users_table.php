<?php

// database/migrations/2025_12_16_000006_create_software_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('software_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_id')->constrained('software')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('date_affectation')->nullable();
            $table->timestamps();
            
            $table->unique(['software_id', 'user_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('software_users');
    }
};