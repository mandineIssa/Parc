<?php
// database/migrations/xxxx_create_network_addresses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('network_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('site');               // AGP, TOUBA, TAMBA, ZIG, PIKINE…
            $table->string('type');               // plan_adressage | branchement_local
            $table->string('vlan')->nullable();
            $table->string('adresse_reseau')->nullable();
            $table->string('masque')->nullable();
            $table->string('adresse_exclue')->nullable();
            $table->string('adresse_dhcp')->nullable();
            $table->string('default_gateway')->nullable();
            // Branchement local
            $table->integer('numero')->nullable();
            $table->string('equipement_reseau')->nullable();
            $table->string('type_equipement')->nullable();
            $table->string('adresse_ip')->nullable();
            $table->string('type_port')->nullable();
            $table->string('port_reseau')->nullable();
            $table->string('vlan_port')->nullable();
            $table->string('emplacement')->nullable();
            $table->string('equipement_connecte')->nullable();
            $table->string('type_cable')->nullable();
            $table->string('adresse_ip_connecte')->nullable();
            $table->string('commentaires')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_addresses');
    }
};
