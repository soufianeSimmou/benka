@extends('layouts.guest')

@section('title', 'Connexion - Presence Chantier')
@section('page-name', 'login')

@section('content')
<style>
    .field-slide-down {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .field-slide-down.show {
        max-height: 200px;
        opacity: 1;
        margin-top: 1.25rem;
    }
</style>

<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-4">
                <img src="/logobenka.png" alt="Benka Logo" class="w-20 h-20 object-contain">
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Benka Presence</h1>
            <p class="text-gray-500 text-sm mt-1">Gestion de presence sur chantier</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-8">
                <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-6">
                    @csrf

                    @if(config('services.google.client_id'))
                        <!-- Google Button -->
                        <a href="{{ route('auth.google') }}" class="w-full inline-flex items-center justify-center gap-3 px-5 py-3 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span>Continuer avec Google</span>
                        </a>

                        @error('google')
                            <div class="flex items-center p-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
                                <svg class="flex-shrink-0 inline w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror

                        <!-- Divider -->
                        <div class="flex items-center gap-4 my-6">
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <span class="text-sm text-gray-500">ou</span>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>
                    @endif

                    <!-- Email Field -->
                    <div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            placeholder="Adresse email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition-colors"
                            oninput="checkEmailFilled()">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field (hidden initially) -->
                    <div id="password-field" class="field-slide-down">
                        <div>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Mot de passe"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 transition-colors">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center mt-3">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="remember" class="ml-2 text-sm text-gray-900 cursor-pointer">Se souvenir de moi</label>
                        </div>
                    </div>

                    <!-- Submit Button (hidden initially) -->
                    <div id="submit-btn" class="field-slide-down">
                        <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-3 text-center transition-colors">
                            Se connecter
                        </button>
                    </div>
                </form>

                <!-- Register Link -->
                <div class="text-center mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        Vous n'avez pas de compte?
                        <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-700 hover:underline">Creer un compte</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function checkEmailFilled() {
        const emailInput = document.getElementById('email');
        const passwordField = document.getElementById('password-field');
        const submitBtn = document.getElementById('submit-btn');

        if (emailInput.value.trim().length > 0) {
            passwordField.classList.add('show');
            submitBtn.classList.add('show');
        } else {
            passwordField.classList.remove('show');
            submitBtn.classList.remove('show');
        }
    }

    // Check on page load if email already filled (validation errors)
    document.addEventListener('DOMContentLoaded', function() {
        checkEmailFilled();
    });
</script>
@endsection
