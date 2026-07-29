<?php

namespace App\Console\Commands;

use App\Services\DocumentationParcTechniquePdfBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;

class BuildParcTechniquePdf extends Command
{
    protected $signature = 'documentation:build-parc-technique-pdf
                            {--open : Affiche le chemin absolu du PDF généré}';

    protected $description = 'Génère le PDF documentation technique du module Parc';

    public function handle(DocumentationParcTechniquePdfBuilder $builder): int
    {
        $dir = storage_path('app/public/documentation');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'documentation_technique_module_parc.pdf';
        $path = $dir.DIRECTORY_SEPARATOR.$filename;

        Pdf::loadView('documentation.pdf.parc-technique', [
            'chapters' => $builder->chapters(),
            'version' => '1.0',
            'generatedAt' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'portrait')->save($path);

        $this->info('PDF technique module Parc généré :');
        $this->line($path);

        if ($this->option('open')) {
            $this->comment('Ouvrir le fichier depuis l’explorateur ou storage/app/public/documentation/');
        }

        return self::SUCCESS;
    }
}
