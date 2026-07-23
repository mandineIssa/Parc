<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class StockApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::query()
            ->where('statut', 'stock')
            ->with(['fournisseur', 'agence', 'detail']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = '%'.$request->search.'%';
            $query->where(function ($q) use ($s) {
                $q->where('numero_serie', 'like', $s)->orWhere('nom', 'like', $s);
            });
        }

        return response()->json($query->paginate(min(100, (int) $request->input('per_page', 25))));
    }
}
