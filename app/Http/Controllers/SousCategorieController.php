<?php
// ============================================
// 3. CONTROLLER - app/Http/Controllers/SousCategorieController.php
// ============================================

namespace App\Http\Controllers;
use App\Models\SousCategorie;
use App\Models\Category;
use Illuminate\Http\Request;

class SousCategorieController extends Controller
{
    public function index()
    {
        $sousCategories = SousCategorie::with('categorie')->paginate(15);
        return view('sous-categories.index', compact('sousCategories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('sous-categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categorie_id' => 'required|exists:categories,id',
            'nom' => 'required|string|max:255|unique:sous_categories',
            'description' => 'nullable|string',
        ]);

        SousCategorie::create($validated);
        return redirect()->route('sous-categories.index')->with('success', 'Sous-catégorie créée avec succès.');
    }

    public function edit(SousCategorie $sousCategorie)
    {
        $categories = Category::all();
        return view('sous-categories.edit', compact('sousCategorie', 'categories'));
    }

    public function update(Request $request, SousCategorie $sousCategorie)
    {
        $validated = $request->validate([
            'categorie_id' => 'required|exists:categories,id',
            'nom' => 'required|string|max:255|unique:sous_categories,nom,' . $sousCategorie->id,
            'description' => 'nullable|string',
        ]);

        $sousCategorie->update($validated);
        return redirect()->route('sous-categories.index')->with('success', 'Sous-catégorie mise à jour avec succès.');
    }

    public function destroy(SousCategorie $sousCategorie)
    {
        $sousCategorie->delete();
        return redirect()->route('sous-categories.index')->with('success', 'Sous-catégorie supprimée avec succès.');
    }

    public function show(SousCategorie $sousCategorie)
    {
        return view('sous-categories.show', compact('sousCategorie'));
    }
}
