<?php

// database/migrations/2025_12_16_000002_create_equipment_reseau_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('equipment_reseau', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->unique()->constrained('equipment')->onDelete('cascade');
            $table->enum('type_reseau', ['switch', 'routeur', 'firewall', 'point_acces', 'modem', 'convertisseur_fibre', 'autre']);
            $table->enum('etat_reseau', ['en_stock', 'dote', 'mise_en_rebus']);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('equipment_reseau');
    }
};
