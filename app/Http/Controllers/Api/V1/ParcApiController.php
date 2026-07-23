<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class ParcApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::query()
            ->where('statut', 'parc')
            ->with(['fournisseur', 'agence', 'parc', 'detail']);

        if ($request->filled('search')) {
            $s = '%'.$request->search.'%';
            $query->where(function ($q) use ($s) {
                $q->where('numero_serie', 'like', $s)
                    ->orWhere('nom', 'like', $s)
                    ->orWhere('marque', 'like', $s);
            });
        }

        return response()->json($query->paginate(min(100, (int) $request->input('per_page', 25))));
    }

    public function show(string $numeroSerie)
    {
        $equipment = Equipment::query()
            ->where('statut', 'parc')
            ->where('numero_serie', $numeroSerie)
            ->with(['fournisseur', 'agence', 'parc', 'detail'])
            ->firstOrFail();

        return response()->json($equipment);
    }
}
