<?php
// app/Models/IncidentHistorique.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentHistorique extends Model
{
    protected $table = 'incident_historiques';

    protected $fillable = [
        'incident_fiche_id', 'user_id', 'action', 'commentaire', 'niveau'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(IncidentFiche::class, 'incident_fiche_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'soumis' => '📋 Incident soumis',
            'modifie' => '✏️ Fiche modifiée',
            'transfere_n2' => '⬆️ Transféré au N+2',
            'transfere_n3' => '⬆️ Transféré au N+3',
            'cloture' => '✅ Clôturé',
            'pdf_uploade' => '📎 PDF uploadé',
            'pdf_final_genere' => '📄 PDF final généré',
            default => ucfirst($this->action),
        };
    }
}