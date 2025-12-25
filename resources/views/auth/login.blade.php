@extends('layouts.guest')

@section('title', 'Connexion - Presence Chantier')
@section('page-name', 'login')

@section('content')
<style>
    .auth-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }

    .auth-gradient::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 15s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(-30px, -30px); }
    }

    .auth-card {
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.95);
        border-radius: 24px;
        box-shadow:
            0 20px 60px rgba(0, 0, 0, 0.15),
            0 0 0 1px rgba(255, 255, 255, 0.5) inset;
    }

    [data-theme="dark"] .auth-card {
        background: rgba(30, 41, 59, 0.95);
    }

    .logo-glow {
        filter: drop-shadow(0 8px 16px rgba(102, 126, 234, 0.3));
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .tab-modern {
        transition: all 0.3s ease;
        border-radius: 12px;
        font-weight: 500;
    }

    .tab-modern.tab-active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .input-modern {
        transition: all 0.3s ease;
    }

    .input-modern:focus {
        transform: translateY(-2px);
    }

    .btn-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
    }

    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        background: linear-gradient(135deg, #7c93f2 0%, #8b5cb8 100%);
    }

    .google-btn {
        transition: all 0.3s ease;
    }

    .google-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="min-h-screen auth-gradient flex items-center justify-center p-4">
    <div class="w-full max-w-md relative z-10">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-6 logo-glow">
                <img src="/logobenka.png" alt="Benka Logo" class="w-28 h-28 object-contain">
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Benka Presence</h1>
            <p class="text-white/80 text-base">Gestion moderne de presence sur chantier</p>
        </div>

        <!-- Tabs -->
        <div class="flex gap-3 mb-6 bg-white/10 backdrop-blur-sm p-2 rounded-2xl">
            <button type="button" id="tab-login" onclick="switchTab('login')" class="tab-modern tab-active flex-1 py-3 px-6 text-white">
                Connexion
            </button>
            <button type="button" id="tab-register" onclick="switchTab('register')" class="tab-modern flex-1 py-3 px-6 text-white hover:bg-white/10">
                Inscription
            </button>
        </div>

        <!-- Card -->
        <div class="auth-card p-8">
            <div class="space-y-6">
                @if(config('services.google.client_id'))
                    <!-- Google Button -->
                    <a href="{{ route('auth.google') }}" class="btn btn-lg bg-white hover:bg-gray-50 text-gray-700 border-2 border-gray-200 w-full gap-3 google-btn">
                        <svg class="w-6 h-6" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span class="font-semibold">Continuer avec Google</span>
                    </a>

                    @error('google')
                        <div class="alert alert-error shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                    <!-- Divider -->
                    <div class="flex items-center gap-4">
                        <div class="flex-1 h-px bg-gray-300"></div>
                        <span class="text-sm text-gray-500 font-medium">ou</span>
                        <div class="flex-1 h-px bg-gray-300"></div>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-5">
                    @csrf
                    <div class="form-control">
                        <label class="label" for="login-email">
                            <span class="label-text font-semibold text-base">Email</span>
                        </label>
                        <input type="email" id="login-email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="votre@email.com" class="input input-bordered input-lg w-full input-modern">
                        @error('email')
                            <label class="label"><span class="label-text-alt text-error font-medium">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label" for="login-password">
                            <span class="label-text font-semibold text-base">Mot de passe</span>
                        </label>
                        <input type="password" id="login-password" name="password" required autocomplete="current-password" placeholder="••••••••" class="input input-bordered input-lg w-full input-modern">
                        @error('password')
                            <label class="label"><span class="label-text-alt text-error font-medium">{{ $message }}</span></label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" name="remember" class="checkbox checkbox-primary">
                            <span class="label-text font-medium">Se souvenir de moi</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-lg btn-gradient w-full">
                        <span class="text-lg">Se connecter</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </form>

                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" id="register-form" class="space-y-5 hidden">
                    @csrf
                    <div class="form-control">
                        <label class="label" for="register-name">
                            <span class="label-text font-semibold text-base">Nom complet</span>
                        </label>
                        <input type="text" id="register-name" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Jean Dupont" class="input input-bordered input-lg w-full input-modern">
                    </div>

                    <div class="form-control">
                        <label class="label" for="register-email">
                            <span class="label-text font-semibold text-base">Email</span>
                        </label>
                        <input type="email" id="register-email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="votre@email.com" class="input input-bordered input-lg w-full input-modern">
                    </div>

                    <div class="form-control">
                        <label class="label" for="register-password">
                            <span class="label-text font-semibold text-base">Mot de passe</span>
                        </label>
                        <input type="password" id="register-password" name="password" required autocomplete="new-password" placeholder="Minimum 8 caracteres" class="input input-bordered input-lg w-full input-modern">
                    </div>

                    <div class="form-control">
                        <label class="label" for="register-password-confirm">
                            <span class="label-text font-semibold text-base">Confirmer le mot de passe</span>
                        </label>
                        <input type="password" id="register-password-confirm" name="password_confirmation" required autocomplete="new-password" placeholder="Confirmez votre mot de passe" class="input input-bordered input-lg w-full input-modern">
                    </div>

                    <button type="submit" class="btn btn-lg btn-gradient w-full">
                        <span class="text-lg">Creer mon compte</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    function switchTab(tab) {
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const tabLogin = document.getElementById('tab-login');
        const tabRegister = document.getElementById('tab-register');

        if (tab === 'login') {
            // Fade out register, fade in login
            registerForm.style.opacity = '0';
            setTimeout(() => {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                tabLogin.classList.add('tab-active');
                tabRegister.classList.remove('tab-active');
                setTimeout(() => {
                    loginForm.style.opacity = '1';
                }, 10);
            }, 200);
        } else {
            // Fade out login, fade in register
            loginForm.style.opacity = '0';
            setTimeout(() => {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                tabRegister.classList.add('tab-active');
                tabLogin.classList.remove('tab-active');
                setTimeout(() => {
                    registerForm.style.opacity = '1';
                }, 10);
            }, 200);
        }
    }

    // Initialize form transitions
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');

        loginForm.style.transition = 'opacity 0.2s ease-in-out';
        registerForm.style.transition = 'opacity 0.2s ease-in-out';
        loginForm.style.opacity = '1';
        registerForm.style.opacity = '0';
    });
</script>
@endsection
