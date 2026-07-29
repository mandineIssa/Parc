<?php

namespace App\Console\Commands;

use App\Services\CahierDesChargesAuditsPostesBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

class BuildCahierDesChargesAuditsPdf extends Command
{
    protected $signature = 'documentation:build-cahier-charges-audits-pdf';

    protected $description = 'Génère le PDF cahier des charges (audits postes / module Parc)';

    public function handle(CahierDesChargesAuditsPostesBuilder $builder): int
    {
        $dir = storage_path('app/public/documentation');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir.DIRECTORY_SEPARATOR.'cahier_des_charges_audits_postes.pdf';

        Pdf::loadView('documentation.pdf.cahier-charges-audits-postes', [
            'chapters' => $builder->chapters(),
            'version' => '1.0',
            'generatedAt' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'portrait')->save($path);

        $this->info('Cahier des charges généré :');
        $this->line($path);

        return self::SUCCESS;
    }
}
