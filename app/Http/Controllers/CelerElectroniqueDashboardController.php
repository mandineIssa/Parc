<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;

class CelerElectroniqueDashboardController extends Controller
{
    /**
     * Display the celer reseau dashboard.
     */
    public function index(Request $request)
    {
        // Récupérer les équipements de type Réseau avec statut stock
        $query = Equipment::where('type', 'Electronique')
                          ->where('statut', 'stock');
        
        // Appliquer les filtres
        if ($request->filled('numero_serie')) {
            $query->where('numero_serie', 'LIKE', "%{$request->numero_serie}%");
        }
        
        if ($request->filled('marque')) {
            $query->where('marque', 'LIKE', "%{$request->marque}%");
        }
        
        if ($request->filled('modele')) {
            $query->where('modele', 'LIKE', "%{$request->modele}%");
        }
        
        if ($request->filled('etat')) {
            $query->where('etat', $request->etat);
        }
        
        // NOTE: Vérifiez si cette colonne existe - commentez si nécessaire
        // if ($request->filled('categorie')) {
        //     $query->where('categorie', $request->categorie);
        // }
        
        if ($request->filled('fournisseur_nom')) {
            $query->whereHas('fournisseur', function($q) use ($request) {
                $q->where('nom', 'LIKE', "%{$request->fournisseur_nom}%");
            });
        }
        
        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }
        
        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_serie', 'LIKE', "%{$search}%")
                  ->orWhere('marque', 'LIKE', "%{$search}%")
                  ->orWhere('modele', 'LIKE', "%{$search}%")
                  // ->orWhere('categorie', 'LIKE', "%{$search}%") // Commenté si colonne inexistante
                  ->orWhere('adresse_mac', 'LIKE', "%{$search}%")
                  ->orWhere('localisation', 'LIKE', "%{$search}%")
                  ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }
        
        // Récupérer les données pour les filtres
        $marques = Equipment::where('type', 'Electronique')
                           ->where('statut', 'stock')
                           ->distinct()
                           ->pluck('marque');
        
        // Pour les fournisseurs
        $fournisseurIds = Equipment::where('type', 'Electronique')
                                  ->where('statut', 'stock')
                                  ->whereNotNull('fournisseur_id')
                                  ->distinct()
                                  ->pluck('fournisseur_id');
        
        // Vérifier si le modèle Supplier existe
        $fournisseurs = collect();
        if (class_exists('App\Models\Supplier')) {
            $fournisseurs = \App\Models\Supplier::whereIn('id', $fournisseurIds)->get();
        }
        
        // Calculer les statistiques
        $stats = [
            'total' => (clone $query)->count(),
            'valeur_totale' => (clone $query)->sum('prix') ?? 0
        ];
        
        // Paginer les résultats
        $equipments = $query->with(['fournisseur', 'agence'])
                           ->orderBy('created_at', 'desc')
                           ->paginate(20)
                           ->withQueryString(); // Garde les paramètres dans la pagination
        
        // Tableau vide pour typesReseau (colonne peut-être inexistante)
        $typesReseau = collect();
        
        return view('dashboard.celer-Electronique', compact(
            'equipments', 
            'stats', 
            'marques', 
            'typesReseau',
            'fournisseurs'
        ));
    }
    
    /**
     * Export to CSV
     */
    public function export(Request $request)
    {
        // Logique d'exportation
    }
}