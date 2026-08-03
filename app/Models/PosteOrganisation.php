<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PosteOrganisation extends Model
{
    protected $table = 'postes_organisation';

    protected $fillable = ['nom', 'actif', 'ordre'];

    protected $casts = [
        'actif' => 'boolean',
        'ordre' => 'integer',
    ];

    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('actif', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('ordre')->orderBy('nom');
    }

    /** @return list<string> */
    public static function options(): array
    {
        return static::query()->actifs()->ordered()->pluck('nom')->all();
    }
}
