<?php
// database/migrations/xxxx_create_licences_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('licences', function (Blueprint $table) {
            $table->id();
            $table->string('type');               // Fortinet, FAI, Certificat, Office365
            $table->string('nom');
            $table->string('site_agence')->nullable();
            // Fortinet
            $table->string('modele')->nullable();
            $table->string('numero_serie')->nullable();
            $table->string('type_licence')->nullable();
            $table->decimal('prix_achat', 10, 2)->nullable();
            // FAI
            $table->string('fournisseur')->nullable();
            $table->string('numero_client')->nullable();
            $table->string('type_ligne')->nullable();
            $table->string('ip_publique')->nullable();
            $table->string('debit')->nullable();
            $table->decimal('montant_mensuel', 10, 2)->nullable();
            // Certificat
            $table->string('environnement')->nullable();
            $table->string('emplacement')->nullable();
            $table->integer('port')->nullable();
            $table->integer('duree_jours')->nullable();
            // Office 365
            $table->string('utilisateur')->nullable();
            $table->string('departement')->nullable();
            $table->string('email')->nullable();
            $table->string('espace_onedrive')->nullable();
            $table->boolean('teams')->default(false);
            $table->string('quota_total')->nullable();
            // Commun
            $table->string('statut')->default('Actif'); // Actif, Bientôt expirée, Expirée, Résiliée
            $table->date('date_activation')->nullable();
            $table->date('date_expiration')->nullable();
            $table->date('date_mise_en_service')->nullable();
            $table->date('echeance_contrat')->nullable();
            $table->boolean('renouvellement_prevu')->default(false);
            $table->string('contact_nom')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_tel')->nullable();
            $table->text('observation')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licences');
    }
};
