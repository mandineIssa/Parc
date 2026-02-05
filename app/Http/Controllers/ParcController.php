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

class ParcController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Récupérer les équipements avec statut "parc" (PAGINÉS)
        $query = Equipment::where('statut', 'parc')
            ->with(['fournisseur', 'parc.utilisateur']);
        
        // Appliquer les filtres
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('numero_serie', 'LIKE', "%{$search}%")
                  ->orWhere('nom', 'LIKE', "%{$search}%")
                  ->orWhere('marque', 'LIKE', "%{$search}%")
                  ->orWhere('modele', 'LIKE', "%{$search}%")
                  ->orWhere('numero_codification', 'LIKE', "%{$search}%")
                  ->orWhereHas('parc.utilisateur', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('etat')) {
            $query->where('etat', $request->input('etat'));
        }
        
        // IMPORTANT: Utiliser paginate() pour avoir ->total(), ->links(), etc.
        $equipments = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Calculer le prix total des équipements filtrés
        // On doit refaire la requête pour éviter les problèmes avec paginate()
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
                  ->orWhereHas('parc.utilisateur', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('type')) {
            $prixTotalQuery->where('type', $request->input('type'));
        }

        if ($request->filled('etat')) {
            $prixTotalQuery->where('etat', $request->input('etat'));
        }
        
        // CORRECTION: Utiliser 'prix' au lieu de 'prix_achat'
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
        
        $request->validate([
            'numero_serie' => 'required|exists:equipment,numero_serie',
            'utilisateur_id' => 'required|exists:users,id',
            'departement' => 'required|string|max:100',
            'poste_affecte' => 'required|string|max:100',
            'date_affectation' => 'required|date',
            'date_retour_prevue' => 'nullable|date|after_or_equal:date_affectation',
            'statut_usage' => 'required|in:en_service,reserve,maintenance',
            'notes_affectation' => 'nullable|string|max:500'
        ]);
        
        // Si le numéro de série change, mettre à jour le statut de l'ancien équipement
        if ($parc->numero_serie != $request->numero_serie) {
            Equipment::where('numero_serie', $parc->numero_serie)
                ->update(['statut' => 'stock']);
        }
        
        // Mettre à jour l'affectation
        $parc->update($request->all());
        
        // Mettre à jour le statut du nouvel équipement
        Equipment::where('numero_serie', $request->numero_serie)
            ->update(['statut' => 'parc']);
        
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
        
        // Écrire les en-têtes avec point-virgule comme séparateur
        fputcsv($file, $headers, ';');
        
        // Ajouter 3 lignes d'exemple
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
            [
                'Logiciel', 'Suite bureautique', 'Standard', 'LIC-MSOFF2024', 'Microsoft', 'Office 365',
                '1 an', '2024-01-01', '500000', 'FACT-2024-003', 'neuf',
                '1', 'Tous sites', '', 'non',
                '', '', '', '',
                '', '', '', '',
                '', '', '', '',
                '', '', '', '',
                '', '', '', '',
                '', '', '', '',
                'Microsoft', '365', 'Abonnement', '50',
                '2025-01-01', 'LIC-MS-OFF365-2024',
                '5', 'Direction', 'Directeur', '2024-01-05', 'Licence direction'
            ]
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
            
            // 1. Lire le fichier COMPLET
            $content = file_get_contents($path);
            
            if (empty(trim($content))) {
                return redirect()->route('parc.import.form')
                    ->with('error', 'Le fichier CSV est vide');
            }
            
            // 2. DÉTECTER ET CORRIGER LE FORMAT - FORCER LE TRAITEMENT TABULATION
            $content = $this->fixCsvFormat($content);
            
            // 3. UTILISER LA MÉTHODE FIABLE avec fgetcsv()
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
            
            // 4. IMPORTER LES DONNÉES DIRECTEMENT DANS LE PARC
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
                $lineNumber = $index + 2; // +2 pour en-têtes + index 0-based
                
                // Créer tableau associatif
                $data = [];
                foreach ($headers as $i => $header) {
                    $value = isset($row[$i]) ? $row[$i] : '';
                    $data[$header] = $value;
                }
                
                // DEBUG: Première ligne
                if ($index === 0) {
                    Log::info('Traitement ligne 1 vers Parc:', [
                        'type' => $data['type'] ?? 'N/A',
                        'numero_serie' => $data['numero_serie'] ?? 'N/A',
                        'utilisateur_id' => $data['utilisateur_id'] ?? 'N/A'
                    ]);
                }
                
                // VALIDATION MINIMALE
                if (empty($data['type'])) {
                    $errors[] = "Ligne $lineNumber: Type manquant";
                    continue;
                }
                
                if (empty($data['numero_serie'])) {
                    $errors[] = "Ligne $lineNumber: Numéro de série manquant";
                    continue;
                }
                
                // Vérifier unicité
                if (Equipment::where('numero_serie', $data['numero_serie'])->exists()) {
                    $errors[] = "Ligne $lineNumber: Numéro de série '{$data['numero_serie']}' existe déjà";
                    continue;
                }
                
                // Fournisseur
                $fournisseurId = null;
                if (!empty($data['fournisseur_id'])) {
                    $supplierId = intval($data['fournisseur_id']);
                    if (Supplier::where('id', $supplierId)->exists()) {
                        $fournisseurId = $supplierId;
                    }
                }
                
                // Utilisateur pour le parc
                $utilisateurId = null;
                $utilisateurInfo = null;
                if (!empty($data['utilisateur_id'])) {
                    $userId = intval($data['utilisateur_id']);
                    $user = User::find($userId);
                    if ($user) {
                        $utilisateurId = $userId;
                        $utilisateurInfo = [
                            'name' => $user->name,
                            'email' => $user->email
                        ];
                    }
                }
                
                // Dates
                $dateLivraison = null;
                if (!empty($data['date_livraison'])) {
                    $dateLivraison = $this->parseDate($data['date_livraison']);
                }
                
                $dateAffectation = now();
                if (!empty($data['date_affectation'])) {
                    $dateAffectation = $this->parseDate($data['date_affectation']);
                }
                
                // Prix
                $prix = 0;
                if (!empty($data['prix'])) {
                    $prix = floatval(str_replace([',', ' '], ['.', ''], $data['prix']));
                }
                
                // État
                $etat = 'neuf';
                if (!empty($data['etat'])) {
                    $etatsValides = ['neuf', 'bon', 'moyen', 'mauvais'];
                    $etatInput = strtolower($data['etat']);
                    if (in_array($etatInput, $etatsValides)) {
                        $etat = $etatInput;
                    }
                }
                
                // Contrat maintenance
                $contratMaintenance = !empty($data['contrat_maintenance']) && 
                    in_array(strtolower($data['contrat_maintenance']), ['1', 'oui', 'yes', 'true']);
                
                // ========== CRÉATION EQUIPEMENT (STATUT PARC DIRECTEMENT) ==========
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
                    'statut' => 'parc', // DIRECTEMENT EN PARC
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
                
                // Champs optionnels
                $optionalFields = ['numero_codification'];
                foreach ($optionalFields as $field) {
                    if (isset($data[$field]) && $data[$field] !== '') {
                        $equipmentData[$field] = $data[$field];
                    }
                }
                
                $equipment = Equipment::create($equipmentData);
                
                // ========== CRÉATION DETAILS ==========
                $detailsData = [
                    'equipment_id' => $equipment->id,
                    'categorie' => $data['categorie'] ?? null,
                    'sous_categorie' => $data['sous_categorie'] ?? null,
                    'contrat_maintenance' => $contratMaintenance,
                ];
                
                // Données spécifiques
                $specificData = $this->collectSpecificData($data, $headers);
                $detailsData['specific_data'] = !empty($specificData) ? json_encode($specificData, JSON_UNESCAPED_UNICODE) : null;
                
                EquipmentDetail::create($detailsData);
                
                // ========== CRÉATION ENREGISTREMENT PARC ==========
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
                
                // Ajouter les infos utilisateur si disponible
                if ($utilisateurInfo) {
                    $parcData['utilisateur_nom'] = $utilisateurInfo['name'];
                    $parcData['utilisateur_email'] = $utilisateurInfo['email'];
                }
                
                Parc::create($parcData);
                
                $imported++;
                
                // Log pour suivi
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
            
            // Message final
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
    
    /**
     * Corrige le format CSV problématique
     */
    private function fixCsvFormat($content)
    {
        // 1. Supprimer BOM UTF-8
        $content = preg_replace('/^\x{FEFF}/u', '', $content);
        
        // 2. Remplacer les retours chariot Windows (\r\n) par \n
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);
        
        // 3. Gérer les guillemets et retours à la ligne à l'intérieur
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
        
        // Dernière ligne
        if ($currentLine !== '') {
            $lines[] = $currentLine;
        }
        
        // Reconstruire avec tabulations propres
        $fixedLines = [];
        foreach ($lines as $line) {
            if (trim($line) !== '') {
                $fixedLines[] = $line;
            }
        }
        
        return implode("\n", $fixedLines);
    }
    
    /**
     * Lit le fichier CSV de manière fiable
     */
    private function readCsvFileReliably($filePath)
    {
        $result = [
            'headers' => [],
            'rows' => []
        ];
        
        // Essayer plusieurs séparateurs
        $separators = ["\t", ";", ","];
        
        foreach ($separators as $separator) {
            Log::info('Essai avec séparateur:', ['separator' => $separator === "\t" ? 'TAB' : $separator]);
            
            $handle = fopen($filePath, 'r');
            if (!$handle) {
                throw new \Exception('Impossible d\'ouvrir le fichier');
            }
            
            // Lire les en-têtes
            $headers = fgetcsv($handle, 0, $separator);
            
            if ($headers === false || count($headers) < 3) {
                fclose($handle);
                Log::warning('Séparateur échoué', ['separator' => $separator]);
                continue;
            }
            
            // Nettoyer les en-têtes
            $headers = array_map(function($header) {
                $header = trim($header, " \t\n\r\0\x0B\"'");
                // Supprimer BOM si présent
                $header = preg_replace('/^\x{FEFF}/u', '', $header);
                return $header;
            }, $headers);
            
            Log::info('En-têtes trouvés avec séparateur ' . ($separator === "\t" ? 'TAB' : $separator), $headers);
            
            // Lire les lignes
            $rows = [];
            $lineNum = 1;
            
            while (($row = fgetcsv($handle, 0, $separator)) !== false) {
                $lineNum++;
                
                // Ignorer les lignes vides
                if ($row === null || (count($row) === 1 && trim($row[0]) === '')) {
                    continue;
                }
                
                // Nettoyer la ligne
                $row = array_map(function($value) {
                    return trim($value, " \t\n\r\0\x0B\"'");
                }, $row);
                
                // Vérifier si la ligne est vide après nettoyage
                $isEmpty = true;
                foreach ($row as $value) {
                    if ($value !== '') {
                        $isEmpty = false;
                        break;
                    }
                }
                
                if (!$isEmpty) {
                    // Ajuster la longueur
                    if (count($row) < count($headers)) {
                        $row = array_pad($row, count($headers), '');
                    } elseif (count($row) > count($headers)) {
                        $row = array_slice($row, 0, count($headers));
                    }
                    
                    $rows[] = $row;
                }
            }
            
            fclose($handle);
            
            // Vérifier si on a des données
            if (count($rows) > 0) {
                Log::info('Séparateur validé!', [
                    'separator' => $separator === "\t" ? 'TAB' : $separator,
                    'lignes' => count($rows)
                ]);
                
                $result['headers'] = $headers;
                $result['rows'] = $rows;
                $result['separator'] = $separator;
                break;
            }
        }
        
        // Si aucun séparateur ne fonctionne, essayer une méthode manuelle
        if (empty($result['headers'])) {
            Log::warning('Aucun séparateur standard ne fonctionne, tentative manuelle...');
            $result = $this->readCsvManually($filePath);
        }
        
        return $result;
    }
    
    /**
     * Lecture manuelle du CSV (dernier recours)
     */
    private function readCsvManually($filePath)
    {
        $content = file_get_contents($filePath);
        $content = $this->fixCsvFormat($content);
        
        // Séparer les lignes
        $lines = explode("\n", $content);
        $lines = array_filter($lines, function($line) {
            return trim($line) !== '';
        });
        
        if (count($lines) < 2) {
            return ['headers' => [], 'rows' => []];
        }
        
        // La première ligne est les en-têtes
        $firstLine = array_shift($lines);
        
        // DIVISER PAR TABULATION manuellement
        $headers = preg_split('/\t/', $firstLine, -1, PREG_SPLIT_NO_EMPTY);
        
        // Nettoyer les en-têtes
        $headers = array_map(function($header) {
            return trim($header, " \t\n\r\0\x0B\"'");
        }, $headers);
        
        Log::info('Lecture manuelle - En-têtes:', $headers);
        
        // Lire les données
        $rows = [];
        foreach ($lines as $line) {
            // Diviser par tabulation
            $row = preg_split('/\t/', $line, -1, PREG_SPLIT_NO_EMPTY);
            
            if (count($row) > 0) {
                // Nettoyer
                $row = array_map(function($value) {
                    return trim($value, " \t\n\r\0\x0B\"'");
                }, $row);
                
                // Ajuster la longueur
                if (count($row) < count($headers)) {
                    $row = array_pad($row, count($headers), '');
                } elseif (count($row) > count($headers)) {
                    $row = array_slice($row, 0, count($headers));
                }
                
                $rows[] = $row;
            }
        }
        
        return [
            'headers' => $headers,
            'rows' => $rows,
            'separator' => "\t"
        ];
    }
    
    /**
     * Collecte les données spécifiques
     */
    private function collectSpecificData($data, $headers)
    {
        $specificFields = [];
        
        // Champs de base à exclure
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
    
    /**
     * Parse une date
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) return null;
        
        $dateString = trim($dateString);
        
        // Formats courants
        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'Y/m/d', 'd.m.Y'];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }
        
        // strtotime
        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }
        
        return null;
    }
    
    /**
     * Normalise le type
     */
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

    /**
     * Debug CSV
     */
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

    /**
     * Exporter les données du parc
     */
    public function export(Request $request)
    {
        $filename = 'export_parc_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function() use ($request) {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes d'export
            $headers = [
                'ID', 'type', 'numero_serie', 'marque', 'modele',
                'garantie', 'date_livraison', 'prix', 'etat', 'statut',
                'fournisseur', 'localisation', 'adresse_mac',
                'categorie', 'sous_categorie',
                'departement', 'poste_staff', 'date_mise_service',
                'utilisateur', 'date_affectation', 'statut_usage', 'notes_affectation',
                'created_at', 'updated_at'
            ];
            
            fputcsv($file, $headers, "\t");
            
            // Données
            $query = Equipment::where('statut', 'parc')
                ->with(['fournisseur', 'detail', 'parc.utilisateur']);
            
            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
            }
            
            $equipments = $query->orderBy('created_at', 'desc')->get();
            
            foreach ($equipments as $equipment) {
                // Récupérer l'utilisateur depuis le parc
                $parcData = $equipment->parc;
                $utilisateur = $parcData ? ($parcData->utilisateur ? $parcData->utilisateur->name : '') : '';
                
                $row = [
                    $equipment->id,
                    $equipment->type,
                    $equipment->numero_serie,
                    $equipment->marque,
                    $equipment->modele,
                    $equipment->garantie,
                    $equipment->date_livraison ? $equipment->date_livraison->format('Y-m-d') : '',
                    $equipment->prix,
                    $equipment->etat,
                    $equipment->statut,
                    $equipment->fournisseur ? $equipment->fournisseur->nom : '',
                    $equipment->localisation,
                    $equipment->adresse_mac,
                    $equipment->detail ? $equipment->detail->categorie : '',
                    $equipment->detail ? $equipment->detail->sous_categorie : '',
                    $equipment->departement,
                    $equipment->poste_staff,
                    $equipment->date_mise_service ? $equipment->date_mise_service->format('Y-m-d') : '',
                    $utilisateur,
                    $parcData ? ($parcData->date_affectation ? $parcData->date_affectation->format('Y-m-d') : '') : '',
                    $parcData ? $parcData->statut_usage : '',
                    $parcData ? $parcData->notes_affectation : '',
                    $equipment->created_at ? $equipment->created_at->format('Y-m-d H:i:s') : '',
                    $equipment->updated_at ? $equipment->updated_at->format('Y-m-d H:i:s') : '',
                ];
                
                fputcsv($file, $row, "\t");
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Ancienne méthode de traitement d'import (gardée pour compatibilité)
     */
    public function processImport(Request $request)
    {
        // Rediriger vers la nouvelle méthode import()
        return $this->import($request);
    }
}