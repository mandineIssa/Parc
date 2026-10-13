<?php

namespace App\Http\Controllers;

use App\Models\Filiale;
use Illuminate\Http\Request;

class FilialeController extends Controller
{
    public function index()
    {
        $filiales = Filiale::query()
            ->withCount(['agencies', 'users'])
            ->orderBy('nom')
            ->paginate(20);

        return view('filiales.index', compact('filiales'));
    }

    public function create()
    {
        return view('filiales.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100', 'unique:filiales,nom'],
            'actif' => ['sometimes', 'boolean'],
        ]);

        $validated['actif'] = $request->boolean('actif', true);

        Filiale::create($validated);

        return redirect()->route('filiales.index')
            ->with('success', 'Filiale créée avec succès.');
    }

    public function edit(Filiale $filiale)
    {
        return view('filiales.edit', compact('filiale'));
    }

    public function update(Request $request, Filiale $filiale)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100', 'unique:filiales,nom,'.$filiale->id],
            'actif' => ['sometimes', 'boolean'],
        ]);

        $validated['actif'] = $request->boolean('actif');

        $filiale->update($validated);

        return redirect()->route('filiales.index')
            ->with('success', 'Filiale mise à jour avec succès.');
    }

    public function destroy(Filiale $filiale)
    {
        if ($filiale->agencies()->exists() || $filiale->users()->exists()) {
            return redirect()->route('filiales.index')
                ->with('error', 'Impossible de supprimer cette filiale : des agences ou des utilisateurs y sont rattachés.');
        }

        $filiale->delete();

        return redirect()->route('filiales.index')
            ->with('success', 'Filiale supprimée avec succès.');
    }
}
