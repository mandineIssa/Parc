@extends('layouts.app')

@section('title', 'Signature Controller - ' . $fiche->reference)
@section('header', 'Validation Controller EOD')

@section('content')
@php
    $ctrlUser = auth()->user();
    $ctrlName = trim(($ctrlUser->prenom ?? '') . ' ' . ($ctrlUser->name ?? ''));
    $pendingDual = $fiche->status === 'PENDING_N3_CONTROLLER' && !$fiche->controller_validated_at;
    $legacyPending = $fiche->status === 'PENDING_CONTROLLER';
@endphp
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Validation Controller — {{ $fiche->reference }}</h1>
            <p class="text-sm text-gray-600 mt-1">Signataire connecté : <strong>{{ $ctrlName }}</strong></p>
            @if($pendingDual && !$fiche->n3_validated_at)
                <p class="text-sm text-amber-700 mt-2">La signature N+3 n’est pas encore enregistrée. Vous pouvez signer en parallèle ; la fiche sera clôturée lorsque les deux signatures seront complètes.</p>
            @elseif($pendingDual && $fiche->n3_validated_at)
                <p class="text-sm text-green-700 mt-2">N+3 a signé. Votre signature clôturera la fiche et activera le PDF.</p>
            @endif
        </div>
        <div class="flex items-center gap-2">
            @if(in_array($fiche->status, ['CLOSED', 'VALIDATED'], true))
                <a href="{{ route('eod.n2.pdf', $fiche) }}" target="_blank" class="px-4 py-2 bg-[#C8102E] hover:bg-[#a00d24] text-white rounded-lg text-sm font-semibold">Télécharger PDF</a>
            @endif
            <a href="{{ route('eod.controller.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold">Retour</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border rounded-lg p-4"><p class="text-xs text-gray-500">Date traitement</p><p class="font-semibold">{{ $fiche->date_traitement?->format('d/m/Y') }}</p></div>
        <div class="bg-white border rounded-lg p-4"><p class="text-xs text-gray-500">Heure début</p><p class="font-semibold">{{ $fiche->heure_lancement ?? '—' }}</p></div>
        <div class="bg-white border rounded-lg p-4"><p class="text-xs text-gray-500">Heure fin</p><p class="font-semibold">{{ $fiche->heure_fin ?? '—' }}</p></div>
        <div class="bg-white border rounded-lg p-4"><p class="text-xs text-gray-500">Statut</p><p><span class="px-2 py-1 rounded-full text-xs {{ $fiche->status_class }}">{{ $fiche->status_label }}</span></p></div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Résumé batch</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs text-gray-500">Batch</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500">Début</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500">Fin</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500">Observation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($batchData as $batch)
                        <tr>
                            <td class="px-3 py-2">{{ $batch['batch'] ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $batch['debut'] ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $batch['fin'] ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $batch['observation'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-3 py-4 text-center text-gray-500">Aucune ligne batch.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Signature Controller</h2>

        @if($legacyPending)
            <form method="POST" action="{{ route('eod.controller.sign', $fiche) }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Date</label>
                        <input type="text" name="controller_validation_date" value="{{ old('controller_validation_date', date('d/m/Y')) }}" class="w-full rounded-lg border-gray-300 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Visa / Signature (texte)</label>
                        <input type="text" name="controller_validation_visa" value="{{ old('controller_validation_visa') }}" class="w-full rounded-lg border-gray-300 text-sm" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-1">Note (optionnel)</label>
                    <textarea name="controller_validation_note" rows="3" class="w-full rounded-lg border-gray-300 text-sm">{{ old('controller_validation_note') }}</textarea>
                </div>
                <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold">Valider et signer (flux historique)</button>
            </form>
        @elseif($pendingDual)
            <form method="POST" action="{{ route('eod.controller.sign', $fiche) }}" enctype="multipart/form-data" id="controller-sign-form">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Date</label>
                        <input type="text" name="controller_validation_date" value="{{ old('controller_validation_date', date('d/m/Y')) }}" class="w-full rounded-lg border-gray-300 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Visa texte (si pas d’image)</label>
                        <input type="text" name="controller_validation_visa" value="{{ old('controller_validation_visa', $ctrlName) }}" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Optionnel si signature image">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-1">Importer signature (image)</label>
                    <input type="file" name="controller_signature_file" accept="image/*" capture="environment" class="block w-full text-sm text-gray-600">
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-1">Ou signer sur le canvas</label>
                    <div class="border border-gray-300 rounded-lg bg-white overflow-hidden max-w-lg">
                        <canvas id="ctrl-sig-canvas" width="480" height="160" class="w-full touch-none cursor-crosshair" style="max-height:160px;"></canvas>
                    </div>
                    <input type="hidden" name="controller_signature_canvas" id="controller_signature_canvas" value="">
                    <button type="button" id="ctrl-sig-clear" class="mt-2 px-3 py-1.5 text-xs bg-gray-200 rounded-lg">Effacer</button>
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-1">Note (optionnel)</label>
                    <textarea name="controller_validation_note" rows="3" class="w-full rounded-lg border-gray-300 text-sm">{{ old('controller_validation_note') }}</textarea>
                </div>
                <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold">Enregistrer la signature Controller</button>
            </form>
        @else
            <div class="text-sm text-gray-700 space-y-2">
                <p><strong>Signé par :</strong> {{ trim(($fiche->controllerValidator?->prenom ?? '') . ' ' . ($fiche->controllerValidator?->name ?? '')) ?: '—' }}</p>
                <p><strong>Date :</strong> {{ $fiche->controller_validation_date ?? '—' }}</p>
                @if($fiche->controller_signature_path)
                    <img src="{{ asset('storage/'.$fiche->controller_signature_path) }}" alt="Signature" class="max-h-28 rounded border border-gray-200">
                @else
                    <p><strong>Visa :</strong> {{ $fiche->controller_validation_visa ?? '—' }}</p>
                @endif
                @if($fiche->controller_validation_note)
                    <p><strong>Note :</strong> {{ $fiche->controller_validation_note }}</p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const canvas = document.getElementById('ctrl-sig-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let drawing = false;
    function pos(ev) {
        const r = canvas.getBoundingClientRect();
        const x = (ev.touches ? ev.touches[0].clientX : ev.clientX) - r.left;
        const y = (ev.touches ? ev.touches[0].clientY : ev.clientY) - r.top;
        return { x, y };
    }
    function start(ev) { drawing = true; ctx.beginPath(); const p = pos(ev); ctx.moveTo(p.x, p.y); ev.preventDefault(); }
    function move(ev) {
        if (!drawing) return;
        const p = pos(ev);
        ctx.strokeStyle = '#111'; ctx.lineWidth = 2; ctx.lineCap = 'round';
        ctx.lineTo(p.x, p.y); ctx.stroke();
        ev.preventDefault();
    }
    function end() { drawing = false; }
    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    window.addEventListener('mouseup', end);
    canvas.addEventListener('touchstart', start, { passive: false });
    canvas.addEventListener('touchmove', move, { passive: false });
    canvas.addEventListener('touchend', end);
    document.getElementById('ctrl-sig-clear')?.addEventListener('click', function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById('controller_signature_canvas').value = '';
    });
    document.getElementById('controller-sign-form')?.addEventListener('submit', function() {
        const blank = document.createElement('canvas');
        blank.width = canvas.width; blank.height = canvas.height;
        if (canvas.toDataURL() !== blank.toDataURL()) {
            document.getElementById('controller_signature_canvas').value = canvas.toDataURL('image/png');
        }
    });
})();
</script>
@endpush
