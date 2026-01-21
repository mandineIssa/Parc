<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ajouter la colonne sans unique d'abord
        Schema::table('agencies', function (Blueprint $table) {
            $table->string('code', 10)->nullable()->after('id');
        });

        // 2. Mettre à jour les agences existantes avec des codes uniques
        $agencies = DB::table('agencies')->get();
        foreach ($agencies as $index => $agency) {
            DB::table('agencies')
                ->where('id', $agency->id)
                ->update(['code' => str_pad($index + 1, 3, '0', STR_PAD_LEFT)]);
        }

        // 3. Modifier la colonne pour qu'elle ne soit pas nullable et unique
        Schema::table('agencies', function (Blueprint $table) {
            $table->string('code', 10)->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};