<?php

namespace App\Exports;

use App\Models\User;
use App\Support\ProfilExcelMapper;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfilExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        private readonly ?Request $filters = null
    ) {}

    public function collection(): Collection
    {
        $query = User::query()
            ->with(['agence', 'filiale', 'nPlus1', 'nPlus2']);

        if ($this->filters) {
            $query->filter($this->filters);
        }

        return $query
            ->orderBy('name')
            ->orderBy('prenom')
            ->get();
    }

    public function headings(): array
    {
        return ProfilExcelMapper::headings(true);
    }

    /**
     * @param  User  $user
     * @return list<string|null>
     */
    public function map($user): array
    {
        return [
            $user->matricule,
            $user->name,
            $user->prenom,
            $user->email,
            $user->telephone,
            $user->fonction,
            $user->departement,
            $user->agence?->nom,
            $user->filiale?->nom,
            $user->type_contrat ?: 'CDI',
            $user->statut ?: 'actif',
            ProfilExcelMapper::displayName($user->nPlus1),
            $user->nPlus1?->matricule,
            ProfilExcelMapper::displayName($user->nPlus2),
            $user->nPlus2?->matricule,
            $user->created_at?->format('d/m/Y H:i:s'),
            $user->updated_at?->format('d/m/Y H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastColumn = 'Q';
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
