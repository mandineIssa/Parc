<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentDetail;
use App\Models\Stock;
use App\Models\Celer;
use App\Models\Deceler;
use App\Models\Parc;
use App\Models\Maintenance;
use App\Models\HorsService;
use App\Models\Perdu;
use App\Models\Agency;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\HasRolePermissions;

class EquipmentController extends Controller
{
    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $agencies = Agency::all();
        $suppliers = Supplier::all();
        
        return view('equipment.create', compact('agencies', 'suppliers'));
    }
public function showTransition(Equipment $equipment)
{
    return view('equipment.transition', compact('equipment'));
}
    /**
     * Stocker un nouvel équipement (VERSION SIMPLIFIÉE MAIS FONCTIONNELLE)
     */
    
  public function store(Request $request)
{
    DB::beginTransaction();
    
    try {
        // 1. VALIDATION DES DONNÉES DE BASE (pour la table equipment)
        $validated = $request->validate([
            'type' => 'required|in:Réseau,Informatique,Électronique,Logiciel',
            'numero_serie' => 'required|unique:equipment,numero_serie',
            'marque' => 'required_if:type,!=,Logiciel|string|max:255',
            'modele' => 'required_if:type,!=,Logiciel|string|max:255',
            'agency_id' => 'nullable|exists:agencies,id',
            'localisation' => 'required|string|max:255',
            'fournisseur_id' => 'nullable|exists:suppliers,id',
            'date_livraison' => 'required|date',
            'prix' => 'required|numeric|min:0',
            'garantie' => 'required|string|max:100',
            'reference_facture' => 'nullable|string|max:255',
            'etat' => 'required|in:neuf,bon,moyen,mauvais',
            'adresse_mac' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'date_mise_service' => 'nullable|date',
            'date_amortissement' => 'nullable|date',
        ]);
        
        // 2. CRÉATION DE L'ÉQUIPEMENT (table equipment)
        $equipment = Equipment::create($validated);
        
        // 3. CRÉATION DES DÉTAILS (table equipment_details)
        $detailsData = [
            'equipment_id' => $equipment->id,
            'categorie' => $request->categorie, // Stocké ici
            'sous_categorie' => $request->sous_categorie, // Stocké ici
            'contrat_maintenance' => $request->has('contrat_maintenance'),
        ];
        
        // Ajouter les champs spécifiques selon le type
        switch ($request->type) {
            case 'Réseau':
                $detailsData['etat_specifique'] = $request->input('etat_reseau');
                $detailsData['adresse_ip_specifique'] = $request->input('adresse_ip');
                $detailsData['adresse_mac_specifique'] = $request->input('adresse_mac');
                break;
                
            case 'Électronique':
                $detailsData['etat_specifique'] = $request->input('etat_electronique');
                $detailsData['adresse_ip_specifique'] = $request->input('adresse_ip_elec');
                $detailsData['numero_codification_specifique'] = $request->input('numero_codification');
                break;
                
            case 'Informatique':
                $detailsData['etat_specifique'] = $request->input('etat_stock');
                $detailsData['adresse_ip_specifique'] = $request->input('adresse_ip_info');
                $detailsData['adresse_mac_specifique'] = $request->input('adresse_mac_info');
                $detailsData['departement_specifique'] = $request->input('departement');
                $detailsData['poste_staff_specifique'] = $request->input('poste_staff');
                break;
        }
        
        // Contrat maintenance
        if ($request->has('contrat_maintenance')) {
            $detailsData['type_contrat'] = $request->input('type_contrat');
            $detailsData['date_debut_contrat'] = $request->input('date_debut_contrat');
            $detailsData['date_fin_contrat'] = $request->input('date_fin_contrat');
            $detailsData['periodicite_maintenance'] = $request->input('periodicite_maintenance');
        }
        
        // Données spécifiques en JSON
        $specificData = $this->extractSpecificData($request);
        $detailsData['specific_data'] = json_encode($specificData);
        
        EquipmentDetail::create($detailsData);
        
        DB::commit();
        
        return redirect()->route('equipment.index')
            ->with('success', 'Équipement créé avec succès !');
            
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur création équipement: ' . $e->getMessage());
        
        return back()->with('error', 'Erreur: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)  // or public function destroy(Equipment $equipment)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->delete();
        
        return redirect()->route('equipment.index')
            ->with('success', 'Equipment deleted successfully');
    }
    /**
     * Extraire les données spécifiques selon le type d'équipement
     */
private function extractSpecificData(Request $request): array
    {
        $specificData = [];
        
        $allPossibleFields = [
            // Réseau
            'etat_reseau', 'adresse_ip', 'adresse_mac',
            'type_switch', 'ports_ethernet', 'ports_poe', 'puissance_poe_totale',
            'vitesse_ports', 'vlan_supportes', 'firmware_switch',
            'date_mise_service_switch', 'etat_switch', 'responsable_switch',
            'type_routeur', 'nombre_ports_routeur', 'debit_max_routeur',
            'firmware_routeur', 'date_mise_service_routeur', 'etat_routeur',
            'responsable_routeur', 'type_wifi', 'utilisateurs_simultanes_wifi',
            'support_poe_wifi', 'firmware_wifi', 'date_mise_service_wifi',
            'etat_wifi', 'responsable_wifi',
            
            // Électronique
            'etat_electronique', 'adresse_ip_elec', 'numero_codification',
            'type_camera', 'resolution_camera', 'angle_vue', 'zoom_optique',
            'zoom_numerique', 'vision_nocturne', 'adresse_ip_camera',
            'adresse_mac_camera', 'alimentation_camera', 'norme_poe',
            'indice_protection', 'audio_camera', 'emplacement_camera',
            'date_installation_camera', 'etat_detaille_camera', 'responsable_camera',
            
            // Informatique
            'etat_stock', 'adresse_ip_info', 'adresse_mac_info', 'departement',
            'poste_staff', 'processeur', 'frequence_processeur', 'coeurs_threads',
            'ram_capacite', 'type_ram', 'stockage_capacite', 'type_stockage',
            'carte_mere', 'carte_graphique', 'type_graphique', 'systeme_exploitation',
            'ports', 'puissance_alimentation', 'boitier', 'date_mise_service',
            'processeur_portable', 'ram_portable', 'stockage_portable',
            'taille_ecran', 'resolution_ecran', 'carte_graphique_portable',
            'batterie', 'etat_batterie', 'clavier', 'webcam', 'wifi', 'bluetooth',
            'os_portable', 'chargeur', 'date_mise_service_portable',
            'taille_ecran_moniteur', 'resolution_ecran_moniteur', 'type_dalle',
            'frequence_ecran', 'temps_reponse', 'support_reglable',
            'date_mise_service_ecran',
            
            // Logiciel
            'editeur', 'version', 'type_licence', 'nombre_licences',
            'licences_utilisees', 'date_expiration_licence', 'reference_licence',
            'etat_logiciel',
        ];
        
        foreach ($allPossibleFields as $field) {
            if ($request->has($field) && $request->input($field) !== null) {
                $specificData[$field] = $request->input($field);
            }
        }
        
        return $specificData;
    }
    
    /**
     * Préparer TOUTES les données de détails (version simplifiée)
     */
    private function prepareAllDetailsData(Request $request): array
    {
        // Données de base
        $data = [
            'type' => $request->type,
            'categorie' => $request->categorie,
            'sous_categorie' => $request->sous_categorie,
            'contrat_maintenance' => $request->has('contrat_maintenance'),
        ];
        
        // Extraire TOUS les champs spécifiques du formulaire
        $specificData = [];
        
        // Liste de tous les champs possibles (à adapter selon vos besoins)
        $allPossibleFields = [
            // Réseau
            'etat_reseau', 'adresse_ip', 'adresse_mac',
            'type_switch', 'ports_ethernet', 'ports_poe', 'puissance_poe_totale',
            'vitesse_ports', 'vlan_supportes', 'firmware_switch',
            'date_mise_service_switch', 'etat_switch', 'responsable_switch',
            'type_routeur', 'nombre_ports_routeur', 'debit_max_routeur',
            'firmware_routeur', 'date_mise_service_routeur', 'etat_routeur',
            'responsable_routeur', 'type_wifi', 'utilisateurs_simultanes_wifi',
            'support_poe_wifi', 'firmware_wifi', 'date_mise_service_wifi',
            'etat_wifi', 'responsable_wifi',
            
            // Électronique
            'etat_electronique', 'adresse_ip_elec', 'numero_codification',
            'type_camera', 'resolution_camera', 'angle_vue', 'zoom_optique',
            'zoom_numerique', 'vision_nocturne', 'adresse_ip_camera',
            'adresse_mac_camera', 'alimentation_camera', 'norme_poe',
            'indice_protection', 'audio_camera', 'emplacement_camera',
            'date_installation_camera', 'etat_detaille_camera', 'responsable_camera',
            
            // Informatique
            'etat_stock', 'adresse_ip_info', 'adresse_mac_info', 'departement',
            'poste_staff', 'processeur', 'frequence_processeur', 'coeurs_threads',
            'ram_capacite', 'type_ram', 'stockage_capacite', 'type_stockage',
            'carte_mere', 'carte_graphique', 'type_graphique', 'systeme_exploitation',
            'ports', 'puissance_alimentation', 'boitier', 'date_mise_service',
            'processeur_portable', 'ram_portable', 'stockage_portable',
            'taille_ecran', 'resolution_ecran', 'carte_graphique_portable',
            'batterie', 'etat_batterie', 'clavier', 'webcam', 'wifi', 'bluetooth',
            'os_portable', 'chargeur', 'date_mise_service_portable',
            'taille_ecran_moniteur', 'resolution_ecran_moniteur', 'type_dalle',
            'frequence_ecran', 'temps_reponse', 'support_reglable',
            'date_mise_service_ecran',
            
            // Logiciel
            'editeur', 'version', 'type_licence', 'nombre_licences',
            'licences_utilisees', 'date_expiration_licence', 'reference_licence',
            'etat_logiciel',
        ];
        
        // Extraire uniquement les champs qui existent dans la requête
        foreach ($allPossibleFields as $field) {
            if ($request->has($field) && $request->input($field) !== null) {
                $specificData[$field] = $request->input($field);
            }
        }
        
        // Gérer les champs spéciaux (cases à cocher multiples)
        $multiCheckboxFields = [
            'protocoles_switch', 'protocoles_routeur', 'interfaces_routeur',
            'normes_wifi', 'bandes_wifi', 'securite_wifi', 'protocoles_modem',
            'compression_video', 'detection_intelligente',
            'connectiques', 'connectivite', 'formats_supportes'
        ];
        
        foreach ($multiCheckboxFields as $field) {
            if ($request->has($field)) {
                $specificData[$field] = json_encode($request->input($field));
            }
        }
        
        // Contrat maintenance
        if ($request->has('contrat_maintenance')) {
            $data['type_contrat'] = $request->input('type_contrat');
            $data['date_debut_contrat'] = $request->input('date_debut_contrat');
            $data['date_fin_contrat'] = $request->input('date_fin_contrat');
            $data['periodicite_maintenance'] = $request->input('periodicite_maintenance');
        }
        
        $data['specific_data'] = $specificData;
        
        return $data;
    }
    
    /**
     * Afficher la liste des équipements
     */
/**
 * Afficher la liste des équipements
 */
public function index(Request $request)
{
    // Commencez sans la relation details pour tester
    $query = Equipment::with(['agence', 'fournisseur']) // Enlevez 'details' temporairement
        ->orderBy('created_at', 'desc');
    
    // Filtres
    if ($request->filled('statut')) {
        $query->where('statut', $request->statut);
    }
    
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }
    
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('numero_serie', 'like', "%{$search}%")
              ->orWhere('marque', 'like', "%{$search}%")
              ->orWhere('modele', 'like', "%{$search}%")
              ->orWhere('adresse_mac', 'like', "%{$search}%");
        });
    }
    
    $equipments = $query->paginate(20);
    
    return view('equipment.index', compact('equipments'));
}
    
    /**
     * Afficher un équipement
     */
   public function show($id)
    {
        
        try {
            $equipment = Equipment::with([
                'agence', 
                'fournisseur', 
                'detail', // Important: charger les détails
                'stock',
                'parc',
                'maintenance'
            ])->findOrFail($id);
            
            // Récupérer les données spécifiques
            $specificData = [];
            if ($equipment->detail && $equipment->detail->specific_data) {
                $specificData = json_decode($equipment->detail->specific_data, true) ?? [];
            }
            
            return view('equipment.show', compact('equipment', 'specificData'));
            
        } catch (\Exception $e) {
            Log::error('Erreur affichage équipement: ' . $e->getMessage());
            return redirect()->route('equipment.index')
                ->with('error', 'Équipement non trouvé.');
        }
    }
    
    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id)
    {
        try {
            // Récupérer l'équipement avec ses relations
            $equipment = Equipment::with(['agence', 'fournisseur', 'detail'])->findOrFail($id);
            
            // Données pour le formulaire
            $agencies = Agency::all();
            $suppliers = Supplier::all();
            $users = \App\Models\User::all(); // Si nécessaire
            
            // Récupérer les données spécifiques
            $specificData = [];
            if ($equipment->detail && $equipment->detail->specific_data) {
                $specificData = json_decode($equipment->detail->specific_data, true) ?? [];
            }
            
            return view('equipment.edit', compact(
                'equipment', 
                'agencies', 
                'suppliers', 
                'users',
                'specificData'
            ));
            
        } catch (\Exception $e) {
            Log::error('Erreur édition équipement: ' . $e->getMessage());
            return redirect()->route('equipment.index')
                ->with('error', 'Équipement non trouvé ou erreur d\'accès.');
        }
    }
    

    // ==================== TEMPLATE CSV ====================
    public function downloadTemplate()
    {
        $filename = 'template_import_equipements_' . date('Y-m-d') . '.csv';
        
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
            
            // En-têtes EXACTEMENT comme dans votre fichier
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
                'nombre_licences', 'date_expiration_licence', 'reference_licence'
            ];
            
            fputcsv($file, $headers, "\t");
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ==================== IMPORT CSV - VERSION ULTIME ====================
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            
            Log::info('=== DÉBUT IMPORT CSV ===', [
                'fichier' => $file->getClientOriginalName(),
                'taille' => filesize($path) . ' octets'
            ]);
            
            // 1. Lire le fichier COMPLET
            $content = file_get_contents($path);
            
            if (empty(trim($content))) {
                return redirect()->route('equipment.import.form')
                    ->with('error', 'Le fichier CSV est vide');
            }
            
            // 2. DÉTECTER ET CORRIGER LE FORMAT - FORCER LE TRAITEMENT TABULATION
            $content = $this->fixCsvFormat($content);
            
            // 3. UTILISER LA MÉTHODE FIABLE avec fgetcsv()
            $data = $this->readCsvFileReliably($path);
            
            if (empty($data['headers']) || empty($data['rows'])) {
                Log::error('Fichier vide après traitement', $data);
                return redirect()->route('equipment.import.form')
                    ->with('error', 'Aucune donnée valide trouvée dans le fichier');
            }
            
            Log::info('Fichier analysé avec succès', [
                'colonnes' => count($data['headers']),
                'lignes' => count($data['rows'])
            ]);
            
            // 4. IMPORTER LES DONNÉES
            return $this->processImportData($data['headers'], $data['rows']);
            
        } catch (\Exception $e) {
            Log::error('ERREUR IMPORT GLOBALE', [
                'message' => $e->getMessage(),
                'fichier' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('equipment.import.form')
                ->with('error', 'Erreur: ' . $e->getMessage());
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
     * Traite l'import des données
     */
    private function processImportData($headers, $rows)
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
                    Log::info('Traitement ligne 1:', [
                        'type' => $data['type'] ?? 'N/A',
                        'numero_serie' => $data['numero_serie'] ?? 'N/A',
                        'colonnes' => count($data)
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
                
                // Date
                $dateLivraison = null;
                if (!empty($data['date_livraison'])) {
                    $dateLivraison = $this->parseDate($data['date_livraison']);
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
                
                // ========== CRÉATION EQUIPEMENT ==========
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
                    'statut' => 'stock',
                    'fournisseur_id' => $fournisseurId,
                    'localisation' => $data['localisation'] ?? 'Non spécifié',
                    'adresse_mac' => $data['adresse_mac'] ?? null,
                    'adresse_ip' => $data['adresse_ip'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Champs optionnels
                $optionalFields = ['numero_codification', 'departement', 'poste_staff', 'notes'];
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
                
                $imported++;
            }
            
            DB::commit();
            
            Log::info('=== IMPORT RÉUSSI ===', [
                'imported' => $imported,
                'errors' => count($errors),
                'skipped' => $skipped
            ]);
            
            // Message final
            $message = "✅ Import terminé : $imported équipement(s) importé(s)";
            if (count($errors) > 0) {
                $message .= ", " . count($errors) . " erreur(s)";
                
                return redirect()->route('equipment.import.form')
                    ->with('warning', $message)
                    ->with('import_errors', $errors);
            }
            
            return redirect()->route('equipment.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('ERREUR IMPORT DATA', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw new \Exception('Erreur base de données: ' . $e->getMessage());
        }
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
            'date_mise_service'
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

    // ==================== FORMULAIRE IMPORT ====================
    public function showImportForm()
    {
        $suppliers = Supplier::orderBy('nom')->get();
        return view('equipment.import', compact('suppliers'));
    }

    // ==================== EXPORT CSV ====================
    public function export(Request $request)
    {
        $filename = 'export_equipements_' . date('Y-m-d_H-i-s') . '.csv';
        
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
                'garantie', 'date_livraison', 'prix', 'etat',
                'fournisseur', 'localisation', 'adresse_mac',
                'categorie', 'sous_categorie',
                'created_at', 'updated_at'
            ];
            
            fputcsv($file, $headers, "\t");
            
            // Données
            $query = Equipment::with(['fournisseur', 'detail']);
            
            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
            }
            
            $equipments = $query->orderBy('created_at', 'desc')->get();
            
            foreach ($equipments as $equipment) {
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
                    $equipment->fournisseur ? $equipment->fournisseur->nom : '',
                    $equipment->localisation,
                    $equipment->adresse_mac,
                    $equipment->detail ? $equipment->detail->categorie : '',
                    $equipment->detail ? $equipment->detail->sous_categorie : '',
                    $equipment->created_at ? $equipment->created_at->format('Y-m-d H:i:s') : '',
                    $equipment->updated_at ? $equipment->updated_at->format('Y-m-d H:i:s') : '',
                ];
                
                fputcsv($file, $row, "\t");
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ==================== DEBUG CSV ====================
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

    // ==================== VERSION SIMPLE POUR TEST ====================
    public function importTest(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);
        
        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        // Ouvrir le fichier et lire les 5 premières lignes
        $handle = fopen($path, 'r');
        
        if (!$handle) {
            return response()->json(['error' => 'Impossible d\'ouvrir le fichier'], 400);
        }
        
        $lines = [];
        for ($i = 0; $i < 5; $i++) {
            $line = fgets($handle);
            if ($line !== false) {
                $lines[] = $line;
            }
        }
        fclose($handle);
        
        // Afficher ce qui est lu
        return response()->json([
            'raw_lines' => $lines,
            'line_count' => count($lines),
            'first_100_chars' => substr(file_get_contents($path), 0, 100)
        ]);
    }

// ==================== EXPORT COMPLET CSV ====================
public function exportFull(Request $request)
{
    $filename = 'export_equipements_complet_' . date('Y-m-d_H-i-s') . '.csv';
    
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
        
        // En-têtes COMPLETS pour export multi-feuilles
        $headers = [
            // Information de base
            'ID', 'type', 'numero_serie', 'marque', 'modele',
            'garantie', 'date_livraison', 'prix', 'reference_facture', 'etat', 'statut',
            
            // Information fournisseur et localisation
            'fournisseur_id', 'fournisseur_nom', 'localisation', 'departement',
            
            // Information réseau
            'adresse_mac', 'adresse_ip', 'numero_codification',
            
            // Dates
            'date_mise_service', 'created_at', 'updated_at',
            
            // Information catégorie
            'categorie', 'sous_categorie', 'contrat_maintenance',
            
            // Information spécifique
            'processeur', 'ram_capacite', 'stockage_capacite', 'type_stockage',
            'systeme_exploitation', 'editeur', 'version', 'type_licence',
            'nombre_licences', 'date_expiration_licence', 'reference_licence',
            
            // Réseau
            'type_switch', 'ports_ethernet', 'ports_poe', 'vitesse_ports',
            'type_routeur', 'nombre_ports_routeur', 'type_wifi',
            
            // Vidéosurveillance
            'type_camera', 'resolution_camera', 'type_nvr_dvr', 'canaux_supportes',
            
            // Autres
            'type_modem', 'vitesse_max_modem', 'numero_unique_badge',
            'type_alarme', 'taille_ecran', 'type_imprimante', 'vitesse_impression',
            
            // Notes
            'notes', 'poste_staff'
        ];
        
        fputcsv($file, $headers, "\t");
        
        // Données complètes
        $query = Equipment::with(['fournisseur', 'detail']);
        
        // Filtres
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('etat') && $request->etat) {
            $query->where('etat', $request->etat);
        }
        
        if ($request->has('localisation') && $request->localisation) {
            $query->where('localisation', 'like', '%' . $request->localisation . '%');
        }
        
        $equipments = $query->orderBy('created_at', 'desc')->get();
        
        foreach ($equipments as $equipment) {
            // Récupérer les données spécifiques
            $specificData = [];
            if ($equipment->detail && $equipment->detail->specific_data) {
                $specificData = json_decode($equipment->detail->specific_data, true);
            }
            
            $row = [
                // Information de base
                $equipment->id,
                $equipment->type,
                $equipment->numero_serie,
                $equipment->marque,
                $equipment->modele,
                $equipment->garantie,
                $equipment->date_livraison ? $equipment->date_livraison->format('Y-m-d') : '',
                $equipment->prix,
                $equipment->reference_facture,
                $equipment->etat,
                $equipment->statut,
                
                // Information fournisseur et localisation
                $equipment->fournisseur_id,
                $equipment->fournisseur ? $equipment->fournisseur->nom : '',
                $equipment->localisation,
                $equipment->departement,
                
                // Information réseau
                $equipment->adresse_mac,
                $equipment->adresse_ip,
                $equipment->numero_codification,
                
                // Dates
                $equipment->date_mise_service ? $equipment->date_mise_service->format('Y-m-d') : '',
                $equipment->created_at ? $equipment->created_at->format('Y-m-d H:i:s') : '',
                $equipment->updated_at ? $equipment->updated_at->format('Y-m-d H:i:s') : '',
                
                // Information catégorie
                $equipment->detail ? $equipment->detail->categorie : '',
                $equipment->detail ? $equipment->detail->sous_categorie : '',
                $equipment->detail ? ($equipment->detail->contrat_maintenance ? '1' : '0') : '0',
                
                // Information spécifique (extraite de specific_data)
                $specificData['processeur'] ?? '',
                $specificData['ram_capacite'] ?? '',
                $specificData['stockage_capacite'] ?? '',
                $specificData['type_stockage'] ?? '',
                $specificData['systeme_exploitation'] ?? '',
                $specificData['editeur'] ?? '',
                $specificData['version'] ?? '',
                $specificData['type_licence'] ?? '',
                $specificData['nombre_licences'] ?? '',
                $specificData['date_expiration_licence'] ?? '',
                $specificData['reference_licence'] ?? '',
                
                // Réseau
                $specificData['type_switch'] ?? '',
                $specificData['ports_ethernet'] ?? '',
                $specificData['ports_poe'] ?? '',
                $specificData['vitesse_ports'] ?? '',
                $specificData['type_routeur'] ?? '',
                $specificData['nombre_ports_routeur'] ?? '',
                $specificData['type_wifi'] ?? '',
                
                // Vidéosurveillance
                $specificData['type_camera'] ?? '',
                $specificData['resolution_camera'] ?? '',
                $specificData['type_nvr_dvr'] ?? '',
                $specificData['canaux_supportes'] ?? '',
                
                // Autres
                $specificData['type_modem'] ?? '',
                $specificData['vitesse_max_modem'] ?? '',
                $specificData['numero_unique_badge'] ?? '',
                $specificData['type_alarme'] ?? '',
                $specificData['taille_ecran'] ?? '',
                $specificData['type_imprimante'] ?? '',
                $specificData['vitesse_impression'] ?? '',
                
                // Notes
                $equipment->notes,
                $equipment->poste_staff
            ];
            
            fputcsv($file, $row, "\t");
        }
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


}