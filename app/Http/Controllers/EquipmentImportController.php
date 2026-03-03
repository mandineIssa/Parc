<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentDetail;
use App\Models\Stock;
use App\Models\Parc;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class EquipmentImportController extends Controller
{
    /**
     * Affiche le formulaire d'importation
     */
    public function showImportForm()
    {
        return view('equipment.imports');
    }

    /**
     * Télécharge le template Excel
     */
    public function downloadTemplate()
    {
        $filePath = storage_path('app/templates/Template_Import_Equipements.xlsx');

        if (!file_exists($filePath)) {
            return back()->with('error', 'Template non trouvé.');
        }

        return response()->download($filePath, 'Template_Import_Equipements.xlsx');
    }

    /**
     * Importe les données depuis le fichier Excel
     * — Vide entièrement les 4 tables AVANT la transaction (truncate = commit implicite MySQL)
     * — Puis insère toutes les lignes du fichier Excel dans une transaction
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240'
        ]);

        try {
            $file        = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());

            // ── TRUNCATE AVANT LA TRANSACTION ─────────────────────
            // En MySQL, TRUNCATE provoque un commit implicite.
            // Il doit donc être exécuté AVANT beginTransaction().
    /*         DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('parc')->truncate();
            DB::table('stock')->truncate();
            DB::table('equipment_details')->truncate();
            DB::table('equipment')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1'); */
            // ──────────────────────────────────────────────────────

            DB::beginTransaction();

            $stats = [
                'equipment_imported'         => 0,
                'equipment_details_imported' => 0,
                'stock_imported'             => 0,
                'parc_imported'              => 0,
                'errors'                     => []
            ];

            $equipmentSheet = $spreadsheet->getSheetByName('EQUIPMENT');
            if ($equipmentSheet) {
                $stats['equipment_imported'] = $this->importEquipment($equipmentSheet, $stats);
            }

            $detailsSheet = $spreadsheet->getSheetByName('EQUIPMENT_DETAILS');
            if ($detailsSheet) {
                $stats['equipment_details_imported'] = $this->importEquipmentDetails($detailsSheet, $stats);
            }

            $stockSheet = $spreadsheet->getSheetByName('STOCK');
            if ($stockSheet) {
                $stats['stock_imported'] = $this->importStock($stockSheet, $stats);
            }

            $parcSheet = $spreadsheet->getSheetByName('PARC');
            if ($parcSheet) {
                $stats['parc_imported'] = $this->importParc($parcSheet, $stats);
            }

            DB::commit();

            if (count($stats['errors']) > 0) {
                return back()->with([
                    'warning' => 'Importation terminée avec des avertissements.',
                    'stats'   => $stats
                ]);
            }

            return back()->with([
                'success' => 'Importation réussie !',
                'stats'   => $stats
            ]);

        } catch (Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Erreur importation: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // IMPORT EQUIPMENT
    // ─────────────────────────────────────────────────────────────────
    private function importEquipment($sheet, &$stats)
    {
        $count      = 0;
        $highestRow = $sheet->getHighestRow();

        for ($row = 3; $row <= $highestRow; $row++) {
            try {
                $numeroSerie = $this->getCellValue($sheet, "A{$row}");

                if (empty($numeroSerie)) {
                    continue;
                }

                $equipment                         = new Equipment();
                $equipment->numero_serie           = $numeroSerie;
                $equipment->agency_id              = $this->validateForeignKey($this->getCellValue($sheet, "B{$row}"), 'App\Models\Agency');
                $equipment->localisation           = $this->getCellValue($sheet, "C{$row}");
                $equipment->type                   = $this->getCellValue($sheet, "D{$row}");
                $equipment->categorie_id           = $this->validateForeignKey($this->getCellValue($sheet, "E{$row}"), 'App\Models\Category');
                $equipment->nom                    = $this->getCellValue($sheet, "F{$row}");
                $equipment->modele                 = $this->getCellValue($sheet, "G{$row}");
                $equipment->marque                 = $this->getCellValue($sheet, "H{$row}");
                $equipment->numero_codification    = $this->getCellValue($sheet, "I{$row}");
                $equipment->adresse_mac            = $this->getCellValue($sheet, "J{$row}");
                $equipment->adresse_ip             = $this->getCellValue($sheet, "K{$row}");
                $equipment->fournisseur_id         = $this->validateForeignKey($this->getCellValue($sheet, "L{$row}"), 'App\Models\Supplier');
                $equipment->date_livraison         = $this->getDateValue($sheet, "M{$row}");
                $equipment->prix                   = $this->getCellValue($sheet, "N{$row}");
                $equipment->garantie               = $this->getCellValue($sheet, "O{$row}");
                $equipment->reference_facture      = $this->getCellValue($sheet, "P{$row}");
                $equipment->reference_installation = $this->getCellValue($sheet, "Q{$row}");
                $equipment->etat                   = $this->getCellValue($sheet, "R{$row}");
                $equipment->lieu_stockage          = $this->getCellValue($sheet, "S{$row}");
                $equipment->notes                  = $this->getCellValue($sheet, "T{$row}");
                $equipment->statut                 = $this->getCellValue($sheet, "U{$row}", 'stock');
                $equipment->departement            = $this->getCellValue($sheet, "V{$row}");
                $equipment->poste_staff            = $this->getCellValue($sheet, "W{$row}");
                $equipment->date_mise_service      = $this->getDateValue($sheet, "X{$row}");
                $equipment->date_amortissement     = $this->getDateValue($sheet, "Y{$row}");

                $equipment->save();
                $count++;

            } catch (Exception $e) {
                $stats['errors'][] = "Ligne {$row} (EQUIPMENT): " . $e->getMessage();
            }
        }

        return $count;
    }

    // ─────────────────────────────────────────────────────────────────
    // IMPORT EQUIPMENT_DETAILS
    // ─────────────────────────────────────────────────────────────────
    private function importEquipmentDetails($sheet, &$stats)
    {
        $count      = 0;
        $highestRow = $sheet->getHighestRow();

        for ($row = 3; $row <= $highestRow; $row++) {
            try {
                $numeroSerie = $this->getCellValue($sheet, "A{$row}");

                if (empty($numeroSerie)) {
                    continue;
                }

                $equipment = Equipment::where('numero_serie', $numeroSerie)->first();
                if (!$equipment) {
                    $stats['errors'][] = "Ligne {$row} (EQUIPMENT_DETAILS): Équipement {$numeroSerie} non trouvé";
                    continue;
                }

                $contratValue = strtolower($this->getCellValue($sheet, "J{$row}", 'non'));
                $specificData = $this->getCellValue($sheet, "O{$row}");
                if (!empty($specificData)) {
                    $decoded      = json_decode($specificData, true);
                    $specificData = $decoded ?: [];
                } else {
                    $specificData = [];
                }

                $detail                                 = new EquipmentDetail();
                $detail->equipment_id                   = $equipment->id;
                $detail->categorie                      = $this->getCellValue($sheet, "B{$row}");
                $detail->sous_categorie                 = $this->getCellValue($sheet, "C{$row}");
                $detail->etat_specifique                = $this->getCellValue($sheet, "D{$row}");
                $detail->adresse_ip_specifique          = $this->getCellValue($sheet, "E{$row}");
                $detail->adresse_mac_specifique         = $this->getCellValue($sheet, "F{$row}");
                $detail->departement_specifique         = $this->getCellValue($sheet, "G{$row}");
                $detail->poste_staff_specifique         = $this->getCellValue($sheet, "H{$row}");
                $detail->numero_codification_specifique = $this->getCellValue($sheet, "I{$row}");
                $detail->contrat_maintenance            = in_array($contratValue, ['oui', '1', 'true', 'yes']);
                $detail->type_contrat                   = $this->getCellValue($sheet, "K{$row}");
                $detail->date_debut_contrat             = $this->getDateValue($sheet, "L{$row}");
                $detail->date_fin_contrat               = $this->getDateValue($sheet, "M{$row}");
                $detail->periodicite_maintenance        = $this->getCellValue($sheet, "N{$row}");
                $detail->specific_data                  = $specificData;

                $detail->save();
                $count++;

            } catch (Exception $e) {
                $stats['errors'][] = "Ligne {$row} (EQUIPMENT_DETAILS): " . $e->getMessage();
            }
        }

        return $count;
    }

    // ─────────────────────────────────────────────────────────────────
    // IMPORT STOCK
    // ─────────────────────────────────────────────────────────────────
    private function importStock($sheet, &$stats)
    {
        $count      = 0;
        $highestRow = $sheet->getHighestRow();

        for ($row = 3; $row <= $highestRow; $row++) {
            try {
                $numeroSerie = $this->getCellValue($sheet, "A{$row}");

                if (empty($numeroSerie)) {
                    continue;
                }

                $equipment = Equipment::where('numero_serie', $numeroSerie)->first();
                if (!$equipment) {
                    $stats['errors'][] = "Ligne {$row} (STOCK): Équipement {$numeroSerie} non trouvé";
                    continue;
                }

                $stock                        = new Stock();
                $stock->numero_serie          = $numeroSerie;
                $stock->type_stock            = $this->getCellValue($sheet, "B{$row}");
                $stock->localisation_physique = $this->getCellValue($sheet, "C{$row}");
                $stock->etat                  = $this->getCellValue($sheet, "D{$row}", 'disponible');
                $stock->quantite              = $this->getCellValue($sheet, "E{$row}", 1);
                $stock->date_entree           = $this->getDateValue($sheet, "F{$row}");
                $stock->date_sortie           = $this->getDateValue($sheet, "G{$row}");
                $stock->observations          = $this->getCellValue($sheet, "H{$row}");

                $stock->save();
                $count++;

            } catch (Exception $e) {
                $stats['errors'][] = "Ligne {$row} (STOCK): " . $e->getMessage();
            }
        }

        return $count;
    }

    // ─────────────────────────────────────────────────────────────────
    // IMPORT PARC
    // DB::table() pour contourner le boot() du modèle Parc
    // qui régénèrerait numero_bon_affectation à chaque creating
    // ─────────────────────────────────────────────────────────────────
    private function importParc($sheet, &$stats)
    {
        $count      = 0;
        $highestRow = $sheet->getHighestRow();

        for ($row = 3; $row <= $highestRow; $row++) {
            try {
                $numeroSerie = $this->getCellValue($sheet, "A{$row}");

                if (empty($numeroSerie)) {
                    continue;
                }

                $equipment = Equipment::where('numero_serie', $numeroSerie)->first();
                if (!$equipment) {
                    $stats['errors'][] = "Ligne {$row} (PARC): Équipement {$numeroSerie} non trouvé";
                    continue;
                }

                DB::table('parc')->insert([
                    'numero_serie'              => $numeroSerie,
                    'utilisateur_id'            => $this->validateForeignKey($this->getCellValue($sheet, "B{$row}"), 'App\Models\User'),
                    'utilisateur_nom'           => $this->getCellValue($sheet, "C{$row}"),
                    'utilisateur_prenom'        => $this->getCellValue($sheet, "D{$row}"),
                    'departement'               => $this->getCellValue($sheet, "E{$row}"),
                    'poste_affecte'             => $this->getCellValue($sheet, "F{$row}"),
                    'position'                  => $this->getCellValue($sheet, "G{$row}"),
                    'date_affectation'          => $this->getDateValue($sheet, "H{$row}"),
                    'date_retour_prevue'        => $this->getDateValue($sheet, "I{$row}"),
                    'affectation_reason'        => $this->getCellValue($sheet, "J{$row}"),
                    'affectation_reason_detail' => $this->getCellValue($sheet, "K{$row}"),
                    'localisation'              => $this->getCellValue($sheet, "L{$row}"),
                    'telephone'                 => $this->getCellValue($sheet, "M{$row}"),
                    'email'                     => $this->getCellValue($sheet, "N{$row}"),
                    'statut_usage'              => $this->getCellValue($sheet, "O{$row}", 'actif'),
                    'notes_affectation'         => $this->getCellValue($sheet, "P{$row}"),
                    'affecte_par'               => $this->validateForeignKey($this->getCellValue($sheet, "Q{$row}"), 'App\Models\User'),
                    'derniere_modification'     => now(),
                    'numero_bon_affectation'    => $this->getCellValue($sheet, "S{$row}"),
                    'created_at'                => now(),
                    'updated_at'                => now(),
                ]);

                $count++;

            } catch (Exception $e) {
                $stats['errors'][] = "Ligne {$row} (PARC): " . $e->getMessage();
            }
        }

        return $count;
    }

    // ─────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────

    /**
     * Récupère la valeur d'une cellule
     * — getCalculatedValue() pour lire aussi les formules Excel
     * — trim() pour nettoyer les espaces
     */
    private function getCellValue($sheet, $cell, $default = null)
    {
        try {
            $value = $sheet->getCell($cell)->getCalculatedValue();

            if (is_string($value)) {
                $value = trim($value);
            }

            return ($value === null || $value === '') ? $default : $value;

        } catch (Exception $e) {
            return $default;
        }
    }

    /**
     * Récupère et parse une date depuis Excel
     * Formats acceptés :
     *   - Numérique Excel     : 44927  (serial date interne)
     *   - Cellule date Excel  : cellule formatée comme date dans Excel
     *   - dd/mm/yyyy          : 12/01/2000
     *   - d/m/yyyy            : 5/1/2000
     *   - yyyy/mm/dd          : 2000/01/12
     *   - dd-mm-yyyy          : 12-01-2000
     *   - yyyy-mm-dd          : 2000-01-12  (déjà correct)
     *   - dd.mm.yyyy          : 12.01.2000
     *   - yyyy.mm.dd          : 2000.01.12
     *   - dd/mm/yy            : 12/01/00  (année sur 2 chiffres)
     */
    private function getDateValue($sheet, $cell)
    {
        try {
            $cellObj = $sheet->getCell($cell);

            // ── 1. Cellule formatée comme date dans Excel ──────────
            if (\PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cellObj)) {
                $value = $cellObj->getValue();
                if (!empty($value) && is_numeric($value)) {
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                        ->format('Y-m-d');
                }
            }

            // ── 2. Valeur calculée (texte ou numérique brut) ───────
            $value = $cellObj->getCalculatedValue();

            if (empty($value)) {
                return null;
            }

            // ── 3. Numérique Excel non détecté comme date ─────────
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                    ->format('Y-m-d');
            }

            $value = trim((string) $value);

            if (empty($value)) {
                return null;
            }

            // ── 4. Formats texte ───────────────────────────────────

            // dd/mm/yyyy ou d/m/yyyy
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $m)) {
                return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
            }

            // dd/mm/yy ou d/m/yy (année 2 chiffres)
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2})$/', $value, $m)) {
                $year = (int)$m[3] >= 50 ? '19' . $m[3] : '20' . $m[3];
                return sprintf('%04d-%02d-%02d', $year, $m[2], $m[1]);
            }

            // yyyy/mm/dd
            if (preg_match('/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/', $value, $m)) {
                return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
            }

            // dd-mm-yyyy ou d-m-yyyy
            if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $value, $m)) {
                return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
            }

            // yyyy-mm-dd (format SQL, déjà correct)
            if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value, $m)) {
                return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
            }

            // dd.mm.yyyy ou d.m.yyyy
            if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $value, $m)) {
                return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
            }

            // yyyy.mm.dd
            if (preg_match('/^(\d{4})\.(\d{1,2})\.(\d{1,2})$/', $value, $m)) {
                return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
            }

            // ── 5. Dernier recours : Carbon ───────────────────────
            return \Carbon\Carbon::parse($value)->format('Y-m-d');

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Valide qu'une clé étrangère existe dans la table référencée
     */
    private function validateForeignKey($value, $modelClass)
    {
        if (empty($value) || !class_exists($modelClass)) {
            return null;
        }

        try {
            $exists = $modelClass::find($value);
            return $exists ? $value : null;
        } catch (Exception $e) {
            return null;
        }
    }
}