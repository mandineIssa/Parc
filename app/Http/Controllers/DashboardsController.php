<?php

namespace App\Http\Controllers;

use App\Models\TransitionApproval;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashboardsController extends Controller
{
    /**
     * Afficher le dashboard principal
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        // Déterminer ce que l'utilisateur peut voir
        $isSuperAdmin = in_array($role, ['super_admin', 'admin', 'responsable_approbation'])
            || $user->email === 'superadmin@cofina.sn';

        // Statistiques globales
        $stats = $this->getDashboardStats($user, $isSuperAdmin, $request);

        // Soumissions récentes
        $recentSubmissions = $this->getRecentSubmissions($user, $isSuperAdmin, $request);

        // Graphiques
        $charts = $this->getChartsData($user, $isSuperAdmin, $request);

        return view('dashboards.index', compact('stats', 'recentSubmissions', 'charts'));
    }

    /**
     * Obtenir les statistiques du dashboard
     */
    private function getDashboardStats($user, $isSuperAdmin, $request)
    {
        $query = TransitionApproval::query();

        // Si pas Super Admin, seulement ses soumissions
        if (!$isSuperAdmin) {
            $query->where('submitted_by', $user->id);
        }

        // Appliquer les filtres de date
        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->date_range);
            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        // Filtrer par type si spécifié
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Calculer les statistiques
        $total = $query->count();
        $pending = (clone $query)->where('status', 'pending')->count();
        $approved = (clone $query)->where('status', 'approved')->count();
        $rejected = (clone $query)->where('status', 'rejected')->count();

        // Statistiques par type de transition
        $typesStats = TransitionApproval::select('type', DB::raw('COUNT(*) as count'))
            ->when(!$isSuperAdmin, function ($q) use ($user) {
                $q->where('submitted_by', $user->id);
            })
            ->when($request->has('date_range'), function ($q) use ($request) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) === 2) {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                }
            })
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'types' => $typesStats,
            'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0,
            'rejection_rate' => $total > 0 ? round(($rejected / $total) * 100, 2) : 0,
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

        return $query->paginate(10)->withQueryString();
    }

    /**
     * Obtenir les données pour les graphiques
     */
    private function getChartsData($user, $isSuperAdmin, $request)
    {
        // Données pour le graphique des statuts (pie chart)
        $statusData = TransitionApproval::select('status', DB::raw('COUNT(*) as count'))
            ->when(!$isSuperAdmin, function ($q) use ($user) {
                $q->where('submitted_by', $user->id);
            })
            ->when($request->has('date_range'), function ($q) use ($request) {
                $dates = explode(' - ', $request->date_range);
                if (count($dates) === 2) {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                }
            })
            ->groupBy('status')
            ->get();

        // Données pour le graphique des types (bar chart)
        $typesData = TransitionApproval::select('type', DB::raw('COUNT(*) as count'))
            ->when(!$isSuperAdmin, function ($q) use ($user) {
                $q->where('submitted_by', $user->id);
            })
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

        // Données pour le graphique temporel (line chart)
        $last30Days = Carbon::now()->subDays(30);
        $timelineData = TransitionApproval::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending')
            )
            ->when(!$isSuperAdmin, function ($q) use ($user) {
                $q->where('submitted_by', $user->id);
            })
            ->where('created_at', '>=', $last30Days)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Données pour le top des demandeurs (si Super Admin)
        $topSubmitters = [];
        if ($isSuperAdmin) {
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
        }

        return [
            'status_data' => $statusData,
            'types_data' => $typesData,
            'timeline_data' => $timelineData,
            'top_submitters' => $topSubmitters,
        ];
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

        $stats = $this->getDashboardStats($user, $isSuperAdmin, $request);

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

        $charts = $this->getChartsData($user, $isSuperAdmin, $request);

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

        // Appliquer les mêmes filtres
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

    /**
     * Dashboard pour mobile (API)
     */
    public function mobileDashboard(Request $request)
    {
        $user = auth()->user();
        $role = strtolower(trim((string) ($user->role ?? '')));

        $isSuperAdmin = in_array($role, ['super_admin', 'admin', 'responsable_approbation'])
            || $user->email === 'superadmin@cofina.sn';

        // Statistiques simplifiées
        $stats = $this->getDashboardStats($user, $isSuperAdmin, $request);

        // Soumissions urgentes (en attente depuis plus de 2 jours)
        $urgentSubmissions = TransitionApproval::with(['equipment'])
            ->where('status', 'pending')
            ->where('created_at', '<=', Carbon::now()->subDays(2))
            ->when(!$isSuperAdmin, function ($q) use ($user) {
                $q->where('submitted_by', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'urgent' => $urgentSubmissions,
                'user_role' => $role,
                'is_super_admin' => $isSuperAdmin,
            ]
        ]);
    }
}