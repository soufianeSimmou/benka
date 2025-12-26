<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chargement - {{ config('app.name', 'Benka') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="apple-touch-icon" href="/icons/icon-180x180.png">

    @vite(['resources/css/app.css'])

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        html, body { -webkit-user-select: none; user-select: none; }

        html, body {
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100%;
            overflow: hidden;
            position: fixed;
            background-color: #f3f4f6;
            background-image:
                url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%233b82f6' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"),
                url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%2359b5f9' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
            background-attachment: fixed;
            background-size: auto;
            padding-top: env(safe-area-inset-top);
            padding-bottom: env(safe-area-inset-bottom);
        }

        .loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            color: #1f2937;
            padding: 20px;
        }

        .logo {
            width: 120px;
            height: 120px;
            margin-bottom: 2rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
        }

        .progress-container {
            width: 280px;
            height: 6px;
            background: rgba(59, 130, 246, 0.2);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .progress-bar {
            height: 100%;
            background: #3b82f6;
            border-radius: 10px;
            width: 0%;
            transition: width 0.3s ease;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.4);
        }

        .loading-text {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .loading-subtext {
            font-size: 14px;
            opacity: 0.8;
            text-align: center;
        }

        .checkmark {
            display: inline-block;
            margin-left: 8px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .checkmark.visible {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <!-- Logo -->
        <img src="/logobenka.png" alt="Benka" class="logo">

        <!-- App Name -->
        <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 0.5rem;">Benka</h1>
        <p style="font-size: 16px; opacity: 0.9; margin-bottom: 3rem;">Gestion de Présence</p>

        <!-- Progress Bar -->
        <div class="progress-container">
            <div class="progress-bar" id="progressBar"></div>
        </div>

        <!-- Loading Text -->
        <div class="loading-text" id="loadingText">Initialisation...</div>
        <div class="loading-subtext" id="loadingSubtext">Préparation de l'application</div>
    </div>

    <script>
        // App Preloader - Loads and caches everything for instant performance
        const AppPreloader = {
            progress: 0,
            steps: [
                { name: 'Service Worker', weight: 10 },
                { name: 'Pages HTML', weight: 20 },
                { name: 'Employés', weight: 20 },
                { name: 'Métiers', weight: 15 },
                { name: 'Présences', weight: 20 },
                { name: 'Statistiques', weight: 15 }
            ],
            currentStep: 0,

            async init() {
                console.log('[Preloader] Starting preload sequence...');

                try {
                    // Step 1: Unregister old Service Workers and clear cache
                    await this.updateProgress('Nettoyage du cache...', 5);
                    await this.cleanupOldServiceWorker();

                    // Step 2: Register new Service Worker
                    await this.updateProgress('Enregistrement Service Worker...', 50);
                    await this.registerServiceWorker();

                    // Step 3: Preload build assets (CSS/JS) by triggering one page load
                    await this.updateProgress('Chargement des ressources...', 80);
                    await fetch('/dashboard', { credentials: 'same-origin' }).catch(() => {});

                    // Step 4: Final preparation
                    await this.updateProgress('Finalisation...', 95);
                    await this.delay(300);

                    // Mark as ready
                    await this.updateProgress('Prêt!', 100);
                    localStorage.setItem('app_preloaded', Date.now());

                    // Mark session as preloaded via API call (GET to avoid CSRF)
                    await fetch('/api/mark-preloaded', {
                        credentials: 'same-origin'
                    });

                    await this.delay(500);

                    // Redirect to dashboard
                    window.location.href = '/dashboard';

                } catch (error) {
                    console.error('[Preloader] Error during preload:', error);
                    document.getElementById('loadingText').textContent = 'Erreur de chargement';
                    document.getElementById('loadingSubtext').textContent = 'Redirection...';

                    // Still redirect after error
                    await this.delay(1500);
                    window.location.href = '/dashboard';
                }
            },

            async cleanupOldServiceWorker() {
                if ('serviceWorker' in navigator) {
                    try {
                        // Unregister ALL service workers
                        const registrations = await navigator.serviceWorker.getRegistrations();
                        console.log('[Preloader] Found', registrations.length, 'service workers to unregister');

                        for (let registration of registrations) {
                            await registration.unregister();
                            console.log('[Preloader] Unregistered service worker');
                        }

                        // Clear all caches
                        const cacheNames = await caches.keys();
                        console.log('[Preloader] Found', cacheNames.length, 'caches to delete');

                        for (let cacheName of cacheNames) {
                            await caches.delete(cacheName);
                            console.log('[Preloader] Deleted cache:', cacheName);
                        }

                        console.log('[Preloader] Cleanup complete');
                        await this.delay(500);
                    } catch (error) {
                        console.warn('[Preloader] Cleanup failed:', error);
                    }
                }
            },

            async registerServiceWorker() {
                if ('serviceWorker' in navigator) {
                    try {
                        const registration = await navigator.serviceWorker.register('/sw.js');
                        console.log('[Preloader] Service Worker registered:', registration.scope);

                        // If SW is waiting (not yet active), force it to activate immediately
                        if (registration.waiting) {
                            console.log('[Preloader] Forcing waiting service worker to activate...');
                            registration.waiting.postMessage({ type: 'SKIP_WAITING' });
                        }

                        // Wait for SW to be ready and active
                        await navigator.serviceWorker.ready;
                        console.log('[Preloader] Service Worker is now active and ready');
                        await this.delay(500);
                    } catch (error) {
                        console.warn('[Preloader] Service Worker registration failed:', error);
                    }
                }
            },

            async preloadPages() {
                const pages = ['/dashboard', '/attendance', '/employees', '/job-roles', '/history', '/statistics'];
                const promises = pages.map(page =>
                    fetch(page, { credentials: 'same-origin' })
                        .then(response => {
                            console.log(`[Preloader] Cached page: ${page}`);
                            return response;
                        })
                        .catch(err => console.warn(`[Preloader] Failed to cache ${page}:`, err))
                );

                await Promise.all(promises);
            },

            async preloadData(url, key) {
                try {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        // Store in localStorage for instant access
                        localStorage.setItem(`cache_${key}`, JSON.stringify({
                            data,
                            timestamp: Date.now()
                        }));
                        console.log(`[Preloader] Cached ${key}:`, data.length || 'N/A', 'items');
                    }
                } catch (error) {
                    console.warn(`[Preloader] Failed to preload ${key}:`, error);
                }
            },

            async preloadAttendance() {
                const today = new Date().toISOString().split('T')[0];
                // Just trigger a fetch to cache it
                try {
                    await fetch(`/dashboard`, { credentials: 'same-origin' });
                } catch (error) {
                    console.warn('[Preloader] Failed to preload attendance:', error);
                }
            },

            async updateProgress(text, percent) {
                document.getElementById('loadingText').textContent = text;
                document.getElementById('progressBar').style.width = percent + '%';

                if (percent === 100) {
                    document.getElementById('loadingSubtext').innerHTML = '✓ Application prête';
                }

                // Small delay for visual feedback
                await this.delay(200);
            },

            delay(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }
        };

        // Start preloading when page loads
        document.addEventListener('DOMContentLoaded', () => {
            AppPreloader.init();
        });

        // Also start immediately if DOM is already loaded
        if (document.readyState === 'interactive' || document.readyState === 'complete') {
            AppPreloader.init();
        }
    </script>
</body>
</html>
