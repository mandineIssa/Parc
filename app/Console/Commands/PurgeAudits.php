<?php

namespace App\Console\Commands;

use App\Models\Audit;
use Illuminate\Console\Command;

class PurgeAudits extends Command
{
    protected $signature = 'audits:purge {--days=365 : Nombre de jours à conserver}';
    protected $description = 'Supprime les audits plus anciens que X jours';

    public function handle()
    {
        $days = $this->option('days');
        $count = Audit::where('created_at', '<', now()->subDays($days))->delete();
        
        $this->info("{$count} audits supprimés (plus anciens que {$days} jours).");
        
        return Command::SUCCESS;
    }
}