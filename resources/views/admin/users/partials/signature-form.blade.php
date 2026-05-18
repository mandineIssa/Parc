<div class="mt-8">
    <form
        method="POST"
        action="{{ route('users.signature.store', $user) }}"
        enctype="multipart/form-data"
        id="admin-user-signature-form"
        class="space-y-4"
    >
        @csrf

        @include('admin.users.partials.signature-fields', [
            'user' => $user,
            'formId' => 'admin-user-signature-form',
        ])

        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md">
            {{ __('Enregistrer la signature pour cet utilisateur') }}
        </button>
    </form>

    @if ($user->hasStoredSignature())
        <form
            method="POST"
            action="{{ route('users.signature.destroy', $user) }}"
            class="mt-3"
            onsubmit="return confirm('{{ __('Supprimer la signature de cet utilisateur ?') }}');"
        >
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm text-red-600 hover:underline">{{ __('Supprimer la signature') }}</button>
        </form>
    @endif
</div>
