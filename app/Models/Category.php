<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Equipment;


class Category extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'type', 
        'nom', 
        'description',
        'parent_id',  // Pour les sous-catégories
        'equipment_list'  // Liste des équipements typiques
    ];
    
    protected $casts = [
        'equipment_list' => 'array'
    ];
    /**
     * Get the equipment for this category.
     */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'categorie_id');
    }
    
    /**
     * Get parent category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    /**
     * Get subcategories.
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    
    /**
     * Scope for main categories (no parent).
     */
    public function scopeMainCategories($query)
    {
        return $query->whereNull('parent_id');
    }
    
    /**
     * Scope for subcategories.
     */
    public function scopeSubcategories($query)
    {
        return $query->whereNotNull('parent_id');
    }
    
    /**
     * Scope for filtering by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute()
    {
        $labels = [
            'réseaux' => 'Réseaux',
            'électronique' => 'Électronique',
            'informatiques' => 'Informatiques',
        ];
        
        return $labels[$this->type] ?? $this->type;
    }
    
    /**
     * Check if category has subcategories.
     */
    public function getHasSubcategoriesAttribute()
    {
        return $this->subcategories()->count() > 0;
    }
    
}