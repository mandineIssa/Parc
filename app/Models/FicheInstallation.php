<?php
// app/Models/FicheInstallation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class FicheInstallation extends Model
{
    use HasFactory, SoftDeletes;
    use Auditable;
    protected $fillable = [
        'approval_id',
        'equipment_id',
        'user_id',
        'date_application',
        'numero_fiche',
        'agence_nom',
        'date_installation',
        'prerequis',
        'logiciels_installes',
        'raccourcis',
        'autres_configurations',
        'installateur_nom',
        'installateur_prenom',
        'installateur_fonction',
        'date_verification',
        'verifications',
        'autres_verifications',
        'verificateur_nom',
        'verificateur_prenom',
        'verificateur_fonction',
        'signature_installateur_path',
        'signature_utilisateur_path',
        'signature_verificateur_path',
        'status',
        'observations',
        'checklist_complete',
        'metadata',
    ];

    protected $casts = [
        'date_application' => 'date',
        'date_installation' => 'date',
        'date_verification' => 'date',
        'prerequis' => 'array',
        'logiciels_installes' => 'array',
        'raccourcis' => 'array',
        'autres_configurations' => 'array',
        'verifications' => 'array',
        'autres_verifications' => 'array',
        'checklist_complete' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Génère un numéro de fiche automatique
     */
    public static function generateNumeroFiche()
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        
        return sprintf('FI-%s-%04d', $date, $count);
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
     * Scopes
     */
    public function scopeEnCours($query)
    {
        return $query->whereIn('status', ['draft', 'en_cours', 'installe']);
    }

    public function scopeComplet($query)
    {
        return $query->where('status', 'complet');
    }

    public function scopeAvecVerification($query)
    {
        return $query->whereNotNull('verificateur_nom');
    }

    /**
     * Accesseurs
     */
    public function getFullInstallateurAttribute()
    {
        return $this->installateur_nom . ' ' . $this->installateur_prenom;
    }

    public function getFullVerificateurAttribute()
    {
        if ($this->verificateur_nom) {
            return $this->verificateur_nom . ' ' . $this->verificateur_prenom;
        }
        return null;
    }

    public function getProgressAttribute()
    {
        $statusProgress = [
            'draft' => 25,
            'en_cours' => 50,
            'installe' => 75,
            'verifie' => 90,
            'complet' => 100,
        ];

        return $statusProgress[$this->status] ?? 0;
    }

    /**
     * Vérifie si une étape est complète
     */
    public function isPrerequisComplete()
    {
        return !empty($this->prerequis) && is_array($this->prerequis);
    }

    public function isLogicielsComplete()
    {
        return !empty($this->logiciels_installes) && is_array($this->logiciels_installes);
    }

    /**
     * Calcule le pourcentage de complétion de la checklist
     */
    public function getChecklistCompletionAttribute()
    {
        if (!$this->checklist_complete) {
            return 0;
        }

        $total = 0;
        $completed = 0;

        // Compter tous les éléments de checklist
        $allChecklists = [
            'prerequis' => $this->prerequis ?? [],
            'logiciels_installes' => $this->logiciels_installes ?? [],
            'raccourcis' => $this->raccourcis ?? [],
            'autres_configurations' => $this->autres_configurations ?? [],
            'verifications' => $this->verifications ?? [],
            'autres_verifications' => $this->autres_verifications ?? [],
        ];

        foreach ($allChecklists as $checklist) {
            foreach ($checklist as $item) {
                $total++;
                if ($item === true || $item === 1 || $item === 'true') {
                    $completed++;
                }
            }
        }

        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }
}