<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reaffectations', function (Blueprint $table) {
            $table->timestamp('annulee_at')->nullable()->after('fait_par');
            $table->foreignId('annulee_par')->nullable()->after('annulee_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reaffectations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('annulee_par');
            $table->dropColumn('annulee_at');
        });
    }
};
