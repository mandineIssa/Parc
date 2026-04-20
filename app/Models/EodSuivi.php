<?php
// app/Models/EodSuivi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EodSuivi extends Model
{
    protected $table = 'eod_suivi';
    
    protected $fillable = [
        'reference', 'status', 'date_traitement', 'institution', 'systeme',
        'heure_lancement', 'heure_fin', 'statut_global', 'responsable_suivi',
        'sauvegarde_avant_incremental', 'sauvegarde_avant_differentiel', 'sauvegarde_avant_complet',
        'sauvegarde_avant_heure', 'sauvegarde_avant_observation',
        'sauvegarde_apres_incremental', 'sauvegarde_apres_differentiel', 'sauvegarde_apres_complet',
        'sauvegarde_apres_heure', 'sauvegarde_apres_observation',
        'nafa_bd_avant_incremental', 'nafa_bd_avant_differentiel', 'nafa_bd_avant_complet',
        'nafa_bd_apres_incremental', 'nafa_bd_apres_differentiel', 'nafa_bd_apres_complet',
        'nafa_bd_heure', 'nafa_bd_observation',
        'batch_data', 'emargement', 'responsable_batch', 'incidents_data',
        'validated_at', 'validation_note', 'validation_head_it_date', 'validation_head_it_visa',
        'validation_audit_date', 'validation_audit_visa', 'history',
        'created_by', 'updated_by', 'validated_by'
    ];

    protected $casts = [
        'date_traitement' => 'date',
        'batch_data' => 'array',
        'incidents_data' => 'array',
        'history' => 'array',
        'validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->reference) {
                $model->reference = 'EOD-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'DRAFT' => 'Brouillon',
            'PENDING_N2' => 'En attente validation N+2',
            'VALIDATED' => 'Validé',
            'REJECTED' => 'Rejeté',
            default => $this->status
        };
    }

    public function getStatusClassAttribute()
    {
        return match($this->status) {
            'DRAFT' => 'bg-gray-100 text-gray-800',
            'PENDING_N2' => 'bg-yellow-100 text-yellow-800',
            'VALIDATED' => 'bg-green-100 text-green-800',
            'REJECTED' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}