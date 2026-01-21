<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Equipment;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer toutes les maintenances avec les relations
        $maintenances = Maintenance::with(['equipment'])
            ->orderBy('date_depart', 'desc')
            ->paginate(20);
        
        // Statistiques
        $stats = [
            'total' => Maintenance::count(),
            'en_cours' => Maintenance::where('statut', 'en_cours')->count(),
            'terminee' => Maintenance::where('statut', 'terminee')->count(),
            'annulee' => Maintenance::where('statut', 'annulee')->count(),
            'cout_total' => Maintenance::sum('cout'),
            'retard' => Maintenance::where('statut', 'en_cours')
                ->where('date_retour_prevue', '<', now())
                ->count()
        ];
        
        return view('maintenance.index', compact('maintenances', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $equipments = Equipment::whereNotIn('statut', ['hors_service', 'perdu', 'maintenance'])
            ->orderBy('numero_serie')
            ->get();
        
        return view('maintenance.create', compact('equipments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_serie' => 'required|exists:equipment,numero_serie',
            'date_depart' => 'required|date',
            'date_retour_prevue' => 'required|date|after_or_equal:date_depart',
            'type_maintenance' => 'required|in:preventive,corrective,contractuelle,autre',
            'prestataire' => 'required|string|max:200',
            'cout' => 'nullable|numeric|min:0',
            'statut' => 'required|in:en_cours,terminee,annulee',
            'description_panne' => 'required|string|max:1000',
            'travaux_realises' => 'nullable|string|max:1000',
            'observations' => 'nullable|string|max:1000'
        ]);
        
        // Vérifier si l'équipement n'est pas déjà en maintenance
        $existing = Maintenance::where('numero_serie', $request->numero_serie)
            ->whereIn('statut', ['en_cours'])
            ->first();
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['numero_serie' => 'Cet équipement est déjà en maintenance.']);
        }
        
        // Créer l'enregistrement de maintenance
        $maintenance = Maintenance::create($request->all());
        
        // Mettre à jour le statut de l'équipement
        if ($request->statut == 'en_cours') {
            Equipment::where('numero_serie', $request->numero_serie)
                ->update(['statut' => 'maintenance']);
        }
        
        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance enregistrée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $maintenance = Maintenance::with(['equipment'])->findOrFail($id);
        
        return view('maintenance.show', compact('maintenance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $equipments = Equipment::orderBy('numero_serie')->get();
        
        return view('maintenance.edit', compact('maintenance', 'equipments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        
        $request->validate([
            'numero_serie' => 'required|exists:equipment,numero_serie',
            'date_depart' => 'required|date',
            'date_retour_prevue' => 'required|date|after_or_equal:date_depart',
            'type_maintenance' => 'required|in:preventive,corrective,contractuelle,autre',
            'prestataire' => 'required|string|max:200',
            'cout' => 'nullable|numeric|min:0',
            'statut' => 'required|in:en_cours,terminee,annulee',
            'description_panne' => 'required|string|max:1000',
            'travaux_realises' => 'nullable|string|max:1000',
            'observations' => 'nullable|string|max:1000'
        ]);
        
        // Vérifier les changements de statut
        $ancienStatut = $maintenance->statut;
        $nouveauStatut = $request->statut;
        
        // Si le numéro de série change, mettre à jour le statut de l'ancien équipement
        if ($maintenance->numero_serie != $request->numero_serie) {
            if ($ancienStatut == 'en_cours') {
                Equipment::where('numero_serie', $maintenance->numero_serie)
                    ->update(['statut' => 'stock']);
            }
            
            if ($nouveauStatut == 'en_cours') {
                Equipment::where('numero_serie', $request->numero_serie)
                    ->update(['statut' => 'maintenance']);
            }
        } else {
            // Même équipement, gérer le changement de statut
            if ($ancienStatut == 'en_cours' && $nouveauStatut != 'en_cours') {
                Equipment::where('numero_serie', $maintenance->numero_serie)
                    ->update(['statut' => 'stock']);
            } elseif ($ancienStatut != 'en_cours' && $nouveauStatut == 'en_cours') {
                Equipment::where('numero_serie', $maintenance->numero_serie)
                    ->update(['statut' => 'maintenance']);
            }
        }
        
        // Si la maintenance est terminée, ajouter la date de retour réelle
        if ($nouveauStatut == 'terminee' && !$maintenance->date_retour_reelle) {
            $request->merge(['date_retour_reelle' => now()->toDateString()]);
        }
        
        $maintenance->update($request->all());
        
        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        
        // Remettre l'équipement en stock si la maintenance était en cours
        if ($maintenance->statut == 'en_cours') {
            Equipment::where('numero_serie', $maintenance->numero_serie)
                ->update(['statut' => 'stock']);
        }
        
        $maintenance->delete();
        
        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance supprimée avec succès.');
    }
    
    /**
     * Marquer comme terminée
     */
    public function terminer(Request $request, string $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        
        $request->validate([
            'date_retour_reelle' => 'required|date|after_or_equal:' . $maintenance->date_depart->format('Y-m-d'),
            'travaux_realises' => 'required|string|max:1000',
            'cout' => 'required|numeric|min:0',
            'observations_fin' => 'nullable|string|max:500'
        ]);
        
        $maintenance->update([
            'statut' => 'terminee',
            'date_retour_reelle' => $request->date_retour_reelle,
            'travaux_realises' => $request->travaux_realises,
            'cout' => $request->cout,
            'observations' => $maintenance->observations . "\n\n=== FIN DE MAINTENANCE ===\n" . 
                "Date retour: " . $request->date_retour_reelle . "\n" .
                "Travaux réalisés: " . $request->travaux_realises . "\n" .
                "Coût: " . $request->cout . " €\n" .
                "Observations: " . ($request->observations_fin ?? '')
        ]);
        
        // Remettre l'équipement en stock
        Equipment::where('numero_serie', $maintenance->numero_serie)
            ->update(['statut' => 'stock']);
        
        return redirect()->route('maintenance.show', $maintenance->id)
            ->with('success', 'Maintenance marquée comme terminée.');
    }
    
    /**
     * Marquer comme annulée
     */
    public function annuler(Request $request, string $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        
        $request->validate([
            'raison_annulation' => 'required|string|max:500'
        ]);
        
        $maintenance->update([
            'statut' => 'annulee',
            'observations' => $maintenance->observations . "\n\n=== ANNULATION ===\n" . 
                "Date: " . now()->format('Y-m-d') . "\n" .
                "Raison: " . $request->raison_annulation
        ]);
        
        // Remettre l'équipement en stock
        Equipment::where('numero_serie', $maintenance->numero_serie)
            ->update(['statut' => 'stock']);
        
        return redirect()->route('maintenance.show', $maintenance->id)
            ->with('success', 'Maintenance annulée.');
    }
    
    /**
     * Équipements en retard de retour
     */
    public function retard()
    {
        $maintenances = Maintenance::with(['equipment'])
            ->where('statut', 'en_cours')
            ->where('date_retour_prevue', '<', now())
            ->orderBy('date_retour_prevue')
            ->paginate(20);
        
        return view('maintenance.retard', compact('maintenances'));
    }
}