<?php
// database/migrations/xxxx_create_passwords_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('passwords', function (Blueprint $table) {
            $table->id();
            $table->string('categorie');          // Serveur, Réseau, BDD, Sécurité
            $table->string('nom');                // Nom de l'équipement / serveur
            $table->string('nom_exi')->nullable();
            $table->string('adresse_ip')->nullable();
            $table->string('nom_vm')->nullable();
            $table->string('adresse_ip_vm')->nullable();
            $table->string('protocole')->nullable(); // SSH, RDP, HTTPS…
            $table->string('compte');
            $table->text('mot_de_passe');         // chiffré (encrypted cast)
            $table->string('instance')->nullable(); // Pour BDD
            $table->string('type_equipement')->nullable(); // Pour réseau
            $table->string('site')->nullable();
            $table->date('date_expiration')->nullable();
            $table->integer('duree_renouvellement')->nullable(); // en jours
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Table de partage
        Schema::create('password_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('password_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('pole')->nullable();    // Réseaux, BDD, Support
            $table->enum('droit', ['lecture', 'modification', 'administration'])->default('lecture');
            $table->date('expiration')->nullable();
            $table->boolean('permanent')->default(true);
            $table->timestamps();
        });

        // Journal d'accès
        Schema::create('password_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('password_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('action', ['consultation', 'creation', 'modification', 'suppression', 'partage']);
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_logs');
        Schema::dropIfExists('password_shares');
        Schema::dropIfExists('passwords');
    }
};
