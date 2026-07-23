@extends('layouts.app')

@section('title', 'Audit — '.$poste->hostname)

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">
    <x-page-header title="{{ $poste->hostname }}" subtitle="N° série {{ $poste->numero_serie }}">
        <a href="{{ route('audits-postes.index') }}"
           class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2.5 px-5 rounded-lg transition">
            Retour à la liste
        </a>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">État courant</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">Utilisateur de session</dt>
                    <dd class="font-medium text-gray-900">{{ $poste->utilisateur_session ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Dernier audit</dt>
                    <dd class="font-medium text-gray-900">{{ optional($poste->date_audit)->format('d/m/Y H:i:s') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Fabricant / modèle</dt>
                    <dd class="font-medium text-gray-900">{{ $poste->fabricant }} — {{ $poste->modele }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">OS</dt>
                    <dd class="font-medium text-gray-900">{{ $poste->os }} ({{ $poste->version_os }})</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Antivirus Defender</dt>
                    <dd class="font-medium {{ $poste->antivirus_defender ? 'text-green-700' : 'text-red-700' }}">
                        {{ $poste->antivirus_defender ? 'Actif' : 'Inactif' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">BitLocker</dt>
                    <dd class="font-medium text-gray-900">{{ $poste->bitlocker }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Firewall</dt>
                    <dd class="font-medium text-gray-900">{{ $poste->firewall }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">USB stockage bloqué</dt>
                    <dd class="font-medium text-gray-900">{{ $poste->usb_stockage_bloque ? 'Oui' : 'Non' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Adresse IP</dt>
                    <dd class="font-medium text-gray-900">{{ $poste->adresse_ip }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Adresse MAC</dt>
                    <dd class="font-medium text-gray-900">{{ $poste->adresse_mac }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Historique utilisateurs</h2>
            @if(count($historiqueUtilisateurs) === 0)
                <p class="text-sm text-gray-500">Aucun historique.</p>
            @else
                <ul class="space-y-3 text-sm">
                    @foreach($historiqueUtilisateurs as $entry)
                        <li class="border-l-2 border-red-600 pl-3">
                            <div class="font-medium text-gray-900">{{ $entry['utilisateur_session'] ?? '—' }}</div>
                            <div class="text-xs text-gray-500">
                                {{ optional($entry['premiere_apparition'])->format('d/m/Y H:i') }}
                                →
                                {{ optional($entry['derniere_apparition'])->format('d/m/Y H:i') }}
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Historique des audits ({{ $poste->audits->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Date audit</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Utilisateur</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">IP</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Antivirus</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">BitLocker</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Firewall</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($poste->audits as $audit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ optional($audit->date_audit)->format('d/m/Y H:i:s') }}</td>
                            <td class="px-4 py-3">{{ $audit->utilisateur_session }}</td>
                            <td class="px-4 py-3">{{ $audit->adresse_ip }}</td>
                            <td class="px-4 py-3">{{ $audit->antivirus_defender ? 'Oui' : 'Non' }}</td>
                            <td class="px-4 py-3">{{ $audit->bitlocker }}</td>
                            <td class="px-4 py-3">{{ $audit->firewall }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
