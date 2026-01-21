<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\Equipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    // Auditer une transition
    public static function logTransition($equipment, $fromStatus, $toStatus, $transitionType, $extraData = [])
    {
        Audit::create([
            'action' => 'transition',
            'model_type' => Equipment::class,
            'model_id' => $equipment->id,
            'old_data' => ['statut' => $fromStatus],
            'new_data' => ['statut' => $toStatus],
            'changes' => [
                'statut' => [
                    'old' => $fromStatus,
                    'new' => $toStatus
                ]
            ],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'user_id' => Auth::id(),
            'transition_type' => $transitionType,
            'notes' => "Transition: {$fromStatus} → {$toStatus}" . 
                      (isset($extraData['notes']) ? " - " . $extraData['notes'] : '')
        ]);
        
        // Audit également la table spécifique créée
        if ($toStatus === 'parc' && isset($extraData['parc_data'])) {
            self::logRelatedCreation('Parc', $extraData['parc_data'], "Création parc après transition");
        }
        
        if ($toStatus === 'maintenance' && isset($extraData['maintenance_data'])) {
            self::logRelatedCreation('Maintenance', $extraData['maintenance_data'], "Création maintenance après transition");
        }
    }
    
    // Auditer la création d'une entrée liée
    public static function logRelatedCreation($modelType, $modelData, $notes = null)
    {
        if (isset($modelData['id'])) {
            Audit::create([
                'action' => 'create',
                'model_type' => "App\Models\\{$modelType}",
                'model_id' => $modelData['id'],
                'new_data' => $modelData,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'user_id' => Auth::id(),
                'notes' => $notes
            ]);
        }
    }
    
    // Récupérer l'historique d'un équipement
    public static function getEquipmentHistory($equipmentId)
    {
        return Audit::where('model_type', Equipment::class)
            ->where('model_id', $equipmentId)
            ->orWhere(function ($query) use ($equipmentId) {
                $query->where('model_type', 'like', '%Equipment%')
                      ->whereJsonContains('new_data->equipment_id', $equipmentId);
            })
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->get();
    }
    
    // Statistiques d'audit
  public static function getAuditStats($days = 30)
{
    $query = Audit::recent($days);
    
    return [
        'total' => $query->count(),
        'by_action' => [
            'create' => (clone $query)->where('action', 'create')->count(),
            'update' => (clone $query)->where('action', 'update')->count(),
            'delete' => (clone $query)->where('action', 'delete')->count(),
            'transition' => (clone $query)->where('action', 'transition')->count(),
        ],
        'by_user' => Audit::recent($days)
            ->whereNotNull('user_id')
            ->selectRaw('user_id, count(*) as count')
            ->with('user')
            ->groupBy('user_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->user->name ?? 'Unknown' => $item->count];
            }),
        'by_model' => Audit::recent($days)
            ->selectRaw('model_type, count(*) as count')
            ->groupBy('model_type')
            ->pluck('count', 'model_type')
    ];
}
}