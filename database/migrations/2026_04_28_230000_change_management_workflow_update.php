<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('change_tickets', function (Blueprint $table) {
            $table->json('n2_progress_entries')->nullable()->after('history');
            $table->json('n3_progress_entries')->nullable()->after('n2_progress_entries');
        });

        DB::table('change_tickets')
            ->where('status', 'VALIDATED_N2')
            ->update(['status' => 'PENDING_N3']);
    }

    public function down(): void
    {
        Schema::table('change_tickets', function (Blueprint $table) {
            $table->dropColumn(['n2_progress_entries', 'n3_progress_entries']);
        });

        DB::table('change_tickets')
            ->where('status', 'PENDING_N3')
            ->whereNull('incident_num')
            ->update(['status' => 'VALIDATED_N2']);
    }
};
