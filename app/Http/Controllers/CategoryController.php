<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $query = Category::mainCategories()->withCount('equipment')->with('subcategories');
        
        // Recherche
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filtre par type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        
        // Tri
        $sort = $request->get('sort', 'type');
        $order = $request->get('order', 'asc');
        $query->orderBy($sort, $order);
        
        $categories = $query->paginate(15);
        
        // Statistiques par type
        $typeStats = [
            'réseaux' => Category::where('type', 'réseaux')->count(),
            'électronique' => Category::where('type', 'électronique')->count(),
            'informatiques' => Category::where('type', 'informatiques')->count(),
        ];
        
        return view('categories.index', compact('categories', 'typeStats'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $parentCategories = Category::mainCategories()->get();
        return view('categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:réseaux,électronique,informatiques',
            'nom' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:500',
            'equipment_list' => 'nullable|array',
            'equipment_list.*' => 'string|max:200',
        ]);

        // Nettoyer les champs d'équipements vides
        if (isset($validated['equipment_list'])) {
            $validated['equipment_list'] = array_filter($validated['equipment_list']);
        }

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function show(Category $category)
    {
        $category->load(['subcategories', 'parent']);
        
        $equipment = $category->equipment()
            ->with(['agence'])
            ->paginate(10);
            
        $stats = [
            'total_equipment' => $category->equipment()->count(),
            'in_stock' => $category->equipment()
                ->whereHas('stock', function($query) {
                    $query->where('type_stock', 'deceler');
                })->count(),
        ];
        
        return view('categories.show', compact('category', 'equipment', 'stats'));
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::where('id', '!=', $category->id)->mainCategories()->get();
        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'type' => 'required|in:réseaux,électronique,informatiques',
            'nom' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:500',
            'equipment_list' => 'nullable|array',
            'equipment_list.*' => 'string|max:200',
        ]);

        // Éviter une catégorie parente qui serait elle-même
        if ($validated['parent_id'] == $category->id) {
            $validated['parent_id'] = null;
        }

        // Nettoyer les champs d'équipements vides
        if (isset($validated['equipment_list'])) {
            $validated['equipment_list'] = array_filter($validated['equipment_list']);
        }

        $category->update($validated);

        return redirect()->route('categories.show', $category)
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(Category $category)
    {
        // Vérifier si la catégorie a des équipements
        if ($category->equipment()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle est associée à des équipements.');
        }

        // Vérifier si la catégorie a des sous-catégories
        if ($category->subcategories()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des sous-catégories.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }

    /**
     * Get categories by type (for API/Ajax).
     */
    public function getByType($type)
    {
        $categories = Category::where('type', $type)
            ->orderBy('nom')
            ->get();
            
        return response()->json($categories);
    }

    /**
     * Get categories by type (for form cascade).
     */
    public function categoriesByType($type)
    {
        $categories = Category::where('type', $type)
            ->where('parent_id', null) // Seulement les catégories principales
            ->orderBy('nom')
            ->get();
            
        return response()->json($categories);
    }

    /**
     * Get subcategories by category (for form cascade).
     */
    public function sousCategoriesByCategorie($categorieId)
    {
        $sousCategories = Category::where('parent_id', $categorieId)
            ->orderBy('nom')
            ->get();
            
        return response()->json($sousCategories);
    }

    /**
     * Display the trash (soft deleted categories).
     */
    public function trash(Request $request)
    {
        $query = Category::onlyTrashed()->withCount('equipment');
        
        // Recherche
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filtre par type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        
        // Tri
        $sort = $request->get('sort', 'deleted_at');
        $order = $request->get('order', 'desc');
        $query->orderBy($sort, $order);
        
        $categories = $query->paginate(15);
        
        return view('categories.trash', compact('categories'));
    }
    
    /**
     * Restore a soft deleted category.
     */
    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();
        
        return redirect()->route('categories.trash')
            ->with('success', 'Catégorie restaurée avec succès.');
    }
    
    /**
     * Permanently delete a category.
     */
    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        
        // Vérifier si la catégorie a des équipements
        if ($category->equipment()->withTrashed()->count() > 0) {
            return redirect()->route('categories.trash')
                ->with('error', 'Impossible de supprimer définitivement cette catégorie car elle est associée à des équipements.');
        }
        
        // Vérifier si la catégorie a des sous-catégories
        if ($category->subcategories()->withTrashed()->count() > 0) {
            return redirect()->route('categories.trash')
                ->with('error', 'Impossible de supprimer définitivement cette catégorie car elle contient des sous-catégories.');
        }
        
        $category->forceDelete();
        
        return redirect()->route('categories.trash')
            ->with('success', 'Catégorie supprimée définitivement.');
    }
    
    /**
     * Restore all categories from trash.
     */
    public function restoreAll()
    {
        Category::onlyTrashed()->restore();
        
        return redirect()->route('categories.trash')
            ->with('success', 'Toutes les catégories ont été restaurées.');
    }
    
    /**
     * Empty the trash (permanently delete all).
     */
    public function emptyTrash()
    {
        $categories = Category::onlyTrashed()->get();
        
        foreach ($categories as $category) {
            // Vérifier si la catégorie peut être supprimée
            if ($category->equipment()->withTrashed()->count() === 0 && 
                $category->subcategories()->withTrashed()->count() === 0) {
                $category->forceDelete();
            }
        }
        
        return redirect()->route('categories.trash')
            ->with('success', 'Corbeille vidée (catégories supprimables supprimées).');
    }
}