<?php
// app/Models/IncidentFiche.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentFiche extends Model
{
    use SoftDeletes;

    protected $table = 'incident_fiches';

    protected $fillable = [
        'reference', 'type', 'utilisateur', 'entite', 'fonction',
        'point_entree', 'date_incident', 'heure_incident', 'sujet',
        'bloquant', 'reproductible', 'description', 'statut',
        
        // Nouveaux champs ITIL
        'application_concernee', 'environnement', 'niveau_criticite',
        'heure_debut', 'heure_resolution', 'duree_incident',
        'service_impacte', 'nb_clients_impactes', 'nb_utilisateurs_impactes',
        'impact_metier', 'cause_racine', 'analyse_initiale',
        'chronologie', 'actions_correctives', 'actions_preventives',
        'sla_respecte', 'temps_resolution', 'commentaires_cloture',
        'valide_par', 'date_cloture',

        // Niveaux
        'n1_user_id', 'n1_description_traitement', 'n1_solutions_envisagees',
        'n1_statut', 'n1_autres_intervenants', 'n1_date_traitement', 'n1_pdf_path',

        'n2_user_id', 'n2_description_traitement', 'n2_solutions_envisagees',
        'n2_statut', 'n2_autres_intervenants', 'n2_date_traitement', 'n2_pdf_path',

        'n3_user_id', 'n3_description_traitement', 'n3_solutions_envisagees',
        'n3_statut', 'n3_autres_intervenants', 'n3_date_traitement', 'n3_pdf_path',

        'pdf_fiche_path', 'created_by',
    ];

    protected $casts = [
        'bloquant' => 'boolean',
        'reproductible' => 'boolean',
        'date_incident' => 'date',
        'n1_date_traitement' => 'datetime',
        'n2_date_traitement' => 'datetime',
        'n3_date_traitement' => 'datetime',
        'date_cloture' => 'datetime',
        'chronologie' => 'array',
        'actions_correctives' => 'array',
        'actions_preventives' => 'array',
        'sla_respecte' => 'boolean',
    ];

    // Relations
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function n1User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'n1_user_id');
    }

    public function n2User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'n2_user_id');
    }

    public function n3User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'n3_user_id');
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(IncidentHistorique::class, 'incident_fiche_id')->latest();
    }

    // Helpers
    public static function generateReference(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        $count = static::whereYear('created_at', $year)->whereMonth('created_at', $month)->count() + 1;
        return 'INC-' . $year . $month . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'brouillon' => 'Brouillon',
            'soumis' => 'Soumis (N+1)',
            'en_cours_n2' => 'En cours N+2',
            'en_cours_n3' => 'En cours N+3',
            'cloture' => 'Clôturé',
            'rejete' => 'Rejeté',
            default => ucfirst($this->statut),
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'logiciel' => 'Logiciel',
            'materiel' => 'Matériel',
            'reseau_telecom' => 'Réseaux & Télécom',
            'application' => 'Application',
            'infrastructure' => 'Infrastructure',
            default => $this->type,
        };
    }

    public function getCriticiteLabelAttribute(): string
    {
        return match ($this->niveau_criticite) {
            'P1' => '🔴 P1 - Critique',
            'P2' => '🟠 P2 - Élevé',
            'P3' => '🟡 P3 - Moyen',
            'P4' => '🟢 P4 - Faible',
            default => $this->niveau_criticite,
        };
    }

    public function getCriticiteColorAttribute(): string
    {
        return match ($this->niveau_criticite) {
            'P1' => 'red',
            'P2' => 'orange',
            'P3' => 'yellow',
            'P4' => 'green',
            default => 'gray',
        };
    }
}