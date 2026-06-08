<?php
// app/Models/Equipment.php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;
use App\Models\Reaffectation;

class Equipment extends Model
{
    use HasFactory, Auditable;
    protected $fillable = [
        'numero_serie',
        'nom',
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
        'adresse_ip',
        'statut',
        'departement',
        'poste_staff',
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
/* public function showTransition(Equipment $equipment)
{
    return view('equipment.transition', compact('equipment'));
} */

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

public function reaffectations() {
    return $this->hasMany(Reaffectation::class);
}

    /*
    |--------------------------------------------------------------------------
    | Cycle de vie & renouvellement (âge depuis mise en service / livraison)
    |--------------------------------------------------------------------------
    */

    /**
     * Date utilisée pour le calcul d’âge : mise en service prioritaire, sinon livraison.
     */
    public function lifecycleReferenceDate(): ?Carbon
    {
        if ($this->date_mise_service) {
            return $this->date_mise_service->copy()->startOfDay();
        }
        if ($this->date_livraison) {
            return $this->date_livraison->copy()->startOfDay();
        }

        return null;
    }

    /**
     * Âge approximatif en années (base 365,25 jours).
     */
    public function ageEquipementAnnees(): ?float
    {
        $ref = $this->lifecycleReferenceDate();
        if ($ref === null) {
            return null;
        }

        return round($ref->diffInDays(now()->startOfDay()) / 365.25, 2);
    }

    public function getAgeEquipementAnneesAttribute(): ?float
    {
        return $this->ageEquipementAnnees();
    }

    /**
     * recent | seuil_reference | a_remplacer | inconnu
     */
    public function niveauRenouvellement(): string
    {
        $age = $this->ageEquipementAnnees();
        if ($age === null) {
            return 'inconnu';
        }

        $orange = (float) config('equipment_renewal.orange_years', 2);
        $red = (float) config('equipment_renewal.red_years', 3);

        if ($age >= $red) {
            return 'a_remplacer';
        }
        if ($age >= $orange) {
            return 'seuil_reference';
        }

        return 'recent';
    }

    public function getNiveauRenouvellementAttribute(): string
    {
        return $this->niveauRenouvellement();
    }

    public function libelleRenouvellementCourt(): string
    {
        return match ($this->niveauRenouvellement()) {
            'recent' => 'Récent',
            'seuil_reference' => 'Seuil de référence',
            'a_remplacer' => 'À remplacer',
            default => 'Non renseigné',
        };
    }

    public function libelleRenouvellementLong(): string
    {
        return match ($this->niveauRenouvellement()) {
            'recent' => 'Récent — cycle de vie normal',
            'seuil_reference' => 'Seuil de référence — planifier le renouvellement',
            'a_remplacer' => 'Seuil critique — remplacement prioritaire',
            default => 'Date de mise en service ou de livraison manquante',
        };
    }

    /**
     * Tri par ancienneté croissante (plus ancien en premier = plus prioritaire).
     */
    public function scopeOrderByRenewalPriority($query)
    {
        return $query->orderByRaw('COALESCE(date_mise_service, date_livraison) ASC');
    }

    /**
     * Filtre SQL MySQL par niveau (nécessite au moins une date renseignée pour les niveaux calculés).
     */
    public function scopeWhereRenewalNiveau($query, string $niveau)
    {
        $orangeDays = (int) max(1, round((float) config('equipment_renewal.orange_years', 2) * 365.25));
        $redDays = (int) max(1, round((float) config('equipment_renewal.red_years', 3) * 365.25));

        $d = 'COALESCE(date_mise_service, date_livraison)';

        return match ($niveau) {
            'recent' => $query->whereRaw("$d IS NOT NULL")->whereRaw("$d > DATE_SUB(CURDATE(), INTERVAL ? DAY)", [$orangeDays]),
            'seuil_reference' => $query->whereRaw("$d IS NOT NULL")
                ->whereRaw("$d <= DATE_SUB(CURDATE(), INTERVAL ? DAY)", [$orangeDays])
                ->whereRaw("$d > DATE_SUB(CURDATE(), INTERVAL ? DAY)", [$redDays]),
            'a_remplacer' => $query->whereRaw("$d IS NOT NULL")->whereRaw("$d <= DATE_SUB(CURDATE(), INTERVAL ? DAY)", [$redDays]),
            'inconnu' => $query->whereNull('date_mise_service')->whereNull('date_livraison'),
            default => $query,
        };
    }


}