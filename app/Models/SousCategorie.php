<?php

// ============================================
// 2. MODEL - app/Models/SousCategorie.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class SousCategorie extends Model
{
    protected $table = 'sous_categories';
    
    protected $fillable = [
        'categorie_id',
        'nom',
        'description',
    ];

    public function categorie()
    {
        return $this->belongsTo(Category::class);
    }
}
