<?php

namespace App\Services;

/**
 * Colonnes communes export / import en masse Parc (types informatiques).
 */
class ParcMassExcelSchema
{
    /**
     * @return array<string, array{label: string, key: string, width: int}>
     */
    public static function columns(): array
    {
        return [
            'A' => ['label' => 'NOM', 'key' => 'utilisateur_nom', 'width' => 18],
            'B' => ['label' => 'PRENOM', 'key' => 'utilisateur_prenom', 'width' => 18],
            'C' => ['label' => 'AGENCE', 'key' => 'agence', 'width' => 22],
            'D' => ['label' => 'Departeme', 'key' => 'departement', 'width' => 18],
            'E' => ['label' => 'POSTE', 'key' => 'poste', 'width' => 22],
            'F' => ['label' => 'Dotation (ordinateur)', 'key' => 'dotation', 'width' => 20],
            'G' => ['label' => "NOM DE L'EQUIPEMENT", 'key' => 'nom', 'width' => 26],
            'H' => ['label' => 'serial number', 'key' => 'numero_serie', 'width' => 18],
            'I' => ['label' => 'Marque/Modele', 'key' => 'marque', 'width' => 16],
            'J' => ['label' => 'Model PC', 'key' => 'modele', 'width' => 24],
            'K' => ['label' => 'DATE MISE EN SERVICE', 'key' => 'date_mise_service', 'width' => 20],
            'L' => ['label' => "DATE D'ACHAT", 'key' => 'date_livraison', 'width' => 16],
            'M' => ['label' => "PRIX D'ACHAT", 'key' => 'prix', 'width' => 14],
            'N' => ['label' => '', 'key' => '_n', 'width' => 4],
            'O' => ['label' => "Date prévue d'amortissement", 'key' => 'date_amortissement', 'width' => 26],
            'P' => ['label' => '', 'key' => '_p', 'width' => 4],
            'Q' => ['label' => 'Fournisseur', 'key' => 'fournisseur', 'width' => 18],
            'R' => ['label' => 'État (Bon / Moyen / Mauvais)', 'key' => 'etat', 'width' => 24],
        ];
    }

    public static function lastColumn(): string
    {
        $keys = array_keys(self::columns());

        return (string) end($keys);
    }

    public static function normalize(string $value): string
    {
        $value = trim(mb_strtolower($value));
        $value = strtr($value, [
            'à' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'î' => 'i', 'ï' => 'i',
            'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', '’' => "'", '`' => "'",
        ]);
        $value = preg_replace("/[^a-z0-9]+/", ' ', $value) ?? $value;

        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }

    /**
     * @return array<string, string> normalized header => key
     */
    public static function headerAliases(): array
    {
        $aliases = [];
        foreach (self::columns() as $col) {
            if ($col['label'] !== '') {
                $aliases[self::normalize($col['label'])] = $col['key'];
            }
        }
        foreach (self::columns() as $col) {
            $keyNorm = self::normalize($col['key']);
            if ($keyNorm !== '' && ! isset($aliases[$keyNorm])) {
                $aliases[$keyNorm] = $col['key'];
            }
        }

        return array_merge($aliases, [
            'nom utilisateur' => 'utilisateur_nom',
            'prenom utilisateur' => 'utilisateur_prenom',
            'departement' => 'departement',
            'dept' => 'departement',
            'poste affecte' => 'poste',
            'dotation' => 'dotation',
            'nom equipement' => 'nom',
            'nom de lequipement' => 'nom',
            'n serie' => 'numero_serie',
            'no serie' => 'numero_serie',
            'numero de serie' => 'numero_serie',
            'serial' => 'numero_serie',
            'serial number' => 'numero_serie',
            'marque' => 'marque',
            'marque modele' => 'marque',
            'modele' => 'modele',
            'model pc' => 'modele',
            'date mise en service' => 'date_mise_service',
            'date achat' => 'date_livraison',
            'prix achat' => 'prix',
            'prix' => 'prix',
            'date amortissement' => 'date_amortissement',
            'date prevue d amortissement' => 'date_amortissement',
            'etat' => 'etat',
            'etat bon moyen mauvais' => 'etat',
        ]);
    }
}
