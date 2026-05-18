<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * La colonne role était un ENUM ('user','agent_it','super_admin') : les valeurs
     * eod_n3 / eod_controller provoquaient SQLSTATE 1265 (Data truncated).
     *
     * Cible production / CI : MySQL ou MariaDB uniquement (pas SQLite).
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `users` MODIFY `role` VARCHAR(40) NOT NULL DEFAULT 'user'");
    }

    public function down(): void
    {
        // Repasser en ENUM strict : les lignes eod_* deviendraient invalides — on laisse VARCHAR en général.
        DB::statement("ALTER TABLE `users` MODIFY `role` VARCHAR(40) NOT NULL DEFAULT 'user'");
    }
};
