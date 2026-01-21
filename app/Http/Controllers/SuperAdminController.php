<?php
// app/Http/Controllers/SuperAdminController.php

namespace App\Http\Controllers;

use App\Models\TransitionApproval;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    /**
     * Afficher le tableau de bord super admin
     */
    public function index()
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur est super admin
        if (!$this->isSuperAdmin($user)) {
            abort(403, 'Accès non autorisé');
        }
        
        // Récupérer les approbations en attente
        $pendingApprovals = TransitionApproval::with(['equipment', 'submitter'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Récupérer les statistiques
        $equipmentCount = Equipment::count();
        $userCount = User::count();
        
        // Récupérer les dernières transitions approuvées
        $recentApprovals = TransitionApproval::where('status', 'approved')
            ->with(['equipment', 'submitter', 'approver'])
            ->orderBy('approved_at', 'desc')
            ->limit(5)
            ->get();
        
        // Statistiques par statut d'équipement
        $statuses = Equipment::select('statut', \DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->orderBy('total', 'desc')
            ->get();
        
        return view('admin.dashboard', compact(
            'pendingApprovals',
            'equipmentCount',
            'userCount',
            'recentApprovals',
            'statuses'
        ));
    }
    
    /**
     * Afficher les statistiques détaillées
     */
    public function stats()
    {
        $user = Auth::user();
        
        if (!$this->isSuperAdmin($user)) {
            abort(403, 'Accès non autorisé');
        }
        
        // Vos statistiques détaillées ici
        // ...
        
        return view('admin.stats');
    }
    
    /**
     * Vérifier si l'utilisateur est super admin
     */
    private function isSuperAdmin($user)
    {
        $role = strtolower(trim((string) ($user->role ?? '')));
        
        return in_array($role, ['super_admin', 'admin'])
            || $user->email === 'superadmin@cofina.sn';
    }
    // Dans votre contrôleur AdminController
public function destroyApproval(TransitionApproval $approval)
{
    try {
        // Supprimer la demande d'approbation
        $approval->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Demande supprimée avec succès'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
        ], 500);
    }
}
}