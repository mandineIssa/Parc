<?php
// app/Models/Software.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Software extends Model {
    protected $table = 'software';
    
    protected $fillable = [
        'equipment_id', 'type_logiciel', 'nom_logiciel', 'editeur', 'version',
        'type_licence', 'nombre_licences', 'licences_utilisees', 'date_expiration_licence',
        'reference_licence', 'etat_logiciel', 'support_technique', 'responsable_it',
        'conformite_legale', 'notes_logiciel'
    ];
    
    protected $casts = [
        'support_technique' => 'boolean'
    ];
    
    public function equipment() {
        return $this->belongsTo(Equipment::class);
    }
}