@props([
    'formId' => 'admin-user-form',
    'canvasId' => 'admin-user-sig-canvas',
    'hiddenInputId' => 'admin_signature_canvas',
    'user' => null,
])

<div class="rounded-lg border border-amber-100 bg-amber-50/40 p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ __('Signature du profil') }}</h3>
    <p class="text-sm text-gray-600 mb-4">
        @if ($user)
            {{ __('Signature liée au compte de') }}
            <strong>{{ $user->prenom }} {{ $user->name }}</strong>.
        @else
            {{ __('Signature optionnelle : elle sera enregistrée avec le compte à la création.') }}
        @endif
        {{ __('L\'utilisateur pourra la charger via « Charger ma signature » sur les formulaires EOD et transitions.') }}
    </p>

    @if ($user && $user->hasStoredSignature())
        <div class="mb-4 p-3 bg-white rounded border border-gray-200 inline-block">
            <img src="{{ $user->signaturePublicUrl() }}" alt="Signature" class="max-h-20">
            @if ($user->signature_updated_at)
                <p class="text-xs text-gray-500 mt-1">{{ __('Mise à jour :') }} {{ $user->signature_updated_at->format('d/m/Y H:i') }}</p>
            @endif
        </div>
    @endif

    <div class="space-y-4 max-w-lg">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Importer une image (PNG, JPG)') }}</label>
            <input type="file" name="signature_file" accept="image/*" class="block w-full text-sm">
            @error('signature_file')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <x-signature-pad
            :canvas-id="$canvasId"
            :hidden-input-id="$hiddenInputId"
            hidden-input-name="signature_canvas"
            :form-id="$formId"
            :label="__('Ou dessiner la signature')"
            :show-load-button="false"
        />
    </div>
</div>
