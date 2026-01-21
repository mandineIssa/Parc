<?php

namespace App\Imports;

use App\Models\Parc;
use App\Models\Equipment;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Carbon\Carbon;

class ParcImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;
    
    private $rowCount = 0;

    public function model(array $row)
    {
        $this->rowCount++;
        
        // Vérifier si l'équipement existe
        $equipment = Equipment::where('numero_serie', $row['numero_serie'])->first();
        if (!$equipment) {
            throw new \Exception("Équipement avec le numéro de série {$row['numero_serie']} n'existe pas");
        }
        
        // Vérifier si l'utilisateur existe
        $user = User::where('email', $row['utilisateur_email'])->first();
        if (!$user) {
            throw new \Exception("Utilisateur avec l'email {$row['utilisateur_email']} n'existe pas");
        }
        
        // Vérifier si une affectation existe déjà pour cet équipement
        $parc = Parc::where('numero_serie', $row['numero_serie'])->first();
        
        if ($parc) {
            // Mettre à jour l'affectation existante
            $parc->update([
                'utilisateur_id' => $user->id,
                'departement' => $row['departement'],
                'poste_affecte' => $row['poste_affecte'],
                'date_affectation' => Carbon::parse($row['date_affectation']),
                'date_retour_prevue' => !empty($row['date_retour_prevue']) ? Carbon::parse($row['date_retour_prevue']) : null,
                'statut_usage' => $row['statut_usage'],
                'notes_affectation' => $row['notes_affectation'] ?? null,
            ]);
            
            return null; // Pas de nouveau modèle créé
        }
        
        // Créer une nouvelle affectation
        return new Parc([
            'numero_serie' => $row['numero_serie'],
            'utilisateur_id' => $user->id,
            'departement' => $row['departement'],
            'poste_affecte' => $row['poste_affecte'],
            'date_affectation' => Carbon::parse($row['date_affectation']),
            'date_retour_prevue' => !empty($row['date_retour_prevue']) ? Carbon::parse($row['date_retour_prevue']) : null,
            'statut_usage' => $row['statut_usage'],
            'notes_affectation' => $row['notes_affectation'] ?? null,
        ]);
    }
    
    public function rules(): array
    {
        return [
            'numero_serie' => 'required|string|max:255',
            'utilisateur_email' => 'required|email',
            'departement' => 'required|string|max:255',
            'poste_affecte' => 'required|string|max:255',
            'date_affectation' => 'required|date',
            'date_retour_prevue' => 'nullable|date',
            'statut_usage' => 'required|in:en_service,maintenance,reserve',
        ];
    }
    
    public function getRowCount()
    {
        return $this->rowCount;
    }
}