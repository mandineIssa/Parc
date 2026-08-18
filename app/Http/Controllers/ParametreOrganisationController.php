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
        return redirect()->route('parametres.departements.index');
    }

    public function indexDepartements(Request $request)
    {
        $perPage = $this->perPage($request);
        $departements = Departement::ordered()->paginate($perPage)->withQueryString();

        return view('parametres.departements.index', compact('departements', 'perPage'));
    }

    public function createDepartement()
    {
        return view('parametres.departements.create');
    }

    public function showDepartement(Departement $departement)
    {
        return view('parametres.departements.show', compact('departement'));
    }

    public function editDepartement(Departement $departement)
    {
        return view('parametres.departements.edit', compact('departement'));
    }

    public function storeDepartement(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100|unique:departements,nom',
            'description' => 'nullable|string|max:255',
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);

        $nom = trim($validated['nom']);
        Departement::create([
            'nom' => $nom,
            'description' => trim((string) ($validated['description'] ?? '')) ?: ('Direction '.mb_strtolower($nom)),
            'ordre' => $validated['ordre'] ?? ((int) Departement::max('ordre') + 1),
            'actif' => $request->boolean('actif', true),
        ]);

        return redirect()->route('parametres.departements.index')->with('success', 'Département ajouté.');
    }

    public function updateDepartement(Request $request, Departement $departement)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100', Rule::unique('departements', 'nom')->ignore($departement->id)],
            'description' => 'nullable|string|max:255',
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);

        $departement->update([
            'nom' => trim($validated['nom']),
            'description' => trim((string) ($validated['description'] ?? '')) ?: $departement->description,
            'ordre' => $validated['ordre'] ?? $departement->ordre,
            'actif' => $request->boolean('actif', $departement->actif),
        ]);

        return redirect()->route('parametres.departements.index')->with('success', 'Département mis à jour.');
    }

    public function toggleDepartement(Departement $departement)
    {
        $departement->update(['actif' => ! $departement->actif]);

        return back()->with('success', $departement->actif ? 'Département activé.' : 'Département désactivé.');
    }

    public function destroyDepartement(Departement $departement)
    {
        $departement->delete();

        return redirect()->route('parametres.departements.index')->with('success', 'Département supprimé.');
    }

    public function indexPostes(Request $request)
    {
        $perPage = $this->perPage($request);
        $postes = PosteOrganisation::ordered()->paginate($perPage)->withQueryString();

        return view('parametres.postes.index', compact('postes', 'perPage'));
    }

    public function createPoste()
    {
        return view('parametres.postes.create');
    }

    public function showPoste(PosteOrganisation $posteOrganisation)
    {
        return view('parametres.postes.show', ['poste' => $posteOrganisation]);
    }

    public function editPoste(PosteOrganisation $posteOrganisation)
    {
        return view('parametres.postes.edit', ['poste' => $posteOrganisation]);
    }

    public function storePoste(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100|unique:postes_organisation,nom',
            'description' => 'nullable|string|max:255',
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);

        $nom = trim($validated['nom']);
        PosteOrganisation::create([
            'nom' => $nom,
            'description' => trim((string) ($validated['description'] ?? '')) ?: $nom,
            'ordre' => $validated['ordre'] ?? ((int) PosteOrganisation::max('ordre') + 1),
            'actif' => $request->boolean('actif', true),
        ]);

        return redirect()->route('parametres.postes.index')->with('success', 'Poste ajouté.');
    }

    public function updatePoste(Request $request, PosteOrganisation $posteOrganisation)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100', Rule::unique('postes_organisation', 'nom')->ignore($posteOrganisation->id)],
            'description' => 'nullable|string|max:255',
            'ordre' => 'nullable|integer|min:0',
            'actif' => 'nullable|boolean',
        ]);

        $posteOrganisation->update([
            'nom' => trim($validated['nom']),
            'description' => trim((string) ($validated['description'] ?? '')) ?: $posteOrganisation->description,
            'ordre' => $validated['ordre'] ?? $posteOrganisation->ordre,
            'actif' => $request->boolean('actif', $posteOrganisation->actif),
        ]);

        return redirect()->route('parametres.postes.index')->with('success', 'Poste mis à jour.');
    }

    public function togglePoste(PosteOrganisation $posteOrganisation)
    {
        $posteOrganisation->update(['actif' => ! $posteOrganisation->actif]);

        return back()->with('success', $posteOrganisation->actif ? 'Poste activé.' : 'Poste désactivé.');
    }

    public function destroyPoste(PosteOrganisation $posteOrganisation)
    {
        $posteOrganisation->delete();

        return redirect()->route('parametres.postes.index')->with('success', 'Poste supprimé.');
    }

    private function perPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 5);

        return in_array($perPage, [5, 10, 15, 25], true) ? $perPage : 5;
    }
}
