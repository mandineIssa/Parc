<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\AgencyImport;
use App\Models\Agency;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AgencyImportController extends Controller
{
    /**
     * Afficher le formulaire d'importation
     */
    public function create()
    {
        // Vérifier les permissions
        /* $this->authorize('import', Agency::class); */
        
        $import = new AgencyImport();
        $expectedHeaders = $import->getExpectedHeaders();
        
        return view('agencies.import', compact('expectedHeaders'));
    }

    /**
     * Traiter l'importation
     */
    public function store(Request $request)
    {
        // Vérifier les permissions
       /*  $this->authorize('import', Agency::class); */
        
        // Validation du fichier
        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:10240' // 10MB
            ],
            'mode' => 'in:skip,update'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $import = new AgencyImport();
            $importMode = $request->input('mode', 'skip');
            
            // Importer le fichier
            Excel::import($import, $request->file('file'));
            
            // Récupérer les statistiques
            $stats = $import->getImportStats();
            
            // Préparer le message de résultat
            $message = $this->getResultMessage($stats);
            
            // Ajouter les erreurs détaillées si présentes
            if (!empty($stats['errors']) || $import->failures()->isNotEmpty()) {
                $errors = array_merge($stats['errors'], $this->formatFailures($import->failures()));
                session()->flash('import_errors', $errors);
            }
            
            return redirect()->route('agencies.index')
                ->with('success', $message)
                ->with('import_stats', $stats);
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }

    /**
     * Télécharger le template Excel
     */
    public function downloadTemplate()
    {
        // Vérifier les permissions
        /* $this->authorize('import', Agency::class); */
        
        $headers = [
            'code',
            'nom',
            'ville',
            'adresse',
            'telephone',
            'email'
        ];
        
        // Créer le contenu CSV
        $csvContent = implode(',', $headers) . "\n";
        $csvContent .= "AG001,Siège Social,Dakar,Plateau,Immeuble Alpha,+221 33 821 00 00,siege@entreprise.sn\n";
        $csvContent .= "AG002,Agence Dakar Médina,Dakar,Médina,Rue 10 x 12,+221 33 821 01 00,dakar-medina@entreprise.sn\n";
        $csvContent .= "AG003,Agence Thiès,Thiès,Centre Ville,Avenue Lamine Gueye,+221 33 821 02 00,thies@entreprise.sn\n";
        
        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, 'template_import_agences.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Formater le message de résultat
     */
    private function getResultMessage(array $stats): string
    {
        $message = "Importation terminée. ";
        
        if ($stats['imported'] > 0) {
            $message .= "✅ {$stats['imported']} agence(s) importée(s) avec succès. ";
        }
        
        if ($stats['skipped'] > 0) {
            $message .= "⚠️ {$stats['skipped']} ligne(s) ignorée(s). ";
        }
        
        if (!empty($stats['errors'])) {
            $message .= "❌ " . count($stats['errors']) . " erreur(s) rencontrée(s). ";
        }
        
        return trim($message);
    }

    /**
     * Formater les échecs d'importation
     */
    private function formatFailures($failures): array
    {
        $formatted = [];
        
        foreach ($failures as $failure) {
            $row = $failure->row();
            $errors = implode(', ', $failure->errors());
            $formatted[] = "Ligne {$row}: {$errors}";
        }
        
        return $formatted;
    }
}