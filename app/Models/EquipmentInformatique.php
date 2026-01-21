<?php
// app/Models/EquipmentInformatique.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class EquipmentInformatique extends Model {
    use Auditable;
    protected $table = 'equipment_informatique';
    protected $fillable = [
        'equipment_id',
        'type_informatique',
        'etat_stock',
    ];

    public function equipment(): BelongsTo {
        return $this->belongsTo(Equipment::class);
    }
}
