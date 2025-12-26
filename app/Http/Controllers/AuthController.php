<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('=== LOGIN ATTEMPT ===');
        \Illuminate\Support\Facades\Log::info('Email: ' . $request->input('email'));

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Veuillez entrer votre adresse email.',
            'email.email' => 'L\'adresse email doit être valide (exemple: nom@exemple.com).',
            'password.required' => 'Veuillez entrer votre mot de passe.',
        ]);

        \Illuminate\Support\Facades\Log::info('Attempting authentication...');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            \Illuminate\Support\Facades\Log::info('✓ Authentication successful');
            $request->session()->regenerate();

            // Check if app is already preloaded
            $isPreloaded = $request->session()->get('app_preloaded', false);

            if (!$isPreloaded) {
                \Illuminate\Support\Facades\Log::info('First login - redirecting to preload page');
                return redirect()->intended('/loading');
            }

            \Illuminate\Support\Facades\Log::info('App preloaded - redirecting to /dashboard');
            return redirect()->intended('/dashboard');
        }

        \Illuminate\Support\Facades\Log::warning('✗ Authentication failed for: ' . $request->input('email'));
        return back()->withErrors([
            'email' => 'Email ou mot de passe incorrect. Veuillez vérifier vos informations et réessayer.',
        ])->onlyInput('email');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'name.required' => 'Veuillez entrer votre nom complet.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required' => 'Veuillez entrer votre adresse email.',
            'email.email' => 'L\'adresse email doit être valide (exemple: nom@exemple.com).',
            'email.unique' => 'Cette adresse email est déjà utilisée. Essayez de vous connecter ou utilisez une autre adresse.',
            'password.required' => 'Veuillez créer un mot de passe.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères pour votre sécurité.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->intended('/dashboard');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($user) {
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(Str::random(24)),
                ]);
            }

            Auth::login($user, true);

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google OAuth error: ' . $e->getMessage());
            return redirect('/login')->withErrors(['google' => 'Erreur de connexion avec Google. Veuillez reessayer.']);
        }
    }
}
