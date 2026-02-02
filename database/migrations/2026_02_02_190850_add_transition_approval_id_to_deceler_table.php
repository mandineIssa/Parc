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
        Schema::table('deceler', function (Blueprint $table) {
            // Vérifier si la colonne n'existe pas déjà
            if (!Schema::hasColumn('deceler', 'transition_approval_id')) {
                $table->foreignId('transition_approval_id')
                      ->nullable()
                      ->after('stock_id')
                      ->constrained('transition_approvals')
                      ->onDelete('set null');
                
                // Ajouter un index pour améliorer les performances
                $table->index('transition_approval_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deceler', function (Blueprint $table) {
            // Supprimer la clé étrangère d'abord
            $table->dropForeign(['transition_approval_id']);
            // Supprimer la colonne
            $table->dropColumn('transition_approval_id');
            // Supprimer l'index
            $table->dropIndex(['transition_approval_id']);
        });
    }
};