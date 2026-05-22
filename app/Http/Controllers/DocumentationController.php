<?php

namespace App\Http\Controllers;

use App\Services\DocumentationManuelPdfBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Artisan;

class DocumentationController extends Controller
{
    public function index()
    {
        $this->ensureCapturesExist();

        return view('documentation.index', [
            'pdfReady' => file_exists(public_path('doc-captures/01-accueil-dashboard.png')),
        ]);
    }

    public function show(string $section)
    {
        $sections = [
            'utilisateur' => 'Guide Utilisateur',
            'admin' => 'Guide Administrateur',
            'agent-it' => 'Guide Agent IT',
            'manuel-complet' => 'Manuel d\'utilisation complet',
            'api' => 'Documentation API',
            'installation' => 'Guide d\'Installation',
        ];

        if (! array_key_exists($section, $sections)) {
            abort(404);
        }

        $this->ensureCapturesExist();

        return view('documentation.show', [
            'section' => $section,
            'title' => $sections[$section],
        ]);
    }

    public function downloadManuelPdf(DocumentationManuelPdfBuilder $builder)
    {
        $this->ensureCapturesExist();

        $pdf = Pdf::loadView('documentation.pdf.manuel-complet', [
            'chapters' => $builder->chapters(),
            'version' => '1.0',
            'generatedAt' => now()->format('d/m/Y H:i'),
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Manuel_Parc_IT_COFINA_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    public function download(string $format = 'pdf')
    {
        if ($format === 'pdf') {
            return $this->downloadManuelPdf(app(DocumentationManuelPdfBuilder::class));
        }

        $filename = 'documentation_parc_informatique.' . $format;
        $path = storage_path('app/public/documentation/' . $filename);

        if (! file_exists($path)) {
            return redirect()->route('documentation.index')
                ->with('error', 'Documentation non disponible. Utilisez le bouton « Télécharger le manuel PDF ».');
        }

        return response()->download($path);
    }

    private function ensureCapturesExist(): void
    {
        $sample = public_path('doc-captures/01-accueil-dashboard.png');
        if (file_exists($sample)) {
            return;
        }

        try {
            Artisan::call('documentation:generate-captures');
        } catch (\Throwable) {
            // GD absent : le manuel s'affiche sans images
        }
    }
}
