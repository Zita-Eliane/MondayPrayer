<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sentinelle — Connexion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            background: #060918;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            position: relative;
        }

        /* Blobs décoratifs */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            pointer-events: none;
        }
        .blob-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #4F8EF7, #1a3a6e);
            top: -150px; left: -100px;
        }
        .blob-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #C9A84C, #7a5a1a);
            bottom: -100px; right: -80px;
        }
        .blob-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, #6366f1, #312e81);
            top: 50%; left: 60%;
            opacity: 0.08;
        }

        /* Card principale */
        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            margin: 1.5rem;
            background: rgba(13, 19, 51, 0.85);
            border: 1px solid rgba(99, 132, 255, 0.15);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.04) inset;
        }

        /* Logo */
        .logo-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
        }
        .logo-flame {
            width: 64px;
            height: 80px;
            margin-bottom: 12px;
            filter: drop-shadow(0 0 20px rgba(201,168,76,0.4));
        }
        .logo-title {
            font-family: 'Cinzel', serif;
            font-size: 22px;
            font-weight: 700;
            color: #C9A84C;
            letter-spacing: 0.12em;
        }
        .logo-subtitle {
            font-size: 12px;
            color: rgba(139,156,196,0.7);
            margin-top: 4px;
            letter-spacing: 0.08em;
        }

        /* Séparateur décoratif */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.75rem;
        }
        .divider-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(201,168,76,0.3), transparent);
        }
        .divider-icon { font-size: 14px; color: rgba(201,168,76,0.5); }

        /* Labels */
        .field-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(139,156,196,0.8);
            margin-bottom: 8px;
        }

        /* Inputs */
        .field-wrap {
            position: relative;
            margin-bottom: 1.25rem;
        }
        .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: rgba(139,156,196,0.5);
            pointer-events: none;
        }
        .field-input {
            width: 100%;
            background: rgba(6, 9, 24, 0.6);
            border: 1px solid rgba(99, 132, 255, 0.15);
            border-radius: 12px;
            color: #E8EDF8;
            font-size: 14px;
            padding: 12px 14px 12px 42px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: 'Inter', sans-serif;
        }
        .field-input:focus {
            border-color: #C9A84C;
            box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
        }
        .field-input::placeholder { color: rgba(139,156,196,0.4); }

        /* Remember me */
        .remember-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: rgba(139,156,196,0.8);
            cursor: pointer;
        }
        .remember-label input {
            width: 16px;
            height: 16px;
            accent-color: #C9A84C;
            cursor: pointer;
        }
        .forgot-link {
            font-size: 12px;
            color: rgba(79,142,247,0.8);
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: #4F8EF7; }

        /* Bouton connexion */
        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #C9A84C, #A8882E);
            border: none;
            border-radius: 12px;
            color: #1A1000;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Cinzel', serif;
            letter-spacing: 0.08em;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 20px rgba(201,168,76,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 28px rgba(201,168,76,0.45);
        }
        .btn-login:active { transform: translateY(0); }

        /* Register link */
        .register-wrap {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 13px;
            color: rgba(139,156,196,0.6);
        }
        .register-wrap a {
            color: #C9A84C;
            text-decoration: none;
            font-weight: 600;
        }
        .register-wrap a:hover { color: #E8C96A; }

        /* Erreurs */
        .error-msg {
            font-size: 11px;
            color: #FCA5A5;
            margin-top: 5px;
        }

        /* Erreur générale */
        .alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            color: #FCA5A5;
            margin-bottom: 1.25rem;
        }
    </style>
</head>
<body>

    {{-- Blobs décoratifs --}}
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="login-card">

        {{-- Logo --}}
        <div class="logo-wrap">
            <svg class="logo-flame" viewBox="0 0 40 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 4C20 4 6 18 6 34C6 46 11 55 20 60C29 55 34 46 34 34C34 18 20 4 20 4Z" fill="#A8882E" opacity="0.9"/>
                <path d="M20 18C20 18 10 28 10 38C10 46 14 52 20 56C26 52 30 46 30 38C30 28 20 18 20 18Z" fill="#E8C96A"/>
                <path d="M20 32C20 32 15 38 15 42C15 46 17 49 20 50C23 49 25 46 25 42C25 38 20 32 20 32Z" fill="#FFFBE6"/>
                {{-- Petite flamme base --}}
                <ellipse cx="20" cy="62" rx="6" ry="3" fill="rgba(201,168,76,0.2)"/>
            </svg>
            <div class="logo-title">Sentinelle</div>
            <div class="logo-subtitle">Jeûne & Prière — Communauté</div>
        </div>

        <div class="divider">
            <div class="divider-line"></div>
            <span class="divider-icon">🕊️</span>
            <div class="divider-line"></div>
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div style="background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.2);border-radius:10px;padding:10px 14px;font-size:13px;color:#34D399;margin-bottom:1.25rem">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div>
                <label class="field-label" for="email">Adresse email</label>
                <div class="field-wrap">
                    <svg class="field-icon" viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                    <input id="email" type="email" name="email" class="field-input"
                        value="{{ old('email') }}" placeholder="nom@exemple.com"
                        required autofocus autocomplete="username">
                </div>
                @error('email')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mot de passe --}}
            <div>
                <label class="field-label" for="password">Mot de passe</label>
                <div class="field-wrap">
                    <svg class="field-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                    <input id="password" type="password" name="password" class="field-input"
                        placeholder="••••••••••"
                        required autocomplete="current-password">
                </div>
                @error('password')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember + Forgot --}}
            <div class="remember-wrap">
                <label class="remember-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Se souvenir de moi
                </label>
                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                @endif
            </div>

            {{-- Bouton --}}
            <button type="submit" class="btn-login">
                <svg style="width:16px;height:16px" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/></svg>
                Se connecter
            </button>
        </form>

        {{-- Inscription --}}
        @if (Route::has('register'))
            <div class="register-wrap">
                Pas encore de compte ? <a href="{{ route('register') }}">S'inscrire</a>
            </div>
        @endif

    </div>

</body>
</html>