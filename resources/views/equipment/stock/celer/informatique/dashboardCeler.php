<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Celer;
use App\Models\Equipment;
use Illuminate\Http\Request;

class CelerDashboardController extends Controller
{
    public function index()
    {
        // Récupérer tous les stocks de type informatique avec type_stock = 'celer'
        $stocks = Stock::with(['equipment', 'celer'])
            ->whereHas('equipment', function($query) {
                $query->where('type', 'informatique');
            })
            ->where('type_stock', 'celer')
            ->orderBy('date_entree', 'desc')
            ->paginate(20);
        
        // Statistiques
        $stats = [
            'total' => Stock::where('type_stock', 'celer')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'informatique');
                })->count(),
            
            'en_stock' => Stock::where('type_stock', 'celer')
                ->where('date_sortie', null)
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'informatique');
                })->count(),
            
            'sortis' => Stock::where('type_stock', 'celer')
                ->whereNotNull('date_sortie')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'informatique');
                })->count(),
            
            'par_etat' => [
                'neuf' => Stock::where('type_stock', 'celer')
                    ->where('etat', 'neuf')
                    ->whereHas('equipment', function($query) {
                        $query->where('type', 'informatique');
                    })->count(),
                'bon' => Stock::where('type_stock', 'celer')
                    ->where('etat', 'bon')
                    ->whereHas('equipment', function($query) {
                        $query->where('type', 'informatique');
                    })->count(),
                'moyen' => Stock::where('type_stock', 'celer')
                    ->where('etat', 'moyen')
                    ->whereHas('equipment', function($query) {
                        $query->where('type', 'informatique');
                    })->count(),
                'mauvais' => Stock::where('type_stock', 'celer')
                    ->where('etat', 'mauvais')
                    ->whereHas('equipment', function($query) {
                        $query->where('type', 'informatique');
                    })->count(),
            ],
            
            'valeur_totale' => Stock::where('type_stock', 'celer')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'informatique');
                })
                ->with(['equipment'])
                ->get()
                ->sum(function($stock) {
                    return $stock->equipment ? $stock->equipment->valeur : 0;
                }),
            
            'recent_entries' => Stock::where('type_stock', 'celer')
                ->whereHas('equipment', function($query) {
                    $query->where('type', 'informatique');
                })
                ->orderBy('date_entree', 'desc')
                ->take(5)
                ->get(),
        ];
        
        // Catégories d'équipements
        $categories = Equipment::where('type', 'informatique')
            ->select('categorie')
            ->distinct()
            ->pluck('categorie');
            
        $categoryStats = [];
        foreach ($categories as $category) {
            $categoryStats[$category] = Stock::where('type_stock', 'celer')
                ->whereHas('equipment', function($query) use ($category) {
                    $query->where('type', 'informatique')
                          ->where('categorie', $category);
                })->count();
        }
        
        return view('dashboard.celer-informatique', compact('stocks', 'stats', 'categoryStats'));
    }
    
    public function show($id)
    {
        $stock = Stock::with(['equipment', 'celer'])->findOrFail($id);
        
        // Vérifier que c'est bien un stock celer informatique
        if ($stock->type_stock !== 'celer' || !$stock->equipment || $stock->equipment->type !== 'informatique') {
            abort(404);
        }
        
        return view('dashboard.celer-details', compact('stock'));
    }
    
    public function filter(Request $request)
    {
        $query = Stock::with(['equipment', 'celer'])
            ->where('type_stock', 'celer')
            ->whereHas('equipment', function($query) {
                $query->where('type', 'informatique');
            });
        
        // Filtres
        if ($request->filled('etat')) {
            $query->where('etat', $request->etat);
        }
        
        if ($request->filled('categorie')) {
            $query->whereHas('equipment', function($q) use ($request) {
                $q->where('categorie', $request->categorie);
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
        
        $stocks = $query->orderBy('date_entree', 'desc')->paginate(20);
        
        // Récupérer les marques disponibles pour les filtres
        $marques = Equipment::where('type', 'informatique')
            ->select('marque')
            ->distinct()
            ->pluck('marque');
        
        // Récupérer les localisations disponibles
        $localisations = Stock::where('type_stock', 'celer')
            ->select('localisation_physique')
            ->distinct()
            ->pluck('localisation_physique');
        
        return view('dashboard.celer-informatique', compact('stocks', 'marques', 'localisations'));
    }
    
    public function export(Request $request)
    {
        $stocks = Stock::with(['equipment', 'celer'])
            ->where('type_stock', 'celer')
            ->whereHas('equipment', function($query) {
                $query->where('type', 'informatique');
            })
            ->orderBy('date_entree', 'desc')
            ->get();
        
        $filename = 'celer_informatique_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($stocks) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'N° Série',
                'Type',
                'Marque',
                'Modèle',
                'Categorie',
                'État stock',
                'Localisation',
                'Quantité',
                'Date entrée',
                'Date sortie',
                'Certificat garantie',
                'Emballage origine',
                'Valeur',
                'Observations'
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
                    $stock->celer->certificat_garantie ?? 'Non',
                    $stock->celer->emballage_origine ? 'Oui' : 'Non',
                    $stock->equipment->valeur ?? 0,
                    $stock->observations
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}