<?php

namespace App\Http\Controllers;

use App\Models\TransitionApproval;
use App\Models\Equipment;
use App\Models\User;
use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function superAdmin()
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur est super admin
        if ($user->role !== 'super_admin' && $user->role !== 'admin') {
            abort(403, 'Accès non autorisé');
        }
        
        // Récupérer les approbations en attente
        $pendingApprovals = TransitionApproval::with(['equipment', 'submitter'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Statistiques
        $equipmentCount = Equipment::count();
        $userCount = User::count();
        
        return view('dashboard.super-admin', compact(
            'pendingApprovals', 
            'equipmentCount', 
            'userCount'
        ));
    }
    
    public function index()
    {
        $user = Auth::user();

        // Si super admin → dashboard super admin
        if ($user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        // Calculer TOUTES les statistiques nécessaires pour la vue
        // NOTE: Utilisez 'statut' (avec u) pas 'status'
        $totalEquipments = Equipment::count();
        $stockEquipments = Equipment::where('statut', 'stock')->count();
        $parcEquipments = Equipment::where('statut', 'parc')->count();
        $maintenanceEquipments = Equipment::where('statut', 'maintenance')->count();
        $perduEquipments = Equipment::where('statut', 'perdu')->count();
        
        // Vérifier si la colonne localisation existe
        if (Schema::hasColumn('equipment', 'localisation')) {
            $celerStock = Equipment::where('localisation', 'CELER')->count();
            $decelerStock = Equipment::where('localisation', 'DECELER')->count();
        } else {
            $celerStock = 0;
            $decelerStock = 0;
        }
        
        // Nombre d'audits - vérifier si la table existe
        if (Schema::hasTable('audits')) {
            $auditCount = Audit::count();
        } else {
            $auditCount = 0;
        }
        
        return view('dashboard.index', compact(
            'totalEquipments',
            'stockEquipments', 
            'parcEquipments',
            'maintenanceEquipments',
            'perduEquipments',
            'celerStock',
            'decelerStock',
            'auditCount'
        ));
    }
}