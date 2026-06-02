<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transition_approvals', function (Blueprint $table) {
            $table->index('created_at', 'transition_approvals_created_at_index');
            $table->index(['submitted_by', 'created_at'], 'transition_approvals_submitter_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('transition_approvals', function (Blueprint $table) {
            $table->dropIndex('transition_approvals_created_at_index');
            $table->dropIndex('transition_approvals_submitter_created_index');
        });
    }
};
