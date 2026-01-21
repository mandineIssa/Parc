<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Equipment;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    // Liste des audits
    public function index(Request $request)
    {
        $query = Audit::with('user')->latest();
        
        // Filtres
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }
        
        if ($request->filled('period')) {
            $query->where('created_at', '>=', now()->subDays($request->period));
        } else {
            $query->where('created_at', '>=', now()->subDays(30)); // Par défaut 30 jours
        }
        
        $audits = $query->paginate(50);
        
        // Statistiques
        $stats = AuditService::getAuditStats($request->period ?? 30);
        
        return view('audits.index', compact('audits', 'stats'));
    }
    
    // Historique d'un équipement
    public function equipmentHistory($equipmentId)
    {
        $equipment = Equipment::findOrFail($equipmentId);
        $audits = AuditService::getEquipmentHistory($equipmentId);
        
        return view('equipment.audit', compact('equipment', 'audits'));
    }
    
    // Export des audits (CSV)
    public function export(Request $request)
    {
        $query = Audit::with('user');
        
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        
        $audits = $query->get();
        
        $filename = 'audits_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($audits) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, [
                'Date', 'Heure', 'Action', 'Modèle', 'ID Modèle', 
                'Utilisateur', 'IP', 'Notes', 'Transition'
            ]);
            
            // Données
            foreach ($audits as $audit) {
                fputcsv($file, [
                    $audit->created_at->format('Y-m-d'),
                    $audit->created_at->format('H:i:s'),
                    $audit->action,
                    class_basename($audit->model_type),
                    $audit->model_id,
                    $audit->user->name ?? 'Système',
                    $audit->ip_address,
                    $audit->notes,
                    $audit->transition_type
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    // Purger les vieux audits (commande artisan)
    public static function purgeOldAudits($days = 365)
    {
        $count = Audit::where('created_at', '<', now()->subDays($days))->delete();
        
        return $count;
    }
}