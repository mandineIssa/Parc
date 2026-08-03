<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaffectation extends Model
{
    protected $fillable = [
        'equipment_id',
        'ancien_utilisateur_nom',
        'ancien_utilisateur_prenom',
        'ancien_departement',
        'ancienne_localisation',
        'nouveau_utilisateur_nom',
        'nouveau_utilisateur_prenom',
        'nouveau_departement',
        'nouvelle_localisation',
        'date_reaffectation',
        'motif',
        'fait_par',
        'annulee_at',
        'annulee_par',
    ];

    protected $casts = [
        'date_reaffectation' => 'date',
        'annulee_at' => 'datetime',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fait_par');
    }

    public function annulateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'annulee_par');
    }

    public function isAnnulee(): bool
    {
        return $this->annulee_at !== null;
    }

    public function scopeActives($query)
    {
        return $query->whereNull('annulee_at');
    }

    // Accesseurs utiles
    public function getAncienNomCompletAttribute(): string
    {
        $nom = trim(($this->ancien_utilisateur_nom ?? '') . ' ' . ($this->ancien_utilisateur_prenom ?? ''));
        return $nom ?: 'Non affecté';
    }

    public function getNouveauNomCompletAttribute(): string
    {
        return trim($this->nouveau_utilisateur_nom . ' ' . ($this->nouveau_utilisateur_prenom ?? ''));
    }
}