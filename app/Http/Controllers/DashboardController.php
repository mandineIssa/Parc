<?php

namespace App\Http\Controllers;

use App\Models\TransitionApproval;
use App\Models\Equipment;
use App\Models\User;
use App\Models\Audit;
use App\Models\Parc;
use App\Models\Stock;
use App\Models\Maintenance;
use App\Models\HorsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Redirection intelligente vers le bon dashboard
     */
    public function redirect()
    {
        $user = Auth::user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        // Déterminer le dashboard approprié
        if (in_array($role, ['super_admin', 'admin', 'responsable_approbation']) || $user->email === 'superadmin@cofina.sn') {
            return redirect()->route('dashboards.index');
        } elseif (in_array($role, ['agent_it', 'technicien', 'user'])) {
            return redirect()->route('dashboards.agent');
        } else {
            return redirect()->route('dashboards.user');
        }
    }

    /**
     * Dashboard Super Admin/Admin
     */
    public function superAdmin(Request $request)
    {
        $user = Auth::user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        // Vérifier les autorisations
        if (!in_array($role, ['super_admin', 'admin', 'responsable_approbation']) && $user->email !== 'superadmin@cofina.sn') {
            abort(403, 'Accès non autorisé');
        }

        // Récupérer toutes les soumissions avec statistiques
        $stats = $this->getSuperAdminStats($user, $request);
        $recentSubmissions = $this->getRecentSubmissions($user, true, $request);
        $charts = $this->getSuperAdminCharts($user, $request);

        // Approbations urgentes (en attente > 2 jours)
        $urgentApprovals = TransitionApproval::with(['equipment', 'submitter'])
            ->where('status', 'pending')
            ->where('created_at', '<=', Carbon::now()->subDays(2))
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // Statistiques système
        $systemStats = $this->getSystemStats();

        return view('dashboards.index', compact(
            'stats',
            'recentSubmissions',
            'charts',
            'urgentApprovals',
            'systemStats'
        ));
    }

    /**
     * Dashboard Agent IT / Technicien
     */
    public function agent(Request $request)
    {
        $user = Auth::user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        // Vérifier les autorisations
        if (!in_array($role, ['agent_it', 'technicien', 'user'])) {
            abort(403, 'Accès non autorisé');
        }

        // Statistiques personnelles
        $personalStats = $this->getAgentStats($user, $request);

        // Mes soumissions récentes
        $mySubmissions = TransitionApproval::with(['equipment', 'submitter', 'approver'])
            ->where('submitted_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Équipements à gérer
        $equipmentsToManage = $this->getEquipmentsToManage($user);

        // Actions rapides nécessaires
        $quickActions = $this->getQuickActions($user);

        return view('dashboards.agent', compact(
            'personalStats',
            'mySubmissions',
            'equipmentsToManage',
            'quickActions'
        ));
    }

    /**
     * Dashboard Utilisateur standard
     */
    public function user(Request $request)
    {
        $user = Auth::user();

        // Équipements affectés à l'utilisateur
        $myEquipments = $this->getUserEquipments($user);

        // Historique des mouvements
        $movementHistory = $this->getMovementHistory($user);

        // Demandes en cours
        $myRequests = $this->getUserRequests($user);

        return view('dashboards.user', compact(
            'myEquipments',
            'movementHistory',
            'myRequests'
        ));
    }

    /**
     * Dashboard principal (unifié)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        // Déterminer ce que l'utilisateur peut voir
        $isSuperAdmin = in_array($role, ['super_admin', 'admin', 'responsable_approbation'])
            || $user->email === 'superadmin@cofina.sn';

        $isAgent = in_array($role, ['agent_it', 'technicien', 'user']);

        // Si Super Admin → dashboard super admin
        if ($isSuperAdmin) {
            return $this->superAdmin($request);
        }

        // Si Agent IT → dashboard agent
        if ($isAgent) {
            return $this->agent($request);
        }

        // Utilisateur standard → dashboard user
        return $this->user($request);
    }

    /**
     * Obtenir les statistiques Super Admin
     */
    private function getSuperAdminStats($user, $request)
    {
        // Base query pour toutes les soumissions
        $baseQuery = TransitionApproval::query();

        // Appliquer les filtres
        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                $baseQuery->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        // Filtrer par type si spécifié
        if ($request->has('type') && $request->type !== 'all') {
            $baseQuery->where('type', $request->type);
        }

        // Calculer les statistiques
        $total = $baseQuery->count();
        $pending = (clone $baseQuery)->where('status', 'pending')->count();
        $approved = (clone $baseQuery)->where('status', 'approved')->count();
        $rejected = (clone $baseQuery)->where('status', 'rejected')->count();

        // Statistiques par type de transition
        $typesStats = TransitionApproval::select('type', DB::raw('COUNT(*) as count'))
            ->when($request->has('date_range'), function ($q) use ($request) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) === 2) {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                }
            })
            ->when($request->has('type') && $request->type !== 'all', function ($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');

        // Statistiques d'approbation par utilisateur
        $approvalStats = User::select('users.id', 'users.name', DB::raw('COUNT(transition_approvals.id) as approved_count'))
            ->leftJoin('transition_approvals', function ($join) use ($request) {
                $join->on('users.id', '=', 'transition_approvals.approved_by')
                     ->where('transition_approvals.status', 'approved');
                
                if ($request->has('date_range')) {
                    $dates = explode(' - ', $request->date_range);
                    if (count($dates) === 2) {
                        $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                        $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                        $join->whereBetween('transition_approvals.approved_at', [$startDate, $endDate]);
                    }
                }
            })
            ->whereIn('users.role', ['super_admin', 'admin', 'responsable_approbation'])
            ->groupBy('users.id', 'users.name')
            ->orderBy('approved_count', 'desc')
            ->get();

        // Temps moyen d'approbation
        $avgApprovalTime = TransitionApproval::where('status', 'approved')
            ->whereNotNull('approved_at')
            ->whereNotNull('created_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours')
            ->value('avg_hours');

        return [
            'total_submissions' => $total,
            'pending_submissions' => $pending,
            'approved_submissions' => $approved,
            'rejected_submissions' => $rejected,
            'types_stats' => $typesStats,
            'approval_stats' => $approvalStats,
            'avg_approval_hours' => round($avgApprovalTime ?? 0, 1),
            'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0,
            'rejection_rate' => $total > 0 ? round(($rejected / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Obtenir les statistiques système
     */
    private function getSystemStats()
    {
        return [
            'total_equipments' => Equipment::count(),
            'stock_count' => Equipment::where('statut', 'stock')->count(),
            'parc_count' => Equipment::where('statut', 'parc')->count(),
            'maintenance_count' => Equipment::where('statut', 'maintenance')->count(),
            'hors_service_count' => Equipment::where('statut', 'hors_service')->count(),
            'perdu_count' => Equipment::where('statut', 'perdu')->count(),
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
        ];
    }

    /**
     * Obtenir les statistiques Agent IT
     */
    private function getAgentStats($user, $request)
    {
        // Mes soumissions
        $myTotal = TransitionApproval::where('submitted_by', $user->id)->count();
        $myPending = TransitionApproval::where('submitted_by', $user->id)->where('status', 'pending')->count();
        $myApproved = TransitionApproval::where('submitted_by', $user->id)->where('status', 'approved')->count();
        $myRejected = TransitionApproval::where('submitted_by', $user->id)->where('status', 'rejected')->count();

        // Équipements que je gère
        $equipmentsManaged = Equipment::whereHas('parc', function ($query) use ($user) {
            $query->where('utilisateur_id', $user->id);
        })->count();

        // Maintenances en cours que je gère
        $maintenancesManaged = Maintenance::where('technicien_id', $user->id)
            ->whereIn('statut', ['en_cours', 'diagnostic'])
            ->count();

        return [
            'my_total_submissions' => $myTotal,
            'my_pending_submissions' => $myPending,
            'my_approved_submissions' => $myApproved,
            'my_rejected_submissions' => $myRejected,
            'equipments_managed' => $equipmentsManaged,
            'maintenances_managed' => $maintenancesManaged,
            'approval_rate' => $myTotal > 0 ? round(($myApproved / $myTotal) * 100, 2) : 0,
        ];
    }

    /**
     * Obtenir les soumissions récentes
     */
    private function getRecentSubmissions($user, $isSuperAdmin, $request)
    {
        $query = TransitionApproval::with(['equipment', 'submitter', 'approver'])
            ->orderBy('created_at', 'desc');

        // Si pas Super Admin, seulement ses soumissions
        if (!$isSuperAdmin) {
            $query->where('submitted_by', $user->id);
        }

        // Filtres
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('equipment', function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('numero_serie', 'like', "%{$search}%");
                })
                ->orWhereHas('submitter', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        return $query->paginate(15)->withQueryString();
    }

    /**
     * Obtenir les graphiques Super Admin
     */
    private function getSuperAdminCharts($user, $request)
    {
        // Données pour le graphique des statuts
        $statusData = TransitionApproval::select('status', DB::raw('COUNT(*) as count'))
            ->when($request->has('date_range'), function ($q) use ($request) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) === 2) {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                }
            })
            ->when($request->has('type') && $request->type !== 'all', function ($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->groupBy('status')
            ->get();

        // Données pour le graphique des types
        $typesData = TransitionApproval::select('type', DB::raw('COUNT(*) as count'))
            ->when($request->has('date_range'), function ($q) use ($request) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) === 2) {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                }
            })
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();

        // Données pour le graphique temporel (30 derniers jours)
        $last30Days = Carbon::now()->subDays(30);
        $timelineData = TransitionApproval::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending')
            )
            ->where('created_at', '>=', $last30Days)
            ->when($request->has('type') && $request->type !== 'all', function ($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top des demandeurs
        $topSubmitters = User::select('users.id', 'users.name', 'users.email', DB::raw('COUNT(transition_approvals.id) as submissions'))
            ->leftJoin('transition_approvals', 'users.id', '=', 'transition_approvals.submitted_by')
            ->when($request->has('date_range'), function ($q) use ($request) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) === 2) {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $q->whereBetween('transition_approvals.created_at', [$startDate, $endDate]);
                }
            })
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('submissions', 'desc')
            ->limit(10)
            ->get();

        // Statistiques des statuts d'équipement
        $equipmentStatusData = Equipment::select('statut', DB::raw('COUNT(*) as count'))
            ->groupBy('statut')
            ->get();

        return [
            'status_data' => $statusData,
            'types_data' => $typesData,
            'timeline_data' => $timelineData,
            'top_submitters' => $topSubmitters,
            'equipment_status_data' => $equipmentStatusData,
        ];
    }

    /**
     * Obtenir les équipements à gérer (Agent IT)
     */
    private function getEquipmentsToManage($user)
    {
        // Équipements en stock qui peuvent être affectés
        $stockEquipments = Equipment::where('statut', 'stock')
            ->whereHas('stock', function ($query) {
                $query->where('etat', 'disponible');
            })
            ->with('stock')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Équipements en maintenance qui nécessitent attention
        $maintenanceEquipments = Equipment::where('statut', 'maintenance')
            ->whereHas('maintenance', function ($query) {
                $query->whereIn('statut', ['diagnostic', 'en_cours'])
                      ->where('date_retour_prevue', '<=', Carbon::now()->addDays(3));
            })
            ->with('maintenance')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return [
            'stock_equipments' => $stockEquipments,
            'maintenance_equipments' => $maintenanceEquipments,
        ];
    }

    /**
     * Obtenir les actions rapides (Agent IT)
     */
    private function getQuickActions($user)
    {
        $actions = [];

        // Vérifier les équipements à affecter
        $stockCount = Equipment::where('statut', 'stock')->count();
        if ($stockCount > 0) {
            $actions[] = [
                'icon' => '📦',
                'title' => 'Équipements en stock',
                'description' => $stockCount . ' équipement(s) disponible(s)',
                'action' => route('equipment.index', ['statut' => 'stock']),
                'color' => 'blue'
            ];
        }

        // Vérifier les maintenances en retard
        $lateMaintenance = Equipment::where('statut', 'maintenance')
            ->whereHas('maintenance', function ($query) {
                $query->where('date_retour_prevue', '<', Carbon::now())
                      ->whereIn('statut', ['diagnostic', 'en_cours']);
            })
            ->count();

        if ($lateMaintenance > 0) {
            $actions[] = [
                'icon' => '⚠️',
                'title' => 'Maintenances en retard',
                'description' => $lateMaintenance . ' maintenance(s) en retard',
                'action' => route('maintenance.index', ['statut' => 'en_retard']),
                'color' => 'red'
            ];
        }

        // Vérifier les soumissions en attente
        $pendingSubmissions = TransitionApproval::where('submitted_by', $user->id)
            ->where('status', 'pending')
            ->count();

        if ($pendingSubmissions > 0) {
            $actions[] = [
                'icon' => '⏳',
                'title' => 'Soumissions en attente',
                'description' => $pendingSubmissions . ' demande(s) en attente',
                'action' => route('transitions.my-submissions'),
                'color' => 'yellow'
            ];
        }

        return $actions;
    }

    /**
     * Obtenir les équipements de l'utilisateur
     */
    private function getUserEquipments($user)
    {
        return Equipment::where('statut', 'parc')
            ->whereHas('parc', function ($query) use ($user) {
                $query->where('utilisateur_id', $user->id);
            })
            ->with(['parc'])
            ->orderBy('date_mise_service', 'desc')
            ->get();
    }

    /**
     * Obtenir l'historique des mouvements
     */
    private function getMovementHistory($user)
    {
        return TransitionApproval::with(['equipment'])
            ->whereHas('equipment', function ($query) use ($user) {
                $query->whereHas('parc', function ($q) use ($user) {
                    $q->where('utilisateur_id', $user->id);
                });
            })
            ->where('status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtenir les demandes de l'utilisateur
     */
    private function getUserRequests($user)
    {
        return TransitionApproval::with(['equipment', 'approver'])
            ->where('submitted_by', $user->id)
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * API pour obtenir les statistiques
     */
    public function getStats(Request $request)
    {
        $user = auth()->user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        $isSuperAdmin = in_array($role, ['super_admin', 'admin', 'responsable_approbation'])
            || $user->email === 'superadmin@cofina.sn';

        $stats = $this->getSuperAdminStats($user, $request);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * API pour obtenir les soumissions
     */
    public function getSubmissions(Request $request)
    {
        $user = auth()->user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        $isSuperAdmin = in_array($role, ['super_admin', 'admin', 'responsable_approbation'])
            || $user->email === 'superadmin@cofina.sn';

        $submissions = $this->getRecentSubmissions($user, $isSuperAdmin, $request);

        return response()->json([
            'success' => true,
            'data' => $submissions
        ]);
    }

    /**
     * API pour obtenir les graphiques
     */
    public function getCharts(Request $request)
    {
        $user = auth()->user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        $isSuperAdmin = in_array($role, ['super_admin', 'admin', 'responsable_approbation'])
            || $user->email === 'superadmin@cofina.sn';

        $charts = $this->getSuperAdminCharts($user, $request);

        return response()->json([
            'success' => true,
            'data' => $charts
        ]);
    }

    /**
     * Exporter les données du dashboard
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        $isSuperAdmin = in_array($role, ['super_admin', 'admin', 'responsable_approbation'])
            || $user->email === 'superadmin@cofina.sn';

        $query = TransitionApproval::with(['equipment', 'submitter', 'approver'])
            ->orderBy('created_at', 'desc');

        if (!$isSuperAdmin) {
            $query->where('submitted_by', $user->id);
        }

        // Appliquer les filtres
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        $submissions = $query->get();

        // Format pour CSV
        $csvData = [];
        $csvData[] = ['ID', 'Équipement', 'N° Série', 'Type', 'De', 'Vers', 'Statut', 'Demandeur', 'Date demande', 'Approbateur', 'Date approbation'];

        foreach ($submissions as $submission) {
            $csvData[] = [
                $submission->id,
                $submission->equipment->nom ?? 'N/A',
                $submission->equipment->numero_serie ?? 'N/A',
                $this->getTypeLabel($submission->type),
                ucfirst($submission->from_status),
                ucfirst($submission->to_status),
                $this->getStatusLabel($submission->status),
                $submission->submitter->name ?? 'N/A',
                $submission->created_at->format('d/m/Y H:i'),
                $submission->approver->name ?? 'N/A',
                $submission->approved_at ? $submission->approved_at->format('d/m/Y H:i') : 'N/A'
            ];
        }

        $filename = 'soumissions_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($csvData) {
            $handle = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($handle, $row, ';');
            }
            fclose($handle);
        }, $filename);
    }

    /**
     * Obtenir le libellé d'un type
     */
    private function getTypeLabel($type)
    {
        $labels = [
            'stock_to_parc' => 'Stock → Parc',
            'stock_to_hors_service' => 'Stock → Hors Service',
            'parc_to_maintenance' => 'Parc → Maintenance',
            'parc_to_hors_service' => 'Parc → Hors Service',
            'parc_to_perdu' => 'Parc → Perdu',
            'maintenance_to_stock' => 'Maintenance → Stock',
            'maintenance_to_hors_service' => 'Maintenance → Hors Service',
        ];

        return $labels[$type] ?? $type;
    }

    /**
     * Obtenir le libellé d'un statut
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
        ];

        return $labels[$status] ?? $status;
    }
}