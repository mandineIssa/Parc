<?php

namespace App\Console\Commands;

use App\Services\CahierDesChargesModuleParcBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

class BuildCahierDesChargesModuleParcPdf extends Command
{
    protected $signature = 'documentation:build-cahier-charges-module-parc-pdf';

    protected $description = 'Génère le PDF cahier des charges du module Parc Informatique';

    public function handle(CahierDesChargesModuleParcBuilder $builder): int
    {
        $dir = storage_path('app/public/documentation');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir.DIRECTORY_SEPARATOR.'cahier_des_charges_module_parc.pdf';

        Pdf::loadView('documentation.pdf.cahier-charges-module-parc', [
            'chapters' => $builder->chapters(),
            'version' => '1.0',
            'generatedAt' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'portrait')->save($path);

        $this->info('Cahier des charges Module Parc généré :');
        $this->line($path);

        return self::SUCCESS;
    }
}
