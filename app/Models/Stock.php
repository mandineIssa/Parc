<?php

namespace App\Models;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use Auditable;
    protected $table = 'stock';
    protected $fillable = [
        'numero_serie',
        'type_stock',
        'localisation_physique',
        'etat',
        'quantite',
        'date_entree',
        'date_sortie',
        'observations'
    ];
    
    protected $casts = [
        'date_entree' => 'date',
        'date_sortie' => 'date',
        'quantite' => 'integer'
    ];
    
    // Relation avec Equipment
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'numero_serie', 'numero_serie');
    }
    
    // Relation avec Celer
    public function celer()
    {
        return $this->hasOne(Celer::class);
    }
    
    // Relation avec Deceler
    public function deceler()
    {
        return $this->hasOne(Deceler::class);
    }
}