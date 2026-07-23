<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'gpi:backup-database';

    protected $description = 'Sauvegarde SQLite ou exporte les métadonnées de connexion MySQL';

    public function handle(): int
    {
        $connection = config('database.default');
        $dir = 'backups/'.now()->format('Y-m-d');
        Storage::disk('local')->makeDirectory($dir);

        if ($connection === 'sqlite') {
            $source = database_path('database.sqlite');
            if (! is_file($source)) {
                $this->error('Fichier SQLite introuvable.');

                return self::FAILURE;
            }
            $dest = "{$dir}/database-".now()->format('His').'.sqlite';
            Storage::disk('local')->put($dest, file_get_contents($source));
            $this->info('Backup SQLite : storage/app/'.$dest);

            return self::SUCCESS;
        }

        $meta = [
            'connection' => $connection,
            'host' => config('database.connections.'.$connection.'.host'),
            'database' => config('database.connections.'.$connection.'.database'),
            'backed_up_at' => now()->toIso8601String(),
            'note' => 'Utilisez mysqldump en production pour une sauvegarde complète.',
        ];
        $path = "{$dir}/db-meta-".now()->format('His').'.json';
        Storage::disk('local')->put($path, json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->warn('MySQL : métadonnées enregistrées. Planifiez mysqldump côté serveur.');
        $this->info('storage/app/'.$path);

        return self::SUCCESS;
    }
}
