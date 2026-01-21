<?php

// app/Models/EquipmentElectronique.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class EquipmentElectronique extends Model {
    use Auditable;
    protected $table = 'equipment_electronique';
    protected $fillable = [
        'equipment_id',
        'type_electronique',
        'etat_electronique',
        'derniere_verification_technique',
        'contrat_maintenance',
        'type_contrat',
        'date_debut_contrat',
        'date_fin_contrat',
        'periodicite_maintenance',
        'derniere_maintenance',
        'prochaine_maintenance',
        'conforme_normes_securite',
    ];

    protected $casts = [
        'derniere_verification_technique' => 'datetime',
        'date_debut_contrat' => 'date',
        'date_fin_contrat' => 'date',
        'derniere_maintenance' => 'datetime',
        'prochaine_maintenance' => 'datetime',
        'contrat_maintenance' => 'boolean',
        'conforme_normes_securite' => 'boolean',
    ];

    public function equipment(): BelongsTo {
        return $this->belongsTo(Equipment::class);
    }
}
