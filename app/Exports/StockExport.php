<?php

namespace App\Exports;

use App\Models\Stock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Stock::with(['equipment'])->get();
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'N° Série Équipement',
            'Type Stock',
            'Localisation Physique',
            'État',
            'Quantité',
            'Date Entrée',
            'Date Sortie',
            'Observations',
            'Date Création'
        ];
    }
    
    public function map($stock): array
    {
        return [
            $stock->id,
            $stock->numero_serie,
            $stock->type_stock == 'celer' ? 'Celer (Neuf)' : 'Deceler (Retour)',
            $stock->localisation_physique,
            $stock->etat,
            $stock->quantite,
            $stock->date_entree->format('d/m/Y'),
            $stock->date_sortie ? $stock->date_sortie->format('d/m/Y') : 'N/A',
            $stock->observations ?? '',
            $stock->created_at->format('d/m/Y H:i')
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:J1' => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E6F2FF']]],
        ];
    }
}