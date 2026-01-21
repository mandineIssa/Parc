<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
/* use Illuminate\Database\Eloquent\SoftDeletes; */

trait Auditable
{
    /* use SoftDeletes; */
    protected static function bootAuditable()
    {
        static::created(function (Model $model) {
            self::audit('create', $model);
        });
        
        static::updated(function (Model $model) {
            self::audit('update', $model);
        });
        
        static::deleted(function (Model $model) {
            self::audit('delete', $model);
        });
    }
    
    protected static function audit($action, Model $model)
    {
        // Ne pas auditer les audits eux-mêmes
        if ($model instanceof \App\Models\Audit) {
            return;
        }
        
        $oldData = [];
        $newData = [];
        $changes = [];
        
        if ($action === 'update') {
            $oldData = $model->getOriginal();
            $newData = $model->getAttributes();
            
            // Calculer les changements
            foreach ($model->getDirty() as $attribute => $value) {
                $changes[$attribute] = [
                    'old' => $model->getOriginal($attribute),
                    'new' => $value
                ];
            }
        } elseif ($action === 'create') {
            $newData = $model->getAttributes();
        } elseif ($action === 'delete') {
            $oldData = $model->getAttributes();
        }
        
        // Nettoyer les données sensibles
        $oldData = self::cleanData($oldData);
        $newData = self::cleanData($newData);
        
        Audit::create([
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'old_data' => $oldData,
            'new_data' => $newData,
            'changes' => $changes,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'user_id' => Auth::id(),
            'notes' => self::getAuditNotes($action, $model)
        ]);
    }
    
    protected static function cleanData(array $data): array
    {
        // Enlever les champs sensibles (mots de passe, tokens, etc.)
        $sensitiveFields = [
            'password', 'password_confirmation', 'remember_token',
            'api_token', 'access_token', 'refresh_token',
            'secret', 'key', 'token'
        ];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***MASKED***';
            }
        }
        
        return $data;
    }
    
    protected static function getAuditNotes($action, Model $model): ?string
    {
        $notes = [];
        
        if ($action === 'create') {
            $notes[] = "Création d'un nouvel enregistrement";
        } elseif ($action === 'update') {
            $notes[] = "Mise à jour de l'enregistrement";
        } elseif ($action === 'delete') {
            $notes[] = "Suppression de l'enregistrement";
        }
        
        // Notes spécifiques selon le modèle
        if ($model instanceof \App\Models\Equipment) {
            $notes[] = "Équipement: {$model->nom} (N°: {$model->numero_serie})";
        } elseif ($model instanceof \App\Models\Parc) {
            $notes[] = "Affectation au parc";
        } elseif ($model instanceof \App\Models\Maintenance) {
            $notes[] = "Maintenance";
        }
        
        return implode(' - ', $notes);
    }
    
    // Méthode pour auditer manuellement (transitions, etc.)
    public static function auditManual($action, Model $model, array $extraData = [])
    {
        $auditData = [
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'user_id' => Auth::id(),
            'notes' => $extraData['notes'] ?? null,
            'transition_type' => $extraData['transition_type'] ?? null
        ];
        
        if (isset($extraData['old_data'])) {
            $auditData['old_data'] = self::cleanData($extraData['old_data']);
        }
        
        if (isset($extraData['new_data'])) {
            $auditData['new_data'] = self::cleanData($extraData['new_data']);
        }
        
        if (isset($extraData['changes'])) {
            $auditData['changes'] = $extraData['changes'];
        }
        
        Audit::create($auditData);
    }
}