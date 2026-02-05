<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Parc extends Model
{
    use Auditable, HasFactory;
    
    protected $table = 'parc';
    
    protected $fillable = [
        'numero_serie',
        'utilisateur_id',
        'utilisateur_nom',          // ← AJOUTÉ
        'utilisateur_prenom',       // ← AJOUTÉ
        'departement',
        'poste_affecte',
        'position',                 // ← AJOUTÉ
        'date_affectation',
        'date_retour_prevue',
        'affectation_reason',       // ← AJOUTÉ
        'affectation_reason_detail', // ← AJOUTÉ
        'localisation',             // ← AJOUTÉ
        'telephone',                // ← AJOUTÉ
        'email',                    // ← AJOUTÉ
        'statut_usage',
        'notes_affectation',
        'affecte_par',              // ← AJOUTÉ
        'derniere_modification',    // ← AJOUTÉ
        'numero_bon_affectation',   // ← AJOUTÉ
        'transition_approval_id',
    ];
    
    protected $casts = [
        'date_affectation' => 'date',
        'date_retour_prevue' => 'date',
        'derniere_modification' => 'datetime', // ← AJOUTÉ
    ];
    
    // Ajout d'un accessor pour le nom complet
    public function getUtilisateurCompletAttribute()
    {
        return trim($this->utilisateur_prenom . ' ' . $this->utilisateur_nom);
    }
    
    // Ajout d'un accessor pour la durée d'affectation (en jours)
    public function getDureeAffectationAttribute()
    {
        if (!$this->date_affectation) {
            return 0;
        }
        
        $endDate = $this->date_retour_prevue ?? now();
        return $this->date_affectation->diffInDays($endDate);
    }
    
    // Ajout d'un mutator pour s'assurer que les noms sont propres
    public function setUtilisateurNomAttribute($value)
    {
        $this->attributes['utilisateur_nom'] = ucfirst(strtolower(trim($value)));
    }
    
    public function setUtilisateurPrenomAttribute($value)
    {
        $this->attributes['utilisateur_prenom'] = ucfirst(strtolower(trim($value)));
    }
    
    // Scopes utiles
    public function scopeActifs($query)
    {
        return $query->where('statut_usage', 'actif');
    }
    
    public function scopeParDepartement($query, $departement)
    {
        return $query->where('departement', $departement);
    }
    
    public function scopeARetournerBientot($query)
    {
        return $query->whereNotNull('date_retour_prevue')
                    ->where('date_retour_prevue', '<=', now()->addDays(7))
                    ->where('date_retour_prevue', '>=', now())
                    ->where('statut_usage', '!=', 'inactif');
    }
    
    // Relations existantes
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
    
    // Nouvelle relation pour la personne qui a fait l'affectation
    public function affectateur()
    {
        return $this->belongsTo(User::class, 'affecte_par');
    }
    
    // Boot du modèle pour générer automatiquement certaines valeurs
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($parc) {
            // Générer un numéro de bon d'affectation unique
            if (empty($parc->numero_bon_affectation)) {
                $parc->numero_bon_affectation = 'BA-' . strtoupper(uniqid());
            }
            
            // Définir qui a fait l'affectation (l'utilisateur connecté)
            if (auth()->check() && empty($parc->affecte_par)) {
                $parc->affecte_par = auth()->id();
            }
            
            // Mettre à jour la date de dernière modification
            $parc->derniere_modification = now();
            
            // Définir le statut par défaut si non spécifié
            if (empty($parc->statut_usage)) {
                $parc->statut_usage = 'actif';
            }
        });
        
        static::updating(function ($parc) {
            $parc->derniere_modification = now();
        });
    }
}