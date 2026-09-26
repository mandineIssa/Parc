{{-- Affiche la valeur, ou un lien N/A vers le formulaire d'édition --}}
@php
    $empty = ! filled($value ?? null);
@endphp
@if(! $empty)
    <span class="font-medium">{{ $value }}</span>
@else
    <a href="{{ route('equipment.edit', $equipment) }}#{{ $field }}"
       class="inline-flex items-center gap-1.5 text-amber-700 italic font-medium hover:text-[#C8102E] hover:underline"
       title="Compléter cette information">
        N/A
        <span class="not-italic text-[11px] font-semibold bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded">Modifier</span>
    </a>
@endif
