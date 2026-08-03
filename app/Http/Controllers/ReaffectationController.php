<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Reaffectation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReaffectationController extends Controller
{
    /**
     * Formulaire de réaffectation d'un équipement.
     */
    public function create(Equipment $equipment)
    {
        $departements = \App\Models\Departement::options();

        return view('equipment.parc.reaffecter', compact('equipment', 'departements'));
    }

    /**
     * Enregistre la réaffectation et met à jour le parc.
     */
    public function store(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'nouveau_utilisateur_nom'    => 'required|string|max:100',
            'nouveau_utilisateur_prenom' => 'nullable|string|max:100',
            'nouveau_departement'        => 'nullable|string|max:100',
            'nouvelle_localisation'      => 'nullable|string|max:200',
            'date_reaffectation'         => 'required|date',
            'motif'                      => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($validated, $equipment) {
            // 1. Snapshot de l'ancienne affectation
            $parc = $equipment->parc;

            Reaffectation::create([
                'equipment_id'              => $equipment->id,
                // Ancien
                'ancien_utilisateur_nom'    => $parc->utilisateur_nom    ?? null,
                'ancien_utilisateur_prenom' => $parc->utilisateur_prenom ?? null,
                'ancien_departement'        => $parc->departement         ?? null,
                'ancienne_localisation'     => $parc->localisation        ?? null,
                // Nouveau
                'nouveau_utilisateur_nom'    => $validated['nouveau_utilisateur_nom'],
                'nouveau_utilisateur_prenom' => $validated['nouveau_utilisateur_prenom'] ?? null,
                'nouveau_departement'        => $validated['nouveau_departement']        ?? null,
                'nouvelle_localisation'      => $validated['nouvelle_localisation']      ?? null,
                // Méta
                'date_reaffectation' => $validated['date_reaffectation'],
                'motif'              => $validated['motif'] ?? null,
                'fait_par'           => Auth::id(),
            ]);

            // 2. Mettre à jour la table parc
            if ($parc) {
                $parc->update([
                    'utilisateur_nom'    => $validated['nouveau_utilisateur_nom'],
                    'utilisateur_prenom' => $validated['nouveau_utilisateur_prenom'] ?? $parc->utilisateur_prenom,
                    'departement'        => $validated['nouveau_departement']        ?? $parc->departement,
                    'localisation'       => $validated['nouvelle_localisation']      ?? $parc->localisation,
                ]);
            }
        });

        return redirect()
            ->route('parc.reaffectations.index')
            ->with('success', "L'équipement #{$equipment->numero_serie} a été réaffecté avec succès.");
    }

    /**
     * Page récapitulatif de toutes les réaffectations.
     */
    public function index(Request $request)
    {
        $query = Reaffectation::with(['equipment', 'auteur'])
            ->orderByDesc('date_reaffectation');

        // Filtre par équipement (numéro de série ou nom)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('equipment', function ($q) use ($search) {
                $q->where('numero_serie', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%");
            });
        }

        // Filtre par date
        if ($request->filled('date_debut')) {
            $query->where('date_reaffectation', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->where('date_reaffectation', '<=', $request->date_fin);
        }

        $reaffectations = $query->paginate(20)->withQueryString();

        // IDs des dernières réaffectations actives par équipement (annulables)
        $equipmentIds = $reaffectations->getCollection()->pluck('equipment_id')->unique()->filter();
        $cancellableIds = [];
        foreach ($equipmentIds as $equipmentId) {
            $latestId = Reaffectation::query()
                ->where('equipment_id', $equipmentId)
                ->actives()
                ->orderByDesc('date_reaffectation')
                ->orderByDesc('id')
                ->value('id');
            if ($latestId) {
                $cancellableIds[] = (int) $latestId;
            }
        }

        return view('equipment.parc.reaffectations.index', compact('reaffectations', 'cancellableIds'));
    }

    /**
     * Annule une réaffectation (restaure l'ancien utilisateur sur le parc).
     * Seule la dernière réaffectation active de l'équipement peut être annulée.
     */
    public function cancel(Reaffectation $reaffectation)
    {
        if ($reaffectation->isAnnulee()) {
            return back()->with('error', 'Cette réaffectation est déjà annulée.');
        }

        $latest = Reaffectation::query()
            ->where('equipment_id', $reaffectation->equipment_id)
            ->actives()
            ->orderByDesc('date_reaffectation')
            ->orderByDesc('id')
            ->first();

        if (! $latest || (int) $latest->id !== (int) $reaffectation->id) {
            return back()->with('error', 'Seule la dernière réaffectation active de cet équipement peut être annulée.');
        }

        $equipment = $reaffectation->equipment;
        if (! $equipment) {
            return back()->with('error', 'Équipement introuvable pour cette réaffectation.');
        }

        $parc = $equipment->parc;
        if (! $parc) {
            return back()->with('error', 'Aucune affectation parc active à restaurer pour cet équipement.');
        }

        DB::transaction(function () use ($reaffectation, $parc) {
            $parc->update([
                'utilisateur_nom'    => $reaffectation->ancien_utilisateur_nom,
                'utilisateur_prenom' => $reaffectation->ancien_utilisateur_prenom,
                'departement'        => $reaffectation->ancien_departement,
                'localisation'       => $reaffectation->ancienne_localisation,
            ]);

            $reaffectation->update([
                'annulee_at'  => now(),
                'annulee_par' => Auth::id(),
            ]);
        });

        $serie = $equipment->numero_serie;

        return redirect()
            ->route('parc.reaffectations.index')
            ->with('success', "Réaffectation de l'équipement #{$serie} annulée. L'ancien utilisateur a été restauré.");
    }
}