<?php

namespace Tests\Unit;

use App\Services\ParcMassExcelSchema;
use PHPUnit\Framework\TestCase;

class ParcMassExcelSchemaTest extends TestCase
{
    public function test_serial_header_aliases_match_export_label(): void
    {
        $aliases = ParcMassExcelSchema::headerAliases();
        $this->assertSame('numero_serie', $aliases[ParcMassExcelSchema::normalize('serial number')]);
        $this->assertSame('nom', $aliases[ParcMassExcelSchema::normalize("NOM DE L'EQUIPEMENT")]);
        $this->assertSame('utilisateur_nom', $aliases[ParcMassExcelSchema::normalize('NOM')]);
        $this->assertSame('date_livraison', $aliases[ParcMassExcelSchema::normalize("DATE D'ACHAT")]);
    }

    public function test_normalize_strips_accents(): void
    {
        $this->assertSame(
            'etat bon moyen mauvais',
            ParcMassExcelSchema::normalize('État (Bon / Moyen / Mauvais)')
        );
    }
}
