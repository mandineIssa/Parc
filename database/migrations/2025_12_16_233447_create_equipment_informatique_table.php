<?php
// database/migrations/2025_12_16_000001_create_equipment_informatique_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('equipment_informatique', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->unique()->constrained('equipment')->onDelete('cascade');
            $table->enum('type_informatique', ['ordinateur', 'imprimante', 'scanner', 'serveur', 'autre']);
            $table->enum('etat_stock', ['en_stock', 'dote', 'en_rupture']);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('equipment_informatique');
    }
};