<?php
// database/migrations/xxxx_xx_xx_create_equipment_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('numero_serie')->unique();
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->nullOnDelete();
            $table->string('localisation');
            $table->enum('type', ['Réseau', 'Informatique', 'Électronique', 'Logiciel']); // Ajouter 'Logiciel'
            // Supprimez categorie_id car vous utiliserez equipment_details
             $table->foreignId('categorie_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('nom')->nullable();
            $table->string('modele');
            $table->string('marque');
            $table->string('numero_codification')->nullable();
            $table->string('adresse_mac')->nullable();
            $table->string('adresse_ip')->nullable();
            $table->foreignId('fournisseur_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->date('date_livraison');
            $table->decimal('prix', 10, 2);
            $table->string('garantie')->nullable();
            $table->string('reference_facture')->nullable();
            $table->string('reference_installation')->nullable();
            $table->enum('etat', ['neuf', 'bon', 'moyen', 'mauvais']);
            $table->string('lieu_stockage')->nullable();
            $table->text('notes')->nullable();
            $table->enum('statut', ['stock', 'parc', 'maintenance', 'hors_service', 'perdu'])->default('stock');
            $table->string('departement')->nullable();
            $table->string('poste_staff')->nullable();
            $table->date('date_mise_service')->nullable();
            $table->date('date_amortissement')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};