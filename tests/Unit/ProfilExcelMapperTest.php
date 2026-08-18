<?php

namespace Tests\Unit;

use App\Support\ProfilExcelMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProfilExcelMapperTest extends TestCase
{
    public function test_headings_match_cofina_template_order(): void
    {
        $this->assertSame([
            'Matricule',
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'Fonction',
            'Département',
            'Site',
            'Filiale',
            'Type de contrat',
            'Statut',
            'N+1 (Nom Prénom)',
            'N+1 (Matricule)',
            'N+2 (Nom Prénom)',
            'N+2 (Matricule)',
            'Date de création',
            'Date de modification',
        ], ProfilExcelMapper::headings(true));

        $withoutDates = ProfilExcelMapper::headings(false);
        $this->assertCount(15, $withoutDates);
        $this->assertSame('N+2 (Matricule)', end($withoutDates));
    }

    #[DataProvider('departementProvider')]
    public function test_it_normalizes_departements(string $input, string $expected): void
    {
        $this->assertSame($expected, ProfilExcelMapper::normalizeDepartement($input));
    }

    public static function departementProvider(): array
    {
        return [
            ['informatique', 'IT'],
            ['Direction Informatique', 'IT'],
            ['exploitation nord', 'EXPLOITATION'],
            ['Direction Finance', 'FINANCE'],
            ['  rh  ', 'RH'],
        ];
    }

    public function test_it_maps_aliases_regardless_of_column_order(): void
    {
        $row = [
            'Last Name' => 'Dupont',
            'firstname' => 'Jean',
            'e-mail' => 'Jean.Dupont@Example.com',
            'phone' => '+221771234567',
        ];

        $normalized = [];
        foreach ($row as $key => $value) {
            $normalized[ProfilExcelMapper::normalizeKey($key)] = $value;
        }

        $this->assertSame('Dupont', ProfilExcelMapper::value($normalized, 'nom'));
        $this->assertSame('Jean', ProfilExcelMapper::value($normalized, 'prenom'));
        $this->assertSame('Jean.Dupont@Example.com', ProfilExcelMapper::value($normalized, 'email'));
        $this->assertSame('jean.dupont@example.com', ProfilExcelMapper::normalizeEmail(ProfilExcelMapper::value($normalized, 'email')));
        $this->assertSame('+221771234567', ProfilExcelMapper::value($normalized, 'telephone'));
    }

    public function test_contract_and_status_fallbacks(): void
    {
        $this->assertSame('CDI', ProfilExcelMapper::normalizeContractType('inconnu'));
        $this->assertSame('CDI', ProfilExcelMapper::normalizeContractType(''));
        $this->assertSame('CDD', ProfilExcelMapper::normalizeContractType('cdd'));
        $this->assertSame('Stagiaire', ProfilExcelMapper::normalizeContractType('stage'));
        $this->assertSame('actif', ProfilExcelMapper::normalizeStatut('inconnu'));
        $this->assertSame('inactif', ProfilExcelMapper::normalizeStatut('inactive'));
    }

    public function test_n_plus_1_heading_aliases(): void
    {
        $row = [ProfilExcelMapper::normalizeKey('N+1 (Nom Prénom)') => 'Martin Marie'];

        $this->assertSame('Martin Marie', ProfilExcelMapper::value($row, 'n_plus_1'));
    }
}
