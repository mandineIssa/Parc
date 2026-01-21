<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;

class CelerDashboardController extends Controller
{
    /**
     * Display the celer informatique dashboard.
     */
    public function index(Request $request)
    {
        // Récupérer les équipements de type Informatique avec statut stock
        $query = Equipment::where('type', 'Informatique')
                          ->where('statut', 'stock');
        
        // Appliquer les filtres
        if ($request->filled('marque')) {
            $query->where('marque', $request->marque);
        }
        
        if ($request->filled('etat')) {
            $query->where('etat', $request->etat);
        }
        
        if ($request->filled('fournisseur')) {
            $query->where('fournisseur_id', $request->fournisseur);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_serie', 'LIKE', "%{$search}%")
                  ->orWhere('modele', 'LIKE', "%{$search}%")
                  ->orWhere('marque', 'LIKE', "%{$search}%")
                  ->orWhere('nom', 'LIKE', "%{$search}%");
            });
        }
        
        // Récupérer les données pour les filtres
        $marques = Equipment::where('type', 'Informatique')
                           ->where('statut', 'stock')
                           ->distinct()
                           ->pluck('marque');
        
        // Récupérer les fournisseurs disponibles
        $fournisseurIds = Equipment::where('type', 'Informatique')
                                  ->where('statut', 'stock')
                                  ->whereNotNull('fournisseur_id')
                                  ->distinct()
                                  ->pluck('fournisseur_id');
        
        // Vérifier si le modèle Fournisseur existe
        $fournisseurs = collect();
        if (class_exists('App\Models\supplier')) {
            $fournisseurs = \App\Models\Supplier::whereIn('id', $fournisseurIds)->get();
        } 
        // Si Fournisseur n'existe pas, essayez Supplier
        elseif (class_exists('App\Models\Supplier')) {
            $fournisseurs = \App\Models\Supplier::whereIn('id', $fournisseurIds)->get();
        }
        // Si aucun des deux n'existe, on utilise une collection vide
        
        // Calculer les statistiques
        $stats = [
            'total' => (clone $query)->count(),
            'en_stock' => (clone $query)->count(),
            'sortis' => 0,
            'valeur_totale' => (clone $query)->sum('prix')
        ];
        
        // Paginer les résultats
        $equipments = $query->with(['fournisseur', 'agence'])
                           ->orderBy('created_at', 'desc')
                           ->paginate(20);
        
        return view('dashboard.celer-informatique', compact(
            'equipments', 
            'stats', 
            'marques', 
            'fournisseurs'
        ));
    }
    
    /**
     * Export to CSV
     */
    public function export(Request $request)
    {
        // Logique d'exportation ici
        // Vous pouvez l'implémenter plus tard
    }
}