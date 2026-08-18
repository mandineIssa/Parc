<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filiale extends Model
{
    protected $fillable = ['nom', 'actif'];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function agencies(): HasMany
    {
        return $this->hasMany(Agency::class, 'filiale_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'filiale_id');
    }

    public static function defaultSenegal(): self
    {
        return static::query()->firstOrCreate(
            ['nom' => 'Sénégal'],
            ['actif' => true]
        );
    }
}
