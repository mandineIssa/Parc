<?php
// app/Http/Controllers/EodSuiviController.php

namespace App\Http\Controllers;

use App\Models\EodSuivi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class EodSuiviController extends Controller
{
    // ==================== N+1 Functions ====================

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
        return view('eod.n1.form');
    }

    public function n1Store(Request $request)
    {
        $this->authorizeRole('N1');
        
        $data = $request->validate([
            'date_traitement'               => 'required|date',
            'heure_lancement'               => 'required|string',
            'heure_fin'                     => 'nullable|string',
            'statut_global'                 => 'nullable|string',
            'responsable_suivi'             => 'nullable|string',

            'sauvegarde_avant_incremental'  => 'nullable|string',
            'sauvegarde_avant_differentiel' => 'nullable|string',
            'sauvegarde_avant_complet'      => 'nullable|string',
            'sauvegarde_avant_heure'        => 'nullable|string',
            'sauvegarde_avant_observation'  => 'nullable|string',

            'sauvegarde_apres_incremental'  => 'nullable|string',
            'sauvegarde_apres_differentiel' => 'nullable|string',
            'sauvegarde_apres_complet'      => 'nullable|string',
            'sauvegarde_apres_heure'        => 'nullable|string',
            'sauvegarde_apres_observation'  => 'nullable|string',

            'nafa_bd_avant_incremental'     => 'nullable|string',
            'nafa_bd_avant_differentiel'    => 'nullable|string',
            'nafa_bd_avant_complet'         => 'nullable|string',
            'nafa_bd_apres_incremental'     => 'nullable|string',
            'nafa_bd_apres_differentiel'    => 'nullable|string',
            'nafa_bd_apres_complet'         => 'nullable|string',
            'nafa_bd_heure'                 => 'nullable|string',
            'nafa_bd_observation'           => 'nullable|string',

            // ✅ FIX : 'string' au lieu de 'json' — le formulaire envoie déjà du JSON sérialisé
            'batch_data'                    => 'nullable|string',

            'emargement'                    => 'nullable|string',
            'responsable_batch'             => 'nullable|string',

            // ✅ FIX : 'string' au lieu de 'json'
            'incidents_data'                => 'nullable|string',
        ]);

        $data['status']      = 'DRAFT';
        $data['created_by']  = Auth::id();
        $data['institution'] = 'COFINA';
        $data['systeme']     = 'Oracle FLEXCUBE Core Banking';
        $data['history']     = [[
            'role'   => 'N1',
            'action' => 'Création de la fiche de suivi EOD',
            'at'     => now()->format('d/m/Y H:i:s')
        ]];

        $fiche = EodSuivi::create($data);

        return redirect()->route('eod.n1.edit', $fiche)
            ->with('success', 'Fiche de suivi créée avec succès.');
    }

    public function n1Edit(EodSuivi $fiche)
    {
        $this->authorizeRole('N1');
        $this->authorizeOwner($fiche);
        
        // ✅ FIX : décoder le JSON stocké en DB pour l'affichage dans la vue
        $batchData     = json_decode($fiche->batch_data     ?? '[]', true) ?: [['batch' => '', 'debut' => '', 'fin' => '', 'observation' => '']];
        $incidentsData = json_decode($fiche->incidents_data ?? '[]', true) ?: [['heure' => '', 'incident' => '', 'impact' => '', 'action' => '', 'statut' => '']];
        
        return view('eod.n1.form', compact('fiche', 'batchData', 'incidentsData'));
    }

    public function n1Update(Request $request, EodSuivi $fiche)
    {
        $this->authorizeRole('N1');
        $this->authorizeOwner($fiche);
        
        // ✅ Vérifier que la fiche est encore modifiable
        if (!in_array($fiche->status, ['DRAFT', 'REJECTED'])) {
            return back()->with('error', 'Cette fiche ne peut plus être modifiée.');
        }

        $data = $request->validate([
            'date_traitement'               => 'required|date',
            'heure_lancement'               => 'required|string',
            'heure_fin'                     => 'nullable|string',
            'statut_global'                 => 'nullable|string',
            'responsable_suivi'             => 'nullable|string',

            'sauvegarde_avant_incremental'  => 'nullable|string',
            'sauvegarde_avant_differentiel' => 'nullable|string',
            'sauvegarde_avant_complet'      => 'nullable|string',
            'sauvegarde_avant_heure'        => 'nullable|string',
            'sauvegarde_avant_observation'  => 'nullable|string',

            'sauvegarde_apres_incremental'  => 'nullable|string',
            'sauvegarde_apres_differentiel' => 'nullable|string',
            'sauvegarde_apres_complet'      => 'nullable|string',
            'sauvegarde_apres_heure'        => 'nullable|string',
            'sauvegarde_apres_observation'  => 'nullable|string',

            'nafa_bd_avant_incremental'     => 'nullable|string',
            'nafa_bd_avant_differentiel'    => 'nullable|string',
            'nafa_bd_avant_complet'         => 'nullable|string',
            'nafa_bd_apres_incremental'     => 'nullable|string',
            'nafa_bd_apres_differentiel'    => 'nullable|string',
            'nafa_bd_apres_complet'         => 'nullable|string',
            'nafa_bd_heure'                 => 'nullable|string',
            'nafa_bd_observation'           => 'nullable|string',

            // ✅ FIX : 'string' au lieu de 'json'
            'batch_data'                    => 'nullable|string',

            'emargement'                    => 'nullable|string',
            'responsable_batch'             => 'nullable|string',

            // ✅ FIX : 'string' au lieu de 'json'
            'incidents_data'                => 'nullable|string',
        ]);

        $data['updated_by'] = Auth::id();
        $fiche->update($data);

        return back()->with('success', 'Fiche mise à jour avec succès.');
    }

    public function n1SubmitToN2(Request $request, EodSuivi $fiche)
    {
        $this->authorizeRole('N1');
        $this->authorizeOwner($fiche);

        // ✅ Vérifier que la fiche est en DRAFT ou REJECTED
        if (!in_array($fiche->status, ['DRAFT', 'REJECTED'])) {
            return redirect()->route('eod.n1.index')
                ->with('error', 'Cette fiche ne peut pas être soumise dans son état actuel.');
        }
        
        $fiche->status     = 'PENDING_N2';
        $fiche->history    = array_merge($fiche->history ?? [], [[
            'role'   => 'N1',
            'action' => 'Fiche soumise à N+2 pour validation',
            'at'     => now()->format('d/m/Y H:i:s')
        ]]);
        $fiche->updated_by = Auth::id();
        $fiche->save();

        return redirect()->route('eod.n1.index')
            ->with('success', 'Fiche transmise à N+2 pour validation.');
    }

    // ==================== N+2 Functions ====================

    public function n2Index(Request $request)
    {
        $this->authorizeRole('N2');
        
        $query = EodSuivi::whereIn('status', ['PENDING_N2', 'VALIDATED', 'REJECTED'])
            ->orderBy('created_at', 'desc');
        
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'pending':   $query->where('status', 'PENDING_N2'); break;
                case 'validated': $query->where('status', 'VALIDATED');  break;
                case 'rejected':  $query->where('status', 'REJECTED');   break;
            }
        }
        
        $fiches = $query->paginate(15)->withQueryString();
        
        return view('eod.n2.index', compact('fiches'));
    }

    public function n2Edit(EodSuivi $fiche)
    {
        $this->authorizeRole('N2');
        
        if (!in_array($fiche->status, ['PENDING_N2', 'VALIDATED', 'REJECTED'])) {
            abort(404);
        }
        
        $batchData     = json_decode($fiche->batch_data     ?? '[]', true) ?: [];
        $incidentsData = json_decode($fiche->incidents_data ?? '[]', true) ?: [];
        
        return view('eod.n2.form', compact('fiche', 'batchData', 'incidentsData'));
    }

    public function n2Validate(Request $request, EodSuivi $fiche)
    {
        $this->authorizeRole('N2');
        
        $data = $request->validate([
            'validation_note'         => 'nullable|string',
            'validation_head_it_date' => 'nullable|string',
            'validation_head_it_visa' => 'nullable|string',
            'validation_audit_date'   => 'nullable|string',
            'validation_audit_visa'   => 'nullable|string',
        ]);

        $data['status']       = 'VALIDATED';
        $data['validated_by'] = Auth::id();
        $data['validated_at'] = now();
        $data['history']      = array_merge($fiche->history ?? [], [[
            'role'   => 'N2',
            'action' => 'Fiche validée',
            'note'   => $request->validation_note,
            'at'     => now()->format('d/m/Y H:i:s')
        ]]);

        $fiche->update($data);

        // ✅ pdf_auto déclenche l'ouverture automatique du PDF dans la vue
        return redirect()->route('eod.n2.edit', $fiche)
            ->with('success', 'Fiche validée avec succès.')
            ->with('pdf_auto', true);
    }

    public function n2Reject(Request $request, EodSuivi $fiche)
    {
        $this->authorizeRole('N2');
        
        $request->validate(['rejet_note' => 'required|string']);

        $fiche->status     = 'REJECTED';
        $fiche->history    = array_merge($fiche->history ?? [], [[
            'role'   => 'N2',
            'action' => 'Fiche rejetée',
            'note'   => $request->rejet_note,
            'at'     => now()->format('d/m/Y H:i:s')
        ]]);
        $fiche->updated_by = Auth::id();
        $fiche->save();

        return redirect()->route('eod.n2.index')
            ->with('success', 'Fiche rejetée.');
    }

    // ==================== N+3 Functions ====================

    public function n3Index(Request $request)
    {
        $this->authorizeRole('N3');
        
        $stats = [
            'total'       => EodSuivi::count(),
            'en_attente'  => EodSuivi::where('status', 'PENDING_N2')->count(),
            'valides'     => EodSuivi::where('status', 'VALIDATED')->count(),
            'rejetes'     => EodSuivi::where('status', 'REJECTED')->count(),
            'brouillons'  => EodSuivi::where('status', 'DRAFT')->count(),
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
        $dateFin   = $request->get('date_fin',   now()->format('Y-m-d'));
        
        $parMois = EodSuivi::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mois, 
                                         count(*) as total,
                                         sum(case when status = "VALIDATED" then 1 else 0 end) as valides,
                                         sum(case when status = "REJECTED" then 1 else 0 end) as rejetes')
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();
        
        $parUtilisateur = EodSuivi::selectRaw('created_by, 
                                               count(*) as total,
                                               sum(case when status = "VALIDATED" then 1 else 0 end) as valides,
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
        $this->authorizeRole('N3');
        
        $batchData     = json_decode($fiche->batch_data     ?? '[]', true) ?: [];
        $incidentsData = json_decode($fiche->incidents_data ?? '[]', true) ?: [];
        
        return view('eod.n3.show', compact('fiche', 'batchData', 'incidentsData'));
    }

    public function n3Export($format)
    {
        $this->authorizeRole('N3');
        
        $fiches = EodSuivi::with('creator', 'validator')
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($format === 'csv') {
            $filename = 'eod_export_' . date('Y-m-d') . '.csv';
            $handle   = fopen('php://temp', 'w+');
            
            fputcsv($handle, ['Référence', 'Date', 'Statut', 'Créateur', 'Validateur', 'Date validation', 'Incidents', 'Statut global']);
            
            foreach ($fiches as $fiche) {
                $incidents   = json_decode($fiche->incidents_data, true);
                $nbIncidents = is_array($incidents) ? count($incidents) : 0;
                
                fputcsv($handle, [
                    $fiche->reference,
                    $fiche->date_traitement->format('d/m/Y'),
                    $fiche->status_label,
                    $fiche->creator?->name  ?? 'N/A',
                    $fiche->validator?->name ?? 'N/A',
                    $fiche->validated_at ? $fiche->validated_at->format('d/m/Y H:i') : '',
                    $nbIncidents,
                    $fiche->statut_global ?? 'N/A'
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
        if (!in_array($user->role_change, ['N2', 'N3']) && $fiche->created_by !== $user->id) {
            abort(403, 'Non autorisé');
        }
        
        $batchData     = json_decode($fiche->batch_data     ?? '[]', true) ?: [];
        $incidentsData = json_decode($fiche->incidents_data ?? '[]', true) ?: [];
        
        $data = [
            'fiche'          => $fiche,
            'batchData'      => $batchData,
            'incidentsData'  => $incidentsData,
            'dateGeneration' => now()->format('d/m/Y H:i:s'),
            'generateur'     => $user->name . ' ' . $user->prenom
        ];
        
        $pdf = Pdf::loadView('eod.pdf.template', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont'          => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true
        ]);
        
        $filename = 'EOD_' . $fiche->reference . '_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    // ==================== Helpers ====================

    private function authorizeRole($role)
    {
        $user = Auth::user();
        // ✅ FIX : si role_change est null, on refuse l'accès (pas de bypass silencieux)
        if ($user->role_change !== $role) {
            abort(403, 'Rôle EOD requis : ' . $role);
        }
    }

    private function authorizeOwner(EodSuivi $fiche)
    {
        if ($fiche->created_by !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas propriétaire de cette fiche.');
        }
    }
}