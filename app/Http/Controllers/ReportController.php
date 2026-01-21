<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentDetail;
use App\Models\Agency;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User; // Pour technician si c'est un User


class ReportController extends Controller
{
    /**
     * Afficher le tableau de bord des rapports
     */
    public function index()
    {
        // 1. STATISTIQUES GLOBALES
        $totalEquipment = Equipment::count();
        $totalValue = Equipment::sum('prix');
        
        // Équipements par type
        $equipmentByType = Equipment::select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();
        
        // Équipements par état
        $equipmentByEtat = Equipment::select('etat', DB::raw('COUNT(*) as count'))
            ->groupBy('etat')
            ->orderBy('count', 'desc')
            ->get();
        
        // Équipements par statut
        $equipmentByStatut = Equipment::select('statut', DB::raw('COUNT(*) as count'))
            ->groupBy('statut')
            ->orderBy('count', 'desc')
            ->get();
        
        // 2. RÉPARTITION PAR AGENCE - CORRECTION : utiliser 'agence' au lieu de 'agency'
        $equipmentByAgency = Equipment::with('agence')
            ->select('agency_id', DB::raw('COUNT(*) as equipment_count'))
            ->whereNotNull('agency_id')
            ->groupBy('agency_id')
            ->orderBy('equipment_count', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->agence ? $item->agence->nom : 'Non attribué',
                    'equipment_count' => $item->equipment_count,
                    'agency_id' => $item->agency_id
                ];
            });
        
        // 3. VALEUR PAR AGENCE - CORRECTION : utiliser 'agence' au lieu de 'agency'
        $valueByAgency = Equipment::with('agence')
            ->select('agency_id', DB::raw('SUM(prix) as total_value'))
            ->whereNotNull('agency_id')
            ->whereNotNull('prix')
            ->groupBy('agency_id')
            ->orderBy('total_value', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->agence ? $item->agence->nom : 'Non attribué',
                    'total_value' => $item->total_value ?? 0,
                    'agency_id' => $item->agency_id
                ];
            });
        
        // 4. RÉPARTITION PAR FOURNISSEUR
        $equipmentBySupplier = Equipment::with('fournisseur')
            ->select('fournisseur_id', DB::raw('COUNT(*) as equipment_count'))
            ->whereNotNull('fournisseur_id')
            ->groupBy('fournisseur_id')
            ->orderBy('equipment_count', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->fournisseur ? $item->fournisseur->nom : 'Non spécifié',
                    'equipment_count' => $item->equipment_count,
                    'fournisseur_id' => $item->fournisseur_id
                ];
            });
        
        // 5. ÉQUIPEMENTS PAR CATÉGORIE (via EquipmentDetail)
        $equipmentByCategory = EquipmentDetail::with('equipment')
            ->select('categorie', DB::raw('COUNT(*) as equipment_count'))
            ->whereNotNull('categorie')
            ->groupBy('categorie')
            ->orderBy('equipment_count', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->categorie,
                    'equipment_count' => $item->equipment_count
                ];
            });
        
        // 6. ÉQUIPEMENTS PAR CATÉGORIE (via Category table)
        $equipmentByCategoryTable = Category::withCount('equipment')
            ->orderBy('equipment_count', 'desc')
            ->get();
        
        // 7. ÉQUIPEMENTS PAR LOCALISATION
        $equipmentByLocation = Equipment::select('localisation', DB::raw('COUNT(*) as count'))
            ->whereNotNull('localisation')
            ->groupBy('localisation')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        // 8. TOP 5 ÉQUIPEMENTS LES PLUS CHERS - CORRECTION : utiliser 'agence' au lieu de 'agency'
        $topExpensive = Equipment::with(['agence', 'fournisseur'])
            ->whereNotNull('prix')
            ->orderBy('prix', 'desc')
            ->limit(5)
            ->get();
        
        // 9. ÉQUIPEMENTS SANS GARANTIE
        $equipmentWithoutWarranty = Equipment::whereNull('garantie')->count();
        
        // 10. STATISTIQUES PAR DÉPARTEMENT
        $equipmentByDepartment = Equipment::select('departement', DB::raw('COUNT(*) as count'))
            ->whereNotNull('departement')
            ->groupBy('departement')
            ->orderBy('count', 'desc')
            ->get();
        
        // 11. RÉPARTITION PAR TYPE DE CATÉGORIE (via Category table)
        $equipmentByCategoryType = Category::select('type', DB::raw('COUNT(*) as category_count'))
            ->whereNotNull('type')
            ->groupBy('type')
            ->orderBy('category_count', 'desc')
            ->get();
        
        // 12. FOURNISSEURS PAR STATUT
        $suppliersByStatus = Supplier::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->orderBy('count', 'desc')
            ->get();
        
        return view('reports.index', compact(
            'totalEquipment',
            'totalValue',
            'equipmentByType',
            'equipmentByEtat',
            'equipmentByStatut',
            'equipmentByAgency',
            'valueByAgency',
            'equipmentBySupplier',
            'equipmentByCategory',
            'equipmentByCategoryTable',
            'equipmentByLocation',
            'topExpensive',
            'equipmentWithoutWarranty',
            'equipmentByDepartment',
            'equipmentByCategoryType',
            'suppliersByStatus'
        ));
    }
    
    /**
     * Export des équipements
     */
    public function exportEquipment()
    {
        $filename = 'export_equipements_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            $headers = [
                'ID', 'type', 'numero_serie', 'marque', 'modele',
                'garantie', 'date_livraison', 'prix', 'reference_facture', 
                'etat', 'statut', 'fournisseur_id', 'fournisseur_nom',
                'agency_id', 'agency_nom', 'localisation', 'departement',
                'adresse_mac', 'adresse_ip', 'numero_codification',
                'date_mise_service', 'date_amortissement', 'notes',
                'poste_staff', 'created_at', 'updated_at'
            ];
            
            fputcsv($file, $headers, "\t");
            
            // Données - CORRECTION : utiliser 'agence' au lieu de 'agency'
            $equipments = Equipment::with(['fournisseur', 'agence'])->get();
            
            foreach ($equipments as $equipment) {
                $row = [
                    $equipment->id,
                    $equipment->type,
                    $equipment->numero_serie,
                    $equipment->marque,
                    $equipment->modele,
                    $equipment->garantie,
                    $equipment->date_livraison ? $equipment->date_livraison->format('Y-m-d') : '',
                    $equipment->prix,
                    $equipment->reference_facture,
                    $equipment->etat,
                    $equipment->statut,
                    $equipment->fournisseur_id,
                    $equipment->fournisseur ? $equipment->fournisseur->nom : '',
                    $equipment->agency_id,
                    $equipment->agence ? $equipment->agence->nom : '',
                    $equipment->localisation,
                    $equipment->departement,
                    $equipment->adresse_mac,
                    $equipment->adresse_ip,
                    $equipment->numero_codification,
                    $equipment->date_mise_service ? $equipment->date_mise_service->format('Y-m-d') : '',
                    $equipment->date_amortissement ? $equipment->date_amortissement->format('Y-m-d') : '',
                    $equipment->notes,
                    $equipment->poste_staff,
                    $equipment->created_at ? $equipment->created_at->format('Y-m-d H:i:s') : '',
                    $equipment->updated_at ? $equipment->updated_at->format('Y-m-d H:i:s') : '',
                ];
                
                fputcsv($file, $row, "\t");
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export des agences
     */
    public function exportAgencies()
    {
        $filename = 'export_agences_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes (ajustées selon votre table agencies)
            $headers = [
                'ID', 'nom', 'ville', 'adresse', 'telephone', 'email',
                'nombre_equipements', 'valeur_totale', 'created_at', 'updated_at'
            ];
            
            fputcsv($file, $headers, "\t");
            
            // Données avec statistiques
            $agencies = Agency::withCount(['equipment' => function($query) {
                $query->where('statut', '!=', 'hors_service');
            }])->get();
            
            foreach ($agencies as $agency) {
                // Calcul de la valeur totale
                $totalValue = Equipment::where('agency_id', $agency->id)
                    ->where('statut', '!=', 'hors_service')
                    ->sum('prix');
                
                $row = [
                    $agency->id,
                    $agency->nom,
                    $agency->ville ?? '',
                    $agency->adresse ?? '',
                    $agency->telephone ?? '',
                    $agency->email ?? '',
                    $agency->equipment_count,
                    $totalValue,
                    $agency->created_at ? $agency->created_at->format('Y-m-d H:i:s') : '',
                    $agency->updated_at ? $agency->updated_at->format('Y-m-d H:i:s') : '',
                ];
                
                fputcsv($file, $row, "\t");
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export des fournisseurs
     */
    public function exportSuppliers()
    {
        $filename = 'export_fournisseurs_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes (ajustées selon votre table suppliers)
            $headers = [
                'ID', 'nom', 'contact', 'email', 'telephone',
                'adresse', 'ville', 'website', 'status',
                'nombre_equipements', 'montant_total', 'created_at', 'updated_at'
            ];
            
            fputcsv($file, $headers, "\t");
            
            // Données avec statistiques
            $suppliers = Supplier::withCount('equipment')->get();
            
            foreach ($suppliers as $supplier) {
                // Calcul du montant total
                $totalAmount = Equipment::where('fournisseur_id', $supplier->id)
                    ->sum('prix');
                
                $row = [
                    $supplier->id,
                    $supplier->nom,
                    $supplier->contact ?? '',
                    $supplier->email ?? '',
                    $supplier->telephone ?? '',
                    $supplier->adresse ?? '',
                    $supplier->ville ?? '',
                    $supplier->website ?? '',
                    $supplier->status,
                    $supplier->equipment_count,
                    $totalAmount,
                    $supplier->created_at ? $supplier->created_at->format('Y-m-d H:i:s') : '',
                    $supplier->updated_at ? $supplier->updated_at->format('Y-m-d H:i:s') : '',
                ];
                
                fputcsv($file, $row, "\t");
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export des catégories
     */
    public function exportCategories()
    {
        $filename = 'export_categories_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            $headers = [
                'ID', 'type', 'nom', 'description', 'parent_id', 'parent_nom',
                'nombre_equipements', 'created_at', 'updated_at', 'deleted_at'
            ];
            
            fputcsv($file, $headers, "\t");
            
            // Données avec statistiques
            $categories = Category::with(['parent', 'equipment'])
                ->withCount('equipment')
                ->get();
            
            foreach ($categories as $category) {
                $row = [
                    $category->id,
                    $category->type,
                    $category->nom,
                    $category->description ?? '',
                    $category->parent_id,
                    $category->parent ? $category->parent->nom : '',
                    $category->equipment_count,
                    $category->created_at ? $category->created_at->format('Y-m-d H:i:s') : '',
                    $category->updated_at ? $category->updated_at->format('Y-m-d H:i:s') : '',
                    $category->deleted_at ? $category->deleted_at->format('Y-m-d H:i:s') : '',
                ];
                
                fputcsv($file, $row, "\t");
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Afficher le formulaire d'import
     */
    public function importEquipment()
    {
        return view('reports.import');
    }
    
    /**
     * Traiter l'import
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:10240'
        ]);
        
        try {
            // Vous pouvez réutiliser votre logique d'import existante
            // ou créer une version simplifiée
            
            return redirect()->route('reports.index')
                ->with('success', 'Import en cours de développement');
                
        } catch (\Exception $e) {
            Log::error('Erreur import rapports: ' . $e->getMessage());
            
            return back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }
    
    /**
     * API pour les graphiques
     */
    public function apiChartData(Request $request)
    {
        $type = $request->get('type', 'equipment_by_type');
        
        switch ($type) {
            case 'equipment_by_type':
                $data = Equipment::select('type', DB::raw('COUNT(*) as count'))
                    ->groupBy('type')
                    ->get();
                return response()->json([
                    'labels' => $data->pluck('type'),
                    'data' => $data->pluck('count')
                ]);
                
            case 'equipment_by_status':
                $data = Equipment::select('statut', DB::raw('COUNT(*) as count'))
                    ->groupBy('statut')
                    ->get();
                return response()->json([
                    'labels' => $data->pluck('statut'),
                    'data' => $data->pluck('count')
                ]);
                
            case 'equipment_by_agency':
                $data = Equipment::with('agence')
                    ->select('agency_id', DB::raw('COUNT(*) as count'))
                    ->whereNotNull('agency_id')
                    ->groupBy('agency_id')
                    ->get()
                    ->map(function($item) {
                        return [
                            'label' => $item->agence ? $item->agence->nom : 'Inconnu',
                            'value' => $item->count
                        ];
                    });
                return response()->json([
                    'labels' => $data->pluck('label'),
                    'data' => $data->pluck('value')
                ]);
                
            case 'equipment_by_supplier':
                $data = Equipment::with('fournisseur')
                    ->select('fournisseur_id', DB::raw('COUNT(*) as count'))
                    ->whereNotNull('fournisseur_id')
                    ->groupBy('fournisseur_id')
                    ->get()
                    ->map(function($item) {
                        return [
                            'label' => $item->fournisseur ? $item->fournisseur->nom : 'Inconnu',
                            'value' => $item->count
                        ];
                    });
                return response()->json([
                    'labels' => $data->pluck('label'),
                    'data' => $data->pluck('value')
                ]);
                
            default:
                return response()->json(['error' => 'Type de graphique non supporté'], 400);
        }
    }
    
    /**
     * Rapport détaillé des équipements - CORRECTION : utiliser 'agence' au lieu de 'agency'
     */
    public function equipmentReport(Request $request)
    {
        $query = Equipment::with(['agence', 'fournisseur', 'detail']);
        
        // Filtres
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('etat')) {
            $query->where('etat', $request->etat);
        }
        
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        if ($request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }
        
        if ($request->filled('fournisseur_id')) {
            $query->where('fournisseur_id', $request->fournisseur_id);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $equipments = $query->orderBy('created_at', 'desc')->paginate(50);
        
        // Statistiques pour le rapport
        $stats = [
            'total' => $equipments->total(),
            'total_value' => $equipments->sum('prix'),
            'avg_price' => $equipments->avg('prix'),
            'min_price' => $equipments->min('prix'),
            'max_price' => $equipments->max('prix'),
        ];
        
        $agencies = Agency::all();
        $suppliers = Supplier::all();
        $categories = Category::all();
        
        return view('reports.equipment', compact('equipments', 'stats', 'agencies', 'suppliers', 'categories'));
    }
    
    /**
     * Rapport financier - CORRECTION : utiliser 'agence' au lieu de 'agency' (si nécessaire)
     */
    public function financialReport(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        // Investissements par mois
        $monthlyInvestments = Equipment::select(
                DB::raw('MONTH(date_livraison) as month'),
                DB::raw('SUM(prix) as total')
            )
            ->whereYear('date_livraison', $year)
            ->whereNotNull('date_livraison')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Investissements par type
        $investmentsByType = Equipment::select('type', DB::raw('SUM(prix) as total'))
            ->whereYear('date_livraison', $year)
            ->whereNotNull('date_livraison')
            ->groupBy('type')
            ->orderBy('total', 'desc')
            ->get();
        
        // Investissements par fournisseur
        $investmentsBySupplier = Equipment::with('fournisseur')
            ->select('fournisseur_id', DB::raw('SUM(prix) as total'))
            ->whereYear('date_livraison', $year)
            ->whereNotNull('date_livraison')
            ->whereNotNull('fournisseur_id')
            ->groupBy('fournisseur_id')
            ->orderBy('total', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->fournisseur ? $item->fournisseur->nom : 'Inconnu',
                    'total' => $item->total
                ];
            });
        
        // Amortissements
        $amortization = Equipment::select(
                DB::raw('YEAR(date_amortissement) as year'),
                DB::raw('MONTH(date_amortissement) as month'),
                DB::raw('SUM(prix) as total')
            )
            ->whereNotNull('date_amortissement')
            ->whereYear('date_amortissement', $year)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        return view('reports.financial', compact(
            'year',
            'monthlyInvestments',
            'investmentsByType',
            'investmentsBySupplier',
            'amortization'
        ));
    }
    
    /**
     * Rapport de maintenance
     */

public function maintenanceReport(Request $request)
{
    // Vérifiez si la classe existe
    if (!class_exists(Maintenance::class)) {
        return redirect()->route('reports.index')
            ->with('warning', 'Module de maintenance non disponible');
    }
    
    // Vérifiez si la table existe
    if (!\Illuminate\Support\Facades\Schema::hasTable('maintenance')) {
        return redirect()->route('reports.index')
            ->with('warning', 'Table de maintenance non disponible');
    }
    
    // Requête de base - ajustez selon vos relations réelles
    // Note: Votre table maintenance utilise 'numero_serie' pas 'equipment_id'
    // Si vous n'avez pas de relation technician, retirez-la
    $query = Maintenance::with(['equipment' /*, 'technician' */]);
    
    // Filtres - ajustez les noms de colonnes selon votre schéma
    if ($request->filled('type_maintenance')) {
        // Votre migration utilise 'type_maintenance' pas 'type'
        $query->where('type_maintenance', $request->type_maintenance);
    }
    
    if ($request->filled('statut')) {
        // Vérifiez le nom exact des statuts dans votre base
        $query->where('statut', $request->statut);
    }
    
    if ($request->filled('date_from')) {
        // Votre migration utilise 'date_depart' pas 'date_intervention'
        $query->whereDate('date_depart', '>=', $request->date_from);
    }
    
    if ($request->filled('date_to')) {
        $query->whereDate('date_depart', '<=', $request->date_to);
    }
    
    // Pagination
    $maintenances = $query->orderBy('date_depart', 'desc')->paginate(50);
    
    // Statistiques - ajustez selon vos valeurs de statut réelles
    $stats = [
        'total' => Maintenance::count(),
        'en_cours' => Maintenance::where('statut', 'en_cours')->count(),
        // Attention: Votre migration a 'termine' mais votre controller Maintenance a 'terminee'
        // Vérifiez ce qui est en base:
        'termines' => Maintenance::where('statut', 'termine')->orWhere('statut', 'terminee')->count(),
        'cout_total' => Maintenance::sum('cout') ?? 0,
        'cout_moyen' => Maintenance::avg('cout') ?? 0,
    ];
    
    // Équipements les plus défaillants - CORRIGÉ: utilisez 'numero_serie'
    $mostFailing = Maintenance::select('numero_serie', DB::raw('COUNT(*) as count'))
        ->where(function($query) {
            // Gérer les deux cas possibles de statut
            $query->where('statut', 'termine')
                  ->orWhere('statut', 'terminee');
        })
        ->groupBy('numero_serie')
        ->orderBy('count', 'desc')
        ->limit(10)
        ->get()
        ->map(function($item) {
            $equipment = Equipment::where('numero_serie', $item->numero_serie)->first();
            return [
                'equipment' => $equipment ? "{$equipment->type} - {$equipment->numero_serie}" : 'Équipement inconnu',
                'count' => $item->count,
                'numero_serie' => $item->numero_serie
            ];
        });
    
    return view('reports.maintenance', compact('maintenances', 'stats', 'mostFailing'));
}
    
    /**
     * Rapport des catégories
     */
    public function categoriesReport(Request $request)
    {
        $query = Category::with(['parent', 'equipment'])
            ->withCount('equipment');
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $categories = $query->orderBy('equipment_count', 'desc')->paginate(50);
        
        // Statistiques
        $stats = [
            'total_categories' => Category::count(),
            'total_with_equipment' => Category::has('equipment')->count(),
            'total_by_type' => Category::select('type', DB::raw('COUNT(*) as count'))
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type')
                ->toArray(),
            'top_categories' => Category::withCount('equipment')
                ->orderBy('equipment_count', 'desc')
                ->limit(5)
                ->get()
        ];
        
        return view('reports.categories', compact('categories', 'stats'));
    }
    
    
}