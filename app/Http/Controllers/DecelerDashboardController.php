<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Equipment;
use App\Models\Deceler;
use Illuminate\Http\Request;

class DecelerDashboardController extends Controller
{
    /**
     * Display the deceler informatique dashboard.
     */
    public function index()
    {
        // Récupérer tous les stocks de type informatique avec type_stock = 'deceler'
        $stocks = Stock::with(['equipment', 'deceler'])
            ->whereHas('equipment', function($query) {
                $query->where('type', 'informatique');
            })
            ->where('type_stock', 'deceler')
            ->orderBy('date_entree', 'desc')
            ->paginate(20);
        
        // Statistiques - CORRECTION : utiliser la valeur résiduelle au lieu du prix
        $stats = [
            'total' => Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'informatique');
                })->count(),
            
            'valeur_totale' => Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'informatique');
                })
                ->with(['deceler']) // Charger la relation deceler
                ->get()
                ->sum(function($stock) {
                    // Utiliser la valeur résiduelle du deceler au lieu du prix de l'équipement
                    return ($stock->deceler?->valeur_residuelle ?? 0) * $stock->quantite;
                }),
            
            'recent_entries' => Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'informatique');
                })
                ->with(['equipment', 'deceler'])
                ->orderBy('date_entree', 'desc')
                ->take(5)
                ->get(),
        ];
        
        // Catégories d'équipements
        $categoryStats = [];
        $categories = Equipment::where('type', 'informatique')
            ->select('categorie_id')
            ->with('categorie')
            ->distinct()
            ->get();
            
        foreach ($categories as $equipment) {
            if ($equipment->categorie) {
                $categoryName = $equipment->categorie->nom;
                $categoryStats[$categoryName] = Stock::where('type_stock', 'deceler')
                    ->whereHas('equipment', function($query) use ($equipment) {
                        $query->where('type', 'informatique')
                              ->where('categorie_id', $equipment->categorie_id);
                    })->count();
            }
        }
        
        return view('dashboard.deceler-informatique', compact('stocks', 'stats', 'categoryStats'));
    }
    
    /**
     * Filter stocks based on criteria.
     */
    public function filter(Request $request)
    {
        $query = Stock::with(['equipment', 'deceler'])
            ->where('type_stock', 'deceler')
            ->whereHas('equipment', function($query) {
                $query->where('type', 'informatique');
            });
        
        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_serie', 'like', '%' . $search . '%')
                  ->orWhereHas('equipment', function($eq) use ($search) {
                      $eq->where('marque', 'like', '%' . $search . '%')
                         ->orWhere('modele', 'like', '%' . $search . '%')
                         ->orWhere('nom', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('deceler', function($dec) use ($search) {
                      $dec->where('diagnostic', 'like', '%' . $search . '%')
                          ->orWhere('raison_retour', 'like', '%' . $search . '%');
                  });
            });
        }
        
        if ($request->filled('etat')) {
            $query->whereHas('deceler', function($q) use ($request) {
                $q->where('etat_retour', $request->etat);
            });
        }
        
        if ($request->filled('categorie')) {
            $query->whereHas('equipment', function($q) use ($request) {
                $q->whereHas('categorie', function($cat) use ($request) {
                    $cat->where('nom', $request->categorie);
                });
            });
        }
        
        if ($request->filled('origine')) {
            $query->whereHas('deceler', function($q) use ($request) {
                $q->where('origine', $request->origine);
            });
        }
        
        $stocks = $query->orderBy('date_entree', 'desc')->paginate(20)->appends($request->all());
        
        // Récupérer les statistiques
        $stats = [
            'total' => Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'informatique');
                })->count(),
            
            'valeur_totale' => Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'informatique');
                })
                ->with(['deceler'])
                ->get()
                ->sum(function($stock) {
                    return ($stock->deceler?->valeur_residuelle ?? 0) * $stock->quantite;
                }),
            
            'recent_entries' => Stock::where('type_stock', 'deceler')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'informatique');
                })
                ->with(['equipment', 'deceler'])
                ->orderBy('date_entree', 'desc')
                ->take(5)
                ->get(),
        ];
        
        // Récupérer les catégories
        $categoryStats = [];
        $categories = Equipment::where('type', 'informatique')
            ->select('categorie_id')
            ->with('categorie')
            ->distinct()
            ->get();
            
        foreach ($categories as $equipment) {
            if ($equipment->categorie) {
                $categoryName = $equipment->categorie->nom;
                $categoryStats[$categoryName] = Stock::where('type_stock', 'deceler')
                    ->whereHas('equipment', function($query) use ($equipment) {
                        $query->where('type', 'informatique')
                              ->where('categorie_id', $equipment->categorie_id);
                    })->count();
            }
        }
        
        return view('dashboard.deceler-informatique', compact('stocks', 'stats', 'categoryStats'));
    }
    
    /**
     * Export stocks to CSV.
     */
    public function export(Request $request)
    {
        $stocks = Stock::with(['equipment', 'deceler', 'equipment.categorie', 'equipment.fournisseur'])
            ->where('type_stock', 'deceler')
            ->whereHas('equipment', function($query) {
                $query->where('type', 'informatique');
            })
            ->orderBy('date_entree', 'desc')
            ->get();
        
        $filename = 'deceler_informatique_' . date('Y-m-d') . '.csv';
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
                'Catégorie',
                'Marque',
                'Modèle',
                'Fournisseur',
                'État stock',
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
                'Valeur résiduelle (FCFA)',
                'Observations retour',
                'Prix d\'achat (FCFA)',
                'Observations stock'
            ]);
            
            foreach ($stocks as $stock) {
                fputcsv($file, [
                    $stock->numero_serie,
                    $stock->equipment->type ?? '',
                    $stock->equipment->categorie->nom ?? '',
                    $stock->equipment->marque ?? '',
                    $stock->equipment->modele ?? '',
                    $stock->equipment->fournisseur->nom ?? '',
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
        $stock = Stock::with(['equipment', 'deceler', 'equipment.categorie', 'equipment.fournisseur'])
            ->findOrFail($id);
        
        // Vérifier que c'est bien un stock deceler informatique
        if ($stock->type_stock !== 'deceler' || !$stock->equipment || $stock->equipment->type !== 'informatique') {
            abort(404);
        }
        
        return view('dashboard.deceler-details', compact('stock'));
    }
}