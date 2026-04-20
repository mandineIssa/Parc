{{-- resources/views/network/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Modifier — Entrée réseau')

@section('content')
<div class="p-6 max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="{{ route('network.index') }}" class="text-gray-500 hover:text-blue-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-800">Modifier une entrée réseau</h1>
            <p class="text-sm text-gray-500">{{ $network->site }} · {{ $network->type === 'plan_adressage' ? "Plan d'adressage" : 'Branchement local' }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-600">
        @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('network.update', $network) }}" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Identification --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Identification</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site <span class="text-red-500">*</span></label>
                    <select name="site" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($sites as $s)
                            <option value="{{ $s }}" @selected(old('site', $network->site) === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" required id="type-select" onchange="toggleSections()"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="plan_adressage"    @selected(old('type', $network->type) === 'plan_adressage')>Plan d'adressage</option>
                        <option value="branchement_local" @selected(old('type', $network->type) === 'branchement_local')>Branchement local</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Plan d'adressage --}}
        <div id="section-plan" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Plan d'adressage VLAN</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach([
                    'vlan'            => 'VLAN',
                    'adresse_reseau'  => 'Adresse réseau',
                    'masque'          => 'Masque sous-réseau',
                    'adresse_exclue'  => 'Adresse exclue',
                    'adresse_dhcp'    => 'Plage DHCP',
                    'default_gateway' => 'Default Gateway',
                ] as $name => $label)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                    <input type="text" name="{{ $name }}"
                           value="{{ old($name, $network->$name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @endforeach
            </div>
        </div>

        {{-- Branchement local --}}
        <div id="section-branchement" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Branchement local</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N°</label>
                    <input type="number" name="numero" value="{{ old('numero', $network->numero) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Équipement réseau</label>
                    <input type="text" name="equipement_reseau" value="{{ old('equipement_reseau', $network->equipement_reseau) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type d'équipement</label>
                    <select name="type_equipement" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">—</option>
                        @foreach($typesEquip as $t)
                            <option value="{{ $t }}" @selected(old('type_equipement', $network->type_equipement) === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse IP</label>
                    <input type="text" name="adresse_ip" value="{{ old('adresse_ip', $network->adresse_ip) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Port réseau</label>
                    <input type="text" name="port_reseau" value="{{ old('port_reseau', $network->port_reseau) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">VLAN port</label>
                    <input type="text" name="vlan_port" value="{{ old('vlan_port', $network->vlan_port) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Emplacement (Baie/Rack)</label>
                    <input type="text" name="emplacement" value="{{ old('emplacement', $network->emplacement) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Équipement connecté</label>
                    <input type="text" name="equipement_connecte" value="{{ old('equipement_connecte', $network->equipement_connecte) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type de câble</label>
                    <select name="type_cable" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">—</option>
                        @foreach($typesCable as $c)
                            <option value="{{ $c }}" @selected(old('type_cable', $network->type_cable) === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">IP équipement connecté</label>
                    <input type="text" name="adresse_ip_connecte" value="{{ old('adresse_ip_connecte', $network->adresse_ip_connecte) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Commentaires</label>
                    <input type="text" name="commentaires" value="{{ old('commentaires', $network->commentaires) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- Danger zone --}}
        <div class="bg-red-50 rounded-xl border border-red-200 p-5">
            <h2 class="text-sm font-semibold text-red-700 mb-2">Zone dangereuse</h2>
            <form method="POST" action="{{ route('network.destroy', $network) }}"
                  onsubmit="return confirm('Supprimer cette entrée ?')">
                @csrf @method('DELETE')
                <button type="submit" class="border border-red-400 text-red-700 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-medium transition">
                    Supprimer cette entrée
                </button>
            </form>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('network.index') }}"
               class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                Annuler
            </a>
            <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

<script>
function toggleSections() {
    const v = document.getElementById('type-select').value;
    document.getElementById('section-plan').classList.toggle('hidden', v !== 'plan_adressage');
    document.getElementById('section-branchement').classList.toggle('hidden', v !== 'branchement_local');
}
document.addEventListener('DOMContentLoaded', toggleSections);
</script>
@endsection
