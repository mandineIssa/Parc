<?php
// app/Models/Equipment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Equipment extends Model
{
    use HasFactory, Auditable;
    protected $fillable = [
        'numero_serie', 
        'type', 
        'categorie', 
        'sous_categorie',
        'marque', 
        'modele', 
        'agency_id',
        'localisation', 
        'fournisseur_id',
        'date_livraison', 
        'prix', 
        'garantie', 
        'reference_facture',
        'etat', 
        'adresse_mac', 
        'statut',
        'notes',
        'date_mise_service',
        'date_amortissement'
    ];
    
    protected $attributes = [
        'statut' => 'stock'
    ];
    
    protected $casts = [
        'date_livraison' => 'date',
        'date_mise_service' => 'date',
        'date_amortissement' => 'date',
        'prix' => 'decimal:2'
    ];
    
    /**
     * Relation avec l'agence
     */
    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
    
    /**
     * Relation avec le fournisseur
     */
    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'fournisseur_id');
    }
    
    /**
     * Relation avec les détails spécifiques
     * NOTE: Le nom doit correspondre à la table equipment_details
     */
    public function detail(): HasOne
    {
        return $this->hasOne(EquipmentDetail::class, 'equipment_id');
    }
    
    /**
     * Alias pour compatibilité (si vous utilisez "details" au lieu de "detail")
     */
    public function details(): HasOne
    {
        return $this->hasOne(EquipmentDetail::class, 'equipment_id');
    }
    
    /**
     * Relation avec le stock
     */
    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class, 'numero_serie', 'numero_serie');
    }
    
    /**
     * Relation avec le parc
     */
    public function parc(): HasOne
    {
        return $this->hasOne(Parc::class, 'numero_serie', 'numero_serie');
    }
    
    /**
     * Relation avec la maintenance
     */
    public function maintenance(): HasMany
    {
        return $this->hasMany(Maintenance::class, 'numero_serie', 'numero_serie');
    }
    
    /**
     * Relation avec hors service
     */
    public function horsServices(): HasMany
    {
        return $this->hasMany(HorsService::class, 'numero_serie', 'numero_serie');
    }
    
    /**
     * Relation avec perdu
     */
    public function perdus(): HasMany
    {
        return $this->hasMany(Perdu::class, 'numero_serie', 'numero_serie');
    }
    
    /**
     * Accesseur pour le statut complet
     */
    public function getStatutCompletAttribute(): string
    {
        $statut = $this->statut;
        $details = '';
        
        if ($this->detail) {
            switch ($this->type) {
                case 'Réseau':
                    $details = $this->detail->specific_data['etat_reseau'] ?? '';
                    break;
                case 'Électronique':
                    $details = $this->detail->specific_data['etat_electronique'] ?? '';
                    break;
                case 'Informatique':
                    $details = $this->detail->specific_data['etat_stock'] ?? '';
                    break;
            }
        }
        
        return $details ? "$statut - $details" : $statut;
    }
    
    /**
     * Scope pour filtrer par statut
     */
    public function scopeParStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }
    
    /**
     * Scope pour filtrer par type
     */
    public function scopeParType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Vérifier si l'équipement est en stock
     */
    public function estEnStock(): bool
    {
        return $this->statut === 'stock';
    }


// Ou peut-être :

  public function categorie()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }
public function showTransition(Equipment $equipment)
{
    return view('equipment.transition', compact('equipment'));
}

    public function transitionApprovals()
    {
        return $this->hasMany(TransitionApproval::class);
    }
    
    // Si vous voulez accéder au dernier approval
    public function latestTransitionApproval()
    {
        return $this->hasOne(TransitionApproval::class)->latestOfMany();
    }

// Ou en français :


public function audits(): MorphMany
{
    return $this->morphMany(Audit::class, 'model')->latest();
}


}