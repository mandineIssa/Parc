<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Equipment;
use App\Models\Deceler;
use Illuminate\Http\Request;

class DecelerReseauDashboardController extends Controller
{
    /**
     * Display the deceler reseau dashboard.
     */
    public function index()
    {
        // Récupérer tous les stocks de type reseau avec type_stock = 'deceler'
        $stocks = Stock::with(['equipment', 'deceler'])
            ->whereHas('equipment', function($query) {
                $query->where('type', 'reseau');
            })
            ->where('type_stock', 'deceler')
            ->orderBy('date_entree', 'desc')
            ->paginate(20);
        
        // Statistiques
        $stats = [
            'total' => Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'reseau');
                })->count(),
            
            'valeur_totale' => Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'reseau');
                })
                ->with(['equipment'])
                ->get()
                ->sum(function($stock) {
                    return $stock->equipment ? ($stock->equipment->prix ?? 0) * $stock->quantite : 0;
                }),
            
            'recent_entries' => Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'reseau');
                })
                ->orderBy('date_entree', 'desc')
                ->take(5)
                ->get(),
        ];
        
        // Catégories d'équipements
        $categorie_ids = Equipment::where('type', 'reseau')
            ->select('categorie_id')
            ->distinct()
            ->pluck('categorie_id');
            
        $categoryStats = [];
        foreach ($categorie_ids as $category) {
            $categoryStats[$category] = Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) use ($category) {
                    $query->where('type', 'reseau')
                          ->where('categorie_id', $category);
                })->count();
        }
        
        return view('dashboard.deceler-reseau', compact('stocks', 'stats', 'categoryStats'));
    }
    
    /**
     * Filter stocks based on criteria.
     */
    public function filter(Request $request)
    {
        $query = Stock::with(['equipment', 'deceler'])
            ->where('type_stock', 'deceler')
            ->whereHas('equipment', function($query) {
                $query->where('type', 'reseau');
            });
        
        // Filtres
        if ($request->filled('etat')) {
            $query->where('etat', $request->etat);
        }
        
        if ($request->filled('categorie_id')) {
            $query->whereHas('equipment', function($q) use ($request) {
                $q->where('categorie_id', $request->categorie_id);
            });
        }
        
        if ($request->filled('marque')) {
            $query->whereHas('equipment', function($q) use ($request) {
                $q->where('marque', 'like', '%' . $request->marque . '%');
            });
        }
        
        if ($request->filled('localisation')) {
            $query->where('localisation_physique', 'like', '%' . $request->localisation . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->where('date_entree', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('date_entree', '<=', $request->date_to);
        }
        
        if ($request->filled('en_stock')) {
            if ($request->en_stock == 'oui') {
                $query->whereNull('date_sortie');
            } else {
                $query->whereNotNull('date_sortie');
            }
        }
        
        if ($request->filled('origine')) {
            $query->whereHas('deceler', function($q) use ($request) {
                $q->where('origine', $request->origine);
            });
        }
        
        $stocks = $query->orderBy('date_entree', 'desc')->paginate(20);
        
        // Récupérer les statistiques
        $stats = [
            'total' => Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'reseau');
                })->count(),
            
            'valeur_totale' => Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'reseau');
                })
                ->with(['equipment'])
                ->get()
                ->sum(function($stock) {
                    return $stock->equipment ? ($stock->equipment->prix ?? 0) * $stock->quantite : 0;
                }),
            
            'recent_entries' => Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'reseau');
                })
                ->orderBy('date_entree', 'desc')
                ->take(5)
                ->get(),
        ];
        
        // Récupérer les catégories
        $categorie_ids = Equipment::where('type', 'reseau')
            ->select('categorie_id')
            ->distinct()
            ->pluck('categorie_id');
            
        $categoryStats = [];
        foreach ($categorie_ids as $category) {
            $categoryStats[$category] = Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) use ($category) {
                    $query->where('type', 'reseau')
                          ->where('categorie_id', $category);
                })->count();
        }
        
        return view('dashboard.deceler-reseau', compact('stocks', 'stats', 'categoryStats'));
    }
    
    /**
     * Export stocks to CSV.
     */
    public function export(Request $request)
    {
        $stocks = Stock::with(['equipment', 'deceler'])
            ->where('type_stock', 'deceler')
            ->whereHas('equipment', function($query) {
                $query->where('type', 'reseau');
            })
            ->orderBy('date_entree', 'desc')
            ->get();
        
        $filename = 'deceler_reseau_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($stocks) {
            $file = fopen('php://output', 'w');
            
            // En-têtes en français
            fputcsv($file, [
                'N° Série',
                'Type',
                'Marque',
                'Modèle',
                'Catégorie',
                'État retour',
                'Localisation',
                'Quantité',
                'Date entrée',
                'Date sortie',
                'Origine retour',
                'Numéro série origine',
                'Date retour',
                'Raison retour',
                'Diagnostic',
                'État retour',
                'Valeur résiduelle',
                'Observations retour',
                'Valeur équipement',
                'Observations stock'
            ]);
            
            foreach ($stocks as $stock) {
                fputcsv($file, [
                    $stock->numero_serie,
                    $stock->equipment->type ?? '',
                    $stock->equipment->marque ?? '',
                    $stock->equipment->modele ?? '',
                    $stock->equipment->categorie ?? '',
                    $stock->etat,
                    $stock->localisation_physique,
                    $stock->quantite,
                    $stock->date_entree ? $stock->date_entree->format('d/m/Y') : '',
                    $stock->date_sortie ? $stock->date_sortie->format('d/m/Y') : '',
                    $stock->deceler->origine ?? '',
                    $stock->deceler->numero_serie_origine ?? '',
                    $stock->deceler->date_retour ? $stock->deceler->date_retour->format('d/m/Y') : '',
                    $stock->deceler->raison_retour ?? '',
                    $stock->deceler->diagnostic ?? '',
                    $stock->deceler->etat_retour ?? '',
                    $stock->deceler->valeur_residuelle ?? 0,
                    $stock->deceler->observations_retour ?? '',
                    $stock->equipment->prix ?? 0,
                    $stock->observations
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Show the details of a specific deceler stock.
     */
    public function show($id)
    {
        $stock = Stock::with(['equipment', 'deceler'])->findOrFail($id);
        
        // Vérifier que c'est bien un stock deceler reseau
        if ($stock->type_stock !== 'deceler' || !$stock->equipment || $stock->equipment->type !== 'reseau') {
            abort(404);
        }
        
        return view('dashboard.deceler-details', compact('stock'));
    }
}