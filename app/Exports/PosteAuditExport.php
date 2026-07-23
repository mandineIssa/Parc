<?php

namespace App\Exports;

use App\Models\Poste;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PosteAuditExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    /** @var array<string, mixed> */
    protected array $filters;

    /** @var Collection<int, Poste>|null */
    protected ?Collection $rows = null;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        if ($this->rows === null) {
            $this->rows = Poste::query()
                ->filter($this->filters)
                ->orderByDesc('date_audit')
                ->get();
        }

        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Hostname',
            'N° Série',
            'Utilisateur',
            'Fabricant',
            'Modèle',
            'OS',
            'Version OS',
            'Antivirus Defender',
            'Firewall',
            'BitLocker',
            'USB stockage bloqué',
            'Adresse MAC',
            'Adresse IP',
            'Date audit',
            'Créé le',
            'Mis à jour le',
        ];
    }

    /**
     * @param  Poste  $poste
     */
    public function map($poste): array
    {
        return [
            $poste->id,
            $poste->hostname,
            $poste->numero_serie,
            $poste->utilisateur_session,
            $poste->fabricant,
            $poste->modele,
            $poste->os,
            $poste->version_os,
            $poste->antivirus_defender ? 'Oui' : 'Non',
            $poste->firewall,
            $poste->bitlocker,
            $poste->usb_stockage_bloque ? 'Oui' : 'Non',
            $poste->adresse_mac,
            $poste->adresse_ip,
            optional($poste->date_audit)?->format('d/m/Y H:i:s'),
            optional($poste->created_at)?->format('d/m/Y H:i:s'),
            optional($poste->updated_at)?->format('d/m/Y H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $styles = [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E60012'],
                ],
            ],
        ];

        // Ligne rouge si antivirus off ou BitLocker C: non actif
        foreach ($this->collection()->values() as $index => $poste) {
            $row = $index + 2;
            $alerte = ! $poste->antivirus_defender || ! $poste->isBitlockerActif();
            if ($alerte) {
                $styles[$row] = [
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFCDD2'],
                    ],
                ];
            }
        }

        return $styles;
    }

    public function title(): string
    {
        return 'Audits postes';
    }
}
