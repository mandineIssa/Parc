<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('controls', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['securite', 'exploitation', 'conformite', 'audit']);
            $table->enum('frequency', ['quotidienne', 'hebdomadaire', 'mensuelle', 'ponctuelle']);
            $table->enum('status', ['actif', 'inactif'])->default('actif');
            $table->text('description')->nullable();
            $table->foreignId('template_id')->nullable()->constrained('control_templates')->nullOnDelete();
            $table->string('associated_application')->nullable();
            $table->enum('responsible_role', ['N1', 'N2', 'N3']);
            $table->json('parameters')->nullable();
            $table->datetime('last_run_at')->nullable();
            $table->datetime('next_run_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('controls');
    }
};