<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\JobRoleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\SpaController;
use Illuminate\Support\Facades\Route;

// ===== PUBLIC ROUTES =====
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// ===== PROTECTED ROUTES =====
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Loading/Preload page - shown on first login to cache everything
    Route::get('/loading', function () {
        return view('loading');
    })->name('loading');

    // API to mark app as preloaded (GET to avoid CSRF issues)
    Route::get('/api/mark-preloaded', function (Illuminate\Http\Request $request) {
        $request->session()->put('app_preloaded', true);
        return response()->json(['success' => true]);
    });

    // Dashboard route - SPA with Alpine.js
    Route::get('/dashboard', function (Illuminate\Http\Request $request) {
        // Check if app needs to be preloaded
        $isPreloaded = $request->session()->get('app_preloaded', false);

        if (!$isPreloaded) {
            // First visit - redirect to loading page
            return redirect('/loading');
        }

        return view('spa');
    })->name('dashboard');

    Route::get('/employees', function () {
        return view('employees');
    })->name('employees.page');

    Route::get('/history', function () {
        return view('history');
    })->name('history');

    Route::get('/job-roles', function () {
        return view('job-roles');
    })->name('job-roles');

    // SPA routes - Return HTML content for Alpine.js
    Route::get('/spa/view/{view}', [SpaController::class, 'getView'])->name('spa.view');

    Route::get('/', function () {
        return redirect()->route('dashboard');
    })->name('home');

    // Data Storage (Load ALL on start, Save ALL on close) - NO CRUD endpoints!
    Route::get('/api/data/load', [\App\Http\Controllers\DataController::class, 'load'])->name('data.load');
    Route::post('/api/data/save', [\App\Http\Controllers\DataController::class, 'save'])->name('data.save');
});
