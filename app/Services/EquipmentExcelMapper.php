<?php

namespace App\Services;

/**
 * Mapping commun export / import équipements.
 * Les libellés doivent rester identiques à EquipmentController::buildEquipmentXlsx.
 */
class EquipmentExcelMapper
{
    /**
     * Colonnes de l'export (clé interne => libellé Excel).
     *
     * @return array<string, string>
     */
    public static function headings(): array
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'numero_serie' => 'Numéro de série',
            'marque' => 'Marque',
            'modele' => 'Modèle',
            'categorie' => 'Catégorie',
            'sous_categorie' => 'Sous-catégorie',
            'nom' => 'Nom de l’équipement',
            'numero_codification' => 'N° codification',
            'etat' => 'État',
            'statut' => 'Statut',
            'prix' => 'Prix (FCFA)',
            'date_livraison' => 'Date livraison',
            'garantie' => 'Garantie',
            'reference_facture' => 'Réf. facture',
            'reference_installation' => 'Réf. installation',
            'fournisseur' => 'Fournisseur',
            'agence' => 'Agence',
            'localisation' => 'Localisation',
            'lieu_stockage' => 'Lieu stockage',
            'adresse_mac' => 'Adresse MAC',
            'adresse_ip' => 'Adresse IP',
            'departement' => 'Département',
            'poste_staff' => 'Poste staff',
            'date_mise_service' => 'Date mise en service',
            'date_amortissement' => 'Date amortissement',
            'contrat_maintenance' => 'Contrat maintenance',
            'notes' => 'Notes',
            'created_at' => 'Date création',
            'updated_at' => 'Dernière modif.',
            'processeur' => 'Processeur',
            'ram_capacite' => 'RAM',
            'stockage_capacite' => 'Stockage capacité',
            'type_stockage' => 'Type stockage',
            'systeme_exploitation' => 'Système exploit.',
            'taille_ecran' => 'Taille écran',
            'editeur' => 'Éditeur',
            'version' => 'Version',
            'type_licence' => 'Type licence',
            'nombre_licences' => 'Nb licences',
            'date_expiration_licence' => 'Exp. licence',
            'reference_licence' => 'Réf. licence',
            'type_switch' => 'Type switch',
            'ports_ethernet' => 'Ports Ethernet',
            'ports_poe' => 'Ports PoE',
            'vitesse_ports' => 'Vitesse ports',
            'type_routeur' => 'Type routeur',
            'nombre_ports_routeur' => 'Nb ports routeur',
            'type_wifi' => 'Type WiFi',
            'type_camera' => 'Type caméra',
            'resolution_camera' => 'Résolution caméra',
            'type_nvr_dvr' => 'Type NVR/DVR',
            'canaux_supportes' => 'Canaux supportés',
            'type_modem' => 'Type modem',
            'vitesse_max_modem' => 'Vitesse modem',
            'numero_unique_badge' => 'N° badge',
            'type_alarme' => 'Type alarme',
            'type_imprimante' => 'Type imprimante',
            'vitesse_impression' => 'Vitesse impression',
        ];
    }

    /**
     * Alias acceptés (fichier export, ancien template, CSV).
     *
     * @return array<string, string> normalized header => key
     */
    public static function aliases(): array
    {
        $aliases = [];
        foreach (self::headings() as $key => $label) {
            $aliases[self::normalize($label)] = $key;
            $aliases[self::normalize($key)] = $key;
        }

        $extra = [
            'n serie' => 'numero_serie',
            'no serie' => 'numero_serie',
            'numeroserie' => 'numero_serie',
            'serial' => 'numero_serie',
            'serial number' => 'numero_serie',
            'numero de serie' => 'numero_serie',
            'nom equipement' => 'nom',
            'nom de lequipement' => 'nom',
            'categorie id' => 'categorie',
            'agence id' => 'agence',
            'agency id' => 'agence',
            'fournisseur id' => 'fournisseur',
            'n codification' => 'numero_codification',
            'codification' => 'numero_codification',
            'prix fcfa' => 'prix',
            'ref facture' => 'reference_facture',
            'ref installation' => 'reference_installation',
            'date mise service' => 'date_mise_service',
            'derniere modif' => 'updated_at',
            'date modification' => 'updated_at',
            'sous categorie' => 'sous_categorie',
        ];

        return array_merge($aliases, $extra);
    }

    public static function normalize(?string $value): string
    {
        $value = trim((string) $value);
        $value = mb_strtolower($value);
        $value = strtr($value, [
            'à' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'î' => 'i', 'ï' => 'i',
            'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'œ' => 'oe',
            '’' => ' ', "'" => ' ',
            '°' => ' ', 'º' => ' ',
        ]);
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? $value;

        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }

    /**
     * @param  array<int, mixed>  $headerCells  index 1-based or 0-based
     * @return array<int, string> columnIndex (1-based) => field key
     */
    public static function mapHeaders(array $headerCells): array
    {
        $aliases = self::aliases();
        $map = [];

        foreach ($headerCells as $index => $label) {
            $col = is_int($index) && $index >= 1 ? $index : ((int) $index + 1);
            $normalized = self::normalize(is_scalar($label) ? (string) $label : '');
            if ($normalized === '') {
                continue;
            }
            if (isset($aliases[$normalized])) {
                $map[$col] = $aliases[$normalized];
            }
        }

        return $map;
    }

    public static function hasSerialColumn(array $columnMap): bool
    {
        return in_array('numero_serie', $columnMap, true);
    }

    /**
     * Colonnes ignorées à l'écriture (métadonnées système).
     *
     * @return list<string>
     */
    public static function readOnlyKeys(): array
    {
        return ['id', 'created_at', 'updated_at'];
    }

    /**
     * Clés specific_data (export complet).
     *
     * @return list<string>
     */
    public static function specificDataKeys(): array
    {
        return [
            'processeur', 'ram_capacite', 'stockage_capacite', 'type_stockage',
            'systeme_exploitation', 'taille_ecran', 'editeur', 'version',
            'type_licence', 'nombre_licences', 'date_expiration_licence', 'reference_licence',
            'type_switch', 'ports_ethernet', 'ports_poe', 'vitesse_ports',
            'type_routeur', 'nombre_ports_routeur', 'type_wifi',
            'type_camera', 'resolution_camera', 'type_nvr_dvr', 'canaux_supportes',
            'type_modem', 'vitesse_max_modem', 'numero_unique_badge', 'type_alarme',
            'type_imprimante', 'vitesse_impression',
        ];
    }

    public static function isEmptyValue(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }
        if (is_string($value)) {
            $trimmed = trim($value);

            return $trimmed === '' || in_array(mb_strtolower($trimmed), ['n/a', 'na', '-', 'null', '#n/a'], true);
        }

        return false;
    }
}
