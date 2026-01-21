<?php

namespace App\Http\Controllers;

use App\Models\Perdu;
use App\Models\Equipment;
use Illuminate\Http\Request;

class PerduController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer tous les équipements perdus avec les relations
        $perdus = Perdu::with(['equipment'])
            ->orderBy('date_disparition', 'desc')
            ->paginate(20);
        
        // Statistiques
        $stats = [
            'total' => Perdu::count(),
            'en_recherche' => Perdu::where('statut_recherche', 'en_cours')->count(),
            'trouves' => Perdu::where('statut_recherche', 'trouve')->count(),
            'definitif' => Perdu::where('statut_recherche', 'definitif')->count(),
        ];
        
        return view('perdu.index', compact('perdus', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $equipments = Equipment::whereNotIn('statut', ['hors_service', 'perdu'])
            ->orderBy('numero_serie')
            ->get();
        
        return view('perdu.create', compact('equipments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_serie' => 'required|exists:equipment,numero_serie',
            'date_disparition' => 'required|date',
            'lieu_disparition' => 'required|string|max:200',
            'type_disparition' => 'required|in:vol,perte,oubli,destruction',
            'circonstances' => 'required|string|max:1000',
            'plainte_deposee' => 'boolean',
            'numero_plainte' => 'nullable|required_if:plainte_deposee,1|string|max:50',
            'valeur_assuree' => 'nullable|numeric|min:0',
            'statut_recherche' => 'required|in:en_cours,trouve,definitif',
            'observations' => 'nullable|string|max:1000'
        ]);
        
        // Vérifier si l'équipement n'est pas déjà déclaré perdu
        $existing = Perdu::where('numero_serie', $request->numero_serie)->first();
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['numero_serie' => 'Cet équipement est déjà déclaré perdu.']);
        }
        
        // Créer la déclaration de perte
        $perdu = Perdu::create($request->all());
        
        // Mettre à jour le statut de l'équipement
        Equipment::where('numero_serie', $request->numero_serie)
            ->update(['statut' => 'perdu']);
        
        return redirect()->route('perdu.index')
            ->with('success', 'Déclaration de perte créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $perdu = Perdu::with(['equipment'])->findOrFail($id);
        
        return view('perdu.show', compact('perdu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $perdu = Perdu::findOrFail($id);
        $equipments = Equipment::orderBy('numero_serie')->get();
        
        return view('perdu.edit', compact('perdu', 'equipments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $perdu = Perdu::findOrFail($id);
        
        $request->validate([
            'numero_serie' => 'required|exists:equipment,numero_serie',
            'date_disparition' => 'required|date',
            'lieu_disparition' => 'required|string|max:200',
            'type_disparition' => 'required|in:vol,perte,oubli,destruction',
            'circonstances' => 'required|string|max:1000',
            'plainte_deposee' => 'boolean',
            'numero_plainte' => 'nullable|required_if:plainte_deposee,1|string|max:50',
            'valeur_assuree' => 'nullable|numeric|min:0',
            'statut_recherche' => 'required|in:en_cours,trouve,definitif',
            'observations' => 'nullable|string|max:1000'
        ]);
        
        // Si le statut de recherche passe à "trouvé", remettre l'équipement en stock
        if ($perdu->statut_recherche != 'trouve' && $request->statut_recherche == 'trouve') {
            Equipment::where('numero_serie', $perdu->numero_serie)
                ->update(['statut' => 'stock']);
        }
        
        // Si le numéro de série change, mettre à jour le statut de l'ancien équipement
        if ($perdu->numero_serie != $request->numero_serie) {
            Equipment::where('numero_serie', $perdu->numero_serie)
                ->update(['statut' => 'stock']);
            
            Equipment::where('numero_serie', $request->numero_serie)
                ->update(['statut' => 'perdu']);
        }
        
        $perdu->update($request->all());
        
        return redirect()->route('perdu.index')
            ->with('success', 'Déclaration de perte mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $perdu = Perdu::findOrFail($id);
        
        // Remettre l'équipement en stock
        Equipment::where('numero_serie', $perdu->numero_serie)
            ->update(['statut' => 'stock']);
        
        $perdu->delete();
        
        return redirect()->route('perdu.index')
            ->with('success', 'Déclaration de perte supprimée avec succès.');
    }
    
    /**
     * Marquer un équipement comme retrouvé
     */
    public function retrouver(Request $request, string $id)
    {
        $perdu = Perdu::findOrFail($id);
        
        $request->validate([
            'date_retrouvaille' => 'required|date',
            'lieu_retrouvaille' => 'required|string|max:200',
            'etat_retrouvaille' => 'required|string|max:500',
        ]);
        
        // Mettre à jour le statut
        $perdu->update([
            'statut_recherche' => 'trouve',
            'date_retrouvaille' => $request->date_retrouvaille,
            'lieu_retrouvaille' => $request->lieu_retrouvaille,
            'etat_retrouvaille' => $request->etat_retrouvaille,
            'observations' => $perdu->observations . "\n\nÉquipement retrouvé le " . 
                $request->date_retrouvaille . " à " . $request->lieu_retrouvaille . 
                ". État: " . $request->etat_retrouvaille
        ]);
        
        // Remettre l'équipement en stock
        Equipment::where('numero_serie', $perdu->numero_serie)
            ->update(['statut' => 'stock']);
        
        return redirect()->route('perdu.show', $perdu->id)
            ->with('success', 'Équipement marqué comme retrouvé.');
    }
}