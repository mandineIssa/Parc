<?php

namespace App\Console\Commands;

use App\Services\DocumentationManuelPdfBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

class BuildDocumentationPdf extends Command
{
    protected $signature = 'documentation:build-pdf';

    protected $description = 'Génère le PDF du manuel dans storage/app/public/documentation/';

    public function handle(DocumentationManuelPdfBuilder $builder): int
    {
        $this->call('documentation:generate-captures');

        $dir = storage_path('app/public/documentation');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir . '/documentation_parc_informatique.pdf';

        Pdf::loadView('documentation.pdf.manuel-complet', [
            'chapters' => $builder->chapters(),
            'version' => '1.0',
            'generatedAt' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'portrait')->save($path);

        $this->info('PDF enregistré : ' . $path);

        return self::SUCCESS;
    }
}
