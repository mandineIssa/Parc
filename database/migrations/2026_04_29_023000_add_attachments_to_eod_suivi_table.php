<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eod_suivi', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('incidents_data');
        });
    }

    public function down(): void
    {
        Schema::table('eod_suivi', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }
};
