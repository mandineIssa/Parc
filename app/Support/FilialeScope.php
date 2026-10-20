<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

final class FilialeScope
{
    public static function applyToEquipment(Builder $builder): void
    {
        try {
            $filialeId = self::restrictedFilialeId();
            if ($filialeId === null) {
                return;
            }

            if ($filialeId === 0 || ! self::agenciesHaveFilialeColumn()) {
                if ($filialeId === 0) {
                    $builder->whereRaw('0 = 1');
                }

                return;
            }

            $table = $builder->getModel()->getTable();
            $builder->whereIn($table.'.agency_id', function ($query) use ($filialeId) {
                $query->select('id')
                    ->from('agencies')
                    ->where('agencies.filiale_id', $filialeId);
            });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function applyToAgency(Builder $builder): void
    {
        try {
            $filialeId = self::restrictedFilialeId();
            if ($filialeId === null) {
                return;
            }

            if ($filialeId === 0) {
                $builder->whereRaw('0 = 1');

                return;
            }

            if (! self::agenciesHaveFilialeColumn()) {
                return;
            }

            $table = $builder->getModel()->getTable();
            $builder->where($table.'.filiale_id', $filialeId);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private static function restrictedFilialeId(): ?int
    {
        $user = Auth::user();
        if (! $user instanceof User || ! method_exists($user, 'scopedFilialeId')) {
            return null;
        }

        return $user->scopedFilialeId();
    }

    private static function agenciesHaveFilialeColumn(): bool
    {
        try {
            return Schema::hasTable('agencies') && Schema::hasColumn('agencies', 'filiale_id');
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }
}
