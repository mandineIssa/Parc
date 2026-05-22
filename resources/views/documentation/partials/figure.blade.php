@props([
    'id',
    'alt' => '',
    'caption' => '',
])
@php
    $filename = $id . '.png';
    $publicPath = public_path('doc-captures/' . $filename);
    // Chemin relatif : évite APP_URL=localhost sans port (images cassées sur 127.0.0.1:8000)
    $url = url('doc-captures/' . $filename);
    $fileExists = file_exists($publicPath) && filesize($publicPath) > 500;
    $version = $fileExists ? filemtime($publicPath) : time();
@endphp
<figure class="doc-figure my-6" id="capture-{{ $id }}">
    @if ($fileExists)
        <img src="{{ $url }}?v={{ $version }}" alt="{{ $alt ?: $caption }}" class="rounded-lg border border-gray-200 shadow-md max-w-full mx-auto block" loading="lazy">
    @else
        <div class="doc-capture-placeholder rounded-lg border-2 border-dashed border-[#A61B29]/40 bg-[#fdf2f3] p-6 text-center">
            <i class="fas fa-image text-3xl text-[#A61B29]/50 mb-2"></i>
            <p class="text-sm font-medium text-gray-700">Capture à insérer : <code class="text-xs">{{ $filename }}</code></p>
            @if ($caption)
                <p class="text-xs text-gray-500 mt-2 max-w-lg mx-auto">{{ $caption }}</p>
            @endif
            <p class="text-xs text-gray-400 mt-3">Enregistrez la capture dans <code>public/doc-captures/</code></p>
        </div>
    @endif
    @if ($caption)
        <figcaption class="text-sm text-gray-600 mt-2 text-center italic">{{ $caption }}</figcaption>
    @endif
</figure>
