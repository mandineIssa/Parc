<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    
    /**
     * Display a listing of the suppliers.
     */
    public function index()
    {
        $suppliers = Supplier::withCount('equipment')
            ->orderBy('nom')
            ->paginate(20);
        
        // Statistiques
        $stats = [
            'total' => Supplier::count(),
            'with_equipment' => Supplier::has('equipment')->count(),
            'active' => Supplier::where('status', 'active')->count(),
            'inactive' => Supplier::where('status', 'inactive')->count(),
            'pending' => Supplier::where('status', 'pending')->count(),
        ];
        
        return view('suppliers.index', compact('suppliers', 'stats'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create()
    {
        $statuses = [
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'pending' => 'En attente',
        ];
        
        return view('suppliers.create', compact('statuses'));
    }

    /**
     * Store a newly created supplier in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'contact' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:200',
            'ville' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:200',
            'status' => 'required|in:active,inactive,pending',
            'notes' => 'nullable|string|max:500',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Fournisseur créé avec succès.');
    }

    /**
     * Display the specified supplier.
     */
    public function show(Supplier $supplier)
    {
        // Équipements fournis par ce fournisseur
        $equipment = $supplier->equipment()
            ->with(['categorie', 'stock' => function($query) {
                $query->where('type_stock', 'deceler');
            }])
            ->paginate(10);
            
        // Statistiques
        $stats = [
            'total_equipment' => $supplier->equipment()->count(),
            'in_stock' => $supplier->equipment()
                ->whereHas('stock', function($query) {
                    $query->where('type_stock', 'deceler');
                })->count(),
            'by_category' => $supplier->equipment()
                ->join('categories', 'equipment.categorie_id', '=', 'categories.id')
                ->select('categories.type', 'categories.nom as category_name', DB::raw('count(*) as count'))
                ->groupBy('categories.type', 'categories.nom')
                ->orderBy('categories.type')
                ->get()
        ];
        
        return view('suppliers.show', compact('supplier', 'equipment', 'stats'));
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier)
    {
        $statuses = [
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'pending' => 'En attente',
        ];
        
        return view('suppliers.edit', compact('supplier', 'statuses'));
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'contact' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:200',
            'ville' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:200',
            'status' => 'required|in:active,inactive,pending',
            'notes' => 'nullable|string|max:500',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Fournisseur mis à jour avec succès.');
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier)
    {
        // Vérifier si le fournisseur est utilisé dans des équipements
        if ($supplier->equipment()->count() > 0) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Impossible de supprimer ce fournisseur car il est associé à des équipements.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Fournisseur supprimé avec succès.');
    }
    
    /**
     * Get suppliers for select (for API/Ajax).
     */
    public function getSuppliers()
    {
        $suppliers = Supplier::where('status', 'active')
            ->orderBy('nom')
            ->get(['id', 'nom']);
            
        return response()->json($suppliers);
    }
}