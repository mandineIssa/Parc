<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosteAudit extends Model
{
    protected $table = 'poste_audits';

    protected $fillable = [
        'poste_id',
        'hostname',
        'numero_serie',
        'utilisateur_session',
        'fabricant',
        'modele',
        'os',
        'version_os',
        'antivirus_defender',
        'firewall',
        'bitlocker',
        'usb_stockage_bloque',
        'adresse_mac',
        'adresse_ip',
        'date_audit',
    ];

    protected $casts = [
        'antivirus_defender' => 'boolean',
        'usb_stockage_bloque' => 'boolean',
        'date_audit' => 'datetime',
    ];

    public function poste(): BelongsTo
    {
        return $this->belongsTo(Poste::class);
    }
}
