<?php

// app/Models/EquipmentReseau.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class EquipmentReseau extends Model {
    use Auditable;
    protected $table = 'equipment_reseau';
    protected $fillable = [
        'equipment_id',
        'type_reseau',
        'etat_reseau',
    ];

    public function equipment(): BelongsTo {
        return $this->belongsTo(Equipment::class);
    }
}
