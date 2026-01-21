<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Parc extends Model
{
    use Auditable;
    protected $table = 'parc';
    /*protected $fillable = [
        'numero_serie',
        'utilisateur_id',
        'departement',
        'poste_affecte',
        'date_affectation',
        'date_retour_prevue',
        'statut_usage',
        'notes_affectation'
    ];*/
      
    
    protected $fillable = [
        'numero_serie',
        'utilisateur_id',
        'departement',
        'poste_affecte',
        'date_affectation',
        'statut_usage',
        'notes_affectation',
        'transition_approval_id',
    ];
     
     use HasFactory;
   
    
    protected $casts = [
        'date_affectation' => 'date',
        'date_retour_prevue' => 'date'
    ];
    
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'numero_serie', 'numero_serie');
    }
    
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
     public function approval()
    {
        return $this->belongsTo(TransitionApproval::class, 'transition_approval_id');
    }
}