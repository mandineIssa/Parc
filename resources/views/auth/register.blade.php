<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Gestion Parc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('images/Cofina1.jpeg') }}" 
                         alt="Cofina Logo" 
                         class="h-16 w-auto">
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Créer un compte</h1>
                <p class="text-gray-600 mt-2">Rejoignez la plateforme de gestion du parc</p>
            </div>

            <!-- Formulaire -->
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Nom -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2"></i>Nom complet
                    </label>
                    <input id="name" 
                           type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus 
                           autocomplete="name"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition"
                           placeholder="Votre nom et prénom">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2"></i>Adresse email
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="email"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition"
                           placeholder="exemple@cofina.sn">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rôle -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-tag mr-2"></i>Rôle dans l'organisation
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach(['agent_it' => 'Agent IT', 'agent_parc' => 'Agent Parc', 'responsable_parc' => 'Responsable Parc', 'super_admin' => 'Super Admin'] as $value => $label)
                            <label class="relative">
                                <input type="radio" 
                                       name="role" 
                                       value="{{ $value }}"
                                       {{ old('role') == $value ? 'checked' : '' }}
                                       class="sr-only peer" 
                                       required>
                                <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer transition-all peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300">
                                    <div class="font-medium text-gray-800">{{ $label }}</div>
                                    @switch($value)
                                        @case('agent_it')
                                            <div class="text-xs text-blue-600 mt-1"><i class="fas fa-laptop-code"></i> Gestion équipements IT</div>
                                            @break
                                        @case('agent_parc')
                                            <div class="text-xs text-green-600 mt-1"><i class="fas fa-boxes"></i> Gestion stock</div>
                                            @break
                                        @case('responsable_parc')
                                            <div class="text-xs text-amber-600 mt-1"><i class="fas fa-user-tie"></i> Supervision</div>
                                            @break
                                        @case('super_admin')
                                            <div class="text-xs text-purple-600 mt-1"><i class="fas fa-shield-alt"></i> Accès complet</div>
                                            @break
                                    @endswitch
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('role')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Mot de passe
                    </label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="new-password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition"
                           placeholder="Minimum 8 caractères">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmation mot de passe -->
                <div class="mb-8">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Confirmer le mot de passe
                    </label>
                    <input id="password_confirmation" 
                           type="password" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition"
                           placeholder="Répétez le mot de passe">
                </div>

                <!-- Bouton d'inscription -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold py-3 px-6 rounded-lg hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 transform hover:-translate-y-0.5">
                    <i class="fas fa-user-plus mr-2"></i>Créer mon compte
                </button>

                <!-- Lien vers connexion -->
                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Déjà inscrit ? 
                        <a href="{{ route('login') }}" class="font-semibold text-red-600 hover:text-red-700">
                            <i class="fas fa-sign-in-alt mr-1"></i>Se connecter
                        </a>
                    </p>
                </div>
            </form>

            <!-- Informations -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="flex items-center text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-2"></i>
                    <p>Votre compte doit être validé par un administrateur avant d'être actif.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>