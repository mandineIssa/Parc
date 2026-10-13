<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Filiale;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    /**
     * Display a listing of the agencies.
     */
    public function index()
    {
        $agencies = Agency::with('filiale')->orderBy('nom')->paginate(20);
        return view('agencies.index', compact('agencies'));
    }

    /**
     * Show the form for creating a new agency.
     */
    public function create()
    {
        return view('agencies.create', $this->formData());
    }

    /**
     * Store a newly created agency in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $validated = $this->constrainFiliale($validated);

        Agency::create($validated);

        return redirect()->route('agencies.index')
            ->with('success', 'Agence créée avec succès.');
    }

    /**
     * Display the specified agency.
     */
    public function show(Agency $agency)
    {
        $agency->loadMissing('filiale');

        return view('agencies.show', compact('agency'));
    }

    /**
     * Show the form for editing the specified agency.
     */
    public function edit(Agency $agency)
    {
        return view('agencies.edit', array_merge(compact('agency'), $this->formData()));
    }

    /**
     * Update the specified agency in storage.
     */
    public function update(Request $request, Agency $agency)
    {
        $validated = $request->validate($this->rules($agency->id));
        $validated = $this->constrainFiliale($validated);

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

    /**
     * @return array<string, mixed>
     */
    private function rules(?int $agencyId = null): array
    {
        $codeRule = $agencyId
            ? 'required|string|max:10|unique:agencies,code,'.$agencyId
            : 'required|string|max:10|unique:agencies';

        $filialeRule = auth()->user()->isFilialeRestricted()
            ? ['nullable', 'exists:filiales,id']
            : ['required', 'exists:filiales,id'];

        return [
            'code' => $codeRule,
            'nom' => 'required|string|max:100',
            'ville' => 'required|string|max:50',
            'adresse' => 'nullable|string|max:200',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'filiale_id' => $filialeRule,
        ];
    }

    /**
     * @return array{filiales: \Illuminate\Support\Collection, lockFiliale: bool, lockedFilialeId: int|null}
     */
    private function formData(): array
    {
        $user = auth()->user();

        return [
            'filiales' => Filiale::query()->where('actif', true)->orderBy('nom')->get(['id', 'nom']),
            'lockFiliale' => $user->isFilialeRestricted(),
            'lockedFilialeId' => $user->scopedFilialeId(),
        ];
    }

    private function constrainFiliale(array $validated): array
    {
        $scoped = auth()->user()->scopedFilialeId();
        if ($scoped !== null) {
            if ($scoped === 0) {
                abort(403, 'Votre compte n’est rattaché à aucune filiale.');
            }
            $validated['filiale_id'] = $scoped;
        }

        return $validated;
    }
}