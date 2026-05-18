@php
    $n = $equipment->niveau_renouvellement;
    $styles = [
        'recent' => ['pill' => 'bg-emerald-50 text-emerald-900 border-emerald-200', 'dot' => 'bg-emerald-500'],
        'seuil_reference' => ['pill' => 'bg-orange-50 text-orange-950 border-orange-300', 'dot' => 'bg-orange-500'],
        'a_remplacer' => ['pill' => 'bg-red-50 text-red-900 border-red-300', 'dot' => 'bg-red-600'],
        'inconnu' => ['pill' => 'bg-gray-50 text-gray-700 border-gray-200', 'dot' => 'bg-gray-400'],
    ];
    $s = $styles[$n] ?? $styles['inconnu'];
@endphp
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $s['pill'] }}" title="{{ $equipment->libelleRenouvellementLong() }}">
    <span class="w-2 h-2 rounded-full shrink-0 {{ $s['dot'] }}" aria-hidden="true"></span>
    {{ $equipment->libelleRenouvellementCourt() }}
</span>
