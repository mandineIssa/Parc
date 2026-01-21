<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    /**
     * Display a listing of the agencies.
     */
    public function index()
    {
        $agencies = Agency::orderBy('nom')->paginate(20);
        return view('agencies.index', compact('agencies'));
    }

    /**
     * Show the form for creating a new agency.
     */
    public function create()
    {
        return view('agencies.create');
    }

    /**
     * Store a newly created agency in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:agencies',
            'nom' => 'required|string|max:100',
            'ville' => 'required|string|max:50',
            'adresse' => 'nullable|string|max:200',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        Agency::create($validated);

        return redirect()->route('agencies.index')
            ->with('success', 'Agence créée avec succès.');
    }

    /**
     * Display the specified agency.
     */
    public function show(Agency $agency)
    {
        return view('agencies.show', compact('agency'));
    }

    /**
     * Show the form for editing the specified agency.
     */
    public function edit(Agency $agency)
    {
        return view('agencies.edit', compact('agency'));
    }

    /**
     * Update the specified agency in storage.
     */
    public function update(Request $request, Agency $agency)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:agencies,code,' . $agency->id,
            'nom' => 'required|string|max:100',
            'ville' => 'required|string|max:50',
            'adresse' => 'nullable|string|max:200',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
        ]);

        $agency->update($validated);

        return redirect()->route('agencies.index')
            ->with('success', 'Agence mise à jour avec succès.');
    }

    /**
     * Remove the specified agency from storage.
     */
    public function destroy(Agency $agency)
    {
        // Vérifier si l'agence est utilisée dans des équipements
        if ($agency->equipments()->count() > 0) {
            return redirect()->route('agencies.index')
                ->with('error', 'Impossible de supprimer cette agence car elle est associée à des équipements.');
        }

        $agency->delete();

        return redirect()->route('agencies.index')
            ->with('success', 'Agence supprimée avec succès.');
    }
}