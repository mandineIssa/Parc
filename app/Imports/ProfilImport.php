<?php

namespace App\Imports;

use App\Models\Agency;
use App\Models\Departement;
use App\Models\Filiale;
use App\Models\User;
use App\Support\ProfilExcelMapper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProfilImport implements ToCollection, WithHeadingRow
{
    /** @var list<string> */
    public array $errors = [];

    public int $imported = 0;

    public int $skipped = 0;

    /** @var array<string, true> */
    private array $emails = [];

    /** @var array<string, true> */
    private array $matricules = [];

    /** @var array<string, Filiale> */
    private array $filiales = [];

    /** @var array<string, Agency> */
    private array $agenciesByName = [];

    /** @var array<string, true> */
    private array $agencyCodes = [];

    /** @var array<string, string> */
    private array $departements = [];

    private int $nextNumericMatricule = 1;

    private int $maxDepartementOrdre = 0;

    private string $passwordHash = '';

    public function collection(Collection $rows): void
    {
        $this->bootCaches();
        $pendingManagers = [];

        foreach ($rows as $index => $row) {
            $lineNumber = $index + 2;
            $data = $this->normalizeRow($row->toArray());

            if (ProfilExcelMapper::isEmptyRow($data)) {
                continue;
            }

            $nom = ProfilExcelMapper::value($data, 'nom');
            $prenom = ProfilExcelMapper::value($data, 'prenom');

            if ($nom === '' || $prenom === '') {
                $this->skipped++;
                $this->errors[] = "Ligne {$lineNumber} : Nom et Prénom sont obligatoires.";
                continue;
            }

            $matricule = ProfilExcelMapper::value($data, 'matricule');
            $email = ProfilExcelMapper::normalizeEmail(ProfilExcelMapper::value($data, 'email'));

            if ($matricule !== '' && isset($this->matricules[mb_strtolower($matricule)])) {
                $this->skipped++;
                $this->errors[] = "Ligne {$lineNumber} : matricule « {$matricule} » déjà existant.";
                continue;
            }

            if ($email && isset($this->emails[$email])) {
                $this->skipped++;
                $this->errors[] = "Ligne {$lineNumber} : email « {$email} » déjà existant.";
                continue;
            }

            if ($matricule === '') {
                $matricule = str_pad((string) $this->nextNumericMatricule, 6, '0', STR_PAD_LEFT);
                $this->nextNumericMatricule++;
                while (isset($this->matricules[mb_strtolower($matricule)])) {
                    $matricule = str_pad((string) $this->nextNumericMatricule, 6, '0', STR_PAD_LEFT);
                    $this->nextNumericMatricule++;
                }
            }

            if (! $email) {
                $email = ProfilExcelMapper::placeholderEmail($nom, $prenom, $matricule);
                if (isset($this->emails[$email])) {
                    $email = Str::uuid().'@import.parc.local';
                }
            }

            $filiale = $this->filiale(ProfilExcelMapper::value($data, 'filiale'));
            $agency = $this->agency(ProfilExcelMapper::value($data, 'site'), $filiale);

            $user = User::query()->create([
                'matricule' => $matricule,
                'name' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'telephone' => ProfilExcelMapper::value($data, 'telephone') ?: null,
                'fonction' => ProfilExcelMapper::value($data, 'fonction') ?: null,
                'departement' => $this->departement(ProfilExcelMapper::value($data, 'departement')),
                'type_contrat' => ProfilExcelMapper::normalizeContractType(ProfilExcelMapper::value($data, 'type_contrat')),
                'statut' => ProfilExcelMapper::normalizeStatut(ProfilExcelMapper::value($data, 'statut')),
                'filiale_id' => $filiale->id,
                'agency_id' => $agency?->id,
                'role' => 'user',
                'password' => $this->passwordHash,
                'email_verified_at' => now(),
            ]);

            $this->matricules[mb_strtolower($matricule)] = true;
            $this->emails[$email] = true;

            $pendingManagers[] = [
                'user' => $user,
                'n1_name' => ProfilExcelMapper::value($data, 'n_plus_1'),
                'n1_matricule' => ProfilExcelMapper::value($data, 'n_plus_1_matricule'),
                'n2_name' => ProfilExcelMapper::value($data, 'n_plus_2'),
                'n2_matricule' => ProfilExcelMapper::value($data, 'n_plus_2_matricule'),
            ];

            $this->imported++;
        }

        $allUsers = User::query()->get(['id', 'name', 'prenom', 'matricule', 'email']);

        foreach ($pendingManagers as $pending) {
            /** @var User $user */
            $user = $pending['user'];
            $n1 = ProfilExcelMapper::resolveManager($pending['n1_name'], $pending['n1_matricule'], null, $allUsers);
            $n2 = ProfilExcelMapper::resolveManager($pending['n2_name'], $pending['n2_matricule'], null, $allUsers);

            $updates = [];
            if ($n1 && $n1->id !== $user->id) {
                $updates['n_plus_1_id'] = $n1->id;
            }
            if ($n2 && $n2->id !== $user->id) {
                $updates['n_plus_2_id'] = $n2->id;
            }
            if ($updates !== []) {
                $user->update($updates);
            }
        }
    }

    private function bootCaches(): void
    {
        $this->passwordHash = Hash::make(Str::random(32));

        foreach (User::query()->get(['matricule', 'email']) as $user) {
            if (filled($user->email)) {
                $this->emails[strtolower((string) $user->email)] = true;
            }
            if (filled($user->matricule)) {
                $this->matricules[mb_strtolower((string) $user->matricule)] = true;
                if (preg_match('/^[0-9]+$/', (string) $user->matricule)) {
                    $this->nextNumericMatricule = max($this->nextNumericMatricule, ((int) $user->matricule) + 1);
                }
            }
        }

        foreach (Filiale::query()->get() as $filiale) {
            $this->filiales[$filiale->nom] = $filiale;
        }

        foreach (Agency::query()->get() as $agency) {
            $this->agenciesByName[$agency->nom] = $agency;
            if (filled($agency->code)) {
                $this->agencyCodes[$agency->code] = true;
            }
        }

        foreach (Departement::query()->get() as $departement) {
            $this->departements[mb_strtoupper((string) $departement->nom)] = $departement->nom;
            $this->maxDepartementOrdre = max($this->maxDepartementOrdre, (int) $departement->ordre);
        }
    }

    private function filiale(string $nom): Filiale
    {
        $nom = trim($nom);
        if ($nom === '') {
            $nom = 'Sénégal';
        }

        if (! isset($this->filiales[$nom])) {
            $this->filiales[$nom] = Filiale::query()->firstOrCreate(
                ['nom' => $nom],
                ['actif' => true]
            );
        }

        return $this->filiales[$nom];
    }

    private function agency(string $site, Filiale $filiale): ?Agency
    {
        $nom = trim($site);
        if ($nom === '') {
            return null;
        }

        if (! isset($this->agenciesByName[$nom])) {
            $agency = Agency::query()->create([
                'code' => $this->nextAgencyCode($nom),
                'nom' => $nom,
                'ville' => $nom,
                'filiale_id' => $filiale->id,
            ]);
            $this->agenciesByName[$nom] = $agency;
            $this->agencyCodes[$agency->code] = true;

            return $agency;
        }

        $agency = $this->agenciesByName[$nom];
        if (! $agency->filiale_id) {
            $agency->filiale_id = $filiale->id;
            $agency->save();
        }

        return $agency;
    }

    private function nextAgencyCode(string $nom): string
    {
        $base = strtoupper(preg_replace('/[^A-Z0-9]/i', '', ProfilExcelMapper::stripAccents($nom)) ?? '');
        $base = substr($base !== '' ? $base : 'SITE', 0, 6);
        $candidate = $base;
        $i = 1;
        while (isset($this->agencyCodes[$candidate])) {
            $suffix = (string) $i;
            $candidate = substr($base, 0, 10 - strlen($suffix)).$suffix;
            $i++;
        }

        return $candidate;
    }

    private function departement(string $value): ?string
    {
        $nom = ProfilExcelMapper::normalizeDepartement($value);
        if ($nom === null) {
            return null;
        }

        if (! isset($this->departements[$nom])) {
            $this->maxDepartementOrdre++;
            Departement::query()->create([
                'nom' => $nom,
                'actif' => true,
                'ordre' => $this->maxDepartementOrdre,
            ]);
            $this->departements[$nom] = $nom;
        }

        return $this->departements[$nom];
    }

    /**
     * @param  array<string|int, mixed>  $row
     * @return array<string, mixed>
     */
    private function normalizeRow(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $normalized[ProfilExcelMapper::normalizeKey((string) $key)] = $value;
        }

        return $normalized;
    }
}
