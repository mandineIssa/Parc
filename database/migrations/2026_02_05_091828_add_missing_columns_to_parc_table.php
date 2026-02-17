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
        Schema::table('parc', function (Blueprint $table) {
            // Colonnes manquantes du formulaire
            if (!Schema::hasColumn('parc', 'utilisateur_nom')) {
                $table->string('utilisateur_nom')->nullable()->after('utilisateur_id');
            }
            
            if (!Schema::hasColumn('parc', 'utilisateur_prenom')) {
                $table->string('utilisateur_prenom')->nullable()->after('utilisateur_nom');
            }
            
            if (!Schema::hasColumn('parc', 'position')) {
                $table->enum('position', [
                    'Directeur',
                    'Manager', 
                    'Chef de Projet',
                    'Technicien',
                    'Développeur',
                    'Analyste',
                    'Consultant',
                    'Administrateur',
                    'Assistant',
                    'Agent',
                    'Stagiaire',
                    'CC',
                    'RH',
                    'Finance',
                    'Caissier',
                    'recouvrement',
                    'juridique',
                    'CAF',
                    'Logistique',
                    'marketing',
                    'Autre'
                ])->nullable()->after('poste_affecte');
            }
            
            if (!Schema::hasColumn('parc', 'affectation_reason')) {
                $table->enum('affectation_reason', [
                    'Nouvelle embauche',
                    'Remplacement d\'\'équipement',
                    'Changement de poste',
                    'Besoins opérationnels',
                    'Mise à niveau',
                    'Dotation temporaire',
                    'Autre'
                ])->nullable()->after('date_affectation');
            }
            
            if (!Schema::hasColumn('parc', 'affectation_reason_detail')) {
                $table->text('affectation_reason_detail')->nullable()->after('affectation_reason');
            }
            
            if (!Schema::hasColumn('parc', 'localisation')) {
                $table->string('localisation')->nullable()->after('affectation_reason_detail');
            }
            
            if (!Schema::hasColumn('parc', 'telephone')) {
                $table->string('telephone')->nullable()->after('localisation');
            }
            
            if (!Schema::hasColumn('parc', 'email')) {
                $table->string('email')->nullable()->after('telephone');
            }
            
            // Colonnes optionnelles de tracking
            if (!Schema::hasColumn('parc', 'affecte_par')) {
                $table->foreignId('affecte_par')->nullable()->constrained('users')->onDelete('set null')->after('email');
            }
            
            if (!Schema::hasColumn('parc', 'derniere_modification')) {
                $table->dateTime('derniere_modification')->nullable()->after('affecte_par');
            }
            
            if (!Schema::hasColumn('parc', 'numero_bon_affectation')) {
                $table->string('numero_bon_affectation')->nullable()->unique()->after('derniere_modification');
            }
            
            // Index (seulement s'ils n'existent pas)
            if (!Schema::hasIndex('parc', ['utilisateur_nom', 'utilisateur_prenom'])) {
                $table->index(['utilisateur_nom', 'utilisateur_prenom']);
            }
            
            if (!Schema::hasIndex('parc', ['position'])) {
                $table->index('position');
            }
            
            if (!Schema::hasIndex('parc', ['affectation_reason'])) {
                $table->index('affectation_reason');
            }
        });
    }

    /**
     * Reverse the migrations. 
     */
    public function down(): void
    {
        Schema::table('parc', function (Blueprint $table) {
            // Supprimer seulement les colonnes que nous ajoutons
            // NE PAS supprimer les colonnes existantes
            $columnsToDrop = [];
            
            if (Schema::hasColumn('parc', 'utilisateur_nom')) {
                $columnsToDrop[] = 'utilisateur_nom';
            }
            
            if (Schema::hasColumn('parc', 'utilisateur_prenom')) {
                $columnsToDrop[] = 'utilisateur_prenom';
            }
            
            if (Schema::hasColumn('parc', 'position')) {
                $columnsToDrop[] = 'position';
            }
            
            if (Schema::hasColumn('parc', 'affectation_reason')) {
                $columnsToDrop[] = 'affectation_reason';
            }
            
            if (Schema::hasColumn('parc', 'affectation_reason_detail')) {
                $columnsToDrop[] = 'affectation_reason_detail';
            }
            
            if (Schema::hasColumn('parc', 'localisation')) {
                $columnsToDrop[] = 'localisation';
            }
            
            if (Schema::hasColumn('parc', 'telephone')) {
                $columnsToDrop[] = 'telephone';
            }
            
            if (Schema::hasColumn('parc', 'email')) {
                $columnsToDrop[] = 'email';
            }
            
            if (Schema::hasColumn('parc', 'affecte_par')) {
                $columnsToDrop[] = 'affecte_par';
            }
            
            if (Schema::hasColumn('parc', 'derniere_modification')) {
                $columnsToDrop[] = 'derniere_modification';
            }
            
            if (Schema::hasColumn('parc', 'numero_bon_affectation')) {
                $columnsToDrop[] = 'numero_bon_affectation';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
            
            // Supprimer les index (s'ils existent)
            $table->dropIndexIfExists(['utilisateur_nom', 'utilisateur_prenom']);
            $table->dropIndexIfExists(['position']);
            $table->dropIndexIfExists(['affectation_reason']);
        });
    }
};