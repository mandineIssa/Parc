<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Traits\Auditable;

class Agency extends Model
{
    //use Auditable;
    protected $fillable = ['code', 'nom', 'ville', 'adresse', 'telephone', 'email', 'filiale_id'];

    protected static function booted(): void
    {
        static::addGlobalScope('filiale', function (Builder $builder) {
            $user = Auth::user();
            if (! $user instanceof User) {
                return;
            }

            $filialeId = $user->scopedFilialeId();
            if ($filialeId === null) {
                return;
            }

            if ($filialeId === 0) {
                $builder->whereRaw('0 = 1');

                return;
            }

            $builder->where('filiale_id', $filialeId);
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
