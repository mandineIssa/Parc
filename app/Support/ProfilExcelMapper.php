<?php

namespace App\Support;

use App\Models\Agency;
use App\Models\Departement;
use App\Models\Filiale;
use App\Models\User;
use Illuminate\Support\Str;

class ProfilExcelMapper
{
    /** @return list<string> */
    public static function headings(bool $withDates = true): array
    {
        $headings = [
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
        ];

        if ($withDates) {
            $headings[] = 'Date de création';
            $headings[] = 'Date de modification';
        }

        return $headings;
    }

    public static function normalizeKey(string $key): string
    {
        $key = mb_strtolower(trim(self::stripAccents($key)));
        $key = str_replace(['+', '(', ')', '/', '\\'], ' ', $key);
        $key = trim((string) preg_replace('/[\s\-]+/', '_', $key));

        return trim($key, '_');
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function value(array $row, string $field): string
    {
        $aliases = self::aliases()[$field] ?? [];
        foreach ($row as $key => $value) {
            $normalized = self::normalizeKey((string) $key);
            if (in_array($normalized, $aliases, true)) {
                return trim((string) ($value ?? ''));
            }
        }

        return '';
    }

    public static function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) ($value ?? '')) !== '') {
                return false;
            }
        }

        return true;
    }

    public static function normalizeEmail(?string $email): ?string
    {
        $email = strtolower(trim((string) $email));

        return $email === '' ? null : $email;
    }

    public static function normalizeContractType(?string $value): string
    {
        $normalized = mb_strtoupper(trim(self::stripAccents((string) $value)));

        return match ($normalized) {
            'CDI' => 'CDI',
            'CDD' => 'CDD',
            'STAGIAIRE', 'STAGE' => 'Stagiaire',
            'AUTRE', 'OTHER' => 'Autre',
            '' => 'CDI',
            default => 'CDI',
        };
    }

    public static function normalizeStatut(?string $value): string
    {
        $normalized = mb_strtolower(trim(self::stripAccents((string) $value)));

        return match ($normalized) {
            'inactif', 'inactive', '0', 'non' => 'inactif',
            'actif', 'active', '1', 'oui', '' => 'actif',
            default => 'actif',
        };
    }

    public static function normalizeDepartement(?string $value): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $withoutPrefix = trim((string) preg_replace('/^direction\s+/iu', '', $raw));
        $folded = mb_strtolower(self::stripAccents($withoutPrefix));

        if (str_contains($folded, 'informatique')) {
            return 'IT';
        }

        if (str_contains($folded, 'exploitation')) {
            return 'EXPLOITATION';
        }

        return mb_strtoupper($withoutPrefix);
    }

    public static function resolveDepartement(?string $value): ?string
    {
        $nom = self::normalizeDepartement($value);
        if ($nom === null) {
            return null;
        }

        $existing = Departement::query()
            ->get()
            ->first(fn (Departement $departement) => mb_strtoupper((string) $departement->nom) === $nom);

        if ($existing) {
            return $existing->nom;
        }

        $maxOrdre = (int) Departement::query()->max('ordre');
        $created = Departement::query()->create([
            'nom' => $nom,
            'actif' => true,
            'ordre' => $maxOrdre + 1,
        ]);

        return $created->nom;
    }

    public static function resolveFiliale(?string $value): Filiale
    {
        $nom = trim((string) $value);
        if ($nom === '') {
            return Filiale::defaultSenegal();
        }

        return Filiale::query()->firstOrCreate(
            ['nom' => $nom],
            ['actif' => true]
        );
    }

    public static function resolveAgency(?string $site, Filiale $filiale): ?Agency
    {
        $nom = trim((string) $site);
        if ($nom === '') {
            return null;
        }

        $agency = Agency::query()->where('nom', $nom)->first();
        if (! $agency) {
            $agency = Agency::query()->create([
                'code' => self::generateAgencyCode($nom),
                'nom' => $nom,
                'ville' => $nom,
                'filiale_id' => $filiale->id,
            ]);
        } elseif (! $agency->filiale_id) {
            $agency->filiale_id = $filiale->id;
            $agency->save();
        }

        return $agency;
    }

    public static function generateAgencyCode(string $nom): string
    {
        $base = strtoupper(preg_replace('/[^A-Z0-9]/i', '', self::stripAccents($nom)) ?? '');
        $base = substr($base !== '' ? $base : 'SITE', 0, 6);

        $candidate = $base;
        $i = 1;
        while (Agency::query()->where('code', $candidate)->exists()) {
            $suffix = (string) $i;
            $candidate = substr($base, 0, 10 - strlen($suffix)).$suffix;
            $i++;
        }

        return $candidate;
    }

    /**
     * @param  iterable<int, User>|null  $users
     */
    public static function resolveManager(?string $name, ?string $matricule, ?string $email = null, ?iterable $users = null): ?User
    {
        $matricule = trim((string) $matricule);
        $collection = $users !== null ? collect($users) : null;

        if ($matricule !== '') {
            $found = $collection
                ? $collection->first(fn (User $user) => strcasecmp((string) $user->matricule, $matricule) === 0)
                : User::query()->where('matricule', $matricule)->first();
            if ($found) {
                return $found;
            }
        }

        $name = trim((string) $name);
        if ($email === null && filter_var($name, FILTER_VALIDATE_EMAIL)) {
            $email = self::normalizeEmail($name);
            $name = '';
        }

        $email = self::normalizeEmail($email);
        if ($email) {
            $found = $users !== null
                ? collect($users)->first(fn (User $user) => strtolower((string) $user->email) === $email)
                : User::query()->where('email', $email)->first();
            if ($found) {
                return $found;
            }
        }
        if ($name === '') {
            return null;
        }

        $needle = self::foldName($name);
        $collection = $users !== null
            ? collect($users)
            : User::query()->get(['id', 'name', 'prenom', 'matricule', 'email']);

        return $collection->first(function (User $user) use ($needle) {
            $nomPrenom = self::foldName(trim(($user->name ?? '').' '.($user->prenom ?? '')));
            $prenomNom = self::foldName(trim(($user->prenom ?? '').' '.($user->name ?? '')));

            return $needle !== '' && ($needle === $nomPrenom || $needle === $prenomNom);
        });
    }

    public static function displayName(?User $user): string
    {
        if (! $user) {
            return '';
        }

        return trim(($user->name ?? '').' '.($user->prenom ?? ''));
    }

    public static function foldName(string $value): string
    {
        $value = mb_strtolower(self::stripAccents($value));

        return (string) preg_replace('/\s+/', ' ', trim($value));
    }

    public static function stripAccents(string $value): string
    {
        $transliterated = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $value = $transliterated !== false ? $transliterated : $value;

        return str_replace(["'", '`', '^', '"', '~'], '', $value);
    }

    public static function placeholderEmail(string $name, string $prenom, string $matricule): string
    {
        $slug = Str::slug($prenom.'-'.$name);
        if ($slug === '') {
            $slug = 'profil';
        }

        return strtolower($slug.'-'.$matricule.'@import.parc.local');
    }

    /**
     * @return array<string, list<string>>
     */
    public static function aliases(): array
    {
        return [
            'matricule' => ['matricule', 'mat', 'id', 'employee_id'],
            'nom' => ['nom', 'name', 'lastname', 'last_name'],
            'prenom' => ['prenom', 'firstname', 'first_name'],
            'email' => ['email', 'e_mail', 'mail'],
            'telephone' => ['telephone', 'tel', 'phone', 'mobile'],
            'fonction' => ['fonction', 'function', 'poste', 'job', 'position'],
            'departement' => ['departement', 'department', 'dept'],
            'site' => ['site', 'agence', 'agency', 'location'],
            'filiale' => ['filiale', 'filiale_nom', 'subsidiary', 'filiales', 'environnement'],
            'type_contrat' => ['type_de_contrat', 'type_contrat', 'type_contrat', 'contract_type', 'contrat'],
            'statut' => ['statut', 'status', 'etat', 'statut_actif'],
            'n_plus_1' => [
                'n_1', 'n1', 'n_plus_1', 'n_plus_1_nom_prenom', 'n1_nom_prenom', 'n_1_nom_prenom',
                'superieur', 'superieur_hierarchique', 'manager', 'responsable',
            ],
            'n_plus_1_matricule' => ['n_1_matricule', 'n1_matricule', 'n_plus_1_matricule'],
            'n_plus_2' => ['n_2', 'n2', 'n_plus_2', 'n_plus_2_nom_prenom', 'n2_nom_prenom', 'n_2_nom_prenom'],
            'n_plus_2_matricule' => ['n_2_matricule', 'n2_matricule', 'n_plus_2_matricule'],
        ];
    }
}
