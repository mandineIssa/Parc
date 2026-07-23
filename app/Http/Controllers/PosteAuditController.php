<?php

namespace App\Http\Controllers;

use App\Exports\PosteAuditExport;
use App\Models\Poste;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PosteAuditController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only([
            'fabricant', 'os', 'utilisateur', 'hostname', 'numero_serie',
            'antivirus_defender', 'usb_stockage_bloque', 'bitlocker_actif', 'search',
        ]);

        $postes = Poste::query()
            ->filter($filters)
            ->orderByDesc('date_audit')
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'total' => Poste::query()->count(),
            'antivirus_off' => Poste::query()->where('antivirus_defender', false)->count(),
            'bitlocker_off' => Poste::query()
                ->where(function ($q): void {
                    $q->whereNull('bitlocker')->orWhere('bitlocker', 'not like', '%C::On%');
                })
                ->count(),
            'audites_24h' => Poste::query()
                ->where('date_audit', '>=', now()->subDay())
                ->count(),
        ];

        return view('audits-postes.index', compact('postes', 'stats', 'filters'));
    }

    public function show(Poste $poste): View
    {
        $poste->load(['audits' => fn ($q) => $q->orderByDesc('date_audit')]);

        return view('audits-postes.show', [
            'poste' => $poste,
            'historiqueUtilisateurs' => $poste->historiqueUtilisateurs(),
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $filters = $request->only([
            'fabricant', 'os', 'utilisateur', 'hostname', 'numero_serie',
            'antivirus_defender', 'usb_stockage_bloque', 'bitlocker_actif', 'search',
        ]);

        $filename = 'audits-postes-'.now()->format('Y-m-d_His').'.xlsx';

        return Excel::download(new PosteAuditExport($filters), $filename);
    }
}
