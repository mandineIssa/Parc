<?php
// ═══════════════════════════════════════════════════════════════════════════
// app/Http/Controllers/LicenceController.php



namespace App\Http\Controllers;

use App\Models\Licence;
use Illuminate\Http\Request;

class LicenceController extends Controller
{
    

    public function index(Request $request)
    {
        $query = Licence::query()
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->when($request->statut, fn($q, $s) => $q->where('statut', $s))
            ->when($request->search, fn($q, $s) =>
                $q->where('nom', 'like', "%$s%")
                  ->orWhere('site_agence', 'like', "%$s%")
                  ->orWhere('numero_serie', 'like', "%$s%")
            )
            ->latest();

        $licences    = $query->paginate(20)->withQueryString();
        $types       = Licence::types();
        $statuts     = Licence::statuts();
        $expiresSoon = Licence::expiresSoon(30)->count();
        $statsByType = Licence::selectRaw('type, count(*) as total')
                          ->groupBy('type')->pluck('total', 'type');

        return view('licences.index', compact(
            'licences', 'types', 'statuts', 'expiresSoon', 'statsByType'
        ));
    }

    public function create()
    {
        $types   = Licence::types();
        $statuts = Licence::statuts();
        $sites   = ['AGP', 'TOUBA', 'TAMBA', 'ZIG', 'PIKINE', 'DAKAR', 'AUTRE'];
        return view('licences.create', compact('types', 'statuts', 'sites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'                 => 'required|string',
            'nom'                  => 'required|string|max:255',
            'site_agence'          => 'nullable|string',
            'statut'               => 'required|string',
            'date_activation'      => 'nullable|date',
            'date_expiration'      => 'nullable|date',
            'date_mise_en_service' => 'nullable|date',
            'echeance_contrat'     => 'nullable|date',
            'renouvellement_prevu' => 'boolean',
            'observation'          => 'nullable|string',
            // Fortinet
            'modele'               => 'nullable|string',
            'numero_serie'         => 'nullable|string',
            'type_licence'         => 'nullable|string',
            'prix_achat'           => 'nullable|numeric',
            // FAI
            'fournisseur'          => 'nullable|string',
            'numero_client'        => 'nullable|string',
            'type_ligne'           => 'nullable|string',
            'ip_publique'          => 'nullable|string',
            'debit'                => 'nullable|string',
            'montant_mensuel'      => 'nullable|numeric',
            // Certificat
            'environnement'        => 'nullable|string',
            'emplacement'          => 'nullable|string',
            'port'                 => 'nullable|integer',
            // O365
            'utilisateur'          => 'nullable|string',
            'departement'          => 'nullable|string',
            'email'                => 'nullable|email',
            // Contact
            'contact_nom'          => 'nullable|string',
            'contact_email'        => 'nullable|email',
            'contact_tel'          => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        Licence::create($validated);

        return redirect()->route('licences.index')
            ->with('success', 'Licence ajoutée avec succès.');
    }

    public function edit(Licence $licence)
    {
        $types   = Licence::types();
        $statuts = Licence::statuts();
        $sites   = ['AGP', 'TOUBA', 'TAMBA', 'ZIG', 'PIKINE', 'DAKAR', 'AUTRE'];
        return view('licences.edit', compact('licence', 'types', 'statuts', 'sites'));
    }

    public function update(Request $request, Licence $licence)
    {
        $validated = $request->validate([
            'type'            => 'required|string',
            'nom'             => 'required|string|max:255',
            'statut'          => 'required|string',
            'date_expiration' => 'nullable|date',
            'observation'     => 'nullable|string',
        ]);

        $licence->update(array_merge($validated, $request->except(['_token','_method'])));
        return redirect()->route('licences.index')->with('success', 'Licence mise à jour.');
    }

    public function destroy(Licence $licence)
    {
        $licence->delete();
        return redirect()->route('licences.index')->with('success', 'Licence supprimée.');
    }
}
