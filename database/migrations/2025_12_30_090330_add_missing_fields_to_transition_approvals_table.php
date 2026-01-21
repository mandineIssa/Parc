<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transition_approvals', function (Blueprint $table) {
            // Ajouter les champs manquants utilisés par votre contrôleur
            if (!Schema::hasColumn('transition_approvals', 'installation_data')) {
                $table->json('installation_data')->nullable()->after('checklist_data');
            }
            
            if (!Schema::hasColumn('transition_approvals', 'final_mouvement_file')) {
                $table->string('final_mouvement_file')->nullable()->after('super_admin_signature');
            }
            
            if (!Schema::hasColumn('transition_approvals', 'final_installation_file')) {
                $table->string('final_installation_file')->nullable()->after('final_mouvement_file');
            }
            
            if (!Schema::hasColumn('transition_approvals', 'validation_date')) {
                $table->date('validation_date')->nullable()->after('rejected_at');
            }
            
            // Optionnel: ajouter user_id et metadata si vous en avez besoin
            // (basé sur votre ancien modèle)
            if (!Schema::hasColumn('transition_approvals', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('equipment_id')->constrained('users');
            }
            
            if (!Schema::hasColumn('transition_approvals', 'metadata')) {
                $table->json('metadata')->nullable()->after('rejected_at');
            }
        });
    }

    public function down()
    {
        Schema::table('transition_approvals', function (Blueprint $table) {
            $columns = [
                'installation_data',
                'final_mouvement_file',
                'final_installation_file',
                'validation_date',
                'user_id',
                'metadata',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('transition_approvals', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};