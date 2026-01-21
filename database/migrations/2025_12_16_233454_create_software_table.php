<?php

// database/migrations/2025_12_16_000004_create_software_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('software', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained('agencies')->onDelete('cascade');
            
            // Identité du logiciel
            $table->enum('type_logiciel', ['systeme_exploitation', 'antivirus', 'bureautique', 'securite', 'utilitaire', 'autre']);
            $table->string('nom_logiciel');
            $table->string('editeur')->nullable();
            $table->string('version')->nullable();
            
            // Licence
            $table->string('reference_licence')->nullable();
            $table->text('cle_licence_chiffree')->nullable();
            $table->integer('nombre_licences')->default(1);
            $table->integer('licences_utilisees')->default(0);
            $table->integer('licences_disponibles')->default(1);
            $table->enum('type_licence', ['perpetuelle', 'abonnement_annuel', 'abonnement_mensuel', 'trial', 'gratuit'])->default('perpetuelle');
            
            // Dates
            $table->date('date_acquisition')->nullable();
            $table->dateTime('date_installation')->nullable();
            $table->date('date_expiration_licence')->nullable();
            
            // Financier
            $table->decimal('prix_unitaire', 10, 2)->nullable();
            $table->decimal('cout_total', 10, 2)->nullable();
            $table->foreignId('fournisseur_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('reference_facture')->nullable();
            $table->string('reference_commande')->nullable();
            
            // Support
            $table->boolean('support_technique')->default(false);
            $table->date('date_expiration_support')->nullable();
            $table->enum('niveau_support', ['standard', 'premium', 'enterprise'])->nullable();
            
            // État
            $table->enum('etat_logiciel', ['actif', 'inactif', 'expire', 'a_renouveler'])->default('actif');
            
            // Conformité
            $table->boolean('conforme_legalement')->default(true);
            $table->string('document_conformite')->nullable();
            
            // Gestion
            $table->string('responsable_it')->nullable();
            $table->dateTime('date_derniere_verification')->nullable();
            $table->boolean('alerte_renouvellement')->default(false);
            $table->integer('jours_avant_expiration_alerte')->default(30);
            $table->text('observations')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('software');
    }
};