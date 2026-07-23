<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poste extends Model
{
    protected $table = 'postes';

    protected $fillable = [
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

    public function audits(): HasMany
    {
        return $this->hasMany(PosteAudit::class)->orderByDesc('date_audit');
    }

    /**
     * Historique distinct des utilisateurs de session (ordre chronologique).
     *
     * @return list<array{utilisateur_session: string|null, premiere_apparition: mixed, derniere_apparition: mixed}>
     */
    public function historiqueUtilisateurs(): array
    {
        return $this->audits()
            ->reorder()
            ->orderBy('date_audit')
            ->get(['utilisateur_session', 'date_audit'])
            ->groupBy('utilisateur_session')
            ->map(fn ($rows, $user) => [
                'utilisateur_session' => $user === '' ? null : $user,
                'premiere_apparition' => $rows->min('date_audit'),
                'derniere_apparition' => $rows->max('date_audit'),
            ])
            ->values()
            ->all();
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (! empty($filters['fabricant'])) {
            $query->where('fabricant', 'like', '%'.$filters['fabricant'].'%');
        }

        if (! empty($filters['os'])) {
            $query->where('os', 'like', '%'.$filters['os'].'%');
        }

        if (! empty($filters['utilisateur']) || ! empty($filters['utilisateur_session'])) {
            $user = $filters['utilisateur'] ?? $filters['utilisateur_session'];
            $query->where('utilisateur_session', 'like', '%'.$user.'%');
        }

        if (! empty($filters['hostname'])) {
            $query->where('hostname', 'like', '%'.$filters['hostname'].'%');
        }

        if (! empty($filters['numero_serie'])) {
            $query->where('numero_serie', 'like', '%'.$filters['numero_serie'].'%');
        }

        if (array_key_exists('antivirus_defender', $filters) && $filters['antivirus_defender'] !== null && $filters['antivirus_defender'] !== '') {
            $query->where('antivirus_defender', filter_var($filters['antivirus_defender'], FILTER_VALIDATE_BOOLEAN));
        }

        if (array_key_exists('usb_stockage_bloque', $filters) && $filters['usb_stockage_bloque'] !== null && $filters['usb_stockage_bloque'] !== '') {
            $query->where('usb_stockage_bloque', filter_var($filters['usb_stockage_bloque'], FILTER_VALIDATE_BOOLEAN));
        }

        // BitLocker actif = partition C: déclarée « On »
        if (array_key_exists('bitlocker_actif', $filters) && $filters['bitlocker_actif'] !== null && $filters['bitlocker_actif'] !== '') {
            $actif = filter_var($filters['bitlocker_actif'], FILTER_VALIDATE_BOOLEAN);
            if ($actif) {
                $query->where('bitlocker', 'like', '%C::On%');
            } else {
                $query->where(function (Builder $q): void {
                    $q->whereNull('bitlocker')
                        ->orWhere('bitlocker', 'not like', '%C::On%');
                });
            }
        }

        if (! empty($filters['search'])) {
            $s = '%'.$filters['search'].'%';
            $query->where(function (Builder $q) use ($s): void {
                $q->where('hostname', 'like', $s)
                    ->orWhere('numero_serie', 'like', $s)
                    ->orWhere('utilisateur_session', 'like', $s)
                    ->orWhere('adresse_ip', 'like', $s)
                    ->orWhere('adresse_mac', 'like', $s);
            });
        }

        return $query;
    }

    public function isBitlockerActif(): bool
    {
        return is_string($this->bitlocker) && str_contains($this->bitlocker, 'C::On');
    }
}
