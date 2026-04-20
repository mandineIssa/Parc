<?php
// app/Http/Controllers/NetworkAddressController.php

namespace App\Http\Controllers;

use App\Models\NetworkAddress;
use Illuminate\Http\Request;

class NetworkAddressController extends Controller
{
    

    public function index(Request $request)
    {
        $query = NetworkAddress::query()
            ->when($request->site, fn($q, $s) => $q->where('site', $s))
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->when($request->search, fn($q, $s) =>
                $q->where('vlan', 'like', "%$s%")
                  ->orWhere('adresse_reseau', 'like', "%$s%")
                  ->orWhere('adresse_ip', 'like', "%$s%")
                  ->orWhere('equipement_reseau', 'like', "%$s%")
            )
            ->latest();

        $addresses   = $query->paginate(20)->withQueryString();
        $sites       = NetworkAddress::sites();
        $statsBySite = NetworkAddress::selectRaw('site, count(*) as total')
                          ->groupBy('site')->pluck('total', 'site');

        return view('network.index', compact('addresses', 'sites', 'statsBySite'));
    }

    public function create()
    {
        $sites         = NetworkAddress::sites();
        $typesEquip    = NetworkAddress::typesEquipement();
        $typesCable    = NetworkAddress::typesCable();
        return view('network.create', compact('sites', 'typesEquip', 'typesCable'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'site'                => 'required|string',
            'type'                => 'required|in:plan_adressage,branchement_local',
            'vlan'                => 'nullable|string',
            'adresse_reseau'      => 'nullable|string',
            'masque'              => 'nullable|string',
            'adresse_dhcp'        => 'nullable|string',
            'default_gateway'     => 'nullable|string',
            'numero'              => 'nullable|integer',
            'equipement_reseau'   => 'nullable|string',
            'type_equipement'     => 'nullable|string',
            'adresse_ip'          => 'nullable|string',
            'type_port'           => 'nullable|string',
            'port_reseau'         => 'nullable|string',
            'vlan_port'           => 'nullable|string',
            'emplacement'         => 'nullable|string',
            'equipement_connecte' => 'nullable|string',
            'type_cable'          => 'nullable|string',
            'adresse_ip_connecte' => 'nullable|string',
            'commentaires'        => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        NetworkAddress::create($validated);

        return redirect()->route('network.index')
            ->with('success', 'Entrée ajoutée au plan d\'adressage.');
    }

    public function edit(NetworkAddress $network)
    {
        $sites      = NetworkAddress::sites();
        $typesEquip = NetworkAddress::typesEquipement();
        $typesCable = NetworkAddress::typesCable();
        return view('network.edit', compact('network', 'sites', 'typesEquip', 'typesCable'));
    }

    public function update(Request $request, NetworkAddress $network)
    {
        $validated = $request->validate([
            'site'                => 'required|string',
            'type'                => 'required|in:plan_adressage,branchement_local',
            'vlan'                => 'nullable|string',
            'adresse_reseau'      => 'nullable|string',
            'masque'              => 'nullable|string',
            'adresse_dhcp'        => 'nullable|string',
            'default_gateway'     => 'nullable|string',
            'numero'              => 'nullable|integer',
            'equipement_reseau'   => 'nullable|string',
            'type_equipement'     => 'nullable|string',
            'adresse_ip'          => 'nullable|string',
            'emplacement'         => 'nullable|string',
            'equipement_connecte' => 'nullable|string',
            'type_cable'          => 'nullable|string',
            'commentaires'        => 'nullable|string',
        ]);

        $network->update($validated);
        return redirect()->route('network.index')->with('success', 'Entrée mise à jour.');
    }

    public function destroy(NetworkAddress $network)
    {
        $network->delete();
        return redirect()->route('network.index')->with('success', 'Entrée supprimée.');
    }
}