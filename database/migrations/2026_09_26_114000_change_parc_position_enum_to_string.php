<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('parc', 'position')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `parc` MODIFY `position` VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        // Intentionnellement vide : les postes importés (CHEF AGENCE, CAISSE, …)
        // ne rentrent pas dans l'ancien ENUM.
    }
};
