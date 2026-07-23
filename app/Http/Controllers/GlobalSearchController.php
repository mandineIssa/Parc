<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EodSuivi;
use App\Models\Parc;
use App\Models\TransitionApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $like = '%'.$q.'%';
        $results = [];

        Equipment::query()
            ->where(function ($query) use ($like) {
                $query->where('numero_serie', 'like', $like)
                    ->orWhere('nom', 'like', $like)
                    ->orWhere('marque', 'like', $like)
                    ->orWhere('modele', 'like', $like);
            })
            ->limit(8)
            ->get()
            ->each(function (Equipment $eq) use (&$results) {
                $results[] = [
                    'type' => 'Équipement',
                    'label' => ($eq->nom ?: $eq->marque.' '.$eq->modele).' — '.$eq->numero_serie,
                    'url' => route('equipment.show', $eq),
                ];
            });

        Parc::query()
            ->where(function ($query) use ($like) {
                $query->where('numero_serie', 'like', $like)
                    ->orWhere('utilisateur_nom', 'like', $like)
                    ->orWhere('utilisateur_prenom', 'like', $like);
            })
            ->limit(5)
            ->get()
            ->each(function (Parc $parc) use (&$results) {
                $results[] = [
                    'type' => 'Parc',
                    'label' => $parc->numero_serie.' — '.trim($parc->utilisateur_prenom.' '.$parc->utilisateur_nom),
                    'url' => route('parc.index', ['search' => $parc->numero_serie]),
                ];
            });

        TransitionApproval::query()
            ->when(! Auth::user()?->canApproveTransitions(), fn ($q) => $q->where('submitted_by', Auth::id()))
            ->where(function ($query) use ($like) {
                $query->where('id', 'like', $like)
                    ->orWhere('type', 'like', $like);
            })
            ->limit(5)
            ->get()
            ->each(function (TransitionApproval $a) use (&$results) {
                $results[] = [
                    'type' => 'Transition',
                    'label' => "#{$a->id} — {$a->type}",
                    'url' => route('transitions.approval.show', $a),
                ];
            });

        EodSuivi::query()
            ->where('created_by', Auth::id())
            ->where('reference', 'like', $like)
            ->limit(5)
            ->get()
            ->each(function (EodSuivi $f) use (&$results) {
                $results[] = [
                    'type' => 'EOD',
                    'label' => $f->reference,
                    'url' => route('eod.n1.edit', $f),
                ];
            });

        return response()->json(['results' => array_slice($results, 0, 20)]);
    }
}
