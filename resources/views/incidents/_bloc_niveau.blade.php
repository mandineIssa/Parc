{{-- resources/views/incidents/_bloc_niveau.blade.php --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden {{ $inactive ? 'opacity-50' : '' }}">

    <div class="{{ $bgColor }} px-5 py-3 flex items-center justify-between">
        <h2 class="text-white font-bold text-xs uppercase tracking-wider">{{ $label }}</h2>
        @if($pdfPath)
        <a href="{{ Storage::url($pdfPath) }}" target="_blank"
           class="inline-flex items-center gap-1 px-2.5 py-1 bg-white/20 hover:bg-white/30 text-white text-xs font-semibold rounded-lg transition-colors">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            PDF joint
        </a>
        @endif
    </div>

    <div class="p-5">

        @if($alreadyDone)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase mb-1.5">Description du traitement</p>
                <div class="{{ $inputBg }} rounded-lg p-3 text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $description ?: '—' }}</div>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase mb-1.5">Solutions envisagées</p>
                <div class="{{ $inputBg }} rounded-lg p-3 text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $solutions ?: '—' }}</div>
            </div>
        </div>
        <div class="flex flex-wrap gap-x-5 gap-y-1 text-xs text-gray-500">
            <span>Responsable : <strong class="text-gray-800">{{ $user?->name ?? '—' }}</strong></span>
            @if($intervenants)
            <span>Intervenants : <strong class="text-gray-800">{{ $intervenants }}</strong></span>
            @endif
            @if($date)
            <span>Date : <strong class="text-gray-800">{{ \Carbon\Carbon::parse($date)->format('d/m/Y H:i') }}</strong></span>
            @endif
        </div>

        @elseif($isActive && $canTrait)
        <form method="POST" action="{{ route($formRoute, $incident) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Description du traitement <span class="text-red-500">*</span></label>
                    <textarea name="{{ $fieldPrefix }}_description_traitement" rows="4" required
                        placeholder="Actions effectuées, vérifications..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm {{ $ringColor }} focus:outline-none focus:ring-2 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Solutions envisagées <span class="text-red-500">*</span></label>
                    <textarea name="{{ $fieldPrefix }}_solutions_envisagees" rows="4" required
                        placeholder="Solutions identifiées, correctifs appliqués..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm {{ $ringColor }} focus:outline-none focus:ring-2 resize-none"></textarea>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Autres intervenants</label>
                <input type="text" name="{{ $fieldPrefix }}_autres_intervenants"
                    placeholder="Noms des personnes impliquées..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm {{ $ringColor }} focus:outline-none focus:ring-2">
            </div>
            <div class="flex flex-wrap items-center gap-4 pt-1">
                <span class="text-xs font-bold text-gray-600">Décision :</span>
                @foreach($statutOptions as $val => $optLabel)
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="radio" name="{{ $statutField }}" value="{{ $val }}"
                        {{ $loop->first ? 'checked' : '' }}
                        class="w-4 h-4 text-{{ $color }}-600 border-gray-300">
                    <span class="text-sm font-semibold text-{{ $color }}-700">{{ $optLabel }}</span>
                </label>
                @endforeach
                <button type="submit"
                    class="ml-auto px-5 py-2 {{ $bgColor }} hover:opacity-90 text-white text-xs font-bold rounded-lg transition-opacity shadow-sm">
                    Valider le traitement
                </button>
            </div>
        </form>

        @else
        <p class="text-sm text-gray-400 italic py-2">
            @if($inactive)
                En attente des niveaux précédents.
            @elseif($isActive && !$canTrait)
                Votre rôle ne vous permet pas de traiter ce niveau.
            @else
                En attente de traitement.
            @endif
        </p>
        @endif

        @if($canUpload && !$inactive)
        <div class="mt-4 pt-4 border-t border-gray-100">
            @include('incidents._upload_pdf', [
                'niveau' => $niveau,
                'label' => 'Joindre un PDF signé',
                'color' => $color,
                'currentPath' => $pdfPath,
                'incident' => $incident,
            ])
        </div>
        @endif

    </div>
</div>