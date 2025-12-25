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
        max-height: 400px;
        opacity: 1;
        margin-top: 1rem;
    }

    .bg-pattern {
        background-color: #f3f4f6;
        background-image:
            url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"),
            url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%2359b5f9' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
    }
</style>

<div class="min-h-screen bg-pattern flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <!-- Logo -->
        <div class="text-center mb-8">
            <img src="/logobenka.png" alt="Benka Logo" class="w-16 h-16 mx-auto mb-3 object-contain">
            <h1 class="text-2xl font-bold text-gray-900">Benka Presence</h1>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-4">
                @csrf

                @if(config('services.google.client_id'))
                    <!-- Google Button -->
                    <a href="{{ route('auth.google') }}" class="w-full inline-flex items-center justify-center gap-3 px-5 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Continue with Google</span>
                    </a>

                    <!-- Divider -->
                    <div class="flex items-center gap-3 my-4">
                        <div class="flex-1 h-px bg-gray-300"></div>
                        <span class="text-xs text-gray-500 uppercase">or</span>
                        <div class="flex-1 h-px bg-gray-300"></div>
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
                        placeholder="Enter your email"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3"
                        oninput="checkEmailFilled()">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Continue Button -->
                <button type="button" id="continue-btn" onclick="showPasswordField()" class="w-full text-gray-700 bg-white hover:bg-gray-50 font-medium rounded-lg text-sm px-5 py-3 text-center transition-colors border border-gray-300">
                    Continue with email
                </button>

                <!-- Password Section (hidden initially) -->
                <div id="password-section" class="field-slide-down space-y-4">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full p-3">
                    @error('password')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="remember" class="ml-2 text-sm text-gray-600 cursor-pointer">Remember me</label>
                    </div>

                    <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-3 text-center transition-colors">
                        Sign in
                    </button>
                </div>
            </form>

            <!-- Register Link -->
            <div class="text-center mt-6 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Vous n'avez pas de compte?
                    <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-700">Créer un compte</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    function showPasswordField() {
        const emailInput = document.getElementById('email');
        if (emailInput.value.trim().length > 0) {
            document.getElementById('password-section').classList.add('show');
            document.getElementById('continue-btn').style.display = 'none';
            document.getElementById('password').focus();
        }
    }

    function checkEmailFilled() {
        const emailInput = document.getElementById('email');
        const continueBtn = document.getElementById('continue-btn');

        if (emailInput.value.trim().length > 0) {
            continueBtn.disabled = false;
        } else {
            continueBtn.disabled = true;
            document.getElementById('password-section').classList.remove('show');
            continueBtn.style.display = 'block';
        }
    }

    // Check on page load if email already filled (validation errors)
    document.addEventListener('DOMContentLoaded', function() {
        checkEmailFilled();
        @if(old('email'))
            showPasswordField();
        @endif
    });
</script>
@endsection
