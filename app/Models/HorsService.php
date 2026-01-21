<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class HorsService extends Model
{
    use Auditable;
    protected $table = 'hors_service';
    protected $fillable = [
        'numero_serie',
        'date_hors_service',
        'raison',
        'description_incident',
        'destinataire',
        'date_traitement',
        'valeur_residuelle',
        'justificatif',
        'observations'
    ];
    
    protected $casts = [
        'date_hors_service' => 'date',
        'date_traitement' => 'date',
        'valeur_residuelle' => 'decimal:2'
    ];
    
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'numero_serie', 'numero_serie');
    }
}