<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - GPI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            display: flex;
            max-width: 1000px;
            width: 90%;
            min-height: 600px;
        }
        .left-side {
            flex: 1;
            background: linear-gradient(rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)), 
                        url('{{ asset("images/Cofina1.jpeg") }}');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .right-side {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .logo {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            color: white;
        }
        .tagline {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 40px;
        }
        .input-group {
            margin-bottom: 25px;
        }
        .input-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        .input-field {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        .input-field:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            outline: none;
        }
        .btn-login {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            margin-top: 10px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(102, 126, 234, 0.4);
        }
        .forgot-password {
            text-align: right;
            margin-top: 15px;
        }
        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        .welcome-text {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
        }
        .welcome-subtext {
            font-size: 16px;
            color: #666;
            margin-bottom: 40px;
        }
        .emoji {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .divider {
            display: flex;
            align-items: center;
            margin: 30px 0;
            color: #888;
        }
        .divider::before, .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e0e0e0;
        }
        .divider span {
            padding: 0 15px;
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                width: 95%;
                max-width: 500px;
            }
            .left-side {
                padding: 40px 30px;
                min-height: 200px;
            }
            .right-side {
                padding: 40px 30px;
            }
        }
        .error-message {
            color: #e53e3e;
            font-size: 14px;
            margin-top: 5px;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #888;
        }
        .password-container {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left side with image and welcome message -->
        <div class="left-side">
            <div class="emoji">🎉</div>
            <div class="logo">GPI!</div>
            <div class="tagline">Votre plateforme de Gestion de parc informatique</div>
            <p>Accédez à vos tableaux de bord, rapports et stockez vos equipments en toute sécurité.</p>
        </div>
        
        <!-- Right side with login form -->
        <div class="right-side">
            <div class="welcome-text">Bienvenue sur Gestion Parc 🎉</div>
            <div class="welcome-subtext">Veuillez vous connecter</div>
            
            <!-- Session Status -->
            @if(session('status'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
                {{ session('status') }}
            </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email Address -->
                <div class="input-group">
                    <label class="input-label" for="email">Email</label>
                    <input 
                        id="email" 
                        class="input-field" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                        placeholder="cofina.support@cofina.com"
                    >
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Password -->
                <div class="input-group">
                    <label class="input-label" for="password">Mot de passe</label>
                    <div class="password-container">
                        <input 
                            id="password" 
                            class="input-field" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="**********"
                        >
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Se souvenir de moi') }}</span>
                    </label>
                </div>
                
                <!-- Forgot Password & Login Button -->
                <div class="forgot-password">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">
                            {{ __('Mot de passe oublié ?') }}
                        </a>
                    @endif
                </div>
                
                <button type="submit" class="btn-login">
                    {{ __('Connexion') }}
                </button>
            </form>
            
            <!-- Divider for additional options (if needed) -->
            <!--
            <div class="divider">
                <span>Ou continuez avec</span>
            </div>
            -->
            
            <!-- Additional info -->
            <div class="mt-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Cofina. Tous droits réservés.
            </div>
        </div>
    </div>
    
    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Add focus effect to inputs
        const inputs = document.querySelectorAll('.input-field');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('input-focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('input-focused');
            });
        });
    </script>
</body>
</html>