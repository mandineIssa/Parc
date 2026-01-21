<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Audit extends Model
{
    
    protected $table = 'audits';
    
    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'old_data',
        'new_data',
        'changes',
        'ip_address',
        'user_agent',
        'user_id',
        'transition_type',
        'notes'
    ];
    
    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Relation polymorphique avec le modèle audité
    public function model()
    {
        return $this->morphTo();
    }
    
    // Helper pour formater les données
    public function getFormattedChangesAttribute()
    {
        if (empty($this->changes)) {
            return [];
        }
        
        $formatted = [];
        foreach ($this->changes as $field => $change) {
            $formatted[] = [
                'field' => $this->getFieldLabel($field),
                'old' => $this->formatValue($change['old']),
                'new' => $this->formatValue($change['new'])
            ];
        }
        
        return $formatted;
    }
    
    private function getFieldLabel($field)
    {
        $labels = [
            'numero_serie' => 'N° Série',
            'nom' => 'Nom',
            'type' => 'Type',
            'statut' => 'Statut',
            'localisation' => 'Localisation',
            'prix' => 'Prix',
            'etat' => 'État',
            'departement' => 'Département',
            'poste_staff' => 'Poste',
            'date_mise_service' => 'Date mise en service',
            'agence_id' => 'Agence',
            'categorie_id' => 'Catégorie',
            'fournisseur_id' => 'Fournisseur',
            'utilisateur_id' => 'Utilisateur',
            'type_stock' => 'Type de stock',
            'type_maintenance' => 'Type maintenance',
            'prestataire' => 'Prestataire',
            'raison' => 'Raison',
            'destinataire' => 'Destinataire',
            'type_disparition' => 'Type disparition',
        ];
        
        return $labels[$field] ?? $field;
    }
    
    private function formatValue($value)
    {
        if (is_null($value)) {
            return '<vide>';
        }
        
        if (is_bool($value)) {
            return $value ? 'Oui' : 'Non';
        }
        
        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT);
        }
        
        return $value;
    }
    
    // Scope pour filtrer par action
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }
    
    public function scopeByModel($query, $modelType, $modelId = null)
    {
        $query = $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query = $query->where('model_id', $modelId);
        }
        
        return $query;
    }
    
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}