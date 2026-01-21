<?php

namespace App\Http\Controllers;

use App\Models\HorsService;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HorsServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer tous les équipements hors service avec les relations
        $horsServices = HorsService::with(['equipment'])
            ->orderBy('date_hors_service', 'desc')
            ->paginate(20);
        
        // Statistiques
        $stats = [
            'total' => HorsService::count(),
            'en_attente' => HorsService::whereNull('date_traitement')->count(),
            'traites' => HorsService::whereNotNull('date_traitement')->count(),
            'valeur_totale' => HorsService::sum('valeur_residuelle'),
        ];
        
        return view('hors-service.index', compact('horsServices', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $equipments = Equipment::whereNotIn('statut', ['hors_service', 'perdu'])
            ->orderBy('numero_serie')
            ->get();
        
        return view('hors-service.create', compact('equipments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_serie' => 'required|exists:equipment,numero_serie',
            'date_hors_service' => 'required|date',
            'raison' => 'required|in:panne,obsolescence,accident,autre',
            'description_incident' => 'required|string|max:1000',
            'destinataire' => 'nullable|string|max:200',
            'date_traitement' => 'nullable|date|after_or_equal:date_hors_service',
            'valeur_residuelle' => 'nullable|numeric|min:0',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'observations' => 'nullable|string|max:1000'
        ]);
        
        // Vérifier si l'équipement n'est pas déjà hors service
        $existing = HorsService::where('numero_serie', $request->numero_serie)->first();
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['numero_serie' => 'Cet équipement est déjà déclaré hors service.']);
        }
        
        $data = $request->except('justificatif');
        
        // Gérer le téléchargement du justificatif
        if ($request->hasFile('justificatif')) {
            $file = $request->file('justificatif');
            $filename = time() . '_' . $request->numero_serie . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('hors-service/justificatifs', $filename, 'public');
            $data['justificatif'] = $path;
        }
        
        // Créer la déclaration hors service
        $horsService = HorsService::create($data);
        
        // Mettre à jour le statut de l'équipement
        Equipment::where('numero_serie', $request->numero_serie)
            ->update(['statut' => 'hors_service']);
        
        return redirect()->route('hors-service.index')
            ->with('success', 'Équipement déclaré hors service avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $horsService = HorsService::with(['equipment'])->findOrFail($id);
        
        return view('hors-service.show', compact('horsService'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $horsService = HorsService::findOrFail($id);
        $equipments = Equipment::orderBy('numero_serie')->get();
        
        return view('hors-service.edit', compact('horsService', 'equipments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $horsService = HorsService::findOrFail($id);
        
        $request->validate([
            'numero_serie' => 'required|exists:equipment,numero_serie',
            'date_hors_service' => 'required|date',
            'raison' => 'required|in:panne,obsolescence,accident,autre',
            'description_incident' => 'required|string|max:1000',
            'destinataire' => 'nullable|string|max:200',
            'date_traitement' => 'nullable|date|after_or_equal:date_hors_service',
            'valeur_residuelle' => 'nullable|numeric|min:0',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'observations' => 'nullable|string|max:1000'
        ]);
        
        $data = $request->except('justificatif');
        
        // Gérer le téléchargement du justificatif
        if ($request->hasFile('justificatif')) {
            // Supprimer l'ancien fichier s'il existe
            if ($horsService->justificatif && Storage::disk('public')->exists($horsService->justificatif)) {
                Storage::disk('public')->delete($horsService->justificatif);
            }
            
            $file = $request->file('justificatif');
            $filename = time() . '_' . $request->numero_serie . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('hors-service/justificatifs', $filename, 'public');
            $data['justificatif'] = $path;
        }
        
        // Si le numéro de série change, mettre à jour le statut de l'ancien équipement
        if ($horsService->numero_serie != $request->numero_serie) {
            Equipment::where('numero_serie', $horsService->numero_serie)
                ->update(['statut' => 'stock']);
            
            Equipment::where('numero_serie', $request->numero_serie)
                ->update(['statut' => 'hors_service']);
        }
        
        $horsService->update($data);
        
        return redirect()->route('hors-service.index')
            ->with('success', 'Déclaration hors service mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $horsService = HorsService::findOrFail($id);
        
        // Supprimer le fichier justificatif s'il existe
        if ($horsService->justificatif && Storage::disk('public')->exists($horsService->justificatif)) {
            Storage::disk('public')->delete($horsService->justificatif);
        }
        
        // Remettre l'équipement en stock
        Equipment::where('numero_serie', $horsService->numero_serie)
            ->update(['statut' => 'stock']);
        
        $horsService->delete();
        
        return redirect()->route('hors-service.index')
            ->with('success', 'Déclaration hors service supprimée avec succès.');
    }
    
    /**
     * Marquer comme traité
     */
    public function traiter(Request $request, string $id)
    {
        $horsService = HorsService::findOrFail($id);
        
        $request->validate([
            'date_traitement' => 'required|date|after_or_equal:' . $horsService->date_hors_service->format('Y-m-d'),
            'destinataire' => 'nullable|string|max:200',
            'valeur_residuelle' => 'nullable|numeric|min:0',
            'observations_traitement' => 'nullable|string|max:500'
        ]);
        
        $horsService->update([
            'date_traitement' => $request->date_traitement,
            'destinataire' => $request->destinataire,
            'valeur_residuelle' => $request->valeur_residuelle,
            'observations' => $horsService->observations . "\n\n=== TRAITEMENT ===\n" . 
                "Date: " . $request->date_traitement . "\n" .
                "Destinataire: " . ($request->destinataire ?? 'N/A') . "\n" .
                "Valeur résiduelle: " . ($request->valeur_residuelle ?? '0') . " €\n" .
                "Observations: " . ($request->observations_traitement ?? '')
        ]);
        
        return redirect()->route('hors-service.show', $horsService->id)
            ->with('success', 'Équipement marqué comme traité.');
    }
    
    /**
     * Télécharger le justificatif
     */
    public function downloadJustificatif(string $id)
    {
        $horsService = HorsService::findOrFail($id);
        
        if (!$horsService->justificatif || !Storage::disk('public')->exists($horsService->justificatif)) {
            return redirect()->back()->with('error', 'Le justificatif n\'existe pas.');
        }
        
        return Storage::disk('public')->download($horsService->justificatif);
    }
    
    /**
     * Supprimer le justificatif
     */
    public function deleteJustificatif(string $id)
    {
        $horsService = HorsService::findOrFail($id);
        
        if ($horsService->justificatif && Storage::disk('public')->exists($horsService->justificatif)) {
            Storage::disk('public')->delete($horsService->justificatif);
            $horsService->update(['justificatif' => null]);
            
            return redirect()->back()->with('success', 'Justificatif supprimé avec succès.');
        }
        
        return redirect()->back()->with('error', 'Aucun justificatif à supprimer.');
    }
}