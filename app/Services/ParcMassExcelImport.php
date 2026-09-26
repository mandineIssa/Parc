<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Equipment;
use App\Models\Parc;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ParcMassExcelImport
{
    /**
     * @return array{created:int,updated:int,ignored:int,ignored_path:?string}
     */
    public function importFromPath(string $path): array
    {
        $spreadsheet = IOFactory::load($path);

        return $this->importSpreadsheet($spreadsheet);
    }

    /**
     * @return array{created:int,updated:int,ignored:int,ignored_path:?string}
     */
    public function importSpreadsheet(Spreadsheet $spreadsheet): array
    {
        $sheet = $spreadsheet->getSheetByName('Parc') ?? $spreadsheet->getActiveSheet();
        $columnMap = $this->mapColumns($sheet);
        $highestRow = (int) $sheet->getHighestDataRow();

        $created = 0;
        $updated = 0;
        $ignored = [];
        $failed = 0;
        $seenSerials = [];

        for ($row = 2; $row <= $highestRow; $row++) {
            $values = $this->rowValues($sheet, $row, $columnMap);
            $serial = $this->cleanSerial($values['numero_serie'] ?? '');

            if ($this->rowIsEmpty($values)) {
                continue;
            }

            if ($serial === '') {
                $ignored[] = $this->ignoredRow($values, $row, 'Numéro de série vide : ligne ignorée.');
                continue;
            }

            $serialKey = mb_strtolower($serial);
            if (isset($seenSerials[$serialKey])) {
                $failed++;
                $ignored[] = $this->ignoredRow($values, $row, 'Doublon dans le fichier (même numéro de série).');
                continue;
            }
            $seenSerials[$serialKey] = $row;

            try {
                $equipment = Equipment::where('numero_serie', $serial)->first();

                if ($equipment && $equipment->type !== 'Informatique') {
                    $failed++;
                    $ignored[] = $this->ignoredRow(
                        $values,
                        $row,
                        'Équipement existant de type « '.$equipment->type.' » : import en masse réservé aux types informatiques.'
                    );
                    continue;
                }

                $wasNew = $equipment === null;
                $this->upsertInformatique($equipment, $serial, $values);

                if ($wasNew) {
                    $created++;
                } else {
                    $updated++;
                }
            } catch (\Throwable $e) {
                $failed++;
                $ignored[] = $this->ignoredRow($values, $row, 'Erreur : '.$e->getMessage());
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'ignored' => count($ignored),
            'failed' => $failed,
            'ignored_path' => $this->writeIgnoredWorkbook($ignored),
        ];
    }

    /**
     * @param  array<string, string>  $columnMap
     * @return array<string, string>
     */
    private function rowValues(Worksheet $sheet, int $row, array $columnMap): array
    {
        $values = [];
        foreach (ParcMassExcelSchema::columns() as $def) {
            $values[$def['key']] = '';
        }

        foreach ($columnMap as $col => $key) {
            $values[$key] = $this->cellString($sheet, $col.$row);
        }

        return $values;
    }

    /**
     * @return array<string, string> column letter => key
     */
    private function mapColumns(Worksheet $sheet): array
    {
        $aliases = ParcMassExcelSchema::headerAliases();
        $highestCol = $sheet->getHighestDataColumn();
        $maxIndex = Coordinate::columnIndexFromString($highestCol);
        $map = [];
        $matched = 0;

        for ($i = 1; $i <= $maxIndex; $i++) {
            $col = Coordinate::stringFromColumnIndex($i);
            $header = ParcMassExcelSchema::normalize($this->cellString($sheet, $col.'1'));
            if ($header === '') {
                continue;
            }
            if (isset($aliases[$header])) {
                $map[$col] = $aliases[$header];
                $matched++;
            }
        }

        if ($matched < 3) {
            $map = [];
            foreach (ParcMassExcelSchema::columns() as $col => $def) {
                if (str_starts_with($def['key'], '_')) {
                    continue;
                }
                $map[$col] = $def['key'];
            }
        }

        return $map;
    }

    /**
     * @param  array<string, string>  $values
     */
    private function upsertInformatique(?Equipment $equipment, string $serial, array $values): void
    {
        $isNew = $equipment === null;
        if ($isNew) {
            $equipment = new Equipment();
            $equipment->numero_serie = $serial;
        }

        $agency = $this->findAgency($values['agence'] ?? '');
        $supplier = $this->findSupplier($values['fournisseur'] ?? '');
        $etat = $this->mapEtat($values['etat'] ?? '', $isNew ? 'bon' : ($equipment->etat ?: 'bon'));
        $dateAchat = $this->parseDate($values['date_livraison'] ?? '');
        $dateMiseService = $this->parseDate($values['date_mise_service'] ?? '');
        $dateAmortissement = $this->parseDate($values['date_amortissement'] ?? '');
        $prixRaw = trim((string) ($values['prix'] ?? ''));

        $equipment->type = 'Informatique';
        $equipment->statut = 'parc';
        $equipment->nom = trim((string) ($values['nom'] ?? ''));
        $equipment->marque = trim((string) ($values['marque'] ?? ''));
        $equipment->modele = trim((string) ($values['modele'] ?? ''));
        $equipment->departement = trim((string) ($values['departement'] ?? ''));
        $equipment->poste_staff = trim((string) ($values['poste'] ?? ''));
        $equipment->agency_id = $agency?->id;
        $equipment->localisation = $agency?->nom ?? trim((string) ($values['agence'] ?? ''));
        $equipment->fournisseur_id = $supplier?->id;
        $equipment->etat = $etat;
        $equipment->prix = $prixRaw === '' ? 0 : (float) str_replace([' ', ','], ['', '.'], $prixRaw);
        $equipment->date_mise_service = $dateMiseService;
        $equipment->date_amortissement = $dateAmortissement;
        $equipment->date_livraison = $dateAchat ?? ($isNew ? ($dateMiseService ?? now()) : ($equipment->date_livraison ?? now()));

        $poste = $this->nullableTrim($values['poste'] ?? '');
        $departement = $this->nullableTrim($values['departement'] ?? '');
        $localisation = $this->nullableTrim($agency?->nom ?? ($values['agence'] ?? ''));

        DB::transaction(function () use ($equipment, $serial, $values, $poste, $departement, $localisation, $dateMiseService) {
            $equipment->save();

            $parc = Parc::where('numero_serie', $serial)->first() ?? new Parc(['numero_serie' => $serial]);
            $parc->numero_serie = $serial;
            $parc->utilisateur_nom = trim((string) ($values['utilisateur_nom'] ?? ''));
            $parc->utilisateur_prenom = trim((string) ($values['utilisateur_prenom'] ?? ''));
            $parc->departement = $departement ?? '';
            $parc->poste_affecte = $poste ?? '';
            $parc->position = $poste;
            $parc->localisation = $localisation;
            $parc->date_affectation = $dateMiseService ?? $parc->date_affectation ?? now();
            $parc->statut_usage = $parc->statut_usage ?: 'actif';
            $parc->utilisateur_id = $this->matchUser(
                $parc->utilisateur_nom,
                $parc->utilisateur_prenom
            );
            if (empty($parc->affecte_par) && Auth::id()) {
                $parc->affecte_par = Auth::id();
            }
            $parc->save();
        });
    }

    private function findAgency(string $name): ?Agency
    {
        $name = trim($name);
        if ($name === '') {
            return null;
        }

        return Agency::query()
            ->whereRaw('LOWER(nom) = ?', [mb_strtolower($name)])
            ->first();
    }

    private function findSupplier(string $name): ?Supplier
    {
        $name = trim($name);
        if ($name === '') {
            return null;
        }

        return Supplier::query()
            ->whereRaw('LOWER(nom) = ?', [mb_strtolower($name)])
            ->first();
    }

    private function nullableTrim(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text === '' ? null : $text;
    }

    private function matchUser(string $nom, string $prenom): ?int
    {
        $nom = trim($nom);
        $prenom = trim($prenom);
        if ($nom === '' && $prenom === '') {
            return null;
        }

        $query = User::query();
        if ($nom !== '') {
            $query->whereRaw('LOWER(name) = ?', [mb_strtolower($nom)]);
        }
        if ($prenom !== '') {
            $query->whereRaw('LOWER(prenom) = ?', [mb_strtolower($prenom)]);
        }

        return $query->value('id');
    }

    private function mapEtat(string $raw, string $fallback): string
    {
        $normalized = ParcMassExcelSchema::normalize($raw);

        return match ($normalized) {
            'bon', 'neuf', 'bon etat' => 'bon',
            'moyen' => 'moyen',
            'mauvais' => 'mauvais',
            '' => $fallback,
            default => $fallback,
        };
    }

    private function parseDate(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value));
            } catch (\Throwable) {
                // year-only like 2024
                if ((int) $value >= 1990 && (int) $value <= 2100) {
                    return Carbon::createFromDate((int) $value, 1, 1);
                }
            }
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y', 'Y'] as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $text);

                return $parsed ?: null;
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($text);
        } catch (\Throwable) {
            return null;
        }
    }

    private function cellString(Worksheet $sheet, string $cell): string
    {
        $value = $sheet->getCell($cell)->getCalculatedValue();
        if ($value === null) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_float($value) || is_int($value)) {
            if (ExcelDate::isDateTime($sheet->getCell($cell))) {
                try {
                    return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->format('d/m/Y');
                } catch (\Throwable) {
                    return (string) $value;
                }
            }

            return (string) $value;
        }

        return trim((string) $value);
    }

    private function cleanSerial(string $serial): string
    {
        $serial = trim($serial);
        if ($serial === '' || strcasecmp($serial, 'null') === 0 || $serial === '-' || $serial === 'N/A') {
            return '';
        }

        return $serial;
    }

    /**
     * @param  array<string, string>  $values
     */
    private function rowIsEmpty(array $values): bool
    {
        foreach ($values as $key => $value) {
            if (str_starts_with((string) $key, '_')) {
                continue;
            }
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, string>  $values
     * @return array<string, mixed>
     */
    private function ignoredRow(array $values, int $row, string $reason): array
    {
        $values['_ligne'] = $row;
        $values['_motif'] = $reason;

        return $values;
    }

    /**
     * @param  list<array<string, mixed>>  $ignored
     */
    private function writeIgnoredWorkbook(array $ignored): ?string
    {
        if ($ignored === []) {
            return null;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ignores');

        $headers = ['Ligne', 'Motif'];
        foreach (ParcMassExcelSchema::columns() as $def) {
            if ($def['label'] !== '') {
                $headers[] = $def['label'];
            }
        }

        $colIndex = 1;
        foreach ($headers as $label) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter.'1', $label);
            $colIndex++;
        }
        $lastHeaderCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:'.$lastHeaderCol.'1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFC00000'],
            ],
        ]);

        $r = 2;
        foreach ($ignored as $item) {
            $colIndex = 1;
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex++).$r, $item['_ligne'] ?? '');
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex++).$r, $item['_motif'] ?? '');
            foreach (ParcMassExcelSchema::columns() as $def) {
                if ($def['label'] === '') {
                    continue;
                }
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex++).$r, $item[$def['key']] ?? '');
            }
            $r++;
        }

        foreach (range(1, count($headers)) as $i) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }

        $dir = storage_path('app/imports');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $filename = 'parc_import_ignores_'.now()->format('Y-m-d_His').'.xlsx';
        $fullPath = $dir.DIRECTORY_SEPARATOR.$filename;
        (new Xlsx($spreadsheet))->save($fullPath);

        return $fullPath;
    }
}
