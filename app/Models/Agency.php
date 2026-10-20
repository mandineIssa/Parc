<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Support\FilialeScope;
use App\Traits\Auditable;

class Agency extends Model
{
    //use Auditable;
    protected $fillable = ['code', 'nom', 'ville', 'adresse', 'telephone', 'email', 'filiale_id'];

    protected static function booted(): void
    {
        static::addGlobalScope('filiale', function (Builder $builder) {
            FilialeScope::applyToAgency($builder);
        });
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class, 'agency_id');
    }

    public function filiale()
    {
        return $this->belongsTo(Filiale::class, 'filiale_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'agency_id');
    }
}
