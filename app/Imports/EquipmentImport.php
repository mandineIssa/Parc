<?php

namespace App\Imports;

use App\Models\Equipment;
use App\Models\Agency;
use App\Models\Category;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EquipmentImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    private $agencies;
    private $categories;
    private $suppliers;
    
    public function __construct()
    {
        // Pré-charger les données pour éviter les requêtes répétitives
        $this->agencies = Agency::all()->pluck('id', 'nom')->toArray();
        $this->categories = Category::all()->pluck('id', 'nom')->toArray();
        $this->suppliers = Supplier::all()->pluck('id', 'nom')->toArray();
    }
    
    public function model(array $row)
    {
        // Rechercher les IDs par nom
        $agenceId = $this->agencies[$row['agence']] ?? null;
        $categorieId = $this->categories[$row['categorie']] ?? null;
        $fournisseurId = $this->suppliers[$row['fournisseur']] ?? null;
        
        // Gérer la date
        $dateLivraison = $this->parseDate($row['date_livraison']);
        $dateMiseService = isset($row['date_mise_service']) ? $this->parseDate($row['date_mise_service']) : null;
        $dateAmortissement = isset($row['date_amortissement']) ? $this->parseDate($row['date_amortissement']) : null;
        
        return new Equipment([
            'numero_serie' => $row['numero_serie'],
            'agence_id' => $agenceId,
            'localisation' => $row['localisation'],
            'type' => $row['type'],
            'categorie_id' => $categorieId,
            'nom' => $row['nom'],
            'modele' => $row['modele'],
            'marque' => $row['marque'],
            'numero_codification' => $row['codification'] ?? null,
            'adresse_mac' => $row['adresse_mac'] ?? null,
            'adresse_ip' => $row['adresse_ip'] ?? null,
            'fournisseur_id' => $fournisseurId,
            'date_livraison' => $dateLivraison,
            'prix' => $this->parsePrice($row['prix']),
            'garantie' => $row['garantie'] ?? null,
            'reference_facture' => $row['reference_facture'] ?? null,
            'reference_installation' => $row['reference_installation'] ?? null,
            'etat' => $row['etat'],
            'lieu_stockage' => $row['lieu_stockage'] ?? null,
            'statut' => $row['statut'] ?? 'stock',
            'departement' => $row['departement'] ?? null,
            'poste_staff' => $row['poste_staff'] ?? null,
            'date_mise_service' => $dateMiseService,
            'date_amortissement' => $dateAmortissement,
            'notes' => $row['notes'] ?? null,
        ]);
    }
    
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }
        
        try {
            // Essayer différents formats de date
            if (is_numeric($date)) {
                // Date Excel (numéro de série)
                return Carbon::createFromTimestamp(($date - 25569) * 86400);
            }
            
            // Essayer différents formats
            $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'm/d/Y'];
            
            foreach ($formats as $format) {
                $parsed = Carbon::createFromFormat($format, $date);
                if ($parsed !== false) {
                    return $parsed;
                }
            }
            
            return Carbon::parse($date);
        } catch (\Exception $e) {
            return now();
        }
    }
    
    private function parsePrice($price)
    {
        if (empty($price)) {
            return 0;
        }
        
        // Nettoyer le prix (enlever espaces, devises, etc.)
        $price = str_replace([' ', 'FCFA', '€', '$', ',', "'"], '', $price);
        $price = str_replace(',', '.', $price);
        
        return (float) $price;
    }
    
    public function rules(): array
    {
        return [
            'numero_serie' => ['required', 'string', 'unique:equipment,numero_serie'],
            'nom' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['Réseau', 'Informatique', 'Électronique'])],
            'agence' => ['required', 'string'],
            'categorie' => ['required', 'string'],
            'marque' => ['required', 'string', 'max:255'],
            'modele' => ['required', 'string', 'max:255'],
            'localisation' => ['required', 'string', 'max:255'],
            'prix' => ['required', 'numeric', 'min:0'],
            'etat' => ['required', Rule::in(['neuf', 'bon', 'moyen', 'mauvais'])],
            'statut' => ['nullable', Rule::in(['stock', 'parc', 'maintenance', 'hors_service', 'perdu'])],
            'date_livraison' => ['required', 'date'],
        ];
    }
    
    public function customValidationMessages()
    {
        return [
            'numero_serie.unique' => 'Le numéro de série :input existe déjà',
            'agence.required' => 'L\'agence est requise',
            'categorie.required' => 'La catégorie est requise',
            'type.in' => 'Le type doit être: Réseau, Informatique ou Électronique',
        ];
    }
    
    public function batchSize(): int
    {
        return 100;
    }
    
    public function chunkSize(): int
    {
        return 100;
    }
    
    public function prepareForValidation($data)
    {
        // Nettoyer les données avant validation
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }
        
        return $data;
    }
}