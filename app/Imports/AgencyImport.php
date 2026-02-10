<?php

namespace App\Imports;

use App\Models\Agency;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Validation\Rule;

class AgencyImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation,
    SkipsOnError,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    private $importedCount = 0;
    private $skippedCount = 0;
    private $customErrors = []; // ✅ RENOMMÉ : $errors → $customErrors

    /**
     * Mapping des colonnes Excel vers les champs de la base
     */
    private $columnMapping = [
        'code' => ['code', 'code_agence', 'codage', 'agence_code'],
        'nom' => ['nom', 'nom_agence', 'agence', 'name', 'agency_name'],
        'ville' => ['ville', 'city', 'localite'],
        'adresse' => ['adresse', 'address', 'rue', 'street'],
        'telephone' => ['telephone', 'phone', 'tel', 'contact', 'phone_number'],
        'email' => ['email', 'mail', 'courriel', 'e_mail']
    ];

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Normaliser les clés du tableau
        $normalizedRow = $this->normalizeRowKeys($row);
        
        // Vérifier si les champs requis sont présents
        if (!$this->hasRequiredFields($normalizedRow)) {
            $this->skippedCount++;
            return null;
        }

        // Vérifier si l'agence existe déjà
        $existingAgency = Agency::where('code', $normalizedRow['code'])->first();
        if ($existingAgency) {
            $this->skippedCount++;
            $this->customErrors[] = "L'agence avec le code {$normalizedRow['code']} existe déjà (ligne " . ($this->importedCount + $this->skippedCount + 1) . ")";
            return null;
        }

        $this->importedCount++;

        return new Agency([
            'code' => $normalizedRow['code'] ?? null,
            'nom' => $normalizedRow['nom'] ?? null,
            'ville' => $normalizedRow['ville'] ?? null,
            'adresse' => $normalizedRow['adresse'] ?? null,
            'telephone' => $normalizedRow['telephone'] ?? null,
            'email' => $normalizedRow['email'] ?? null,
        ]);
    }

    /**
     * Normaliser les clés du tableau selon le mapping
     */
    private function normalizeRowKeys(array $row): array
    {
        $normalized = [];
        
        foreach ($this->columnMapping as $dbField => $excelHeaders) {
            foreach ($excelHeaders as $header) {
                $headerLower = strtolower(trim($header));
                
                foreach ($row as $key => $value) {
                    $keyLower = strtolower(trim($key));
                    
                    if ($keyLower === $headerLower || $this->stringsMatch($keyLower, $headerLower)) {
                        $normalized[$dbField] = $value;
                        break 2;
                    }
                }
            }
        }
        
        return $normalized;
    }

    /**
     * Vérifier si deux chaînes sont similaires
     */
    private function stringsMatch(string $str1, string $str2): bool
    {
        $str1 = preg_replace('/[^a-z0-9]/', '', $str1);
        $str2 = preg_replace('/[^a-z0-9]/', '', $str2);
        
        return $str1 === $str2 || similar_text($str1, $str2) / max(strlen($str1), strlen($str2)) > 0.8;
    }

    /**
     * Vérifier si les champs requis sont présents
     */
    private function hasRequiredFields(array $row): bool
    {
        return !empty($row['code']) && !empty($row['nom']);
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            '*.code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('agencies', 'code')
            ],
            '*.nom' => 'required|string|max:100',
            '*.ville' => 'nullable|string|max:50',
            '*.adresse' => 'nullable|string|max:255',
            '*.telephone' => 'nullable|string|max:20',
            '*.email' => 'nullable|email|max:100',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    public function customValidationMessages(): array
    {
        return [
            '*.code.required' => 'Le code agence est obligatoire',
            '*.code.unique' => 'Le code agence existe déjà',
            '*.nom.required' => 'Le nom de l\'agence est obligatoire',
            '*.email.email' => 'L\'email doit être valide',
        ];
    }

    /**
     * Taille du batch pour l'insertion
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Taille du chunk pour la lecture
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Récupérer les statistiques d'importation
     */
    public function getImportStats(): array
    {
        return [
            'imported' => $this->importedCount,
            'skipped' => $this->skippedCount,
            'errors' => $this->customErrors, // ✅ Utiliser $customErrors
            'failures' => $this->failures()
        ];
    }

    /**
     * Nom des colonnes attendues (pour le template)
     */
    public function getExpectedHeaders(): array
    {
        return [
            'code' => 'Code Agence (obligatoire)',
            'nom' => 'Nom Agence (obligatoire)',
            'ville' => 'Ville',
            'adresse' => 'Adresse',
            'telephone' => 'Téléphone',
            'email' => 'Email',
        ];
    }
}