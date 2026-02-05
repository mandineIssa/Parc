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
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240'
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());

            $stats = [
                'equipment_imported' => 0,
                'equipment_details_imported' => 0,
                'stock_imported' => 0,
                'parc_imported' => 0,
                'errors' => []
            ];

            // 1. Importer EQUIPMENT
            $equipmentSheet = $spreadsheet->getSheetByName('EQUIPMENT');
            if ($equipmentSheet) {
                $stats['equipment_imported'] = $this->importEquipment($equipmentSheet, $stats);
            }

            // 2. Importer EQUIPMENT_DETAILS
            $detailsSheet = $spreadsheet->getSheetByName('EQUIPMENT_DETAILS');
            if ($detailsSheet) {
                $stats['equipment_details_imported'] = $this->importEquipmentDetails($detailsSheet, $stats);
            }

            // 3. Importer STOCK
            $stockSheet = $spreadsheet->getSheetByName('STOCK');
            if ($stockSheet) {
                $stats['stock_imported'] = $this->importStock($stockSheet, $stats);
            }

            // 4. Importer PARC
            $parcSheet = $spreadsheet->getSheetByName('PARC');
            if ($parcSheet) {
                $stats['parc_imported'] = $this->importParc($parcSheet, $stats);
            }

            DB::commit();

            if (count($stats['errors']) > 0) {
                return back()->with([
                    'warning' => 'Importation terminée avec des avertissements.',
                    'stats' => $stats
                ]);
            }

            return back()->with([
                'success' => 'Importation réussie !',
                'stats' => $stats
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur importation: ' . $e->getMessage());
            
            return back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }

    /**
     * Importe les équipements
     */
    private function importEquipment($sheet, &$stats)
    {
        $count = 0;
        $highestRow = $sheet->getHighestRow();

        // Commence à la ligne 3 (ligne 1 = en-têtes, ligne 2 = descriptions)
        for ($row = 3; $row <= $highestRow; $row++) {
            try {
                // Récupérer les valeurs
                $numeroSerie = $sheet->getCell("A{$row}")->getValue();
                
                // Ignorer les lignes vides
                if (empty($numeroSerie)) {
                    continue;
                }

                // Vérifier si l'équipement existe déjà
                $existing = Equipment::where('numero_serie', $numeroSerie)->first();
                if ($existing) {
                    $stats['errors'][] = "Ligne {$row}: Équipement {$numeroSerie} existe déjà (ignoré)";
                    continue;
                }

                // Créer l'équipement
                $equipment = new Equipment();
                $equipment->numero_serie = $numeroSerie;
                
                // Gérer agency_id avec vérification
                $agencyId = $this->getCellValue($sheet, "B{$row}");
                $equipment->agency_id = $this->validateForeignKey($agencyId, 'App\Models\Agency');
                
                $equipment->localisation = $this->getCellValue($sheet, "C{$row}");
                $equipment->type = $this->getCellValue($sheet, "D{$row}");
                
                // Gérer categorie_id avec vérification
                $categorieId = $this->getCellValue($sheet, "E{$row}");
                $equipment->categorie_id = $this->validateForeignKey($categorieId, 'App\Models\Category');
                
                $equipment->nom = $this->getCellValue($sheet, "F{$row}");
                $equipment->modele = $this->getCellValue($sheet, "G{$row}");
                $equipment->marque = $this->getCellValue($sheet, "H{$row}");
                $equipment->numero_codification = $this->getCellValue($sheet, "I{$row}");
                $equipment->adresse_mac = $this->getCellValue($sheet, "J{$row}");
                $equipment->adresse_ip = $this->getCellValue($sheet, "K{$row}");
                
                // Gérer fournisseur_id avec vérification
                $fournisseurId = $this->getCellValue($sheet, "L{$row}");
                $equipment->fournisseur_id = $this->validateForeignKey($fournisseurId, 'App\Models\Supplier');
                
                $equipment->date_livraison = $this->getDateValue($sheet, "M{$row}");
                $equipment->prix = $this->getCellValue($sheet, "N{$row}");
                $equipment->garantie = $this->getCellValue($sheet, "O{$row}");
                $equipment->reference_facture = $this->getCellValue($sheet, "P{$row}");
                $equipment->reference_installation = $this->getCellValue($sheet, "Q{$row}");
                $equipment->etat = $this->getCellValue($sheet, "R{$row}");
                $equipment->lieu_stockage = $this->getCellValue($sheet, "S{$row}");
                $equipment->notes = $this->getCellValue($sheet, "T{$row}");
                $equipment->statut = $this->getCellValue($sheet, "U{$row}", 'stock');
                $equipment->departement = $this->getCellValue($sheet, "V{$row}");
                $equipment->poste_staff = $this->getCellValue($sheet, "W{$row}");
                $equipment->date_mise_service = $this->getDateValue($sheet, "X{$row}");
                $equipment->date_amortissement = $this->getDateValue($sheet, "Y{$row}");

                $equipment->save();
                $count++;

            } catch (Exception $e) {
                $stats['errors'][] = "Ligne {$row} (EQUIPMENT): " . $e->getMessage();
            }
        }

        return $count;
    }

    /**
     * Importe les détails des équipements
     */
    private function importEquipmentDetails($sheet, &$stats)
    {
        $count = 0;
        $highestRow = $sheet->getHighestRow();

        for ($row = 3; $row <= $highestRow; $row++) {
            try {
                $numeroSerie = $sheet->getCell("A{$row}")->getValue();
                
                if (empty($numeroSerie)) {
                    continue;
                }

                // Vérifier que l'équipement existe
                $equipment = Equipment::where('numero_serie', $numeroSerie)->first();
                if (!$equipment) {
                    $stats['errors'][] = "Ligne {$row} (EQUIPMENT_DETAILS): Équipement {$numeroSerie} non trouvé";
                    continue;
                }

                // Vérifier si les détails existent déjà
                $existing = EquipmentDetail::where('equipment_id', $equipment->id)->first();
                if ($existing) {
                    $stats['errors'][] = "Ligne {$row} (EQUIPMENT_DETAILS): Détails pour {$numeroSerie} existent déjà (ignoré)";
                    continue;
                }

                $detail = new EquipmentDetail();
                $detail->equipment_id = $equipment->id;
                $detail->categorie = $this->getCellValue($sheet, "B{$row}");
                $detail->sous_categorie = $this->getCellValue($sheet, "C{$row}");
                $detail->etat_specifique = $this->getCellValue($sheet, "D{$row}");
                $detail->adresse_ip_specifique = $this->getCellValue($sheet, "E{$row}");
                $detail->adresse_mac_specifique = $this->getCellValue($sheet, "F{$row}");
                $detail->departement_specifique = $this->getCellValue($sheet, "G{$row}");
                $detail->poste_staff_specifique = $this->getCellValue($sheet, "H{$row}");
                $detail->numero_codification_specifique = $this->getCellValue($sheet, "I{$row}");
                
                // Contrat maintenance
                $contratValue = strtolower($this->getCellValue($sheet, "J{$row}", 'non'));
                $detail->contrat_maintenance = in_array($contratValue, ['oui', '1', 'true', 'yes']);
                
                $detail->type_contrat = $this->getCellValue($sheet, "K{$row}");
                $detail->date_debut_contrat = $this->getDateValue($sheet, "L{$row}");
                $detail->date_fin_contrat = $this->getDateValue($sheet, "M{$row}");
                $detail->periodicite_maintenance = $this->getCellValue($sheet, "N{$row}");
                
                // Données spécifiques JSON
                $specificData = $this->getCellValue($sheet, "O{$row}");
                if (!empty($specificData)) {
                    // Si c'est déjà du JSON valide
                    $decoded = json_decode($specificData, true);
                    $detail->specific_data = $decoded ?: [];
                } else {
                    $detail->specific_data = [];
                }

                $detail->save();
                $count++;

            } catch (Exception $e) {
                $stats['errors'][] = "Ligne {$row} (EQUIPMENT_DETAILS): " . $e->getMessage();
            }
        }

        return $count;
    }

    /**
     * Importe les stocks
     */
    private function importStock($sheet, &$stats)
    {
        $count = 0;
        $highestRow = $sheet->getHighestRow();

        for ($row = 3; $row <= $highestRow; $row++) {
            try {
                $numeroSerie = $sheet->getCell("A{$row}")->getValue();
                
                if (empty($numeroSerie)) {
                    continue;
                }

                // Vérifier que l'équipement existe
                $equipment = Equipment::where('numero_serie', $numeroSerie)->first();
                if (!$equipment) {
                    $stats['errors'][] = "Ligne {$row} (STOCK): Équipement {$numeroSerie} non trouvé";
                    continue;
                }

                // Vérifier si le stock existe déjà
                $existing = Stock::where('numero_serie', $numeroSerie)->first();
                if ($existing) {
                    $stats['errors'][] = "Ligne {$row} (STOCK): Stock pour {$numeroSerie} existe déjà (ignoré)";
                    continue;
                }

                $stock = new Stock();
                $stock->numero_serie = $numeroSerie;
                $stock->type_stock = $this->getCellValue($sheet, "B{$row}");
                $stock->localisation_physique = $this->getCellValue($sheet, "C{$row}");
                $stock->etat = $this->getCellValue($sheet, "D{$row}", 'disponible');
                $stock->quantite = $this->getCellValue($sheet, "E{$row}", 1);
                $stock->date_entree = $this->getDateValue($sheet, "F{$row}");
                $stock->date_sortie = $this->getDateValue($sheet, "G{$row}");
                $stock->observations = $this->getCellValue($sheet, "H{$row}");

                $stock->save();
                $count++;

            } catch (Exception $e) {
                $stats['errors'][] = "Ligne {$row} (STOCK): " . $e->getMessage();
            }
        }

        return $count;
    }

    /**
     * Importe les affectations parc
     */
    private function importParc($sheet, &$stats)
    {
        $count = 0;
        $highestRow = $sheet->getHighestRow();

        for ($row = 3; $row <= $highestRow; $row++) {
            try {
                $numeroSerie = $sheet->getCell("A{$row}")->getValue();
                
                if (empty($numeroSerie)) {
                    continue;
                }

                // Vérifier que l'équipement existe
                $equipment = Equipment::where('numero_serie', $numeroSerie)->first();
                if (!$equipment) {
                    $stats['errors'][] = "Ligne {$row} (PARC): Équipement {$numeroSerie} non trouvé";
                    continue;
                }

                // Vérifier si l'affectation existe déjà
                $existing = Parc::where('numero_serie', $numeroSerie)->first();
                if ($existing) {
                    $stats['errors'][] = "Ligne {$row} (PARC): Affectation pour {$numeroSerie} existe déjà (ignoré)";
                    continue;
                }

                $parc = new Parc();
                $parc->numero_serie = $numeroSerie;
                
                // Gérer utilisateur_id avec vérification
                $utilisateurId = $this->getCellValue($sheet, "B{$row}");
                $parc->utilisateur_id = $this->validateForeignKey($utilisateurId, 'App\Models\User');
                
                // Colonnes obligatoires
                $parc->utilisateur_nom = $this->getCellValue($sheet, "C{$row}");
                $parc->utilisateur_prenom = $this->getCellValue($sheet, "D{$row}");
                $parc->departement = $this->getCellValue($sheet, "E{$row}");
                $parc->poste_affecte = $this->getCellValue($sheet, "F{$row}");
                $parc->position = $this->getCellValue($sheet, "G{$row}");
                $parc->date_affectation = $this->getDateValue($sheet, "H{$row}");
                
                // Colonnes optionnelles
                $parc->date_retour_prevue = $this->getDateValue($sheet, "I{$row}");
                $parc->affectation_reason = $this->getCellValue($sheet, "J{$row}");
                $parc->affectation_reason_detail = $this->getCellValue($sheet, "K{$row}");
                $parc->localisation = $this->getCellValue($sheet, "L{$row}");
                $parc->telephone = $this->getCellValue($sheet, "M{$row}");
                $parc->email = $this->getCellValue($sheet, "N{$row}");
                $parc->statut_usage = $this->getCellValue($sheet, "O{$row}", 'actif');
                $parc->notes_affectation = $this->getCellValue($sheet, "P{$row}");
                
                // Gérer affecte_par avec vérification
                $affecteParId = $this->getCellValue($sheet, "Q{$row}");
                $parc->affecte_par = $this->validateForeignKey($affecteParId, 'App\Models\User');
                
                $parc->derniere_modification = $this->getDateValue($sheet, "R{$row}");
                $parc->numero_bon_affectation = $this->getCellValue($sheet, "S{$row}");

                $parc->save();
                $count++;

            } catch (Exception $e) {
                $stats['errors'][] = "Ligne {$row} (PARC): " . $e->getMessage();
            }
        }

        return $count;
    }

    /**
     * Récupère la valeur d'une cellule avec gestion null
     */
    private function getCellValue($sheet, $cell, $default = null)
    {
        $value = $sheet->getCell($cell)->getValue();
        return empty($value) ? $default : $value;
    }

    /**
     * Valide qu'une clé étrangère existe dans la table référencée
     */
    private function validateForeignKey($value, $modelClass)
    {
        // Si la valeur est vide ou null, retourner null
        if (empty($value)) {
            return null;
        }

        // Vérifier si le modèle existe
        if (!class_exists($modelClass)) {
            return null;
        }

        // Vérifier si l'ID existe dans la table
        try {
            $exists = $modelClass::find($value);
            return $exists ? $value : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Récupère une date avec conversion au format Y-m-d
     */
    private function getDateValue($sheet, $cell)
    {
        $value = $sheet->getCell($cell)->getValue();
        
        if (empty($value)) {
            return null;
        }

        try {
            // Si c'est un nombre Excel (serial date)
            if (is_numeric($value)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            }
            
            // Si c'est déjà une chaîne de date
            return date('Y-m-d', strtotime($value));
            
        } catch (Exception $e) {
            return null;
        }
    }
}