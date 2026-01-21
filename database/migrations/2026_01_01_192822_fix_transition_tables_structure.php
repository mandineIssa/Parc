<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Renommer approvals en transition_approvals si nécessaire
        if (Schema::hasTable('approvals') && !Schema::hasTable('transition_approvals')) {
            Schema::rename('approvals', 'transition_approvals');
        }
        
        // 2. Ajouter les champs manquants à transition_approvals
        Schema::table('transition_approvals', function (Blueprint $table) {
            if (!Schema::hasColumn('transition_approvals', 'type')) {
                $table->string('type')->nullable()->after('to_status');
            }
            
            if (!Schema::hasColumn('transition_approvals', 'requires_super_admin_validation')) {
                $table->boolean('requires_super_admin_validation')->default(true)->after('type');
            }
            
            if (!Schema::hasColumn('transition_approvals', 'submitted_by')) {
                if (Schema::hasColumn('transition_approvals', 'requested_by')) {
                    $table->renameColumn('requested_by', 'submitted_by');
                } else {
                    $table->foreignId('submitted_by')->nullable()->constrained('users')->after('equipment_id');
                }
            }
            
            if (!Schema::hasColumn('transition_approvals', 'data')) {
                if (Schema::hasColumn('transition_approvals', 'request_data')) {
                    $table->renameColumn('request_data', 'data');
                } else {
                    $table->json('data')->nullable()->after('type');
                }
            }
            
            if (!Schema::hasColumn('transition_approvals', 'generated_files')) {
                $table->json('generated_files')->nullable()->after('data');
            }
            
            if (!Schema::hasColumn('transition_approvals', 'checklist_data')) {
                $table->json('checklist_data')->nullable()->after('generated_files');
            }
            
            if (!Schema::hasColumn('transition_approvals', 'installation_data')) {
                $table->json('installation_data')->nullable()->after('checklist_data');
            }
            
            if (!Schema::hasColumn('transition_approvals', 'validation_notes')) {
                if (Schema::hasColumn('transition_approvals', 'validation_notes')) {
                    // Déjà présent
                } else {
                    $table->text('validation_notes')->nullable()->after('checklist_data');
                }
            }
            
            if (!Schema::hasColumn('transition_approvals', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('validation_notes');
            }
        });
        
        // 3. Créer les tables de formulaires si elles n'existent pas
        if (!Schema::hasTable('fiche_installations')) {
            Schema::create('fiche_installations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transition_approval_id')->constrained('transition_approvals')->onDelete('cascade');
                $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                
                // Données de base
                $table->date('date_application');
                $table->string('agence_nom');
                $table->date('date_installation');
                
                // Checklist
                $table->json('checklist')->nullable();
                
                // Installateur
                $table->string('installateur_nom');
                $table->string('installateur_fonction');
                $table->string('signature_installateur')->nullable();
                
                // Vérification (pour Super Admin)
                $table->date('date_verification')->nullable();
                $table->string('verificateur_nom')->nullable();
                $table->string('verificateur_fonction')->nullable();
                $table->string('signature_verificateur')->nullable();
                $table->string('signature_utilisateur')->nullable();
                
                // Observations
                $table->text('observations')->nullable();
                
                // Statut
                $table->enum('status', ['draft', 'completed', 'archived'])->default('draft');
                
                $table->timestamps();
                $table->softDeletes();
            });
        }
        
        if (!Schema::hasTable('fiche_mouvements')) {
            Schema::create('fiche_mouvements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transition_approval_id')->constrained('transition_approvals')->onDelete('cascade');
                $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                
                // Données de base
                $table->date('date_application');
                
                // Expéditeur
                $table->string('expediteur_nom');
                $table->string('expediteur_fonction');
                $table->date('date_expediteur');
                $table->string('signature_expediteur')->nullable();
                
                // Réceptionnaire
                $table->string('receptionnaire_nom');
                $table->string('receptionnaire_fonction');
                $table->date('date_receptionnaire')->nullable();
                $table->string('signature_receptionnaire')->nullable();
                
                // Détails du mouvement
                $table->string('lieu_depart');
                $table->string('destination');
                $table->string('motif');
                
                // Statut
                $table->enum('status', ['draft', 'completed', 'archived'])->default('draft');
                
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        // Ne pas supprimer les tables, seulement les renommer si nécessaire
        Schema::dropIfExists('fiche_mouvements');
        Schema::dropIfExists('fiche_installations');
    }
};