<?php

namespace App\Imports;

use App\Models\Equipment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Validation\Rule;

class EquipmentUpdateImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    /**
     * Mapper chaque ligne à un modèle Equipment existant
     */
    public function model(array $row)
    {
        // Vérifier que le numéro de série existe
        if (!isset($row['numero_serie']) || empty($row['numero_serie'])) {
            return null;
        }

        // Trouver l'équipement existant
        $equipment = Equipment::where('numero_serie', $row['numero_serie'])->first();

        if (!$equipment) {
            // L'équipement n'existe pas, le créer
            return new Equipment($this->prepareData($row));
        }

        // Mettre à jour l'équipement existant
        $equipment->update($this->prepareData($row));

        return $equipment;
    }

    /**
     * Préparer les données pour la création/mise à jour
     */
    private function prepareData(array $row): array
    {
        return [
            'agence_id' => $row['agence_id'] ?? null,
            'categorie_id' => $row['categorie_id'] ?? null,
            'fournisseur_id' => $row['fournisseur_id'] ?? null,
            'nom' => $row['nom'] ?? null,
            'numero_serie' => $row['numero_serie'] ?? null,
            'numero_codification' => $row['numero_codification'] ?? null,
            'marque' => $row['marque'] ?? null,
            'modele' => $row['modele'] ?? null,
            'type' => $row['type'] ?? null,
            'adresse_mac' => $row['adresse_mac'] ?? null,
            'adresse_ip' => $row['adresse_ip'] ?? null,
            'localisation' => $row['localisation'] ?? null,
            'lieu_stockage' => $row['lieu_stockage'] ?? null,
            'date_livraison' => isset($row['date_livraison']) ? \Carbon\Carbon::parse($row['date_livraison']) : null,
            'prix' => $row['prix'] ?? null,
            'garantie' => $row['garantie'] ?? null,
            'reference_facture' => $row['reference_facture'] ?? null,
            'etat' => $row['etat'] ?? 'bon',
            'departement' => $row['departement'] ?? null,
            'poste_staff' => $row['poste_staff'] ?? null,
            'date_mise_service' => isset($row['date_mise_service']) ? \Carbon\Carbon::parse($row['date_mise_service']) : null,
            'date_amortissement' => isset($row['date_amortissement']) ? \Carbon\Carbon::parse($row['date_amortissement']) : null,
            'reference_installation' => $row['reference_installation'] ?? null,
            'notes' => $row['notes'] ?? null,
        ];
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'numero_serie' => 'required|string',
            'agence_id' => 'nullable|exists:agencies,id',
            'categorie_id' => 'nullable|exists:categories,id',
            'fournisseur_id' => 'nullable|exists:suppliers,id',
            'nom' => 'nullable|string|max:255',
            'marque' => 'nullable|string|max:255',
            'modele' => 'nullable|string|max:255',
            'type' => 'nullable|in:Réseau,Informatique,Électronique',
            'localisation' => 'nullable|string|max:255',
            'lieu_stockage' => 'nullable|string|max:255',
            'date_livraison' => 'nullable|date',
            'prix' => 'nullable|numeric|min:0',
            'garantie' => 'nullable|string|max:255',
            'reference_facture' => 'nullable|string|max:255',
            'etat' => 'nullable|in:neuf,bon,moyen,mauvais',
            'departement' => 'nullable|string|max:255',
            'poste_staff' => 'nullable|string|max:255',
            'date_mise_service' => 'nullable|date',
            'date_amortissement' => 'nullable|date',
            'reference_installation' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function customValidationMessages()
    {
        return [
            'numero_serie.required' => 'Le numéro de série est obligatoire',
            'agence_id.exists' => 'L\'agence spécifiée n\'existe pas',
            'categorie_id.exists' => 'La catégorie spécifiée n\'existe pas',
            'fournisseur_id.exists' => 'Le fournisseur spécifié n\'existe pas',
            'type.in' => 'Le type doit être Réseau, Informatique ou Électronique',
            'etat.in' => 'L\'état doit être neuf, bon, moyen ou mauvais',
            'date_livraison.date' => 'La date de livraison doit être une date valide',
            'date_mise_service.date' => 'La date de mise en service doit être une date valide',
            'date_amortissement.date' => 'La date d\'amortissement doit être une date valide',
        ];
    }
}