<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Deceler extends Model
{
    use Auditable;
    protected $table = 'deceler';
    protected $fillable = [
        'stock_id',
        'origine',
        'numero_serie_origine',
        'date_retour',
        'raison_retour',
        'diagnostic',
        'etat_retour',
        'valeur_residuelle',
        'observations_retour',
        'transition_approval_id'
    ];
    
    protected $casts = [
        'date_retour' => 'date',
        'valeur_residuelle' => 'decimal:2'
    ];
    
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
    
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'numero_serie_origine', 'numero_serie');
    }
    // Dans app/Models/Stock.php, ajoutez :
public function deceler()
{
    return $this->hasOne(Deceler::class, 'stock_id');
}
}