<!DOCTYPE html>
<html lang="fr" data-theme="benka">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Presence">

    <link rel="apple-touch-icon" sizes="180x180" href="/icons/icon-180x180.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <title>@yield('title', 'Presence Chantier')</title>

    @if(app()->environment('production'))
        @php
            $manifestPath = public_path('build/manifest.json');
            $cssFile = null;
            $jsFile = null;

            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
                $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
            }
        @endphp
        @if($cssFile)
            <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
        @endif
        @if($jsFile)
            <script type="module" src="{{ asset('build/' . $jsFile) }}"></script>
        @endif
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        html, body { -webkit-user-select: none; user-select: none; }
        input, textarea, select { -webkit-user-select: text; user-select: text; }
        .safe-area-bottom { padding-bottom: env(safe-area-inset-bottom); }
    </style>
</head>
<body class="bg-base-200 min-h-screen" data-page="@yield('page-name')">
    <div class="pb-20">
        @yield('content')
    </div>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-2xl safe-area-bottom">
        <div class="flex items-center justify-around h-16 max-w-screen-xl mx-auto">
            <!-- Présence -->
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 h-full gap-1 transition-all duration-200 {{ Route::is('dashboard') ? 'text-blue-600 border-t-2 border-blue-600' : 'text-gray-500 hover:text-yellow-500' }}">
                <svg class="w-6 h-6" fill="{{ Route::is('dashboard') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span class="text-xs font-semibold">Présence</span>
            </a>

            <!-- Employés -->
            <a href="{{ route('employees.page') }}" class="flex flex-col items-center justify-center flex-1 h-full gap-1 transition-all duration-200 {{ Route::is('employees.page') ? 'text-blue-600 border-t-2 border-blue-600' : 'text-gray-500 hover:text-yellow-500' }}">
                <svg class="w-6 h-6" fill="{{ Route::is('employees.page') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="text-xs font-semibold">Employés</span>
            </a>

            <!-- Métiers -->
            <a href="{{ route('job-roles') }}" class="flex flex-col items-center justify-center flex-1 h-full gap-1 transition-all duration-200 {{ Route::is('job-roles') ? 'text-blue-600 border-t-2 border-blue-600' : 'text-gray-500 hover:text-yellow-500' }}">
                <svg class="w-6 h-6" fill="{{ Route::is('job-roles') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs font-semibold">Métiers</span>
            </a>

            <!-- Historique -->
            <a href="{{ route('history') }}" class="flex flex-col items-center justify-center flex-1 h-full gap-1 transition-all duration-200 {{ Route::is('history') ? 'text-blue-600 border-t-2 border-blue-600' : 'text-gray-500 hover:text-yellow-500' }}">
                <svg class="w-6 h-6" fill="{{ Route::is('history') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs font-semibold">Historique</span>
            </a>

            <!-- Stats -->
            <a href="{{ route('statistics') }}" class="flex flex-col items-center justify-center flex-1 h-full gap-1 transition-all duration-200 {{ Route::is('statistics') ? 'text-blue-600 border-t-2 border-blue-600' : 'text-gray-500 hover:text-yellow-500' }}">
                <svg class="w-6 h-6" fill="{{ Route::is('statistics') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-xs font-semibold">Stats</span>
            </a>

            <!-- Déconnexion -->
            <form method="POST" action="{{ route('logout') }}" class="flex-1 h-full">
                @csrf
                <button type="submit" class="flex flex-col items-center justify-center w-full h-full gap-1 transition-all duration-200 text-gray-500 hover:text-red-500 group">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="text-xs font-semibold">Sortir</span>
                </button>
            </form>
        </div>
    </nav>
</body>
</html>
