@extends('layouts.guest')

@section('title', 'Connexion - Presence Chantier')
@section('page-name', 'login')

@section('content')
<style>
    .bg-pattern {
        background-color: #f3f4f6;
        background-image:
            url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"),
            url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%2359b5f9' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
    }

    .form-container {
        transition: opacity 0.3s ease-in-out;
    }

    .form-container.hidden {
        display: none;
    }
</style>

<div class="min-h-screen bg-pattern flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <!-- Logo -->
        <div class="text-center mb-8">
            <img src="/logobenka.png" alt="Benka Logo" class="w-32 h-32 mx-auto object-contain">
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <!-- Titre et indicateur de mode -->
            <div class="mb-6">
                <h2 id="form-title" class="text-xl font-semibold text-gray-900">Connexion</h2>
                <p id="form-subtitle" class="text-sm text-gray-500 mt-1">Connectez-vous à votre compte</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                @foreach($errors->all() as $error)
                                    {{ $error }}<br>
                                @endforeach
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if(config('services.google.client_id'))
                <!-- Google Button -->
                <a href="{{ route('auth.google') }}" id="google-btn" onclick="showGoogleLoading(event)" class="w-full inline-flex items-center justify-center gap-3 px-5 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors mb-4">
                    <svg id="google-icon" class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <svg id="google-spinner" class="hidden w-5 h-5 animate-spin text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="google-text">Continuer avec Google</span>
                </a>

                <!-- Divider -->
                <div class="flex items-center gap-3 my-4">
                    <div class="flex-1 h-px bg-gray-300"></div>
                    <span class="text-xs text-gray-500 uppercase">ou</span>
                    <div class="flex-1 h-px bg-gray-300"></div>
                </div>
            @endif

            <!-- LOGIN FORM -->
            <form method="POST" action="{{ route('login') }}" id="login-form" class="form-container space-y-4">
                @csrf

                <div>
                    <label for="login-email" class="block text-sm font-medium text-gray-700 mb-2">Adresse email</label>
                    <input
                        type="email"
                        id="login-email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        placeholder="nom@exemple.com"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3">
                </div>

                <div>
                    <label for="login-password" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                    <input
                        type="password"
                        id="login-password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer">Se souvenir de moi</label>
                </div>

                <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-3 text-center transition-colors">
                    Se connecter
                </button>
            </form>

            <!-- REGISTER FORM -->
            <form method="POST" action="{{ route('register') }}" id="register-form" class="form-container hidden space-y-4">
                @csrf

                <div>
                    <label for="register-name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom complet <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="register-name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        placeholder="Jean Dupont"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3">
                    <p class="text-xs text-gray-500 mt-1">Entrez votre prénom et nom</p>
                </div>

                <div>
                    <label for="register-email" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse email <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        id="register-email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        placeholder="nom@exemple.com"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3">
                    <p class="text-xs text-gray-500 mt-1">Vous recevrez vos informations de connexion à cette adresse</p>
                </div>

                <div>
                    <label for="register-password" class="block text-sm font-medium text-gray-700 mb-2">
                        Mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="password"
                        id="register-password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3">
                    <p class="text-xs text-gray-500 mt-1">Minimum 8 caractères pour votre sécurité</p>
                </div>

                <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-3 text-center transition-colors">
                    Créer mon compte
                </button>
            </form>

            <!-- Toggle Link -->
            <div class="text-center mt-6 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    <span id="toggle-text">Vous n'avez pas de compte?</span>
                    <button type="button" id="toggle-btn" onclick="toggleForm()" class="font-medium text-blue-600 hover:text-blue-700 ml-1">
                        Créer un compte
                    </button>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    let isRegisterMode = false;

    function showGoogleLoading(event) {
        const googleIcon = document.getElementById('google-icon');
        const googleSpinner = document.getElementById('google-spinner');
        const googleText = document.getElementById('google-text');
        const googleBtn = document.getElementById('google-btn');

        googleIcon.classList.add('hidden');
        googleSpinner.classList.remove('hidden');
        googleText.textContent = 'Connexion...';
        googleBtn.classList.add('opacity-75', 'pointer-events-none');
    }

    function toggleForm() {
        isRegisterMode = !isRegisterMode;

        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const formTitle = document.getElementById('form-title');
        const formSubtitle = document.getElementById('form-subtitle');
        const toggleText = document.getElementById('toggle-text');
        const toggleBtn = document.getElementById('toggle-btn');

        if (isRegisterMode) {
            // Afficher le formulaire d'inscription
            loginForm.classList.add('hidden');
            registerForm.classList.remove('hidden');
            formTitle.textContent = 'Inscription';
            formSubtitle.textContent = 'Créez votre compte pour commencer';
            toggleText.textContent = 'Vous avez déjà un compte?';
            toggleBtn.textContent = 'Se connecter';
        } else {
            // Afficher le formulaire de connexion
            registerForm.classList.add('hidden');
            loginForm.classList.remove('hidden');
            formTitle.textContent = 'Connexion';
            formSubtitle.textContent = 'Connectez-vous à votre compte';
            toggleText.textContent = "Vous n'avez pas de compte?";
            toggleBtn.textContent = 'Créer un compte';
        }
    }

    // Si il y a des erreurs de validation pour l'inscription, afficher le formulaire d'inscription
    document.addEventListener('DOMContentLoaded', function() {
        @if(old('name') || ($errors->has('name')))
            toggleForm(); // Switch to register form
        @endif
    });
</script>
@endsection
