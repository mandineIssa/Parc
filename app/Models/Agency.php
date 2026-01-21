<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Agency extends Model
{
    //use Auditable;
    protected $fillable = ['code', 'nom', 'ville', 'adresse', 'telephone', 'email'];
    // Dans app/Models/Agency.php, ajoutez :
    public function equipments()
       {
            return $this->hasMany(Equipment::class, 'agency_id');
        }
}
