<?php

namespace App\Exports;

use App\Support\ProfilExcelMapper;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfilTemplateExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function collection(): Collection
    {
        return collect([
            [
                '',
                'Dupont',
                'Jean',
                'jean.dupont@example.com',
                '+221771234567',
                'Directeur',
                'IT',
                'Dakar',
                'Sénégal',
                'CDI',
                'actif',
                '',
                '',
                '',
                '',
            ],
            [
                '',
                'Martin',
                'Marie',
                'marie.martin@example.com',
                '+221771234568',
                'Manager',
                'Finance',
                'Abidjan',
                "Côte d'Ivoire",
                'CDI',
                'actif',
                '',
                '',
                '',
                '',
            ],
        ]);
    }

    public function headings(): array
    {
        return ProfilExcelMapper::headings(false);
    }

    public function styles(Worksheet $sheet): array
    {
        $lastColumn = 'O';
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC143C'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        return [];
    }

    public function title(): string
    {
        return 'Profils';
    }
}
