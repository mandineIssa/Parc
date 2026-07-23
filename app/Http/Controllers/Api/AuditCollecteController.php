<?php

namespace App\Http\Controllers\Api;

use App\Exports\PosteAuditExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePosteAuditRequest;
use App\Models\Poste;
use App\Services\PosteAuditIngestService;
use App\Support\SecureLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class AuditCollecteController extends Controller
{
    public function __construct(
        private readonly PosteAuditIngestService $ingestService,
    ) {}

    /**
     * POST /api/audit — réception d'un audit PowerShell.
     */
    public function store(StorePosteAuditRequest $request): JsonResponse
    {
        try {
            Log::info('poste_audit.received', array_merge(
                SecureLog::requestPayload($request),
                ['client_ip' => $request->ip()]
            ));

            $result = $this->ingestService->ingest($request->posteAttributes());

            return response()->json([
                'message' => $result['created'] ? 'Poste créé et audit enregistré.' : 'Poste mis à jour et audit enregistré.',
                'data' => [
                    'poste_id' => $result['poste']->id,
                    'audit_id' => $result['audit']->id,
                    'created' => $result['created'],
                    'utilisateur_change' => $result['utilisateur_change'],
                    'hostname' => $result['poste']->hostname,
                    'numero_serie' => $result['poste']->numero_serie,
                    'utilisateur_session' => $result['poste']->utilisateur_session,
                ],
            ], $result['created'] ? 201 : 200);
        } catch (Throwable $e) {
            Log::error('poste_audit.ingest_failed', [
                'message' => $e->getMessage(),
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Erreur serveur lors de l\'enregistrement de l\'audit.',
                'error' => 'server_error',
            ], 500);
        }
    }

    /**
     * GET /api/audit — liste paginée + filtres.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(100, max(1, (int) $request->input('per_page', 25)));

        $postes = Poste::query()
            ->filter($request->only([
                'fabricant', 'os', 'utilisateur', 'utilisateur_session',
                'hostname', 'numero_serie', 'antivirus_defender',
                'usb_stockage_bloque', 'bitlocker_actif', 'search',
            ]))
            ->orderByDesc('date_audit')
            ->paginate($perPage);

        return response()->json($postes);
    }

    /**
     * GET /api/audit/{id} — détail + historique complet.
     */
    public function show(int $id): JsonResponse
    {
        $poste = Poste::query()->with(['audits'])->findOrFail($id);

        return response()->json([
            'data' => $poste,
            'historique_utilisateurs' => $poste->historiqueUtilisateurs(),
        ]);
    }

    /**
     * GET /api/audit/export — export Excel (.xlsx) avec les mêmes filtres.
     */
    public function export(Request $request): BinaryFileResponse
    {
        $filters = $request->only([
            'fabricant', 'os', 'utilisateur', 'utilisateur_session',
            'hostname', 'numero_serie', 'antivirus_defender',
            'usb_stockage_bloque', 'bitlocker_actif', 'search',
        ]);

        $filename = 'audits-postes-'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new PosteAuditExport($filters), $filename);
    }
}
