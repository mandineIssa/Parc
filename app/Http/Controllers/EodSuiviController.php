<?php
// app/Http/Controllers/EodSuiviController.php

namespace App\Http\Controllers;

use App\Models\EodSuivi;
use App\Services\EodSuiviNotifier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EodSuiviController extends Controller
{
    public function __construct(
        private readonly EodSuiviNotifier $eodNotifier
    ) {}

    // ==================== N+1 (créateur) ====================

    public function n1Index()
    {
        $this->authorizeRole('N1');

        $fiches = EodSuivi::where('created_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('eod.n1.index', compact('fiches'));
    }

    public function n1Create()
    {
        $this->authorizeRole('N1');

        return view('eod.n1.form', ['eodRoutePrefix' => 'eod.n1']);
    }

    public function n1Store(Request $request)
    {
        $this->authorizeRole('N1');

        $data = $this->validatedEodPayload($request);
        $data['status'] = 'DRAFT';
        $data['created_by'] = Auth::id();
        $data['institution'] = 'COFINA';
        $data['systeme'] = 'Oracle FLEXCUBE Core Banking';
        $data['responsable_batch'] = $this->currentUserFullName();
        $data['history'] = [[
            'role' => 'N1',
            'action' => 'Création de la fiche de suivi EOD',
            'at' => now()->format('d/m/Y H:i:s'),
        ]];

        $fiche = EodSuivi::create($data);
        $this->attachEmargementSignature($request, $fiche);
        $this->attachEodFiles($request, $fiche);

        return $this->submitFicheToN3AndController($fiche->fresh(), 'eod.n1.index');
    }

    public function n1Edit(EodSuivi $fiche)
    {
        $this->authorizeRole('N1');
        $this->authorizeOwner($fiche);

        return $this->renderAuthorForm($fiche, 'eod.n1');
    }

    public function n1Update(Request $request, EodSuivi $fiche)
    {
        $this->authorizeRole('N1');
        $this->authorizeOwner($fiche);

        if (! in_array($fiche->status, ['DRAFT', 'REJECTED'], true)) {
            return back()->with('error', 'Cette fiche ne peut plus être modifiée.');
        }

        $data = $this->validatedEodPayload($request);
        $data['updated_by'] = Auth::id();
        $data['responsable_batch'] = $this->currentUserFullName();

        $fiche->update($data);
        $this->attachEmargementSignature($request, $fiche);
        $this->attachEodFiles($request, $fiche);

        return $this->submitFicheToN3AndController($fiche->fresh(), 'eod.n1.index');
    }

    public function n1SubmitToN3Controller(Request $request, EodSuivi $fiche)
    {
        $this->authorizeRole('N1');
        $this->authorizeOwner($fiche);

        return $this->submitFicheToN3AndController($fiche, 'eod.n1.index');
    }

    // ==================== N+2 (créateur) ====================

    public function n2Create()
    {
        $this->authorizeRole('N2');

        return view('eod.n1.form', ['eodRoutePrefix' => 'eod.n2']);
    }

    public function n2Store(Request $request)
    {
        $this->authorizeRole('N2');

        $data = $this->validatedEodPayload($request);
        $data['status'] = 'DRAFT';
        $data['created_by'] = Auth::id();
        $data['institution'] = 'COFINA';
        $data['systeme'] = 'Oracle FLEXCUBE Core Banking';
        $data['responsable_batch'] = $this->currentUserFullName();
        $data['history'] = [[
            'role' => 'N2',
            'action' => 'Création de la fiche de suivi EOD',
            'at' => now()->format('d/m/Y H:i:s'),
        ]];

        $fiche = EodSuivi::create($data);
        $this->attachEmargementSignature($request, $fiche);
        $this->attachEodFiles($request, $fiche);

        return $this->submitFicheToN3AndController($fiche->fresh(), 'eod.n2.index');
    }

    public function n2Edit(EodSuivi $fiche)
    {
        $this->authorizeRole('N2');

        if ($fiche->status === 'PENDING_N2' && (int) $fiche->created_by !== Auth::id()) {
            $batchData = json_decode($fiche->batch_data ?? '[]', true) ?: [];
            $incidentsData = json_decode($fiche->incidents_data ?? '[]', true) ?: [];

            return view('eod.n2.form', compact('fiche', 'batchData', 'incidentsData'));
        }

        if ((int) $fiche->created_by !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas le créateur de cette fiche.');
        }

        if (! in_array($fiche->status, ['DRAFT', 'REJECTED', 'PENDING_N3_CONTROLLER', 'CLOSED', 'VALIDATED'], true)) {
            abort(404);
        }

        return $this->renderAuthorForm($fiche, 'eod.n2');
    }

    public function n2Update(Request $request, EodSuivi $fiche)
    {
        $this->authorizeRole('N2');
        $this->authorizeOwner($fiche);

        if (! in_array($fiche->status, ['DRAFT', 'REJECTED'], true)) {
            return back()->with('error', 'Cette fiche ne peut plus être modifiée.');
        }

        $data = $this->validatedEodPayload($request);
        $data['updated_by'] = Auth::id();
        $data['responsable_batch'] = $this->currentUserFullName();

        $fiche->update($data);
        $this->attachEmargementSignature($request, $fiche);
        $this->attachEodFiles($request, $fiche);

        return $this->submitFicheToN3AndController($fiche->fresh(), 'eod.n2.index');
    }

    public function n2SubmitToN3Controller(Request $request, EodSuivi $fiche)
    {
        $this->authorizeRole('N2');
        $this->authorizeOwner($fiche);

        return $this->submitFicheToN3AndController($fiche, 'eod.n2.index');
    }

    public function n2Index(Request $request)
    {
        $this->authorizeRole('N2');

        $query = EodSuivi::where(function ($q) {
            $q->where('created_by', Auth::id())
                ->orWhere('status', 'PENDING_N2');
        })
            ->orderBy('created_at', 'desc');

        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'pending':
                    $query->whereIn('status', ['DRAFT', 'PENDING_N2', 'PENDING_N3_CONTROLLER', 'PENDING_CONTROLLER']);
                    break;
                case 'closed':
                    $query->whereIn('status', ['CLOSED', 'VALIDATED']);
                    break;
                case 'rejected':
                    $query->where('status', 'REJECTED');
                    break;
            }
        }

        $fiches = $query->paginate(15)->withQueryString();

        return view('eod.n2.index', compact('fiches'));
    }

    public function n2Validate(Request $request, EodSuivi $fiche)
    {
        $this->authorizeRole('N2');

        if ($fiche->status !== 'PENDING_N2') {
            return back()->with('error', 'Cette fiche ne peut pas être validée à ce stade.');
        }

        $data = $request->validate([
            'validation_note' => 'nullable|string',
            'validation_head_it_date' => 'nullable|string',
            'validation_head_it_visa' => 'nullable|string',
            'validation_audit_date' => 'nullable|string',
            'validation_audit_visa' => 'nullable|string',
        ]);

        $data['status'] = 'PENDING_CONTROLLER';
        $data['validated_by'] = Auth::id();
        $data['validated_at'] = now();
        $data['history'] = array_merge($fiche->history ?? [], [[
            'role' => 'N2',
            'action' => 'Fiche validée par N+2 et transmise au Controller (flux historique)',
            'note' => $request->validation_note,
            'at' => now()->format('d/m/Y H:i:s'),
        ]]);

        $fiche->update($data);

        $this->eodNotifier->notifyValidatedByN2($fiche->fresh(), $request->validation_note);

        return redirect()->route('eod.n2.edit', $fiche)
            ->with('success', 'Validation N+2 enregistrée. Fiche transmise au Controller pour signature.');
    }

    public function n2Reject(Request $request, EodSuivi $fiche)
    {
        $this->authorizeRole('N2');

        $request->validate(['rejet_note' => 'required|string']);

        $fiche->status = 'REJECTED';
        $fiche->history = array_merge($fiche->history ?? [], [[
            'role' => 'N2',
            'action' => 'Fiche rejetée',
            'note' => $request->rejet_note,
            'at' => now()->format('d/m/Y H:i:s'),
        ]]);
        $fiche->updated_by = Auth::id();
        $fiche->save();

        $this->eodNotifier->notifyRejected($fiche->fresh(), $request->rejet_note);

        return redirect()->route('eod.n2.index')
            ->with('success', 'Fiche rejetée.');
    }

    // ==================== CONTROLLER ====================

    public function controllerIndex(Request $request)
    {
        $this->authorizeRole('CONTROLLER');

        $query = EodSuivi::whereIn('status', ['PENDING_N3_CONTROLLER', 'PENDING_CONTROLLER', 'CLOSED', 'VALIDATED'])
            ->orderBy('created_at', 'desc');

        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'pending':
                    $query->where(function ($q) {
                        $q->where('status', 'PENDING_CONTROLLER')
                            ->orWhere(function ($q2) {
                                $q2->where('status', 'PENDING_N3_CONTROLLER')
                                    ->whereNull('controller_validated_at');
                            });
                    });
                    break;
                case 'signed':
                    $query->whereIn('status', ['CLOSED', 'VALIDATED'])
                        ->whereNotNull('controller_validated_at');
                    break;
            }
        }

        $fiches = $query->paginate(15)->withQueryString();

        return view('eod.controller.index', compact('fiches'));
    }

    public function controllerEdit(EodSuivi $fiche)
    {
        $this->authorizeRole('CONTROLLER');

        if (! in_array($fiche->status, ['PENDING_N3_CONTROLLER', 'PENDING_CONTROLLER', 'CLOSED', 'VALIDATED'], true)) {
            abort(404);
        }

        $batchData = json_decode($fiche->batch_data ?? '[]', true) ?: [];
        $incidentsData = json_decode($fiche->incidents_data ?? '[]', true) ?: [];

        return view('eod.controller.form', compact('fiche', 'batchData', 'incidentsData'));
    }

    public function controllerSign(Request $request, EodSuivi $fiche)
    {
        $this->authorizeRole('CONTROLLER');

        if ($fiche->status === 'PENDING_CONTROLLER') {
            return $this->controllerSignLegacy($request, $fiche);
        }

        if ($fiche->status !== 'PENDING_N3_CONTROLLER') {
            return back()->with('error', 'Cette fiche n\'est pas en attente de signature Controller.');
        }

        if ($fiche->controller_validated_at) {
            return back()->with('error', 'Signature Controller déjà enregistrée.');
        }

        $request->validate([
            'controller_validation_date' => 'required|string|max:30',
            'controller_validation_visa' => 'nullable|string|max:255',
            'controller_validation_note' => 'nullable|string',
            'controller_signature_file' => 'nullable|image|max:4096',
            'controller_signature_canvas' => 'nullable|string',
        ]);

        $hasSig = $request->hasFile('controller_signature_file')
            || $this->isDataImage($request->input('controller_signature_canvas'));
        $hasVisaText = trim((string) $request->controller_validation_visa) !== '';

        if (! $hasSig && ! $hasVisaText) {
            return back()->with('error', 'Veuillez signer, importer une image ou saisir un visa texte.');
        }

        $sig = $this->storeSignatureFromRequest($request, 'controller_signature_file', 'controller_signature_canvas', 'controller');
        if ($sig) {
            if ($fiche->controller_signature_path) {
                Storage::disk('public')->delete($fiche->controller_signature_path);
            }
            $fiche->controller_signature_path = $sig;
        }

        $fiche->controller_validation_date = $request->controller_validation_date;
        $fiche->controller_validation_visa = $request->controller_validation_visa;
        $fiche->controller_validation_note = $request->controller_validation_note;
        $fiche->controller_validated_by = Auth::id();
        $fiche->controller_validated_at = now();
        $fiche->history = array_merge($fiche->history ?? [], [[
            'role' => 'CONTROLLER',
            'action' => 'Signature Controller enregistrée',
            'note' => $request->controller_validation_note,
            'at' => now()->format('d/m/Y H:i:s'),
        ]]);
        $fiche->updated_by = Auth::id();
        $fiche->save();

        $fiche = $fiche->fresh();
        $this->finalizeDualSign($fiche);
        $fiche = $fiche->fresh();
        $this->notifyAfterSignature($fiche, 'CONTROLLER');

        return redirect()->route('eod.controller.edit', $fiche)
            ->with('success', $fiche->status === 'CLOSED'
                ? 'Fiche validée et clôturée. Un e-mail de confirmation a été envoyé à l\'auteur.'
                : 'Signature enregistrée. En attente de la signature N+3.');
    }

    // ==================== N+3 ====================

    public function n3PendingList()
    {
        $this->authorizeRole(['N3', 'CONTROLLER']);

        $user = Auth::user();

        if ($user->isEodControllerOnly()) {
            $query = EodSuivi::with('validator')
                ->whereIn('status', ['PENDING_N3_CONTROLLER', 'PENDING_CONTROLLER', 'CLOSED', 'VALIDATED'])
                ->orderBy('created_at', 'desc');
            $query->where(function ($q) {
                $q->where('status', 'PENDING_CONTROLLER')
                    ->orWhere(function ($q2) {
                        $q2->where('status', 'PENDING_N3_CONTROLLER')
                            ->whereNull('controller_validated_at');
                    });
            });
            $fiches = $query->paginate(15);
            $pendingAsController = true;

            return view('eod.n3.pending', compact('fiches', 'pendingAsController'));
        }

        $fiches = EodSuivi::with('creator')
            ->where('status', 'PENDING_N3_CONTROLLER')
            ->whereNull('n3_validated_at')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $pendingAsController = false;

        return view('eod.n3.pending', compact('fiches', 'pendingAsController'));
    }

    public function n3Index(Request $request)
    {
        $this->authorizeRole('N3');

        $stats = [
            'total' => EodSuivi::count(),
            'en_attente' => EodSuivi::whereIn('status', ['PENDING_N2', 'PENDING_N3_CONTROLLER', 'PENDING_CONTROLLER'])->count(),
            'en_attente_signature' => EodSuivi::where('status', 'PENDING_N3_CONTROLLER')->whereNull('n3_validated_at')->count(),
            'en_attente_dual' => EodSuivi::where('status', 'PENDING_N3_CONTROLLER')->count(),
            'valides' => EodSuivi::whereIn('status', ['CLOSED', 'VALIDATED'])->count(),
            'rejetes' => EodSuivi::where('status', 'REJECTED')->count(),
            'brouillons' => EodSuivi::where('status', 'DRAFT')->count(),
        ];

        $evolution = EodSuivi::selectRaw('DATE(created_at) as date, count(*) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topContributeurs = EodSuivi::selectRaw('created_by, count(*) as total')
            ->with('creator')
            ->groupBy('created_by')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $dernieresFiches = EodSuivi::with('creator', 'validator')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $repartitionSemaine = EodSuivi::selectRaw('DAYOFWEEK(created_at) as jour, count(*) as total')
            ->groupBy('jour')
            ->get()
            ->mapWithKeys(function ($item) {
                $jours = ['', 'Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];

                return [$jours[$item->jour] => $item->total];
            });

        return view('eod.n3.index', compact(
            'stats', 'evolution', 'topContributeurs', 'dernieresFiches', 'repartitionSemaine'
        ));
    }

    public function n3Statistiques(Request $request)
    {
        $this->authorizeRole('N3');

        $dateDebut = $request->get('date_debut', now()->subMonths(3)->format('Y-m-d'));
        $dateFin = $request->get('date_fin', now()->format('Y-m-d'));

        $parMois = EodSuivi::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mois, 
                                         count(*) as total,
                                         sum(case when status in ("CLOSED", "VALIDATED") then 1 else 0 end) as valides,
                                         sum(case when status = "REJECTED" then 1 else 0 end) as rejetes')
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        $parUtilisateur = EodSuivi::selectRaw('created_by, 
                                               count(*) as total,
                                               sum(case when status in ("CLOSED", "VALIDATED") then 1 else 0 end) as valides,
                                               sum(case when status = "REJECTED" then 1 else 0 end) as rejetes,
                                               avg(timestampdiff(hour, created_at, updated_at)) as duree_moyenne')
            ->with('creator')
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->groupBy('created_by')
            ->orderByDesc('total')
            ->get();

        $incidentsFrequents = [];
        $fiches = EodSuivi::whereNotNull('incidents_data')
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->get();

        foreach ($fiches as $fiche) {
            $incidents = json_decode($fiche->incidents_data, true) ?? [];
            foreach ($incidents as $incident) {
                $type = $incident['incident'] ?? 'Non spécifié';
                $incidentsFrequents[$type] = ($incidentsFrequents[$type] ?? 0) + 1;
            }
        }
        arsort($incidentsFrequents);
        $incidentsFrequents = array_slice($incidentsFrequents, 0, 10);

        return view('eod.n3.statistiques', compact(
            'parMois', 'parUtilisateur', 'incidentsFrequents', 'dateDebut', 'dateFin'
        ));
    }

    public function n3Show(EodSuivi $fiche)
    {
        $this->authorizeRole(['N3', 'CONTROLLER']);

        $batchData = json_decode($fiche->batch_data ?? '[]', true) ?: [];
        $incidentsData = json_decode($fiche->incidents_data ?? '[]', true) ?: [];

        return view('eod.n3.show', compact('fiche', 'batchData', 'incidentsData'));
    }

    public function n3Sign(Request $request, EodSuivi $fiche)
    {
        $this->authorizeRole('N3');

        if ($fiche->status !== 'PENDING_N3_CONTROLLER' || $fiche->n3_validated_at) {
            return back()->with('error', 'Signature N+3 non disponible pour cette fiche.');
        }

        $request->validate([
            'n3_validation_date' => 'required|string|max:30',
            'n3_validation_note' => 'nullable|string',
            'n3_signature_file' => 'nullable|image|max:4096',
            'n3_signature_canvas' => 'nullable|string',
        ]);

        if (! $request->hasFile('n3_signature_file') && ! $this->isDataImage($request->input('n3_signature_canvas'))) {
            return back()->with('error', 'Veuillez signer ou importer une image de signature.');
        }

        $sig = $this->storeSignatureFromRequest($request, 'n3_signature_file', 'n3_signature_canvas', 'n3');
        if (! $sig) {
            return back()->with('error', 'Signature invalide.');
        }

        if ($fiche->n3_signature_path) {
            Storage::disk('public')->delete($fiche->n3_signature_path);
        }

        $fiche->n3_signature_path = $sig;
        $fiche->n3_validation_date = $request->n3_validation_date;
        $fiche->n3_validation_note = $request->n3_validation_note;
        $fiche->n3_validated_by = Auth::id();
        $fiche->n3_validated_at = now();
        $fiche->history = array_merge($fiche->history ?? [], [[
            'role' => 'N3',
            'action' => 'Signature N+3 enregistrée',
            'at' => now()->format('d/m/Y H:i:s'),
        ]]);
        $fiche->updated_by = Auth::id();
        $fiche->save();

        $fiche = $fiche->fresh();
        $this->finalizeDualSign($fiche);
        $fiche = $fiche->fresh();
        $this->notifyAfterSignature($fiche, 'N3');

        return redirect()->route('eod.n3.show', $fiche)
            ->with('success', $fiche->status === 'CLOSED'
                ? 'Fiche validée et clôturée. Un e-mail de confirmation a été envoyé à l\'auteur.'
                : 'Signature N+3 enregistrée. En attente du Controller.');
    }

    public function n3Export($format)
    {
        $this->authorizeRole('N3');

        $fiches = EodSuivi::with('creator', 'validator')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($format === 'csv') {
            $filename = 'eod_export_' . date('Y-m-d') . '.csv';
            $handle = fopen('php://temp', 'w+');

            fputcsv($handle, ['Référence', 'Date', 'Statut', 'Créateur', 'Validateur', 'Date validation', 'Incidents', 'Statut global']);

            foreach ($fiches as $fiche) {
                $incidents = json_decode($fiche->incidents_data, true);
                $nbIncidents = is_array($incidents) ? count($incidents) : 0;

                fputcsv($handle, [
                    $fiche->reference,
                    $fiche->date_traitement->format('d/m/Y'),
                    $fiche->status_label,
                    $fiche->creator?->name ?? 'N/A',
                    $fiche->validator?->name ?? 'N/A',
                    $fiche->validated_at ? $fiche->validated_at->format('d/m/Y H:i') : '',
                    $nbIncidents,
                    $fiche->statut_global ?? 'N/A',
                ]);
            }

            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);

            return response($content)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        return redirect()->back()->with('error', 'Format non supporté');
    }

    public function generatePdf(EodSuivi $fiche)
    {
        $user = Auth::user();
        $isAuthor = in_array($user->role_change, ['N1', 'N2'], true)
            && (int) $fiche->created_by === (int) $user->id;
        $isSupervisor = $user->role === 'super_admin'
            || $user->canAccessEodAsN3()
            || $user->canAccessEodAsController();
        if (! $isAuthor && ! $isSupervisor) {
            abort(403, 'Non autorisé');
        }

        if ($fiche->status === 'CLOSED') {
            // ok
        } elseif ($fiche->status === 'VALIDATED') {
            if (empty($fiche->controller_validation_visa) && empty($fiche->controller_signature_path)) {
                return back()->with('error', 'PDF non disponible pour cette fiche.');
            }
        } else {
            return back()->with('error', 'Le PDF est disponible uniquement après clôture complète (ou validation historique).');
        }

        $fiche->loadMissing(['n3Validator', 'controllerValidator', 'creator']);

        $batchData = json_decode($fiche->batch_data ?? '[]', true) ?: [];
        $incidentsData = json_decode($fiche->incidents_data ?? '[]', true) ?: [];

        $data = [
            'fiche' => $fiche,
            'batchData' => $batchData,
            'incidentsData' => $incidentsData,
            'dateGeneration' => now()->format('d/m/Y H:i:s'),
            'generateur' => trim(($user->prenom ?? '') . ' ' . ($user->name ?? '')),
            'emargementSigDataUri' => $this->fileToPdfDataUri($fiche->emargement_signature_path),
            'n3SigDataUri' => $this->fileToPdfDataUri($fiche->n3_signature_path),
            'controllerSigDataUri' => $this->fileToPdfDataUri($fiche->controller_signature_path),
        ];

        $pdf = Pdf::loadView('eod.pdf.template', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        $filename = 'EOD_' . $fiche->reference . '_' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    // ==================== Internes ====================

    private function renderAuthorForm(EodSuivi $fiche, string $routePrefix)
    {
        $batchData = json_decode($fiche->batch_data ?? '[]', true) ?: [['batch' => '', 'debut' => '', 'fin' => '', 'observation' => '']];
        $incidentsData = json_decode($fiche->incidents_data ?? '[]', true) ?: [['heure' => '', 'incident' => '', 'impact' => '', 'action' => '', 'statut' => '']];
        $eodRoutePrefix = $routePrefix;

        return view('eod.n1.form', compact('fiche', 'batchData', 'incidentsData', 'eodRoutePrefix'));
    }

    private function persistAuthorUpdate(Request $request, EodSuivi $fiche)
    {
        if (! in_array($fiche->status, ['DRAFT', 'REJECTED'], true)) {
            return back()->with('error', 'Cette fiche ne peut plus être modifiée.');
        }

        $data = $this->validatedEodPayload($request);
        $data['updated_by'] = Auth::id();
        $data['responsable_batch'] = $this->currentUserFullName();

        $fiche->update($data);
        $this->attachEmargementSignature($request, $fiche);
        $this->attachEodFiles($request, $fiche);

        return back()->with('success', 'Fiche mise à jour avec succès.');
    }

    private function validatedEodPayload(Request $request): array
    {
        $data = $request->validate([
            'date_traitement' => 'required|date',
            'heure_lancement' => 'required|string',
            'heure_fin' => 'nullable|string',
            'statut_global' => 'nullable|string',
            'responsable_suivi' => 'nullable|string',
            'sauvegarde_avant_incremental' => 'nullable|string',
            'sauvegarde_avant_differentiel' => 'nullable|string',
            'sauvegarde_avant_complet' => 'nullable|string',
            'sauvegarde_avant_heure' => 'nullable|string',
            'sauvegarde_avant_observation' => 'nullable|string',
            'sauvegarde_apres_incremental' => 'nullable|string',
            'sauvegarde_apres_differentiel' => 'nullable|string',
            'sauvegarde_apres_complet' => 'nullable|string',
            'sauvegarde_apres_heure' => 'nullable|string',
            'sauvegarde_apres_observation' => 'nullable|string',
            'nafa_bd_avant_incremental' => 'nullable|string',
            'nafa_bd_avant_differentiel' => 'nullable|string',
            'nafa_bd_avant_complet' => 'nullable|string',
            'nafa_bd_apres_incremental' => 'nullable|string',
            'nafa_bd_apres_differentiel' => 'nullable|string',
            'nafa_bd_apres_complet' => 'nullable|string',
            'nafa_bd_heure' => 'nullable|string',
            'nafa_bd_observation' => 'nullable|string',
            'batch_data' => 'nullable|string',
            'emargement' => 'nullable|string',
            'incidents_data' => 'nullable|string',
            'emargement_signature_file' => 'nullable|image|max:4096',
            'emargement_signature_canvas' => 'nullable|string',
            'eod_attachments_files' => 'nullable|array',
            'eod_attachments_files.*' => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,csv,txt,png,jpg,jpeg',
        ]);

        unset(
            $data['emargement_signature_file'],
            $data['emargement_signature_canvas'],
            $data['eod_attachments_files']
        );

        return $data;
    }

    private function attachEmargementSignature(Request $request, EodSuivi $fiche): void
    {
        $new = $this->storeSignatureFromRequest($request, 'emargement_signature_file', 'emargement_signature_canvas', 'emargement');
        if ($new) {
            if ($fiche->emargement_signature_path) {
                Storage::disk('public')->delete($fiche->emargement_signature_path);
            }
            $fiche->emargement_signature_path = $new;
            $fiche->save();
        }
    }

    private function attachEodFiles(Request $request, EodSuivi $fiche): void
    {
        if (! $request->hasFile('eod_attachments_files')) {
            return;
        }

        $existing = is_array($fiche->attachments) ? $fiche->attachments : [];
        $newItems = [];
        foreach ((array) $request->file('eod_attachments_files') as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }
            $stored = $file->store('eod/attachments', 'public');
            $newItems[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $stored,
                'size' => $file->getSize(),
                'uploaded_at' => now()->format('d/m/Y H:i:s'),
                'uploaded_by' => Auth::id(),
            ];
        }
        if (! empty($newItems)) {
            $fiche->attachments = array_values(array_merge($existing, $newItems));
            $fiche->save();
        }
    }

    private function submitFicheToN3AndController(EodSuivi $fiche, string $redirectIndexRoute)
    {
        if (! in_array($fiche->status, ['DRAFT', 'REJECTED'], true)) {
            return redirect()->route($redirectIndexRoute)
                ->with('error', 'Cette fiche ne peut pas être soumise dans son état actuel.');
        }

        $this->deletePathIfSet($fiche->n3_signature_path);
        $this->deletePathIfSet($fiche->controller_signature_path);

        $fiche->n3_validated_by = null;
        $fiche->n3_validated_at = null;
        $fiche->n3_validation_date = null;
        $fiche->n3_validation_note = null;
        $fiche->n3_signature_path = null;
        $fiche->controller_validated_by = null;
        $fiche->controller_validated_at = null;
        $fiche->controller_validation_date = null;
        $fiche->controller_validation_visa = null;
        $fiche->controller_validation_note = null;
        $fiche->controller_signature_path = null;

        $fiche->status = 'PENDING_N3_CONTROLLER';
        $fiche->history = array_merge($fiche->history ?? [], [[
            'role' => Auth::user()->role_change ?? 'Auteur',
            'action' => 'Fiche soumise à N+3 et Controller pour signature',
            'at' => now()->format('d/m/Y H:i:s'),
        ]]);
        $fiche->updated_by = Auth::id();
        $fiche->save();

        $this->eodNotifier->notifySubmittedForSignatures($fiche->fresh());

        $authorEmail = Auth::user()->email ?? 'votre adresse enregistrée';

        return redirect()->route($redirectIndexRoute)
            ->with('success', "Fiche transmise à N+3 et au Controller pour signature. Un e-mail de confirmation a été envoyé à : {$authorEmail}");
    }

    private function finalizeDualSign(EodSuivi $fiche): void
    {
        $fiche->refresh();
        if ($fiche->status !== 'PENDING_N3_CONTROLLER') {
            return;
        }
        if ($fiche->n3_validated_at && $fiche->controller_validated_at) {
            $fiche->status = 'CLOSED';
            $fiche->history = array_merge($fiche->history ?? [], [[
                'role' => 'Système',
                'action' => 'Fiche clôturée — signatures N+3 et Controller complètes',
                'at' => now()->format('d/m/Y H:i:s'),
            ]]);
            $fiche->save();
        }
    }

    private function notifyAfterSignature(EodSuivi $fiche, string $signedRole): void
    {
        if ($fiche->status === 'CLOSED') {
            $this->eodNotifier->notifyValidated($fiche);

            return;
        }

        if ($fiche->status !== 'PENDING_N3_CONTROLLER') {
            return;
        }

        $signerId = Auth::id();

        if ($signedRole === 'N3' && ! $fiche->controller_validated_at) {
            $this->eodNotifier->notifyWaitingControllerSignature($fiche, $signerId);
        } elseif ($signedRole === 'CONTROLLER' && ! $fiche->n3_validated_at) {
            $this->eodNotifier->notifyWaitingN3Signature($fiche, $signerId);
        }
    }

    private function controllerSignLegacy(Request $request, EodSuivi $fiche)
    {
        $data = $request->validate([
            'controller_validation_date' => 'required|string|max:30',
            'controller_validation_visa' => 'required|string|max:255',
            'controller_validation_note' => 'nullable|string',
        ]);

        $data['status'] = 'VALIDATED';
        $data['controller_validated_by'] = Auth::id();
        $data['controller_validated_at'] = now();
        $data['history'] = array_merge($fiche->history ?? [], [[
            'role' => 'CONTROLLER',
            'action' => 'Validation et signature Controller (flux historique)',
            'note' => $request->controller_validation_note,
            'at' => now()->format('d/m/Y H:i:s'),
        ]]);

        $fiche->update($data);

        $this->eodNotifier->notifyValidated($fiche->fresh());

        return redirect()->route('eod.controller.edit', $fiche)
            ->with('success', 'Validation Controller enregistrée (flux historique).');
    }

    private function currentUserFullName(): string
    {
        $u = Auth::user();
        $p = trim((string) ($u->prenom ?? ''));
        $n = trim((string) ($u->name ?? ''));

        return trim($p . ' ' . $n) ?: trim((string) ($u->email ?? ''));
    }

    private function storeSignatureFromRequest(Request $request, string $fileKey, string $canvasKey, string $subdir): ?string
    {
        if ($request->hasFile($fileKey) && $request->file($fileKey)->isValid()) {
            return $request->file($fileKey)->store("eod/{$subdir}", 'public');
        }

        $b64 = $request->input($canvasKey);
        if ($this->isDataImage($b64)) {
            $parts = explode(',', $b64, 2);
            if (count($parts) === 2) {
                $raw = base64_decode($parts[1], true);
                if ($raw !== false) {
                    $filename = 'eod/' . $subdir . '/' . uniqid('sig_', true) . '.png';
                    Storage::disk('public')->put($filename, $raw);

                    return $filename;
                }
            }
        }

        return null;
    }

    private function isDataImage(?string $value): bool
    {
        return is_string($value) && str_starts_with($value, 'data:image');
    }

    private function deletePathIfSet(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function fileToPdfDataUri(?string $relative): ?string
    {
        if (! $relative) {
            return null;
        }
        $p = storage_path('app/public/' . $relative);
        if (! is_file($p)) {
            return null;
        }
        $mime = @mime_content_type($p) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($p));
    }

    private function authorizeRole($role): void
    {
        $user = Auth::user();
        $allowedRoles = is_array($role) ? $role : [$role];

        if ($user->role === 'super_admin') {
            $controllerOnly = count($allowedRoles) === 1 && $allowedRoles[0] === 'CONTROLLER';
            if ($controllerOnly) {
                if (! $user->canSignEodControllerSlot()) {
                    abort(403, 'Seuls les comptes Contrôleur EOD (batch) ou la désignation Controller — validation batch EOD peuvent accéder à cette fonction.');
                }

                return;
            }

            return;
        }

        foreach ($allowedRoles as $r) {
            if ($this->userMatchesEodRole($user, $r)) {
                return;
            }
        }

        abort(403, 'Rôle EOD requis : ' . implode(' ou ', $allowedRoles));
    }

    private function userMatchesEodRole(\App\Models\User $user, string $r): bool
    {
        return match ($r) {
            'N1' => $user->role_change === 'N1',
            'N2' => $user->role_change === 'N2',
            'N3' => $user->canAccessEodAsN3(),
            'CONTROLLER' => $user->canSignEodControllerSlot(),
            default => false,
        };
    }

    private function authorizeOwner(EodSuivi $fiche)
    {
        if ((int) $fiche->created_by !== (int) Auth::id()) {
            abort(403, 'Vous n\'êtes pas propriétaire de cette fiche.');
        }
    }
}
