<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Collecte d'audits postes (script PowerShell).
 * Identité unique d'un poste : hostname + numero_serie.
 * L'historique (y compris utilisateur de session) est dans poste_audits.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postes', function (Blueprint $table) {
            $table->id();
            $table->string('hostname', 255);
            $table->string('numero_serie', 255);
            $table->string('utilisateur_session', 255)->nullable()->comment('Utilisateur courant (DOMAINE\\user)');
            $table->string('fabricant', 255)->nullable();
            $table->string('modele', 255)->nullable();
            $table->string('os', 255)->nullable();
            $table->string('version_os', 255)->nullable();
            $table->boolean('antivirus_defender')->default(false);
            $table->string('firewall', 512)->nullable();
            $table->string('bitlocker', 512)->nullable();
            $table->boolean('usb_stockage_bloque')->default(false);
            $table->string('adresse_mac', 64)->nullable();
            $table->string('adresse_ip', 64)->nullable();
            $table->timestamp('date_audit')->nullable();
            $table->timestamps();

            $table->unique(['hostname', 'numero_serie'], 'postes_hostname_numero_serie_unique');
            $table->index('utilisateur_session');
            $table->index('fabricant');
            $table->index('os');
            $table->index('antivirus_defender');
            $table->index('date_audit');
        });

        Schema::create('poste_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poste_id')->constrained('postes')->cascadeOnDelete();
            $table->string('hostname', 255);
            $table->string('numero_serie', 255);
            $table->string('utilisateur_session', 255)->nullable();
            $table->string('fabricant', 255)->nullable();
            $table->string('modele', 255)->nullable();
            $table->string('os', 255)->nullable();
            $table->string('version_os', 255)->nullable();
            $table->boolean('antivirus_defender')->default(false);
            $table->string('firewall', 512)->nullable();
            $table->string('bitlocker', 512)->nullable();
            $table->boolean('usb_stockage_bloque')->default(false);
            $table->string('adresse_mac', 64)->nullable();
            $table->string('adresse_ip', 64)->nullable();
            $table->timestamp('date_audit')->nullable();
            $table->timestamps();

            $table->index(['poste_id', 'date_audit']);
            $table->index('utilisateur_session');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poste_audits');
        Schema::dropIfExists('postes');
    }
};
