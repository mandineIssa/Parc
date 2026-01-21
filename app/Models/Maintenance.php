<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Maintenance extends Model
{
    //use Auditable;
    use Auditable;
    protected $table = 'maintenance';
    protected $fillable = [
        'numero_serie',
        'date_depart',
        'date_retour_prevue',
        'date_retour_reelle',
        'type_maintenance',
        'prestataire',
        'cout',
        'statut',
        'description_panne',
        'travaux_realises',
        'observations'
    ];
    
    protected $casts = [
        'date_depart' => 'date',
        'date_retour_prevue' => 'date',
        'date_retour_reelle' => 'date',
        'cout' => 'decimal:2'
    ];
    
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'numero_serie', 'numero_serie');
    }

       // Si vous avez un modèle Technician
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id'); // ou le modèle approprié
    }
}