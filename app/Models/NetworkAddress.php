<?php
// app/Models/NetworkAddress.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NetworkAddress extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site', 'type', 'vlan', 'adresse_reseau', 'masque', 'adresse_exclue',
        'adresse_dhcp', 'default_gateway', 'numero', 'equipement_reseau',
        'type_equipement', 'adresse_ip', 'type_port', 'port_reseau', 'vlan_port',
        'emplacement', 'equipement_connecte', 'type_cable', 'adresse_ip_connecte',
        'commentaires', 'created_by',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public static function sites(): array
    {
        return ['AGP', 'TOUBA', 'TAMBA', 'ZIG', 'PIKINE', 'DAKAR', 'CONGO', 'AUTRE'];
    }

    public static function typesEquipement(): array
    {
        return ['Switch', 'Routeur', 'Firewall', 'AP WiFi', 'Serveur', 'Imprimante', 'Autre'];
    }

    public static function typesCable(): array
    {
        return ['RJ45 Cat5e', 'RJ45 Cat6', 'Fibre optique', 'Console', 'Autre'];
    }
}
