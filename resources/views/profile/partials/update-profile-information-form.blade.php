<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informations du profil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Mettez à jour les informations de votre compte.") }}
        </p>
    </header>

    {{-- Formulaire de renvoi de vérification email --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- Formulaire principal --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Nom -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
                {{ __('Nom') }}
            </label>
            <input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                       focus:border-indigo-500 focus:ring-indigo-500"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
            >
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Prénom -->
        <div>
            <label for="prenom" class="block text-sm font-medium text-gray-700">
                {{ __('Prénom') }}
            </label>
            <input
                id="prenom"
                name="prenom"
                type="text"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                       focus:border-indigo-500 focus:ring-indigo-500"
                value="{{ old('prenom', $user->prenom) }}"
                required
                autocomplete="given-name"
            >
            @error('prenom')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                {{ __('Email') }}
            </label>
            <input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                       focus:border-indigo-500 focus:ring-indigo-500"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-gray-800">
                        {{ __('Votre adresse email n\'est pas vérifiée.') }}

                        <button
                            form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900
                                   rounded-md focus:outline-none focus:ring-2
                                   focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            {{ __('Cliquez ici pour renvoyer l\'email de vérification.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Un nouveau lien de vérification a été envoyé.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Département -->
        <div>
            <label for="departement" class="block text-sm font-medium text-gray-700">
                {{ __('Département') }}
            </label>
            <input
                id="departement"
                name="departement"
                type="text"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                       focus:border-indigo-500 focus:ring-indigo-500"
                value="{{ old('departement', $user->departement) }}"
            >
            @error('departement')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Fonction -->
        <div>
            <label for="fonction" class="block text-sm font-medium text-gray-700">
                {{ __('Fonction') }}
            </label>
            <input
                id="fonction"
                name="fonction"
                type="text"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                       focus:border-indigo-500 focus:ring-indigo-500"
                value="{{ old('fonction', $user->fonction) }}"
            >
            @error('fonction')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Rôle (lecture seule) -->
        <div>
            <label for="role_display" class="block text-sm font-medium text-gray-700">
                {{ __('Rôle') }}
            </label>
            <input
                id="role_display"
                type="text"
                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm"
                value="{{ $user->role === 'super_admin'
                    ? 'Super Admin'
                    : ($user->role === 'agent_it'
                        ? 'Agent IT'
                        : 'Utilisateur') }}"
                disabled
            >
            <p class="mt-1 text-sm text-gray-500">
                {{ __('Le rôle ne peut être modifié que par un administrateur.') }}
            </p>
        </div>

        <!-- Bouton -->
        <div class="flex items-center gap-4">
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600
                       border border-transparent rounded-md font-semibold
                       text-xs text-white uppercase tracking-widest
                       hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900
                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                       focus:ring-offset-2 transition ease-in-out duration-150"
            >
                {{ __('Enregistrer') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-gray-600">
                    {{ __('Enregistré.') }}
                </p>
            @endif
        </div>
    </form>
</section>
