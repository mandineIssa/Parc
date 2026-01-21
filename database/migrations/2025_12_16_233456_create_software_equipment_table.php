<?php

// database/migrations/2025_12_16_000005_create_software_equipment_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('software_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_id')->constrained('software')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->dateTime('date_installation')->nullable();
            $table->timestamps();
            
            $table->unique(['software_id', 'equipment_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('software_equipment');
    }
};
