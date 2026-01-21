<?php
// app/Models/EquipmentDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class EquipmentDetail extends Model
{
    use Auditable;
    protected $table = 'equipment_details'; // Assurez-vous que c'est le bon nom de table
    
    protected $fillable = [
        'equipment_id',
        'type',
        'categorie',
        'sous_categorie',
        'etat_specifique',
        'adresse_ip_specifique',
        'adresse_mac_specifique',
        'departement_specifique',
        'poste_staff_specifique',
        'numero_codification_specifique',
        'contrat_maintenance',
        'date_debut_contrat',
        'date_fin_contrat',
        'periodicite_maintenance',
        'type_contrat',
        'specific_data'
    ];
    
    protected $casts = [
        'contrat_maintenance' => 'boolean',
        'date_debut_contrat' => 'date',
        'date_fin_contrat' => 'date',
        'specific_data' => 'array'
    ];
    
    /**
     * Relation avec l'équipement parent
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
    
    /**
     * Accesseur pour les données spécifiques
     */
    public function getSpecificDataAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }
    
    /**
     * Mutateur pour les données spécifiques
     */
    public function setSpecificDataAttribute($value)
    {
        $this->attributes['specific_data'] = json_encode($value ?: []);
    }
}