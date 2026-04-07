<?php

namespace App\Http\Controllers;

use App\Models\Parc;
use App\Models\Equipment;
use App\Models\EquipmentDetail;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ParcController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    /**
     * Afficher la liste du parc d'équipements avec filtres
     */
    public function index(Request $request)
    {
        // Construction de la requête de base
        $query = Equipment::where('statut', 'parc')
            ->with(['fournisseur', 'parc', 'agence']);
        
        // Recherche globale
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('numero_serie', 'LIKE', "%{$search}%")
                  ->orWhere('nom', 'LIKE', "%{$search}%")
                  ->orWhere('marque', 'LIKE', "%{$search}%")
                  ->orWhere('modele', 'LIKE', "%{$search}%")
                  ->orWhere('numero_codification', 'LIKE', "%{$search}%")
                  // Recherche dans les données du parc
                  ->orWhereHas('parc', function($parcQuery) use ($search) {
                      $parcQuery->where('utilisateur_nom', 'LIKE', "%{$search}%")
                               ->orWhere('utilisateur_prenom', 'LIKE', "%{$search}%")
                               ->orWhere('localisation', 'LIKE', "%{$search}%")
                               ->orWhere('departement', 'LIKE', "%{$search}%");
                  })
                  // Recherche dans l'agence
                  ->orWhereHas('agence', function($agenceQuery) use ($search) {
                      $agenceQuery->where('nom', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filtre par état
        if ($request->filled('etat')) {
            $query->where('etat', $request->input('etat'));
        }
        
        // Filtre rapide
        if ($request->filled('filtre_rapide')) {
            $filtreRapide = $request->input('filtre_rapide');
            
            switch ($filtreRapide) {
                case 'a_remplacer':
                    $query->where('etat', 'mauvais');
                    break;
                case 'non_affecte':
                    $query->whereDoesntHave('parc');
                    break;
                case 'reseau':
                    $query->where('type', 'Réseau');
                    break;
                case 'informatique':
                    $query->where('type', 'Informatique');
                    break;
                case 'electronique':
                    $query->where('type', 'Électronique');
                    break;
            }
        }
        
        // Pagination avec conservation des paramètres de recherche
        $equipments = $query->orderBy('created_at', 'desc')
                            ->paginate(20)
                            ->withQueryString();
        
        // Calcul du prix total (sur les résultats filtrés, pas paginés)
        $prixTotalQuery = Equipment::where('statut', 'parc');
        
        // Appliquer les mêmes filtres pour le calcul du prix
        if ($request->filled('search')) {
            $search = $request->input('search');
            $prixTotalQuery->where(function($q) use ($search) {
                $q->where('numero_serie', 'LIKE', "%{$search}%")
                  ->orWhere('nom', 'LIKE', "%{$search}%")
                  ->orWhere('marque', 'LIKE', "%{$search}%")
                  ->orWhere('modele', 'LIKE', "%{$search}%")
                  ->orWhere('numero_codification', 'LIKE', "%{$search}%")
                  ->orWhereHas('parc', function($parcQuery) use ($search) {
                      $parcQuery->where('utilisateur_nom', 'LIKE', "%{$search}%")
                               ->orWhere('utilisateur_prenom', 'LIKE', "%{$search}%")
                               ->orWhere('localisation', 'LIKE', "%{$search}%")
                               ->orWhere('departement', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('agence', function($agenceQuery) use ($search) {
                      $agenceQuery->where('nom', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('type')) {
            $prixTotalQuery->where('type', $request->input('type'));
        }

        if ($request->filled('etat')) {
            $prixTotalQuery->where('etat', $request->input('etat'));
        }
        
        if ($request->filled('filtre_rapide')) {
            $filtreRapide = $request->input('filtre_rapide');
            
            switch ($filtreRapide) {
                case 'a_remplacer':
                    $prixTotalQuery->where('etat', 'mauvais');
                    break;
                case 'non_affecte':
                    $prixTotalQuery->whereDoesntHave('parc');
                    break;
                case 'reseau':
                    $prixTotalQuery->where('type', 'Réseau');
                    break;
                case 'informatique':
                    $prixTotalQuery->where('type', 'Informatique');
                    break;
                case 'electronique':
                    $prixTotalQuery->where('type', 'Électronique');
                    break;
            }
        }
        
        $prixTotal = $prixTotalQuery->sum('prix');
        
        return view('equipment.parc.index', compact('equipments', 'prixTotal'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $equipments = Equipment::where('statut', '!=', 'hors_service')
            ->orderBy('numero_serie')
            ->get();
        $users = User::orderBy('name')->get();
        
        return view('equipment.parc.create', compact('equipments', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    // Validation étendue pour tous les champs du formulaire
    $validated = $request->validate([
        'numero_serie' => 'required|exists:equipment,numero_serie',
        
        // Champs de base
        'utilisateur_nom' => 'required|string|max:100',
        'utilisateur_prenom' => 'required|string|max:100',
        'departement' => 'required|string|max:100',
        'poste_affecte' => 'required|string|max:100',
        'position' => 'required|in:Directeur,Manager,Chef de Projet,Technicien,Développeur,Analyste,Consultant,Administrateur,Assistant,Agent,Stagiaire,CC,RH,Finance,Caissier,recouvrement,juridique,CAF,Logistique,marketing,Autre',
        
        // Dates
        'date_affectation' => 'required|date',
        'date_retour_prevue' => 'nullable|date|after_or_equal:date_affectation',
        
        // Raison d'affectation
        'affectation_reason' => 'nullable|in:Nouvelle embauche,Remplacement d\'équipement,Changement de poste,Besoins opérationnels,Mise à niveau,Dotation temporaire,Autre',
        'affectation_reason_detail' => 'nullable|string|max:500',
        
        // Informations complémentaires
        'localisation' => 'nullable|string|max:200',
        'telephone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:100',
        
        // Statut
        'statut_usage' => 'required|in:actif,inactif,en_pret',
        'notes_affectation' => 'nullable|string|max:500',
        
        // Champs cachés
        'form_type' => 'required|in:affectation_simple',
        'transition_type' => 'required|in:stock_to_parc',
        'equipment_id' => 'required|exists:equipment,id',
    ]);
    
    // Vérifier si l'équipement n'est pas déjà affecté
    $existing = Parc::where('numero_serie', $request->numero_serie)->first();
    if ($existing) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['numero_serie' => 'Cet équipement est déjà affecté.']);
    }
    
    // Ajouter les champs de tracking
    $validated['affecte_par'] = auth()->id();
    $validated['derniere_modification'] = now();
    $validated['numero_bon_affectation'] = 'AFF-' . strtoupper(uniqid());
    
    // Créer l'affectation dans la table parc
    $parc = Parc::create($validated);
    
    // Mettre à jour le statut de l'équipement
    Equipment::where('numero_serie', $request->numero_serie)
        ->update(['statut' => 'parc']);
    
    return redirect()->route('parc.index')
        ->with('success', 'Affectation créée avec succès. Numéro de bon : ' . $validated['numero_bon_affectation']);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $parc = Parc::with(['equipment', 'utilisateur'])->findOrFail($id);
        
        return view('equipment.parc.show', compact('parc'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $parc = Parc::findOrFail($id);
        $equipments = Equipment::where('statut', '!=', 'hors_service')
            ->orderBy('numero_serie')
            ->get();
        $users = User::orderBy('name')->get();
        
        return view('equipment.parc.edit', compact('parc', 'equipments', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, string $id)
{
    $parc = Parc::findOrFail($id);
    
    $validated = $request->validate([
        'utilisateur_nom' => 'required|string|max:100',
        'utilisateur_prenom' => 'required|string|max:100',
        'departement' => 'required|string|max:100',
        'poste_affecte' => 'required|string|max:100',
        'date_affectation' => 'required|date',
        'date_retour_prevue' => 'nullable|date|after_or_equal:date_affectation',
        'statut_usage' => 'required|in:actif,inactif,en_pret',
        'notes_affectation' => 'nullable|string|max:500'
    ]);
    
    // Mettre à jour l'affectation (le numero_serie ne change PAS)
    $parc->update($validated);
    
    return redirect()->route('parc.index')
        ->with('success', 'Affectation mise à jour avec succès.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $parc = Parc::findOrFail($id);
        
        // Remettre l'équipement en stock
        Equipment::where('numero_serie', $parc->numero_serie)
            ->update(['statut' => 'stock']);
        
        $parc->delete();
        
        return redirect()->route('equipment.parc.index')
            ->with('success', 'Affectation supprimée avec succès.');
    }

    // ==================== IMPORT CSV VERS PARC ====================

    /**
     * Afficher le formulaire d'import CSV
     */
    public function showImportForm()
    {
        $suppliers = Supplier::orderBy('nom')->get();
        $users = User::orderBy('name')->get();
        return view('equipment.parc.import', compact('suppliers', 'users'));
    }

    /**
     * Télécharger le template CSV
     */
public function downloadTemplate()
{
    $filename = 'template_import_parc_' . date('Y-m-d') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ];

    $callback = function() {
        $file = fopen('php://output', 'w');
        
        // BOM UTF-8 pour Excel
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // En-têtes pour l'import vers parc
        $headers = [
            'type', 'categorie', 'sous_categorie', 'numero_serie', 'marque', 'modele',
            'garantie', 'date_livraison', 'prix', 'reference_facture', 'etat',
            'fournisseur_id', 'localisation', 'adresse_mac', 'contrat_maintenance',
            'type_switch', 'ports_ethernet', 'ports_poe', 'vitesse_ports',
            'type_routeur', 'nombre_ports_routeur', 'type_wifi', 'type_modem',
            'vitesse_max_modem', 'type_camera', 'resolution_camera', 'adresse_ip',
            'type_nvr_dvr', 'canaux_supportes', 'numero_unique_badge', 'type_alarme',
            'processeur', 'ram_capacite', 'stockage_capacite', 'type_stockage',
            'systeme_exploitation', 'taille_ecran', 'type_imprimante',
            'vitesse_impression', 'editeur', 'version', 'type_licence',
            'nombre_licences', 'date_expiration_licence', 'reference_licence',
            'utilisateur_id', 'departement', 'poste_affecte', 'date_affectation', 'notes_affectation'
        ];
        
        fputcsv($file, $headers, ';');
        
        $examples = [
            [
                'Informatique', 'Ordinateur portable', 'Professionnel', 'SN2024001', 'DELL', 'Latitude 5420',
                '3 ans', '2024-01-15', '1500000', 'FACT-2024-001', 'neuf',
                '1', 'Siège Dakar', '00:1A:2B:3C:4D:5E', 'oui',
                '', '', '', '',
                '', '', '', '',
                '', '', '', '',
                '', '', '', '',
                'Intel Core i7', '16 Go', '512 Go', 'SSD',
                'Windows 11 Pro', '14 pouces', '', '',
                '', '', '', '',
                '', '', '',
                '2', 'IT', 'Développeur', '2024-01-20', 'Affectation initiale'
            ],
            [
                'Réseau', 'Switch', '24 ports', 'SN2024002', 'Cisco', 'Catalyst 2960',
                '5 ans', '2024-01-20', '2500000', 'FACT-2024-002', 'neuf',
                '3', 'Data Center', '00:1A:2B:3C:4D:5F', 'oui',
                'Gigabit', '24', '12', '1 Gbps',
                '', '', '', '',
                '', '', '', '',
                '', '', '', '',
                '', '', '', '',
                '', '', '', '',
                '', '', '', '',
                '', '', '', '',
                '4', 'Infrastructure', 'Admin Réseau', '2024-01-25', 'Installation réseau'
            ],
        ];
        
        foreach ($examples as $example) {
            fputcsv($file, $example, ';');
        }
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

    /**
     * Importer un CSV vers le parc
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            
            Log::info('=== DÉBUT IMPORT CSV VERS PARC ===', [
                'fichier' => $file->getClientOriginalName(),
                'taille' => filesize($path) . ' octets'
            ]);
            
            $content = file_get_contents($path);
            
            if (empty(trim($content))) {
                return redirect()->route('parc.import.form')
                    ->with('error', 'Le fichier CSV est vide');
            }
            
            $content = $this->fixCsvFormat($content);
            $data = $this->readCsvFileReliably($path);
            
            if (empty($data['headers']) || empty($data['rows'])) {
                Log::error('Fichier vide après traitement', $data);
                return redirect()->route('parc.import.form')
                    ->with('error', 'Aucune donnée valide trouvée dans le fichier');
            }
            
            Log::info('Fichier analysé avec succès', [
                'colonnes' => count($data['headers']),
                'lignes' => count($data['rows'])
            ]);
            
            return $this->processImportToParc($data['headers'], $data['rows']);
            
        } catch (\Exception $e) {
            Log::error('ERREUR IMPORT GLOBALE', [
                'message' => $e->getMessage(),
                'fichier' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('parc.import.form')
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Traitement de l'import vers le parc
     */
    private function processImportToParc($headers, $rows)
    {
        $imported = 0;
        $errors = [];
        $skipped = 0;
        
        DB::beginTransaction();
        
        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2;
                
                $data = [];
                foreach ($headers as $i => $header) {
                    $value = isset($row[$i]) ? $row[$i] : '';
                    $data[$header] = $value;
                }
                
                if ($index === 0) {
                    Log::info('Traitement ligne 1 vers Parc:', [
                        'type' => $data['type'] ?? 'N/A',
                        'numero_serie' => $data['numero_serie'] ?? 'N/A',
                        'utilisateur_id' => $data['utilisateur_id'] ?? 'N/A'
                    ]);
                }
                
                if (empty($data['type'])) {
                    $errors[] = "Ligne $lineNumber: Type manquant";
                    continue;
                }
                
                if (empty($data['numero_serie'])) {
                    $errors[] = "Ligne $lineNumber: Numéro de série manquant";
                    continue;
                }
                
                if (Equipment::where('numero_serie', $data['numero_serie'])->exists()) {
                    $errors[] = "Ligne $lineNumber: Numéro de série '{$data['numero_serie']}' existe déjà";
                    continue;
                }
                
                $fournisseurId = null;
                if (!empty($data['fournisseur_id'])) {
                    $supplierId = intval($data['fournisseur_id']);
                    if (Supplier::where('id', $supplierId)->exists()) {
                        $fournisseurId = $supplierId;
                    }
                }
                
                $utilisateurId = null;
                $utilisateurInfo = null;
                if (!empty($data['utilisateur_id'])) {
                    $userId = intval($data['utilisateur_id']);
                    $user = User::find($userId);
                    if ($user) {
                        $utilisateurId = $userId;
                        $utilisateurInfo = ['name' => $user->name, 'email' => $user->email];
                    }
                }
                
                $dateLivraison = null;
                if (!empty($data['date_livraison'])) {
                    $dateLivraison = $this->parseDate($data['date_livraison']);
                }
                
                $dateAffectation = now();
                if (!empty($data['date_affectation'])) {
                    $dateAffectation = $this->parseDate($data['date_affectation']);
                }
                
                $prix = 0;
                if (!empty($data['prix'])) {
                    $prix = floatval(str_replace([',', ' '], ['.', ''], $data['prix']));
                }
                
                $etat = 'neuf';
                if (!empty($data['etat'])) {
                    $etatsValides = ['neuf', 'bon', 'moyen', 'mauvais'];
                    $etatInput = strtolower($data['etat']);
                    if (in_array($etatInput, $etatsValides)) {
                        $etat = $etatInput;
                    }
                }
                
                $contratMaintenance = !empty($data['contrat_maintenance']) && 
                    in_array(strtolower($data['contrat_maintenance']), ['1', 'oui', 'yes', 'true']);
                
                $equipmentData = [
                    'type' => $this->normalizeType($data['type']),
                    'numero_serie' => $data['numero_serie'],
                    'marque' => $data['marque'] ?? '',
                    'modele' => $data['modele'] ?? '',
                    'garantie' => $data['garantie'] ?? null,
                    'date_livraison' => $dateLivraison ?? now(),
                    'prix' => $prix,
                    'reference_facture' => $data['reference_facture'] ?? null,
                    'etat' => $etat,
                    'statut' => 'parc',
                    'fournisseur_id' => $fournisseurId,
                    'localisation' => $data['localisation'] ?? 'Non spécifié',
                    'adresse_mac' => $data['adresse_mac'] ?? null,
                    'adresse_ip' => $data['adresse_ip'] ?? null,
                    'departement' => $data['departement'] ?? null,
                    'poste_staff' => $data['poste_affecte'] ?? null,
                    'date_mise_service' => $dateAffectation ?? now(),
                    'notes' => $data['notes_affectation'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $optionalFields = ['numero_codification'];
                foreach ($optionalFields as $field) {
                    if (isset($data[$field]) && $data[$field] !== '') {
                        $equipmentData[$field] = $data[$field];
                    }
                }
                
                $equipment = Equipment::create($equipmentData);
                
                $detailsData = [
                    'equipment_id' => $equipment->id,
                    'categorie' => $data['categorie'] ?? null,
                    'sous_categorie' => $data['sous_categorie'] ?? null,
                    'contrat_maintenance' => $contratMaintenance,
                ];
                
                $specificData = $this->collectSpecificData($data, $headers);
                $detailsData['specific_data'] = !empty($specificData) ? json_encode($specificData, JSON_UNESCAPED_UNICODE) : null;
                
                EquipmentDetail::create($detailsData);
                
                $parcData = [
                    'numero_serie' => $equipment->numero_serie,
                    'equipment_id' => $equipment->id,
                    'utilisateur_id' => $utilisateurId,
                    'departement' => $data['departement'] ?? null,
                    'poste_affecte' => $data['poste_affecte'] ?? null,
                    'date_affectation' => $dateAffectation ?? now(),
                    'statut_usage' => 'actif',
                    'notes_affectation' => $data['notes_affectation'] ?? 'Import CSV direct vers parc',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                if ($utilisateurInfo) {
                    $parcData['utilisateur_nom'] = $utilisateurInfo['name'];
                    $parcData['utilisateur_email'] = $utilisateurInfo['email'];
                }
                
                Parc::create($parcData);
                
                $imported++;
                
                Log::info('Équipement importé vers parc', [
                    'id' => $equipment->id,
                    'numero_serie' => $equipment->numero_serie,
                    'statut' => $equipment->statut,
                    'utilisateur_id' => $utilisateurId
                ]);
            }
            
            DB::commit();
            
            Log::info('=== IMPORT PARC RÉUSSI ===', [
                'imported' => $imported,
                'errors' => count($errors),
                'skipped' => $skipped
            ]);
            
            $message = "✅ Import terminé : $imported équipement(s) importé(s) directement dans le parc";
            if (count($errors) > 0) {
                $message .= ", " . count($errors) . " erreur(s)";
                
                return redirect()->route('parc.import.form')
                    ->with('warning', $message)
                    ->with('import_errors', $errors);
            }
            
            return redirect()->route('parc.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('ERREUR IMPORT PARC DATA', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Erreur base de données: ' . $e->getMessage());
        }
    }

    // ==================== MÉTHODES DE SUPPORT PRIVÉES ====================
    
    private function fixCsvFormat($content)
    {
        $content = preg_replace('/^\x{FEFF}/u', '', $content);
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);
        
        $lines = [];
        $currentLine = '';
        $inQuotes = false;
        
        for ($i = 0; $i < strlen($content); $i++) {
            $char = $content[$i];
            $prevChar = $i > 0 ? $content[$i - 1] : '';
            
            if ($char === '"' && $prevChar !== '\\') {
                $inQuotes = !$inQuotes;
                $currentLine .= $char;
            } elseif ($char === "\n" && !$inQuotes) {
                $lines[] = $currentLine;
                $currentLine = '';
            } else {
                $currentLine .= $char;
            }
        }
        
        if ($currentLine !== '') {
            $lines[] = $currentLine;
        }
        
        $fixedLines = [];
        foreach ($lines as $line) {
            if (trim($line) !== '') {
                $fixedLines[] = $line;
            }
        }
        
        return implode("\n", $fixedLines);
    }
    
    private function readCsvFileReliably($filePath)
    {
        $result = ['headers' => [], 'rows' => []];
        $separators = ["\t", ";", ","];
        
        foreach ($separators as $separator) {
            $handle = fopen($filePath, 'r');
            if (!$handle) {
                throw new \Exception('Impossible d\'ouvrir le fichier');
            }
            
            $headers = fgetcsv($handle, 0, $separator);
            
            if ($headers === false || count($headers) < 3) {
                fclose($handle);
                continue;
            }
            
            $headers = array_map(function($header) {
                $header = trim($header, " \t\n\r\0\x0B\"'");
                $header = preg_replace('/^\x{FEFF}/u', '', $header);
                return $header;
            }, $headers);
            
            $rows = [];
            
            while (($row = fgetcsv($handle, 0, $separator)) !== false) {
                if ($row === null || (count($row) === 1 && trim($row[0]) === '')) {
                    continue;
                }
                
                $row = array_map(function($value) {
                    return trim($value, " \t\n\r\0\x0B\"'");
                }, $row);
                
                $isEmpty = true;
                foreach ($row as $value) {
                    if ($value !== '') { $isEmpty = false; break; }
                }
                
                if (!$isEmpty) {
                    if (count($row) < count($headers)) {
                        $row = array_pad($row, count($headers), '');
                    } elseif (count($row) > count($headers)) {
                        $row = array_slice($row, 0, count($headers));
                    }
                    $rows[] = $row;
                }
            }
            
            fclose($handle);
            
            if (count($rows) > 0) {
                $result['headers'] = $headers;
                $result['rows'] = $rows;
                $result['separator'] = $separator;
                break;
            }
        }
        
        if (empty($result['headers'])) {
            $result = $this->readCsvManually($filePath);
        }
        
        return $result;
    }
    
    private function readCsvManually($filePath)
    {
        $content = file_get_contents($filePath);
        $content = $this->fixCsvFormat($content);
        
        $lines = explode("\n", $content);
        $lines = array_filter($lines, function($line) {
            return trim($line) !== '';
        });
        
        if (count($lines) < 2) {
            return ['headers' => [], 'rows' => []];
        }
        
        $firstLine = array_shift($lines);
        $headers = preg_split('/\t/', $firstLine, -1, PREG_SPLIT_NO_EMPTY);
        $headers = array_map(function($header) {
            return trim($header, " \t\n\r\0\x0B\"'");
        }, $headers);
        
        $rows = [];
        foreach ($lines as $line) {
            $row = preg_split('/\t/', $line, -1, PREG_SPLIT_NO_EMPTY);
            
            if (count($row) > 0) {
                $row = array_map(function($value) {
                    return trim($value, " \t\n\r\0\x0B\"'");
                }, $row);
                
                if (count($row) < count($headers)) {
                    $row = array_pad($row, count($headers), '');
                } elseif (count($row) > count($headers)) {
                    $row = array_slice($row, 0, count($headers));
                }
                
                $rows[] = $row;
            }
        }
        
        return ['headers' => $headers, 'rows' => $rows, 'separator' => "\t"];
    }
    
    private function collectSpecificData($data, $headers)
    {
        $specificFields = [];
        
        $excludeFields = [
            'type', 'categorie', 'sous_categorie', 'numero_serie',
            'marque', 'modele', 'garantie', 'date_livraison', 'prix',
            'reference_facture', 'etat', 'fournisseur_id', 'localisation',
            'adresse_mac', 'contrat_maintenance', 'adresse_ip',
            'numero_codification', 'departement', 'poste_staff', 'notes',
            'date_mise_service', 'utilisateur_id', 'date_affectation',
            'notes_affectation', 'poste_affecte'
        ];
        
        foreach ($headers as $header) {
            if (!in_array($header, $excludeFields) && 
                isset($data[$header]) && 
                $data[$header] !== '') {
                $specificFields[$header] = $data[$header];
            }
        }
        
        return $specificFields;
    }
    
    private function parseDate($dateString)
    {
        if (empty($dateString)) return null;
        
        $dateString = trim($dateString);
        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'Y/m/d', 'd.m.Y'];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }
        
        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }
        
        return null;
    }
    
    private function normalizeType($type)
    {
        $type = trim($type);
        
        $map = [
            'reseau' => 'Réseau',
            'reseaux' => 'Réseau',
            'informatique' => 'Informatique',
            'electronique' => 'Électronique',
            'logiciel' => 'Logiciel',
            'serveur' => 'Serveur',
            'peripherique' => 'Périphérique',
        ];
        
        $lower = strtolower($type);
        return $map[$lower] ?? ucfirst($type);
    }

    public function debugCsv(Request $request)
    {
        if (!$request->hasFile('csv_file')) {
            return response()->json(['error' => 'Aucun fichier'], 400);
        }
        
        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        try {
            $result = $this->readCsvFileReliably($path);
            
            return response()->json([
                'status' => 'success',
                'info' => [
                    'nom_fichier' => $file->getClientOriginalName(),
                    'taille' => filesize($path),
                    'colonnes' => count($result['headers']),
                    'lignes_donnees' => count($result['rows']),
                    'separateur' => isset($result['separator']) ? 
                        ($result['separator'] === "\t" ? 'TABULATION' : $result['separator']) : 'inconnu'
                ],
                'en_tetes' => $result['headers'],
                'premiere_ligne_donnees' => $result['rows'][0] ?? [],
                'echantillon' => array_slice($result['rows'], 0, 3)
            ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== EXPORT XLSX ====================

    /**
     * Exporter les données du parc en fichier Excel (.xlsx)
     * Nécessite : composer require phpoffice/phpspreadsheet
     *
     * Paramètres GET optionnels : ?type=Informatique&etat=bon&statut_usage=actif
     */
    public function export(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Parc Informatique');

        // ---- Définition des colonnes [libellé, largeur] ----------------------
        $columns = [
            'A'  => ['ID',                    8],
            'B'  => ['Type',                 14],
            'C'  => ['Numéro de série',       22],
            'D'  => ['Marque',               14],
            'E'  => ['Modèle',               18],
            'F'  => ['Catégorie',            16],
            'G'  => ['Sous-catégorie',       18],
            'H'  => ['État',                 12],
            'I'  => ['Statut',               12],
            'J'  => ['Prix (FCFA)',          16],
            'K'  => ['Garantie',             14],
            'L'  => ['Date livraison',       16],
            'M'  => ['Fournisseur',          20],
            'N'  => ['Adresse MAC',          18],
            'O'  => ['Adresse IP',           16],
            'P'  => ['Réf. facture',         18],
            'Q'  => ['Localisation équip.',  20],
            // --- Données parc ---
            'R'  => ['Utilisateur (compte)', 22],
            'S'  => ['Nom',                  16],
            'T'  => ['Prénom',               16],
            'U'  => ['Position',             16],
            'V'  => ['Département',          18],
            'W'  => ['Poste affecté',        18],
            'X'  => ['Date affectation',     18],
            'Y'  => ['Date retour prévue',   18],
            'Z'  => ['Statut usage',         14],
            'AA' => ['Raison affectation',   22],
            'AB' => ['Détail raison',        30],
            'AC' => ['Localisation parc',    20],
            'AD' => ['Téléphone',            16],
            'AE' => ['Email',                24],
            'AF' => ['Affecté par',          20],
            'AG' => ['N° bon affectation',   22],
            'AH' => ['Notes affectation',    35],
            'AI' => ['Date création',        18],
            'AJ' => ['Dernière modif.',      18],
        ];

        $lastCol  = array_key_last($columns);
        $colKeys  = array_keys($columns);

        // ---- Ligne 1 : titre -------------------------------------------------
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'PARC INFORMATIQUE — Export du ' . now()->format('d/m/Y H:i'));
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // ---- Ligne 2 : en-têtes ----------------------------------------------
        foreach ($columns as $col => [$label, $width]) {
            $sheet->setCellValue("{$col}2", $label);
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // ---- Requête ---------------------------------------------------------
        $query = Equipment::where('statut', 'parc')
            ->with([
                'fournisseur',
                'detail',
                'categorie',
                'parc.utilisateur',
                'parc.affectePar',
            ]);

        if ($request->filled('type'))         $query->where('type', $request->type);
        if ($request->filled('etat'))         $query->where('etat', $request->etat);
        if ($request->filled('statut_usage'))
            $query->whereHas('parc', fn($q) => $q->where('statut_usage', $request->statut_usage));

        $equipments = $query->orderBy('created_at', 'desc')->get();

        // ---- Données ---------------------------------------------------------
        $row = 3;
        foreach ($equipments as $eq) {
            $parc = $eq->parc;
            $util = $parc?->utilisateur;
            $bg   = ($row % 2 === 0) ? 'FFEEF4FB' : 'FFFAFAFA';

            $data = [
                'A'  => $eq->id,
                'B'  => $eq->type,
                'C'  => $eq->numero_serie,
                'D'  => $eq->marque,
                'E'  => $eq->modele,
                'F'  => $eq->detail?->categorie ?? $eq->categorie?->nom ?? '',
                'G'  => $eq->detail?->sous_categorie ?? '',
                'H'  => $eq->etat,
                'I'  => $eq->statut,
                'J'  => $eq->prix,
                'K'  => $eq->garantie,
                'L'  => $eq->date_livraison?->format('d/m/Y'),
                'M'  => $eq->fournisseur?->nom,
                'N'  => $eq->adresse_mac,
                'O'  => $eq->adresse_ip,
                'P'  => $eq->reference_facture,
                'Q'  => $eq->localisation,
                'R'  => $util?->name,
                'S'  => $parc?->utilisateur_nom,
                'T'  => $parc?->utilisateur_prenom,
                'U'  => $parc?->position,
                'V'  => $eq->departement,
                'W'  => $parc?->poste_affecte ?? $eq->poste_staff,
                'X'  => $parc?->date_affectation?->format('d/m/Y'),
                'Y'  => $parc?->date_retour_prevue?->format('d/m/Y'),
                'Z'  => $parc?->statut_usage,
                'AA' => $parc?->affectation_reason,
                'AB' => $parc?->affectation_reason_detail,
                'AC' => $parc?->localisation,
                'AD' => $parc?->telephone,
                'AE' => $parc?->email,
                'AF' => $parc?->affectePar?->name,
                'AG' => $parc?->numero_bon_affectation,
                'AH' => $parc?->notes_affectation,
                'AI' => $eq->created_at?->format('d/m/Y H:i'),
                'AJ' => ($parc?->derniere_modification ?? $eq->updated_at)?->format('d/m/Y H:i'),
            ];

            foreach ($data as $col => $value) {
                $sheet->setCellValue("{$col}{$row}", $value ?? '');
            }

            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'font'      => ['size' => 9, 'name' => 'Arial'],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Format prix
            $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');

            $sheet->getRowDimension($row)->setRowHeight(16);
            $row++;
        }

        // ---- Bordures sur toutes les données ---------------------------------
        if ($row > 3) {
            $sheet->getStyle("A2:{$lastCol}" . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN,   'color' => ['argb' => 'FFBDD7EE']],
                    'outline'    => ['borderStyle' => Border::BORDER_MEDIUM,  'color' => ['argb' => 'FF1F3864']],
                ],
            ]);
        }

        // ---- Ligne de résumé -------------------------------------------------
        $total = count($equipments);
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", "Total : {$total} équipement(s)");
        if ($row > 3) {
            $sheet->setCellValue("J{$row}", "=SUM(J3:J" . ($row - 1) . ")");
            $sheet->getStyle("J{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
        }
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(18);

        // ---- Figer + filtre automatique --------------------------------------
        $sheet->freezePane('A3');
        $sheet->setAutoFilter("A2:{$lastCol}2");

        // ---- Stream du fichier -----------------------------------------------
        $filename = 'export_parc_' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Ancienne méthode de traitement d'import (gardée pour compatibilité)
     */
    public function processImport(Request $request)
    {
        return $this->import($request);
    }
}