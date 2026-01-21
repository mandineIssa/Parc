<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Celer extends Model
{
    use Auditable;
    protected $table = 'celer';
    protected $fillable = [
        'stock_id',
        'date_acquisition',
        'numero_facture',
        'certificat_garantie',
        'emballage_origine',
        'caracteristiques_specifiques'
    ];
    
    protected $casts = [
        'date_acquisition' => 'date',
        'emballage_origine' => 'boolean'
    ];
    
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}