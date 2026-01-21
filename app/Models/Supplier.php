<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class Supplier extends Model
{
    use Auditable;
    protected $fillable = [
        'nom',
        'contact',  // Supprimez l'espace après 'contact'
        'email',
        'telephone',
        'adresse',
        'ville',
        'website',
        'status',
        'notes'
    ];
    
    /**
     * Get the equipment for this supplier.
     */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'fournisseur_id');
    }
    
    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'pending' => 'En attente',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }
    
    /**
     * Get the status badge class.
     */
    public function getStatusBadgeAttribute()
    {
        $classes = [
            'active' => 'success',
            'inactive' => 'danger',
            'pending' => 'warning',
        ];
        
        return $classes[$this->status] ?? 'secondary';
    }
}