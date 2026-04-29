<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fusion de l’ancienne migration 2026_04_28_220000 (champs validation Controller)
     * + signature N+3 / dual sign / émargement fichier.
     */
    public function up(): void
    {
        Schema::table('eod_suivi', function (Blueprint $table) {
            $table->unsignedBigInteger('controller_validated_by')->nullable()->after('validated_by');
            $table->dateTime('controller_validated_at')->nullable()->after('validated_at');
            $table->string('controller_validation_date')->nullable()->after('validation_audit_visa');
            $table->string('controller_validation_visa')->nullable()->after('controller_validation_date');
            $table->text('controller_validation_note')->nullable()->after('controller_validation_visa');

            $table->unsignedBigInteger('n3_validated_by')->nullable()->after('controller_validated_by');
            $table->dateTime('n3_validated_at')->nullable()->after('n3_validated_by');
            $table->string('n3_validation_date')->nullable()->after('n3_validated_at');
            $table->text('n3_validation_note')->nullable()->after('n3_validation_date');
            $table->string('n3_signature_path')->nullable()->after('n3_validation_note');
            $table->string('controller_signature_path')->nullable()->after('controller_validation_note');
            $table->string('emargement_signature_path')->nullable()->after('emargement');

            $table->foreign('controller_validated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('n3_validated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('eod_suivi', function (Blueprint $table) {
            $table->dropForeign(['controller_validated_by']);
            $table->dropForeign(['n3_validated_by']);
            $table->dropColumn([
                'n3_validated_by',
                'n3_validated_at',
                'n3_validation_date',
                'n3_validation_note',
                'n3_signature_path',
                'controller_signature_path',
                'emargement_signature_path',
                'controller_validated_by',
                'controller_validated_at',
                'controller_validation_date',
                'controller_validation_visa',
                'controller_validation_note',
            ]);
        });
    }
};
