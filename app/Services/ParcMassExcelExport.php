<?php

namespace App\Services;

use App\Models\Equipment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ParcMassExcelExport
{
    /** Colonnes alignées sur le modèle Excel COFINA (feuille Parc). */
    private const COLUMNS = [
        'A' => ['NOM', 18],
        'B' => ['PRENOM', 18],
        'C' => ['AGENCE', 22],
        'D' => ['Departeme', 18],
        'E' => ['POSTE', 22],
        'F' => ['Dotation (ordinateur)', 20],
        'G' => ['serial number', 18],
        'H' => ['Marque/Modele', 16],
        'I' => ['Model PC', 24],
        'J' => ['DATE MISE EN SERVICE', 20],
        'K' => ['DATE D\'ACHAT', 16],
        'L' => ['PRIX D\'ACHAT', 14],
        'M' => ['', 4],
        'N' => ['Date prévue d\'amortissement', 26],
        'O' => ['', 4],
        'P' => ['Fournisseur', 18],
        'Q' => ['État (Bon / Moyen / Mauvais)', 24],
    ];

    public function build(Request $request): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Parc');

        $lastCol = array_key_last(self::COLUMNS);

        foreach (self::COLUMNS as $col => [$label, $width]) {
            $sheet->setCellValue("{$col}1", $label);
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['argb' => 'FFFFFFFF'],
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFC00000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:{$lastCol}1");

        $equipments = $this->buildQuery($request)
            ->orderBy('numero_serie')
            ->get();

        $row = 2;
        foreach ($equipments as $equipment) {
            $parc = $equipment->parc;
            $dateMiseService = $equipment->date_mise_service
                ?? $parc?->date_affectation
                ?? $equipment->date_livraison;
            $dateAchat = $equipment->date_livraison;
            $dateAmortissement = $equipment->date_amortissement
                ?? ($dateMiseService ? $dateMiseService->copy()->addYears(5) : null);

            $sheet->setCellValue("A{$row}", $parc?->utilisateur_nom ?? '');
            $sheet->setCellValue("B{$row}", $parc?->utilisateur_prenom ?? '');
            $sheet->setCellValue("C{$row}", $this->resolveAgence($equipment, $parc));
            $sheet->setCellValue("D{$row}", $parc?->departement ?? $equipment->departement ?? '');
            $sheet->setCellValue("E{$row}", $parc?->poste_affecte ?? $parc?->position ?? $equipment->poste_staff ?? '');
            $sheet->setCellValue("F{$row}", $parc ? 'OUI' : 'NON');
            $sheet->setCellValue("G{$row}", $equipment->numero_serie ?? '');
            $sheet->setCellValue("H{$row}", $equipment->marque ?? '');
            $sheet->setCellValue("I{$row}", $equipment->modele ?? '');

            $this->setDateCell($sheet, "J{$row}", $dateMiseService);
            $this->setDateCell($sheet, "K{$row}", $dateAchat, yearOnlyFallback: true);

            if ($equipment->prix !== null && (float) $equipment->prix > 0) {
                $sheet->setCellValue("L{$row}", (float) $equipment->prix);
            }

            $this->setDateCell($sheet, "N{$row}", $dateAmortissement);

            $sheet->setCellValue("P{$row}", $equipment->fournisseur?->nom ?? '');
            $sheet->setCellValue("Q{$row}", $this->formatEtatLabel($equipment->etat));

            $bg = ($row % 2 === 0) ? 'FFF2F2F2' : 'FFFFFFFF';
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $bg],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFD9D9D9'],
                    ],
                ],
            ]);

            $sheet->getStyle("L{$row}")->getNumberFormat()->setFormatCode('#,##0');
            foreach (["J{$row}", "K{$row}", "N{$row}"] as $dateCell) {
                $sheet->getStyle($dateCell)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            }
            $sheet->getRowDimension($row)->setRowHeight(16);
            $row++;
        }

        if ($row > 2) {
            $sheet->getStyle('A2:' . $lastCol . ($row - 1))->applyFromArray([
                'font' => ['size' => 10, 'name' => 'Calibri'],
            ]);
        }

        return $spreadsheet;
    }

    public function buildQuery(Request $request): Builder
    {
        $query = Equipment::where('statut', 'parc')
            ->with(['fournisseur', 'parc', 'agence']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('numero_serie', 'LIKE', "%{$search}%")
                    ->orWhere('nom', 'LIKE', "%{$search}%")
                    ->orWhere('marque', 'LIKE', "%{$search}%")
                    ->orWhere('modele', 'LIKE', "%{$search}%")
                    ->orWhere('numero_codification', 'LIKE', "%{$search}%")
                    ->orWhereHas('parc', function ($parcQuery) use ($search) {
                        $parcQuery->where('utilisateur_nom', 'LIKE', "%{$search}%")
                            ->orWhere('utilisateur_prenom', 'LIKE', "%{$search}%")
                            ->orWhere('localisation', 'LIKE', "%{$search}%")
                            ->orWhere('departement', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('agence', function ($agenceQuery) use ($search) {
                        $agenceQuery->where('nom', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('etat')) {
            $query->where('etat', $request->input('etat'));
        }

        if ($request->filled('filtre_rapide')) {
            match ($request->input('filtre_rapide')) {
                'a_remplacer' => $query->where('etat', 'mauvais'),
                'en_service' => $query->whereIn('etat', ['neuf', 'bon', 'moyen']),
                'non_affecte' => $query->whereDoesntHave('parc'),
                'reseau' => $query->where('type', 'Réseau'),
                'informatique' => $query->where('type', 'Informatique'),
                'electronique' => $query->where('type', 'Électronique'),
                default => null,
            };
        }

        return $query;
    }

    private function resolveAgence(Equipment $equipment, $parc): string
    {
        if ($equipment->agence?->nom) {
            return $equipment->agence->nom;
        }

        if (!empty($parc?->localisation)) {
            return $parc->localisation;
        }

        return $equipment->localisation ?? '';
    }

    private function formatEtatLabel(?string $etat): string
    {
        return match (strtolower(trim((string) $etat))) {
            'moyen' => 'MOYEN',
            'mauvais' => 'MAUVAIS',
            'bon', 'neuf' => 'BON',
            default => $etat ? strtoupper($etat) : '',
        };
    }

    private function setDateCell($sheet, string $cell, $date, bool $yearOnlyFallback = false): void
    {
        if (empty($date)) {
            return;
        }

        $carbon = $date instanceof Carbon
            ? $date
            : Carbon::parse($date);

        if ($yearOnlyFallback && $carbon->month === 1 && $carbon->day === 1) {
            $sheet->setCellValue($cell, (string) $carbon->year);

            return;
        }

        $sheet->setCellValue($cell, ExcelDate::PHPToExcel($carbon));
    }
}
