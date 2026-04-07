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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
     * Stocker un nouvel équipement
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
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
            
            $equipment = Equipment::create($validated);
            
            $detailsData = [
                'equipment_id' => $equipment->id,
                'categorie' => $request->categorie,
                'sous_categorie' => $request->sous_categorie,
                'contrat_maintenance' => $request->has('contrat_maintenance'),
            ];
            
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
            
            if ($request->has('contrat_maintenance')) {
                $detailsData['type_contrat'] = $request->input('type_contrat');
                $detailsData['date_debut_contrat'] = $request->input('date_debut_contrat');
                $detailsData['date_fin_contrat'] = $request->input('date_fin_contrat');
                $detailsData['periodicite_maintenance'] = $request->input('periodicite_maintenance');
            }
            
            $specificData = $this->extractSpecificData($request);
            $detailsData['specific_data'] = json_encode($specificData);
            
            EquipmentDetail::create($detailsData);
            
            DB::commit();
            
            return redirect()->route('equipment.index')
                ->with('success', 'Équipement créé avec succès !');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création équipement: ' . $e->getMessage());
            return back()->with('error', 'Erreur: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $equipment = Equipment::findOrFail($id);
            
            $validated = $request->validate([
                'type' => 'sometimes|required|in:Réseau,Informatique,Électronique,Logiciel',
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
                'categorie' => 'sometimes|string|max:255',
                'sous_categorie' => 'sometimes|string|max:255',
            ]);
            
            $equipmentData = array_filter($validated, function($key) {
                return in_array($key, [
                    'type', 'marque', 'modele', 'agency_id',
                    'localisation', 'fournisseur_id', 'date_livraison', 'prix',
                    'garantie', 'reference_facture', 'etat', 'adresse_mac',
                    'notes', 'date_mise_service', 'date_amortissement'
                ]);
            }, ARRAY_FILTER_USE_KEY);
            
            $equipment->update($equipmentData);
            
            $type = $request->input('type', $equipment->type);
            $existingDetail = $equipment->detail;
            
            $detailsData = [
                'categorie' => $request->input('categorie', $existingDetail->categorie ?? null),
                'sous_categorie' => $request->input('sous_categorie', $existingDetail->sous_categorie ?? null),
                'contrat_maintenance' => $request->has('contrat_maintenance'),
            ];
            
            switch ($type) {
                case 'Réseau':
                    $detailsData['etat_specifique'] = $request->input('etat_reseau', $existingDetail->etat_specifique ?? null);
                    $detailsData['adresse_ip_specifique'] = $request->input('adresse_ip', $existingDetail->adresse_ip_specifique ?? null);
                    $detailsData['adresse_mac_specifique'] = $request->input('adresse_mac', $existingDetail->adresse_mac_specifique ?? null);
                    break;
                case 'Électronique':
                    $detailsData['etat_specifique'] = $request->input('etat_electronique', $existingDetail->etat_specifique ?? null);
                    $detailsData['adresse_ip_specifique'] = $request->input('adresse_ip_elec', $existingDetail->adresse_ip_specifique ?? null);
                    $detailsData['numero_codification_specifique'] = $request->input('numero_codification', $existingDetail->numero_codification_specifique ?? null);
                    break;
                case 'Informatique':
                    $detailsData['etat_specifique'] = $request->input('etat_stock', $existingDetail->etat_specifique ?? null);
                    $detailsData['adresse_ip_specifique'] = $request->input('adresse_ip_info', $existingDetail->adresse_ip_specifique ?? null);
                    $detailsData['adresse_mac_specifique'] = $request->input('adresse_mac_info', $existingDetail->adresse_mac_specifique ?? null);
                    $detailsData['departement_specifique'] = $request->input('departement', $existingDetail->departement_specifique ?? null);
                    $detailsData['poste_staff_specifique'] = $request->input('poste_staff', $existingDetail->poste_staff_specifique ?? null);
                    break;
            }
            
            if ($request->has('contrat_maintenance')) {
                $detailsData['type_contrat'] = $request->input('type_contrat', $existingDetail->type_contrat ?? null);
                $detailsData['date_debut_contrat'] = $request->input('date_debut_contrat', $existingDetail->date_debut_contrat ?? null);
                $detailsData['date_fin_contrat'] = $request->input('date_fin_contrat', $existingDetail->date_fin_contrat ?? null);
                $detailsData['periodicite_maintenance'] = $request->input('periodicite_maintenance', $existingDetail->periodicite_maintenance ?? null);
            } else {
                $detailsData['type_contrat'] = null;
                $detailsData['date_debut_contrat'] = null;
                $detailsData['date_fin_contrat'] = null;
                $detailsData['periodicite_maintenance'] = null;
            }
            
            $existingSpecificData = $existingDetail && $existingDetail->specific_data
                ? json_decode($existingDetail->specific_data, true)
                : [];
            
            $newSpecificData = $this->extractSpecificData($request);
            $mergedSpecificData = array_merge($existingSpecificData, $newSpecificData);
            $detailsData['specific_data'] = json_encode($mergedSpecificData);
            
            $equipment->details()->updateOrCreate(
                ['equipment_id' => $equipment->id],
                $detailsData
            );
            
            DB::commit();
            
            return redirect()->route('equipment.index')
                ->with('success', 'Équipement mis à jour avec succès !');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour équipement: ' . $e->getMessage());
            return back()->with('error', 'Erreur: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $equipment = Equipment::findOrFail($id);

            $approvalIds = DB::table('transition_approvals')
                ->where('equipment_id', $id)
                ->pluck('id');

            DB::table('parc')
                ->whereIn('transition_approval_id', $approvalIds)
                ->update(['transition_approval_id' => null]);

            DB::table('transition_approvals')
                ->whereIn('id', $approvalIds)
                ->delete();

            $equipment->delete();
        });

        return redirect()->route('equipment.index')
            ->with('success', 'Équipement supprimé avec succès');
    }

    private function extractSpecificData(Request $request): array
    {
        $specificData = [];
        
        $allPossibleFields = [
            'etat_reseau', 'adresse_ip', 'adresse_mac',
            'type_switch', 'ports_ethernet', 'ports_poe', 'puissance_poe_totale',
            'vitesse_ports', 'vlan_supportes', 'firmware_switch',
            'date_mise_service_switch', 'etat_switch', 'responsable_switch',
            'type_routeur', 'nombre_ports_routeur', 'debit_max_routeur',
            'firmware_routeur', 'date_mise_service_routeur', 'etat_routeur',
            'responsable_routeur', 'type_wifi', 'utilisateurs_simultanes_wifi',
            'support_poe_wifi', 'firmware_wifi', 'date_mise_service_wifi',
            'etat_wifi', 'responsable_wifi',
            'etat_electronique', 'adresse_ip_elec', 'numero_codification',
            'type_camera', 'resolution_camera', 'angle_vue', 'zoom_optique',
            'zoom_numerique', 'vision_nocturne', 'adresse_ip_camera',
            'adresse_mac_camera', 'alimentation_camera', 'norme_poe',
            'indice_protection', 'audio_camera', 'emplacement_camera',
            'date_installation_camera', 'etat_detaille_camera', 'responsable_camera',
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

    private function prepareAllDetailsData(Request $request): array
    {
        $data = [
            'type' => $request->type,
            'categorie' => $request->categorie,
            'sous_categorie' => $request->sous_categorie,
            'contrat_maintenance' => $request->has('contrat_maintenance'),
        ];
        
        $specificData = [];
        $allPossibleFields = [
            'etat_reseau', 'adresse_ip', 'adresse_mac',
            'type_switch', 'ports_ethernet', 'ports_poe', 'puissance_poe_totale',
            'vitesse_ports', 'vlan_supportes', 'firmware_switch',
            'date_mise_service_switch', 'etat_switch', 'responsable_switch',
            'type_routeur', 'nombre_ports_routeur', 'debit_max_routeur',
            'firmware_routeur', 'date_mise_service_routeur', 'etat_routeur',
            'responsable_routeur', 'type_wifi', 'utilisateurs_simultanes_wifi',
            'support_poe_wifi', 'firmware_wifi', 'date_mise_service_wifi',
            'etat_wifi', 'responsable_wifi',
            'etat_electronique', 'adresse_ip_elec', 'numero_codification',
            'type_camera', 'resolution_camera', 'angle_vue', 'zoom_optique',
            'zoom_numerique', 'vision_nocturne', 'adresse_ip_camera',
            'adresse_mac_camera', 'alimentation_camera', 'norme_poe',
            'indice_protection', 'audio_camera', 'emplacement_camera',
            'date_installation_camera', 'etat_detaille_camera', 'responsable_camera',
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
            'editeur', 'version', 'type_licence', 'nombre_licences',
            'licences_utilisees', 'date_expiration_licence', 'reference_licence',
            'etat_logiciel',
        ];
        
        foreach ($allPossibleFields as $field) {
            if ($request->has($field) && $request->input($field) !== null) {
                $specificData[$field] = $request->input($field);
            }
        }
        
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
        
        if ($request->has('contrat_maintenance')) {
            $data['type_contrat'] = $request->input('type_contrat');
            $data['date_debut_contrat'] = $request->input('date_debut_contrat');
            $data['date_fin_contrat'] = $request->input('date_fin_contrat');
            $data['periodicite_maintenance'] = $request->input('periodicite_maintenance');
        }
        
        $data['specific_data'] = $specificData;
        
        return $data;
    }

    public function index(Request $request)
    {
        $query = Equipment::with(['agence', 'fournisseur'])
            ->orderBy('created_at', 'desc');
        
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
        
        $stats = [
            'stock' => Equipment::where('statut', 'stock')->count(),
            'parc' => Equipment::where('statut', 'parc')->count(),
            'maintenance' => Equipment::where('statut', 'maintenance')->count(),
            'hors_service' => Equipment::where('statut', 'hors_service')->count(),
            'perdu' => Equipment::where('statut', 'perdu')->count(),
        ];
        
        return view('equipment.index', compact('equipments', 'stats'));
    }

    public function show($id)
    {
        try {
            $equipment = Equipment::with([
                'agence', 'fournisseur', 'detail', 'stock', 'parc', 'maintenance'
            ])->findOrFail($id);
            
            $specificData = [];
            if ($equipment->detail && $equipment->detail->specific_data) {
                $specificData = json_decode($equipment->detail->specific_data, true) ?? [];
            }
            
            return view('equipment.show', compact('equipment', 'specificData'));
            
        } catch (\Exception $e) {
            Log::error('Erreur affichage équipement: ' . $e->getMessage());
            return redirect()->route('equipment.index')->with('error', 'Équipement non trouvé.');
        }
    }

    public function edit($id)
    {
        try {
            $equipment = Equipment::with(['agence', 'fournisseur', 'detail'])->findOrFail($id);
            $agencies = Agency::all();
            $suppliers = Supplier::all();
            $users = \App\Models\User::all();
            
            $specificData = [];
            if ($equipment->detail && $equipment->detail->specific_data) {
                $specificData = json_decode($equipment->detail->specific_data, true) ?? [];
            }
            
            return view('equipment.edit', compact('equipment', 'agencies', 'suppliers', 'users', 'specificData'));
            
        } catch (\Exception $e) {
            Log::error('Erreur édition équipement: ' . $e->getMessage());
            return redirect()->route('equipment.index')->with('error', 'Équipement non trouvé ou erreur d\'accès.');
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
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
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

    // ==================== IMPORT CSV ====================
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
            
            $content = file_get_contents($path);
            
            if (empty(trim($content))) {
                return redirect()->route('equipment.import.form')->with('error', 'Le fichier CSV est vide');
            }
            
            $content = $this->fixCsvFormat($content);
            $data = $this->readCsvFileReliably($path);
            
            if (empty($data['headers']) || empty($data['rows'])) {
                return redirect()->route('equipment.import.form')->with('error', 'Aucune donnée valide trouvée dans le fichier');
            }
            
            return $this->processImportData($data['headers'], $data['rows']);
            
        } catch (\Exception $e) {
            Log::error('ERREUR IMPORT GLOBALE', ['message' => $e->getMessage()]);
            return redirect()->route('equipment.import.form')->with('error', 'Erreur: ' . $e->getMessage());
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
            if (!$handle) throw new \Exception('Impossible d\'ouvrir le fichier');
            
            $headers = fgetcsv($handle, 0, $separator);
            
            if ($headers === false || count($headers) < 3) {
                fclose($handle);
                continue;
            }
            
            $headers = array_map(function($header) {
                $header = trim($header, " \t\n\r\0\x0B\"'");
                return preg_replace('/^\x{FEFF}/u', '', $header);
            }, $headers);
            
            $rows = [];
            
            while (($row = fgetcsv($handle, 0, $separator)) !== false) {
                if ($row === null || (count($row) === 1 && trim($row[0]) === '')) continue;
                
                $row = array_map(fn($v) => trim($v, " \t\n\r\0\x0B\"'"), $row);
                
                $isEmpty = empty(array_filter($row, fn($v) => $v !== ''));
                
                if (!$isEmpty) {
                    if (count($row) < count($headers)) $row = array_pad($row, count($headers), '');
                    elseif (count($row) > count($headers)) $row = array_slice($row, 0, count($headers));
                    $rows[] = $row;
                }
            }
            
            fclose($handle);
            
            if (count($rows) > 0) {
                $result = ['headers' => $headers, 'rows' => $rows, 'separator' => $separator];
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
        
        $lines = array_filter(explode("\n", $content), fn($l) => trim($l) !== '');
        
        if (count($lines) < 2) return ['headers' => [], 'rows' => []];
        
        $firstLine = array_shift($lines);
        $headers = array_map(
            fn($h) => trim($h, " \t\n\r\0\x0B\"'"),
            preg_split('/\t/', $firstLine, -1, PREG_SPLIT_NO_EMPTY)
        );
        
        $rows = [];
        foreach ($lines as $line) {
            $row = array_map(
                fn($v) => trim($v, " \t\n\r\0\x0B\"'"),
                preg_split('/\t/', $line, -1, PREG_SPLIT_NO_EMPTY)
            );
            
            if (count($row) > 0) {
                if (count($row) < count($headers)) $row = array_pad($row, count($headers), '');
                elseif (count($row) > count($headers)) $row = array_slice($row, 0, count($headers));
                $rows[] = $row;
            }
        }
        
        return ['headers' => $headers, 'rows' => $rows, 'separator' => "\t"];
    }

    private function processImportData($headers, $rows)
    {
        $imported = 0;
        $errors = [];
        
        DB::beginTransaction();
        
        try {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2;
                $data = [];
                foreach ($headers as $i => $header) {
                    $data[$header] = $row[$i] ?? '';
                }
                
                if (empty($data['type'])) { $errors[] = "Ligne $lineNumber: Type manquant"; continue; }
                if (empty($data['numero_serie'])) { $errors[] = "Ligne $lineNumber: Numéro de série manquant"; continue; }
                
                if (Equipment::where('numero_serie', $data['numero_serie'])->exists()) {
                    $errors[] = "Ligne $lineNumber: Numéro de série '{$data['numero_serie']}' existe déjà";
                    continue;
                }
                
                $fournisseurId = null;
                if (!empty($data['fournisseur_id'])) {
                    $sid = intval($data['fournisseur_id']);
                    if (Supplier::where('id', $sid)->exists()) $fournisseurId = $sid;
                }
                
                $dateLivraison = !empty($data['date_livraison']) ? $this->parseDate($data['date_livraison']) : null;
                $prix = !empty($data['prix']) ? floatval(str_replace([',', ' '], ['.', ''], $data['prix'])) : 0;
                
                $etat = 'neuf';
                if (!empty($data['etat']) && in_array(strtolower($data['etat']), ['neuf', 'bon', 'moyen', 'mauvais'])) {
                    $etat = strtolower($data['etat']);
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
                    'statut' => 'stock',
                    'fournisseur_id' => $fournisseurId,
                    'localisation' => $data['localisation'] ?? 'Non spécifié',
                    'adresse_mac' => $data['adresse_mac'] ?? null,
                    'adresse_ip' => $data['adresse_ip'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                foreach (['numero_codification', 'departement', 'poste_staff', 'notes'] as $field) {
                    if (isset($data[$field]) && $data[$field] !== '') $equipmentData[$field] = $data[$field];
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
                $imported++;
            }
            
            DB::commit();
            
            $message = "✅ Import terminé : $imported équipement(s) importé(s)";
            if (count($errors) > 0) {
                $message .= ", " . count($errors) . " erreur(s)";
                return redirect()->route('equipment.import.form')
                    ->with('warning', $message)->with('import_errors', $errors);
            }
            
            return redirect()->route('equipment.index')->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ERREUR IMPORT DATA', ['message' => $e->getMessage()]);
            throw new \Exception('Erreur base de données: ' . $e->getMessage());
        }
    }

    private function collectSpecificData($data, $headers)
    {
        $excludeFields = [
            'type', 'categorie', 'sous_categorie', 'numero_serie',
            'marque', 'modele', 'garantie', 'date_livraison', 'prix',
            'reference_facture', 'etat', 'fournisseur_id', 'localisation',
            'adresse_mac', 'contrat_maintenance', 'adresse_ip',
            'numero_codification', 'departement', 'poste_staff', 'notes',
            'date_mise_service'
        ];
        
        $specificFields = [];
        foreach ($headers as $header) {
            if (!in_array($header, $excludeFields) && isset($data[$header]) && $data[$header] !== '') {
                $specificFields[$header] = $data[$header];
            }
        }
        
        return $specificFields;
    }

    private function parseDate($dateString)
    {
        if (empty($dateString)) return null;
        $dateString = trim($dateString);
        
        foreach (['d/m/Y', 'Y-m-d', 'd-m-Y', 'Y/m/d', 'd.m.Y'] as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) return $date->format('Y-m-d');
        }
        
        $timestamp = strtotime($dateString);
        return $timestamp !== false ? date('Y-m-d', $timestamp) : null;
    }

    private function normalizeType($type)
    {
        $map = [
            'reseau' => 'Réseau', 'reseaux' => 'Réseau',
            'informatique' => 'Informatique', 'electronique' => 'Électronique',
            'logiciel' => 'Logiciel', 'serveur' => 'Serveur',
            'peripherique' => 'Périphérique',
        ];
        return $map[strtolower(trim($type))] ?? ucfirst(trim($type));
    }

    public function showImportForm()
    {
        $suppliers = Supplier::orderBy('nom')->get();
        return view('equipment.import', compact('suppliers'));
    }

    // ==================== EXPORT XLSX ====================

    /**
     * Export standard — tous les équipements (tous statuts)
     * Nécessite : composer require phpoffice/phpspreadsheet
     *
     * Paramètres GET optionnels : ?type=Réseau&etat=bon&statut=stock
     */
    public function export(Request $request)
    {
        return $this->buildEquipmentXlsx($request, false);
    }

    /**
     * Export complet — tous champs + données specific_data dépliées
     * Paramètres GET optionnels : ?type=Réseau&etat=bon&localisation=Dakar
     */
    public function exportFull(Request $request)
    {
        return $this->buildEquipmentXlsx($request, true);
    }

    /**
     * Construit et retourne le fichier xlsx.
     * $full = false → colonnes de base | $full = true → toutes colonnes + specific_data
     */
    private function buildEquipmentXlsx(Request $request, bool $full)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($full ? 'Équipements Complet' : 'Équipements');

        // ---- Colonnes communes ----------------------------------------------
        $columns = [
            'A'  => ['ID',                    8],
            'B'  => ['Type',                 14],
            'C'  => ['Numéro de série',       22],
            'D'  => ['Marque',               14],
            'E'  => ['Modèle',               18],
            'F'  => ['Catégorie',            16],
            'G'  => ['Sous-catégorie',       18],
            'H'  => ['Nom',                  18],
            'I'  => ['N° codification',      20],
            'J'  => ['État',                 12],
            'K'  => ['Statut',               14],
            'L'  => ['Prix (FCFA)',          16],
            'M'  => ['Date livraison',       16],
            'N'  => ['Garantie',             14],
            'O'  => ['Réf. facture',         18],
            'P'  => ['Réf. installation',    20],
            'Q'  => ['Fournisseur',          20],
            'R'  => ['Agence',               16],
            'S'  => ['Localisation',         18],
            'T'  => ['Lieu stockage',        18],
            'U'  => ['Adresse MAC',          18],
            'V'  => ['Adresse IP',           16],
            'W'  => ['Département',          18],
            'X'  => ['Poste staff',          18],
            'Y'  => ['Date mise en service', 20],
            'Z'  => ['Date amortissement',   20],
            'AA' => ['Contrat maintenance',  20],
            'AB' => ['Notes',                35],
            'AC' => ['Date création',        18],
            'AD' => ['Dernière modif.',      18],
        ];

        // ---- Colonnes supplémentaires pour l'export complet -----------------
        $extraColumns = [];
        if ($full) {
            $extraColumns = [
                'AE' => ['Processeur',           18],
                'AF' => ['RAM',                  14],
                'AG' => ['Stockage capacité',    18],
                'AH' => ['Type stockage',        16],
                'AI' => ['Système exploit.',     18],
                'AJ' => ['Taille écran',         14],
                'AK' => ['Éditeur',              16],
                'AL' => ['Version',              14],
                'AM' => ['Type licence',         16],
                'AN' => ['Nb licences',          14],
                'AO' => ['Exp. licence',         16],
                'AP' => ['Réf. licence',         20],
                'AQ' => ['Type switch',          16],
                'AR' => ['Ports Ethernet',       16],
                'AS' => ['Ports PoE',            14],
                'AT' => ['Vitesse ports',        14],
                'AU' => ['Type routeur',         16],
                'AV' => ['Nb ports routeur',     16],
                'AW' => ['Type WiFi',            14],
                'AX' => ['Type caméra',          16],
                'AY' => ['Résolution caméra',    18],
                'AZ' => ['Type NVR/DVR',         16],
                'BA' => ['Canaux supportés',     16],
                'BB' => ['Type modem',           14],
                'BC' => ['Vitesse modem',        14],
                'BD' => ['N° badge',             16],
                'BE' => ['Type alarme',          14],
                'BF' => ['Type imprimante',      16],
                'BG' => ['Vitesse impression',   18],
            ];
            $columns = array_merge($columns, $extraColumns);
        }

        $lastCol = array_key_last($columns);
        $colKeys  = array_keys($columns);

        // ---- Ligne 1 : titre ------------------------------------------------
        $label = $full ? 'INVENTAIRE ÉQUIPEMENTS (COMPLET)' : 'INVENTAIRE ÉQUIPEMENTS';
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', $label . ' — Export du ' . now()->format('d/m/Y H:i'));
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // ---- Ligne 2 : en-têtes ---------------------------------------------
        foreach ($columns as $col => [$lbl, $width]) {
            $sheet->setCellValue("{$col}2", $lbl);
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Arial'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // ---- Requête --------------------------------------------------------
        $query = Equipment::with(['fournisseur', 'detail', 'categorie', 'agence']);

        if ($request->filled('type'))        $query->where('type', $request->type);
        if ($request->filled('etat'))        $query->where('etat', $request->etat);
        if ($request->filled('statut'))      $query->where('statut', $request->statut);
        if ($request->filled('localisation'))
            $query->where('localisation', 'like', '%' . $request->localisation . '%');

        $equipments = $query->orderBy('created_at', 'desc')->get();

        // ---- Données --------------------------------------------------------
        $row = 3;
        foreach ($equipments as $eq) {
            $bg = ($row % 2 === 0) ? 'FFEEF4FB' : 'FFFAFAFA';

            $sd = [];
            if ($eq->detail?->specific_data) {
                $sd = json_decode($eq->detail->specific_data, true) ?? [];
            }

            $data = [
                'A'  => $eq->id,
                'B'  => $eq->type,
                'C'  => $eq->numero_serie,
                'D'  => $eq->marque,
                'E'  => $eq->modele,
                'F'  => $eq->detail?->categorie ?? $eq->categorie?->nom ?? '',
                'G'  => $eq->detail?->sous_categorie ?? '',
                'H'  => $eq->nom,
                'I'  => $eq->numero_codification,
                'J'  => $eq->etat,
                'K'  => $eq->statut,
                'L'  => $eq->prix,
                'M'  => $eq->date_livraison?->format('d/m/Y'),
                'N'  => $eq->garantie,
                'O'  => $eq->reference_facture,
                'P'  => $eq->reference_installation,
                'Q'  => $eq->fournisseur?->nom,
                'R'  => $eq->agence?->nom ?? $eq->agence?->name ?? '',
                'S'  => $eq->localisation,
                'T'  => $eq->lieu_stockage,
                'U'  => $eq->adresse_mac,
                'V'  => $eq->adresse_ip,
                'W'  => $eq->departement,
                'X'  => $eq->poste_staff,
                'Y'  => $eq->date_mise_service?->format('d/m/Y'),
                'Z'  => $eq->date_amortissement?->format('d/m/Y'),
                'AA' => $eq->detail ? ($eq->detail->contrat_maintenance ? 'Oui' : 'Non') : '',
                'AB' => $eq->notes,
                'AC' => $eq->created_at?->format('d/m/Y H:i'),
                'AD' => $eq->updated_at?->format('d/m/Y H:i'),
            ];

            if ($full) {
                $data = array_merge($data, [
                    'AE' => $sd['processeur'] ?? '',
                    'AF' => $sd['ram_capacite'] ?? '',
                    'AG' => $sd['stockage_capacite'] ?? '',
                    'AH' => $sd['type_stockage'] ?? '',
                    'AI' => $sd['systeme_exploitation'] ?? $sd['os_portable'] ?? '',
                    'AJ' => $sd['taille_ecran'] ?? $sd['taille_ecran_moniteur'] ?? '',
                    'AK' => $sd['editeur'] ?? '',
                    'AL' => $sd['version'] ?? '',
                    'AM' => $sd['type_licence'] ?? '',
                    'AN' => $sd['nombre_licences'] ?? '',
                    'AO' => $sd['date_expiration_licence'] ?? '',
                    'AP' => $sd['reference_licence'] ?? '',
                    'AQ' => $sd['type_switch'] ?? '',
                    'AR' => $sd['ports_ethernet'] ?? '',
                    'AS' => $sd['ports_poe'] ?? '',
                    'AT' => $sd['vitesse_ports'] ?? '',
                    'AU' => $sd['type_routeur'] ?? '',
                    'AV' => $sd['nombre_ports_routeur'] ?? '',
                    'AW' => $sd['type_wifi'] ?? '',
                    'AX' => $sd['type_camera'] ?? '',
                    'AY' => $sd['resolution_camera'] ?? '',
                    'AZ' => $sd['type_nvr_dvr'] ?? '',
                    'BA' => $sd['canaux_supportes'] ?? '',
                    'BB' => $sd['type_modem'] ?? '',
                    'BC' => $sd['vitesse_max_modem'] ?? '',
                    'BD' => $sd['numero_unique_badge'] ?? '',
                    'BE' => $sd['type_alarme'] ?? '',
                    'BF' => $sd['type_imprimante'] ?? '',
                    'BG' => $sd['vitesse_impression'] ?? '',
                ]);
            }

            foreach ($data as $col => $value) {
                $sheet->setCellValue("{$col}{$row}", $value ?? '');
            }

            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'font'      => ['size' => 9, 'name' => 'Arial'],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $sheet->getStyle("L{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
            $sheet->getRowDimension($row)->setRowHeight(16);
            $row++;
        }

        // ---- Bordures -------------------------------------------------------
        if ($row > 3) {
            $sheet->getStyle("A2:{$lastCol}" . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN,  'color' => ['argb' => 'FFBDD7EE']],
                    'outline'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF1F3864']],
                ],
            ]);
        }

        // ---- Ligne résumé ---------------------------------------------------
        $total = count($equipments);
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", "Total : {$total} équipement(s)");
        if ($row > 3) {
            $sheet->setCellValue("L{$row}", "=SUM(L3:L" . ($row - 1) . ")");
            $sheet->getStyle("L{$row}")->getNumberFormat()->setFormatCode('#,##0 "FCFA"');
        }
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial', 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(18);

        // ---- Figer + filtre -------------------------------------------------
        $sheet->freezePane('A3');
        $sheet->setAutoFilter("A2:{$lastCol}2");

        // ---- Stream ---------------------------------------------------------
        $suffix   = $full ? 'complet_' : '';
        $filename = "export_equipements_{$suffix}" . now()->format('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
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
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function importTest(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:10240']);
        
        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        
        if (!$handle) return response()->json(['error' => 'Impossible d\'ouvrir le fichier'], 400);
        
        $lines = [];
        for ($i = 0; $i < 5; $i++) {
            $line = fgets($handle);
            if ($line !== false) $lines[] = $line;
        }
        fclose($handle);
        
        return response()->json([
            'raw_lines' => $lines,
            'line_count' => count($lines),
            'first_100_chars' => substr(file_get_contents($path), 0, 100)
        ]);
    }
}