<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ControlTemplate extends Model
{
    protected $fillable = [
        'name', 'review_type', 'frequency', 'description',
        'checklist', 'questions', 'required_attachments', 'is_active'
    ];

    protected $casts = [
        'checklist' => 'array',
        'questions' => 'array',
        'required_attachments' => 'array',
        'is_active' => 'boolean'
    ];

    public function controls(): HasMany
    {
        return $this->hasMany(Control::class);
    }

    public static function getReviewTypes(): array
    {
        return [
            'controle_premier_niveau' => 'Contrôle de premier niveau',
            'maintenance_preventive' => 'Maintenance préventive',
            'inventaire_parc' => 'Inventaire du parc informatique',
            'revue_pca' => 'Revue PCA',
            'schema_directeur' => 'Revue du Schéma Directeur IT',
            'politique_securite' => 'Revue de la Politique de Sécurité'
        ];
    }

    public static function getFrequencies(): array
    {
        return [
            'quotidienne' => 'Quotidienne',
            'hebdomadaire' => 'Hebdomadaire',
            'mensuelle' => 'Mensuelle',
            'trimestrielle' => 'Trimestrielle',
            'semestrielle' => 'Semestrielle',
            'annuelle' => 'Annuelle'
        ];
    }
}