<?php
// app/Models/FicheMouvement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class FicheMouvement extends Model
{
    use Auditable;
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'approval_id',
        'equipment_id',
        'user_id',
        'date_application',
        'numero_fiche',
        'expediteur_nom',
        'expediteur_prenom',
        'expediteur_fonction',
        'receptionnaire_nom',
        'receptionnaire_prenom',
        'receptionnaire_fonction',
        'type_materiel',
        'reference',
        'lieu_depart',
        'destination',
        'motif',
        'date_expediteur',
        'date_receptionnaire',
        'signature_expediteur_path',
        'signature_receptionnaire_path',
        'status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'date_application' => 'date',
        'date_expediteur' => 'date',
        'date_receptionnaire' => 'date',
        'metadata' => 'array',
    ];

    /**
     * Génère un numéro de fiche automatique
     */
    public static function generateNumeroFiche()
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        
        return sprintf('FM-%s-%04d', $date, $count);
    }

    /**
     * Relations
     */
    public function approval()
    {
        return $this->belongsTo(Approval::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope pour les fiches en cours
     */
    public function scopeEnCours($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Accesseurs
     */
    public function getFullExpediteurAttribute()
    {
        return $this->expediteur_nom . ' ' . $this->expediteur_prenom;
    }

    public function getFullReceptionnaireAttribute()
    {
        return $this->receptionnaire_nom . ' ' . $this->receptionnaire_prenom;
    }

    public function getLieuDestinationAttribute()
    {
        return $this->lieu_depart . ' → ' . $this->destination;
    }
}