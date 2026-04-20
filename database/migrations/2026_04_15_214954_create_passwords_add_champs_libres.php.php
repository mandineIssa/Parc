<?php
// database/migrations/xxxx_update_passwords_add_champs_libres.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('passwords', function (Blueprint $table) {
            $table->json('champs_libres')->nullable()->after('description');
        });

        Schema::create('password_fichiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('password_id')->constrained()->cascadeOnDelete();
            $table->string('nom_original');
            $table->string('chemin');
            $table->unsignedBigInteger('taille')->default(0);
            $table->string('mime')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_fichiers');
        Schema::table('passwords', fn($t) => $t->dropColumn('champs_libres'));
    }
};
