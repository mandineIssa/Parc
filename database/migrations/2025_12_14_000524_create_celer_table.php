<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
// database/migrations/xxxx_xx_xx_create_celer_table.php
Schema::create('celer', function (Blueprint $table) {
    $table->id();
    $table->foreignId('stock_id')->constrained('stock')->onDelete('cascade');
    $table->date('date_acquisition');
    $table->string('numero_facture')->nullable();
    $table->string('certificat_garantie')->nullable();
    $table->boolean('emballage_origine')->default(true);
    $table->text('caracteristiques_specifiques')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('celer');
    }
};
