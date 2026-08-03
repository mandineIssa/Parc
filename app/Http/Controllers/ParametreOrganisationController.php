<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\PosteOrganisation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParametreOrganisationController extends Controller
{
    public function index()
    {
        $departements = Departement::ordered()->get();
        $postes = PosteOrganisation::ordered()->get();

        return view('parametres.organisation', compact('departements', 'postes'));
    }

    public function storeDepartement(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100|unique:departements,nom',
            'ordre' => 'nullable|integer|min:0',
        ]);

        Departement::create([
            'nom' => trim($validated['nom']),
            'ordre' => $validated['ordre'] ?? ((int) Departement::max('ordre') + 1),
            'actif' => true,
        ]);

        return back()->with('success', 'Département ajouté.');
    }

    public function updateDepartement(Request $request, Departement $departement)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100', Rule::unique('departements', 'nom')->ignore($departement->id)],
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);

        $departement->update([
            'nom' => trim($validated['nom']),
            'ordre' => $validated['ordre'] ?? $departement->ordre,
            'actif' => $request->boolean('actif', $departement->actif),
        ]);

        return back()->with('success', 'Département mis à jour.');
    }

    public function toggleDepartement(Departement $departement)
    {
        $departement->update(['actif' => ! $departement->actif]);

        return back()->with('success', $departement->actif ? 'Département activé.' : 'Département désactivé.');
    }

    public function destroyDepartement(Departement $departement)
    {
        $departement->delete();

        return back()->with('success', 'Département supprimé.');
    }

    public function storePoste(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100|unique:postes_organisation,nom',
            'ordre' => 'nullable|integer|min:0',
        ]);

        PosteOrganisation::create([
            'nom' => trim($validated['nom']),
            'ordre' => $validated['ordre'] ?? ((int) PosteOrganisation::max('ordre') + 1),
            'actif' => true,
        ]);

        return back()->with('success', 'Poste ajouté.');
    }

    public function updatePoste(Request $request, PosteOrganisation $posteOrganisation)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100', Rule::unique('postes_organisation', 'nom')->ignore($posteOrganisation->id)],
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);

        $posteOrganisation->update([
            'nom' => trim($validated['nom']),
            'ordre' => $validated['ordre'] ?? $posteOrganisation->ordre,
            'actif' => $request->boolean('actif', $posteOrganisation->actif),
        ]);

        return back()->with('success', 'Poste mis à jour.');
    }

    public function togglePoste(PosteOrganisation $posteOrganisation)
    {
        $posteOrganisation->update(['actif' => ! $posteOrganisation->actif]);

        return back()->with('success', $posteOrganisation->actif ? 'Poste activé.' : 'Poste désactivé.');
    }

    public function destroyPoste(PosteOrganisation $posteOrganisation)
    {
        $posteOrganisation->delete();

        return back()->with('success', 'Poste supprimé.');
    }
}
