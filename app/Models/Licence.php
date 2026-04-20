<?php

// app/Models/Licence.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Licence extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type', 'nom', 'site_agence', 'modele', 'numero_serie', 'type_licence',
        'prix_achat', 'fournisseur', 'numero_client', 'type_ligne', 'ip_publique',
        'debit', 'montant_mensuel', 'environnement', 'emplacement', 'port',
        'duree_jours', 'utilisateur', 'departement', 'email', 'espace_onedrive',
        'teams', 'quota_total', 'statut', 'date_activation', 'date_expiration',
        'date_mise_en_service', 'echeance_contrat', 'renouvellement_prevu',
        'contact_nom', 'contact_email', 'contact_tel', 'observation', 'created_by',
    ];

    protected $casts = [
        'date_activation'     => 'date',
        'date_expiration'     => 'date',
        'date_mise_en_service'=> 'date',
        'echeance_contrat'    => 'date',
        'teams'               => 'boolean',
        'renouvellement_prevu'=> 'boolean',
        'prix_achat'          => 'decimal:2',
        'montant_mensuel'     => 'decimal:2',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeByType($query, $type)   { return $query->where('type', $type); }
    public function scopeExpiresSoon($query, $days = 30)
    {
        return $query->whereNotNull('date_expiration')
                     ->whereBetween('date_expiration', [now(), now()->addDays($days)]);
    }

    public function getJoursRestantsAttribute(): ?int
    {
        if (!$this->date_expiration) return null;
        return max(0, now()->diffInDays($this->date_expiration, false));
    }

    public static function types(): array
    {
        return ['Fortinet', 'FAI', 'Certificat', 'Office365'];
    }

    public static function statuts(): array
    {
        return ['Actif', 'Bientôt expirée', 'Expirée', 'Résiliée', 'UP', 'DOWN'];
    }
}
