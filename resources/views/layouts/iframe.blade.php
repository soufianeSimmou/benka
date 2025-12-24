<!DOCTYPE html>
<html lang="fr" data-theme="benka">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Benka')</title>

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
    @if($cssFile && $jsFile)
        <link rel="stylesheet" href="/build/{{ $cssFile }}">
        <script type="module" src="/build/{{ $jsFile }}"></script>
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        html, body { -webkit-user-select: none; user-select: none; }
        input, textarea, select { -webkit-user-select: text; user-select: text; }
    </style>
</head>
<body class="bg-base-200" data-page="@yield('page-name')">
    @yield('content')
</body>
</html>
