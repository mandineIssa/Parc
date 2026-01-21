<?php

namespace App\Exports;

use App\Models\Equipment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class EquipmentExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, ShouldAutoSize, WithTitle
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    public function collection()
    {
        $query = Equipment::with(['agence', 'categorie', 'fournisseur']);
        
        // Appliquer les filtres
        if (!empty($this->filters['statut'])) {
            $query->where('statut', $this->filters['statut']);
        }
        
        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }
        
        if (!empty($this->filters['agence_id'])) {
            $query->where('agence_id', $this->filters['agence_id']);
        }
        
        if (!empty($this->filters['date_debut'])) {
            $query->where('date_livraison', '>=', $this->filters['date_debut']);
        }
        
        if (!empty($this->filters['date_fin'])) {
            $query->where('date_livraison', '<=', $this->filters['date_fin']);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'N° Série',
            'Nom',
            'Type',
            'Catégorie',
            'Marque',
            'Modèle',
            'Agence',
            'Localisation',
            'Statut',
            'État',
            'Prix (FCFA)',
            'Date Livraison',
            'Garantie',
            'Fournisseur',
            'Réf. Facture',
            'Adresse IP',
            'Adresse MAC',
            'Codification',
            'Lieu Stockage',
            'Département',
            'Poste Staff',
            'Date Mise Service',
            'Date Amortissement',
            'Notes',
            'Date Création',
            'Date Modification'
        ];
    }
    
    public function map($equipment): array
    {
        return [
            $equipment->id,
            $equipment->numero_serie,
            $equipment->nom,
            $equipment->type,
            $equipment->categorie->nom ?? 'N/A',
            $equipment->marque,
            $equipment->modele,
            $equipment->agence->nom ?? 'N/A',
            $equipment->localisation,
            $this->getStatusLabel($equipment->statut),
            $equipment->etat,
            number_format($equipment->prix, 2, ',', ' '),
            $equipment->date_livraison->format('d/m/Y'),
            $equipment->garantie ?? 'N/A',
            $equipment->fournisseur->nom ?? 'N/A',
            $equipment->reference_facture ?? 'N/A',
            $equipment->adresse_ip ?? 'N/A',
            $equipment->adresse_mac ?? 'N/A',
            $equipment->numero_codification ?? 'N/A',
            $equipment->lieu_stockage ?? 'N/A',
            $equipment->departement ?? 'N/A',
            $equipment->poste_staff ?? 'N/A',
            $equipment->date_mise_service ? $equipment->date_mise_service->format('d/m/Y') : 'N/A',
            $equipment->date_amortissement ? $equipment->date_amortissement->format('d/m/Y') : 'N/A',
            $equipment->notes ?? '',
            $equipment->created_at->format('d/m/Y H:i'),
            $equipment->updated_at->format('d/m/Y H:i')
        ];
    }
    
    private function getStatusLabel($status)
    {
        $labels = [
            'stock' => 'Stock',
            'parc' => 'Parc',
            'maintenance' => 'Maintenance',
            'hors_service' => 'Hors Service',
            'perdu' => 'Perdu'
        ];
        
        return $labels[$status] ?? $status;
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Style pour l'en-tête
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E60012']]
            ],
            // Alignement des colonnes
            'A:Z' => [
                'alignment' => ['vertical' => 'center']
            ],
            // Bordures pour tout le tableau
            'A1:AA' . ($this->collection()->count() + 1) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ]
        ];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 20,  // N° Série
            'C' => 25,  // Nom
            'D' => 15,  // Type
            'E' => 20,  // Catégorie
            'F' => 15,  // Marque
            'G' => 20,  // Modèle
            'H' => 20,  // Agence
            'I' => 25,  // Localisation
            'J' => 15,  // Statut
            'K' => 15,  // État
            'L' => 15,  // Prix
            'M' => 15,  // Date Livraison
            'N' => 15,  // Garantie
            'O' => 20,  // Fournisseur
            'P' => 20,  // Réf. Facture
            'Q' => 15,  // Adresse IP
            'R' => 20,  // Adresse MAC
            'S' => 20,  // Codification
            'T' => 20,  // Lieu Stockage
            'U' => 20,  // Département
            'V' => 20,  // Poste Staff
            'W' => 15,  // Date Mise Service
            'X' => 15,  // Date Amortissement
            'Y' => 40,  // Notes
            'Z' => 20,  // Date Création
            'AA' => 20, // Date Modification
        ];
    }
    
    public function title(): string
    {
        return 'Équipements';
    }
}