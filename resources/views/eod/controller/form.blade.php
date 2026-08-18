@extends('layouts.app')

@section('title', 'Signature Controller - ' . $fiche->reference)
@section('header', 'Validation Controller EOD')

@section('content')
@php
    $ctrlUser = auth()->user();
    $ctrlName = trim(($ctrlUser->prenom ?? '') . ' ' . ($ctrlUser->name ?? ''));
    $demandeurName = trim(($fiche->creator?->prenom ?? '') . ' ' . ($fiche->creator?->name ?? ''));
    $n3Name = trim(($fiche->n3Validator?->prenom ?? '') . ' ' . ($fiche->n3Validator?->name ?? ''));
    $controllerSignerName = trim(($fiche->controllerValidator?->prenom ?? '') . ' ' . ($fiche->controllerValidator?->name ?? ''));
    $pendingDual = $fiche->status === 'PENDING_N3_CONTROLLER' && !$fiche->controller_validated_at;
    $legacyPending = $fiche->status === 'PENDING_CONTROLLER';
    $pdfAvailable = in_array($fiche->status, ['CLOSED', 'VALIDATED', 'PENDING_N3_CONTROLLER', 'PENDING_CONTROLLER'], true);
    $pdfIsFinal = in_array($fiche->status, ['CLOSED', 'VALIDATED'], true);
    $existingAttachments = is_array($fiche->attachments ?? null) ? $fiche->attachments : [];
@endphp
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-600 mt-1">Signataire connecté : <strong>{{ $ctrlName }}</strong></p>
            <p class="text-base text-gray-900 mt-2">
                Agent IT :
                <strong class="text-[#C8102E]">{{ $demandeurName !== '' ? $demandeurName : '—' }}</strong>
                @if($fiche->reference)
                    <span class="text-gray-500 text-sm font-normal"> — {{ $fiche->reference }}</span>
                @endif
            </p>
            @if($pendingDual && !$fiche->n3_validated_at)
                <p class="text-sm text-amber-700 mt-2">Le Head IT n’a pas encore signé. Le Controller ne peut signer qu’après la signature Head IT.</p>
            @elseif($pendingDual && $fiche->n3_validated_at)
                <p class="text-sm text-green-700 mt-2">Le Head IT a signé. Votre signature clôturera la fiche et activera le PDF définitif.</p>
            @endif
        </div>
        <div class="flex flex-nowrap items-center gap-2 shrink-0">
            @if($pdfAvailable)
                <button
                    type="button"
                    class="eod-att-preview whitespace-nowrap px-4 py-2 bg-white border border-[#C8102E] text-[#C8102E] hover:bg-red-50 rounded-lg text-sm font-semibold"
                    data-url="{{ route('eod.n2.pdf', ['fiche' => $fiche, 'inline' => 1]) }}"
                    data-name="PDF batch — {{ $fiche->reference }}"
                    data-ext="pdf"
                >
                    Prévisualiser PDF
                </button>
                <a href="{{ route('eod.n2.pdf', $fiche) }}" class="whitespace-nowrap px-4 py-2 bg-[#C8102E] hover:bg-[#a00d24] text-white rounded-lg text-sm font-semibold">
                    {{ $pdfIsFinal ? 'Télécharger PDF' : 'Télécharger aperçu PDF' }}
                </a>
            @endif
            <a href="{{ route('eod.controller.index') }}" class="whitespace-nowrap px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold">Retour</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white border rounded-lg p-4"><p class="text-xs text-gray-500">Date traitement</p><p class="font-semibold">{{ $fiche->date_traitement?->format('d/m/Y') }}</p></div>
        <div class="bg-white border rounded-lg p-4"><p class="text-xs text-gray-500">Heure début</p><p class="font-semibold">{{ $fiche->heure_lancement ?? '—' }}</p></div>
        <div class="bg-white border rounded-lg p-4"><p class="text-xs text-gray-500">Heure fin</p><p class="font-semibold">{{ $fiche->heure_fin ?? '—' }}</p></div>
        <div class="bg-white border rounded-lg p-4"><p class="text-xs text-gray-500">Statut</p><p><span class="px-2 py-1 rounded-full text-xs {{ $fiche->status_class }}">{{ $fiche->status_label }}</span></p></div>
        <div class="bg-white border border-[#C8102E]/30 rounded-lg p-4">
            <p class="text-xs text-gray-500">Agent IT</p>
            <p class="font-semibold text-[#C8102E]">{{ $demandeurName !== '' ? $demandeurName : '—' }}</p>
        </div>
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

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Pièces jointes</h2>
        @if(count($existingAttachments) > 0)
            <div class="space-y-2">
                @foreach($existingAttachments as $idx => $att)
                    @php
                        $attName = $att['name'] ?? 'Fichier joint';
                        $viewUrl = route('eod.attachments.show', ['fiche' => $fiche, 'index' => $idx]);
                        $downloadUrl = route('eod.attachments.show', ['fiche' => $fiche, 'index' => $idx, 'download' => 1]);
                        $ext = strtolower(pathinfo($attName, PATHINFO_EXTENSION));
                        $isPreviewable = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'xlsx', 'xls', 'csv'], true);
                    @endphp
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-3 rounded-lg border border-gray-200 bg-gray-50">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-800 truncate">{{ $attName }}</p>
                            @if(!empty($att['uploaded_at']))
                                <p class="text-xs text-gray-500">Ajouté le {{ $att['uploaded_at'] }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($isPreviewable)
                                <button
                                    type="button"
                                    class="eod-att-preview px-3 py-1.5 text-sm font-semibold rounded-lg border border-gray-300 bg-white hover:bg-gray-100 text-gray-700"
                                    data-url="{{ $viewUrl }}"
                                    data-name="{{ $attName }}"
                                    data-ext="{{ $ext }}"
                                >
                                    Visualiser
                                </button>
                            @endif
                            <a href="{{ $downloadUrl }}" class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-[#C8102E] hover:bg-[#a00d24] text-white">
                                Télécharger
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 text-center py-4">Aucune pièce jointe.</p>
        @endif
    </div>

    <div id="eod-att-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60 p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-gray-200">
                <p id="eod-att-modal-title" class="text-sm font-semibold text-gray-800 truncate">Pièce jointe</p>
                <button type="button" id="eod-att-modal-close" class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700">Fermer</button>
            </div>
            <div id="eod-att-modal-body" class="flex-1 overflow-auto bg-gray-100 p-2 min-h-[50vh]"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Émargement</h2>
            <p class="text-sm bg-gray-50 p-3 rounded">{{ $fiche->emargement ?: '—' }}</p>
            <p class="mt-2 text-sm"><span class="text-gray-600">Responsable batch :</span> {{ $fiche->responsable_batch ?: '—' }}</p>
            @if($fiche->emargement_signature_path)
                <p class="mt-2 text-xs text-gray-500">Signature émargement</p>
                <img src="{{ asset('storage/'.$fiche->emargement_signature_path) }}" alt="Signature émargement" class="mt-1 max-h-28 rounded border border-gray-200">
            @endif
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">Signataires (Head IT puis Controller)</h2>
            <div class="space-y-3 text-sm">
                @if($fiche->n3_validated_at)
                    <div>
                        <p><span class="text-gray-600">Head IT :</span> <strong>{{ $n3Name !== '' ? $n3Name : '—' }}</strong>
                            — {{ $fiche->n3_validation_date ?? $fiche->n3_validated_at->format('d/m/Y H:i') }}</p>
                        @if($fiche->n3_signature_path)
                            <img src="{{ asset('storage/'.$fiche->n3_signature_path) }}" alt="Signature Head IT" class="mt-2 max-h-24 rounded border border-gray-200">
                        @endif
                    </div>
                @else
                    <p class="text-amber-800">Signature Head IT en attente.</p>
                @endif
                <hr class="border-gray-100">
                @if($fiche->controller_validated_at)
                    <div>
                        <p><span class="text-gray-600">Controller :</span> <strong>{{ $controllerSignerName !== '' ? $controllerSignerName : '—' }}</strong>
                            — {{ $fiche->controller_validation_date ?? $fiche->controller_validated_at->format('d/m/Y H:i') }}</p>
                        @if($fiche->controller_signature_path)
                            <img src="{{ asset('storage/'.$fiche->controller_signature_path) }}" alt="Signature Controller" class="mt-2 max-h-24 rounded border border-gray-200">
                        @elseif($fiche->controller_validation_visa)
                            <p class="text-gray-700 mt-1">Visa : {{ $fiche->controller_validation_visa }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-amber-800">Signature Controller en attente.</p>
                @endif
            </div>
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
        @elseif($pendingDual && $fiche->n3_validated_at)
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
                    <x-signature-pad
                        canvas-id="ctrl-sig-canvas"
                        hidden-input-id="controller_signature_canvas"
                        hidden-input-name="controller_signature_canvas"
                        form-id="controller-sign-form"
                        label="Ou signer sur le canvas"
                    />
                </div>
                <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-1">Note (optionnel)</label>
                    <textarea name="controller_validation_note" rows="3" class="w-full rounded-lg border-gray-300 text-sm">{{ old('controller_validation_note') }}</textarea>
                </div>
                <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold">Enregistrer la signature Controller</button>
            </form>
        @elseif($pendingDual)
            <p class="text-sm text-amber-800">Le formulaire de signature Controller s’activera après la signature Head IT.</p>
        @else
            <div class="text-sm text-gray-700 space-y-2">
                <p><strong>Signé par :</strong> {{ $controllerSignerName !== '' ? $controllerSignerName : '—' }}</p>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('eod-att-modal');
    const title = document.getElementById('eod-att-modal-title');
    const body = document.getElementById('eod-att-modal-body');
    const closeBtn = document.getElementById('eod-att-modal-close');
    if (!modal || !body) return;

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        body.innerHTML = '';
    }

    document.querySelectorAll('.eod-att-preview').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const url = btn.getAttribute('data-url');
            const name = btn.getAttribute('data-name') || 'Pièce jointe';
            const ext = (btn.getAttribute('data-ext') || '').toLowerCase();
            title.textContent = name;
            body.innerHTML = '';
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                const img = document.createElement('img');
                img.src = url;
                img.alt = name;
                img.className = 'max-w-full max-h-[80vh] mx-auto block rounded';
                body.appendChild(img);
            } else {
                // PDF + Excel (xlsx/xls/csv) en iframe
                const iframe = document.createElement('iframe');
                iframe.src = url;
                iframe.title = name;
                iframe.className = 'w-full h-[75vh] bg-white rounded border-0';
                body.appendChild(iframe);
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });

    closeBtn?.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });
});
</script>
@endsection
