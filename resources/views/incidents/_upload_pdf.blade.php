{{-- resources/views/incidents/_upload_pdf.blade.php --}}
@php
    $colorClasses = [
        'blue' => ['btn' => 'bg-blue-600 hover:bg-blue-700', 'text' => 'text-blue-600'],
        'orange' => ['btn' => 'bg-orange-500 hover:bg-orange-600', 'text' => 'text-orange-600'],
        'purple' => ['btn' => 'bg-purple-600 hover:bg-purple-700', 'text' => 'text-purple-600'],
    ];
    $c = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div x-data="{ fileName: null }" class="space-y-2">
    <p class="text-xs font-semibold text-gray-600">
        📎 {{ $label }}
        @if($currentPath)
        <a href="{{ Storage::url($currentPath) }}" target="_blank"
           class="ml-2 {{ $c['text'] }} underline text-[11px]">Voir le PDF actuel</a>
        @endif
    </p>

    <form method="POST" action="{{ route('incidents.upload-pdf', $incident) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="niveau" value="{{ $niveau }}">

        <div class="border-2 border-dashed border-gray-300 bg-gray-50 rounded-lg p-4 text-center transition-all cursor-pointer hover:border-gray-400"
            onclick="document.getElementById('pdf_input_{{ $niveau }}').click()">
            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <p class="text-xs text-gray-500" x-text="fileName ?? 'Cliquez pour sélectionner un PDF'"></p>
            <p class="text-[10px] text-gray-400 mt-1">PDF uniquement · Max 10 Mo</p>

            <input type="file" name="pdf" accept="application/pdf" class="hidden"
                id="pdf_input_{{ $niveau }}"
                @change="fileName = $event.target.files[0]?.name">
        </div>

        <button type="submit"
            class="mt-2 w-full px-3 py-2 {{ $c['btn'] }} text-white text-xs font-semibold rounded-lg transition-colors">
            Uploader le PDF
        </button>
    </form>
</div>