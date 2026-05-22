<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Ma signature') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Enregistrez une signature sur votre profil. Elle pourra être chargée automatiquement sur les formulaires EOD, transitions, etc.') }}
        </p>
    </header>

    @if (session('success') && str_contains(session('success'), 'Signature'))
        <p class="mt-4 text-sm text-green-600">{{ session('success') }}</p>
    @endif
    @if (session('error'))
        <p class="mt-4 text-sm text-red-600">{{ session('error') }}</p>
    @endif

    @if ($user->hasStoredSignature())
        <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 max-w-lg">
            <p class="text-sm text-gray-600 mb-2">
                {{ __('Signature actuelle') }}
                @if ($user->signature_updated_at)
                    <span class="text-gray-400">— {{ $user->signature_updated_at->format('d/m/Y H:i') }}</span>
                @endif
            </p>
            <img
                src="{{ $user->signaturePublicUrl() }}"
                alt="{{ __('Signature enregistrée') }}"
                class="max-h-24 rounded border border-gray-200 bg-white"
            >
        </div>
    @endif

    <form
        method="post"
        action="{{ route('profile.signature.store') }}"
        enctype="multipart/form-data"
        class="profile-form mt-6 space-y-4 max-w-lg"
        id="profile-signature-form"
    >
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Importer une image (PNG, JPG)') }}
            </label>
            <input
                type="file"
                name="signature_file"
                accept="image/*"
                class="block w-full text-sm text-gray-600"
            >
            @error('signature_file')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <x-signature-pad
            canvas-id="profile-sig-canvas"
            hidden-input-id="signature_canvas"
            hidden-input-name="signature_canvas"
            form-id="profile-signature-form"
            :label="__('Ou dessinez votre signature')"
            :show-load-button="false"
        />

        <div class="flex flex-wrap items-center gap-3">
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                {{ __('Enregistrer la signature') }}
            </button>
        </div>
    </form>

    @if ($user->hasStoredSignature())
        <form method="post" action="{{ route('profile.signature.destroy') }}" class="mt-4" onsubmit="return confirm('{{ __('Supprimer votre signature enregistrée ?') }}');">
            @csrf
            @method('delete')
            <button type="submit" class="text-sm text-red-600 hover:text-red-800 underline">
                {{ __('Supprimer la signature du profil') }}
            </button>
        </form>
    @endif
</section>
