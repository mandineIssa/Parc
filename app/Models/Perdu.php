<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Perdu extends Model
{
    use Auditable;
    protected $table = 'perdu';
    protected $fillable = [
        'numero_serie',
        'date_disparition',
        'lieu_disparition',
        'type_disparition',
        'circonstances',
        'plainte_deposee',
        'numero_plainte',
        'valeur_assuree',
        'statut_recherche',
        'observations'
    ];
    
    protected $casts = [
        'date_disparition' => 'date',
        'plainte_deposee' => 'boolean',
        'valeur_assuree' => 'decimal:2'
    ];
    
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'numero_serie', 'numero_serie');
    }
}